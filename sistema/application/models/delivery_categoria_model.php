<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Delivery_Categoria_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("delivery_categorias","id","nombre ASC",0);
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
	
	function get_by_name($nombre) {
		$sql = "SELECT * FROM delivery_categorias WHERE nombre = '$nombre' ";
		$q = $this->db->query($sql);
		if ($q->num_rows()>0) {
			return $q->row();
		} else return FALSE;
	}

}