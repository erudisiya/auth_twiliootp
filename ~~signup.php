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
 * Open ID authentication. This file is a simple login entry point for OAuth identity providers.
 *
 * @package    auth_otp
 * @copyright  2021 Brain Station 23 ltd
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once('../../config.php');
global $USER, $DB, $OUTPUT, $CFG, $PAGE, $SITE, $SESSION;

if (isloggedin() ) {
    return redirect($CFG->wwwroot.'/my');
}
require_once($CFG->dirroot.'/auth/twiliootp/signup_form.php');
$PAGE->set_url('/auth/twiliootp/signup.php');
$PAGE->set_context(context_system::instance());
// If wantsurl is empty or /login/signup.php, override wanted URL.
// We do not want to end up here again if user clicks "Login".
if (empty($SESSION->wantsurl)) {
    $SESSION->wantsurl = $CFG->wwwroot . '/';
} else {
    $wantsurl = new moodle_url($SESSION->wantsurl);
    if ($PAGE->url->compare($wantsurl, URL_MATCH_BASE)) {
        $SESSION->wantsurl = $CFG->wwwroot . '/';
    }
}

$mform_signup = new login_signup_form;

$newaccount = get_string('newaccount');
$login      = get_string('login');

$PAGE->navbar->add($login);
$PAGE->navbar->add($newaccount);

$PAGE->set_pagelayout('login');
$PAGE->set_title($newaccount);
$PAGE->set_heading($SITE->fullname);

echo $OUTPUT->header();
$token = \core\session\manager::get_login_token();
$url = $CFG->wwwroot . "/login/index.php";
/*if ($mform_signup->is_cancelled()) {
     redirect($CFG->wwwroot.'/blocks/social_feed/list.php');
} elseif ($data = $mform_signup->get_data()) {
    $mform_signup->display();
}*/
if ($mform_signup->is_cancelled()) {
     redirect($CFG->wwwroot.'/blocks/social_feed/list.php');
} elseif ($data = $mform_signup->get_data()) {
    //print_r($data);die;
    if (isset($data->id) && !empty($data->id)) {
    } else {
    }
} else {
    $mform_signup->display();
}
echo $OUTPUT->footer();
