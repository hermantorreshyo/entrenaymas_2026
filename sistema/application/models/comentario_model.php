<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Comentario_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("not_entradas_comentarios","id","fecha DESC");
	}
	
	function save($data) {
		unset($data->usuario);
		unset($data->entrada);
		unset($data->path);
		return parent::save($data);
	}	
    
	function buscar($conf = array()) {
		
		$id_empresa = isset($conf["id_empresa"]) ? $conf["id_empresa"] : parent::get_empresa();
		$filter = isset($conf["filter"]) ? $conf["filter"] : "";
		$limit = isset($conf["limit"]) ? $conf["limit"] : 0;
		$offset = isset($conf["offset"]) ? $conf["offset"] : 10;
		$fecha = isset($conf["fecha"]) ? $conf["fecha"] : "";
		$desde = isset($conf["desde"]) ? $conf["desde"] : "";
		$hasta = isset($conf["hasta"]) ? $conf["hasta"] : "";
		$order = isset($conf["order"]) ? $conf["order"] : "A.fecha DESC";
		$id_usuario = isset($conf["id_usuario"]) ? $conf["id_usuario"] : 0;
		$id_entrada = isset($conf["id_entrada"]) ? $conf["id_entrada"] : 0;
		
        $sql = "SELECT SQL_CALC_FOUND_ROWS DISTINCT A.*, ";
		$sql.= "  IF(A.fecha='0000-00-00 00:00:00','',DATE_FORMAT(A.fecha,'%d/%m/%Y %H:%i')) AS fecha, ";
		$sql.= "  IF(E.titulo IS NULL,'',E.titulo) AS entrada, ";
		$sql.= "  IF(E.path IS NULL,'',E.path) AS entrada_path, ";
		$sql.= "  IF(U.nombre IS NULL,'',U.nombre) AS usuario, ";
		$sql.= "  IF(U.path IS NULL,'',U.path) AS path ";
        $sql.= "FROM not_entradas_comentarios A ";
		$sql.= "LEFT JOIN web_users U ON (A.id_usuario = U.id) ";
		$sql.= "LEFT JOIN not_entradas E ON (A.id_entrada = E.id) ";
		$sql.= "WHERE 1=1 ";
		$sql.= "AND A.id_empresa = $id_empresa ";
        if (!empty($filter)) $sql.= "AND A.texto LIKE '%$filter%' ";
		if (!empty($fecha)) $sql.= "AND DATE_FORMAT(A.fecha,'%d/%m/%Y') = '$fecha' ";
		if (!empty($desde)) $sql.= "AND '$desde' <= A.fecha ";
		if (!empty($hasta)) $sql.= "AND A.fecha <= '$hasta' ";
		if (!empty($id_usuario)) $sql.= "AND A.id_usuario = '$id_usuario' ";
		if (!empty($id_entrada)) $sql.= "AND A.id_entrada = '$id_entrada' ";
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