<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Medios_Pago_Configuracion extends REST_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('Medio_Pago_Configuracion_Model', 'modelo');
    }
}