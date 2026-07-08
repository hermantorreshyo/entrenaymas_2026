<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Petips_Tipos_Alimentos extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Petips_Tipo_Alimento_Model', 'modelo');
  }
	
}