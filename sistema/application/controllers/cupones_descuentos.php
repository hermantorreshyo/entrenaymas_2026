<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Cupones_Descuentos extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Cupon_Descuento_Model', 'modelo');
  }

  function get($id) {
    if ($id == "index") {
      $limit = parent::get_get("limit",0);
      $offset = parent::get_get("offset",10);
      $filter = parent::get_get("filter","");
      $order_by = parent::get_get("order_by","C.id");
      $order = parent::get_get("order","DESC");
      $salida = $this->modelo->buscar(array(
        "limit"=>$limit,
        "offset"=>$offset,
        "filter"=>$filter,
        "order_by"=>$order_by,
        "order"=>$order,
      ));
      echo json_encode($salida);
    } else {
      echo json_encode($this->modelo->get($id));
    }
  }  	
}