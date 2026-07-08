<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Sindi_Estudios_Contables extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Sindi_Estudio_Contable_Model', 'modelo');
  }
  
  function buscar() {
    $id_empresa = parent::get_empresa();
    $codigo = parent::get_get("codigo","");
    $filter = parent::get_get("filter","");
    $offset = parent::get_get("offset",10);
    $limit = parent::get_get("limit",0);
    $s = $this->modelo->buscar(array(
      "id_empresa"=>$id_empresa,
      "codigo"=>$codigo,
      "filter"=>$filter,
      "offset"=>$offset,
      "limit"=>$limit,
    ));
    echo json_encode($s);
  }
  
}