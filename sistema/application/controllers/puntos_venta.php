<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Puntos_Venta extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Punto_Venta_Model', 'modelo');
  }

  function export($id_empresa = 0, $id_sucursal = 0, $numero = 0) {
    if ($id_empresa == 0) { echo gzdeflate("0"); exit(); }
    $sql = "SELECT A.* ";
    $sql.= "FROM puntos_venta A ";
    $sql.= "WHERE A.id_empresa = $id_empresa ";
    if ($id_sucursal != 0) $sql.= "AND A.id_sucursal = $id_sucursal ";
    if ($numero != 0) $sql.= "AND A.numero = $numero ";

    $q = $this->db->query($sql);
    if ($q->num_rows() == 0) { echo gzdeflate("0"); exit(); }

    $this->load->helper("import_helper");
    $salida = create_string_to_export($q);
    
    // Enviamos la cadena comprimida para ahorrar ancho de banda
    echo gzdeflate($salida);
  }
    
}