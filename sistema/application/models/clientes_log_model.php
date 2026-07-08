<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Clientes_Log_Model extends Abstract_Model {
  
  function __construct() {
    parent::__construct("clientes_log","id","id ASC");
  }

  function registrar($config = array()) {
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $id_cliente = isset($config["id_cliente"]) ? $config["id_cliente"] : 0;
    $accion = isset($config["accion"]) ? $config["accion"] : "";
    $fecha = isset($config["fecha"]) ? $config["fecha"] : date("Y-m-d H:i:s");
    $sql = "INSERT INTO clientes_log (id_empresa, id_cliente, fecha, accion) VALUES (";
    $sql.= " $id_empresa, $id_cliente, '$fecha', 'Inicio de sesion' )";
    $this->db->query($sql);    
  }
  
  function buscar($config = array()){
    $limit = isset($config["limit"]) ? $config["limit"] : 0;
    $offset = isset($config["offset"]) ? $config["offset"] : 10;
    $id_cliente = isset($config["id_cliente"]) ? $config["id_cliente"] : 0;
    $id_empresa = parent::get_empresa();
    $sql= "SELECT SQL_CALC_FOUND_ROWS CL.id, CL.id_cliente AS id_usuario, U.nombre, ";
    $sql.= "DATE_FORMAT(CL.fecha,'%d/%m/%Y %H:%i') AS fecha, CL.accion ";
    $sql.= "FROM clientes_log CL ";
    $sql.= "INNER JOIN clientes U ON (CL.id_cliente = U.id AND CL.id_empresa = U.id_empresa) ";
    $sql.= "WHERE CL.id_empresa = $id_empresa ";
    if (!empty($id_cliente)) $sql.= "AND CL.id_cliente = $id_cliente ";
    $sql.= "ORDER BY CL.fecha ASC ";
    $sql.= "LIMIT $limit,$offset ";
    $query = $this->db->query($sql);

    $q_total = $this->db->query("SELECT FOUND_ROWS() AS total");
    $total = $q_total->row();

    $salida = array();
    foreach($query->result() as $r) {
      $salida[] = $r;
    }

    return array(
      "results"=>$salida,
      "total"=>$total->total,
    );
  }

}