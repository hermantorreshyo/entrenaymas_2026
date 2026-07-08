<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Usuario_Visita_Model extends Abstract_Model {
  
  function __construct() {
    parent::__construct("com_usuarios_visitas","id","fecha DESC");
  }

  /*
  function buscar($conf = array()) {
    
    $id_empresa = isset($conf["id_empresa"]) ? $conf["id_empresa"] : parent::get_empresa();
    $filter = isset($conf["filter"]) ? $conf["filter"] : "";
    $limit = isset($conf["limit"]) ? $conf["limit"] : 0;
    $offset = isset($conf["offset"]) ? $conf["offset"] : 10;
    $order = isset($conf["order"]) ? $conf["order"] : "A.apellido ASC";
    
    $sql = "SELECT SQL_CALC_FOUND_ROWS DISTINCT C.nombre, C.id, C.email, C.celular, C.activo ";
    $sql.= "FROM aca_tutores A ";
    $sql.= "INNER JOIN clientes C ON (A.id_cliente = C.id AND A.id_empresa = C.id_empresa) ";
    $sql.= "WHERE 1=1 ";
    $sql.= "AND A.id_empresa = $id_empresa ";
    if (!empty($filter)) $sql.= "AND C.nombre LIKE '%$filter%' ";
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
  */
  
}