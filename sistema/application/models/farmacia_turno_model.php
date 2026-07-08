<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Farmacia_Turno_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("inf_farmacias_turnos","id","fecha DESC",1);
	}
  
	function guardia($conf = array()) {
		$id_empresa = isset($conf["id_empresa"])?$conf["id_empresa"]:parent::get_empresa();
		$fecha_desde = isset($conf["desde"])?$conf["desde"]:"";
		$fecha_hasta = isset($conf["hasta"])?$conf["hasta"]:"";
		$sql = "SELECT T.*, ";
		$sql.= " F.nombre AS title, TRUE as 'allDay', ";
		$sql.= " DATE_FORMAT(T.fecha, '%Y-%m-%d %H:%i:%s') AS start, ";
		$sql.= " DATE_FORMAT(T.fecha, '%Y-%m-%d %H:%i:%s') AS end ";
		$sql.= "FROM inf_farmacias_turnos T ";
		$sql.= "INNER JOIN inf_farmacias F ON (T.id_farmacia = F.id) ";
		$sql.= "WHERE 1=1 ";
		if (!empty($id_empresa)) $sql.= "AND T.id_empresa = $id_empresa ";
		if (!empty($fecha_desde)) $sql.= "AND '$fecha_desde' <= T.fecha ";
		if (!empty($fecha_hasta)) $sql.= "AND T.fecha <= '$fecha_hasta' ";
		$q = $this->db->query($sql);
		return $q->result();
	}
	
	function save($data) {
		$this->load->helper("fecha_helper");
		$data->fecha = fecha_mysql($data->fecha);
		unset($data->farmacia);
		$s = parent::save($data);

    // Llamamos al cacheador
    if ($data->id_empresa == 70) {
      $ch = curl_init();
      curl_setopt($ch,CURLOPT_URL, "https://www.quepensaschacabuco.com/sistema/application/cronjobs/cachear_quepensas.php");
      curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
      curl_exec($ch);
    }    

		return $s;
	}	
}