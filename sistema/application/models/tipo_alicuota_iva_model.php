<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Tipo_Alicuota_Iva_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("tipos_alicuotas_iva","id","id ASC",0);
	}

	function find($filter) {
		$this->db->like("nombre",$filter);
		$query = $this->db->get($this->tabla);
		$result = $query->result();
		$this->db->close();
		return $result;
	}

  function get($id) {
    $id_empresa = parent::get_empresa();
    $sql = "SELECT T.* ";
    $sql.= "FROM tipos_alicuotas_iva T ";
    $sql.= "WHERE T.id = $id AND T.id_empresa = $id_empresa ";
    $q = $this->db->query($sql);
    return $q->row();
  }

  function buscar($conf=array()) {
    $id_empresa = isset($conf["id_empresa"]) ? $conf["id_empresa"] : $this->get_empresa();
    $limit = isset($conf["limit"]) ? $conf["limit"] : 0;
    $offset = isset($conf["offset"]) ? $conf["offset"] : 10;
    $filter = isset($conf["filter"]) ? $conf["filter"] : "";
    $order_by = isset($conf["order_by"]) ? $conf["order_by"] : "";
    $sql = "SELECT * ";
    $sql.= "FROM tipos_alicuotas_iva ";
    $sql.= "WHERE id_empresa = $id_empresa ";
    if (!empty($filter)) $sql.= "AND nombre LIKE '%$filter%' ";
    if (!empty($order_by)) $sql.= "ORDER BY $order_by ";
    $query = $this->db->query($sql);
    $result = $query->result();
    return $result;
  }
	
}