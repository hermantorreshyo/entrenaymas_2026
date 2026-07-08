<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Pieza_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("pub_piezas","id","id DESC");
	}

  function get_list($config = array()) {

    date_default_timezone_set('America/Argentina/Buenos_Aires');
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $not_ids = isset($config["not_ids"]) ? $config["not_ids"] : "";
    $estado = isset($config["estado"]) ? $config["estado"] : "A";
    $id_campania = isset($config["id_campania"]) ? $config["id_campania"] : 0;
    $id_categoria = isset($config["id_categoria"]) ? $config["id_categoria"] : 0;
    $categoria = isset($config["categoria"]) ? $config["categoria"] : "";
    $limit = isset($config["limit"]) ? $config["limit"] : 0;
    $offset = isset($config["offset"]) ? $config["offset"] : 10;
    $id_cliente = isset($config["id_cliente"]) ? $config["id_cliente"] : 0;
    $buscar_por_fecha = isset($config["buscar_por_fecha"]) ? $config["buscar_por_fecha"] : 0;
    $hoy = date("Y-m-d");
    $ahora = date("H:i:s");

    // Obtenemos todas las publicidades segun los parametros
    $sql = "SELECT P.id, P.cerrar, P.cerrar_despues, P.nombre, P.link, P.link_target, P.path, P.path_2, P.path_video, C.id_cliente, P.video, P.id_categoria, P.repetir, P.prioridad, P.codigo, ";
    $sql.= "  P.lunes, P.martes, P.miercoles, P.jueves, P.viernes, P.sabado, P.domingo, P.activo, P.id_tipo_publicidad, P.id_categoria_entrada, ";
    $sql.= "  P.hora_desde_1, P.hora_desde_2, P.hora_desde_3, P.hora_desde_4, P.hora_desde_5, P.hora_desde_6, P.hora_desde_7, P.hora_desde_8, P.hora_desde_9, P.hora_desde_10, P.hora_desde_11, P.hora_desde_12, P.hora_hasta_1, P.hora_hasta_2, P.hora_hasta_3, P.hora_hasta_4, P.hora_hasta_5, P.hora_hasta_6, P.hora_hasta_7, P.hora_hasta_8, P.hora_hasta_9, P.hora_hasta_10, P.hora_hasta_11, P.hora_hasta_12, ";
    $sql.= "  IF(PC.nombre IS NULL,'',PC.nombre) AS categoria, ";
    $sql.= "  IF(P.fecha_desde = '0000-00-00','',DATE_FORMAT(P.fecha_desde,'%d/%m/%Y')) AS fecha_desde, ";
    $sql.= "  IF(P.fecha_hasta = '0000-00-00','',DATE_FORMAT(P.fecha_hasta,'%d/%m/%Y')) AS fecha_hasta ";
    $sql.= "FROM pub_piezas P ";
    $sql.= " INNER JOIN pub_campanias C ON (P.id_campania = C.id AND C.id_empresa = P.id_empresa) ";
    $sql.= " INNER JOIN not_publicidades_categorias PC ON (P.id_categoria = PC.id AND PC.id_empresa = P.id_empresa) ";
    $sql.= "WHERE P.id_empresa = $id_empresa ";
    if (!empty($estado)) $sql.= "AND C.estado = '$estado' "; // Si esta activa
    if (!empty($id_campania)) $sql.= "AND C.id = $id_campania ";
    if (!empty($id_cliente)) $sql.= "AND C.id_cliente = $id_cliente ";
    if (!empty($id_categoria)) $sql.= "AND P.id_categoria = $id_categoria "; // Si pertenece a la categoria
    if (!empty($categoria)) $sql.= "AND PC.nombre = '$categoria' ";
    if (!empty($not_ids)) $sql.= "AND C.id NOT IN ($not_ids) ";
    if ($buscar_por_fecha == 1) {
      $sql.= "AND IF(P.fecha_desde != '0000-00-00',(P.fecha_desde <= '$hoy'),(C.valida_desde <= '$hoy')) ";
      $sql.= "AND IF(P.fecha_hasta != '0000-00-00',(P.fecha_hasta >= '$hoy'),(C.valida_hasta >= '$hoy')) ";
      $dia = date("N");
      if ($dia == 1) $sql.= "AND P.lunes = 1 ";
      else if ($dia == 2) $sql.= "AND P.martes = 1 ";
      else if ($dia == 3) $sql.= "AND P.miercoles = 1 ";
      else if ($dia == 4) $sql.= "AND P.jueves = 1 ";
      else if ($dia == 5) $sql.= "AND P.viernes = 1 ";
      else if ($dia == 6) $sql.= "AND P.sabado = 1 ";
      else if ($dia == 7) $sql.= "AND P.domingo = 1 ";
      $sql.= "AND (";
      $sql.= "IF ((P.hora_desde_1 != '00:00:00' OR P.hora_desde_2 != '00:00:00' OR P.hora_desde_3 != '00:00:00' OR P.hora_desde_4 != '00:00:00'), ";
      $sql.= " (IF(P.hora_desde_1 != '00:00:00',(P.hora_desde_1 <= '$ahora' AND '$ahora' <= P.hora_hasta_1),0) ";
      $sql.= " OR IF(P.hora_desde_2 != '00:00:00',(P.hora_desde_2 <= '$ahora' AND '$ahora' <= P.hora_hasta_2),0) ";
      $sql.= " OR IF(P.hora_desde_3 != '00:00:00',(P.hora_desde_3 <= '$ahora' AND '$ahora' <= P.hora_hasta_3),0) ";
      $sql.= " OR IF(P.hora_desde_4 != '00:00:00',(P.hora_desde_4 <= '$ahora' AND '$ahora' <= P.hora_hasta_4),0)) ";
      $sql.= ",1)) ";      
    }
    $sql.= "ORDER BY RAND() ASC ";
    $sql.= "LIMIT $limit,$offset ";
    $q = $this->db->query($sql);
    $publicidades = array();
    foreach($q->result() as $row) {
      //if (!empty($row->path) && $id_empresa != 70) {
      //  $row->path = ((strpos($row->path,"http://")===FALSE)) ? "/sistema/".$row->path : $row->path;
      //}

      // Obtenemos las categorias relacionados con ese producto
      $sql = "SELECT R.id, R.nombre ";
      $sql.= "FROM not_categorias R INNER JOIN pub_piezas_categorias AR ON (R.id = AR.id_relacion AND R.id_empresa = AR.id_empresa) ";
      $sql.= "WHERE AR.id_pieza = $row->id AND AR.id_empresa = $id_empresa ";
      $sql.= "ORDER BY AR.orden ASC ";
      $q = $this->db->query($sql);
      $row->categorias_relacionados = array();
      foreach($q->result() as $r) {
        $obj = new stdClass();
        $obj->id = $r->id;
        $obj->nombre = $r->nombre;
        $row->categorias_relacionados[] = $obj;
      } 

      $publicidades[] = $row;
    }
    if (sizeof($publicidades)==0) return FALSE;
    else return $publicidades;
	}

}