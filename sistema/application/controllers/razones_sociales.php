<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Razones_Sociales extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Razon_Social_Model', 'modelo');
  }
	
}