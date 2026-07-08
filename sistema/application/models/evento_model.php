<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Cliente_Model extends Abstract_Model {

	function __construct() {
		parent::__construct("crm_eventos","id","nombre ASC");
	}
/*
	function get($id) {
		if (empty($id)) return FALSE;
		$id_empresa = parent::get_empresa();
		$sql = "SELECT C.*, ";
		$sql.= "  IF (TI.nombre IS NULL,'',TI.nombre) AS tipo_iva, ";
		$sql.= "  IF (L.nombre IS NULL,'',L.nombre) AS localidad ";
		$sql.= "FROM clientes C ";
		$sql.= " LEFT JOIN tipos_iva TI ON (C.id_tipo_iva = TI.id) ";
		$sql.= " LEFT JOIN localidades L ON (C.id_localidad = L.id) ";
		$sql.= "WHERE C.id = $id ";
		$sql.= "AND C.id_empresa = $id_empresa ";
		$query = $this->db->query($sql);
		$row = $query->row(); 
		$this->db->close();
		return $row;
	}
	
	function find($filter) {
		$id_empresa = parent::get_empresa();
		$this->db->where("id_empresa",$id_empresa);
		$this->db->like("nombre",$filter);
		$this->db->or_like("codigo",$filter);
		$query = $this->db->get($this->tabla);
		$result = $query->result();
		$this->db->close();
		return $result;
	}
*/
}