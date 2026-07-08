<?php
set_time_limit(0);

$url = "https://api.instagram.com/oauth/access_token";
$postData = array(
	"client_id" => "864f43caeee242919283d295007c6d0c", 
	"client_secret" => "f232b14aec564428833daf4e0d7eb3ae",
	"grant_type" => "authorization_code",
	"redirect_uri" => "http://likeagreenbuddha.com/",
	"code" => "52ad4b7521c345c4a31326b0a9cee0f0",
);
/*
curl.exe -F client_id=864f43caeee242919283d295007c6d0c -F client_secret=f232b14aec564428833daf4e0d7eb3ae -F grant_type=authorization_code -F redirect_uri=http://likeagreenbuddha.com -F code=559ed033693f491db424d68e7dbcce51 https://api.instagram.com/oauth/access_token
*/
$elements = array();
foreach ($postData as $name=>$value) {
   $elements[] = "$name=".urlencode($value);
}
$handler = curl_init();
curl_setopt($handler, CURLOPT_URL, $url);
curl_setopt($handler, CURLOPT_POST,true);
curl_setopt($handler, CURLOPT_POSTFIELDS, $elements);
$response = curl_exec($handler);
curl_close($handler);
print_r($response);


?>