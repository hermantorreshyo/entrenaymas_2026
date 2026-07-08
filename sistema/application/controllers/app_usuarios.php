<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class App_Usuarios extends REST_Controller {

  function __construct() {
    parent::__construct();
  }

  function registrar() {
    $id_empresa = parent::get_post("id_empresa",0);
    $device = parent::get_post("device","");
    $sql = "INSERT INTO app_usuarios (id_empresa,device) VALUES ('$id_empresa','$device') ";
    $this->db->query($sql);
    echo json_encode(array(
      "error"=>0,
    ));
  }
	
}