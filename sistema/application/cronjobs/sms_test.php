<?php
$bloque = "";
$bloque.= "2352444378,2352444378,Hola matias";

$url = 'http://servicio.smsmasivos.com.ar/enviar_sms_bloque.asp';
$fields = array(
	'usuario' => "MATIASBASILLE",
	'clave' => "MATIASBASILLE403",
	'separadorcampos' => "coma",
	'bloque' => urlencode($bloque),
);

$fields_string = "";
foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
rtrim($fields_string, '&');
$ch = curl_init();
curl_setopt($ch,CURLOPT_URL, $url);
curl_setopt($ch,CURLOPT_POST, count($fields));
curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
$result = curl_exec($ch);
curl_close($ch);
echo $result;
?>