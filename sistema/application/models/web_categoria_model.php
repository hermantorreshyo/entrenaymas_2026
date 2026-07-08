<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Web_Categoria_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("web_categorias","id","nombre_es ASC");
	}
	
	function find($filter) {
		$id_empresa = parent::get_empresa();
		$this->db->where("id_empresa",$id_empresa);		
		$this->db->like("nombre_es",$filter);
		$query = $this->db->get($this->tabla);
		$result = $query->result();
		$this->db->close();
		return $result;
	}
    
    function get_arbol($id_padre = 0,$separador = "") {
		$id_empresa = parent::get_empresa();
        $result = array();
        $q = $this->db->query("SELECT * FROM web_categorias WHERE id_empresa = $id_empresa AND id_padre = $id_padre AND id_proyecto = 2 ORDER BY nombre_es ASC");
        foreach($q->result() as $row) {
			$e = new stdClass();
			$e->id = $row->id;
			$e->id_padre = $id_padre;
			$e->title = $row->nombre_es;
			$e->nombre_es = $e->title;
			$e->key = $row->id;
			$e->children = $this->get_arbol($row->id,$separador."&nbsp;&nbsp;&nbsp;");
			$result[] = $e;            
        }
        return $result;
    }
	
    function get_select($id_padre = 0,$separador = "") {
		$id_empresa = parent::get_empresa();
        $result = array();
        $q = $this->db->query("SELECT * FROM web_categorias WHERE id_empresa = $id_empresa AND id_padre = $id_padre AND id_proyecto = 2 ORDER BY nombre_es ASC");
        foreach($q->result() as $row) {
			$e = new stdClass();
			$e->id = $row->id;
			$e->id_padre = $id_padre;
			$e->nombre_es = $separador.$row->nombre_es;
			$result[] = $e;
			$hijos = $this->get_select($row->id,$separador."&nbsp;&nbsp;&nbsp;");
			$result = array_merge($result,$hijos);
        }
        return $result;
    }
	
	
	function save($data) {
		$this->load->helper("file_helper");
		
		$padre = $this->get($data->id_padre);
		if (!empty($padre)) {
			$link_padre = $padre->link;
		} else {
			$link_padre = "/";
		}
		
		$data->link = $link_padre.filename($data->nombre_es,"-",0)."/";
		parent::save($data);
	}	
}