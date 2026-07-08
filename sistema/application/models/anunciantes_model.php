<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Anunciantes_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("anunciantes","id","nombre ASC");
	}

  function buscar($conf = array()) {
    
    $id_empresa = isset($conf["id_empresa"]) ? $conf["id_empresa"] : parent::get_empresa();
    $filter = isset($conf["filter"]) ? $conf["filter"] : "";
    $limit = isset($conf["limit"]) ? $conf["limit"] : 0;
    $offset = isset($conf["offset"]) ? $conf["offset"] : 10;
    $order = isset($conf["order"]) ? $conf["order"] : "";
    
    $sql = "SELECT SQL_CALC_FOUND_ROWS A.* ";
    $sql.= "FROM anunciantes A ";
    $sql.= "WHERE 1=1 ";
    $sql.= "AND A.id_empresa = $id_empresa ";
    if ($_SESSION["perfil"] != 1355) $sql.= "AND (mostrar = 'todos' OR mostrar = 'backend') ";
    if (!empty($order)) $sql.= "ORDER BY $order ";
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