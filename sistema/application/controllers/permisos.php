<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Permisos extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model("Permiso_Model");
  }  

  public function get_permisos_by_perfil($perfil,$id_proyecto) {
    $arr = $this->Permiso_Model->get_permisos(array(
      "id_perfil"=>$perfil,
      "id_proyecto"=>$id_proyecto,
    ));
    echo json_encode($arr);
  }

}