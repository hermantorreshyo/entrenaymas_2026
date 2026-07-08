<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Anunciantes extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Anunciantes_Model', 'modelo');
  }

  function save_image($dir="",$filename="") {
    $id_empresa = $this->get_empresa();
    $dir = "uploads/$id_empresa/anunciantes/";
    $filename = $this->input->post("file");
    echo parent::save_image($dir,$filename);
  } 

  function get($id) {

    $id_empresa = parent::get_get("id_empresa", parent::get_empresa());

    if ($id == "index") {

      $limit = parent::get_get("limit", 0);
      $offset = parent::get_get("offset", 10);
      $order = parent::get_get("order", "");

      $output = $this->modelo->buscar(array(
        "id_empresa"=>$id_empresa,
        "limit"=>$limit,
        "offset"=>$offset,
        "order"=>$order,
      ));
      
      echo json_encode($output);
    } else {
      $output = $this->modelo->get($id);
      echo json_encode($output);
    }
  }

	
}