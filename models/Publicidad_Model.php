<?php
class Publicidad_Model {

  private $id_empresa = 0;
  private $conx = null;
  private $cache = array();
  public $sql = "";

  function __construct($id_empresa,$conx) {
    $this->id_empresa = $id_empresa;
    $this->conx = $conx;
  }

  private function encod($r) {
    return ((mb_check_encoding($r) == "UTF-8") ? $r : mb_convert_encoding($r, 'UTF-8', 'ISO-8859-1'));
  }

  function get_random_banner($config = array()) {
    $not_id = isset($config["not_id"]) ? $config["not_id"] : "";
    $sql = "SELECT * FROM web_banners ";
    $sql.= "WHERE id_empresa = $this->id_empresa ";
    if (!empty($not_id)) $sql.= "AND id NOT IN ($not_id) ";
    $q = mysqli_query($this->conx,$sql);
    if (mysqli_num_rows($q)<=0) return FALSE;
    $r = mysqli_fetch_object($q);
    if (!empty($r->path) && $this->id_empresa != 70) {
      $r->path = ((strpos($r->path,"http://")===FALSE)) ? "/sistema/".$r->path : $r->path;
    }
    return $r;
  }

  function get_fullscreen($config=array()) {

    $not_id_cliente = isset($config["not_id_cliente"]) ? $config["not_id_cliente"] : "";
    $config["id_categoria"] = (isset($config["id_categoria"]) ? $config["id_categoria"] : 7);
    $config["offset"] = 999;
    $listado = $this->get_list($config);
    if ($listado === FALSE || sizeof($listado)<=0) return FALSE;

    // Controlamos si la primera opcion del array es prioritaria, entonces siempre lo mostramos (no importa si se repite)
    $primera = $listado[0];
    if ($primera->prioridad == 2) {
      return $primera;
    } else {
      // Obtenemos un array con todos los fullscreens posibles a mostrar
      $opciones = array();
      foreach($listado as $row) {
        $opciones[$row->id_cliente][] = $row;
      }
      // Eliminamos las publicidades que corresponden al cliente
      if (!empty($not_id_cliente) && sizeof($opciones)>1) unset($opciones[$not_id_cliente]);

      // Elegimos aleatoriamente otro cliente
      $rand = array_rand($opciones);
      return $opciones[$rand][0];
    }
  }

  function get_list($config = array()) {

    date_default_timezone_set('America/Argentina/Buenos_Aires');
    $id = isset($config["id"]) ? $config["id"] : "";
    $not_ids = isset($config["not_ids"]) ? $config["not_ids"] : "";
    $id_categoria = isset($config["id_categoria"]) ? $config["id_categoria"] : 0;
    $id_categoria_entrada = isset($config["id_categoria_entrada"]) ? $config["id_categoria_entrada"] : 0;
    $categoria = isset($config["categoria"]) ? $config["categoria"] : "";
    $prioridad = isset($config["prioridad"]) ? $config["prioridad"] : -1;
    $limit = isset($config["limit"]) ? $config["limit"] : 0;
    $horarios = isset($config["horarios"]) ? $config["horarios"] : 1;
    $offset = isset($config["offset"]) ? $config["offset"] : 10;
    $id_cliente = isset($config["id_cliente"]) ? $config["id_cliente"] : 0;

    $hoy = date("Y-m-d");
    $ahora = date("H:i:s");

    // Obtenemos todas las publicidades segun los parametros
    $sql = "SELECT P.id, P.cerrar, P.cerrar_despues, P.nombre, P.link, P.path, P.path_2, P.path_3, P.path_video, C.id_cliente, P.link_target, P.prioridad, P.video, P.codigo ";
    $sql.= "FROM pub_piezas P ";
    $sql.= " INNER JOIN pub_campanias C ON (P.id_campania = C.id AND P.id_empresa = C.id_empresa) ";
    $sql.= " INNER JOIN not_publicidades_categorias PC ON (P.id_categoria = PC.id AND PC.id_empresa = P.id_empresa) ";
    $sql.= "WHERE P.activo = 1 AND C.estado = 'A' "; // Si esta activa
    $sql.= "AND P.id_empresa = ".$this->id_empresa." ";
    if (!empty($id)) $sql.= "AND P.id = $id ";
    else {
      $sql.= "AND IF(P.fecha_desde != '0000-00-00',(P.fecha_desde <= '$hoy'),(C.valida_desde <= '$hoy')) ";
      $sql.= "AND IF(P.fecha_hasta != '0000-00-00',(P.fecha_hasta >= '$hoy'),(C.valida_hasta >= '$hoy')) ";
      $sql.= "AND C.valida_desde <= '$hoy $ahora' AND '$hoy $ahora' <= C.valida_hasta  ";
      if (!empty($id_cliente)) $sql.= "AND C.id_cliente = $id_cliente ";
      if (!empty($id_categoria)) $sql.= "AND P.id_categoria = $id_categoria ";
      if (!empty($id_categoria_entrada)) $sql.= "AND EXISTS (SELECT 1 FROM pub_piezas_categorias PIC WHERE PIC.id_empresa = P.id_empresa AND PIC.id_pieza = P.id AND PIC.id_relacion = $id_categoria_entrada) ";
      if (!empty($categoria)) $sql.= "AND PC.nombre = '$categoria' ";
      if (!empty($not_ids)) {
        if (is_array($not_ids)) $not_ids = implode(",", $not_ids);
        $sql.= "AND P.id NOT IN ($not_ids) ";
      }
      if ($prioridad != -1) $sql.= "AND P.prioridad = $prioridad ";
      $dia = date("N");
      if ($dia == 1) $sql.= "AND P.lunes = 1 ";
      else if ($dia == 2) $sql.= "AND P.martes = 1 ";
      else if ($dia == 3) $sql.= "AND P.miercoles = 1 ";
      else if ($dia == 4) $sql.= "AND P.jueves = 1 ";
      else if ($dia == 5) $sql.= "AND P.viernes = 1 ";
      else if ($dia == 6) $sql.= "AND P.sabado = 1 ";
      else if ($dia == 7) $sql.= "AND P.domingo = 1 ";
      if ($horarios == 1 && $this->id_empresa != 256 && $this->id_empresa != 257 && $this->id_empresa != 403 && $this->id_empresa != 448 && $this->id_empresa != 493 && $this->id_empresa != 520) {
        $sql.= "AND (";
        $sql.= "IF ((P.hora_desde_1 != '00:00:00' OR P.hora_desde_2 != '00:00:00' OR P.hora_desde_3 != '00:00:00' OR P.hora_desde_4 != '00:00:00' OR P.hora_desde_5 != '00:00:00' OR P.hora_desde_6 != '00:00:00' OR P.hora_desde_7 != '00:00:00' OR P.hora_desde_8 != '00:00:00' OR P.hora_desde_9 != '00:00:00' OR P.hora_desde_10 != '00:00:00' OR P.hora_desde_11 != '00:00:00' OR P.hora_desde_12 != '00:00:00'), ";
        $sql.= " (IF(P.hora_desde_1 != '00:00:00',(P.hora_desde_1 <= '$ahora' AND '$ahora' <= P.hora_hasta_1),0) ";
        $sql.= " OR IF(P.hora_desde_2 != '00:00:00',(P.hora_desde_2 <= '$ahora' AND '$ahora' <= P.hora_hasta_2),0) ";
        $sql.= " OR IF(P.hora_desde_3 != '00:00:00',(P.hora_desde_3 <= '$ahora' AND '$ahora' <= P.hora_hasta_3),0) ";
        $sql.= " OR IF(P.hora_desde_4 != '00:00:00',(P.hora_desde_4 <= '$ahora' AND '$ahora' <= P.hora_hasta_4),0) ";
        $sql.= " OR IF(P.hora_desde_5 != '00:00:00',(P.hora_desde_5 <= '$ahora' AND '$ahora' <= P.hora_hasta_5),0) ";
        $sql.= " OR IF(P.hora_desde_6 != '00:00:00',(P.hora_desde_6 <= '$ahora' AND '$ahora' <= P.hora_hasta_6),0) ";
        $sql.= " OR IF(P.hora_desde_7 != '00:00:00',(P.hora_desde_7 <= '$ahora' AND '$ahora' <= P.hora_hasta_7),0) ";
        $sql.= " OR IF(P.hora_desde_8 != '00:00:00',(P.hora_desde_8 <= '$ahora' AND '$ahora' <= P.hora_hasta_8),0) ";
        $sql.= " OR IF(P.hora_desde_9 != '00:00:00',(P.hora_desde_9 <= '$ahora' AND '$ahora' <= P.hora_hasta_9),0) ";
        $sql.= " OR IF(P.hora_desde_10 != '00:00:00',(P.hora_desde_10 <= '$ahora' AND '$ahora' <= P.hora_hasta_10),0) ";
        $sql.= " OR IF(P.hora_desde_11 != '00:00:00',(P.hora_desde_11 <= '$ahora' AND '$ahora' <= P.hora_hasta_11),0) ";
        $sql.= " OR IF(P.hora_desde_12 != '00:00:00',(P.hora_desde_12 <= '$ahora' AND '$ahora' <= P.hora_hasta_12),0)) ";
        $sql.= ",1)) ";
      }
    }
    $sql.= "ORDER BY P.prioridad DESC, RAND() ASC ";
    $sql.= "LIMIT $limit,$offset ";
    $this->sql = $sql;
    $q = mysqli_query($this->conx,$sql);
    $publicidades = array();
    while(($row = mysqli_fetch_object($q))!==NULL) {
      if (!empty($row->path) && $this->id_empresa != 70) {
        $row->path = ((strpos($row->path,"http://")===FALSE)) ? "/sistema/".$row->path : $row->path;
      }
      $publicidades[] = $row;
    }
    if (sizeof($publicidades)==0) return FALSE;
    else return $publicidades;
  }

  function get($config = array()) {
    $insertar_impresion = isset($config["insertar_impresion"]) ? $config["insertar_impresion"] : 0;
    // Obtenemos el primer elemento de la lista
    $array = $this->get_list($config);
    // Si esta definida la prioridad, y no se obtuvo resultados anteriores, bajamos uno y buscamos de nuevo
    if (isset($config["prioridad"]) && $config["prioridad"] == 1 && ($array===FALSE)) {
      $config["prioridad"] = 0;
      $config["id_categoria_entrada"] = 0;
      $sql = $this->sql;
      $array = $this->get_list($config);
      $this->sql = $sql;
    }
    if ($array!==FALSE) {
      $pub = $array[0];
      if ($insertar_impresion == 1) $this->insertar_impresion($pub->id);
      return $pub;
    } else {
      return FALSE;
    }
  }

  function insertar_impresion($id) {
    $sql = "INSERT INTO not_publicidades_impresiones (id_empresa,id_publicidad,stamp) VALUES($this->id_empresa,$id,NOW())";
    mysqli_query($this->conx,$sql);      
  }

}
?>