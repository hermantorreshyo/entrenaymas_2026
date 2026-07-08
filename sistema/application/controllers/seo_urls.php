<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Seo_Urls extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Seo_Url_Model', 'modelo');
  }

  function buscar($min=0) {
    $id_empresa = parent::get_empresa();
    $filter = ($this->input->get("filter") === FALSE) ? "" : $this->input->get("filter");
    $limit = $this->input->get("limit");
    $offset = $this->input->get("offset");
    $order_by = $this->input->get("order_by");
    $order = $this->input->get("order");
    if (!empty($order_by) && !empty($order)) $order = $order_by." ".$order;
    else $order = "";
    $r = $this->modelo->buscar(array(
      "filter"=>$filter,
      "limit"=>$limit,
      "offset"=>$offset,
      "order"=>$order,
    ));
    echo json_encode($r);
  }

  function duplicar($id) {
    $viaje = $this->modelo->get($id);
    if ($viaje === FALSE) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"No se encuentra el viaje con ID: $id",
      ));
      return;
    }
    $viaje->id = 0;
    $insert_id = $this->modelo->save($viaje);
    echo json_encode(array(
      "id"=>$insert_id
    ));
  }

}