<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Consultas_Tipos extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Consulta_Tipo_Model', 'modelo');
  }

  function calcular() {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    $sql = "SELECT * FROM empresas";
    $q = $this->db->query($sql);
    $salida = "";
    foreach($q->result() as $r) {
      $salida.= $this->modelo->crear_por_defecto(array(
        "id_empresa"=>$r->id,
        "imprimir"=>1,
      ));
    }
    echo $salida;
  }

  public function reorder() {
    $id_empresa = parent::get_empresa();
    $datos = $this->input->post("datos");
    if ($datos === FALSE) return;
    $this->modelo->reorder(array(
      "id"=>0,
      "children"=>$datos,
    ));
    echo json_encode(array("error"=>1));
  }
  
  function get_by_nombre() {
    $id_empresa = parent::get_empresa();
    $nombre = $this->input->get("term");
    $sql = "SELECT * ";
    $sql.= "FROM crm_consultas_tipos ";
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