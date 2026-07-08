<?php
// ================================

// SCRIPT DE INSTALACION DE PUNTO DE VENTA

// PARAMETROS
//
$id_empresa = 0;
$punto_venta = 0;
$sucursal = 0;

$dest_dir = "C:/xampp/htdocs/sistema/"; // Carpeta donde se trabajan los archivos
$http = "https"; // HTTP o HTTPS
$ip_server = "www.varcreative.com";
$abs_path = "C:/xampp/htdocs/sistema/";

// Tomamos el archivo de configuracion del punto de venta 
$ini = file_get_contents("punto_venta.txt");
$configs = explode("\n", $ini);
foreach($configs as $c) {
  $c1 = explode("=", $c);
  $p = trim($c1[0]);
  $v = trim($c1[1]);
  if ($p == "id_empresa") $id_empresa = $v;
  else if ($p == "punto_venta") $punto_venta = $v;
  else if ($p == "sucursal") $sucursal = $v;
  else if ($p == "ip_server") $ip_server = $v;
  else if ($p == "dest_dir") $dest_dir = $v;
  else if ($p == "http") $http = $v;
  else if ($p == "abs_path") $abs_path = $v;
  else if ($p == "exportar_archivo") $exportar_archivo = $v;
}

if ($id_empresa == 0 || $punto_venta == 0 || $sucursal == 0) {
  echo "Error en el archivo punto_venta.txt"; exit();
}

// ================================

include($abs_path."application/helpers/connection_helper.php");
if (!is_connected($ip_server)) exit(); // Si no tenemos conexion al servidor, volvemos mas tarde

include($abs_path."params.php");

$sql = "SELECT * FROM com_configuracion WHERE id = 1";
$q_conf = mysqli_query($conx,$sql);
if (mysqli_num_rows($q_conf) == 0) {
  echo "ERROR en com_configuracion";
  exit();
}
$conf = mysqli_fetch_object($q_conf);
if ($conf->local != 1) {
  echo "ERROR. com_configuracion.local no es 1";
  exit();
}

$url = $http."://".$ip_server."/sistema/app/get_max_ids/?id_empresa=$id_empresa&punto_venta=$punto_venta";
$c = curl_init($url);
curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($c,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
$html = curl_exec($c);
if (empty($html)) {
  echo "No se obtuvo ningun dato."; exit();
}
$salida = json_decode($html);
if ($salida->error == 1) {
  echo $salida->mensaje; exit();
}

foreach($salida->datos as $r) {
  $proximo = $r->ultimo + 1;
  $sql = "ALTER TABLE $r->tabla AUTO_INCREMENT = $proximo";
  mysqli_query($conx,$sql);
}
echo "TERMINO ACTUALIZACION DE IDS";
?>