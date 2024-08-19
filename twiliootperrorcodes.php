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
 * Twili OTP authentication plugin.
 *
 * @package    auth_twiliootp
 * @author     Erudisiya <contact.erudisiya@gmail.com>
 * @copyright  2024 Erudisiya Team(https://erudisiya.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class TwilioOTPErrorCodes {
    const GENERAL_ERROR = 0;
    const SUCCESS = 1;
    const TWILIO_ERROR = 2;
    const INVALID_PHONE = 3;
    const USERNAME_TAKEN = 4;
    const EMAIL_TAKEN = 5;
    const PHONE_TAKEN = 6;
    const OTP_EXPIRED = 7;
    const INVALID_OTP = 8;
    const OTP_RECORD_NOT_FOUND = 9;
}
