<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Departamento_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("aca_departamentos","id","nombre ASC");
	}
	
	function save($data) {
    unset($data->departamento);
    parent::save($data);
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

}