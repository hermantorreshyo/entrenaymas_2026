<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Provincias extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Provincia_Model', 'modelo');
  }

  public function get_select() {
    $id_pais = parent::get_get("id_pais",0);
    $arr = $this->modelo->get_select(array(
      "id_pais"=>$id_pais
    ));
    echo json_encode(array(
      "results"=>$arr,
      "total"=>sizeof($arr)
    ));
  } 

}