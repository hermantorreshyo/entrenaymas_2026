<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Cuenta_Bancaria_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("cuentas_bancarias","id","nombre ASC");
	}
    
	function find($filter) {
		$id_empresa = parent::get_empresa();
		$this->db->where("id_empresa",$id_empresa);
		$this->db->like("nombre",$filter);
		$query = $this->db->get($this->tabla);
		$result = $query->result();
		$this->db->close();
		return $result;
	}
	
	function save($data) {
		unset($data->banco);
		return parent::save($data);
	}
	
	function get($id) {
		$id_empresa = parent::get_empresa();
		$sql = "SELECT CB.*, B.nombre AS banco FROM cuentas_bancarias CB INNER JOIN bancos B ON (CB.id_banco = B.id) WHERE CB.id = $id ";
		$sql.= "AND CB.id_empresa = $id_empresa ";
		$q = $this->db->query($sql);
		return $q->row();
	}
	
	function get_all($limit = null, $offset = null,$order_by = '',$order = '') {
		$id_empresa = parent::get_empresa();
		$sql = "SELECT CB.*, B.nombre AS banco FROM cuentas_bancarias CB INNER JOIN bancos B ON (CB.id_banco = B.id) ";
		$sql.= "AND CB.id_empresa = $id_empresa ";
		if (!empty($order_by)) $sql.= "ORDER BY $order_by $order ";
		if (!is_null($limit)) $sql.= "LIMIT $limit, $offset ";
		$q = $this->db->query($sql);
		return $q->result();
	}

}