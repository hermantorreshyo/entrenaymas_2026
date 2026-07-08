<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Articulo_Propiedad_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("articulos_propiedades","id","nombre ASC");
	}

	function get($id) {
		$row = parent::get($id);
		if ($row === FALSE) return $row;
		$sql = "SELECT nombre FROM articulos_propiedades_opciones WHERE id_empresa = $row->id_empresa AND id_propiedad = $row->id ";
		$q = $this->db->query($sql);
		$row->opciones = array();
		foreach($q->result() as $r) {
			$row->opciones[] = $r->nombre;
		}
		return $row;
	}		

	function get_by_articulo($id_articulo) {

		$id_empresa = parent::get_empresa();
		$sql = "SELECT APP.* ";
		$sql.= "FROM articulos_propiedades APP ";
		$sql.= "WHERE APP.id_empresa = $id_empresa ";
		$sql.= "AND EXISTS (";
		$sql.= " SELECT AP.* FROM articulos_variantes AV ";
		$sql.= " INNER JOIN articulos_propiedades_opciones APC ON (AV.id_opcion_1 = APC.id AND AV.id_empresa = APC.id_empresa) ";
		$sql.= " INNER JOIN articulos_propiedades AP ON (APC.id_propiedad = AP.id AND APC.id_empresa = AP.id_empresa) ";
		$sql.= " WHERE AV.id_articulo = $id_articulo AND AV.id_empresa = $id_empresa ";
		$sql.= " AND AP.id = APP.id ";
		$sql.= ") ";
		$q=$this->db->query($sql);
		$lista_1 = $q->result();
		if ($lista_1 === FALSE) $lista_1 = array();

		$sql = "SELECT APP.* ";
		$sql.= "FROM articulos_propiedades APP ";
		$sql.= "WHERE APP.id_empresa = $id_empresa ";
		$sql.= "AND EXISTS (";
		$sql.= " SELECT AP.* FROM articulos_variantes AV ";
		$sql.= " INNER JOIN articulos_propiedades_opciones APC ON (AV.id_opcion_2 = APC.id AND AV.id_empresa = APC.id_empresa) ";
		$sql.= " INNER JOIN articulos_propiedades AP ON (APC.id_propiedad = AP.id AND APC.id_empresa = AP.id_empresa) ";
		$sql.= " WHERE AV.id_articulo = $id_articulo AND AV.id_empresa = $id_empresa ";
		$sql.= " AND AP.id = APP.id ";
		$sql.= ") ";
		$q=$this->db->query($sql);
		$lista_2 = $q->result();
		if ($lista_2 === FALSE) $lista_2 = array();

		$sql = "SELECT APP.* ";
		$sql.= "FROM articulos_propiedades APP ";
		$sql.= "WHERE APP.id_empresa = $id_empresa ";
		$sql.= "AND EXISTS (";
		$sql.= " SELECT AP.* FROM articulos_variantes AV ";
		$sql.= " INNER JOIN articulos_propiedades_opciones APC ON (AV.id_opcion_3 = APC.id AND AV.id_empresa = APC.id_empresa) ";
		$sql.= " INNER JOIN articulos_propiedades AP ON (APC.id_propiedad = AP.id AND APC.id_empresa = AP.id_empresa) ";
		$sql.= " WHERE AV.id_articulo = $id_articulo AND AV.id_empresa = $id_empresa ";
		$sql.= " AND AP.id = APP.id ";
		$sql.= ") ";
		$q=$this->db->query($sql);
		$lista_3 = $q->result();
		if ($lista_3 === FALSE) $lista_3 = array();

		$lista = array_merge($lista_1,$lista_2,$lista_3);
		foreach($lista as $l) {
			$sql = "SELECT id,nombre FROM articulos_propiedades_opciones WHERE id_empresa = $l->id_empresa AND id_propiedad = $l->id ";
			$q = $this->db->query($sql);
			$l->opciones = $q->result();
		}
		return $lista;
	}


	function get_all($limit = null, $offset = null,$order_by = '',$order = '') {
		$id_empresa = parent::get_empresa();
		$sql = "SELECT * FROM articulos_propiedades ";
		$sql.= "WHERE id_empresa = $id_empresa ";
		// TODO: Hacer esto dinamico despues
		if ($id_empresa != 1305) $sql.= "OR id_empresa = 0 ";
		$q = $this->db->query($sql);
		$lista = array();
		foreach($q->result() as $l) {			
			$sql = "SELECT id,nombre,etiqueta FROM articulos_propiedades_opciones WHERE id_empresa = $l->id_empresa AND id_propiedad = $l->id ";
			$sql.= "ORDER BY etiqueta ASC ";
			$q = $this->db->query($sql);
			$l->opciones = $q->result();
			$lista[] = $l;
		}
		return $lista;
	}

	function save_opcion($tag) {
		// Primero controlamos si existe la etiqueta
		$q = $this->db->query("SELECT * FROM articulos_propiedades_opciones WHERE nombre = '$tag->nombre' AND id_propiedad = $tag->id_propiedad AND id_empresa = $tag->id_empresa LIMIT 0,1");
		if ($q->num_rows()<=0) {
			// Si no existe, la guardamos
			$this->db->query("INSERT INTO articulos_propiedades_opciones (nombre,id_propiedad,id_empresa) VALUES ('$tag->nombre',$tag->id_propiedad,$tag->id_empresa)");
			$id_etiqueta = $this->db->insert_id();
		} else {
			$row = $q->row();
			$id_etiqueta = $row->id;
		}
	}	
    
}