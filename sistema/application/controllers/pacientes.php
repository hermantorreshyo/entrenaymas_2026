<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Pacientes extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Paciente_Model', 'modelo');
  }

  function get_consultas() {
    $this->load->model("Consulta_Model");
    $id_paciente = $this->input->post("id_paciente");
    $res = $this->Consulta_Model->buscar(array(
      "id_contacto"=>$id_paciente,
      "offset"=>999999,
      "tipo"=>1,
    ));
    echo json_encode($res["results"]);
  }

  function get_by_nombre() {
    $id_empresa = parent::get_empresa();
    $nombre = $this->input->get("term");
    $s = $this->modelo->buscar(array(
      "filter"=>$nombre,
    ));
    $resultado = array();
    foreach($s["results"] as $r) {
      $rr = new stdClass();
      $rr->id = $r->id;
      $rr->value = $r->id;
      $rr->label = $r->nombre;
      $rr->info = "Cel: ".$r->celular." ".$r->obra_social;
      $rr->nombre = $r->nombre;
      $resultado[] = $rr;
    }            
    echo json_encode($resultado);
  }


  function ver() {
    $limit = $this->input->get("limit");
    $offset = $this->input->get("offset");
    $order_by = $this->input->get("order_by");
    $order = $this->input->get("order");
    $filter = $this->input->get("filter");
    if (!empty($order_by) && !empty($order)) $order = $order_by." ".$order;
    else $order = "";
    $conf = array(
      "filter"=>$filter,
      "limit"=>$limit,
      "offset"=>$offset,
      "order"=>$order,
    );
    $r = $this->modelo->buscar($conf);
    echo json_encode($r);
  }

}