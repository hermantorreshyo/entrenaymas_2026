<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Categorias_Entrena extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Categorias_Entrena_Model', 'modelo',"id");
  }
    
}