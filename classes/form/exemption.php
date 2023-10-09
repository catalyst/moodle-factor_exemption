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

namespace factor_exemption\form;

use core_user;

require_once("$CFG->libdir/formslib.php");

/**
 * Form to add users to exemptions or update exemptions.
 *
 * @package     factor_exemption
 * @author      Peter Burnett <peterburnett@catalyst-au.net>
 * @copyright   Catalyst IT 2023
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class exemption extends \moodleform {

    /**
     * Form definition.
     */
    public function definition() {
        $mform = $this->_form;
        $mform->addElement('text', 'user', get_string('form:exemptionentry', 'factor_exemption'));
        $mform->setType('user', PARAM_TEXT);
        $mform->addHelpButton('user', 'form:exemptionentry', 'factor_exemption');
        $this->add_action_buttons(true, get_string('resetconfirm', 'tool_mfa'));
    }

    /**
     * Form validation.
     *
     * @param array $data array of ("fieldname"=>value) of submitted data
     * @param array $files array of uploaded files "element_name"=>tmp_file_path
     * @return array of "element_name"=>"error_description" if there are errors,
     *         or an empty array if everything is OK (true allowed for backwards compatibility too).
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);
        // Try input as username first, then email.
        $user = core_user::get_user_by_username($data['user']);
        if (!$user) {
            $user = core_user::get_user_by_email($data['user']);
        }

        if (!$user) {
            $errors['user'] = get_string('cannotfinduser', 'factor_exemption');
        }
        
        return $errors;
    }
}
