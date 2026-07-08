<?php 
set_time_limit(0);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include("../../params.php");

$sql = "SELECT MAX(fecha) AS fecha ";
$sql.= "FROM not_entradas ";
$sql.= "WHERE id_empresa = $id_empresa ";
$sql.= "AND activo = 1";
$q = mysqli_query($conx,$sql);
$row = mysqli_fetch_object($q);
$ultima = new DateTime($row->fecha);
$ahora = new DateTime();
$diff = $ahora->diff($ultima);
//if ($diff->format("%h"))

?>