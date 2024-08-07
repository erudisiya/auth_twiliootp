<?php
$url = 'https://api.twilio.com';

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
curl_setopt($ch, CURLOPT_CAINFO, "C:\Users\Suman_Erudisiya\Documents\cacert.pem");

$response = curl_exec($ch);

if ($response === false) {
    echo "Error fetching URL: " . curl_error($ch);
} else {
    echo $response;
}

curl_close($ch);
?>
