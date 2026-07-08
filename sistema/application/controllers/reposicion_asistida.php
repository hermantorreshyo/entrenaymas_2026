<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Reposicion_Asistida extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Reposicion_Asistida_Model', 'modelo');
  }

  function calcular() {
    $id_empresa = 868; //parent::get_empresa();
    $this->modelo->calcular(array(
      "id_empresa"=>$id_empresa,
    ));
  }

  function ver_listado() {
    $id_empresa = parent::get_empresa();
    $id_sucursal = parent::get_get("id_sucursal");
    $salida = $this->modelo->ver_proveedores(array(
      "id_empresa"=>$id_empresa,
      "id_sucursal"=>$id_sucursal,
    ));
    echo json_encode($salida);
  }

  function ver_pedido_sugerido() {
    $id_empresa = parent::get_empresa();
    $id_sucursal = parent::get_get("id_sucursal");
    $id_proveedor = parent::get_get("id_proveedor");
    $salida = $this->modelo->ver_pedido_sugerido(array(
      "id_empresa"=>$id_empresa,
      "id_sucursal"=>$id_sucursal,
      "id_proveedor"=>$id_proveedor,
    ));
    echo json_encode(array(
      "results"=>$salida,
    ));
  }

  function generar_pedido() {
    $id_empresa = parent::get_empresa();
    $id_sucursal = parent::get_post("id_sucursal");
    $id_proveedor = parent::get_post("id_proveedor");
    $items = parent::get_post("items");
    $this->load->model("Pedido_Proveedor_Model");
    $id = $this->Pedido_Proveedor_Model->generar(array(
      "id_sucursal"=>$id_sucursal,
      "id_proveedor"=>$id_proveedor,
      "items"=>$items,
    ));
    echo json_encode(array(
      "id"=>$id,
    ));
  }

}