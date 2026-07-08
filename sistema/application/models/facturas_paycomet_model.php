<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Facturas_Paycomet_Model extends Abstract_Model {
  
  function __construct() {
    parent::__construct("facturas_paycomet","id","id DESC");
  }
  
  function buscar($config = array()){
    $limit = isset($config["limit"]) ? $config["limit"] : 0;
    $offset = isset($config["offset"]) ? $config["offset"] : 10;
    $id_usuario = isset($config["id_usuario"]) ? $config["id_usuario"] : 0;
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();

    $sql = "SELECT SQL_CALC_FOUND_ROWS FC.*, ";
    $sql.= "(SELECT COUNT(UT.id) FROM usuarios_tarjetas UT WHERE UT.id_usuario = FC.id_usuario AND UT.id_empresa = FC.id_empresa) as cantidad_tarjeta_usuario, ";
    $sql.= "IF(U.nombre is NULL, 'Usuario no disponible', U.nombre) as usuario ";
    $sql.= "FROM facturas_paycomet FC ";
    $sql.= "LEFT JOIN com_usuarios U ON (FC.id_usuario = U.id AND FC.id_empresa = U.id_empresa) ";
    $sql.= "WHERE FC.id_empresa = $id_empresa ";
    if (!empty($id_usuario)) $sql.= "AND FC.id_usuario = $id_usuario ";
    $sql.= "ORDER BY FC.id DESC ";
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

  function get($id) {
    $sql.= "SELECT FP.*, ";
    $sql.= "IF(U.nombre IS NULL, '', U.nombre) as nombre_usuario ";
    $sql.= "FROM facturas_paycomet FP ";
    $sql.= "LEFT JOIN com_usuarios U ON (FP.id_usuario = U.id AND FP.id_empresa = U.id_empresa) ";
    $sql.= "WHERE FP.id = '$id' ";

    $q = $this->db->query($sql);
    return $q->row();
  }

}