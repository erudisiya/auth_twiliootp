<?php

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
