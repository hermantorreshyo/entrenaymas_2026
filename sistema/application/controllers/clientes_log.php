<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Clientes_Log extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Clientes_Log_Model', 'modelo');
  }
  function buscar() {
    $limit = parent::get_get("limit",0);
    $id_cliente = parent::get_get("id_cliente",0);
    $offset = parent::get_get("offset",10);
    $salida = $this->modelo->buscar(array(
      "limit"=>$limit,
      "offset"=>$offset,
      "id_cliente"=>$id_cliente,
    ));
    echo json_encode($salida);
  }
}