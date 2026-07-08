<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Sitemap_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("web_sitemap","id","id ASC");
	}
	
	function find($filter) {
		$id_empresa = parent::get_empresa();
		$this->db->where("id_empresa",$id_empresa);
		$this->db->like("url",$filter);
		$query = $this->db->get($this->tabla);
		$result = $query->result();
		$this->db->close();
		return $result;
	}
	
	function buscar($conf = array()) {
		
		$id_empresa = isset($conf["id_empresa"]) ? $conf["id_empresa"] : parent::get_empresa();
		$filter = isset($conf["filter"]) ? $conf["filter"] : "";
		$limit = isset($conf["limit"]) ? $conf["limit"] : 0;
		$offset = isset($conf["offset"]) ? $conf["offset"] : 10;
		$order = isset($conf["order"]) ? $conf["order"] : "A.id ASC";
		
        $sql = "SELECT SQL_CALC_FOUND_ROWS DISTINCT A.*, ";
        $sql.= " DATE_FORMAT(A.lastmod,'%d/%m/%Y') AS lastmod ";
        $sql.= "FROM web_sitemap A ";
		$sql.= "WHERE 1=1 ";
		$sql.= "AND A.id_empresa = $id_empresa ";
        if (!empty($filter)) $sql.= "AND A.url LIKE '%$filter%' ";
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
	
	function get($id) {
		$id_empresa = parent::get_empresa();
		// Obtenemos los datos del libro
		$id = (int)$id;
		$sql = "SELECT A.*, ";
		$sql.= " DATE_FORMAT(A.lastmod,'%d/%m/%Y') AS lastmod ";
		$sql.= "FROM web_sitemap A ";
		$sql.= "WHERE A.id = $id ";
		$sql.= "AND A.id_empresa = $id_empresa ";
		$q = $this->db->query($sql);
		if ($q->num_rows() == 0) return array();
		$row = $q->row();
		return $row;
	}
	
}