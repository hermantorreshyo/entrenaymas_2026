<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Galeria_Imagen_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("galerias_imagenes","id","nombre ASC",0);
	}
	
	function find($filter) {
		$this->db->like("nombre",$filter);
		$query = $this->db->get($this->tabla);
		$result = $query->result();
		$this->db->close();
		return $result;
	}
	
	function save_tag($tag) {
		$this->load->helper("file_helper");
		// Primero controlamos si existe la etiqueta
		$q = $this->db->query("SELECT * FROM galerias_etiquetas WHERE nombre = '$tag->nombre' LIMIT 0,1");
		if ($q->num_rows()<=0) {
			// Si no existe, la guardamos
			$link = filename($tag->nombre,"-",0);
			$this->db->query("INSERT INTO galerias_etiquetas (nombre,link) VALUES ('$tag->nombre','$link')");
			$id_etiqueta = $this->db->insert_id();
		} else {
			$row = $q->row();
			$id_etiqueta = $row->id;
		}
		$this->db->query("INSERT INTO galerias_imagenes_etiquetas (id_galeria,id_etiqueta) VALUES ($tag->id_galeria,$id_etiqueta) ");
	}
	
	/**
	 * Obtiene los libros a partir de diferentes parametros
	 */
	function buscar($conf = array()) {
		
		$filter = isset($conf["filter"]) ? $conf["filter"] : "";
		$limit = isset($conf["limit"]) ? $conf["limit"] : 0;
		$offset = isset($conf["offset"]) ? $conf["offset"] : 10;
		$order = isset($conf["order"]) ? $conf["order"] : "A.nombre ASC";
		$id_etiqueta = isset($conf["id_etiqueta"]) ? $conf["id_etiqueta"] : 0;
		
        $sql = "SELECT SQL_CALC_FOUND_ROWS DISTINCT A.* ";
        $sql.= "FROM galerias_imagenes A ";
		$sql.= "WHERE 1=1 ";
        if (!empty($filter)) $sql.= "AND A.nombre LIKE '%$filter%' ";
		if (!empty($id_etiqueta)) $sql.= "AND EXISTS (SELECT * FROM galerias_imagenes_etiquetas LE WHERE LE.id_galeria = A.id AND LE.id_etiqueta = $id_etiqueta) ";
		$sql.= "ORDER BY $order ";
		$sql.= "LIMIT $limit, $offset ";
        $q = $this->db->query($sql);
        
        $q_total = $this->db->query("SELECT FOUND_ROWS() AS total");
        $total = $q_total->row();
		
		return array(
            "results"=>$q->result(),
            "total"=>$total->total,
		);
	}
	
	function get($id) {
		$id = (int)$id;
		$sql = "SELECT A.* ";
		$sql.= "FROM galerias_imagenes A ";
		$sql.= "WHERE A.id = $id ";
		$q = $this->db->query($sql);
		if ($q->num_rows() == 0) return array();
		$libro = $q->row();
		
		$sql = "SELECT E.nombre FROM galerias_imagenes_etiquetas EE INNER JOIN galerias_etiquetas E ON (EE.id_etiqueta = E.id) ";
		$sql.= "WHERE EE.id_galeria = $id ORDER BY EE.orden ASC";
		$q = $this->db->query($sql);
		$libro->etiquetas = array();
		foreach($q->result() as $r) {
			$libro->etiquetas[] = $r->nombre;
		}
		return $libro;
	}
	
	function delete($id) {
		$q = $this->db->query("SELECT * FROM galerias_imagenes WHERE id = $id ");
		if ($q->num_rows()>0) {
			$this->db->query("DELETE FROM galerias_imagenes_etiquetas WHERE id_galeria = $id ");
			$this->db->query("DELETE FROM galerias_imagenes WHERE id = $id");
		}
	}

}