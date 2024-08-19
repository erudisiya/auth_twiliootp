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
 * Twili OTP authentication plugin lib.
 *
 * @package    auth_twiliootp
 * @author     Erudisiya <contact.erudisiya@gmail.com>
 * @copyright  2024 Erudisiya Team(https://erudisiya.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
/**
 * Check if sign-up is enabled in the site. If is enabled, the function will return the authplugin instance.
 *
 * @return mixed false if sign-up is not enabled, the authplugin instance otherwise.
 * @since  Moodle 3.2
 */
function signup_is_enabled_twiliootp() {
    global $CFG;
    if (!empty($CFG->registerauth)) {
            $authplugin = get_auth_plugin($CFG->registerauth);
            if ($authplugin->can_signup()) {
                return $authplugin;
            }  
    }
    return false;
}
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