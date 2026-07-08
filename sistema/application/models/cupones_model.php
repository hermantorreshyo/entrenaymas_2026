<?php defined('BASEPATH') OR exit('No direct script access allowed');
require_once("abstract_model.php");

class Cupones_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("cupones","id","nombre ASC");
	}

	function verificar_cupon($codigo, $conf = array()) {
		$id_empresa = isset($conf["id_empresa"]) ? $conf["id_empresa"] : parent::get_empresa();
		$use_if_valid = isset($conf["use_if_valid"]) ? $conf["use_if_valid"] : 0;

		$hoy = date("Y-m-d");

		$sql = "SELECT * FROM cupones ";
		$sql.= "WHERE id_empresa = '$id_empresa' ";
		$sql.= "AND (maximo_utilizable > cantidad_utilizada OR maximo_utilizable = 0) ";
		$sql.= "AND fecha_desde <= '$hoy' AND '$hoy' <= fecha_hasta ";
		$sql.= "AND codigo = '$codigo' ";
		$sql.= "LIMIT 0,1";
		$q = $this->db->query($sql);

		if ($q->num_rows() == 0) return FALSE;

		$descuento = $q->row()->descuento;
		if ($use_if_valid == 1) {
			$sql = "UPDATE cupones SET cantidad_utilizada = cantidad_utilizada + 1 ";
			$sql.= "WHERE id_empresa = '$id_empresa' AND codigo = '$codigo' "; 
			$q = $this->db->query($sql);
		}

		return $descuento;
	}

	function get($id) {
		$id_empresa = parent::get_empresa();
		$sql = "SELECT C.*, ";
		$sql.= " IF(C.fecha_desde = '0000-00-00','',DATE_FORMAT(C.fecha_desde,'%d/%m/%Y')) AS fecha_desde, ";
		$sql.= " IF(C.fecha_hasta = '0000-00-00','',DATE_FORMAT(C.fecha_hasta,'%d/%m/%Y')) AS fecha_hasta ";
		$sql.= "FROM cupones C ";
		$sql.= "WHERE C.id_empresa = $id_empresa ";		
		$sql.= "AND C.id = $id ";
		$q = $this->db->query($sql);
		return $q->row();
	}

	function buscar($config = array()) {
		$id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
		$filter = isset($config["filter"]) ? $config["filter"] : "";
		$sql = "SELECT SQL_CALC_FOUND_ROWS C.*, ";
		$sql.= " IF(C.fecha_desde = '0000-00-00','',DATE_FORMAT(C.fecha_desde,'%d/%m/%Y')) AS fecha_desde, ";
		$sql.= " IF(C.fecha_hasta = '0000-00-00','',DATE_FORMAT(C.fecha_hasta,'%d/%m/%Y')) AS fecha_hasta ";
		$sql.= "FROM cupones C ";
		$sql.= "WHERE C.id_empresa = $id_empresa ";
		if (!empty($filter)) $sql.= "AND C.nombre LIKE '%$filter%' ";
		$q = $this->db->query($sql);
		$resultado = $q->result();
    $q_total = $this->db->query("SELECT FOUND_ROWS() AS total");
    $total = $q_total->row();
		return array(
      "results"=>$resultado,
      "total"=>$total->total,
		);
	}

	function save($data) {
		$this->load->helper("fecha_helper");
		$data->fecha_desde = fecha_mysql($data->fecha_desde);
		$data->fecha_hasta = fecha_mysql($data->fecha_hasta);
		return parent::save($data);
	}

}