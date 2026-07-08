<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Landing_Page_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("landing_pages","id");
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
    
	/**
	 * Obtiene los publicidades a partir de diferentes parametros
	 */
	function buscar($conf = array()) {
		
		$id_empresa = parent::get_empresa();
		
		$sql = "SELECT SQL_CALC_FOUND_ROWS A.* ";
		$sql.= "FROM landing_pages A ";
		$sql.= "WHERE 1=1 ";
		$sql.= "AND A.id_empresa = $id_empresa ";
		if (isset($conf["filter"]) && !empty($conf["filter"])) $sql.= "AND A.nombre LIKE '%".$conf["filter"]."%' ";
        
		//if (empty($order)) $sql.= "ORDER BY A.nombre ASC ";
		//else $sql.= "ORDER BY A.$order ";
		
		//if ($offset != 0) $sql.= "LIMIT $limit, $offset ";
		$q = $this->db->query($sql);
		
		$q_total = $this->db->query("SELECT FOUND_ROWS() AS total");
		$total = $q_total->row();

		return array(
			"results"=>$q->result(),
			"total"=>$total->total
		);
	}
	
	function impresiones($conf = array()) {
		
		$id_empresa = parent::get_empresa();
		$sql = "SELECT SQL_CALC_FOUND_ROWS A.* ";
		$sql.= "FROM landing_pages A ";
		$sql.= "WHERE 1=1 ";
		$sql.= "AND A.id_empresa = $id_empresa ";
		if (isset($conf["order"]) && !empty($conf["order"])) $sql.= "ORDER BY ".$conf["order"]." ";
		else $sql.= "ORDER BY A.nombre ASC ";
		if (isset($conf["limit"]) && !empty($conf["limit"])) $sql.= "LIMIT ".$conf["limit"].", ".$conf["offset"];
		
		$q = $this->db->query($sql);
		$result = $q->result();
		foreach($result as &$r) {
			
			// Contamos la cantidad de impresiones
			$sql = "SELECT IF(COUNT(*) IS NULL,0,COUNT(*)) AS impresiones ";
			$sql.= "FROM landing_pages_impresiones ";
			$sql.= "WHERE id_landing_page = $r->id AND id_empresa = $id_empresa ";
			$q1 = $this->db->query($sql);
			$imp = $q1->row();
			$r->impresiones = $imp->impresiones;
			
			// Contamos la cantidad de clicks
			$sql = "SELECT IF(COUNT(*) IS NULL,0,COUNT(*)) AS clicks ";
			$sql.= "FROM landing_pages_clicks ";
			$sql.= "WHERE id_landing_page = $r->id AND id_empresa = $id_empresa ";
			$q1 = $this->db->query($sql);
			$imp = $q1->row();
			$r->clicks = $imp->clicks;
			
			// Contamos la cantidad de contactos
			$sql = "SELECT IF(COUNT(*) IS NULL,0,COUNT(*)) AS contactos ";
			$sql.= "FROM landing_pages_contactos ";
			$sql.= "WHERE id_landing_page = $r->id AND id_empresa = $id_empresa ";
			$q1 = $this->db->query($sql);
			$imp = $q1->row();
			$r->clicks = $imp->contactos;			
			
			$r->promedio_impresiones_dia = 0;
		}
		
		return $result;
	}
	
	function get($id) {
		$id_empresa = parent::get_empresa();
		// Obtenemos los datos del publicidad
		$id = (int)$id;
		$sql = "SELECT A.* ";
		$sql.= "FROM landing_pages A ";
		$sql.= "WHERE A.id = $id ";
		$sql.= "AND A.id_empresa = $id_empresa ";
		$q = $this->db->query($sql);
		if ($q->num_rows() == 0) return array();
		$publicidad = $q->row();
		
		// Obtenemos las imagenes de ese publicidad
		$sql = "SELECT AI.* FROM landing_pages_images AI WHERE AI.id_landing_page = $id AND AI.id_empresa = $id_empresa ORDER BY AI.orden ASC";
		$q = $this->db->query($sql);
		$publicidad->images = array();
		foreach($q->result() as $r) {
			$publicidad->images[] = $r->path;
		}
		return $publicidad;
	}
	
	function delete($id) {
		// Controlamos que se este borrando un publicidad que pertenece a la empresa de la session
		$id_empresa = parent::get_empresa();
		if ($id_empresa === FALSE) return;
		$this->db->query("DELETE FROM landing_pages_impresiones WHERE id_landing_page = $id");
		$this->db->query("DELETE FROM landing_pages_clicks WHERE id_landing_page = $id");
		$this->db->query("DELETE FROM landing_pages_contactos WHERE id_landing_page = $id");
		$this->db->query("DELETE FROM landing_pages_images WHERE id_landing_page = $id");
		$this->db->query("DELETE FROM landing_pages WHERE id = $id");
	}

}