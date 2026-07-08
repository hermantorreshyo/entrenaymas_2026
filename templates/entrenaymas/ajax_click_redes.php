<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include("includes/init.php");

$ref = isset($_POST["ref"]) ? $_POST["ref"] : "";
$profesional_email = isset($_POST["profesional_email"]) ? $_POST["profesional_email"] : "";
$id_usuario = isset($_POST["id_usuario"]) ? $_POST["id_usuario"] : 0;
$id_empresa = isset($_POST["id_empresa"]) ? $_POST["id_empresa"] : 0;
$ip_cliente = isset($_POST["ip_cliente"]) ? $_POST["ip_cliente"] : 0;

$web_model->click_redes(array(
  "red"=>$ref,
  "id_usuario"=>$id_usuario,
  "id_empresa"=>$id_empresa,
  "ip_cliente"=>$ip_cliente,
  "profesional_email"=>$profesional_email,
));

?>