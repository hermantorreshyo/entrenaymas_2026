<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Petips_Tamanios_Animales extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Petips_Tamanio_Animal_Model', 'modelo');
  }
	
}