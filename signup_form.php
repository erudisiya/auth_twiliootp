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
 * User sign-up form.
 *
 * @package    core
 * @subpackage auth
 * @copyright  1999 onwards Martin Dougiamas  http://dougiamas.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/formslib.php');
require_once($CFG->dirroot.'/user/profile/lib.php');
require_once($CFG->dirroot . '/user/editlib.php');
require_once($CFG->dirroot.'/login/lib.php');

class login_signup_form extends moodleform implements renderable, templatable {
    function definition() {
        global $USER, $CFG;

        $mform = $this->_form;

        $mform->addElement('text', 'username', get_string('username'), 'maxlength="100" size="12" autocapitalize="none"');
        $mform->setType('username', PARAM_RAW);
        $mform->addRule('username', get_string('missingusername'), 'required', null, 'client');
        $mform->addElement('html', '<div class="text-danger already_username" style="display:none; text-align:center;">Username is already taken <span id="taken_phone"></span></div>');
        if (!empty($CFG->passwordpolicy)){
            $mform->addElement('static', 'passwordpolicyinfo', '', print_password_policy());
        }
        $mform->addElement('password', 'password', get_string('password'), [
            'maxlength' => 32,
            'size' => 12,
            'autocomplete' => 'new-password'
        ]);
        $mform->setType('password', core_user::get_property_type('password'));
        $mform->addRule('password', get_string('missingpassword'), 'required', null, 'client');
        /*$mform->addRule('password', get_string('maximumchars', '', MAX_PASSWORD_CHARACTERS),
            'maxlength', MAX_PASSWORD_CHARACTERS, 'client');*/

        $mform->addElement('text', 'email', get_string('email'), 'maxlength="100" size="25"');
        $mform->setType('email', core_user::get_property_type('email'));
        $mform->addRule('email', get_string('missingemail'), 'required', null, 'client');
        $mform->setForceLtr('email');

        $mform->addElement('html', '<div class="text-danger already_email" style="display:none; text-align:center;">Useremail is already taken <span id="taken_phone"></span></div>');

        /*$mform->addElement('text', 'email2', get_string('emailagain'), 'maxlength="100" size="25"');
        $mform->setType('email2', core_user::get_property_type('email'));
        $mform->addRule('email2', get_string('missingemail'), 'required', null, 'client');
        $mform->setForceLtr('email2');*/

        $namefields = useredit_get_required_name_fields();
        foreach ($namefields as $field) {
            $mform->addElement('text', $field, get_string($field), 'maxlength="100" size="30"');
            $mform->setType($field, core_user::get_property_type('firstname'));
            $stringid = 'missing' . $field;
            if (!get_string_manager()->string_exists($stringid, 'moodle')) {
                $stringid = 'required';
            }
            $mform->addRule($field, get_string($stringid), 'required', null, 'client');
        }

        $mform->addElement('text', 'city', get_string('city'), 'maxlength="120" size="20"');
        $mform->setType('city', core_user::get_property_type('city'));
        if (!empty($CFG->defaultcity)) {
            $mform->setDefault('city', $CFG->defaultcity);
        }

        $country = get_string_manager()->get_list_of_countries();
        $default_country[''] = get_string('selectacountry');
        $country = array_merge($default_country, $country);
        $mform->addElement('select', 'country', get_string('country'), $country);
        $mform->addRule('country', get_string('required'), 'required', null, 'client');
        
        if( !empty($CFG->country) ){
            $mform->setDefault('country', $CFG->country);
        }else{
            $mform->setDefault('country', '');
        }
         /*$sendOTPButton = $mform->createElement('submit', 'send_otp', 'Send OTP');
        $mform->addElement($sendOTPButton);
        $mform->registerNoSubmitButton('send_otp');*/
        $mform->addElement('text', 'phone2', get_string('mobilenumber','auth_twiliootp'),array('maxlength' => 15, 'size' => 25, 'value' => '', 'id' => 'id_phone2'));
        $mform->setType('phone2', core_user::get_property_type('phone2'));
        $mform->addRule('phone2', 'Missing mobile number', 'required', null, 'client');
        $mform->addRule('phone2', 'Please enter a valid phone number.', 'numeric', null, 'client');
        $mform->addElement('html', '<div class="text-danger already_phone" style="display:none; text-align:center;">Phone is already taken <span id="taken_phone"></span></div>');
        //otp sent message
        $mform->addElement('html', '<div class="text-success otpsent_message" style="display:none; text-align:center;">OTP sent successfully to mobile number <span id="mobile-number"></span></div>');
        $mform->addElement('html', '<div class="text-danger otpnotsent_message" style="display:none; text-align:center;">OTP Could not sent.please try again</div>');
        $mform->addElement('html', '<div class="text-danger invalid_number" style="display:none; text-align:center;">Invalid mobile number.please check.</div>');
        //otp button container
        $mform->addElement('html', '<div class="send-otp-container">');
        $mform->addElement('html', '<button id="otpButton" class="send-otp" style="display:none;">Send OTP</button>');//otp button
        $mform->addElement('html', '<input type="text" id="otpbox" name="otpbox" value="" style="display:none;" title="Please enter 4 digit OTP received in WhatsApp SMS">'); //otp text box
        $mform->addElement('html', '<button id="verifybutton" class="verify-otp btn-secondary" style="display:none;">Verify OTP</button>');// verify otp button
        $mform->addElement('html', '</div>');
        //verfied otp message
        $mform->addElement('html', '<div class="verfied-otp-container" style="display:none; text-align:center;">');
        $mform->addElement('html', '<div class="text-success">Your phone number has been verified successfully!</div>');
        $mform->addElement('html', '</div>');
        //not verfied otp message
        $mform->addElement('html', '<div class="notverfied-otp-container" style="display:none; text-align:center;">');
        $mform->addElement('html', '<div class="text-danger">Verification unsuccessful,Please Try again!</div>');
        $mform->addElement('html', '</div>');
        //loader
        $mform->addElement('html', ' <div class="loader-wrapper" id="loader-container"><div id="loader"></div></div>');
        profile_signup_fields($mform);
        //profile_signup_fields($mform);

        /*if (signup_captcha_enabled_twiliootp()) {
            $mform->addElement('recaptcha', 'recaptcha_element', get_string('security_question', 'auth'));
            $mform->addHelpButton('recaptcha_element', 'recaptcha', 'auth');
            $mform->closeHeaderBefore('recaptcha_element');
        }*/

        // Hook for plugins to extend form definition.
        core_login_extend_signup_form($mform);

        // Add "Agree to sitepolicy" controls. By default it is a link to the policy text and a checkbox but
        // it can be implemented differently in custom sitepolicy handlers.
        $manager = new \core_privacy\local\sitepolicy\manager();
        $manager->signup_form($mform);

        // buttons
        $this->set_display_vertical();
        $this->add_action_buttons(true, get_string('createaccount'));

    }

    function definition_after_data(){
        $mform = $this->_form;
        $mform->applyFilter('username', 'trim');

        // Trim required name fields.
        foreach (useredit_get_required_name_fields() as $field) {
            $mform->applyFilter($field, 'trim');
        }
    }

    /**
     * Validate user supplied data on the signup form.
     *
     * @param array $data array of ("fieldname"=>value) of submitted data
     * @param array $files array of uploaded files "element_name"=>tmp_file_path
     * @return array of "element_name"=>"error_description" if there are errors,
     *         or an empty array if everything is OK (true allowed for backwards compatibility too).
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        // Extend validation for any form extensions from plugins.
        $errors = array_merge($errors, core_login_validate_extend_signup_form($data));
        
        /*if (signup_captcha_enabled()) {
            $recaptchaelement = $this->_form->getElement('recaptcha_element');
            if (!empty($this->_form->_submitValues['g-recaptcha-response'])) {
                $response = $this->_form->_submitValues['g-recaptcha-response'];
                if (!$recaptchaelement->verify($response)) {
                    $errors['recaptcha_element'] = get_string('incorrectpleasetryagain', 'auth');
                }
            } else {
                $errors['recaptcha_element'] = get_string('missingrecaptchachallengefield');
            }
        }*/

        //$errors += signup_validate_data($data, $files);
        return $errors;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output Used to do a final render of any components that need to be rendered for export.
     * @return array
     */
    public function export_for_template(renderer_base $output) {
        ob_start();
        $this->display();
        $formhtml = ob_get_contents();
        ob_end_clean();
        $context = [
            'formhtml' => $formhtml
        ];
        return $context;
    }
}
