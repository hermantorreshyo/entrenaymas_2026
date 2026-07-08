<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Sindi_Localidades extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Sindi_Localidad_Model', 'modelo');
  }

  function buscar() {
    $sql = "SELECT * ";
    $sql.= "FROM sindi_localidades ";
    $sql.= "ORDER BY orden, nombre ";
    $q = $this->db->query($sql);
    $salida = $q->result();
    echo json_encode(array(
      "results"=>$salida,
      "total"=>sizeof($salida),
    ));
  }
	
	
}