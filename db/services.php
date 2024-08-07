<?php
defined('MOODLE_INTERNAL') || die();
/*$services = [
    'moodle_auth_twiliootp' = [
        'functions' = ['moodle_twiliootp_twiliootp_js_settings', 'moodle_twiliootp_success_twiliootp_url'],
        'requiredcapability' => '',
        'restrictedusers' => 0,
        'enabled' => 1,
        'shortname' => 'authtwiliootp',
    ],
];*/
$services = array(
    'moodle_auth_twiliootp' => array(
        'functions' => array ('moodle_twiliootp_twiliootp_js_settings','moodle_twiliootp_success_twiliootp_url'),
        'restrictedusers' => 0,
        'enabled' => 1,
        'shortname' => 'authtwiliootp',
    )
);
/*$functions = [
    'moodle_twiliootp_twiliootp_js_settings' = [
        'classname' => 'moodle_auth_twiliootp_external',
        'methodname' => 'twiliootp_js_method',
        'classpath' => 'auth/twiliootp/externallib.php',
        'description' => 'Update information after twiliootp Successful Connect',
        'type' => 'write',
        'ajax' => true,
        'loginrequired' => true,
    ],
    'moodle_twiliootp_success_twiliootp_url' = [
        'classname' => 'moodle_auth_twiliootp_external',
        'methodname' => 'success_twiliootp_url',
        'classpath' => 'auth/twiliootp/externallib.php',
        'description' => 'Update information after twiliootp Successful Payment',
        'type' => 'write',
        'ajax' => true,
    ],
];*/

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

