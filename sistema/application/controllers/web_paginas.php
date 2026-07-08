<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Web_Paginas extends REST_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('Web_Pagina_Model', 'modelo',"orden ASC",1);
    }
    
    function save_image($dir="",$filename="") {
		$id_empresa = $this->get_empresa();
		$dir = "uploads/$id_empresa/paginas/";
		$filename = $this->input->post("file");
		echo parent::save_image($dir,$filename);
    }    
    
}