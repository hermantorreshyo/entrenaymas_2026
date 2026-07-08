<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Libro_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("biblio_libros","id","nombre ASC");
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
	
	function save_tag($tag) {
		$this->load->helper("file_helper");
		// Primero controlamos si existe la etiqueta
		$q = $this->db->query("SELECT * FROM biblio_etiquetas WHERE nombre = '$tag->nombre' AND id_empresa = $tag->id_empresa LIMIT 0,1");
		if ($q->num_rows()<=0) {
			// Si no existe, la guardamos
			$link = filename($tag->nombre,"-",0);
			$this->db->query("INSERT INTO biblio_etiquetas (nombre,link,id_empresa) VALUES ('$tag->nombre','$link',$tag->id_empresa)");
			$id_etiqueta = $this->db->insert_id();
		} else {
			$row = $q->row();
			$id_etiqueta = $row->id;
		}
		$this->db->query("INSERT INTO biblio_libros_etiquetas (id_empresa,id_libro,id_etiqueta) VALUES ($tag->id_empresa,$tag->id_libro,$id_etiqueta) ");
	}
	
	/**
	 * Obtiene los libros a partir de diferentes parametros
	 */
	function buscar($conf = array()) {
		
		$id_empresa = isset($conf["id_empresa"]) ? $conf["id_empresa"] : parent::get_empresa();
		$filter = isset($conf["filter"]) ? $conf["filter"] : "";
		$limit = isset($conf["limit"]) ? $conf["limit"] : 0;
		$offset = isset($conf["offset"]) ? $conf["offset"] : 10;
		$order = isset($conf["order"]) ? $conf["order"] : "A.nombre ASC";
		$id_autor = isset($conf["id_autor"]) ? $conf["id_autor"] : 0;
		$id_etiqueta = isset($conf["id_etiqueta"]) ? $conf["id_etiqueta"] : 0;
		
        $sql = "SELECT SQL_CALC_FOUND_ROWS DISTINCT A.*, ";
		$sql.= "  IF(R.nombre IS NULL,'Sin definir',R.nombre) AS autor ";
        $sql.= "FROM biblio_libros A ";
		$sql.= "LEFT JOIN biblio_autores R ON (A.id_autor = R.id) ";
		$sql.= "WHERE 1=1 ";
		$sql.= "AND A.id_empresa = $id_empresa ";
        if (!empty($filter)) $sql.= "AND A.nombre LIKE '%$filter%' ";
		if (!empty($id_autor)) $sql.= "AND A.id_autor = $id_autor ";
		if (!empty($id_etiqueta)) $sql.= "AND EXISTS (SELECT * FROM biblio_libros_etiquetas LE WHERE LE.id_libro = A.id AND LE.id_etiqueta = $id_etiqueta) ";
		$sql.= "ORDER BY $order ";
		$sql.= "LIMIT $limit, $offset ";
        $q = $this->db->query($sql);
        
        $q_total = $this->db->query("SELECT FOUND_ROWS() AS total");
        $total = $q_total->row();
		
		return array(
            "results"=>$q->result(),
            "total"=>$total->total,
			"sql"=>$sql,
		);
	}
	
	function get($id) {
		$id_empresa = parent::get_empresa();
		// Obtenemos los datos del libro
		$id = (int)$id;
		$sql = "SELECT A.*, ";
		$sql.= "  IF(R.nombre IS NULL,'Sin definir',R.nombre) AS autor ";
		$sql.= "FROM biblio_libros A ";
		$sql.= "LEFT JOIN biblio_autores R ON (A.id_autor = R.id) ";
		$sql.= "WHERE A.id = $id ";
		$sql.= "AND A.id_empresa = $id_empresa ";
		$q = $this->db->query($sql);
		if ($q->num_rows() == 0) return array();
		$libro = $q->row();
		
		// Obtenemos las etiquetas de esa libro
		$sql = "SELECT E.nombre FROM biblio_libros_etiquetas EE INNER JOIN biblio_etiquetas E ON (EE.id_etiqueta = E.id) ";
		$sql.= "WHERE EE.id_libro = $id AND EE.id_empresa = $id_empresa ORDER BY EE.orden ASC";
		$q = $this->db->query($sql);
		$libro->etiquetas = array();
		foreach($q->result() as $r) {
			$libro->etiquetas[] = $r->nombre;
		}
		
		/*
		// Obtenemos los comentarios
		$sql = "SELECT EC.*, ";
		$sql.= " DATE_FORMAT(EC.fecha,'%d/%m/%Y') AS fecha, ";
		$sql.= " DATE_FORMAT(EC.fecha,'%H:%i') AS hora, ";
		$sql.= " IF(C.path IS NULL,'',C.path) AS path ";
		$sql.= "FROM biblio_libros_comentarios EC ";
		$sql.= " LEFT JOIN web_users C ON(C.id = EC.id_usuario) ";
		$sql.= "WHERE EC.id_libro = $id AND EC.id_empresa = $id_empresa ";
		$sql.= "ORDER BY EC.orden ASC";
		$q = $this->db->query($sql);
		$libro->comentarios = array();
		foreach($q->result() as $r) {
			if (!empty($r->path)) $r->path = ((strpos($r->path,"http://")===FALSE)) ? "/sistema/".$r->path : $r->path;
			$libro->comentarios[] = $r;
		}
		*/
		$libro->prestamos = array();
		
		return $libro;
	}
	
	function delete($id) {
		// Controlamos que se este borrando un libro que pertenece a la empresa de la session
		$id_empresa = parent::get_empresa();
		if ($id_empresa === FALSE) return;
		$q = $this->db->query("SELECT * FROM biblio_libros WHERE id = $id AND id_empresa = $id_empresa ");
		if ($q->num_rows()>0) {
			$this->db->query("DELETE FROM biblio_libros_etiquetas WHERE id_libro = $id ");
			$this->db->query("DELETE FROM biblio_libros WHERE id = $id");
		}
	}

}