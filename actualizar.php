<?php
/**
ESTE SCRIPT HACE LO MISMO QUE EL BOTON ACTUALIZAR
SE USA PARA AUTOMATIZAR ESO
*/

set_time_limit(0);
ini_set('memory_limit','1024M');

// A traves de un archivo, controlamos que no se ejecuten dos veces el mismo proceso
$filename = "upload_sem.txt";
if (file_exists($filename) === FALSE) file_put_contents($filename, "");
$file = fopen($filename, "r+");
// Intenta adquirir un bloqueo exclusivo
while((flock($file, LOCK_EX | LOCK_NB) === FALSE)) sleep(10);

// ================================
// PARAMETROS
//
$id_empresa = 0;
$punto_venta = 0;
$sucursal = 0;
$ip_server = "localhost";
$dest_dir = "C:/xampp/htdocs/sistema/"; // Carpeta donde se trabajan los archivos
$http = "http"; // HTTP o HTTPS
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
}

// Valores minimos que debe tener el archivo
if ($id_empresa == 0 || $punto_venta == 0) {
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

// Obtenemos el punto de venta
$sql = "SELECT * FROM puntos_venta WHERE id_empresa = '$id_empresa' AND numero = '$punto_venta' ";
$q_pv = mysqli_query($conx,$sql);
if (mysqli_num_rows($q_pv) == 0) {
  echo "ERROR no se encuentra el punto de venta";
  exit();
}
$pv = mysqli_fetch_object($q_pv);

$data = array(
  "id_sucursal"=>$sucursal,
  "id_empresa"=>$id_empresa,
  "id_punto_venta"=>$pv->id,
  "completa"=>1,
  "id_usuario"=>0,
);

$datos_json = json_encode($data);

// Ahora llamamos al metodo para que procese los archivos que subimos
$url = "$http://$ip_server/sistema/uploader/function/get_data_from_server/";
$c = curl_init($url);
curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($c, CURLOPT_POST, true);
curl_setopt($c, CURLOPT_POSTFIELDS, $data);
curl_setopt($c, CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
$html = curl_exec($c);  
?>