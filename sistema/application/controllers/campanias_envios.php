<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Campanias_Envios extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Campania_Envio_Model', 'modelo');
  }
  
  function test() {
    $dest = $this->input->post("destinatarios");
    $operacion = $this->input->post("operacion");
    $filtro = $this->input->post("filtro");
    $valores = $this->input->post("valores");
    $r = $this->modelo->ver(array(
      "destinatarios"=>$dest,
      "operacion"=>$operacion,
      "filtro"=>$filtro,
      "valores"=>$valores,
    ));
    echo json_encode(array("results"=>$r));
  }
}