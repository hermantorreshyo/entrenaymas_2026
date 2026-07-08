<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Caja_Model extends Abstract_Model {
  
  function __construct() {
    parent::__construct("cajas","id");
  }
    
  function save($data) {
    unset($data->sucursal);
    parent::save($data);
  }
    
    
  function find($filter) {
    $id_empresa = parent::get_empresa();
    $sql = "SELECT C.*, IF(E.nombre IS NULL,'',E.nombre) AS sucursal ";
    $sql.= "FROM cajas C LEFT JOIN almacenes E ON (C.id_sucursal = E.id AND C.id_empresa = E.id_empresa) ";
    $sql.= "WHERE C.id_empresa = $id_empresa ";
    $sql.= "AND C.nombre LIKE '%$filter%' ";
    $sql.= "ORDER BY E.nombre ASC, C.nombre ASC ";
    $query = $this->db->query($sql);
    $result = $query->result();
    $this->db->close();
    return $result;
  }

  function buscar($config = array()) {
    $id_empresa = (isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa());
    $id_sucursal = (isset($config["id_sucursal"]) ? $config["id_sucursal"] : 0);
    $limit = (isset($config["limit"]) ? $config["limit"] : 0);
    $offset = (isset($config["offset"]) ? $config["offset"] : 9999);
    $filter = (isset($config["filter"]) ? $config["filter"] : "");
    $activo = (isset($config["activo"]) ? $config["activo"] : 1);
    $buscar_saldo = (isset($config["buscar_saldo"]) ? $config["buscar_saldo"] : 1);
    $tipo = (isset($config["tipo"]) ? $config["tipo"] : -1);
    $sql = "SELECT C.*, IF(E.nombre IS NULL,'',E.nombre) AS sucursal ";
    $sql.= "FROM cajas C LEFT JOIN almacenes E ON (C.id_sucursal = E.id AND C.id_empresa = E.id_empresa) ";
    $sql.= "WHERE C.id_empresa = $id_empresa ";
    if ($activo != -1) $sql.= "AND C.activo = $activo ";
    if ($tipo != -1) $sql.= "AND C.tipo = $tipo ";
    if (!empty($id_sucursal)) $sql.= "AND C.id_sucursal = $id_sucursal ";
    if (!empty($filter)) {
      $sql.= "AND C.nombre LIKE '%$filter%' ";
    }
    $sql.= "ORDER BY E.nombre ASC, C.nombre ASC ";
    //if (!is_null($limit) && (strlen($limit)>0) && !is_null($offset) && (strlen($offset)>0)) {
      //$sql.= "LIMIT $limit,$offset ";
    //}
    $query = $this->db->query($sql);
    $result = $query->result();

    $this->load->model("Caja_Movimiento_Model");
    // Obtenemos los saldos de las cajas
    foreach($result as $r) {
      if ($buscar_saldo == 1) {
        $r->saldo = $this->Caja_Movimiento_Model->calcular_saldo(array(
          "id_caja"=>$r->id,
          "id_empresa"=>$r->id_empresa,
          "desde"=>date("Y-m-d",strtotime(date("Y-m-d")." +1 day")),
        ));
      }
    }
    return array(
      "total"=>sizeof($result),
      "results"=>$result,
    );
  }
        
    
  function get_all($limit = null, $offset = null,$order_by = '',$order = '') {
    return $this->buscar(array(
      "filter"=>$filter,
      "limit"=>$limit,
      "offset"=>$offset,
    ));
  }
  
  function get($id) {
    $sql = "SELECT C.*, IF(E.nombre IS NULL,'',E.nombre) AS sucursal ";
    $sql.= "FROM cajas C LEFT JOIN almacenes E ON (C.id_sucursal = E.id AND C.id_empresa = E.id_empresa) ";
    $sql.= "WHERE C.id = $id ";
    $query = $this->db->query($sql);
    $row = $query->row(); 
    $this->db->close();
    return $row;
  }    
    

}