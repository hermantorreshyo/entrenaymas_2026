<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Petips_Foro_Categorias extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Petips_Foro_Categoria_Model', 'modelo');
  }

  // petips_foro_categorias/function/miFuncion/?id=1
  function miFuncion() {
    $offset = parent::get_post("offset");
    $sql = "SELECT * FROM tabla LIMIT 0,$offset ";
    $query = $this->db->query($sql);
    $salida = array();
    foreach($query->result() as $item) {
      $salida[] = $item;
    }
    echo json_encode($salida);
  }

}