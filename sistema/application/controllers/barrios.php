<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Barrios extends REST_Controller {
  
  function __construct() {
    parent::__construct();
  }

  function buscar_por_localidad() {
    $id_localidad = parent::get_get("id_localidad",0);
    $this->load->model("Localidad_Model");
    $localidad = $this->Localidad_Model->get($id_localidad);
    $sql = "SELECT * FROM com_barrios ";
    if (!empty($localidad)) $sql.= "WHERE id_localidad_inmobusqueda = $localidad->id_localidad_inmobusquedas ";
    $q = $this->db->query($sql);
    $salida = $q->result();
    echo json_encode(array(
      "results"=>$salida,
      "total"=>sizeof($salida),
    ));
  }  

}