<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Subrubros extends REST_Controller
{

    function __construct() {
        parent::__construct();
        $this->load->model('Subrubro_Model', 'modelo','id');
    }
    
    /**
     * @param Int $id ID del RUBRO que se obtienen los subrubros
     */
    function get_by_rubro($id) {
        
        $this->load->model("Subrubro_Model");
        $salida = array();
        if ($id != -1) {
            $subrubros = $this->Subrubro_Model->get_all(null,null,$id);    
        } else {
            $subrubros = array();
        }
        $salida["results"] = $subrubros;
        $salida["total"] = sizeof($subrubros);
        echo json_encode($salida);
    }
    
    
    function get($id) {

        // Obtenemos todos los registros
        if ($id == "index") {
        
            $limit = $this->input->get("limit");
            $offset = $this->input->get("offset");
            $filter = $this->input->get("filter");
            $id_rubro = $this->input->get("id_rubro");
            
            if (!empty($filter) || !empty($id_rubro)) {
                $lista = $this->modelo->find($filter,$id_rubro);
            } else {
                $lista = $this->modelo->get_all($limit,$offset);	
            }
            
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