<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Categorias_Opcionales extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Categoria_Opcional_Model', 'modelo');
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
  
}