<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Proyectos extends REST_Controller {

	function __construct() {
		parent::__construct();
		$this->load->model('Proyecto_Model', 'modelo');
	}
	
	public function get_modulos($id_proyecto=0) {
		$this->load->model("Modulo_Model");
		$arr = $this->Modulo_Model->get_by_proyecto($id_proyecto);
		echo json_encode($arr);
	}	

	public function reorder($id_proyecto) {
		$datos = $this->input->post("datos");
		if ($datos === FALSE) return;
		$this->modelo->reorder($id_proyecto,array(
			"id"=>0,
			"children"=>$datos,
		));
		echo json_encode(array("error"=>1));
	}
	
}