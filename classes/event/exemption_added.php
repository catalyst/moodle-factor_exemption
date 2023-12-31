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
 * Event for adding an exemption for a user
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
class exemption_added extends \core\event\base {

    /**
     * Create instance of event.
     *
     * @param \stdClass $user the User object of the User who the exemption was added for.
     *
     * @return exemption_added the exemption added event object.
     *
     * @throws \coding_exception
     */
    public static function exemption_added_event( \stdClass $user) {
        global $USER;
        $data = [
            'relateduserid' => $user->id,
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
        $this->data['crud'] = 'c';
        $this->data['edulevel'] = self::LEVEL_OTHER;
    }

    /**
     * Returns description of what happened.
     *
     * @return string
     */
    public function get_description() {
        $duration = get_config('factor_exemption', 'duration');
        return "The user with id '{$this->other['userid']}' created an MFA exemption for user with id '{$this->relateduserid}' with duration {$duration} seconds.";
    }

    /**
     * Return localised event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('event:exemptionadded', 'factor_exemption');
    }
}
