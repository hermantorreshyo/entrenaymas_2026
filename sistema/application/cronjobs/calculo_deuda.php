<?php
// CRONJOB PARA AHORRAR DATOS CALCULADOS
date_default_timezone_set("America/Argentina/Buenos_Aires");
set_time_limit(0);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ERROR);

// Guardamos el log en un archivo
function logfile($str){
  $file='log_deuda.txt';
  echo $str."<br/>";
  file_put_contents(dirname(__FILE__)."/".$file, date("Y-m-d H:i:s")." ".$str."\n", FILE_APPEND);
}

// ===================================================

include("../../params.php");
include_once("../helpers/fecha_helper.php");
include_once("../libraries/Mandrill/Mandrill.php");
$id_empresa = 228;
$salida = "";
$fecha = date("Y-m-d");

$sql = "SELECT P.*, PC.nombre AS plan, C.nombre, C.apellido, P.id_sucursal, ";
$sql.= " IF(P.fecha = '0000-00-00','',DATE_FORMAT(P.fecha,'%d/%m/%Y')) AS fecha ";
$sql.= "FROM pres_prestamos P ";
$sql.= "INNER JOIN pres_planes_credito PC ON (P.id_plan = PC.id AND P.id_empresa = PC.id_empresa) ";
$sql.= "INNER JOIN pres_clientes C ON (P.id_cliente = C.id AND P.id_empresa = C.id_empresa) ";
$sql.= "WHERE P.id_empresa = $id_empresa ";
$q = mysqli_query($conx,$sql);
if (mysqli_num_rows($q)<=0) exit();

while(($row = mysqli_fetch_object($q))!==NULL) {

  $id = $row->id;

  // Contamos la cantidad de cuotas pagas
  $sql = "SELECT IF(COUNT(*) IS NULL,0,COUNT(*)) AS cantidad_cuotas_pagas ";
  $sql.= "FROM pres_prestamos_cuotas ";
  $sql.= "WHERE estado = 1 AND id_prestamo = $id ";
  $sql.= "AND id_empresa = $id_empresa ";
  //if (!empty($fecha)) $sql.= "AND fecha_vencimiento < '$fecha' ";
  $qq = mysqli_query($conx,$sql);
  $rr = mysqli_fetch_object($qq);
  $row->cantidad_cuotas_pagas = $rr->cantidad_cuotas_pagas;

  // Fecha de ultimo pago
  $sql = "SELECT MAX(fecha_pago) AS fecha_ultimo_pago ";
  $sql.= "FROM pres_prestamos_cuotas ";
  $sql.= "WHERE estado IN (1,2) AND id_prestamo = $id ";
  //if (!empty($fecha)) $sql.= "AND fecha_vencimiento < '$fecha' ";
  $sql.= "AND id_empresa = $id_empresa ";
  $qq = mysqli_query($conx,$sql);
  $rr = mysqli_fetch_object($qq);
  $row->fecha_ultimo_pago = is_null($rr->fecha_ultimo_pago) ? '' : ($rr->fecha_ultimo_pago);

  $sql = "SELECT monto_pagado ";
  $sql.= "FROM pres_prestamos_cuotas ";
  $sql.= "WHERE estado IN (1,2) AND id_prestamo = $id ";
  $sql.= "AND id_empresa = $id_empresa ";
  //if (!empty($fecha)) $sql.= "AND fecha_vencimiento < '$fecha' ";
  $sql.= "ORDER BY fecha_pago DESC ";
  $qq = mysqli_query($conx,$sql);
  $row->ultimo_pago = 0;
  if (mysqli_num_rows($qq) > 0) {
    $rr = mysqli_fetch_object($qq);
    $row->ultimo_pago = $rr->monto_pagado;
  }

  // Calculamos el proximo vencimiento
  $sql = "SELECT IF(MIN(fecha_vencimiento) IS NULL,'',MIN(fecha_vencimiento)) AS proximo_vencimiento ";
  $sql.= "FROM pres_prestamos_cuotas ";
  $sql.= "WHERE (estado = 0 OR estado = 2) AND id_prestamo = $id ";
  $sql.= "AND id_empresa = $id_empresa ";
  if (!empty($fecha)) $sql.= "AND fecha_vencimiento < '$fecha' ";
  $qq = mysqli_query($conx,$sql);
  $rr = mysqli_fetch_object($qq);
  $row->proximo_vencimiento = $rr->proximo_vencimiento;

  // Deuda vencida
  $sql = "SELECT IF(SUM(saldo) IS NULL,0,SUM(saldo)) AS deuda ";
  $sql.= "FROM pres_prestamos_cuotas ";
  $sql.= "WHERE estado != 1 AND id_prestamo = $id ";
  $sql.= "AND id_empresa = $id_empresa ";
  if (!empty($fecha)) $sql.= "AND fecha_vencimiento < '$fecha' ";
  $qq = mysqli_query($conx,$sql);
  $rr = mysqli_fetch_object($qq);
  $row->deuda_vencida = $rr->deuda;

  // Calculamos los dias de mora
  $datetime1 = date_create($fecha);
  $datetime2 = date_create($row->proximo_vencimiento);
  $interval = date_diff($datetime1, $datetime2);
  $row->dias_mora = (int)$interval->format('%a');

  $sql = "UPDATE pres_prestamos SET ";
  $sql.= " cantidad_cuotas_pagas = '$row->cantidad_cuotas_pagas', ";
  $sql.= " fecha_ultimo_pago = '$row->fecha_ultimo_pago', ";
  $sql.= " ultimo_pago = '$row->ultimo_pago', ";
  $sql.= " proximo_vencimiento = '$row->proximo_vencimiento', ";
  $sql.= " deuda_vencida = '$row->deuda_vencida', ";
  $sql.= " dias_mora = '$row->dias_mora' ";
  $sql.= "WHERE id = $id AND id_empresa = $id_empresa ";
  mysqli_query($conx,$sql);

  if ($row->dias_mora == 1) {
    $link = "app/#pres_cliente_acciones/".$row->id_cliente."/seguimiento";
    $nombre = $row->nombre." ".$row->apellido;
    $sql = "INSERT INTO com_log (id_empresa,fecha,link,texto,texto_2,estado,leida,importancia,id_sucursal) VALUES($id_empresa,NOW(),'$link','$nombre','Tiene 1 dia de mora.',0,0,'N','$row->id_sucursal') ";
    mysqli_query($conx,$sql);
  }

  echo "ID_CLIENTE: ".$row->id_cliente." | ID_PRESTAMO: ".$id." | PROX VENC: ".$row->proximo_vencimiento." | DIAS MORA: ".$row->dias_mora."<br/>";
}

echo "TERMINO";

// Mandamos el email
/*
mandrill_send(array(
  "to"=>"basile.matias99@gmail.com",
  "subject"=>"CALCULO MORA",
  "body"=>$salida,
));
*/
?>