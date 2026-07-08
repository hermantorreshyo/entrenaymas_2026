<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Carp_Postulantes extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Carp_Chofer_Model', 'modelo');
  }

  function contratar() {
    $id_empresa = parent::get_empresa();
    $id = parent::get_post("id",0);
    $sql = "UPDATE carp_choferes SET bolsa_trabajo = 0 ";
    $sql.= "WHERE id_usuario = $id AND id_empresa = $id_empresa ";
    $this->db->query($sql);
    echo json_encode(array("error"=>0));
  }

  function get($id) {

    if ($id == "index") {
      $conf = array();
      $order_by = $this->input->get("order_by");
      $order = $this->input->get("order");
      if ($order_by !== FALSE) $conf["order"] = $order_by." ".$order;
      $conf["limit"] = parent::get_get("limit",0);
      $conf["id_agencia"] = parent::get_get("id_agencia",0);
      $conf["id_propietario"] = parent::get_get("id_propietario",0);
      $conf["offset"] = parent::get_get("offset",30);
      $conf["filter"] = parent::get_get("filter","");
      $conf["estado"] = parent::get_get("estado","");
      $conf["id_empresa"] = parent::get_get("id_empresa",parent::get_empresa());
      $conf["bolsa_trabajo"] = 1;
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