<?php
/**
ESTE SCRIPT SE ENCARGA DE ACTUALIZAR EL PROPIO SISTEMA

- Corrobora que la version de base de datos haya cambiado

- Realiza un git pull desde el servidor

- DEBE ESTAR => com_configuracion.local = 1

- Se configura "upgrade.bat" en el System Scheduler, todas las noches

*/
set_time_limit(0);
ini_set('memory_limit','1024M');

// A traves de un archivo, controlamos que no se ejecuten dos veces el mismo proceso
$filename = "upgrade_sem.txt";
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
$http = "https"; // HTTP o HTTPS
$ip_server = "www.varcreative.com";
$abs_path = "C:/xampp/htdocs/sistema/";
$mysql_path = "C:/xampp/mysql/bin/mysql.exe";
$dest_dir = "C:/xampp/htdocs/"; // Carpeta donde se trabajan los archivos
$skip_local = 0;

function salir($mensaje = "") {
  global $file, $filename;
  if (!empty($mensaje)) echo $mensaje;
  fclose($file);
  @unlink($filename);
  exit();
}

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
  else if ($p == "ip_server_upgrade") $ip_server = $v;
  else if ($p == "dest_dir") $dest_dir = $v;
  else if ($p == "http_upgrade") $http = $v;
  else if ($p == "mysql_path") $mysql_path = $v;
  else if ($p == "abs_path") $abs_path = $v;
  else if ($p == "skip_local") $skip_local = $v;
}

if ($id_empresa == 0) {
  echo "Error en el archivo punto_venta.txt"; exit();
}

// ================================

include($abs_path."application/helpers/connection_helper.php");
if (!is_connected($ip_server)) exit(); // Si no tenemos conexion al servidor, volvemos mas tarde

include($abs_path."params.php");

$sql = "SELECT * FROM com_configuracion WHERE id = 1";
$q_conf = mysqli_query($conx,$sql);
if (mysqli_num_rows($q_conf) == 0) salir("ERROR en com_configuracion");

$conf = mysqli_fetch_object($q_conf);
if ($conf->local != 1 && $skip_local == 0) salir("ERROR. com_configuracion.local no es 1");

$rand = time();
$sql = "UPDATE com_configuracion SET version_js = $rand WHERE id = 1";
mysqli_query($conx,$sql);

$data = array(
  "id_empresa"=>$id_empresa,
  "version_db"=>$conf->version_db,
);

// Ahora llamamos al metodo para que procese los archivos que subimos
$url = "$http://$ip_server/sistema/uploader/function/upgrade_database/";
$c = curl_init($url);
curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($c, CURLOPT_POST, true);
curl_setopt($c, CURLOPT_POSTFIELDS, $data);
curl_setopt($c,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
$html = curl_exec($c);
$salida = gzinflate($html); // Descomprimimos

// Si lo que se devuelve del servidor es "0", es porque no hay cambios
if ($salida != "0") {
  $salida = str_replace("\\n", "\n", $salida);
  file_put_contents($dest_dir."upgrade.sql", $salida);
  exec($mysql_path." -u root servidor < ".$dest_dir."upgrade.sql");
  @unlink($dest_dir."upgrade.sql");
}

exec("git reset --hard");
exec("git pull https://matiasbasile:qu4r2200@github.com/matiasbasile/varcreative.git");

salir();
?>