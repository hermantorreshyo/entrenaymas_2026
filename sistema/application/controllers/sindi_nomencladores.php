<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Sindi_Nomencladores extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Sindi_Nomenclador_Model', 'modelo');
  }

  function buscar() {
    $id_empresa = parent::get_empresa();
    $filter = parent::get_get("filter","");
    $id_tipo_practica = parent::get_get("id_tipo_practica",0);
    $offset = parent::get_get("offset",10);
    $order_by = parent::get_get("order_by","");
    $order = parent::get_get("order","ASC");
    $limit = parent::get_get("limit",0);
    $sql = "SELECT SQL_CALC_FOUND_ROWS * ";
    $sql.= "FROM sindi_nomencladores ";
    $sql.= "WHERE id_empresa = $id_empresa ";
    if (!empty($id_tipo_practica)) $sql.= "AND id_tipo_practica = $id_tipo_practica ";
    if (!empty($filter)) $sql.= "AND (nombre LIKE '%$filter%' OR codigo = '$filter') ";
    if (!empty($order_by)) $sql.= "ORDER BY $order_by $order ";
    $sql.= "LIMIT $limit, $offset ";
    $q = $this->db->query($sql);
    $salida = $q->result();

    $q_total = $this->db->query("SELECT FOUND_ROWS() AS total");
    $total = $q_total->row();

    echo json_encode(array(
      "results"=>$salida,
      "total"=>$total->total,
    ));
  }
  
  function valor_consulta() {
    $id_empresa = parent::get_empresa();
    $valor_consulta = parent::get_post("valor_consulta",0);
    $sql = "UPDATE sindi_configuracion SET valor_consulta = '$valor_consulta' WHERE id_empresa = '$id_empresa' ";
    $q = $this->db->query($sql);
    echo json_encode(array(
      "error"=>0
    ));
  }

  function get_valor_consulta() {
    $id_empresa = parent::get_empresa();
    $sql = "SELECT valor_consulta FROM sindi_configuracion WHERE id_empresa = '$id_empresa' ";
    $q = $this->db->query($sql);
    $salida = $q->row();     
    echo json_encode(array(
      "valor_consulta"=>$salida->valor_consulta,
    ));
  }
  	
}