<?php defined('BASEPATH') OR exit('No direct script access allowed');
require_once("abstract_model.php");

class Cupon_Descuento_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("cupones_descuentos","id","nombre ASC");
	}

	function get($id) {
		$id_empresa = parent::get_empresa();
		$sql = "SELECT C.*, ";
		$sql.= " IF(C.fecha_desde = '0000-00-00','',DATE_FORMAT(C.fecha_desde,'%d/%m/%Y')) AS fecha_desde, ";
		$sql.= " IF(C.fecha_hasta = '0000-00-00','',DATE_FORMAT(C.fecha_hasta,'%d/%m/%Y')) AS fecha_hasta ";
		$sql.= "FROM cupones_descuentos C ";
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
		$sql.= "FROM cupones_descuentos C ";
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