<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Trimestres extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Trimestre_Model', 'modelo');
  }

  function get($id) {

    // Obtenemos todos los registros
    if ($id == "index") {
            
      $limit = $this->input->get("limit");
      $offset = $this->input->get("offset");
      $filter = $this->input->get("filter");
      $order_by = $this->input->get("order_by");
      $order = $this->input->get("order");

      $lista = $this->modelo->buscar(array(
        "limit"=>(($limit === FALSE) ? 0 : $limit),
        "offset"=>(($offset === FALSE) ? 10 : $offset),
        "filter"=>(($filter === FALSE) ? "" : $filter),
        "order_by"=>(($order_by === FALSE && $order === FALSE) ? "nombre ASC" : $order_by." ".$order),
      ));
      $total = $this->modelo->count_all();

      $salida = array(
        "total"=> $total,
        "results"=>$lista
      );
      echo json_encode($salida);

    } else {
      // Estamos obteniendo un elemento en particular
      echo json_encode($this->modelo->get($id));
    }
  }
    
}