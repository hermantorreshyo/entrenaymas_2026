<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Web_Categorias extends REST_Controller
{

    function __construct() {
        parent::__construct();
        $this->load->model('Web_Categoria_Model', 'modelo');
    }
    
    public function get_arbol() {
        $arr = $this->modelo->get_arbol();
        echo json_encode($arr);
    }
    
    public function get_select() {
        $arr = $this->modelo->get_select();
        echo json_encode(array(
            "results"=>$arr,
            "total"=>sizeof($arr)
        ));
    }    

}