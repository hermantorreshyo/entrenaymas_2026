<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Web_User_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("web_users","id");
	}
	
	function buscar($conf = array()) {
		
		$id_empresa = isset($conf["id_empresa"]) ? $conf["id_empresa"] : parent::get_empresa();
		$filter = isset($conf["filter"]) ? $conf["filter"] : "";
		$limit = isset($conf["limit"]) ? $conf["limit"] : 0;
		$offset = isset($conf["offset"]) ? $conf["offset"] : 10;
		$order = isset($conf["order"]) ? $conf["order"] : "A.nombre ASC";
		
    $sql = "SELECT SQL_CALC_FOUND_ROWS DISTINCT A.* ";
    $sql.= "FROM web_users A ";
		$sql.= "WHERE A.id_empresa = $id_empresa ";
    if (!empty($filter)) $sql.= "AND A.nombre LIKE '%$filter%' ";
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
    
	function find($filter) {
		$this->db->like("nombre",$filter);
    $this->db->or_like("apellido",$filter);
		$query = $this->db->get($this->tabla);
		$result = $query->result();
		$this->db->close();
		return $result;
	}
    
	function get_by_email($email,$id_empresa = 0) {
		if ($id_empresa == 0) $id_empresa = parent::get_empresa();
		$email = trim($email);
		if (empty($email)) return FALSE;
		$sql = "SELECT C.* ";
		$sql.= "FROM web_users C ";
		$sql.= "WHERE C.email = '$email' AND C.id_empresa = $id_empresa ";
		$query = $this->db->query($sql);
		if ($query->num_rows() == 0) return FALSE;
		$row = $query->row(); 
		$this->db->close();
		return $row;
	}

  function get_by_codigo($codigo,$id_empresa = 0) {
    if ($id_empresa == 0) $id_empresa = parent::get_empresa();
    $codigo = trim($codigo);
    if (empty($codigo)) return FALSE;
    $sql = "SELECT C.* ";
    $sql.= "FROM web_users C ";
    $sql.= "WHERE C.codigo = '$codigo' AND C.id_empresa = $id_empresa ";
    $query = $this->db->query($sql);
    if ($query->num_rows() == 0) return FALSE;
    $row = $query->row(); 
    $this->db->close();
    return $row;
  }
	
	function get($id,$id_empresa=0) {
		if ($id_empresa == 0) $id_empresa = parent::get_empresa();
		$sql = "SELECT * FROM web_users WHERE id = $id AND id_empresa = $id_empresa";
		$q = $this->db->query($sql);
		$row = $q->row();
		if ($row !== FALSE) {
			$row->comentarios = array();
			// Obtenemos los comentarios
			$sql = "SELECT EC.*, ";
			$sql.= " DATE_FORMAT(EC.fecha,'%d/%m/%Y') AS fecha, ";
			$sql.= " DATE_FORMAT(EC.fecha,'%H:%i') AS hora, ";
			$sql.= " IF(E.titulo IS NULL,'',E.titulo) AS entrada ";
			$sql.= "FROM not_entradas_comentarios EC ";
			$sql.= " LEFT JOIN not_entradas E ON(E.id = EC.id_entrada) ";
			$sql.= "WHERE EC.id_usuario = $id AND EC.id_empresa = $id_empresa ";
			$sql.= "ORDER BY EC.orden ASC";
			$q = $this->db->query($sql);
			foreach($q->result() as $r) {
				if (!empty($r->path)) $r->path = ((strpos($row->path,"http://")===FALSE)) ? "/sistema/".$row->path : $row->path;
				$row->comentarios[] = $r;
			}
		}
		return $row;
	}		
	
}