<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Sorteo_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("sorteos","id");
	}
	
	function participo($id_usuario,$id_sorteo,$id_empresa = 0) {
		if ($id_empresa == 0) $id_empresa = parent::get_empresa();
		$sql = "SELECT * FROM sorteos_usuarios WHERE id_usuario = '$id_usuario' AND id_sorteo = '$id_sorteo' AND id_empresa = '$id_empresa'";
		$q = $this->db->query($sql);
		return (($q->num_rows()>0)?TRUE:FALSE);
	}
	
	function find($filter) {
		$id_empresa = parent::get_empresa();
		$this->db->where("id_empresa",$id_empresa);
		$this->db->like("titulo",$filter);
		$query = $this->db->get($this->tabla);
		$result = $query->result();
		$this->db->close();
		return $result;
	}
    
	/**
	 * Obtiene los sorteoes a partir de diferentes parametros
	 */
	function buscar($conf = array()) {
		
		$id_empresa = parent::get_empresa();
		
		$sql = "SELECT SQL_CALC_FOUND_ROWS A.* ";
		$sql.= "FROM sorteos A ";
		$sql.= "WHERE 1=1 ";
		$sql.= "AND A.id_empresa = $id_empresa ";
		if (isset($conf["filter"]) && !empty($conf["filter"])) $sql.= "AND A.titulo LIKE '%".$conf["filter"]."%' ";
        
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
	
	function get($id,$id_empresa = 0) {
		if ($id_empresa == 0) $id_empresa = parent::get_empresa();
		// Obtenemos los datos del sorteo
		$id = (int)$id;
		$sql = "SELECT A.* ";
		$sql.= "FROM sorteos A ";
		$sql.= "WHERE A.id = $id ";
		$sql.= "AND A.id_empresa = $id_empresa ";
		$q = $this->db->query($sql);
		if ($q->num_rows() == 0) return array();
		$sorteo = $q->row();
		
		$sql = "SELECT SU.*, U.path, U.nombre, U.email, ";
		$sql.= " DATE_FORMAT(SU.fecha,'%d/%m/%Y') AS fecha, ";
		$sql.= " DATE_FORMAT(SU.fecha,'%H:%i') AS hora ";
		$sql.= "FROM sorteos_usuarios SU INNER JOIN web_users U ON (U.id = SU.id_usuario) ";
		$sql.= "WHERE SU.id_sorteo = $id AND SU.id_empresa = $id_empresa ORDER BY SU.fecha DESC";
		$q = $this->db->query($sql);
		$sorteo->usuarios = array();
		foreach($q->result() as $r) {
			$sorteo->usuarios[] = $r;
		}
		
		return $sorteo;
	}
	
	function delete($id) {
		// Controlamos que se este borrando un sorteo que pertenece a la empresa de la session
		$id_empresa = parent::get_empresa();
		if ($id_empresa === FALSE) return;
		$this->db->query("DELETE FROM sorteos_usuarios WHERE id_sorteo = $id AND id_empresa = $id_empresa");
		$this->db->query("DELETE FROM sorteos WHERE id = $id AND id_empresa = $id_empresa");
	}

}