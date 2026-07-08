<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Farmacias_Turnos extends REST_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('Farmacia_Turno_Model', 'modelo',"fecha ASC",1);
    }
    
    function guardia() {
        $conf = array();
        $conf["id_empresa"] = parent::get_empresa();
        $desde = $this->input->get("start");
        $conf["desde"] = $desde;
        //if ($desde !== FALSE) $conf["desde"] = date("Y-m-d",$desde);
        $hasta = $this->input->get("end");
        $conf["hasta"] = $hasta;
        //if ($hasta !== FALSE) $conf["hasta"] = date("Y-m-d",$hasta-1000);
        $salida = $this->modelo->guardia($conf);
        echo json_encode($salida);
    }    
    
}