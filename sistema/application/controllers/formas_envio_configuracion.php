<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Formas_Envio_Configuracion extends REST_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('Forma_Envio_Configuracion_Model', 'modelo');
    }
}