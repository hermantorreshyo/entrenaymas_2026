<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Ocupacion_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("hot_disponibilidad","id","fecha DESC",1);
	}

	function get_disponibilidad($conf = array()) {
		$id_empresa = isset($conf["id_empresa"])?$conf["id_empresa"]:parent::get_empresa();
		$fecha = isset($conf["fecha"])?$conf["fecha"]:"";
		$id_habitacion = isset($conf["id_habitacion"])?$conf["id_habitacion"]:0;
		$sql = "SELECT * ";
		$sql.= "FROM hot_disponibilidad ";
		$sql.= "WHERE 1=1 ";
		if (!empty($id_empresa)) $sql.= "AND id_empresa = $id_empresa ";
		if (!empty($fecha)) $sql.= "AND fecha = '$fecha' ";
		if (!empty($id_habitacion)) $sql.= "AND id_habitacion = '$id_habitacion' ";
		$sql.= "LIMIT 0,1 ";
		$q = $this->db->query($sql);
		if ($q->num_rows()>0) {
			return $q->row();
		} else return FALSE;
	}
  
	function save($data) {
		$this->load->helper("fecha_helper");
		unset($data->habitacion);
		parent::save($data);
	}
}