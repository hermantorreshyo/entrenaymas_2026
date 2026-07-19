<?php
/**
ESTE SCRIPT SE ENCARGA DE SUBIR AL SERVIDOR LAS FACTURAS, CLIENTES, CAJA DIARIA, ETC
QUE VAYA REALIZANDO EL PUNTO DE VENTA

- Se configura el ID_EMPRESA de este SCRIPT

- DEBE ESTAR => com_configuracion.local = 1

- Se configura "upload.bat" en el System Scheduler, cada 5 minutos
    Link de descarga: http://www.splinterware.com/download/index.htm

- Se generan distintos archivos por cada tabla

- Luego se llama al metodo que procese la informacion enviada
  y se espera la respuesta para poder actualizar los registros y ponerlos como
  que todos fueron actualizados correctamente
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
$ip_server = "www.varcreative.com";
$dest_dir = "C:/xampp/htdocs/sistema/"; // Carpeta donde se trabajan los archivos
$http = "https"; // HTTP o HTTPS
$abs_path = "C:/xampp/htdocs/sistema/";
$exportar_archivo = 0;

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

// Archivos utilizados
$file_facturas = $dest_dir."facturas.sql";
$file_facturas_items = $dest_dir."facturas_items.sql";
$file_facturas_iva = $dest_dir."facturas_iva.sql";
$file_cupones_tarjetas = $dest_dir."cupones_tarjetas.sql";
$file_clientes = $dest_dir."clientes.sql";
$file_caja_diaria = $dest_dir."caja_diaria.sql";
$file_facturas_pagos = $dest_dir."facturas_pagos.sql";
$file_cheques = $dest_dir."cheques.sql";
$file_gastos = $dest_dir."gastos.sql";
$file_caja_diaria_facturas = $dest_dir."caja_diaria_facturas.txt";
$files = array(
  $file_facturas,
  $file_facturas_items,
  $file_facturas_iva,
  $file_cupones_tarjetas,
  $file_clientes,
  $file_caja_diaria,
  $file_facturas_pagos,
  $file_cheques,
  $file_gastos,
  $file_caja_diaria_facturas,
);

// ================================

// Primero eliminamos archivos anteriores para que no haya un doble submit
if (file_exists($file_facturas))              unlink($file_facturas);
if (file_exists($file_facturas_items))        unlink($file_facturas_items);
if (file_exists($file_facturas_iva))          unlink($file_facturas_iva);
if (file_exists($file_clientes))              unlink($file_clientes);
if (file_exists($file_cupones_tarjetas))      unlink($file_cupones_tarjetas);
if (file_exists($file_caja_diaria))           unlink($file_caja_diaria);
if (file_exists($file_caja_diaria_facturas))  unlink($file_caja_diaria_facturas);
if (file_exists($file_facturas_pagos))        unlink($file_facturas_pagos);
if (file_exists($file_cheques))               unlink($file_cheques);
if (file_exists($file_gastos))                unlink($file_gastos);

// Obtenemos el maximo numero de facturas que todavia no subio
// Esto se hace para poner un limite, por si cuando esta ejecutando este script
// se agreguen nuevos datos y quede inconsistente despues
$sql = "SELECT IF(MAX(id) IS NULL,0,MAX(id)) AS id ";
$sql.= "FROM facturas WHERE id_empresa = $id_empresa ";
$sql.= "AND uploaded = 0 "; // Que no hayan sido subidos
$q = mysqli_query($conx,$sql);
$max_id_factura = mysqli_fetch_object($q);

// Si hay nuevas facturas para subir
if ($max_id_factura->id > 0) {

  // Esperamos un segundo, por si justo agarro una factura que todavia 
  // se estaban guardando los items, iva, etc.
  sleep(1);

  // Guardamos en un archivo las facturas NO SUBIDAS hasta ese ID MAXIMO
  $sql = "SELECT * ";
  $sql.= "INTO OUTFILE '$file_facturas' ";
  $sql.= "FROM facturas ";
  $sql.= "WHERE id_empresa = $id_empresa ";
  $sql.= "AND id <= $max_id_factura->id ";
  $sql.= "AND uploaded = 0 ";
  mysqli_query($conx,$sql);

  // Guardamos los items
  $sql = "SELECT FI.* ";
  $sql.= "INTO OUTFILE '$file_facturas_items' ";
  $sql.= "FROM facturas_items FI ";
  $sql.= "WHERE FI.id_empresa = $id_empresa ";
  $sql.= "AND FI.id_factura <= $max_id_factura->id ";
  $sql.= "AND FI.uploaded = 0 ";
  mysqli_query($conx,$sql);

  // Guardamos los iva
  $sql = "SELECT FI.* ";
  $sql.= "INTO OUTFILE '$file_facturas_iva' ";
  $sql.= "FROM facturas_iva FI ";
  $sql.= "WHERE FI.id_empresa = $id_empresa ";
  $sql.= "AND FI.id_factura <= $max_id_factura->id ";
  $sql.= "AND FI.uploaded = 0 ";
  mysqli_query($conx,$sql);

  // Guardamos los cupones de tarjetas
  $sql = "SELECT FI.* ";
  $sql.= "INTO OUTFILE '$file_cupones_tarjetas' ";
  $sql.= "FROM cupones_tarjetas FI ";
  $sql.= "WHERE FI.id_empresa = $id_empresa ";
  $sql.= "AND FI.id_factura <= $max_id_factura->id ";
  $sql.= "AND FI.uploaded = 0 ";
  mysqli_query($conx,$sql);

}

// Obtenemos el maximo numero de clientes que todavia no subio
// Esto se hace para poner un limite, por si cuando esta ejecutando este script
// se agreguen nuevos datos y quede inconsistente despues
$sql = "SELECT IF(MAX(id) IS NULL,0,MAX(id)) AS id ";
$sql.= "FROM clientes WHERE id_empresa = $id_empresa ";
$sql.= "AND uploaded = 0 "; // Que no hayan sido uploadeds
$q = mysqli_query($conx,$sql);
$max_id_cliente = mysqli_fetch_object($q);

// Si hay nuevos clientes para subir
if ($max_id_cliente->id > 0) {

  // Guardamos en un archivo los clientes NO SUBIDOS hasta ese ID MAXIMO
  $sql = "SELECT * ";
  $sql.= "INTO OUTFILE '$file_clientes' ";
  $sql.= "FROM clientes ";
  $sql.= "WHERE id_empresa = $id_empresa ";
  $sql.= "AND id <= $max_id_cliente->id ";
  $sql.= "AND uploaded = 0 ";
  mysqli_query($conx,$sql);

}

// Obtenemos el maximo numero de cheques que todavia no subio
// Esto se hace para poner un limite, por si cuando esta ejecutando este script
// se agreguen nuevos datos y quede inconsistente despues
$sql = "SELECT IF(MAX(id) IS NULL,0,MAX(id)) AS id ";
$sql.= "FROM cheques WHERE id_empresa = $id_empresa ";
$sql.= "AND uploaded = 0 "; // Que no hayan sido uploadeds
$q = mysqli_query($conx,$sql);
$max_id_cheque = mysqli_fetch_object($q);

// Si hay nuevos cheques para subir
if ($max_id_cheque->id > 0) {

  // Guardamos en un archivo los cheques NO SUBIDOS hasta ese ID MAXIMO
  $sql = "SELECT * ";
  $sql.= "INTO OUTFILE '$file_cheques' ";
  $sql.= "FROM cheques ";
  $sql.= "WHERE id_empresa = $id_empresa ";
  $sql.= "AND id <= $max_id_cheque->id ";
  $sql.= "AND uploaded = 0 ";
  mysqli_query($conx,$sql);

}

// Obtenemos el maximo numero de gastos que todavia no subio
// Esto se hace para poner un limite, por si cuando esta ejecutando este script
// se agreguen nuevos datos y quede inconsistente despues
$sql = "SELECT IF(MAX(id) IS NULL,0,MAX(id)) AS id ";
$sql.= "FROM gastos WHERE id_empresa = $id_empresa ";
$sql.= "AND uploaded = 0 "; // Que no hayan sido uploadeds
$q = mysqli_query($conx,$sql);
if ($q !== FALSE) {
  $max_id_gasto = mysqli_fetch_object($q);

  // Si hay nuevos gastos para subir
  if ($max_id_gasto->id > 0) {

    // Guardamos en un archivo los gastos NO SUBIDOS hasta ese ID MAXIMO
    $sql = "SELECT * ";
    $sql.= "INTO OUTFILE '$file_gastos' ";
    $sql.= "FROM gastos ";
    $sql.= "WHERE id_empresa = $id_empresa ";
    $sql.= "AND id <= $max_id_gasto->id ";
    $sql.= "AND uploaded = 0 ";
    mysqli_query($conx,$sql);

  }
} else {
  $max_id_gasto = new stdClass();
  $max_id_gasto->id = 0;
}

// Guardamos las cajas diarias
$sql = "SELECT IF(MAX(id) IS NULL,0,MAX(id)) AS id ";
$sql.= "FROM caja_diaria WHERE id_empresa = $id_empresa ";
$sql.= "AND uploaded = 0 AND estado = 'C' "; // Cerradas que no hayan sido uploadeds
$q = mysqli_query($conx,$sql);
$max_id_caja_diaria = mysqli_fetch_object($q);
if ($max_id_caja_diaria->id > 0) {

  // Esperamos un segundo, para que si justo se esta guardando una caja,
  // termine de actualizar todas las facturas que pertenecen a esa caja
  sleep(1);

  // Guardamos en el archivo
  $sql = "SELECT * ";
  $sql.= "INTO OUTFILE '$file_caja_diaria' ";
  $sql.= "FROM caja_diaria ";
  $sql.= "WHERE id_empresa = $id_empresa ";
  $sql.= "AND id <= $max_id_caja_diaria->id ";
  $sql.= "AND estado = 'C' "; // Cerrada
  $sql.= "AND uploaded = 0 "; // No subida
  mysqli_query($conx,$sql);

  $sql = "SELECT * ";
  $sql.= "FROM caja_diaria ";
  $sql.= "WHERE id_empresa = $id_empresa ";
  $sql.= "AND id <= $max_id_caja_diaria->id ";
  $sql.= "AND estado = 'C' "; // Cerrada
  $sql.= "AND uploaded = 0 "; // No subida
  $q_cajas = mysqli_query($conx,$sql);
  $cajas = array();
  while(($caja=mysqli_fetch_object($q_cajas))!==NULL) {
    $sql = "SELECT id FROM facturas WHERE id_caja_diaria = $caja->id ";
    $q_fact = mysqli_query($conx,$sql);
    if (mysqli_num_rows($q_fact)>0) {
      $ids = array();
      while(($fact=mysqli_fetch_object($q_fact))!==NULL) $ids[] = $fact->id;
      $ids_s = implode(",", $ids);
      // El primer campo es el id de la caja
      // El segundo campo es un listado de IDs de Facturas separados por comas
      $cajas[] = $caja->id.";;;".$ids_s;
    }
  }
  if (!empty($cajas)) {
    // Cada linea es una caja por separado
    $cajas_s = implode("\n", $cajas);
    file_put_contents($file_caja_diaria_facturas, $cajas_s);
  }

}

if ($max_id_factura->id <= 0 && $max_id_caja_diaria->id <= 0 && $max_id_cliente->id <= 0 && $max_id_cheque->id <= 0 && $max_id_gasto->id <= 0) {
  echo "Nada para subir.";
  exit();
}

$salida = array();
foreach($files as $file) {
  if (!file_exists($file)) continue; // Si el archivo no existe, pasamos al proximo
  $info = file_get_contents($file);
  if (empty($info)) continue;
  $table = str_replace($dest_dir, "", $file);
  $table = str_replace(".sql", "", $table);
  $table = str_replace(".txt", "", $table);
  $salida[] = array(
    "table"=>$table,
    "data"=>mb_convert_encoding($info, 'UTF-8', 'ISO-8859-1'),
  );
}

$data = array(
  "id_empresa"=>$id_empresa,
  "punto_venta"=>$punto_venta,
  "datos"=>$salida,
);

$datos_json = json_encode($data);
if ($datos_json === FALSE) {
  $err_number = json_last_error();
  switch ($err_number) {
    case JSON_ERROR_NONE:
      echo "No error has occurred";
      break;
    case JSON_ERROR_DEPTH:
      echo "The maximum stack depth has been exceeded";
      break;
    case JSON_ERROR_STATE_MISMATCH:
      echo "Invalid or malformed JSON";
      break;
    case JSON_ERROR_CTRL_CHAR:
      echo "Control character error, possibly incorrectly encoded";
      break;
    case JSON_ERROR_SYNTAX:
      echo "Syntax error";
      break;
    case JSON_ERROR_UTF8:
      echo "ERROR UTF8";
      break;
  }
  exit();
}

$tmpFile = "upload.txt";
file_put_contents($tmpFile, $datos_json);

if ($exportar_archivo == 0) {

  // Ahora llamamos al metodo para que procese los archivos que subimos
  $url = "$http://$ip_server/sistema/uploader/function/procesar_put/";
  $c = curl_init($url);
  curl_setopt($c, CURLOPT_PUT, 1 );
  curl_setopt($c, CURLOPT_INFILESIZE, filesize($tmpFile) );
  curl_setopt($c, CURLOPT_INFILE, ($in=fopen($tmpFile, 'r')) );
  curl_setopt($c, CURLOPT_CUSTOMREQUEST, 'POST' );
  curl_setopt($c, CURLOPT_HTTPHEADER, [ 'Content-Type: application/json' ] );
  curl_setopt($c, CURLOPT_URL, $url );
  curl_setopt($c, CURLOPT_RETURNTRANSFER, 1 );
  curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($c, CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
  $html = curl_exec($c);  

} else {
  // Solo guardamos el archivo, por lo tanto tomamos que el proceso es OK
  $html = "1";
}

// Si la ejecucion devolvio 1, actualizamos los elementos para no volverlos a subir
if (strlen($html) == 1 && $html === "1") {

  if ($max_id_factura->id > 0) {

    // Guardamos en un archivo las facturas NO SUBIDAS hasta ese ID MAXIMO
    $sql = "UPDATE facturas SET uploaded = 1 ";
    $sql.= "WHERE id_empresa = $id_empresa ";
    $sql.= "AND id <= $max_id_factura->id ";
    $sql.= "AND uploaded = 0 ";
    mysqli_query($conx,$sql);

    // Guardamos los items
    $sql = "UPDATE facturas_items FI SET uploaded = 1 ";
    $sql.= "WHERE FI.id_empresa = $id_empresa ";
    $sql.= "AND FI.id_factura <= $max_id_factura->id ";
    $sql.= "AND FI.uploaded = 0 ";
    mysqli_query($conx,$sql);

    // Guardamos los iva
    $sql = "UPDATE facturas_iva FI SET uploaded = 1 ";
    $sql.= "WHERE FI.id_empresa = $id_empresa ";
    $sql.= "AND FI.id_factura <= $max_id_factura->id ";
    $sql.= "AND FI.uploaded = 0 ";
    mysqli_query($conx,$sql);

    // Guardamos los cupones de tarjetas
    $sql = "UPDATE cupones_tarjetas FI SET uploaded = 1 ";
    $sql.= "WHERE FI.id_empresa = $id_empresa ";
    $sql.= "AND FI.id_factura <= $max_id_factura->id ";
    $sql.= "AND FI.uploaded = 0 ";
    mysqli_query($conx,$sql);

  }

  if ($max_id_cliente->id > 0) {
    // Actualizamos los clientes
    $sql = "UPDATE clientes SET uploaded = 1 ";
    $sql.= "WHERE id_empresa = $id_empresa ";
    $sql.= "AND id <= $max_id_cliente->id ";
    $sql.= "AND uploaded = 0 ";
    mysqli_query($conx,$sql);
  }

  if ($max_id_cheque->id > 0) {
    // Actualizamos los cheques
    $sql = "UPDATE cheques SET uploaded = 1 ";
    $sql.= "WHERE id_empresa = $id_empresa ";
    $sql.= "AND id <= $max_id_cheque->id ";
    $sql.= "AND uploaded = 0 ";
    mysqli_query($conx,$sql);
  }

  if ($max_id_gasto->id > 0) {
    // Actualizamos los gastos
    $sql = "UPDATE gastos SET uploaded = 1 ";
    $sql.= "WHERE id_empresa = $id_empresa ";
    $sql.= "AND id <= $max_id_gasto->id ";
    $sql.= "AND uploaded = 0 ";
    mysqli_query($conx,$sql);
  }

  if ($max_id_caja_diaria->id > 0) {
    // Actualizamos las cajas diarias
    $sql = "UPDATE caja_diaria SET uploaded = 1 ";
    $sql.= "WHERE id_empresa = $id_empresa ";
    $sql.= "AND id <= $max_id_caja_diaria->id ";
    $sql.= "AND uploaded = 0 ";
    mysqli_query($conx,$sql);
  }

}

if ($exportar_archivo == 1) {

  // Renombramos el archivo por si se ejecuta el metodo dos veces, para evitar que se pierdan los datos
  $newname = "upload_".time().".txt";
  if (file_exists("importacion") === FALSE) mkdir("importacion");
  $ren = rename($tmpFile,"importacion/".$newname);
  echo "\r\n";
  echo "Los datos se exportaron correctamente al archivo: ";
  if ($ren) echo $abs_path."importacion/".$newname;
  else echo $abs_path.$tmpFile;
  echo "\r\n";

} else {
  echo $html;  
}
?>