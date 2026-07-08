<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Carp_Agencias extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Carp_Agencia_Model', 'modelo');
  }

  function get($id) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    if ($id == "index") {
      $conf = array();
      $order_by = $this->input->get("order_by");
      $order = $this->input->get("order");
      if ($order_by !== FALSE) $conf["order"] = $order_by." ".$order;
      $conf["limit"] = parent::get_get("limit",0);
      $conf["id_agencia"] = parent::get_get("id_agencia",0);
      $conf["offset"] = parent::get_get("offset",10);
      $conf["filter"] = parent::get_get("filter","");
      $conf["id_empresa"] = parent::get_get("id_empresa",parent::get_empresa());
      $lista = $this->modelo->buscar($conf);
      $total = $this->modelo->get_total_results();
      $salida = array(
        "total"=> $total,
        "results"=>$lista
      );
      echo json_encode($salida);
    }  else {
      // Estamos obteniendo un elemento en particular
      echo json_encode($this->modelo->get($id));
    }
  }    
    
  function save_file() {
    $this->load->helper("file_helper");
    $id_empresa = $this->get_empresa();
    if (!isset($_FILES['path']) || empty($_FILES['path'])) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"No se ha enviado ningun archivo."
      ));
      return;
    }
    $filename = filename($_FILES["path"]["name"],"-");
    $path = "uploads/$id_empresa/$filename";
    @move_uploaded_file($_FILES["path"]["tmp_name"],$path);
    echo json_encode(array(
      "path"=>$path,
      "error"=>0,
    ));
  }
    
  function save_image($dir="",$filename="") {
		$id_empresa = $this->get_empresa();
		$dir = "uploads/$id_empresa/";
		$filename = $this->input->post("file");
		echo parent::save_image($dir,$filename);
  }    

}