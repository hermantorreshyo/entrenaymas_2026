<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Articulos_Propiedades extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Articulo_Propiedad_Model', 'modelo');
  }

  function get_by_articulo($id_articulo) {
    $salida = $this->modelo->get_by_articulo($id_articulo);
    echo json_encode(array(
      "results"=>$salida,
    ));
  }

  function eliminar() {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    $id = parent::get_post("id");
    $id_empresa = parent::get_empresa();
    $sql = "SELECT * FROM articulos_propiedades_opciones WHERE id_empresa = $id_empresa AND id_propiedad = $id ";
    $q = $this->db->query($sql);
    if ($q->num_rows() > 0) {
      echo json_encode(array("error"=>1,"mensaje"=>"ERROR: No se puede eliminar una propiedad cuando hay productos que tienen asignadas variantes."));
      exit();
    }
    $sql = "DELETE FROM articulos_propiedades WHERE id_empresa = $id_empresa AND id = $id ";
    $this->db->query($sql);
    echo json_encode(array("error"=>0));
  }

  function update($id) {
    
    if ($id == 0) { $this->insert(); return; }
    $array = $this->parse_put();
    $opciones = $array->opciones;
    unset($array->opciones);
    $this->modelo->save($array);
    
    // Guardamos las relaciones con las opciones (Y se crean en caso de que no exitan)
    foreach($opciones as $e) {
      $tag = new stdClass();
      $tag->id_empresa = $array->id_empresa;
      $tag->id_propiedad = $id;
      $tag->nombre = $e;
      $this->modelo->save_opcion($tag);
    }    
    
    $salida = array(
      "id"=>$id,
      "error"=>0,
    );
    echo json_encode($salida);    
  }
  
  function insert() {
    
    $this->load->helper("file_helper");
  	$array = $this->parse_put();
    $opciones = $array->opciones;
    unset($array->opciones);
    $insert_id = $this->modelo->save($array);

    foreach($opciones as $e) {
      $tag = new stdClass();
      $tag->id_empresa = $array->id_empresa;
      $tag->id_propiedad = $insert_id;
      $tag->nombre = $e;
      $this->modelo->save_opcion($tag);
    }
    
    $salida = array(
      "id"=>$insert_id,
      "error"=>0,
    );
    echo json_encode($salida);    
  }
	
}