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

/**
 * Settings
 *
 * @package     factor_exemption
 * @author      Peter Burnett <peterburnett@catalyst-au.net>
 * @copyright   Catalyst IT 2023
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$enabled = new admin_setting_configcheckbox('factor_exemption/enabled',
    new lang_string('settings:enablefactor', 'tool_mfa'),
    new lang_string('settings:enablefactor_help', 'tool_mfa'), 0);
$enabled->set_updatedcallback(function () {
    \tool_mfa\manager::do_factor_action('exemption', get_config('factor_exemption', 'enabled') ? 'enable' : 'disable');
});
$settings->add($enabled);

$settings->add(new admin_setting_configtext('factor_exemption/weight',
    new lang_string('settings:weight', 'tool_mfa'),
    new lang_string('settings:weight_help', 'tool_mfa'), 100, PARAM_INT));

$settings->add(new admin_setting_configduration('factor_exemption/duration',
    new lang_string('settings:duration', 'factor_exemption'),
    new lang_string('settings:duration_help', 'factor_exemption'), DAYSECS));

$ADMIN->add('toolmfafolder', new admin_externalpage('factor_exemption_manageexemptions',
    get_string('manageexemptions', 'factor_exemption'),
    new moodle_url('/admin/tool/mfa/factor/exemption/exemption.php')));
