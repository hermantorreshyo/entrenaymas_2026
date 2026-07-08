<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Centros_Costos extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Centro_Costo_Model', 'modelo');
  }
	
}