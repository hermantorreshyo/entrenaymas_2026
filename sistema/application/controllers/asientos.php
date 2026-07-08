<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Asientos extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Asiento_Model', 'modelo');
  }

  function ver() {
    $piso = $this->input->post("piso");
    $id_vehiculo = $this->input->post("id_vehiculo");
    $id_viaje = ($this->input->post("id_viaje") !== FALSE) ? $this->input->post("id_viaje") : 0;
    $filter = ($this->input->post("filter") !== FALSE) ? $this->input->post("filter") : "";
    $array = $this->modelo->ver(array(
      "piso"=>$piso,
      "id_vehiculo"=>$id_vehiculo,
      "id_viaje"=>$id_viaje,
      "filter"=>$filter,
    ));
    echo json_encode($array);
  }

}