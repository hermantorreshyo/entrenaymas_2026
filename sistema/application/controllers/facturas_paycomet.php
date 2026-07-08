<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Facturas_Paycomet extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Facturas_Paycomet_Model', 'modelo');
  }

  function get($id) {

    $id_empresa = parent::get_get("id_empresa", parent::get_empresa());

    if ($id == "index") {
      $limit = parent::get_get("limit", 0);
      $offset = parent::get_get("offset", 10);
      $id_usuario = parent::get_get("id_usuario", 0);
      $salida = $this->modelo->buscar(array(
        "limit"=>$limit,
        "offset"=>$offset,
        "id_usuario"=>$id_usuario,
      ));
    } else {
      $salida = $this->modelo->get($id);
    }

    echo json_encode($salida);
  }

  function ver_factura($id) {
    $factura = $this->modelo->get($id);

    $datos = array(
      "factura"=>$factura,
    );

    $this->load->view("reports/factura/entrenaymas/paycomet.php",$datos);
  }
}