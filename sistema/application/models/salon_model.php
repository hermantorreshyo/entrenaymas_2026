<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Salon_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("res_salones","id","nombre ASC");
	}

	function delete($id) {
		$id_empresa = parent::get_empresa();
		if ($id_empresa === FALSE) return;
		$this->db->query("DELETE FROM res_mesas WHERE id_salon = $id AND id_empresa = $id_empresa");
		$this->db->query("DELETE FROM res_salones WHERE id = $id AND id_empresa = $id_empresa");
	}
    
}