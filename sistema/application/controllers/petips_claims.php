<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Petips_Claims extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Petips_Claim_Model', 'modelo');
  }
	
}