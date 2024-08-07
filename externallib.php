<?php
require_once("$CFG->libdir/externallib.php");
require_once("$CFG->dirroot/auth/twiliootp/twilio/vendor/autoload.php");
require_once("$CFG->dirroot/auth/twiliootp/lib.php");
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
                'status' => new external_value(PARAM_RAW, 'status: true if success'),
                /*'error_code' => new external_value(PARAM_RAW, 'error_code: error reason flag')*/
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
	    //print_r($otp_expiry);
	    $tonumber = $country_code.''.$phone;
	    $fromnumber = $plugin->config->twilio_number;
	    $status = 0;
	    $error = false;
	    $isusername = $DB->get_record('user',array('username' => $username));
	    $isuseremail = $DB->get_record('user',array('email' => $useremail));
	    $isuserphone = $DB->get_record('user',array('phone2' => $phone));
	    if(isset($isusername) && !empty($isusername)){
	    	$status = 4;
	    	$error = true;
	    }
	    if(isset($isuseremail) && !empty($isuseremail)){
	    	$status = 5;
	    	$error = true;
	    }
	    if(isset($isuserphone) && !empty($isuserphone)){
	    	$status = 6;
	    	$error = true;
	    }
	    //print_r($tonumber);die;
	    if(empty($error)){
		    try {
			    $lookup = $twilio->lookups->v1->phoneNumbers($phone)->fetch(array("countryCode" => $usercountry));
			    try {
				    $message = $twilio->messages
				        ->create("whatsapp:".$lookup->phoneNumber, // to
				            array(
				                "from" => "whatsapp:".$fromnumber,
				                "body" => $otp.' is your OTP for create new account in '.$sitename.'. Note that the OTP will be valid for next '.$otp_validity.' mins.'
				            )
				        );
				    $status = 1;
				    // If no exception is thrown, the message was sent successfully
				    //echo "Message sent successfully.";

				} catch (\Twilio\Exceptions\TwilioException $e) {
				    // Handle specific Twilio exceptions
				    //echo 'Message could not be sent. Twilio Error: ' . $e->getMessage();
				    $status = 2;
				} catch (Exception $e) {
				    // Handle other exceptions
				    //echo 'Message could not be sent.';
				    $status = 2;
				}
			} catch (Exception $e) {
			    //echo 'Invalid phone number.';
			    $status = 3;

			}
		}
	    //print_r($message);
	    //die;
	    if($status == 1){
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
	    }
	    
	    $result = array();
	    $result['status'] = $status;
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
                'status' => new external_value(PARAM_RAW, 'status: true if success')
            )
        );
	}
	public static function success_twiliootp_url($flag,$phone,$username,$useremail,$otp) {
		global $DB;
		$plugin = get_auth_plugin('twiliootp');
		$otp_record = $DB->get_record_sql('SELECT * FROM {auth_twiliootp_create} WHERE username = ? AND email = ? AND phone = ? ORDER BY id DESC LIMIT 1',[$username,$useremail,$phone]);
		//print_r($otp_record);
		$verification_status = false;
		if ($otp_record) {
		    if ($otp_record->otp_code == $otp) {
		        $current_time = time();
		        $otp_expiry_time = $otp_record->otp_expiry;
		        //echo '1';
		        if ($current_time <= $otp_expiry_time) {
		            $verification_status = true;
		            //echo '2';
		        } else {
		            $verification_status = false;
		            //echo '3';
		        }
		    } else {
		        $verification_status = false;
		        //echo '4';
		    }
		} else {
		    $verification_status = false;
		    //echo '5';
		}
		$previousattempt = $otp_record->attempts_count;
		if($verification_status){
			$updateobj = new stdClass();
    		$updateobj->id = $otp_record->id;
    		$updateobj->verification_status = 1;//1=verified 0=not verfied
    		$updateobj->attempts_count = $previousattempt + 1;
    		$DB->update_record('auth_twiliootp_create', $updateobj, true);
		} else {
			$updateobj = new stdClass();
    		$updateobj->id = $otp_record->id;
    		$updateobj->attempts_count = $previousattempt + 1;
    		$DB->update_record('auth_twiliootp_create', $updateobj, true);
		}
		$result['status'] = $verification_status;
		return $result;
        die;
	}
}