<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Notas extends REST_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('Nota_Model', 'modelo');
    }
    
    private function remove_properties($array) {
    }
    
    function update($id) {
        if ($id == 0) { $this->insert(); return; }
        $array = $this->parse_put();
        $id_empresa = parent::get_empresa();
        $array->id_empresa = $id_empresa;        
        $this->remove_properties($array);
        $this->modelo->save($array);
        $salida = array(
            "id"=>$id,
            "error"=>0,
        );
        echo json_encode($salida);        
    }
    
    
    function insert() {
		$array = $this->parse_put();
        $id_empresa = parent::get_empresa();
        $array->id_empresa = $id_empresa;
        $this->remove_properties($array);
        $insert_id = $this->modelo->save($array);
        $salida = array(
            "id"=>$insert_id,
            "error"=>0,
        );
        echo json_encode($salida);        
    }
    
    function ver() {
        // La comision es obligatoria, la materia NO
        // Ya que podemos tomar notas por dia
        $id_comision = $this->input->get("id_comision");
        $id_materia = ($this->input->get("id_materia") !== FALSE) ? $this->input->get("id_materia") : 0;
        $conf = array(
            "id_comision"=>$id_comision,
            "id_materia"=>$id_materia,
        );
        $r = $this->modelo->buscar($conf);
        echo json_encode($r);
    }

    function guardar() {
        $notas = ($this->input->post("notas"));
        // Recorremos los registros de notas
        foreach($notas as $asis) {
            $this->modelo->save_or_update($asis);
        }
        echo json_encode(array("error"=>0));
    }
	
}
