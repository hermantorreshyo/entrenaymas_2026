<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Docentes extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Docente_Model', 'modelo');
  }

  function save_image($dir="",$filename="") {
    $id_empresa = $this->get_empresa();
    $dir = "uploads/$id_empresa/alumnos/";
    $filename = $this->input->post("file");
    echo parent::save_image($dir,$filename);
  } 
  
  function get_by_nombre() {
    $nombre = $this->input->get("term");
    $sql = "SELECT * FROM aca_docentes L ";
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

  function ver() {
    $limit = $this->input->get("limit");
    $offset = $this->input->get("offset");
    $order_by = $this->input->get("order_by");
    $order = $this->input->get("order");
    $filter = $this->input->get("filter");
    $id_departamento = $this->input->get("id_departamento");
    if (!empty($order_by) && !empty($order)) $order = $order_by." ".$order;
    else $order = "";
    $conf = array(
      "filter"=>$filter,
      "id_departamento"=>$id_departamento,
      "limit"=>$limit,
      "offset"=>$offset,
      "order"=>$order,
    );
    $r = $this->modelo->buscar($conf);
    echo json_encode($r);
  }

  
  function registro() {
      
      $obj = new stdClass();
      $obj->id_empresa = $this->input->post("id_empresa");
      if ($obj->id_empresa === FALSE) {
          echo json_encode(array(
              "error"=>1,
              "mensaje"=>"ERROR: id_empresa no definida",
          ));
          return;
      }
      $obj->nombre = $this->input->post("nombre");
      if ($obj->nombre === FALSE) $obj->nombre = "";
      $obj->apellido = $this->input->post("apellido");
      if ($obj->apellido === FALSE) $obj->apellido = "";
      $obj->email = $this->input->post("email");
      if ($obj->email === FALSE) $obj->email = "";
      $obj->id_departamento = $this->input->post("id_departamento");
      if ($obj->id_departamento === FALSE) $obj->id_departamento = 0;
      $obj->password = $this->input->post("password");
      if ($obj->password === FALSE) $obj->password = "";
      $obj->dni = $this->input->post("dni");
      if ($obj->dni === FALSE) $obj->dni = "";
      
      // Guardamos los datos
      $this->modelo->insert($obj);
      echo json_encode(array(
          "error"=>0,
      ));
  }
  
}