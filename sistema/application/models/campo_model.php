<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Campo_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("campos","id","nombre");
	}

	function find($filter) 
	{
		$sql = "SELECT C.*, E.nombre AS empresa ";
		$sql.= "FROM campos C INNER JOIN empresas E ON (C.id_empresa = E.id) WHERE 1=1 ";
        $sql.= "AND C.nombre LIKE '%$filter%' ";
		$sql.= "ORDER BY E.nombre ASC, C.nombre ASC ";
		$query = $this->db->query($sql);
		$result = $query->result();
		$this->db->close();
		return $result;
	}
    
    function get_by_empresa($id_empresa) {
        $sql = "SELECT * FROM campos WHERE id_empresa = $id_empresa ORDER BY nombre ASC ";
        $q = $this->db->query($sql);
		$result = $q->result();
		$this->db->close();
		return $result;
    }
    
    
	/**
	 * Devuelve todos los registros de la tabla
	 */
	function get_all($filter = "", $limit = null, $offset = null) 
	{
		$sql = "SELECT C.*, E.nombre AS empresa ";
		$sql.= "FROM campos C INNER JOIN empresas E ON (C.id_empresa = E.id) WHERE 1=1 ";
        if (!empty($filter)) {
            $sql.= "AND C.nombre LIKE '%$filter%' ";
        }
		$sql.= "ORDER BY E.nombre ASC, C.nombre ASC ";
		if (!is_null($limit) && (strlen($limit)>0) && !is_null($offset) && (strlen($offset)>0)) {
			$sql.= "LIMIT $limit,$offset ";
		}
		$query = $this->db->query($sql);
		$result = $query->result();
		$this->db->close();
		return $result;
	}
	
	function get($id) 
	{
		$sql = "SELECT C.*, E.nombre AS empresa ";
		$sql.= "FROM campos C INNER JOIN empresas E ON (C.id_empresa = E.id) ";
		$sql.= "WHERE C.id = $id ";
		$query = $this->db->query($sql);
		$row = $query->row(); 
		$this->db->close();
		return $row;
	}		
    
}