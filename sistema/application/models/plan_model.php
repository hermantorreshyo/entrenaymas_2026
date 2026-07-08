<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Plan_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("planes","id","id_proyecto ASC, precio_anual ASC",0);
	}
    
	function find($filter) {
		return $this->get_all(null,null,$filter);
	}
	
	function get_by_proyecto($id_proyecto) {
		$this->db->where("id_proyecto",$id_proyecto);
		$query = $this->db->get($this->tabla);
		$result = $query->result();
		return $result;
	}
	
	function get_all($limit = null, $offset = null,$order = "", $order_by = "") {
    $filter = "";
		$id_empresa = $this->get_empresa();
    $sql = "SELECT P.*, ";
		$sql.= " IF(PR.nombre IS NULL,'',PR.nombre) AS proyecto ";
		$sql.= "FROM planes P ";
		$sql.= "LEFT JOIN com_proyectos PR ON (P.id_proyecto = PR.id) ";
		$sql.= "WHERE 1=1 ";
		$sql.= "AND P.id_empresa = $id_empresa ";
		if (!empty($filter)) $sql.= "AND P.nombre LIKE '%$filter%' ";
		$sql.= "ORDER BY P.id_proyecto ASC, P.precio_anual ASC ";
    $query = $this->db->query($sql);
		$salida = $query->result();
		return $salida;
	}

}