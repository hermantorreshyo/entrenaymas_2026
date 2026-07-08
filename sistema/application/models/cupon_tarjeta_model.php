<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Cupon_Tarjeta_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("cupones_tarjetas","id");
	}

  function get_total($config=array()) {
    $id_empresa = isset($conf["id_empresa"]) ? $conf["id_empresa"] : parent::get_empresa();
    $campo = isset($conf["campo"]) ? $conf["campo"] : "total";
    $desde = isset($conf["desde"]) ? $conf["desde"] : date("Y-m-d");
    $hasta = isset($conf["hasta"]) ? $conf["hasta"] : date("Y-m-d");
    $sql = "SELECT SUM($campo) AS total ";
    $sql.= "FROM cupones_tarjetas CT ";
    $sql.= "WHERE CT.id_empresa = $id_empresa ";
    $sql.= "AND '$desde' <= DATE_FORMAT(CT.fecha,'%Y-%m-%d') AND DATE_FORMAT(CT.fecha,'%Y-%m-%d') <= '$hasta' ";
    $q = $this->db->query($sql);
    $row = $q->row();
    if (is_null($row->total)) return 0;
    else return (float)$row->total;
  }
}