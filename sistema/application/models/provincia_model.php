<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Provincia_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("com_provincias","id","nombre ASC",0);
	}
    
	function find($filter) {
		$this->db->like("nombre",$filter);
		$query = $this->db->get($this->tabla);
		$result = $query->result();
		$this->db->close();
		return $result;
	}

  function get_select($config = array()) {
    $id_pais = (isset($config["id_pais"])) ? $config["id_pais"] : 0;
    $sql = "SELECT P.* FROM com_provincias P WHERE P.id_pais = $id_pais ORDER BY P.nombre ASC";
    $q = $this->db->query($sql);
    $result = array();
    foreach($q->result() as $row) {
      $e = new stdClass();
      $e->id = $row->id;
      $e->nombre = $row->nombre;
      $result[] = $e;
    }
    return $result;
  }	
	
}