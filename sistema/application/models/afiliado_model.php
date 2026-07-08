<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Afiliado_Model extends Abstract_Model {

	function __construct() {
		parent::__construct("custom_afiliados","id","apellido ASC");
	}
	
	function get($id,$id_empresa = 0) {
		if (empty($id)) return FALSE;
		if ($id_empresa == 0) $id_empresa = parent::get_empresa();
		$sql = "SELECT C.*, ";
		$sql.= "  IF(U.nombre IS NULL,'',U.nombre) AS usuario, ";
		$sql.= "  IF(C.fecha_inicial = '0000-00-00','',DATE_FORMAT(C.fecha_inicial,'%d/%m/%Y')) AS fecha_inicial, ";
		$sql.= "  DATE_FORMAT(C.fecha_nac,'%d/%m/%Y') AS fecha_nac ";
		$sql.= "FROM custom_afiliados C ";
		$sql.= "  LEFT JOIN com_usuarios U ON (C.id_usuario = U.id) ";
		$sql.= "WHERE C.id = $id ";
		$sql.= "AND C.id_empresa = $id_empresa ";
		$query = $this->db->query($sql);
		$row = $query->row(); 
		$this->db->close();
		return $row;
	}
	
	function get_by_email($email,$id_empresa = 0) {
		if ($id_empresa == 0) $id_empresa = parent::get_empresa();
		$sql = "SELECT C.*, ";
		$sql.= "  DATE_FORMAT(C.fecha_inicial,'%d/%m/%Y') AS fecha_inicial, ";
		$sql.= "  DATE_FORMAT(C.fecha_nac,'%d/%m/%Y') AS fecha_nac ";
		$sql.= "FROM custom_afiliados C ";
		$sql.= "WHERE C.email = '$email' AND C.id_empresa = $id_empresa ";
		$query = $this->db->query($sql);
		if ($query->num_rows() == 0) return FALSE;
		$row = $query->row(); 
		$this->db->close();
		return $row;
	}	
	
    
	function buscar($params = array()) {
		$id_empresa = (isset($params["id_empresa"])) ? $params["id_empresa"] : $this->get_empresa();
		$filter = isset($params["filter"]) ? $params["filter"] : "";
		$limit = isset($params["limit"]) ? $params["limit"] : 0;
		$offset = isset($params["offset"]) ? $params["offset"] : 0;		
		$order = (isset($params["order"]) && !empty($params["order"])) ? $params["order"] : "C.nombre ASC ";
		$sql = "SELECT SQL_CALC_FOUND_ROWS C.*, ";
		$sql.= "  IF(U.nombre IS NULL,'',U.nombre) AS usuario, ";
		$sql.= "  IF(C.fecha_inicial = '0000-00-00','',DATE_FORMAT(C.fecha_inicial,'%d/%m/%Y')) AS fecha_inicial, ";
		$sql.= "  DATE_FORMAT(C.fecha_nac,'%d/%m/%Y') AS fecha_nac ";
		$sql.= "FROM custom_afiliados C ";
		$sql.= "  LEFT JOIN com_usuarios U ON (C.id_usuario = U.id) ";
		$sql.= "WHERE C.id_empresa = $id_empresa ";
		if (!empty($filter)) $sql.= "AND (CONCAT(C.apellido,' ',C.nombre) LIKE '%$filter%') ";
		$sql.= "ORDER BY $order ";
		if (!empty($offset)) $sql.= "LIMIT $limit, $offset ";
    $q = $this->db->query($sql);
    
    $q_total = $this->db->query("SELECT FOUND_ROWS() AS total");
    $total = $q_total->row();

		return array(
            "results"=>$q->result(),
            "total"=>$total->total,
            "sql"=>$sql,
		);	
	}    
}