<?php defined('BASEPATH') OR exit('No direct script access allowed');
require_once("abstract_model.php");

class Webinar_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("webinars","id","nombre ASC");
	}

  function insert($data) {
    $this->load->helper("file_helper");
    $this->load->helper();
    $data->link = "webinar/".filename($data->nombre,"-",0)."/";
    return parent::insert($data);
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