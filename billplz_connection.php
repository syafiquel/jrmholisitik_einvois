<?php
$api_key = '17f47baf-94b3-4550-8cdd-d741325ae614';
// $host = 'https://www.billplz.com/api/v3/bills/';
$host = 'https://www.billplz-sandbox.com/api/v3/bills/';

$collection_id = 's_8pap1x';


$data = array(
          'collection_id' => $collection_id,
          'email' => 'customer@email.com',
          'mobile' => '60123456789',
          'name' => "Jone Doe",
          'amount' => 2000, // RM20
		  'description' => 'Test',
          'callback_url' => "http://yourwebsite.com/return_url"
);
$process = curl_init($host );

curl_setopt($process, CURLOPT_HEADER, 1);
curl_setopt($process, CURLOPT_USERPWD, $api_key . ":");
curl_setopt($process, CURLOPT_TIMEOUT, 30);
curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($process, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($process, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($process, CURLOPT_POSTFIELDS, http_build_query($data) );

$return = curl_exec($process);
curl_close($process);

echo '<pre>'.print_r($return, true) .'</pre>';
