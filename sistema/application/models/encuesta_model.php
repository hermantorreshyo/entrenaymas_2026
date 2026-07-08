<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Encuesta_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("encuestas","id");
	}
	
	function participo($id_usuario,$id_encuesta,$id_empresa) {
		$sql = "SELECT * FROM encuestas_opciones_usuarios WHERE id_usuario = '$id_usuario' AND id_encuesta = '$id_encuesta' AND id_empresa = '$id_empresa'";
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
	 * Obtiene los encuestaes a partir de diferentes parametros
	 */
	function buscar($conf = array()) {
		
		$id_empresa = parent::get_empresa();
		
		$sql = "SELECT SQL_CALC_FOUND_ROWS A.* ";
		$sql.= "FROM encuestas A ";
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
		// Obtenemos los datos del encuesta
		$id = (int)$id;
		$sql = "SELECT A.* ";
		$sql.= "FROM encuestas A ";
		$sql.= "WHERE A.id = $id ";
		$sql.= "AND A.id_empresa = $id_empresa ";
		$q = $this->db->query($sql);
		if ($q->num_rows() == 0) return array();
		$encuesta = $q->row();
		
		// Obtenemos las imagenes de ese encuesta
		$sql = "SELECT AI.* FROM encuestas_opciones AI WHERE AI.id_encuesta = $id AND AI.id_empresa = $id_empresa ORDER BY AI.orden ASC";
		$q = $this->db->query($sql);
		$encuesta->opciones = array();
		foreach($q->result() as $r) {

      // Contamos la cantidad de votos que tuvo esa opcion
      $sql = "SELECT IF(COUNT(DISTINCT id_usuario) IS NULL,0,COUNT(DISTINCT id_usuario)) AS votos ";
      $sql.= "FROM encuestas_opciones_usuarios ";
      $sql.= "WHERE id_empresa = $id_empresa ";
      $sql.= "AND id_opcion = $r->id ";
      $sql.= "AND id_encuesta = $id ";
      $qq = $this->db->query($sql);
      $rr = $qq->row();
      $r->votos = $rr->votos;

      // Mostramos los usuarios que votaron por esa opcion
      $sql = "SELECT U.nombre, U.path, DATE_FORMAT(OU.fecha,'%d/%m/%Y %H:%i') AS fecha ";
      $sql.= "FROM encuestas_opciones_usuarios OU ";
      $sql.= "INNER JOIN web_users U ON (OU.id_usuario = U.id AND OU.id_empresa = U.id_empresa) ";
      $sql.= "WHERE OU.id_empresa = $id_empresa ";
      $sql.= "AND OU.id_opcion = $r->id ";
      $sql.= "AND OU.id_encuesta = $id ";
      $sql.= "ORDER BY OU.fecha ASC ";
      $qq = $this->db->query($sql);
      $r->usuarios = $qq->result();

			$encuesta->opciones[] = $r;
		}
		return $encuesta;
	}
	
	function delete($id) {
		// Controlamos que se este borrando un encuesta que pertenece a la empresa de la session
		$id_empresa = parent::get_empresa();
		if ($id_empresa === FALSE) return;
		$this->db->query("DELETE FROM encuestas_opciones_usuarios WHERE id_encuesta = $id AND id_empresa = $id_empresa");
		$this->db->query("DELETE FROM encuestas_opciones WHERE id_encuesta = $id AND id_empresa = $id_empresa");
		$this->db->query("DELETE FROM encuestas WHERE id = $id AND id_empresa = $id_empresa");
	}

}