<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Reservas_Asientos extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Reserva_Asiento_Model', 'modelo',"id DESC",1);
  }

  function buscar($min=0) {
    $filter = ($this->input->get("filter") === FALSE) ? "" : $this->input->get("filter");
    $id_usuario = ($this->input->get("id_usuario") === FALSE) ? 0 : $this->input->get("id_usuario");
    $limit = $this->input->get("limit");
    $offset = $this->input->get("offset");
    $order_by = $this->input->get("order_by");
    $order = $this->input->get("order");
    if (!empty($order_by) && !empty($order)) $order = $order_by." ".$order;
    else $order = "";
    $r = $this->modelo->get_all(array(
      "filter"=>$filter,
      "id_usuario"=>$id_usuario,
      "limit"=>$limit,
      "offset"=>$offset,
      "order"=>$order,
    ));
    $total = $this->modelo->get_total_results();
    echo json_encode(array(
      "results"=>$r,
      "total"=>$total,
    ));
  }

  function get_asientos() {
    $filter = ($this->input->post("filter") === FALSE) ? "" : $this->input->post("filter");
    $id_viaje = ($this->input->post("id_viaje") === FALSE) ? 0 : $this->input->post("id_viaje");
    $id_vehiculo = ($this->input->post("id_vehiculo") === FALSE) ? 0 : $this->input->post("id_vehiculo");
    $r = $this->modelo->get_asientos(array(
      "filter"=>$filter,
      "id_viaje"=>$id_viaje,
      "id_vehiculo"=>$id_vehiculo,
    ));
    echo json_encode(array(
      "results"=>$r,
    ));
  }

  function actualizar_asientos($min=0) {
    $asientos = ($this->input->post("asientos") === FALSE) ? array() : json_decode($this->input->post("asientos"));
    $r = $this->modelo->actualizar_asientos(array(
      "asientos"=>$asientos,
    ));
    echo json_encode(array(
      "error"=>0,
    ));
  }

  function asignar_habitaciones() {
    $id_empresa = parent::get_empresa();
    $ids = parent::get_post("ids","");
    $ids = str_replace("-", ",", $ids);
    $tipo_habitacion = parent::get_post("tipo_habitacion",0);
    $numero_habitacion = parent::get_post("numero_habitacion","");
    $hotel = parent::get_post("hotel","");
    $sql = "UPDATE via_reservas_asientos SET ";
    $sql.= " tipo_habitacion = '$tipo_habitacion', ";
    $sql.= " numero_habitacion = '$numero_habitacion', ";
    $sql.= " hotel = '$hotel' ";
    $sql.= "WHERE id_empresa = $id_empresa AND id IN ($ids) ";
    $this->db->query($sql);
    echo json_encode(array(
      "error"=>0,
    ));
  }

  function voucher($id) {
    $this->load->helper("fecha_helper");

    $this->load->model("Empresa_Model");
    if (isset($_SESSION["id_empresa"])) {
      $id_empresa = $_SESSION["id_empresa"];
      $empresa = $this->Empresa_Model->get($id_empresa);
    } else {
      $dominio = strtolower($_SERVER["HTTP_HOST"]);
      $empresa = $this->Empresa_Model->get_empresa_by_dominio($dominio);
      $id_empresa = $empresa->id;      
    }

    $reserva = $this->modelo->get($id,array(
      "id_empresa"=>$id_empresa
    ));
    
    $empresa = $this->Empresa_Model->get($id_empresa);

    $this->load->model("Web_Configuracion_Model");
    $web_conf = $this->Web_Configuracion_Model->get($empresa->id);
    $empresa = (object) array_merge((array) $empresa, (array) $web_conf);

    $header = $this->load->view("reports/viajes/header",null,true);
    $datos = array(
      "reserva"=>$reserva,
      "empresa"=>$empresa,
      "header"=>$header,
    );
    $this->load->view("reports/viajes/modelo1/voucher.php",$datos);
  }

}