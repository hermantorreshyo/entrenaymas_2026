<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$url = "https://www.millingandgrain.com/";
$id_empresa = 256;
$id = isset($GET_["id"]) ? ((int)$GET_["id"]) : 0;
$marca = "//CACHE VERSION: ".date("Y-m-d H:i:s");
include("../../params.php");

// Borramos toda la carpeta de cache
/*
$files = glob('../../../cache/256/*');
foreach($files as $file){
  if(is_file($file)) unlink($file);
}
*/

// Obtenemos todas las noticias del dia
$fecha = date("Y-m-d",strtotime("yesterday"));
/*
$sql = "SELECT * FROM not_entradas WHERE DATE_FORMAT(fecha,'%Y-%m-%d') >= '$fecha' AND id_empresa = $id_empresa AND eliminada = 0 ";
if ($id != 0) $sql.= "AND id = $id ";
$q = mysqli_query($conx,$sql);
while(($entrada = mysqli_fetch_object($q))!==NULL) {
  $ch = curl_init();
  $url2 = $url.$entrada->link."?cache=0";
  echo $url2."<br/>";
  curl_setopt($ch,CURLOPT_URL, $url2);
  curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
  $result = curl_exec($ch);
  $result = str_replace('<script>//CACHE</script>', '<script>'.$marca.'</script>', $result);
  $link = $entrada->link;
  $link = str_replace("entrada/", "", $link);
  $link = str_replace("/", "", $link);
  file_put_contents("../../../cache/256/".$link.".html", $result.$marca);
}
*/

// Y tambien hacemos el home
$ch = curl_init();
curl_setopt($ch,CURLOPT_URL, $url."?cache=0");
curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
$result = curl_exec($ch);
$result = str_replace('<script>//CACHE</script>', '<script>'.$marca.'</script>', $result);
file_put_contents("../../../cache/256/index.html", $result);
?>