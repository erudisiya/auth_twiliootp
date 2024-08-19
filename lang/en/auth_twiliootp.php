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
 * Twili OTP authentication plugin lang en.
 *
 * @package    auth_twiliootp
 * @author     Erudisiya <contact.erudisiya@gmail.com>
 * @copyright  2024 Erudisiya Team(https://erudisiya.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Twilio OTP Authentication';
$string['twiliootp:config'] = 'Configure Twilio OTP Authentication';
$string['twiliootp:verifyotp'] = 'Verify OTP';
$string['createnewbutton'] = 'Create new account using OTP';
//signup_form
$string['mobilenumber'] = 'Mobile Number(without country code)';
//settings
$string['enabletwilio'] = 'Enable Twilio WhatsApp Service';
$string['enabletwilio_help'] = 'Enable Twilio WhatsApp SMS Service';
$string['twiliossid'] = 'Twilio ssid';
$string['twiliossid_help'] = 'Twilio ssid';
$string['twiliotoken'] = 'Twilio Token';
$string['twiliotoken_help'] = 'Twilio Token';
$string['twilionumber'] = 'Twilio Number';
$string['twilionumber_help'] = 'Twilio Registered Number';
$string['validityperiod'] = 'Validity period';
$string['validityperiod_help'] = 'A time in minutes after which duration, the OTP will expire and become invalid (0 - unlimited).';
$string['minrequestperiod'] = 'Minium period';
$string['minrequestperiod_help'] = 'A time in seconds after which another password can be generated (0 - unrestricted). Enabled logstore required.';
$string['usernametaken'] = 'This username is already taken. Please choose a different username.';
$string['useremailtaken'] = 'This email is already taken. Please choose a different email.';
$string['userphonetaken'] = 'This number is already taken. Please choose a different number.';
$string['otpsentsucess'] = 'OTP sent successfully to mobile number';
$string['otpnotsent'] = 'OTP Could not sent.please try again';
$string['invalidnumber'] = 'Invalid mobile number.please check.';
$string['sendotpbtn'] = 'Send OTP';
$string['verifyotpbtn'] = 'Verify OTP';
$string['verifedsucess'] = 'Your phone number has been verified successfully!';
$string['verifedunsucess'] = 'Verification unsuccessful, Please Try again!';
$string['expiredotp'] = 'OTP is expired, Please Try again!';
$string['otpmessage'] = '{$a->otp} is your OTP for create new account in {$a->sitename}. Note that the OTP will be valid for next {$a->otp_validity} mins.';

