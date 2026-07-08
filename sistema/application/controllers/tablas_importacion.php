<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Tablas_Importacion extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Tabla_Importacion_Model', 'modelo');
  }
	
}