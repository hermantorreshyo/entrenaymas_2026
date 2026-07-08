<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Pais_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("com_paises","id","nombre ASC",0);
	}

  function get_select($config = array()) {
    $solo_con_provincias = (isset($config["solo_con_provincias"])) ? $config["solo_con_provincias"] : 1;
    if ($solo_con_provincias == 1) {
      $sql = "SELECT DISTINCT P.* FROM com_paises P INNER JOIN com_provincias PRO ON (PRO.id_pais = P.id) ORDER BY P.nombre ASC";
    } else {
      $sql = "SELECT P.* FROM com_paises P ORDER BY P.nombre ASC";
    }
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