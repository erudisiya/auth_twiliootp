<?php
require_once("$CFG->libdir/externallib.php");
require_once("$CFG->dirroot/auth/twiliootp/twilio/vendor/autoload.php");
require_once("$CFG->dirroot/auth/twiliootp/lib.php");
require_once("$CFG->dirroot/auth/twiliootp/twiliootperrorcodes.php");
use Twilio\Rest\Client;
class moodle_auth_twiliootp_external extends external_api {
	public static function twiliootp_js_method_parameters() {
		return new external_function_parameters(
            array(
                'flag' => new external_value(PARAM_RAW, 'see flag'),
                'phone' => new external_value(PARAM_RAW, 'see flag'),
                'username' => new external_value(PARAM_RAW, 'see flag'),
                'useremail' => new external_value(PARAM_RAW, 'see flag'),
                'usercountry' => new external_value(PARAM_RAW, 'see flag')
            )
        );
	}
	public static function twiliootp_js_method_returns() {
        return new external_single_structure(
            array(
                'status' => new external_value(PARAM_INT, 'Status code: see TwilioOTPErrorCodes'),
	            'errors' => new external_multiple_structure(
	                new external_value(PARAM_INT, 'Error code')
	            )
            )
        );
    }
	public static function twiliootp_js_method($flag,$phone,$username,$useremail,$usercountry) {
		global $DB,$SITE;
		$plugin = get_auth_plugin('twiliootp');
		$sid = $plugin->config->twilio_ssid;
		$token = $plugin->config->twilio_token;
		$otp_validity = $plugin->config->validityperiod;
		$otp_validity_sec = $otp_validity * 60;
		$country_code = country_code_twiliootp($usercountry);
		$sitename = $SITE->fullname;
	    $twilio = new Client($sid, $token);
	    $otp = mt_rand(1000, 9999);
	    $otp_expiry = time() + $otp_validity_sec;
	    $tonumber = $country_code.''.$phone;
	    $fromnumber = $plugin->config->twilio_number;
	    $error = false;
	    $errors = array();

	    $isusername = $DB->get_record('user',array('username' => $username));
	    $isuseremail = $DB->get_record('user',array('email' => $useremail));
	    $isuserphone = $DB->get_record('user',array('phone2' => $phone));
	    if(isset($isusername) && !empty($isusername)){
	    	$errors[] = TwilioOTPErrorCodes::USERNAME_TAKEN;
	    }
	    if(isset($isuseremail) && !empty($isuseremail)){
	    	$errors[] = TwilioOTPErrorCodes::EMAIL_TAKEN;
	    }
	    /*if(isset($isuserphone) && !empty($isuserphone)){
	    	$status = 6;
	    	//$error = true;
	    	$errors[] = TwilioOTPErrorCodes::PHONE_TAKEN;
	    }*/
	    $str = array("otp" => $otp, "sitename" => $sitename, "otp_validity" => $otp_validity);
	    if(empty($error)){
		    try {
			    $lookup = $twilio->lookups->v1->phoneNumbers($phone)->fetch(array("countryCode" => $usercountry));
			    try {
				    $message = $twilio->messages
				        ->create("whatsapp:".$lookup->phoneNumber, // to
				            array(
				                "from" => "whatsapp:".$fromnumber,
				                "body" => get_string('otpmessage', 'auth_twiliootp', $str)
				            )
				        );
				    //$status = 1;

				} catch (\Twilio\Exceptions\TwilioException $e) {
				    $errors[] = TwilioOTPErrorCodes::TWILIO_ERROR;
				} catch (Exception $e) {
				    $errors[] = TwilioOTPErrorCodes::TWILIO_ERROR;
				}
			} catch (Exception $e) {
			    $errors[] = TwilioOTPErrorCodes::INVALID_PHONE;

			}
		}
	    
	    $result = array();
	    if (empty($errors)) {
	    	$data = new stdClass();
		    $data->username = $username;
		    $data->email = $useremail;
		    $data->phone = $phone;
		    $data->countrycode = $usercountry;
		    $data->otp_code = $otp;
		    $data->otp_expiry = $otp_expiry;
		    $data->verification_status = '';
		    $data->otpcreated = time();
		    $data->attempts_count = '';
		    $DB->insert_record('auth_twiliootp_create', $data);
	        $result['status'] = TwilioOTPErrorCodes::SUCCESS;
	        $result['errors'] = $errors;
	    } else {
	        $result['status'] = TwilioOTPErrorCodes::GENERAL_ERROR; // You might define a general error code for unspecified issues
	        $result['errors'] = $errors;
	    }
	    return $result;
	}
	public static function success_twiliootp_url_parameters() {
		return new external_function_parameters(
            array(
                'flag' => new external_value(PARAM_RAW, 'see flag'),
                'phone' => new external_value(PARAM_RAW, 'see flag'),
                'username' => new external_value(PARAM_RAW, 'see flag'),
                'useremail' => new external_value(PARAM_RAW, 'see flag'),
                'otp' => new external_value(PARAM_RAW, 'see flag')
            )
        );
	}
	public static function success_twiliootp_url_returns() {
		return new external_single_structure(
            array(
                'status' => new external_value(PARAM_RAW, 'status: true if success'),
                'errors' => new external_multiple_structure(
	                new external_value(PARAM_INT, 'Error code')
	            )
            )
        );
	}
	public static function success_twiliootp_url($flag,$phone,$username,$useremail,$otp) {
		global $DB;
		$plugin = get_auth_plugin('twiliootp');
		$otp_record = $DB->get_record_sql('SELECT * FROM {auth_twiliootp_create} WHERE username = ? AND email = ? AND phone = ? ORDER BY id DESC LIMIT 1',[$username,$useremail,$phone]);
		$errors = array();
		$verification_status = false;
		if ($otp_record) {
		    if ($otp_record->otp_code == $otp) {
		        $current_time = time();
		        $otp_expiry_time = $otp_record->otp_expiry;
		        if ($current_time <= $otp_expiry_time) {
		            $verification_status = true;
		        } else {
		            $verification_status = false;
		            $errors[] = TwilioOTPErrorCodes::OTP_EXPIRED;
		        }
		    } else {
		        $verification_status = false;
		        $errors[] = TwilioOTPErrorCodes::INVALID_OTP;
		    }
		} else {
		    $verification_status = false;
		    $errors[] = TwilioOTPErrorCodes::OTP_RECORD_NOT_FOUND;
		}
		$previousattempt = $otp_record->attempts_count;
		if (empty($errors)) {
			$updateobj = new stdClass();
    		$updateobj->id = $otp_record->id;
    		$updateobj->verification_status = 1;//1=verified 0=not verfied
    		$updateobj->attempts_count = $previousattempt + 1;
    		$DB->update_record('auth_twiliootp_create', $updateobj, true);
    		$result['status'] = TwilioOTPErrorCodes::SUCCESS;
	        $result['errors'] = $errors;
		} else {
			$updateobj = new stdClass();
    		$updateobj->id = $otp_record->id;
    		$updateobj->attempts_count = $previousattempt + 1;
    		$DB->update_record('auth_twiliootp_create', $updateobj, true);
    		$result['status'] = TwilioOTPErrorCodes::GENERAL_ERROR; // You might define a general error code for unspecified issues
	        $result['errors'] = $errors;
		}
		return $result;
        die;
	}
}