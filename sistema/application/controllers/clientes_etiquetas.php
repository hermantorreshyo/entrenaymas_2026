<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Clientes_Etiquetas extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Cliente_Etiqueta_Model', 'modelo');
  }
	
  function get_by_nombre($id_empresa = 0) {
    $id_empresa = ($id_empresa == 0) ? parent::get_empresa() : $id_empresa;
    $nombre = $this->input->get("term");
    $sql = "SELECT * ";
    $sql.= "FROM clientes_etiquetas ";
    $sql.= "WHERE nombre LIKE '%$nombre%' ";
    $sql.= "AND id_empresa = $id_empresa ";
    $q = $this->db->query($sql);
    $resultado = array();
    foreach($q->result() as $r) {
      $rr = new stdClass();
      $rr->id = $r->nombre;
      $rr->label = $r->nombre;
      $rr->value = $r->nombre;
      $rr->text = $r->nombre;
      $resultado[] = $rr;
    }
    echo json_encode($resultado);
  }

  function export($id_empresa = 0, $id_sucursal = 0, $last_update = 0) {
    if ($id_empresa == 0) { echo gzdeflate("0"); exit(); }
    $sql = "SELECT A.* ";
    $sql.= "FROM clientes_etiquetas A ";
    $sql.= "WHERE A.id_empresa = $id_empresa ";
    //if ($last_update > 0) $sql.= "AND UNIX_TIMESTAMP(A.fecha_ult_operacion) >= $last_update ";

    $q = $this->db->query($sql);
    if ($q->num_rows() == 0) { echo gzdeflate("0"); exit(); }

    $this->load->helper("import_helper");
    $salida = create_string_to_export($q);
    
    // Enviamos la cadena comprimida para ahorrar ancho de banda
    echo gzdeflate($salida);
  }

  function export_relaciones($id_empresa = 0, $id_sucursal = 0, $last_update = 0) {
    if ($id_empresa == 0) { echo gzdeflate("0"); exit(); }
    $sql = "SELECT A.* ";
    $sql.= "FROM clientes_etiquetas_relacion A ";
    $sql.= "WHERE A.id_empresa = $id_empresa ";
    //if ($last_update > 0) $sql.= "AND UNIX_TIMESTAMP(A.fecha_ult_operacion) >= $last_update ";

    $q = $this->db->query($sql);
    if ($q->num_rows() == 0) { echo gzdeflate("0"); exit(); }

    $this->load->helper("import_helper");
    $salida = create_string_to_export($q);
    
    // Enviamos la cadena comprimida para ahorrar ancho de banda
    echo gzdeflate($salida);
  }
    
}