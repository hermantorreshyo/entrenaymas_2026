<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Chat_Preguntas extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Chat_Pregunta_Model', 'modelo');
  }
	
}