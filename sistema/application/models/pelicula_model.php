<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Pelicula_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("inf_cartelera","id");
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
		$limit = isset($conf["limit"]) ? $conf["limit"] : 0;
		$order = isset($conf["order"]) ? $conf["order"] : "A.nombre ASC ";
		$offset = isset($conf["offset"]) ? $conf["offset"] : 10;
		
		$sql = "SELECT SQL_CALC_FOUND_ROWS A.* ";
		$sql.= "FROM inf_cartelera A ";
		$sql.= "WHERE 1=1 ";
		$sql.= "AND A.id_empresa = $id_empresa ";
		if (isset($conf["filter"]) && !empty($conf["filter"])) $sql.= "AND A.nombre LIKE '%".$conf["filter"]."%' ";
		$sql.= "ORDER BY $order ";
		$sql.= "LIMIT $limit, $offset ";
		$q = $this->db->query($sql);
		
		$q_total = $this->db->query("SELECT FOUND_ROWS() AS total");
		$total = $q_total->row();

		return array(
			"results"=>$q->result(),
			"total"=>$total->total
		);
	}
	
	function get($id) {
		$id_empresa = parent::get_empresa();
		// Obtenemos los datos del publicidad
		$id = (int)$id;
		$sql = "SELECT A.* ";
		$sql.= "FROM inf_cartelera A ";
		$sql.= "WHERE A.id = $id ";
		$sql.= "AND A.id_empresa = $id_empresa ";
		$q = $this->db->query($sql);
		if ($q->num_rows() == 0) return array();
		$publicidad = $q->row();
		return $publicidad;
	}
	
	function delete($id) {
		// Controlamos que se este borrando un publicidad que pertenece a la empresa de la session
		$id_empresa = parent::get_empresa();
		if ($id_empresa === FALSE) return;
		$this->db->query("DELETE FROM inf_cartelera WHERE id = $id");
	}
	
	function participo($id_usuario,$id_encuesta,$id_empresa = 0) {
		$id_empresa = ($id_empresa != 0) ? parent::get_empresa() : $id_empresa;
		$sql = "SELECT * FROM inf_cartelera_usuarios WHERE id_usuario = '$id_usuario' AND id_cartelera = '$id_encuesta' AND id_empresa = '$id_empresa'";
		$q = $this->db->query($sql);
		return (($q->num_rows()>0)?TRUE:FALSE);
	}	

}