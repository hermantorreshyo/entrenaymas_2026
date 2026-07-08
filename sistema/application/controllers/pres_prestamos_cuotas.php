<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Pres_Prestamos_Cuotas extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Pres_Prestamo_Cuota_Model', 'modelo');
  }

  function buscar() {
    $filter = ($this->input->get("texto") === FALSE) ? "" : $this->input->get("texto");
    $id_cliente = $this->input->get("id_cliente");
    $id_sucursal = parent::get_get("id_sucursal",0);
    $limit = $this->input->get("limit");
    $offset = $this->input->get("offset");
    $order_by = $this->input->get("order_by");
    $order = $this->input->get("order");
    if (!empty($order_by) && !empty($order)) $order = $order_by." ".$order;
    else $order = "";
    $r = $this->modelo->buscar(array(
      "filter"=>$filter,
      "id_cliente"=>$id_cliente,
      "id_sucursal"=>$id_sucursal,
      "limit"=>$limit,
      "offset"=>$offset,
      "order"=>$order,
    ));
    echo json_encode($r);
  }

  function imprimir($id_cuota,$id_sucursal = 0) {
    $id_empresa = parent::get_empresa();
    $this->load->model("Empresa_Model");
    $empresa = $this->Empresa_Model->get($id_empresa);
    $cuota = $this->modelo->get($id_cuota);

    $sucursal = FALSE;
    if ($id_sucursal != 0) {
      $this->load->model("Almacen_Model");
      $sucursal = $this->Almacen_Model->get($id_sucursal);
    }

    $this->load->model("Pres_Prestamo_Model");
    $prestamo = $this->Pres_Prestamo_Model->get($cuota->id_prestamo);
    $this->load->model("Pres_Cliente_Model");
    $cliente = $this->Pres_Cliente_Model->get($prestamo->id_cliente);
    $datos = array(
      "sucursal"=>$sucursal,
      "cuota"=>$cuota,
      "prestamo"=>$prestamo,
      "cliente"=>$cliente,
      "empresa"=>$empresa,
    );
    $this->load->view("reports/prestamo/pago_cuota.php",$datos);
  }
}