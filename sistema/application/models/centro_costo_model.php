<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Centro_Costo_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("centros_costos","id","nombre ASC");
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

  function get_sucursales($id_centro_costo,$config = array()) {
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $sql = "SELECT * FROM almacenes WHERE id_empresa = $id_empresa ";
    $sql.= "AND id_centro_costo = $id_centro_costo ";
    $query = $this->db->query($sql);
    return $query->result();
  }

}