<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

set_time_limit(0);
include("../../params.php");

// PERIODICIDAD: 1 VEZ AL DIA

// OBJETIVO:
// Cuenta la cantidad de impresiones que tuvieron cada publicidad en el dia de ayer
// y vuelca el resultado en una tabla de resumen, para mejorar la performance
// en las estadisticas de publicidades
// Tambien borra todo lo que no sea de ayer para atras. De esta manera, la tabla
// not_publicidades_impresiones no crece exponencialmente

$ayer = date('Y-m-d',strtotime("-1 days"));
$sql = "SELECT COUNT(*) AS cantidad, id_publicidad, id_empresa ";
$sql.= "FROM not_publicidades_impresiones ";
$sql.= "WHERE DATE_FORMAT(stamp,'%Y-%m-%d') = '$ayer' ";
$sql.= "GROUP BY id_publicidad ";
$q = mysqli_query($conx,$sql);

// Si no hay nada para seleccionar, salimos
if (mysqli_num_rows($q)<=0) exit();

// Borramos los datos del dia de la fecha, por si se ejecuta por error dos veces el script el mismo dia
$sql = "DELETE FROM pub_resumen_impresiones WHERE fecha = '$ayer' ";
mysqli_query($conx,$sql);

while(($row = mysqli_fetch_object($q))!==NULL) {

  // Insertamos en la tabla de resumen
  $sql = "INSERT INTO pub_resumen_impresiones (";
  $sql.= " id_empresa, fecha, id_publicidad, cantidad ";
  $sql.= ") VALUES (";
  $sql.= " $row->id_empresa, '$ayer', '$row->id_publicidad', '$row->cantidad' ";
  $sql.= ") ";
  mysqli_query($conx,$sql);
}

$sql = "DELETE FROM not_publicidades_impresiones ";
$sql.= "WHERE DATE_FORMAT(stamp,'%Y-%m-%d') < '$ayer' ";
mysqli_query($conx,$sql);

echo "TERMINO";
?>