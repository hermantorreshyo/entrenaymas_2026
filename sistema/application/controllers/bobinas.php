<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Bobinas extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Bobina_Model', 'modelo');
	}

  function cargar() {
    $listado = json_decode($this->input->post("listado"));
    foreach($listado as $r) {
      $this->modelo->save($r);
    }
    echo json_encode(array(
      "error"=>0,
    ));
  }

  function buscar() {
    $salida = $this->modelo->get_list(array(
      "filter"=>$this->get_get("filter"),
      "limit"=>$this->get_get("limit"),
      "offset"=>$this->get_get("offset"),
      "order_by"=>$this->get_get("order_by"),
      "order"=>$this->get_get("order"),
    ));
    echo json_encode($salida);
  }   
    
}