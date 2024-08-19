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
 * Twili OTP authentication plugin auth.
 *
 * @package    auth_twiliootp
 * @author     Erudisiya <contact.erudisiya@gmail.com>
 * @copyright  2024 Erudisiya Team(https://erudisiya.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->libdir . "/formslib.php");
require_once($CFG->libdir . '/authlib.php');

/**
 * Phone OTP authentication plugin.
 *
 * @see self::user_login()
 * @see self::get_user_field()
 * @package    auth_otp
 * @copyright  2021 Brain Station 23 ltd
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class auth_plugin_twiliootp extends auth_plugin_base {

    /**
     * Default mapping field.
     */
    const DEFAULT_MAPPING_FIELD = 'twiliootp';
    const COMPONENT_NAME = 'auth_twiliootp';
    const LEGACY_COMPONENT_NAME = 'auth/twiliootp';

    /**
     * User key manager.
     *
     * @var userkey_manager_interface
     */
    //protected $userkeymanager;

    /**
     * Defaults for config form.
     *
     * @var array
     */

    /**
     * Constructor.
     */
    public function __construct() {
        $this->authtype = 'twiliootp';
        $this->config = get_config('auth_twiliootp');
    }
    function can_signup() {
        return true;
    }
    function loginpage_hook() {
        global $PAGE, $CFG;
        $PAGE->requires->jquery();
        $PAGE->requires->js_init_code("buttonsAddMethod = 'auto';");
        $content = str_replace(array("\n", "\r"), array("\\\n", "\\\r",), $this->get_buttons_string());
        $PAGE->requires->js_init_code("buttons = '$content';");
        $PAGE->requires->js(new moodle_url($CFG->wwwroot . "/auth/twiliootp/script.js"));
    }
    private function get_buttons_string() {
        global $CFG;

        $link = $CFG->wwwroot.'/auth/twiliootp/signup.php';
        $content = '<div class="login-divider"></div>
        <div class="login-instructions mb-3">
            <h2 class="login-heading">Is this your first time here?</h2>
            For full access to this site, you first need to create an account.
            <div class="createnewlink">
                <a class="btn btn-secondary" 
                    href="'.$link.'" >'.get_string("createnewbutton", "auth_twiliootp") .'
                </a><br>
            </div>
        </div>
        ';

        return $content;
    }
    function user_signup($user, $notify=true) {
        // Standard signup, without custom confirmatinurl.
        return $this->user_signup_with_confirmation($user, $notify);
    }
    public function user_signup_with_confirmation($user, $notify=true, $confirmationurl = null) {
        global $CFG, $DB, $SESSION;
        require_once($CFG->dirroot.'/user/profile/lib.php');
        require_once($CFG->dirroot.'/user/lib.php');

        $plainpassword = $user->password;
        $user->password = hash_internal_user_password($user->password);
        if (empty($user->calendartype)) {
            $user->calendartype = $CFG->calendartype;
        }
        $user->id = user_create_user($user, false, false);

        user_add_password_history($user->id, $plainpassword);

        // Save any custom profile field information.
        profile_save_data($user);
    }
    function user_confirm($username, $confirmsecret) { //echo 'heee';die;
        global $DB, $SESSION;
        $user = get_complete_user_data('username', $username);

        if (!empty($user)) {
            if ($user->confirmed) {
                return AUTH_CONFIRM_ALREADY;
            } else {
                $DB->set_field("user", "confirmed", 1, array("id"=>$user->id));
                return AUTH_CONFIRM_OK;
            }
        } else  {
            return AUTH_CONFIRM_ERROR;
        }
    }
    public function user_login($username, $password) {
        global $CFG, $DB, $USER;
        if (!$user = $DB->get_record('user', array('username'=>$username, 'mnethostid'=>$CFG->mnet_localhost_id))) {
            return false;
        }
        if (!validate_internal_user_password($user, $password)) {
            return false;
        }
        if ($password === 'changeme') {
            // force the change - this is deprecated and it makes sense only for manual auth,
            // because most other plugins can not change password easily or
            // passwords are always specified by users
            set_user_preference('auth_forcepasswordchange', true, $user->id);
        }
        return true;
    }
}
