<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Videos extends REST_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('Video_Model', 'modelo');
    }
	
}