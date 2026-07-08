<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Salones extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Salon_Model', 'modelo');
  }

  function export($id_empresa = 0) {
    if ($id_empresa == 0) { echo gzdeflate("0"); exit(); }
    $sql = "SELECT A.* ";
    $sql.= "FROM res_salones A ";
    $sql.= "WHERE A.id_empresa = $id_empresa ";

    $q = $this->db->query($sql);
    if ($q->num_rows() == 0) { echo gzdeflate("0"); exit(); }

    $this->load->helper("import_helper");
    $salida = create_string_to_export($q);
    
    // Enviamos la cadena comprimida para ahorrar ancho de banda
    echo gzdeflate($salida);
  }

}