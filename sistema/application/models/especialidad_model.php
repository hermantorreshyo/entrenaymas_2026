<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Especialidad_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("med_especialidades","id","nombre ASC");
	}

	function save($data) {
		$data->nombre = trim($data->nombre);
		$id = parent::save($data);
		$this->load->helper("file_helper");
		$data->link = filename($data->nombre,"-",0);
		$this->db->query("UPDATE med_especialidades SET link = '$data->link' WHERE id_empresa = $data->id_empresa AND id = $id ");
		return $id;
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