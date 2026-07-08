<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Galerias_Categorias extends REST_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('Galeria_Categoria_Model', 'modelo');
    }

    public function reorder() {
        $datos = $this->input->post("datos");
        if ($datos === FALSE) return;
        $this->modelo->reorder(array(
            "id"=>0,
            "children"=>$datos,
        ));
        echo json_encode(array("error"=>1));
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
    
    function get_by_nombre() {
        $nombre = $this->input->get("term");
        $sql = "SELECT * ";
        $sql.= "FROM galerias_categorias ";
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