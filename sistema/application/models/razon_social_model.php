<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Razon_Social_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("razones_sociales","id","nombre ASC");
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

  function get_sucursales_by_razon_social($id_razon_social) {
    $ids_sucursales = "";
    $salida = array();
    $sql = "SELECT * FROM almacenes WHERE id_razon_social = $id_razon_social ";
    $q = $this->db->query($sql);
    foreach($q->result() as $row) {
      $salida[] = $row->id;
    }
    if (!empty($salida)) {
      $ids_sucursales = implode(",", $salida);
    }
    return $ids_sucursales;

    /*
    // TODO: Hacer esto dinamico despues
    if ($id_razon_social == 1) {
      // Gonzalo
      $ids_sucursales = "7,8";

    } else if ($id_razon_social == 2) {
      // Pinamar
      $ids_sucursales = "22";

    } else if ($id_razon_social == 5) {
      // Cascio Jesus: Mendoza y San Luis
      $ids_sucursales = "11,12";

    } else if ($id_razon_social == 5) {
      // Cascio Jesus: Mendoza y San Luis
      $ids_sucursales = "11,12";

    } else if ($id_razon_social == 12) {
      // Rio Grande
      $ids_sucursales = "23";
    }
    return $ids_sucursales;
    */
  }

}