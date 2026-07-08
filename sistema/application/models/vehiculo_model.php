<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Vehiculo_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("via_vehiculos","id","nombre ASC");
	}
    
	function find($filter) {
		$id_empresa = parent::get_empresa();
		$this->db->where("id_empresa",$id_empresa);
		$this->db->like("nombre",$filter);
		$query = $this->db->get($this->tabla);
		$result = $query->result();
		$this->db->close();
		return $result;
	}

	function get($id) {
		$row = parent::get($id);
		return $row;
	}

	function save($data) {
		$id = parent::save($data);
		return $id;
	}

}