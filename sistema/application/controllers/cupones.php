<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Cupones extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Cupones_Model', 'modelo');
  }

  function verificar_cupon() {
    $codigo = parent::get_post("codigo", "");
    $id_empresa = parent::get_post("id_empresa", parent::get_empresa());

    if (empty($codigo)) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"Cupon vacio",
      ));
      exit();
    } 

    $output = $this->modelo->verificar_cupon($codigo, array(
      "id_empresa"=>$id_empresa,
    ));
    
    if ($output !== FALSE) {

      $sql = "SELECT * FROM cupones WHERE codigo = '$codigo' ";
      $q = $this->db->query($sql);
      $cupon_dto = $q->row();
      
      echo json_encode(array(
        "error"=>0,
        "mensaje"=>"¡Cupon valido!",
        "descuento"=>$output,
        "cupon"=>$cupon_dto->codigo,
      ));
      exit();     
    } else {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"Cupon no valido.",
      ));
      exit();         
    }
  }

  function get($id) {
    if ($id == "index") {
      $limit = parent::get_get("limit",0);
      $offset = parent::get_get("offset",10);
      $filter = parent::get_get("filter","");
      $order_by = parent::get_get("order_by","C.id");
      $order = parent::get_get("order","DESC");
      $salida = $this->modelo->buscar(array(
        "limit"=>$limit,
        "offset"=>$offset,
        "filter"=>$filter,
        "order_by"=>$order_by,
        "order"=>$order,
      ));
      echo json_encode($salida);
    } else {
      echo json_encode($this->modelo->get($id));
    }
  }  	
}