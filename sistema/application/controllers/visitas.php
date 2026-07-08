<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Visitas extends REST_Controller {

  // Registramos las visitas de las propiedades de Inmovar
  function inmovar() {
    header('Access-Control-Allow-Origin: *');
    $id_empresa = parent::get_get("e",0);
    $id_propiedad = parent::get_get("p",0);
    $id_cliente = parent::get_get("c",0);
    $id_empresa_compartida = parent::get_get("ec",0);
    $sql = "INSERT INTO inm_propiedades_visitas (id_empresa,id_propiedad,id_cliente,stamp,id_empresa_propiedad) VALUES (?,?,?,?,?) ";
    $this->db->query($sql, array($id_empresa, $id_propiedad, $id_cliente, date("Y-m-d H:i:s"), $id_empresa_compartida));    
    echo json_encode(array("error"=>0));
  }

}