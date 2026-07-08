<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Entradas_Etiquetas extends REST_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('Entrada_Etiqueta_Model', 'modelo');
    }

    // Procesa todas las etiquetas y vuelve a generar los links
    function relink() {
        $this->load->helper("file_helper");
        $sql = "SELECT * FROM not_etiquetas ";
        $q = $this->db->query($sql);
        foreach($q->result() as $r) {
            $link = filename($r->nombre,"-",0);
            $sql = "UPDATE not_etiquetas SET link = '$link' WHERE id = $r->id AND id_empresa = $r->id_empresa ";
            $this->db->query($sql);
        }
        echo "TERMINO";
    }
	
  function get_by_nombre() {
    $id_empresa = parent::get_empresa();
    $nombre = $this->input->get("term");
    $sql = "SELECT * ";
    $sql.= "FROM not_etiquetas ";
    $sql.= "WHERE nombre LIKE '%$nombre%' ";
    $sql.= "AND id_empresa = $id_empresa ";
    $q = $this->db->query($sql);
    $resultado = array();
    foreach($q->result() as $r) {
      $rr = new stdClass();
      $rr->id = $r->nombre;
      $rr->text = $r->nombre;
      $resultado[] = $rr;
    }
    echo json_encode($resultado);
  }    
    
}