<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Pres_Documentaciones extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Pres_Documentacion_Model', 'modelo');
  }
	
  function get_by_nombre() {
		$id_empresa = parent::get_empresa();
    $nombre = $this->input->get("term");
    $sql = "SELECT * ";
    $sql.= "FROM pres_documentacion ";
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