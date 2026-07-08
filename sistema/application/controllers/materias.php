<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Materias extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Materia_Model', 'modelo');
  }

  public function get_select() {
    $id_carrera = ($this->input->get("id_carrera") === FALSE) ? 0 : $this->input->get("id_carrera");
    $anio = ($this->input->get("anio") === FALSE) ? 0 : $this->input->get("anio");
    $cuatrimestre = ($this->input->get("cuatrimestre") === FALSE) ? 0 : $this->input->get("cuatrimestre");
    $arr = $this->modelo->get_all(array(
      "id_carrera"=>$id_carrera,
      "anio"=>$anio,
      "cuatrimestre"=>$cuatrimestre,
      "order_by"=>"nombre ASC",
    ));
    echo json_encode(array(
      "results"=>$arr,
      "total"=>sizeof($arr)
    ));
  }
    
  function get_by_nombre() {
    $id_empresa = parent::get_empresa();
    $nombre = $this->input->get("term");
    $sql = "SELECT * ";
    $sql.= "FROM aca_materias ";
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