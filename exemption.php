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
 * Page to manage user exemptions.
 *
 * @package     factor_exemption
 * @author      Peter Burnett <peterburnett@catalyst-au.net>
 * @copyright   Catalyst IT 2023
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../../../config.php');
require_once($CFG->libdir . '/adminlib.php');
admin_externalpage_setup('factor_exemption_manageexemptions');

$form = new \factor_exemption\form\exemption();

if ($form->is_cancelled()) {
    redirect('/');
} else if ($fromform = $form->get_data()) {

    // Add/update exemption for user.
    \factor_exemption\factor::add_exemption($data->user);

    // TODO: Emit event.

    \core\notification::success(get_string('exemptionadded', 'factor_exemption', $stringvar));
    redirect(new moodle_url('/admin/tool/mfa/factor/exemption/exemption.php'));
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('resetfactor', 'tool_mfa'));
$form->display();

// Echo table of exempt users
echo $OUTPUT->footer();
