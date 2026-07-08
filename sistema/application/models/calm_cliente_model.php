<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Calm_Cliente_Model extends Abstract_Model {
  
  function __construct() {
    parent::__construct("clientes","id","nombre ASC");
  }

  function buscar($conf = array()) {
    $id_empresa = isset($conf["id_empresa"]) ? $conf["id_empresa"] : parent::get_empresa();
    $order = isset($conf["order"]) ? $conf["order"] : "ASC";
    if (empty($order)) $order = "ASC";
    $order_by = isset($conf["order_by"]) ? $conf["order_by"] : "A.nombre";
    if (empty($order_by)) $order_by = "A.nombre";
    $filter = isset($conf["filter"]) ? $conf["filter"] : "";
    $limit = isset($conf["limit"]) ? $conf["limit"] : 0;
    $offset = isset($conf["offset"]) ? $conf["offset"] : 10;
    $sql = "SELECT SQL_CALC_FOUND_ROWS A.* ";
    $sql.= "FROM clientes A ";
    $sql.= "WHERE A.id_empresa = $id_empresa ";
    if (!empty($filter)) $sql.= "AND A.nombre LIKE '%$filter%' ";
    $sql.= "ORDER BY $order_by $order ";
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