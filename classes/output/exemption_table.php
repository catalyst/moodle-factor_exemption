<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace factor_exemption\output;

/**
 * Table to display user exemptions, along with management actions.
 *
 * @package     factor_exemption
 * @author      Peter Burnett <peterburnett@catalyst-au.net>
 * @copyright   Catalyst IT 2023
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class exemption_table extends \table_sql {
    /**
     * Sets up the table_log parameters.
     *
     * @param string $uniqueid Unique id of form.
     * @param \moodle_url $url Url where this table is displayed.
     * @param int $perpage Number of rules to display per page.
     */
    public function __construct($uniqueid, \moodle_url $url, $perpage = 100) {
        parent::__construct($uniqueid);

        $this->set_attribute('id', 'factorexemptiontable');
        $this->set_attribute('class', 'generaltable generalbox');
        $this->define_columns(array(
                'userid',
                'username',
                'email',
                'timecreated',
                'expiry',
                'actions',
        ));
        $this->define_headers(array(
                get_string('userid', 'factor_exemption'),
                get_string('username'),
                get_string('email'),
                get_string('timecreated'),
                get_string('expiry', 'factor_exemption'),
                get_string('actions'),
            )
        );
        $this->pagesize = $perpage;
        $this->collapsible(false);
        $this->sortable(false, 'timecreated', 'DESC');
        $this->pageable(true);
        $this->is_downloadable(false);
        $this->define_baseurl($url);

        $fields = 'e.id AS eid, u.id AS userid, u.username, u.email, e.timecreated, e.expiry';
        $from = '{factor_exemption} e JOIN {user} u ON e.userid = u.id';
        $where = 'e.expiry > :time';

        $this->set_sql($fields, $from, $where, ['time' => time()]);
    }

    /**
     * Generate content for actions column.
     *
     * @param object $row object
     * @return string html used to display the manage column field.
     */
    public function col_actions($row) {
        global $OUTPUT;

        $actions = '';

        $extendurl = new \moodle_url('/admin/tool/mfa/factor/exemption/exemption.php', ['extend' => $row->eid]);
        $deleteurl = new \moodle_url('/admin/tool/mfa/factor/exemption/exemption.php', ['delete' => $row->eid]);
        $extendicon = $OUTPUT->render(new \pix_icon('t/add', get_string('extend', 'factor_exemption')));
        $deleteicon = $OUTPUT->render(new \pix_icon('t/delete', get_string('delete')));
        $actions .= \html_writer::link($extendurl, $extendicon, array('class' => 'action-icon'));
        $actions .= \html_writer::link($deleteurl, $deleteicon, array('class' => 'action-icon'));

        return $actions;
    }

    /**
     * Generate content for timecreated column.
     *
     * @param object $row object
     * @return string html used to display the manage column field.
     */
    public function col_timecreated($row) {
        $format = get_string('strftimedatetime', 'langconfig');
        return userdate($row->timecreated, $format);
    }

    /**
     * Generate content for expiry column.
     *
     * @param object $row object
     * @return string html used to display the manage column field.
     */
    public function col_expiry($row) {
        $format = get_string('strftimedatetime', 'langconfig');
        return userdate($row->expiry, $format);
    }

}
