<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Recorridos_Clientes extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Recorrido_Cliente_Model', 'modelo');
  }

  function get($id) {

    // Obtenemos todos los registros
    if ($id == "index") {
      $conf = array();
      $conf["limit"] = $this->input->get("limit");
      $conf["offset"] = $this->input->get("offset");
      $conf["filter"] = $this->input->get("filter");
      $conf["order_by"] = $this->input->get("order_by");
      $conf["order"] = $this->input->get("order");
      $salida = $this->modelo->buscar($conf);
      echo json_encode($salida);
    } else {
      echo json_encode($this->modelo->get($id));
    }
  }

  function imprimir($id) {

    $recorrido = $this->modelo->get($id);

    $id_empresa = parent::get_empresa();
    $this->load->model("Empresa_Model");
    $empresa = $this->Empresa_Model->get($id_empresa);

    $header = $this->load->view("reports/pedido/header",null,true);

    $datos = array(
      "header"=>$header,
      "recorrido"=>$recorrido,
      "empresa"=>$empresa,
    );
    $this->load->view("reports/recorrido.php",$datos);
  }
  
  /*
  function get_by_nombre() {
    $nombre = $this->input->get("term");
    $sql = "SELECT * FROM aca_comisiones L ";
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