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
 * Admin settings and defaults
 *
 * @package auth_userkey
 * @copyright  2017 Stephen Bourget
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    /*$settings->add(new admin_setting_configcheckbox('auth_twiliootp/enabletwilio',
        get_string('enabletwilio', 'auth_twiliootp'),
        get_string('enabletwilio_help', 'auth_twiliootp'), 0, PARAM_INT));
*/
    $settings->add(new admin_setting_configtext('auth_twiliootp/twilio_ssid',
        get_string('twiliossid', 'auth_twiliootp'),
        get_string('twiliossid_help', 'auth_twiliootp'), '', PARAM_TEXT));

    $settings->add(new admin_setting_configtext('auth_twiliootp/twilio_token',
        get_string('twiliotoken', 'auth_twiliootp'),
        get_string('twiliotoken_help', 'auth_twiliootp'), '', PARAM_TEXT));

    $settings->add(new admin_setting_configtext('auth_twiliootp/twilio_number',
        get_string('twilionumber', 'auth_twiliootp'),
        get_string('twilionumber_help', 'auth_twiliootp'), '', PARAM_TEXT));

    /*$settings->add(new admin_setting_configtext('auth_twiliootp/revokethreshold',
        get_string('revokethreshold', 'auth_twiliootp'),
        get_string('revokethreshold_help', 'auth_twiliootp'), 3, PARAM_INT));*/

    $settings->add(new admin_setting_configtext(
        'auth_twiliootp/validityperiod',
        get_string('validityperiod', 'auth_twiliootp'),
        get_string('validityperiod_help', 'auth_twiliootp'), 0, PARAM_INT));

    $settings->add(new admin_setting_configtext(
        'auth_twiliootp/minrequestperiod',
        get_string('minrequestperiod', 'auth_twiliootp'),
        get_string('minrequestperiod_help', 'auth_twiliootp'), 0, PARAM_INT));
}
