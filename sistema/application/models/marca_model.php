<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Marca_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("marcas","id","orden ASC, nombre ASC");
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

	// Crea una nueva marca a partir de un nombre, se usa en las importaciones
	// Si ya existe devuelve el ID
  function create($config = array()) {
    
    $id_empresa = (isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa());
    $nombre = (isset($config["nombre"]) ? $config["nombre"] : "");
    if (empty($nombre)) return -1;

    // Consultamos si el rubro ya existe
    $sql = "SELECT * FROM marcas WHERE UPPER(nombre) = '".mb_strtoupper($nombre)."' ";
    $sql.= "AND id_empresa = $id_empresa ";
    $q = $this->db->query($sql);
    if ($q->num_rows()>0) {
      $row = $q->row();
      $id_marca = $row->id;
    } else {
      $sql = "INSERT INTO marcas (id_empresa, nombre, activo) VALUES (";
      $sql.= "$id_empresa, '$nombre', 1)";
      $this->db->query($sql);
      $id_marca = $this->db->insert_id();
    }
    return $id_marca;
  }	

}