<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Notificaciones extends REST_Controller {

	function __construct() {
		parent::__construct();
		$this->load->model('Notificacion_Model', 'modelo');
	}
	
	function guardar() {
    header('Access-Control-Allow-Origin: *');
    $subs = json_decode($this->input->post("data"));
		$endpoint = $subs->endpoint;
    $keys = $subs->keys;
    $id_empresa = $this->input->post("id_empresa");
    $id_usuario = parent::get_post("id_usuario",0);
		$sql = "SELECT * FROM notif_usuarios WHERE endpoint = '$endpoint' AND id_empresa = '$id_empresa' ";
    if (!empty($id_usuario)) $sql.= "AND id_usuario = '$id_usuario' ";
		$q = $this->db->query($sql);
		if ($q->num_rows()<=0) {
			$sql = "INSERT INTO notif_usuarios (";
      $sql.= " endpoint,id_empresa,user_public_key,user_auth_key,fecha_alta,id_usuario";
      $sql.= ") VALUES (";
      $sql.= " '$endpoint','$id_empresa','$keys->p256dh','$keys->auth',NOW(),'$id_usuario') ";
			$q = $this->db->query($sql);
		} else {
      $sql = "UPDATE notif_usuarios SET user_public_key = '$keys->p256dh', user_auth_key = '$keys->auth', fecha_alta = 'NOW()' ";
      $sql.= "WHERE endpoint = '$endpoint' AND id_empresa = $id_empresa ";
      if (!empty($id_usuario)) $sql.= "AND id_usuario = '$id_usuario' ";
      $q = $this->db->query($sql);
    }
		echo json_encode(array("error"=>0));
	}
	
	// CONSULTA SI HAY NUEVAS NOTIFICACIONES PARA EL USUARIO
	function consultar($id_empresa,$id_sucursal = 0) {
		if (!empty($id_empresa)) {
			$sql = "SELECT * FROM com_log WHERE id_empresa = $id_empresa AND importancia = 'N' AND leida = 0 ";
      if ($id_sucursal != 0) $sql.= "AND id_sucursal = $id_sucursal ";
      $sql.= "ORDER BY fecha DESC ";
			$q = $this->db->query($sql);
      $lista = $q->result();
			echo json_encode(array(
				"results"=>$lista,
        "total"=>sizeof($lista),
			));			
		} else {
			echo json_encode(array(
				"results"=>array(),
        "total"=>0,
			));						
		}
	}
	
	// LIMPIA TODAS LAS NOTIFICACIONES COMO LEIDAS
	function limpiar($id_empresa,$id = 0,$id_sucursal = 0) {
		$sql = "UPDATE com_log SET leida = 1 WHERE id_empresa = $id_empresa AND importancia = 'N' ";
    if ($id != 0) $sql.= "AND id = $id ";
    if ($id_sucursal != 0) $sql.= "AND id_sucursal = $id_sucursal ";
		$this->db->query($sql);
		echo json_encode(array("error"=>0));
	}
	
}