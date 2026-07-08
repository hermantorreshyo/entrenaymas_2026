<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Lote_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("lotes","id");
	}

	function find($filter) 
	{
		$this->db->like("nombre",$filter);
		$query = $this->db->get($this->tabla);
		$result = $query->result();
		$this->db->close();
		return $result;
	}
    
    
    function get_by_campo($id_campo) {
        $sql = "SELECT * FROM lotes WHERE id_campo = $id_campo ";
        $q = $this->db->query($sql);
		$result = $q->result();
		$this->db->close();
		return $result;
    }
    
	
	/**
	 * Devuelve todos los registros de la tabla
	 * @return Lista de registros
	 */
	function get_all($limit = null, $offset = null, $id_campo = 0) 
	{
		$sql = "SELECT * ";
		$sql.= "FROM lotes WHERE id_campo = $id_campo ";
        if (!empty($filter)) {
            $sql.= "AND codigo LIKE '%$filter%' ";
        }
		$sql.= "ORDER BY nombre ASC ";
		if (!is_null($limit) && (strlen($limit)>0) && !is_null($offset) && (strlen($offset)>0)) {
			$sql.= "LIMIT $limit,$offset ";
		}
		$query = $this->db->query($sql);
		$result = $query->result();
		$this->db->close();
		return $result;
	}
	
}