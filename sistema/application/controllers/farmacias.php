<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Farmacias extends REST_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('Farmacia_Model', 'modelo',"nombre ASC",1);
    }    
    
}