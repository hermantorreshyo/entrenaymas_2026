<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$id_empresa = 70;
include("../../params.php");
$url = "http://api.openweathermap.org/data/2.5/forecast?id=3861953&APPID=6a3453f5110d3abfa843fb1a1489b7e4";
$ch = curl_init();
curl_setopt($ch,CURLOPT_URL, $url);
curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
$result = curl_exec($ch);
if (empty($result)) return;
$obj = json_decode($result);
$maniana = date("Y-m-d",strtotime("+1 day"));
$maxima = 0;
$minima = 99999;
foreach($obj->list as $l) {
  $f = explode(" ",$l->dt_txt);
  if ($f[0] != $maniana) continue;
  if ($l->main->temp_min < $minima) $minima = $l->main->temp_min;
  if ($l->main->temp_max > $maxima) $maxima = $l->main->temp_max;
}

if ($maxima == 0) return;
$maxima = round($maxima - 273.15,0);
$minima = round($minima - 273.15,0);
echo "Maxima: $maxima | Minima: $minima";

$sql = "SELECT * FROM clima WHERE id_empresa = $id_empresa AND fecha = '$maniana' ";
$q = mysqli_query($conx,$sql);
if (mysqli_num_rows($q)>0) {
  $sql = "UPDATE clima SET temp_maxima = '$maxima', temp_minima = '$minima' WHERE id_empresa = 70 AND fecha = '$maniana' ";
} else {
  $sql = "INSERT INTO clima (id_empresa,fecha,temp_maxima,temp_minima) VALUES ($id_empresa,'$maniana','$maxima','$minima') ";
}
echo $sql;
mysqli_query($conx,$sql);
?>