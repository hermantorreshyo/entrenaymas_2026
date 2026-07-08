<?php defined('BASEPATH') OR exit('No direct script access allowed');
require_once("abstract_model.php");

class Not_Editor_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("not_editores","id","nombre ASC");
	}

  function get_editor_by_id($id_editor, $id_empresa) {
    $sql = "SELECT * FROM not_editores WHERE id_empresa = $id_empresa AND id = $id_editor ";
    $q = $this->db->query($sql);
    return $q->row();
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
}