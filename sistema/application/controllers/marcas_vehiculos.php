<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Marcas_Vehiculos extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Marca_Vehiculo_Model', 'modelo');
  }

  function get_marcas_by_tipo() {
    header('Access-Control-Allow-Origin: *');
    $id_tipo = parent::get_post("id_tipo",0);
    $id_empresa = parent::get_post("id_empresa",0);
    $sql = "SELECT MV.* FROM marcas_vehiculos MV ";
    $sql.= "WHERE MV.id_empresa = $id_empresa ";
    $sql.= "AND EXISTS(SELECT * FROM veh_autos A WHERE A.id_empresa = MV.id_empresa AND A.id_marca = MV.id ";
    if ($id_tipo != 0 && $id_tipo != 99) $sql.= "AND A.id_tipo = $id_tipo ";
    $sql.= ") ";
    $sql.= "ORDER BY MV.orden ASC, MV.nombre ASC ";
    $q = $this->db->query($sql);
    echo json_encode(array(
      "results"=>$q->result(),
    ));
  }

  function save_image($dir="",$filename="") {
    $id_empresa = $this->get_empresa();
    $dir = "uploads/$id_empresa/marcas/";
    $filename = $this->input->post("file");
    echo parent::save_image($dir,$filename);
  }	

  function get_by_nombre() {
    $id_empresa = parent::get_empresa();
    $nombre = $this->input->get("term");
    $sql = "SELECT * ";
    $sql.= "FROM marcas_vehiculos ";
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

  function get_modelos() {
    $id_empresa = $this->input->post("id_empresa");
    $id_marca = $this->input->post("id_marca");
    $sql = "SELECT DISTINCT modelo ";
    $sql.= "FROM articulos_marcas_vehiculos ";
    $sql.= "WHERE id_empresa = $id_empresa ";
    $sql.= "AND id_marca_vehiculo = $id_marca ";
    $q = $this->db->query($sql);
    $resultado = array();
    foreach($q->result() as $r) {
      $resultado[] = $r->modelo;
    }
    echo json_encode($resultado);
  }


}