<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Tipo_Vehiculo_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("veh_tipos","id","orden ASC",0);
	}
    
	function find($filter) {
		$this->db->like("nombre",$filter);
		$query = $this->db->get($this->tabla);
		$result = $query->result();
		$this->db->close();
		return $result;
	}        

}