<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Tutores extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Tutor_Model', 'modelo');
  }

  function ver() {
    $limit = $this->input->get("limit");
    $offset = $this->input->get("offset");
    $order_by = $this->input->get("order_by");
    $order = $this->input->get("order");
    $filter = $this->input->get("filter");
    if (!empty($order_by) && !empty($order)) $order = $order_by." ".$order;
    else $order = "";
    $conf = array(
      "filter"=>$filter,
      "limit"=>$limit,
      "offset"=>$offset,
      "order"=>$order,
    );
    $r = $this->modelo->buscar($conf);
    echo json_encode($r);
  }
  
  /*
    function get_by_nombre() {
        $nombre = $this->input->get("term");
        $sql = "SELECT * FROM aca_tutores L ";
        $sql.= "WHERE L.nombre LIKE '%$nombre%' ";
        $sql.= "ORDER BY L.nombre ASC ";
        $sql.= "LIMIT 0,20 ";
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

  */
    
}