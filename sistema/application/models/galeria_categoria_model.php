<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Galeria_Categoria_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("galerias_categorias","id","nombre ASC",0);
	}
	
	// Reordena los elementos del arbol
	function reorder($elements,$orden = 0, $id_padre = 0) {

		if (isset($elements["id"])) {
			$id = $elements["id"];
			if (!empty($id)) {
				$sql = "UPDATE galerias_categorias SET orden = $orden, id_padre = $id_padre ";
				$sql.= "WHERE id = $id ";
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
        $result = array();
        $q = $this->db->query("SELECT * FROM galerias_categorias WHERE id_padre = $id_padre ORDER BY orden ASC");
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
        $result = array();
        $q = $this->db->query("SELECT * FROM galerias_categorias WHERE id_padre = $id_padre ORDER BY nombre ASC");
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
	
}