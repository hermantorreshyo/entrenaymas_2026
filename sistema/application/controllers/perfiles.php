<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Perfiles extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Perfil_Model', 'modelo');
  }
  
  function get_by_empresa($id_empresa) {
    $lista = $this->modelo->get_by_empresa($id_empresa);
    echo json_encode(array(
      "total"=> sizeof($lista),
      "results"=>$lista
    ));
  }  

  function export($id_empresa = 0, $id_sucursal = 0, $last_update = 0) {
    if ($id_empresa == 0) { echo gzdeflate("0"); exit(); }
    $sql = "SELECT A.* ";
    $sql.= "FROM com_perfiles A ";
    $sql.= "WHERE A.id_empresa = $id_empresa ";
    //if ($last_update > 0) $sql.= "AND A.last_update >= $last_update ";

    $q = $this->db->query($sql);
    if ($q->num_rows() == 0) { echo gzdeflate("0"); exit(); }

    $this->load->helper("import_helper");
    $salida = create_string_to_export($q);

    // Enviamos la cadena comprimida para ahorrar ancho de banda
    echo gzdeflate($salida);
  }

  function export_permisos($id_empresa = 0, $id_sucursal = 0, $last_update = 0) {
    if ($id_empresa == 0) { echo gzdeflate("0"); exit(); }
    $sql = "SELECT A.* ";
    $sql.= "FROM com_permisos_modulos A ";
    $sql.= "WHERE A.id_empresa = $id_empresa ";
    //if ($last_update > 0) $sql.= "AND A.last_update >= $last_update ";

    $q = $this->db->query($sql);
    if ($q->num_rows() == 0) { echo gzdeflate("0"); exit(); }

    $this->load->helper("import_helper");
    $salida = create_string_to_export($q);

    // Enviamos la cadena comprimida para ahorrar ancho de banda
    echo gzdeflate($salida);
  }

  function export_modulos_empresas($id_empresa = 0) {
    if ($id_empresa == 0) { echo gzdeflate("0"); exit(); }
    $sql = "SELECT A.* ";
    $sql.= "FROM com_modulos_empresas A ";
    $sql.= "WHERE A.id_empresa = $id_empresa ";

    $q = $this->db->query($sql);
    if ($q->num_rows() == 0) { echo gzdeflate("0"); exit(); }

    $this->load->helper("import_helper");
    $salida = create_string_to_export($q);

    // Enviamos la cadena comprimida para ahorrar ancho de banda
    echo gzdeflate($salida);
  }

}