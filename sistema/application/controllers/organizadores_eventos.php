<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Organizadores_Eventos extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Organizador_Evento_Model', 'modelo');
  }
	
  function save_image($dir="",$filename="") {
		$id_empresa = $this->get_empresa();
		$dir = "uploads/$id_empresa/entradas/";
		$filename = $this->input->post("file");
		echo parent::save_image($dir,$filename);
  }	
	
  function get_by_nombre() {
		$id_empresa = parent::get_empresa();
    $nombre = $this->input->get("term");
    $sql = "SELECT * ";
    $sql.= "FROM organizadores_eventos ";
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