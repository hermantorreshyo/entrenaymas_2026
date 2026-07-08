<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Habitaciones extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Habitacion_Model', 'modelo');
  }

  function duplicar($id) {
    
    $row = $this->modelo->get($id);
    if ($row === FALSE) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"No se encuentra el item con ID: $id",
      ));
      return;
    }
    $row->id = 0;
    $insert_id = $this->modelo->insert($row);
    echo json_encode(array(
      "id"=>$insert_id
    ));
  }

  // Utilizado en Calendar de Reservas
  function get_all_min() {
    $id_empresa = $this->get_empresa();
    $sql = "SELECT T.id, T.nombre, ";
    $sql.= " IF(TH.nombre IS NULL,'',TH.nombre) AS tipo ";
    $sql.= "FROM hot_habitaciones T ";
    $sql.= " LEFT JOIN hot_tipos_habitaciones TH ON (T.id_tipo_habitacion = TH.id AND T.id_empresa = TH.id_empresa) ";
    $sql.= "WHERE T.id_empresa = $id_empresa ";
    $sql.= "ORDER BY T.nombre ASC ";
    $q = $this->db->query($sql);
    echo json_encode($q->result());
  }

	
}