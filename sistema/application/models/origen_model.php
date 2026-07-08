<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Origen_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("crm_origenes","id","orden ASC",0);
	}
    
	function find($filter) {
		$this->db->like("nombre",$filter);
		$query = $this->db->get($this->tabla);
		$result = $query->result();
		$this->db->close();
		return $result;
	}
	
	function post_save($id) {
		$this->load->helper("file_helper");
		$row = $this->get($id);
		$link = filename($row->nombre,"-",0);
		$this->db->query("UPDATE crm_origenes SET link = '$link' WHERE id = $id");
	}
	
}