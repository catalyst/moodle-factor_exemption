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

$delete = optional_param('delete', 0, PARAM_INT);
$extend = optional_param('extend', 0, PARAM_INT);

$context = \context_system::instance();
$PAGE->set_context($context);
// We need to load the full admin tree into memory here, otherwise our node isnt locatable as it is loaded conditionally.
admin_get_root(false, true);
admin_externalpage_setup('factorexemptionmanageexemptions');
$url = new moodle_url('/admin/tool/mfa/factor/exemption/exemption.php');

// Process table actions if any.
if ($extend !== 0) {
    \factor_exemption\factor::extend_exemption($extend);
    redirect($url);
}

if ($delete !== 0) {
    \factor_exemption\factor::expire_exemption($delete);
    redirect($url);
}

$form = new \factor_exemption\form\exemption();

if ($form->is_cancelled()) {
    redirect('/');
} else if ($fromform = $form->get_data()) {
    $user = \factor_exemption\factor::get_searched_user($fromform->user);
    if (!$user) {
        \core\notification::error(get_string('usernotfound', 'tool_mfa'));
        redirect($url);
    }
    \factor_exemption\factor::add_exemption($user);

    \core\notification::success(get_string('exemptionadded', 'factor_exemption', $fromform->user));
    redirect($url);
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('manageexemptions', 'factor_exemption'));
$form->display();

// Echo table of exempt users
$url = new moodle_url('/admin/tool/mfa/factor/exemption/exemption.php');
$table = new \factor_exemption\output\exemption_table('exemptiontable', $url);
$table->out(100, true);

echo $OUTPUT->footer();
