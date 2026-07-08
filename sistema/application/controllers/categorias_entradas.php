<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Categorias_Entradas extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Categoria_Entrada_Model', 'modelo');
  }

  // Recalcula todos los links de las categorias
  function recalcular_full_link() {
    $id_empresa = parent::get_empresa();
    $q = $this->db->query("SELECT * FROM not_categorias WHERE id_empresa = $id_empresa");
    foreach($q->result() as $r) {
      $full_link = $this->modelo->full_link($r->id,array(
        "id_empresa"=>$r->id_empresa,
      ));
      $this->db->query("UPDATE not_categorias SET full_link = '".$full_link["full_link"]."' WHERE id = $r->id AND id_empresa = $r->id_empresa ");
    }
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

  public function get_arbol_milling() {
    header('Access-Control-Allow-Origin: *');
    $arr = $this->modelo->get_arbol(array(
      "id_empresa"=>256,
      "not_ids"=>"340,216,278,378,344,517,345"
    ));
    echo json_encode($arr);
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
    $sql.= "FROM entradas_categorias ";
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