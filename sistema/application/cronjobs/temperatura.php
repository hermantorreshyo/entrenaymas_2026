<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$id_empresa = 70;
include("../../params.php");
$url = "http://api.openweathermap.org/data/2.5/weather?q=Chacabuco,ar&APPID=6a3453f5110d3abfa843fb1a1489b7e4";
$ch = curl_init();
curl_setopt($ch,CURLOPT_URL, $url);
curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
$result = curl_exec($ch);
if (empty($result)) return;
$obj = json_decode($result);
$actual = round($obj->main->temp - 273.15,1);
$estado = $obj->weather[0]->id;
$hoy = date("Y-m-d");
$sql = "SELECT * FROM clima WHERE id_empresa = $id_empresa AND fecha = '$hoy' ";
$q = mysqli_query($conx,$sql);
if (mysqli_num_rows($q)>0) {
  $sql = "UPDATE clima SET temp_actual = '$actual', estado = '$estado' WHERE id_empresa = $id_empresa AND fecha = '$hoy' ";
} else {
  $sql = "INSERT INTO clima (id_empresa,fecha,temp_actual,estado) VALUES ('$id_empresa','$hoy','$actual','$estado') ";
}
echo $sql;
mysqli_query($conx,$sql);
?>