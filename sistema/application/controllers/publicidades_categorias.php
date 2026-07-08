<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Publicidades_Categorias extends REST_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('Publicidad_Categoria_Model', 'modelo');
    }
    
}