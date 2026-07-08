<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Sindi_Tipos_Bonos extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Sindi_Tipo_Bono_Model', 'modelo');
  }
	
}