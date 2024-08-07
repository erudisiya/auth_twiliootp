<?php

/*$url = 'https://api.twilio.com';

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// If SSL certificate validation fails, you can set this to false (not recommended for production)
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

$response = curl_exec($ch);

if ($response === false) {
    echo "Error fetching URL: " . curl_error($ch);
} else {
    echo $response;
}

curl_close($ch);



//echo $response;
die;*/
//
    // Update the path below to your autoload.php,
    // see https://getcomposer.org/doc/01-basic-usage.md
    require_once 'vendor/autoload.php';
    use Twilio\Rest\Client;
    
    $sid    = "ACff82cabd3320d18bc5257cfec4f5c074";
    $token  = "6fcfdd1cc0ddc2e1c324b0bb7c00028c";
    $twilio = new Client($sid, $token);
    $otp = mt_rand(1000, 9999);
    $message = $twilio->messages
      ->create("whatsapp:+918296198350", // to
        array(
          "from" => "whatsapp:+14155238886",
          "body" => 'Your OTP for login is :'.$otp
        )
      );
      //echo 'here';die;
print_r($message->sid);





