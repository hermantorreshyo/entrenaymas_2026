<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Sucursal_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("sucursales","id",'nombre ASC');
	}

	function find($filter) 
	{
		$this->db->like("nombre",$filter);
		$query = $this->db->get($this->tabla);
		$result = $query->result();
		$this->db->close();
		return $result;
	}
	
	function save($data) {
		unset($data->localidad);
		parent::save($data);
	}
	
	function get($id) {
		$sql = "SELECT S.*, ";
		$sql.= " IF(L.nombre IS NULL,'',L.nombre) AS localidad ";
		$sql.= "FROM sucursales S ";
		$sql.= "LEFT JOIN com_localidades L ON (S.id_localidad = L.id) ";
		$sql.= "WHERE S.id = $id";
		$query = $this->db->query($sql);
		$row = $query->row();
		$this->db->close();
		return $row;
	}

}