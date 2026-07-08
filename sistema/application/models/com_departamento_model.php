<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Com_Departamento_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("com_departamentos","id","nombre ASC");
	}

  function get_select($config = array()) {
    $id_provincia = (isset($config["id_provincia"])) ? $config["id_provincia"] : 0;
    $sql = "SELECT P.* FROM com_departamentos P WHERE P.id_provincia = $id_provincia ORDER BY P.nombre ASC";
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