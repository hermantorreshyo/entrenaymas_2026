<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Web_Banner_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("web_banners","id","orden ASC",1);
	}
    
	function find($filter) {
		$this->db->like("nombre",$filter);
		$query = $this->db->get($this->tabla);
		$result = $query->result();
		$this->db->close();
		return $result;
	}
	
	function save($data) {
		$data->id_empresa = $this->get_empresa();
		parent::save($data);
	}	

}