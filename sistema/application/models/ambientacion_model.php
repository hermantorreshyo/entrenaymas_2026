<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Ambientacion_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("ambientaciones","id");
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
    
	function buscar($params = array()) {
		
		$filter = isset($params["filter"]) ? $params["filter"] : "";
		$limit = isset($params["limit"]) ? $params["limit"] : 0;
		$offset = isset($params["offset"]) ? $params["offset"] : 0;
		$order = isset($params["order"]) ? $params["order"] : "";
		
		$id_empresa = parent::get_empresa();
		
		$sql = "SELECT SQL_CALC_FOUND_ROWS DISTINCT ";
		$sql.= "A.* FROM ambientaciones A ";
        $sql.= "WHERE 1=1 ";    
		$sql.= "AND A.id_empresa = $id_empresa ";
		if (!empty($filter)) $sql.= "AND A.nombre LIKE '%$filter%' ";
		if (empty($order)) $sql.= "ORDER BY A.nombre ASC ";
		else $sql.= "ORDER BY A.orden ASC ";
		if ($offset != 0) $sql.= "LIMIT $limit, $offset ";
        $q = $this->db->query($sql);
        
        $q_total = $this->db->query("SELECT FOUND_ROWS() AS total");
        $total = $q_total->row();
		$resultado = $q->result();
		return array(
            "results"=>$resultado,
            "total"=>$total->total,
		);
	}
	
	function get($id) {
		$id_empresa = parent::get_empresa();
		// Obtenemos los datos del articulo
		$id = (int)$id;
		$sql = "SELECT A.* ";
		$sql.= "FROM ambientaciones A ";
		$sql.= "WHERE A.id = $id ";
		$sql.= "AND A.id_empresa = $id_empresa ";
		$q = $this->db->query($sql);
		if ($q->num_rows() == 0) return array();
		$articulo = $q->row();

		// Obtenemos los ambientaciones articulos con ese producto
		$sql = "SELECT A.id, A.nombre, A.path ";
		$sql.= "FROM articulos A INNER JOIN ambientaciones_articulos AR ON (A.id = AR.id_articulo) ";
		$sql.= "WHERE AR.id_ambientacion = $id ";
		$sql.= "ORDER BY AR.orden ASC ";
		$q = $this->db->query($sql);
		$articulo->articulos = array();
		foreach($q->result() as $r) {
			$obj = new stdClass();
			$obj->id = $r->id;
			$obj->nombre = $r->nombre;
			$obj->path = $r->path;
			$articulo->articulos[] = $obj;
		}
		
		// Obtenemos las imagenes de ese articulo
		$sql = "SELECT AI.* FROM ambientaciones_images AI WHERE AI.id_ambientacion = $id AND AI.id_empresa = $id_empresa ORDER BY AI.orden ASC";
		$q = $this->db->query($sql);
		$articulo->images = array();
		foreach($q->result() as $r) {
			$articulo->images[] = $r->path;
		}
		return $articulo;
	}
	
	
	function delete($id) {
		// Controlamos que se este borrando un articulo que pertenece a la empresa de la session
		$id_empresa = parent::get_empresa();
		if ($id_empresa === FALSE) return;
		$q = $this->db->query("SELECT * FROM ambientaciones WHERE id = $id AND id_empresa = $id_empresa ");
		if ($q->num_rows()>0) {
			$this->db->query("DELETE FROM ambientaciones_articulos WHERE id_ambientacion = $id ");
			$this->db->query("DELETE FROM ambientaciones_images WHERE id_ambientacion = $id");
			$this->db->query("DELETE FROM ambientaciones WHERE id = $id");
		}
	}

}