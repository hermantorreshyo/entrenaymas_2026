<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Andromeda_Prescriptores extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Andromeda_Prescriptor_Model', 'modelo');
  }
	
}