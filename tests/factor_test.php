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

namespace factor_exemption\tests;

/**
 * Tests for TOTP factor.
 *
 * @covers      \factor_exemption\factor
 * @package     factor_exemption
 * @author      Peter Burnett <peterburnett@catalyst-au.net>
 * @copyright   Catalyst IT 2023
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class factor_test extends \advanced_testcase {

    /**
     * Tests for add_exemption
     *
     * @covers \factor_exemption\factor::add_exemption
     */
    public function test_add_exemption() {
        global $DB;
        $this->resetAfterTest(true);

        $user = $this->getDataGenerator()->create_user();
        $this->assertEquals(0, $DB->count_records('factor_exemption'));
        \factor_exemption\factor::add_exemption($user);

        $records = $DB->get_records('factor_exemption');
        $this->assertEquals(1, count($records));

        // Confirm the expiry time is > now, and less than a day from now (by default).
        $record = reset($records);
        $this->assertTrue($record->expiry > time());
        $this->assertTrue($record->expiry <= time() + DAYSECS);
        $this->assertEquals($user->id, $record->userid);

        // Add one more and confirm a second record is added correctly.
        \factor_exemption\factor::add_exemption($user);
        $records = $DB->get_records('factor_exemption');
        $this->assertEquals(2, count($records));
    }

    /**
     * Tests for extend_exemption
     *
     * @covers \factor_exemption\factor::extend_exemption
     */
    public function test_extend_exemption() {
        global $DB;
        $this->resetAfterTest(true);

        $user = $this->getDataGenerator()->create_user();
        $this->assertEquals(0, $DB->count_records('factor_exemption'));
        \factor_exemption\factor::add_exemption($user);
        $origrecord = $DB->get_record('factor_exemption');

        sleep(1);
        \factor_exemption\factor::extend_exemption($origrecord->id);
        $this->assertEquals(1, $DB->count_records('factor_exemption'));
        $newrecord = $DB->get_record('factor_exemption');

        $this->assertNotEquals($origrecord->expiry, $newrecord->expiry);
        $this->assertTrue($origrecord->expiry < $newrecord->expiry);
        $this->assertEquals($origrecord->timecreated, $newrecord->timecreated);
    }

    /**
     * Tests for delete_exemption
     *
     * @covers \factor_exemption\factor::delete_exemption
     */
    public function test_delete_exemption() {
        global $DB;
        $this->resetAfterTest(true);

        $user = $this->getDataGenerator()->create_user();
        $this->assertEquals(0, $DB->count_records('factor_exemption'));
        \factor_exemption\factor::add_exemption($user);
        $origrecord = $DB->get_record('factor_exemption');

        sleep(1);
        \factor_exemption\factor::expire_exemption($origrecord->id);
        $this->assertEquals(1, $DB->count_records('factor_exemption'));
        $newrecord = $DB->get_record('factor_exemption');

        $this->assertNotEquals($origrecord->expiry, $newrecord->expiry);
        $this->assertTrue($origrecord->expiry > $newrecord->expiry);
        $this->assertTrue(time() > $newrecord->expiry);
        $this->assertEquals($origrecord->timecreated, $newrecord->timecreated);
    }

    public function test_get_state() {
        global $DB;
        $this->resetAfterTest(true);

        $user = $this->getDataGenerator()->create_user();
        $factor = \tool_mfa\plugininfo\factor::get_factor('exemption');
        // No points to start.
        $this->assertEquals(\tool_mfa\plugininfo\factor::STATE_NEUTRAL, $factor->get_state());
        $this->assertEquals(0, $DB->count_records('factor_exemption'));

        // Add an exemption and check points.
        \factor_exemption\factor::add_exemption($user);
        $record = $DB->get_record('factor_exemption');
        $this->assertEquals(\tool_mfa\plugininfo\factor::STATE_PASS, $factor->get_state());

        // Delete exemption and confirm no points again.
        \factor_exemption\factor::expire_exemption($record->id);
        $this->assertEquals(\tool_mfa\plugininfo\factor::STATE_NEUTRAL, $factor->get_state());
    }
}
