<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Comisiones extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Comision_Model', 'modelo');
  }

  function get($id) {

    // Obtenemos todos los registros
    if ($id == "index") {
      $conf = array();
      $conf["limit"] = $this->input->get("limit");
      $conf["offset"] = $this->input->get("offset");
      $conf["filter"] = $this->input->get("filter");
      $conf["order_by"] = $this->input->get("order_by");
      $conf["order"] = $this->input->get("order");
      $salida = $this->modelo->buscar($conf);
      echo json_encode($salida);
    } else {
      echo json_encode($this->modelo->get($id));
    }
  }
  
  function get_by_nombre() {
    $nombre = $this->input->get("term");
    $sql = "SELECT * FROM aca_comisiones L ";
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