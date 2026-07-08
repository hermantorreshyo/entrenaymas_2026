<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Servicio_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("turnos_servicios","id");
	}

  function buscar($config = array()) {

  	$id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $limit = isset($config["limit"]) ? $config["limit"] : 0;
    $offset = isset($config["offset"]) ? $config["offset"] : 9999;
    $activo = isset($config["activo"]) ? $config["activo"] : 1;
		$destacado = isset($config["destacado"]) ? $config["destacado"] : -1; // -1 = No se tiene en cuenta el parametro
		$filter = isset($config["filter"]) ? $config["filter"] : 0;
		$order_by = isset($config["order_by"]) ? $config["order_by"] : "A.nombre ASC";

		$sql = "SELECT SQL_CALC_FOUND_ROWS A.*, ";
    $sql.= " IF(ES.nombre IS NULL,'',ES.nombre) AS especialidad ";
    $sql.= "FROM turnos_servicios A ";
    $sql.= "LEFT JOIN med_especialidades ES ON (A.id_especialidad = ES.id AND A.id_empresa = ES.id_empresa) ";
    $sql.= "WHERE 1=1 ";
    $sql.= "AND A.id_empresa = $id_empresa ";
    if (!empty($filter)) $sql.= "AND (A.nombre LIKE '%$filter%' OR A.texto LIKE '%$filter%') ";
    if ($activo != -1) $sql.= "AND A.activo = $activo ";
    if ($destacado != -1) $sql.= "AND A.destacado = $destacado ";
    $sql.= "ORDER BY $order_by ";
    $sql.= "LIMIT $limit,$offset ";
    $q = $this->db->query($sql);
    $salida = array();

    $q_total = $this->db->query("SELECT FOUND_ROWS() AS total");
    $total = $q_total->row();

    foreach($q->result() as $r) {

	    $r->texto = ($r->texto);
	    $r->texto_en = ($r->texto_en);
	    $r->nombre = ($r->nombre);
      $r->path = (!empty($r->path)) ? (((strpos($r->path,"http://")===FALSE)) ? "/sistema/".$r->path : $r->path) : "";
      $r->link = str_replace("servicio/", "profesional/", $r->link);

      // Obtenemos los dias de ese turno
      $sql = "SELECT DISTINCT dia ";
      $sql.= "FROM turnos_servicios_horarios H ";
      $sql.= "WHERE H.id_empresa = $r->id_empresa AND H.id_servicio = $r->id ";
      $qq = $this->db->query($sql);
      $r->dias = array();
      foreach($qq->result() as $rr) {
        $r->dias[] = $rr->dia;
      }
      $salida[] = $r;
    }
    return $salida;
  }	
		
}