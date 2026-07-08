<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Sindi_Condiciones_Especiales extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Sindi_Condicion_Especial_Model', 'modelo');
  }

  function buscar_por_afiliado($id) {
    $id_empresa = parent::get_empresa();
    $sql = "SELECT CE.* ";
    $sql.= "FROM sindi_condiciones_especiales_pacientes CEP ";
    $sql.= "INNER JOIN sindi_condiciones_especiales CE ON (CEP.id_empresa = CE.id_empresa AND CEP.id_condicion_especial = CE.id) ";
    $sql.= "WHERE CEP.id_empresa = $id_empresa ";
    $sql.= "AND CEP.id_afiliado = $id ";
    $sql.= "AND CEP.estado = 1 "; // Cuando ya fue asignada por el medico
    $q = $this->db->query($sql);
    $res = $q->result();
    echo json_encode(array(
      "results"=>$res,
      "total"=>sizeof($res)
    ));
  }
	
}