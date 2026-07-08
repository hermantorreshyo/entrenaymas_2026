<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include("../../params.php");
$time = time();
$sql = "UPDATE com_configuracion SET version_js = $time WHERE id = 1 ";
$q = mysqli_query($conx,$sql);
echo "TERMINO";
?>