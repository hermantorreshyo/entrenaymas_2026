<?php
/*
En este script realizamos varias tareas

  + Creamos el remito de la empresa correspondiente al mes
  + Enviamos un email antes de que este por vencer la cuenta, con ese remito y con link a MercadoPago para pagarlo
  + Si la fecha de vencimiento es de hoy, le recordamos que hoy vence su remito
  + Si se vencio por 1 dia, le mandamos de nuevo y le recordamos
  + A los X dias lo mismo
  + Cuando se cumple la fecha de suspension, le mandamos que le suspendimos la cuenta que para activarla de nuevo tiene que pagar

*/
date_default_timezone_set("America/Argentina/Buenos_Aires");
set_time_limit(0);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ERROR);

// Guardamos el log en un archivo
function logfile($str){
  $file='log_vencimientos.txt';
  $linea = date("Y-m-d H:i:s")." ".$str;
  file_put_contents(dirname(__FILE__)."/".$file, $linea."\n", FILE_APPEND);
  echo $linea."<br/>";
}

// ===================================================

include("../../params.php");
include_once("../helpers/fatal_helper.php");
include_once("../libraries/Mandrill/Mandrill.php");
include_once("../helpers/fecha_helper.php");

$id_empresa_varcreative = 99;
$dia = date("d");
$hoy = date("Y-m-d");
$hora = date("H:i:s");
$numero_referencia = date("Ym");
$notificaciones = array();
$dias_antes_vencimiento = 5;

// Seleccionamos las empresas que tienen fecha de vencimiento dentro de [$dias_antes_vencimiento] dias
$sql = "SELECT E.*, IF(P.boton_pago_mp IS NULL,'',P.boton_pago_mp) AS boton_pago_mp ";
$sql.= "FROM empresas E ";
$sql.= "LEFT JOIN planes P ON (E.id_plan = P.id) ";
$sql.= "WHERE '$hoy' = DATE_SUB(E.fecha_prox_venc,INTERVAL $dias_antes_vencimiento DAY) ";
$sql.= "AND E.administrar_pagos = 1 ";
$sql.= "AND E.activo = 1 ";
logfile("BUSCAR EMPRESAS: ".$sql);
$q_empresas = mysqli_query($conx,$sql);
// Si no hay nada para seleccionar, salimos
if (mysqli_num_rows($q_empresas)<=0) exit();

while(($empresa = mysqli_fetch_object($q_empresas))!==NULL) {

  // Controlamos que no exista un remito para el mismo periodo
  $sql = "SELECT * FROM facturas ";
  $sql.= "WHERE id_empresa = $id_empresa_varcreative ";
  $sql.= "AND id_cliente = $empresa->id ";
  $sql.= "AND numero_referencia = '$numero_referencia' ";
  $q_controlar_fact = mysqli_query($conx,$sql);
  if (mysqli_num_rows($q_controlar_fact)>0) continue;

  // TODO: El dia de mañana, en vez de ser remitos seran facturas conectadas con AFIP

  // Creamos los remitos automáticos
  $remito = new stdClass();
  $sql = "SELECT IF(MAX(numero) IS NULL,0,MAX(numero)) AS numero ";
  $sql.= "FROM facturas ";
  $sql.= "WHERE id_tipo_comprobante = 999 ";
  $sql.= "AND id_empresa = $id_empresa_varcreative ";
  logfile("BUSCAR PROXIMO REMITO: ".$sql);
  $q_remito = mysqli_query($conx,$sql);
  $row = mysqli_fetch_object($q_remito);
  $numero_remito = $row->numero + 1;

  $comprobante = "R 0001-".str_pad($numero_remito, 8, "0", STR_PAD_LEFT);
  $hash = md5($id_empresa_varcreative."-".$empresa->id."-".$comprobante);
  
  $ahora = new DateTime($hoy);
  $mes = get_mes($ahora->format("m"));
  $anio = $ahora->format("Y");
  $observaciones = $mes." ".$anio;

  // Insertamos la factura
  $sql = "INSERT INTO facturas (";
  $sql.= " id_empresa, fecha, hora, punto_venta, numero, comprobante, ";
  $sql.= " id_cliente, id_tipo_comprobante, total, subtotal, ";
  $sql.= " tipo_pago, estado, hash, observaciones, numero_referencia ";
  $sql.= ") VALUES (";
  $sql.= " '$id_empresa_varcreative', '$hoy', '$hora', 1, '$numero_remito', '$comprobante', ";
  $sql.= " '$empresa->id', '999', $empresa->costo, $empresa->costo,  ";
  $sql.= " 'C',1,'$hash', '$observaciones', '$numero_referencia' ";
  $sql.= ")";
  logfile("INSERTAR FACTURA: ".$sql);
  $q_factura = mysqli_query($conx,$sql);
  $id_remito = mysqli_insert_id($conx);

  // Insertamos una fila en el remito
  $sql = "INSERT INTO facturas_items (";
  $sql.= " id_empresa, id_factura, cantidad, porc_iva, id_tipo_alicuota_iva, ";
  $sql.= " neto, precio, nombre, iva, total_sin_iva, total_con_iva ";
  $sql.= ") VALUES (";
  $sql.= " $id_empresa_varcreative, $id_remito, 1, 0, 0, ";
  $sql.= " $empresa->costo, $empresa->costo, '$observaciones', 0, $empresa->costo, $empresa->costo ";
  $sql.= ")";
  logfile("INSERTAR FACTURA_ITEMS: ".$sql);
  mysqli_query($conx,$sql);

  // Insertamos
  $sql = "INSERT INTO empresas_facturas ( ";
  $sql.= " id_empresa, numero, monto, vencimiento, pagada ";
  $sql.= ") VALUES (";
  $sql.= " '$empresa->id', '$numero_remito', '$empresa->costo', '$empresa->fecha_prox_venc', 0 ";
  $sql.= ")";
  logfile("INSERTAR EMPRESAS_FACTURAS: ".$sql);
  mysqli_query($conx,$sql);

  // Movemos la fecha de vencimiento
  /*
  $empresa->periodo_fact = (!empty($empresa->periodo_fact)) ? $empresa->periodo_fact : "+1 month";
  $ahora->modify($empresa->periodo_fact);
  $sql = "UPDATE empresas SET fecha_prox_venc = '".$ahora->format("Y-m-d")."' ";
  $sql.= "WHERE id = $empresa->id ";
  logfile("ACTUALIZAR PROX_FECHA_VENCIMIENTO: ".$sql);
  mysqli_query($conx,$sql);
  */

  // Buscamos el email por defecto para mandar (depende del proyecto)
  $id_proyecto = 0;
  if ($empresa->id_proyecto == 1) $id_proyecto = 127; // PYMVAR
  else if ($empresa->id_proyecto == 2) $id_proyecto = 119; // SHOPVAR
  else if ($empresa->id_proyecto == 3) $id_proyecto = 118; // INMOVAR
  else if ($empresa->id_proyecto == 5) $id_proyecto = 116; // COLVAR
  $clave = "email-factura";
  $sql = "SELECT * FROM crm_emails_templates WHERE id_empresa = $id_proyecto AND clave = '$clave' ";
  logfile("BUSCAR EMAIL: ".$sql);
  $q_template = mysqli_query($conx,$sql);
  if (mysqli_num_rows($q_template)>0) {

    $email_template = mysqli_fetch_object($q_template);
    $email_template->texto = str_replace("{{nombre}}", $empresa->nombre, $email_template->texto);
    $email_template->texto = str_replace("{{mes}}", $observaciones, $email_template->texto);
    $email_template->texto = str_replace("{{hash}}", $hash, $email_template->texto);
    $email_template->texto = str_replace("{{fecha_vencimiento}}", fecha_es($empresa->fecha_prox_venc), $email_template->texto);
    $email_template->texto = str_replace("{{boton_pago_mp}}", $empresa->boton_pago_mp, $email_template->texto);

    // Enviamos un email al cliente
    mandrill_send(array(
      "to"=>$empresa->email,
      "subject"=>$email_template->nombre,
      "body"=>$email_template->texto,
      "reply_to"=>"info@varcreative.com",
      "bcc"=>"basile.matias99@gmail.com",
    )); 
    logfile("ENVIAR EMAIL: ".$empresa->email);

    $notificaciones[] = $empresa->nombre." (".$empresa->email.")";
  }
}

// Me envio un email con el resumen de empresas a las cuales se les notifico el pago
/*
if (sizeof($notificaciones)>0) {
  $body = "";
  foreach($notificaciones as $n) $body.= $n."<br/>";
  mandrill_send(array(
    "to"=>"basile.matias99@gmail.com",
    "subject"=>"Notificacion de pago",
    "body"=>$body,
  )); 
}
*/
?>