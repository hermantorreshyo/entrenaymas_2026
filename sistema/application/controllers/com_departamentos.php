<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Com_Departamentos extends REST_Controller {
    
  function __construct() {
    parent::__construct();
    $this->load->model('Com_Departamento_Model', 'modelo');
  }

  public function get_select() {
    $id_provincia = parent::get_get("id_provincia",0);
    $arr = $this->modelo->get_select(array(
      "id_provincia"=>$id_provincia
    ));
    echo json_encode(array(
      "results"=>$arr,
      "total"=>sizeof($arr)
    ));
  }

}