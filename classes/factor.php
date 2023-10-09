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

namespace factor_exemption;

use tool_mfa\local\factor\object_factor_base;

/**
 * Exemption factor class.
 *
 * @package     factor_exemption
 * @author      Peter Burnett <peterburnett@catalyst-au.net> 
 * @copyright   Catalyst IT 2023
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class factor extends object_factor_base {

    /**
     * Exemption Factor implementation.
     *
     * @param stdClass $user the user to check against.
     * @return array
     */
    public function get_all_user_factors($user) {
        return $this->get_singleton_user_factor($user);
    }

    /**
     * Exemption Factor implementation.
     *
     * {@inheritDoc}
     */
    public function has_input() {
        return false;
    }

    /**
     * Exemption Factor implementation.
     *
     * {@inheritDoc}
     */
    public function get_state() {
        global $DB, $USER;

        // As long as the user has an exemption record that hasnt expired yet, they pass.
        $select = 'userid = ? AND expiry > ?';
        $exempt = $DB->record_exists_select('factor_exemption', $select, [$USER->id, time()]);

        return $exempt ? \tool_mfa\plugininfo\factor::STATE_PASS : \tool_mfa\plugininfo\factor::STATE_NEUTRAL;
    }


    /**
     * Exemption factor implementation.
     *
     * @param \stdClass $user
     */
    public function possible_states($user) {
        // Exemption can only be neutral or pass.
        return [
            \tool_mfa\plugininfo\factor::STATE_PASS,
            \tool_mfa\plugininfo\factor::STATE_NEUTRAL,
        ];
    }

    /**
     * Add an exemption for a user.
     *
     * @param \stdClass $user
     */
    public static function add_exemption(\stdClass $user) {
        global $DB;

        // We don't need to handle logic for dealing with anything here except inserts.
        // Duplicate records do not cause any issues.
        $duration = get_config('factor_exemption', 'duration');
        $record = [
            'userid' => $user->id,
            'expiry' => time() + (int) $duration,
            'timecreated' => time()
        ];
        $DB->insert_record('factor_exemption', $record);
    }

    /**
     * Extend an exemption for the user.
     * This should be used instead of creating a new extension to avoid cluttering the table with duplicate records.
     *
     * @param int $eid the exemption record id.
     */
    public static function extend_exemption(int $eid) {
        global $DB;

        $duration = get_config('factor_exemption', 'duration');
        $DB->set_field('factor_exemption', 'expiry', time() + (int) $duration, ['id' => $eid]);
    }

    /**
     * Delete an exemption for the user.
     * This doesn't do a real delete,  just sets the expiry in the past.
     *
     * @param int $eid the exemption record id.
     */
    public static function delete_exemption(int $eid) {
        global $DB;

        $DB->set_field('factor_exemption', 'expiry', time() - 1, ['id' => $eid]);
    }
}
