<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Configuracion extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Configuracion_Model', 'modelo');
  }

	function update($id) { exit(); }
	function insert() { exit(); }
	function delete($id) { exit(); }

  function set_tablas_configuracion() {
    $row = new stdClass();
    $row->id_empresa = parent::get_empresa();
    $row->tabla = parent::get_post("tabla");
    $row->configuracion = parent::get_post("configuracion");
    $row->cant_items = parent::get_post("cant_items",10);
    $this->modelo->set_tablas_configuracion($row);
    echo json_encode(array(
      "error"=>0,
    ));
  }
    
}