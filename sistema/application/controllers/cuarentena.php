<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Cuarentena extends REST_Controller {
  
  function __construct() {
    parent::__construct();
  }

  function registrar() {
    header('Access-Control-Allow-Origin: *');
    $this->load->model("Log_Model");
    $nombre = parent::get_post("nombre","");
    $telefono = parent::get_post("telefono","");
    $direccion = parent::get_post("direccion","");
    $fecha_inicio = parent::get_post("fecha_inicio","");
    $documento = parent::get_post("documento","");
    $id_empresa = parent::get_post("id_empresa",0);
    $this->Log_Model->imprimir(array(
      "id_empresa"=>$id_empresa,
      "id_usuario"=>0,
      "file"=>"registro.txt",
      "texto"=>print_r($_POST,true),
    ));
    $sql = "INSERT INTO vendedores (nombre, telefono, direccion, codigo, color, id_empresa) VALUES (";
    $sql.= " '$nombre', '$telefono', '$direccion', '$documento', '$fecha_inicio', '$id_empresa' )";
    $q = $this->db->query($sql);
    $id = $this->db->insert_id();
    echo json_encode(array(
      "error"=>0,
      "id"=>$id,
      "id_empresa"=>$id_empresa,
      "mensaje"=>"Los datos se cargaron correctamente.",
    ));
  }
  
}