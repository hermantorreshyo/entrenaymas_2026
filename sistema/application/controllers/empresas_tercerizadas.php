<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Empresas_Tercerizadas extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Empresa_Tercerizada_Model', 'modelo');
  }

}