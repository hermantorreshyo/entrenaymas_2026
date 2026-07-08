<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Vendedor_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("vendedores","id","nombre ASC");
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

  function get($id = 0,$conf=array()) {
    $id_empresa = isset($conf["id_empresa"]) ? $conf["id_empresa"] : parent::get_empresa();
    $codigo = isset($conf["codigo"]) ? $conf["codigo"] : "";
    $sql = "SELECT * FROM vendedores ";
    $sql.= "WHERE 1=1 ";
    if ($id_empresa != -1) $sql.= "AND id_empresa = $id_empresa ";
    if ($id != 0) $sql.= "AND id = $id ";
    if (!empty($codigo)) $sql.= "AND codigo = '$codigo' ";
    $q = $this->db->query($sql);
    return ($q->num_rows()>0) ? $q->row() : FALSE;
  }

}