<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Tipos_Comprobante extends REST_Controller
{

    function __construct() {
        parent::__construct();
        $this->load->model('Tipo_Comprobante_Model', 'modelo');
    }

}