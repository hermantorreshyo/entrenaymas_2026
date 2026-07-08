<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Tipos_Alicuotas_Iva extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Tipo_Alicuota_Iva_Model', 'modelo');
  }

  function buscar() {
    $order = parent::get_get("order","nombre");
    $order_by = parent::get_get("order_by","asc");
    if (empty($order)) $order = "nombre";
    if (empty($order_by)) $order_by = "asc";

    $salida = $this->modelo->buscar(array(
      "filter"=>parent::get_get("filter"),
      "limit"=>parent::get_get("limit",0),
      "offset"=>parent::get_get("offset",10),
      "order_by"=>$order." ".$order_by,
    ));
    echo json_encode(array(
      "results"=>$salida,
      "total"=>sizeof($salida),
    ));
  }

}