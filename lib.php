<?php
defined('MOODLE_INTERNAL') || die();
/**
 * Check if sign-up is enabled in the site. If is enabled, the function will return the authplugin instance.
 *
 * @return mixed false if sign-up is not enabled, the authplugin instance otherwise.
 * @since  Moodle 3.2
 */
function signup_is_enabled_twiliootp() {
    global $CFG;
    //$isenable = get_config('auth_twiliootp', 'enabletwilio');
    if (!empty($CFG->registerauth)) {
        //if($CFG->registerauth == 'twiliootp'){ //only if registerauth is twilio
            $authplugin = get_auth_plugin($CFG->registerauth);
            if ($authplugin->can_signup()) {
                return $authplugin;
            }
        //}   
    }
    return false;
}
/**
 * Plugins can create pre sign up requests.
 */
/*function core_login_pre_signup_requests_twilio() {
    $callbacks = get_plugins_with_function('pre_signup_requests');
    foreach ($callbacks as $type => $plugins) {
        foreach ($plugins as $plugin => $pluginfunction) {
            $pluginfunction();
        }
    }
}*/
 /** Inject form elements into signup_form.
  * @param mform $mform the form to inject elements into.
  */
/*function core_login_extend_signup_forma($mform) {
    $callbacks = get_plugins_with_function('extend_signup_form');
    foreach ($callbacks as $type => $plugins) {
        foreach ($plugins as $plugin => $pluginfunction) {
            $pluginfunction($mform);
        }
    }
}*/
function signup_form() {
    global $CFG;

    require_once($CFG->dirroot.'/auth/twiliootp/signup_form.php');
    return new login_signup_form(null, null, 'post', '', array('autocomplete'=>'on'));
}
function signup_captcha_enabled_twiliootp() {
    global $CFG;
    $authplugin = get_auth_plugin($CFG->registerauth);
    return !empty($CFG->recaptchapublickey) && !empty($CFG->recaptchaprivatekey) && $authplugin->is_captcha_enabled();
}
function country_code_twiliootp($country_code) {
    if($country_code == 'IN'){
        $code = '+91';
    } elseif($country_code == 'AF') {
        $code = '+93';
    } elseif($country_code == 'AX') {
        $code = '+358';
    } elseif($country_code == 'AL') {
        $code = '+355';
    } elseif($country_code == 'DZ') {
        $code = '+213';
    }
    return $code;
}