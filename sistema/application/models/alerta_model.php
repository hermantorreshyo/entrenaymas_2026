<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Alerta_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("app_alertas","id","fecha DESC");
	}

	function buscar($conf = array()) {
		
		$id_empresa = isset($conf["id_empresa"]) ? $conf["id_empresa"] : parent::get_empresa();
		$filter = isset($conf["filter"]) ? $conf["filter"] : "";
		$limit = isset($conf["limit"]) ? $conf["limit"] : 0;
		$offset = isset($conf["offset"]) ? $conf["offset"] : 10;
		$order = isset($conf["order"]) ? $conf["order"] : "A.id DESC";
		
		$sql = "SELECT A.*, ";
		$sql.= " DATE_FORMAT(A.fecha,'%d/%m/%Y') AS fecha, ";
		$sql.= " IF(U.nombre IS NULL,'',U.nombre) AS nombre, ";
		$sql.= " IF(U.direccion IS NULL,'',U.direccion) AS direccion ";
		$sql.= "FROM app_alertas A ";
		$sql.= " LEFT JOIN web_users U ON (A.id_usuario = U.id) ";
		$sql.= "WHERE A.id_empresa = $id_empresa ";
		$sql.= "ORDER BY $order ";
		$sql.= "LIMIT $limit, $offset ";
        $q = $this->db->query($sql);
        
        $q_total = $this->db->query("SELECT FOUND_ROWS() AS total");
        $total = $q_total->row();
		
		return array(
            "results"=>$q->result(),
            "total"=>$total->total,
		);
	}	
}