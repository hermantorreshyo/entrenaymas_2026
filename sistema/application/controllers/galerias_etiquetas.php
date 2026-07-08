<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Galerias_Etiquetas extends REST_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('Galeria_Etiqueta_Model', 'modelo');
    }
	
    function get_by_nombre() {
        $nombre = $this->input->get("term");
        $sql = "SELECT * ";
        $sql.= "FROM galerias_etiquetas ";
        $sql.= "WHERE nombre LIKE '%$nombre%' ";
        $q = $this->db->query($sql);
        $resultado = array();
        foreach($q->result() as $r) {
            $rr = new stdClass();
            $rr->id = $r->id;
            $rr->value = $r->nombre;
            $rr->label = $r->nombre;
            $resultado[] = $rr;
        }
        echo json_encode($resultado);
    }    
    
}