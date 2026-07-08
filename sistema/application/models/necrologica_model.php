<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Necrologica_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("inf_necrologicas","id","fecha_fallecimiento DESC",1);
	}
    
	function find($filter) {
		$this->db->like("nombre",$filter);
		$query = $this->db->get($this->tabla);
		$result = $query->result();
		$this->db->close();
		return $result;
	}
	
	function save($data) {
		$this->load->helper("fecha_helper");
		$data->fecha_fallecimiento = fecha_mysql($data->fecha_fallecimiento);
		$data->fecha_traslado = fecha_mysql($data->fecha_traslado);
		$data->fecha = date("Y-m-d H:i:s");
		return parent::save($data);
	}
}