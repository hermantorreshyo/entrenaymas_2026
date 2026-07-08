<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Permisos_Red extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Permiso_Red_Model', 'modelo');
  }

  // Esta funcion notifica el registro de una empresa nueva en Inmovar
  // Se ejecuta como una tarea programada para que no retrase el registro de la empresa nueva
  function notificar($id_empresa) {
    $this->modelo->notificar(array(
      "id_empresa"=>$res["id"],
    ));
    echo json_encode(array("error"=>0));
  } 

  function get_by_empresa() {
    $id_empresa = parent::get_empresa();
    $s = $this->modelo->get_inmobiliarias_red(array(
      "id_empresa"=>$id_empresa
    ));
    $salida = array();
    foreach($s as $row) {
      $sql = "SELECT * FROM inm_permisos_red ";
      $sql.= "WHERE id_empresa_compartida = $row->id ";
      $sql.= "AND id_empresa = $id_empresa ";
      $qq = $this->db->query($sql);
      if ($qq->num_rows()>0) {
        $rr = $qq->row();
        $row->estado = $rr->estado;
      } else {
        $row->estado = 0;
      }
      $salida[] = $row;
    }
    echo json_encode(array(
      "results"=>$salida,
    ));
  }

  function guardar() {
    $id_empresa = parent::get_empresa();
    $datos = parent::get_post("datos",array());
    $this->db->query("DELETE FROM inm_permisos_red WHERE id_empresa = $id_empresa ");
    foreach($datos as $d) {
      $id_empresa_compartida = $d["id_empresa_compartida"];
      $estado = $d["estado"];
      $this->db->query("INSERT INTO inm_permisos_red (id_empresa,id_empresa_compartida,estado) VALUES ($id_empresa,'$id_empresa_compartida','$estado') ");
    }
    echo json_encode(array(
      "error"=>0,
    ));
  }

  function insert() {}
  function update($id) {}
  function delete($id) {}
  function get() {}

}