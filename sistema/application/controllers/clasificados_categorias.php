<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Clasificados_Categorias extends REST_Controller
{

    function __construct() {
        parent::__construct();
        $this->load->model('Clasificado_Categoria_Model', 'modelo');
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
		$id_empresa = parent::get_empresa();
        $nombre = $this->input->get("term");
        $sql = "SELECT * ";
        $sql.= "FROM clasificados_categorias ";
        $sql.= "WHERE nombre LIKE '%$nombre%' ";
		$sql.= "AND id_empresa = $id_empresa ";
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