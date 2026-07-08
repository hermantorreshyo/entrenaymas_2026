<?php
// CRONJOB QUE GENERA LOS COMPROBANTES DE LAS EMPRESAS
date_default_timezone_set("America/Argentina/Buenos_Aires");
set_time_limit(0);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ERROR);

// Guardamos el log en un archivo
function logfile($str){
  $file='log_mora.txt';
  echo $str."<br/>";
  file_put_contents(dirname(__FILE__)."/".$file, date("Y-m-d H:i:s")." ".$str."\n", FILE_APPEND);
}

// ===================================================

include("../../params.php");
include_once("../helpers/fecha_helper.php");
include_once("../libraries/Mandrill/Mandrill.php");
$id_empresa = 228;
$salida = "";
$dia = date("d");
$hoy = date("Y-m-d");

function calcular_interes($monto = 0,$dias_mora = 0) {
  if ($monto <= 0 || $dias_mora <= 0) return 0;
  $coef = 1.01050;
  $acumulado = $monto;
  for($i=1;$i<=$dias_mora;$i++) {
    if ($i <= 90) {
      $acumulado = $acumulado * $coef;
    } else {
      $coef2 = (($coef-1) * (1/((pow($i,1.05)*0.01)+1))) + 1;
      $acumulado = $acumulado * $coef2;
    }
  }
  return ($acumulado - $monto);
}

$sql = "SELECT PC.*, DATEDIFF('$hoy',PC.fecha_vencimiento) AS dias_mora ";
$sql.= "FROM pres_prestamos_cuotas PC ";
$sql.= "WHERE PC.id_empresa = $id_empresa ";
$sql.= "AND (PC.estado = 0 OR PC.estado = 2) ";
$sql.= "AND fecha_vencimiento < '$hoy' ";
$sql.= "AND ultimo_calculo_interes < '$hoy' ";

$q = mysqli_query($conx,$sql);
// Si no hay nada para seleccionar, salimos
if (mysqli_num_rows($q)<=0) exit();
while(($cuota = mysqli_fetch_object($q))!==NULL) {
  $resto = $cuota->monto - $cuota->monto_pagado;
  //$resto = 1000;
  //$cuota->dias_mora = 600;
  if ($resto > 0) {
    $interes_anterior = (float)calcular_interes($resto,$cuota->dias_mora-1);
    $interes = (float)calcular_interes($resto,$cuota->dias_mora);
    $variacion = $interes - $interes_anterior;
    $sql = "UPDATE pres_prestamos_cuotas SET ";
    $sql.= " interes = interes + $variacion, saldo = saldo + $variacion, ultimo_calculo_interes = '$hoy', ";
    $sql.= " saldo_interes = interes - interes_pagado ";
    $sql.= "WHERE id = $cuota->id AND id_empresa = $cuota->id_empresa AND id_sucursal = $cuota->id_sucursal ";
    mysqli_query($conx,$sql);
    $lin = "Pres: $cuota->id_prestamo  Cuota: $cuota->numero  Interes: $interes";
    logfile($lin);
    $salida.= $lin."<br/>";
  }
}

// Mandamos el email
/*
mandrill_send(array(
  "to"=>"basile.matias99@gmail.com",
  "subject"=>"CALCULO MORA",
  "body"=>$salida,
));
*/
?>