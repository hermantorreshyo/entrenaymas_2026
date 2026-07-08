<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Departamentos extends REST_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('Departamento_Model', 'modelo');
    }
    
    function get_by_nombre() {
        $nombre = $this->input->get("term");
        $sql = "SELECT * FROM aca_docentes L ";
        $sql.= "WHERE L.nombre LIKE '%$nombre%' ";
        $sql.= "ORDER BY L.nombre ASC ";
        $sql.= "LIMIT 0,20 ";
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