<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Web_Componente_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("web_componentes","id","orden ASC",1);
	}
    
	function find($filter) {
		$this->db->like("nombre",$filter);
		$query = $this->db->get($this->tabla);
		$result = $query->result();
		$this->db->close();
		return $result;
	}

	function insert($data) {
		// Ponemos el orden del elemento en ultimo lugar
		$sql = "SELECT IF(MAX(orden) IS NULL,0,MAX(orden)+1) AS maximo FROM web_componentes WHERE id_empresa = $data->id_empresa ORDER BY orden ASC ";
		$q = $this->db->query($sql);
		$row = $q->row();
		$data->orden = $row->maximo;
		return parent::insert($data);
	}	
	
	function save($data) {
		$data->id_empresa = $this->get_empresa();
		return parent::save($data);
	}	

}