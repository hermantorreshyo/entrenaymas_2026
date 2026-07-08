<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Sindi_Tipos_Practicas extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Sindi_Tipo_Practica_Model', 'modelo');
  }
	
}