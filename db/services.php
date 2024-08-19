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
 * Twili OTP authentication plugin services.
 *
 * @package    auth_twiliootp
 * @author     Erudisiya <contact.erudisiya@gmail.com>
 * @copyright  2024 Erudisiya Team(https://erudisiya.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$services = array(
    'moodle_auth_twiliootp' => array(
        'functions' => array ('moodle_twiliootp_twiliootp_js_settings','moodle_twiliootp_success_twiliootp_url'),
        'restrictedusers' => 0,
        'enabled' => 1,
        'shortname' => 'authtwiliootp',
    )
);

$functions = array(
    'moodle_twiliootp_twiliootp_js_settings' => array(
        'classname'   => 'moodle_auth_twiliootp_external',
        'methodname'  => 'twiliootp_js_method',
        'classpath'   => 'auth/twiliootp/externallib.php',
        'description' => 'Return one time key based login URL',
        'type'        => 'write',
        'ajax'  => 'true',
        'loginrequired' => false,
    ),
    'moodle_twiliootp_success_twiliootp_url' => array(
        'classname'   => 'moodle_auth_twiliootp_external',
        'methodname'  => 'success_twiliootp_url',
        'classpath'   => 'auth/twiliootp/externallib.php',
        'description' => 'Return one time key based login URL',
        'type'        => 'write',
        'ajax'  => 'true',
        'loginrequired' => false,
    )
);

