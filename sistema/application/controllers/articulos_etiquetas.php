<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Articulos_Etiquetas extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Articulo_Etiqueta_Model', 'modelo');
  }

  function acomodar_link() {
    $this->load->helper("file_helper");
    $q = $this->db->query("SELECT * FROM articulos_etiquetas WHERE id_empresa = 900");
    foreach($q->result() as $row) {
      $row->nombre = trim($row->nombre);
      $row->nombre = str_replace("/", "-", $row->nombre);
      $link = filename($row->nombre,"-",0);
      $this->db->query("UPDATE articulos_etiquetas SET link = '$link' WHERE id = $row->id AND id_empresa = $row->id_empresa ");
    }
    echo "TERMINO";
  }
	
  function get_by_nombre() {
    $id_empresa = parent::get_empresa();
    $nombre = $this->input->get("term");
    $sql = "SELECT * ";
    $sql.= "FROM articulos_etiquetas ";
    $sql.= "WHERE nombre LIKE '%$nombre%' ";
    $sql.= "AND id_empresa = $id_empresa ";
    $q = $this->db->query($sql);
    $resultado = array();
    foreach($q->result() as $r) {
      $rr = new stdClass();
      $rr->id = $r->nombre;
      $rr->text = $r->nombre;
      $resultado[] = $rr;
    }
    echo json_encode($resultado);
  }     
    
}