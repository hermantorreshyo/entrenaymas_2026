<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Carp_Agencia_Model extends Abstract_Model {

  private $id_perfil = 952;
  private $total;
  
  function __construct() {
    parent::__construct("com_usuarios","id");
  }

  function buscar($config=array()) {

    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : $this->get_empresa();
    $filter = isset($config["filter"]) ? $config["filter"] : "";
    $limit = isset($config["limit"]) ? $config["limit"] : 0;
    $id_agencia = isset($config["id_agencia"]) ? $config["id_agencia"] : 0;
    $activo = isset($config["activo"]) ? $config["activo"] : -1;
    $offset = isset($config["offset"]) ? $config["offset"] : 10;
    $order = isset($config["order"]) ? $config["order"] : "U.nombre ASC ";
    $order = trim($order);
    if (empty($order)) $order = "U.nombre ASC ";

    $sql = "SELECT SQL_CALC_FOUND_ROWS U.* ";
    $sql.= "FROM com_usuarios U ";
    $sql.= "WHERE U.id_empresa = $id_empresa ";
    $sql.= "AND id_perfiles = $this->id_perfil ";
    if (!empty($filter)) $sql.= "AND U.nombre LIKE '%$filter%' ";
    if ($activo != -1) $sql.= "AND U.activo = $activo ";
    if (!empty($id_agencia)) $sql.= "AND U.id = $id_agencia ";
    $sql.= "ORDER BY $order ";
    $sql.= "LIMIT $limit, $offset ";
    $query = $this->db->query($sql);

    $q_total = $this->db->query("SELECT FOUND_ROWS() AS total");
    $total = $q_total->row();
    $this->total = $total->total;

    $result = $query->result();
    $this->db->close();
    return $result;
  }

  function get_total_results() {
    return $this->total;
  }
    
}