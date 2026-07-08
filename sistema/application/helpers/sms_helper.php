<?php

function send_sms_smart($config = array()) {
  $nombre = isset($config["nombre"]) ? $config["nombre"] : "";
  $numero = isset($config["numero"]) ? $config["numero"] : "";
  $usuario = isset($config["usuario"]) ? $config["usuario"] : "";
  $clave = isset($config["clave"]) ? $config["clave"] : "";
  $sms_corto = isset($config["sms_corto"]) ? $config["sms_corto"] : 1;
  if (empty($numero)) {
    return array(
      "error"=>1,
      "mensaje"=>"ERROR: Falta parametro numero",
    );
  }
  $texto = isset($config["texto"]) ? $config["texto"] : "";
  if (empty($texto)) {
    return array(
      "error"=>1,
      "mensaje"=>"ERROR: Falta parametro texto",
    );
  }
  $ch = curl_init();
  $mensaje = urlencode($texto);
  if ($sms_corto == 1) {
    // Por defecto mandamos SMS corto, que te garantiza que llega
    $usuario = (empty($usuario)) ? "ToqueApp_sc" : $usuario;
    $clave = (empty($clave)) ? "Toque30te$" : $clave;
    $url = "http://www.smstartplus.com/api_sc_simple.php/?usuario=$usuario&clave=$clave&celular=$numero&mensaje=$mensaje";  
  } else {
    // Sino tambien dejamos la forma anterior
    $usuario = (empty($usuario)) ? "toqueapp" : $usuario;
    $clave = (empty($clave)) ? "italia" : $clave;
    $url = "http://www.smstartplus.com/api_send_url.php/?usuario=$usuario&clave=$clave&celulares=$numero&nom=$nombre&mensaje=".urlencode($texto);
  }
  curl_setopt($ch,CURLOPT_URL, $url);
  curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
  $result = curl_exec($ch);
  curl_close($ch);
  return array(
    "error"=>0,
    "mensaje"=>$result,
  );
}

// Usa Telerivet
function send_sms($config = array()) {
  $numero = isset($config["numero"]) ? $config["numero"] : "";
  if (empty($numero)) {
    return array(
      "error"=>1,
      "mensaje"=>"ERROR: Falta parametro numero",
    );
  }
  $texto = isset($config["texto"]) ? $config["texto"] : "";
  if (empty($texto)) {
    return array(
      "error"=>1,
      "texto"=>"ERROR: Falta parametro texto",
    );
  }
  $api_key = "PRiJ6SQtxdWWWOaxHvbyMKSTkOUgaKOy"; // see https://telerivet.com/dashboard/api
  $project_id = "PJ9cdae1013dcbab2f";
  require_once 'application/libraries/telerivet/telerivet.php';
  $api = new Telerivet_API($api_key);
  $project = $api->initProjectById($project_id);
  $error = 0;
  $mensaje = "";
  try {
    $contact = $project->sendMessage(array(
      'to_number' => $numero,
      'content' => $texto,
    ));
  } catch (Telerivet_Exception $ex) {
    $error = 1;
    $mensaje = htmlentities($ex->getMessage());
  }
  return array(
    "error"=>$error,
    "mensaje"=>$mensaje,
  );
}
?>