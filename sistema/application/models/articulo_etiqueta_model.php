<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Articulo_Etiqueta_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("articulos_etiquetas","id","nombre ASC");
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
		$id = parent::save($data);

    $this->load->helper("file_helper");
    $data->nombre = trim($data->nombre);
    $data->nombre = str_replace("/", "-", $data->nombre);
    $link = filename($data->nombre,"-",0);
    $this->db->query("UPDATE articulos_etiquetas SET link = '$link' WHERE id = $id AND id_empresa = $data->id_empresa ");

		return $id;
	}

}