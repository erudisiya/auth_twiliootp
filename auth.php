<?php
defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->libdir . "/formslib.php");
require_once($CFG->libdir . '/authlib.php');

//use core\output\notification;

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
    /*protected $defaults = array(
        'mappingfield' => self::DEFAULT_MAPPING_FIELD,
        'keylifetime' => 60,
        'iprestriction' => 0,
        'ipwhitelist' => '',
        'redirecturl' => '',
        'ssourl' => '',
        'createuser' => false,
        'updateuser' => false,
    );*/

    /**
     * Constructor.
     */
    public function __construct() {
        $this->authtype = 'twiliootp';
        $this->config = get_config('auth_twiliootp');
        //$this->userkeymanager = new core_userkey_manager($this->config);
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
    /*function user_signup($user, $notify=true) {
        global $CFG, $DB;
        echo "string";die;
        // Generate OTP and save it in the user profile
        $otp = generate_random_otp(); // You need to implement this function
        $user->otp = $otp;

        // Send OTP via Twilio
        $twilio = new \Twilio\Rest\Client($CFG->twilio_account_sid, $CFG->twilio_auth_token);
        $message = "Your OTP for Moodle registration is: $otp";
        $twilio->messages->create(
            $user->phone, // User's phone number
            ['from' => $CFG->twilio_phone_number, 'body' => $message]
        );

        return true; // Proceed with Moodle's default signup process
    }*/
    function user_signup($user, $notify=true) {//echo 'twilio';die;
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
        //print_r($user);die;
        $user->id = user_create_user($user, false, false);

        user_add_password_history($user->id, $plainpassword);

        // Save any custom profile field information.
        profile_save_data($user);

        /*global $CFG, $PAGE, $OUTPUT;
        $emailconfirm = get_string('emailconfirm');
        $PAGE->navbar->add($emailconfirm);
        $PAGE->set_title($emailconfirm);
        $PAGE->set_heading($PAGE->course->fullname);
        echo $OUTPUT->header();
        notice(get_string('emailconfirmsent', '', $user->email), "$CFG->wwwroot/index.php");*/
        /*// Save wantsurl against user's profile, so we can return them there upon confirmation.
        if (!empty($SESSION->wantsurl)) {
            set_user_preference('auth_email_wantsurl', $SESSION->wantsurl, $user);
        }

        // Trigger event.
        \core\event\user_created::create_from_userid($user->id)->trigger();

        if (! send_confirmation_email($user, $confirmationurl)) {
            throw new \moodle_exception('auth_emailnoemail', 'auth_email');
        }

        if ($notify) {
            global $CFG, $PAGE, $OUTPUT;
            $emailconfirm = get_string('emailconfirm');
            $PAGE->navbar->add($emailconfirm);
            $PAGE->set_title($emailconfirm);
            $PAGE->set_heading($PAGE->course->fullname);
            echo $OUTPUT->header();
            notice(get_string('emailconfirmsent', '', $user->email), "$CFG->wwwroot/index.php");
        } else {
            return true;
        }*/
    }
    /*function user_confirm($username, $otp) {
        // Verify OTP against stored OTP
        global $DB;
echo "string1";die;
        // Fetch user details based on username
        $user = $DB->get_record('user', array('username' => $username), '*', MUST_EXIST);

        // Compare provided OTP with stored OTP
        if ($user && isset($user->otp) && $user->otp == $otp) {
            return true; // OTP verification successful
        } else {
            return false; // OTP verification failed
        }
    }*/
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
        //redirect(new moodle_url('/my/'));
        return true;
    }
    /*public function user_login($username, $password) {
        global $CFG, $DB, $USER;

        // Validate the user credentials
        if (!$user = $DB->get_record('user', array('username'=>$username, 'mnethostid'=>$CFG->mnet_localhost_id))) {
            return false; // User not found
        }

        if (!validate_internal_user_password($user, $password)) {
            return false; // Invalid password
        }

        // Check if the password is 'changeme' and force password change if needed
        if ($password === 'changeme') {
            set_user_preference('auth_forcepasswordchange', true, $user->id);
        }

        // Perform additional checks or actions as needed
        // Example: Check user status, roles, etc.

        // If user credentials are valid, set $USER and redirect to /my/
        $USER = $user; // Set the global $USER object
        echo 'here';die;
        // Redirect to /my/ after successful login
        redirect(new moodle_url('/my'));
        //exit; // Ensure no further code executes after redirect
    }*/

}
