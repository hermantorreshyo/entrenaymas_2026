<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Habitacion_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("hot_habitaciones","id","nombre ASC");
	}

	function find($filter) {
		return $this->get_all(array(
			"filter"=>$filter,
		));
	}    

	function buscar($config = array()) {
		$id_empresa = (isset($config["id_empresa"])) ? $config["id_empresa"] : parent::get_empresa();
		$filter = (isset($config["filter"])) ? $config["filter"] : "";
		$sql = "SELECT H.*, ";
		$sql.= " IF(TH.nombre IS NULL,'',TH.nombre) AS tipo ";
		$sql.= "FROM hot_habitaciones H ";
		$sql.= "LEFT JOIN hot_tipos_habitaciones TH ON (H.id_tipo_habitacion = TH.id) ";
		$sql.= "WHERE H.id_empresa = $id_empresa ";
		if (!empty($filter)) $sql.= "AND H.nombre LIKE '%$filter%' ";
		$q = $this->db->query($sql);
		return $q->result();
	}

	function get_all($limit = null, $offset = null,$order_by = '',$order = '') {
		return $this->buscar();
	}

	function save($data) {
		unset($data->tipo);
		return parent::save($data);
	}

	function get($id,$id_empresa=0) {
		if ($id_empresa == 0) $id_empresa = parent::get_empresa();
		parent::set_empresa($id_empresa);
		return parent::get($id);
	}

}