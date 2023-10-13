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

namespace factor_exemption\event;

/**
 * Event for extending an exemption for a user
 *
 * @property-read array $other {
 *      Extra information about event.
 * }
 *
 * @package     factor_exemption
 * @author      Peter Burnett <peterburnett@catalyst-au.net>
 * @copyright   Catalyst IT 2023
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class exemption_expired extends \core\event\base {

    /**
     * Create instance of event.
     *
     * @param \stdClass $user the User object of the User who the exemption was added for.
     *
     * @return exemption_expired the exemption added event object.
     * @throws \coding_exception
     */
    public static function exemption_expired_event(int $eid) {
        global $DB, $USER;
        $exemption = $DB->get_record('factor_exemption', ['id' => $eid], 'userid', MUST_EXIST);
        $data = [
            'relateduserid' => $exemption->userid,
            'context' => \context_system::instance(),
            'other' => [
                'userid' => $USER->id,
            ],
        ];

        return self::create($data);
    }

    /**
     * Init method.
     *
     * @return void
     */
    protected function init() {
        $this->data['crud'] = 'd';
        $this->data['edulevel'] = self::LEVEL_OTHER;
    }

    /**
     * Returns description of what happened.
     *
     * @return string
     */
    public function get_description() {
        return "The user with id '{$this->other['userid']}' ended an MFA exemption for user with id '{$this->relateduserid}'.";
    }

    /**
     * Return localised event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('event:exemptionexpired', 'factor_exemption');
    }
}

