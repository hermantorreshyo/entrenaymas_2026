<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Tipo_Comprobante_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("tipos_comprobante","id","nombre ASC",0);
	}

	function find($filter) {
		$this->db->like("nombre",$filter);
		$query = $this->db->get($this->tabla);
		$result = $query->result();
		$this->db->close();
		return $result;
	}
	
	function get($id) {
		$query = $this->db->get_where($this->tabla,array($this->ident=>$id));
		$row = $query->row(); 
		$this->db->close();
		return $row;
	}
	
	function get_all($limit = null, $offset = null,$order_by = '',$order = '') {
		if (!empty($order_by)) $this->db->order_by($order_by." ".$order);
		else $this->db->order_by($this->order_by);
		if (!is_null($limit) && (strlen($limit)>0) && !is_null($offset) && (strlen($offset)>0)) {
			$query = $this->db->get($this->tabla,$offset,$limit);	
		} else {
			$query = $this->db->get($this->tabla);
		}
		$result = $query->result();
		$this->db->close();
		return $result;
	}	

}