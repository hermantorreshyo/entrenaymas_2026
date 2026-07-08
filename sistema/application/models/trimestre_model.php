<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Trimestre_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("aca_trimestres","id","fecha_desde ASC");
	}

  function save($data) {
    $this->load->helper("fecha_helper");
    $data->fecha_desde = fecha_mysql($data->fecha_desde);
    $data->fecha_hasta = fecha_mysql($data->fecha_hasta);
    return parent::save($data);
  }

  function buscar($conf = array()) {
    $id_empresa = isset($conf["id_empresa"]) ? $conf["id_empresa"] : $this->get_empresa();
    $limit = isset($conf["limit"]) ? $conf["limit"] : 0;
    $offset = isset($conf["offset"]) ? $conf["offset"] : 10;
    $filter = isset($conf["filter"]) ? $conf["filter"] : "";
    $order_by = isset($conf["order_by"]) ? trim($conf["order_by"]) : "";
    if (empty($order_by)) $order_by = "fecha_desde ASC ";
    $sql = "SELECT *, ";
    $sql.= " IF(fecha_desde = '0000-00-00','',DATE_FORMAT(fecha_desde,'%d/%m/%Y')) AS fecha_desde, ";
    $sql.= " IF(fecha_hasta = '0000-00-00','',DATE_FORMAT(fecha_hasta,'%d/%m/%Y')) AS fecha_hasta ";
    $sql.= "FROM aca_trimestres ";
    $sql.= "WHERE id_empresa = $id_empresa ";
    if (!empty($filter)) $sql.= "AND nombre LIKE '%$filter%' ";
    if (!empty($order_by)) $sql.= "ORDER BY $order_by ";
    $query = $this->db->query($sql);
    $result = $query->result();
    return $result;
  }
	
	function find($filter) {
		$id_empresa = parent::get_empresa();
		$this->db->where("id_empresa",$id_empresa);
		$this->db->like("nombre",$filter);
		$query = $this->db->get($this->tabla);
		$result = $query->result();
		$this->db->close();
		return $result;
	}    

}