<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Eventos extends REST_Controller {
	
    function __construct() {
        parent::__construct();
        $this->load->model('Evento_Model', 'modelo');
    }
	
    function consulta() {
		
		$id_empresa = parent::get_empresa();
		$desde = $this->input->get("start");
		$hasta = $this->input->get("end");
		$id_usuario = $this->input->get("id_usuario");
		
		$salida = array();
		$e = new stdClass();
		$e->id = $m->id;
		$e->title = $m->nombre;
		$e->allDay = true;
		$e->start = $m->start;
		$e->end = $m->end;
		$e->editable = true;
		$e->fecha_fin = $m->hasta;
		$e->color = $m->color;
		$e->textColor = "#FFFFFF";
		$salida[] = $e;
		
		echo json_encode($salida);
    }
    
    
}