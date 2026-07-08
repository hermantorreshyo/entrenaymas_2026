<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Entrenaymas extends REST_Controller {

  function __construct() {
    parent::__construct();
  }
	
  function ver_puja_ciudad() {
    $id_empresa = parent::get_empresa();
    $id_localidad = parent::get_get("id_localidad",0);
    $id_usuario = parent::get_get("id_usuario",0);
    $sql = "SELECT IF(MAX(valor) IS NULL,0,MAX(valor)) AS valor ";
    $sql.= "FROM com_usuarios_localidades ";
    $sql.= "WHERE id_empresa = $id_empresa ";
    $sql.= "AND id_localidad = $id_localidad ";
    if (!empty($id_usuario)) $sql.= "AND id_usuario != $id_usuario ";
    $q = $this->db->query($sql);
    $r = $q->row();
    echo json_encode(array(
      "maximo"=>$r->valor,
    ));
  }

  function guardar_calificacion() {
    $id_empresa = parent::get_post("id_empresa",0);
    $comentario = parent::get_post("comentario","");
    $nombre = parent::get_post("nombre","");
    $rating = parent::get_post("rating",0);
    $id_entrada = parent::get_post("id",0);
    $hoy = date("Y-m-d H:i:s");
    $sql = "INSERT INTO not_entradas_comentarios (id_empresa, id_entrada, fecha, texto, nombre, votos_positivos) VALUES ";
    $sql.= "($id_empresa, $id_entrada, '$hoy', '$comentario', '$nombre', '$rating') ";
    $this->db->query($sql);
    echo json_encode(array(
      "error"=>0,
      "mensaje"=>"¡Muchas gracias por calificar al usuario!",
    ));
  }
    
    
}