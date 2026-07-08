<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Gasto_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("gastos","id");
	}
    
	function update($id,$data) {
        unset($data->proveedor);
        unset($data->tipo_gasto);
		$this->db->where($this->ident,$id);
		$this->db->update($this->tabla,$data);
		$aff = $this->db->affected_rows();
		$this->db->close();
		return $aff;
	}
	
	function insert($data) {
        unset($data->proveedor);
        unset($data->tipo_gasto);
        $this->load->helper("fecha_helper");
        $data->fecha = fecha_mysql($data->fecha);
		$this->db->insert($this->tabla,$data);
		$id = $this->db->insert_id();
		$this->db->close();
		if (!isset($id)) return -1;
		else return $id;
	}    

}