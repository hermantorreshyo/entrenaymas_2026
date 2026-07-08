<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Dispositivo_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("com_dispositivos","id","dispositivo ASC");
	}

  function get_all($limit = null, $offset = null,$order_by = '',$order = '') {
    $id_empresa = parent::get_empresa();
    $sql = "SELECT D.*, IF(V.nombre IS NULL,'',V.nombre) AS vendedor ";
    $sql.= "FROM com_dispositivos D ";
    $sql.= "LEFT JOIN vendedores V ON (D.id_empresa = V.id_empresa AND V.id = D.id_vendedor) ";
    $sql.= "WHERE D.id_empresa = $id_empresa ";
    $q = $this->db->query($sql);
    return $q->result();
  }

  function get_by_dispositivo($dispositivo = FALSE) {
    if ($dispositivo === FALSE) return FALSE;
    $sql = "SELECT D.*, IF(V.nombre IS NULL,'',V.nombre) AS vendedor ";
    $sql.= "FROM com_dispositivos D ";
    $sql.= "LEFT JOIN vendedores V ON (D.id_empresa = V.id_empresa AND V.id = D.id_vendedor) ";
    $sql.= "WHERE D.dispositivo = '$dispositivo' ";
    $q = $this->db->query($sql);
    return $q->row();
  }
}