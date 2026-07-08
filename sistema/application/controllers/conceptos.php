<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Conceptos extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Concepto_Model', 'modelo');
  }

  public function get_arbol() {
    $totaliza_en = parent::get_get("totaliza_en","");
    $arr = $this->modelo->get_arbol(0,array(
      "totaliza_en"=>$totaliza_en,
    ));
    echo json_encode($arr);
  }
  
  function unique_find_by_codigo($codigo) {
    $id_empresa = parent::get_empresa();
    $this->db->where(array("codigo"=>$codigo,"id_empresa"=>$id_empresa));
    $query = $this->db->get("tipos_gastos");
    if ($query->num_rows($query)>0) {
      $row = $query->row();
      echo json_encode(array("results"=>array($row),"total"=>1));
    } else {
      echo json_encode(array("results"=>array(),"total"=>0));
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

  
}