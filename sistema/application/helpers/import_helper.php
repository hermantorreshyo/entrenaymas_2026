<?php
function importar_tabla_csv($config = array()) {
  
  $tabla = isset($config["tabla"]) ? $config["tabla"] : "";
  $file = isset($config["archivo"]) ? $config["archivo"] : "";
  $db = isset($config["db"]) ? $config["db"] : "";
  $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : 0;
  $borrar_tabla = isset($config["borrar_tabla"]) ? $config["borrar_tabla"] : 0;
  $sqls = array();

  $f = fopen($file,"r+");
  $i=0;

  // Obtenemos la cantidad de campos que tiene la tabla
  $cant_campos = sizeof($db->list_fields($tabla));

  $lineas = array();
  while(($linea = fgets($f))!==FALSE) {
    if ($i==0) { $i++; continue; } // La primera linea la obviamos
    $linea = trim($linea);
    if (substr_count($linea, ";") != $cant_campos) {
      echo "ERROR: NO COINCIDE LA CANTIDAD DE CAMPOS CON LA TABLA. LINEA: $i "; 
      exit();
    }
    $linea = substr($linea,0,strlen($linea)-1); // Sacamos el ultimo ;
    $linea = str_replace(";",",",$linea);
    $s = 'INSERT INTO '.$tabla.' VALUES ('.$linea.')';
    $sqls[] = $s;
    $i++;
  }
  fclose($f);
  if (!empty($sqls)) {
    if ($borrar_tabla == 1) $db->query("DELETE FROM $tabla WHERE id_empresa = $id_empresa");
    foreach($sqls as $s) {
      $db->query($s);
    }
  }
}


// Como parametro recibe un query CODEIGNITER
function create_string_to_export($q) {
  // La primera linea son los campos de la tabla que se tienen que actualizar
  $salida = "";
  $k = 0;
  foreach($q->result() as $row) {
    $props = get_object_vars($row);
    $cantidad = sizeof($props);
    
    // Como primera linea, ponemos los campos que vamos a actualizar
    $i=0;
    if ($k==0) {
      foreach ($props as $key => $value) {
        $salida.= $key;
        if ($i<$cantidad-1) $salida.= ";;;";
        $i++;
      }
      $salida.="|||";
    }

    $i=0;
    $sql = "";
    foreach ($props as $value) {
      $value = str_replace("\"", "", $value);
      $value = str_replace("\'", "", $value);
      $sql.= $value;
      if ($i<$cantidad-1) $sql.= ";;;";
      $i++;
    }
    $sql.= "|||";
    $salida.= $sql;
    $k++;
  }
  return $salida;
}

function create_insert_sql($config = array()) {

  $table = isset($config["table"]) ? $config["table"] : "";
  if (empty($table)) throw new Exception("Tabla no especificada");
  $fields = isset($config["fields"]) ? $config["fields"] : array();
  if (empty($fields)) throw new Exception("Campos no especificados");
  $data = isset($config["data"]) ? $config["data"] : array();
  if (empty($data)) throw new Exception("Datos no especificados");
  if (sizeof($fields) != sizeof($data)) throw new Exception("El array de datos no coincide con el array de campos");

  $salida = "INSERT INTO `$table` (";
  $i=0;
  foreach($fields as $f) {
    $salida.=$f;
    if ($i<sizeof($fields)-1) $salida.= ",";
    $i++;
  }
  $salida.= ") VALUES (";
  $i=0;
  foreach($data as $f) {
    $salida.="\"$f\"";
    if ($i<sizeof($data)-1) $salida.= ",";
    $i++;
  }  
  $salida.= ")";
  return $salida;
}

function create_update_sql($config = array()) {

  $table = isset($config["table"]) ? $config["table"] : "";
  if (empty($table)) throw new Exception("Tabla no especificada");
  $fields = isset($config["fields"]) ? $config["fields"] : array();
  if (empty($fields)) throw new Exception("Campos no especificados");
  $data = isset($config["data"]) ? $config["data"] : array();
  if (empty($data)) throw new Exception("Datos no especificados");
  $where = isset($config["where"]) ? $config["where"] : "";
  if (sizeof($fields) != sizeof($data)) throw new Exception("El array de datos no coincide con el array de campos");

  $salida = "UPDATE `$table` SET ";
  for($i=0; $i<sizeof($fields);$i++) {
    $campo = $fields[$i];
    $dato = $data[$i];
    $salida.=" $campo = \"$dato\"";
    if ($i<sizeof($fields)-1) $salida.= ",";
  }
  if (!empty($where)) $salida.= " WHERE $where ";
  return $salida;
}

?>