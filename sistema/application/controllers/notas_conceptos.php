<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Notas_Conceptos extends REST_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('Nota_Concepto_Model', 'modelo',"id DESC",1);
    }
}