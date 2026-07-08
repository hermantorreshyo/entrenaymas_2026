<?php
set_time_limit(0);
include_once("models/PaypalIPN.php");
include_once("models/Web_Model.php");
include_once("models/Viaje_Model.php");
include_once("sistema/application/libraries/Mandrill/Mandrill.php");
$web_model = new Web_Model($empresa->id,$conx);
$viaje_model = new Viaje_Model($empresa->id,$conx);

use PaypalIPN;
$ipn = new PaypalIPN();
$ipn->useSandbox();
$verified = $ipn->verifyIPN();
if (!$verified) return;

file_put_contents("ipn_paypal.txt",print_r($_POST,true));

$id_pedido = $_POST["custom"];
$paid_amount = $_POST["payment_gross"];
$status = $_POST["payment_status"];

// Obtenemos los datos del pedido
$sql_pedido = "SELECT R.*, ";
$sql_pedido.= " IF(R.fecha_reserva = '0000-00-00 00:00:00','',DATE_FORMAT(R.fecha_reserva,'%d/%m/%Y')) AS fecha_reserva, ";
$sql_pedido.= " IF(C.nombre IS NULL,'',C.nombre) AS cliente, ";
$sql_pedido.= " IF(C.email IS NULL,'',C.email) AS cliente_email, ";
$sql_pedido.= " IF(V.nombre IS NULL,'',V.nombre) AS viaje ";
$sql_pedido.= "FROM via_reservas R ";
$sql_pedido.= "LEFT JOIN clientes C ON (R.id_cliente = C.id AND R.id_empresa = C.id_empresa) ";
$sql_pedido.= "LEFT JOIN via_viajes V ON (R.id_viaje = V.id AND R.id_empresa = V.id_empresa) ";
$sql_pedido.= "WHERE R.id = $id_pedido AND R.id_empresa = $empresa->id ";

$q = mysqli_query($conx,$sql_pedido);
if (mysqli_num_rows($q)>0) {
  
  $pedido = mysqli_fetch_object($q);        
  
  // Primero de todo, finalizamos el pedido y actualizamos los datos
  $sql = "UPDATE via_reservas SET ";
  if ($status == "Completed") {
    $sql.= " id_tipo_estado = 6, "; // ESTADO FINALIZADO  
  }
  $sql.= " pagado = $paid_amount ";
  //$sql.= "codigo_autorizacion = '".$payment_info["response"]["collection"]["authorization_code"]."' ";
  $sql.= "WHERE id = $id_pedido AND id_empresa = $empresa->id ";
  mysqli_query($conx,$sql);

  $hoy = date("d/m/Y H:i");
  // Ponemos el pago que corresponde a esa reserva
  $sql = "INSERT INTO via_reservas_pagos (id_empresa,id_reserva,metodo,total,fecha) VALUES(";
  $sql.= "$empresa->id, $id_pedido, 'Paypal',$paid_amount,'$hoy')";
  mysqli_query($conx,$sql);
  
  // TODO: CREARIAMOS LA FACTURA ELECTRONICA Y EL PAGO ASOCIADO A LA MISMA, Y SE ENVIARIA AL CLIENTE
  
  // FINALMENTE:
  // Enviamos un email al administrador que la compra fue exitosa
  $clave_email = "reserva-viaje-ok-admin";
  $texto = $web_model->get_email($clave_email);
  if (empty($texto->nombre)) $texto->nombre = "Compra exitosa";
  if (empty($texto->texto)) $texto->texto = "La compra de {{cliente}} ha sido pagada.";
  $body = $texto->texto;
  $body = str_replace("{{cliente}}",$pedido->cliente,$body);
  $body = str_replace("'", "\"", $body);

  mandrill_send(array(
    "from_name"=>$empresa->nombre,
    "to"=>$empresa->email,
    "bcc"=>"basile.matias99@gmail.com",
    "subject"=>$texto->nombre,
    "body"=>$body,
  ));  
  
  // Y un email al usuario con el link de su pedido
  $clave_email = "reserva-viaje-ok";
  if ($pedido->idioma == "en") $clave_email = "reserva-viaje-ok-en";
  else if ($pedido->idioma == "pt") $clave_email = "reserva-viaje-ok-pt";
  $texto = $web_model->get_email($clave_email);
  if (empty($texto->nombre)) $texto->nombre = "Compra Exitosa";
  if (empty($texto->texto)) $texto->texto = "Muchas gracias por su compra!";
  $body = $texto->texto;
  $body = str_replace("{{cliente}}",$pedido->cliente,$body);
  $link_ver_pedido = mklink("sistema/reservas_asientos/function/voucher/".$pedido->id."/");
  $body = str_replace("{{link_ver_pedido}}",$link_ver_pedido,$body);
  $body = str_replace("'", "\"", $body);

  mandrill_send(array(
    "from_name"=>$empresa->nombre,
    "to"=>$pedido->cliente_email,
    "to_name"=>$pedido->cliente,
    "bcc"=>"basile.matias99@gmail.com",
    "subject"=>$texto->nombre,
    "body"=>$body,
  ));

  // Guardamos el evento
  $texto_evento = "Estado: Pagado. Pago por Paypal";
  $id_origen = 13; // VIAJES
  // En id_referencia guardamos el viaje que contrato
  // En id_relacion guardamos el pedido realizado
  $sql = "INSERT INTO crm_consultas (id_empresa,fecha,asunto,id_origen,id_referencia,id_contacto,texto,id_relacion) VALUES(";
  $sql.= "'$empresa->id',NOW(),'Reserva','$id_origen','$pedido->id_viaje','$pedido->id_cliente','$texto_evento','$pedido->id')";
  mysqli_query($conx,$sql);

  // Actualizamos el contacto con la ultima fecha de operacion
  $sql = "UPDATE clientes SET fecha_ult_operacion = NOW(), no_leido = 1 ";
  $sql.= "WHERE id = $pedido->id_cliente AND id_empresa = $empresa->id ";
  mysqli_query($conx,$sql);
  
}
?>