<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Categoria_Viaje_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("via_viajes_categorias","id","nombre ASC");
	}
	
	function get($id) {
		$id_empresa = parent::get_empresa();
		$id = (int)$id;
		$sql = "SELECT R.* ";
		$sql.= "FROM via_viajes_categorias R ";
		$sql.= "WHERE R.id = $id ";
		$sql.= "AND R.id_empresa = $id_empresa ";
		$q = $this->db->query($sql);
		if ($q->num_rows() == 0) return array();
		$row = $q->row();
		return $row;
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
	
	// Reordena los elementos del arbol
	function reorder($elements,$orden = 0, $id_padre = 0) {
		$id_empresa = parent::get_empresa();

		if (isset($elements["id"])) {
			$id = $elements["id"];
			if (!empty($id)) {
				$sql = "UPDATE via_viajes_categorias SET orden = $orden, id_padre = $id_padre ";
				$sql.= "WHERE id = $id AND id_empresa = $id_empresa ";
				$this->db->query($sql);				
			}
		}
		if (isset($elements["children"]) && is_array($elements["children"])){
			for($i=0;$i<sizeof($elements["children"]);$i++) {
				$e = $elements["children"][$i];
				$this->reorder($e,$i,$id);
			}
		}
	}
	
	
    function get_arbol($id_padre = 0,$separador = "") {
		$id_empresa = parent::get_empresa();
        $result = array();
        $q = $this->db->query("SELECT * FROM via_viajes_categorias WHERE id_empresa = $id_empresa AND id_padre = $id_padre ORDER BY orden ASC");
        foreach($q->result() as $row) {
			$e = new stdClass();
			$e->id = $row->id;
			$e->id_padre = $id_padre;
			$e->title = $row->nombre;
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
        $q = $this->db->query("SELECT * FROM via_viajes_categorias WHERE id_empresa = $id_empresa AND id_padre = $id_padre ORDER BY nombre ASC");
        foreach($q->result() as $row) {
			$e = new stdClass();
			$e->id = $row->id;
			$e->id_padre = $id_padre;
			$e->nombre = $separador.$row->nombre;
			$result[] = $e;
			$hijos = $this->get_select($row->id,$separador."&nbsp;&nbsp;&nbsp;");
			$result = array_merge($result,$hijos);
        }
        return $result;
    }
	
	function save($data) {
		$this->load->helper("file_helper");
		$data->link = filename($data->nombre,"-",0);
		parent::save($data);
	}
	
	function delete($id) {
		$id_empresa = parent::get_empresa();
		if ($id_empresa === FALSE) return;
		$this->db->query("DELETE FROM via_viajes_categorias WHERE id = $id AND id_empresa = $id_empresa");
	}
}