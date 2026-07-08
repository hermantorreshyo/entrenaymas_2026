<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Calm_Escena_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("calm_escenas","id","nombre ASC");
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