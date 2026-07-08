<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Toque extends REST_Controller {

  function __construct() {
    parent::__construct();
  }

  function get_categorias_select($id_padre = 0,$separador = "",$id_empresa) {
    $result = array();
    $q = $this->db->query("SELECT * FROM toque_categorias WHERE id_padre = $id_padre AND id_empresa = $id_empresa ORDER BY id ASC");
    foreach($q->result() as $row) {
      $e = new stdClass();
      $e->id = $row->id;
      $e->id_padre = $id_padre;
      $e->nombre = $separador.$row->nombre;
      $result[] = $e;
      $hijos = $this->get_categorias_select($row->id,$separador."&nbsp;&nbsp;&nbsp;",$id_empresa);
      $result = array_merge($result,$hijos);
    }
    return $result;
  }

  function get_categorias() {
    $id_empresa = parent::get_get("id_empresa",parent::get_empresa());
    $arr = $this->get_categorias_select(0,"",$id_empresa);
    echo json_encode(array(
      "results"=>$arr,
      "total"=>sizeof($arr)
    ));
  }

}