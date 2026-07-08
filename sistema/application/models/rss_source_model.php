<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Rss_Source_Model extends Abstract_Model {
	
	private $noticias_etiquetas = "";
	
	function __construct() {
		parent::__construct("rss_sources","id","nombre ASC");
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
		$this->noticias_etiquetas = $data->noticias_etiquetas;
		unset($data->noticias_etiquetas);
		return parent::save($data);
	}
	
	function get($id) {
		$data = parent::get($id);
		
		// Obtenemos las etiquetas de esa entrada
		$sql = "SELECT E.nombre FROM not_etiquetas E ";
		$sql.= "WHERE E.id IN ($data->noticias_etiquetas) ";
		$q = $this->db->query($sql);
		$data->noticias_etiquetas = array();
		foreach($q->result() as $r) {
			$data->noticias_etiquetas[] = $r->nombre;
		}
		return $data;
	}
	
	
	function post_save($id) {
		$id_empresa = parent::get_empresa();
		$etiquetas = explode(";;;",$this->noticias_etiquetas);
		$this->load->model("Entrada_Etiqueta_Model");
		$et2 = array();
		foreach($etiquetas as $e) {
			$et = $this->Entrada_Etiqueta_Model->get_by_name($e,$id_empresa);
			if ($et !== FALSE) $et2[] = $et->id;
		}
		$etiq = implode(",",$et2);
		$this->db->query("UPDATE rss_sources SET noticias_etiquetas = '$etiq' WHERE id = $id");
	}

}