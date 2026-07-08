<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Petips_Ingredientes extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Petips_Ingrediente_Model', 'modelo');
  }
	
}