<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Chequeras extends REST_Controller
{

    function __construct() {
        parent::__construct();
        $this->load->model('Chequera_Model', 'modelo','id');
    }
    
    
    function get($id) {

        // Obtenemos todos los registros
        if ($id == "index") {
        
            $limit = $this->input->get("limit");
            $offset = $this->input->get("offset");
            $filter = $this->input->get("filter");
            $id_banco = ($this->input->get("id_banco") == false) ? -1 : $this->input->get("id_banco");
            
            $lista = $this->modelo->get_all($limit,$offset,$id_banco);
            if (!$lista) $lista = array();
    
            // Total de lista
            $total = $this->modelo->count_all();
    
            // Armamos la salida
            $salida = array(
                "total"=> $total,
                "results"=>$lista
            );
            echo json_encode($salida);
        } 
        else 
        {
            // Estamos obteniendo un elemento en particular
            echo json_encode($this->modelo->get($id));
        }

    }   
    
}