<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Auto_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("veh_autos","id");
	}
	
	function find($filter) {
		$id_empresa = parent::get_empresa();
		$this->db->where("id_empresa",$id_empresa);
		$this->db->like("marca",$filter);
		$this->db->or_like("modelo",$filter);
		$query = $this->db->get($this->tabla);
		$result = $query->result();
		$this->db->close();
		return $result;
	}
    
	function buscar($conf = array()) {
		
		$id_empresa = parent::get_empresa();
    $filter = isset($conf["filter"]) ? $conf["filter"] : "";
    $limit = isset($conf["limit"]) ? $conf["limit"] : 0;
    $offset = isset($conf["offset"]) ? $conf["offset"] : 10;
    $order = isset($conf["order"]) ? $conf["order"] : "";
		
		$sql = "SELECT SQL_CALC_FOUND_ROWS A.*, ";
    $sql.= "IF(A.valido_hasta='0000-00-00','',DATE_FORMAT(A.valido_hasta,'%d/%m/%Y')) AS valido_hasta, ";
		$sql.= "IF(P.nombre IS NULL,'',P.nombre) AS cliente, ";
		$sql.= "IF(TI.nombre IS NULL,'',TI.nombre) AS tipo ";
		$sql.= "FROM veh_autos A ";
		$sql.= "LEFT JOIN veh_tipos TI ON (A.id_tipo = TI.id) ";
		$sql.= "LEFT JOIN clientes P ON (A.id_cliente = P.id) ";
		$sql.= "WHERE 1=1 ";
		$sql.= "AND A.id_empresa = $id_empresa ";
		if (!empty($filter)) $sql.= "AND (A.marca LIKE '%$filter%' OR A.titulo LIKE '%$filter%' OR P.nombre LIKE '%$filter%') ";
		if (empty($order)) $sql.= "ORDER BY A.fecha DESC ";
		else $sql.= "ORDER BY $order ";
		if ($offset != 0) $sql.= "LIMIT $limit, $offset ";
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
		// Obtenemos los datos del propiedad
		$id = (int)$id;
		$sql = "SELECT A.*, ";
    $sql.= "IF(A.valido_hasta='0000-00-00','',DATE_FORMAT(A.valido_hasta,'%d/%m/%Y')) AS valido_hasta, ";
		$sql.= "IF(P.nombre IS NULL,'',P.nombre) AS cliente, ";
		$sql.= "IF(TI.nombre IS NULL,'',TI.nombre) AS tipo ";
		$sql.= "FROM veh_autos A ";
		$sql.= "LEFT JOIN veh_tipos TI ON (A.id_tipo = TI.id) ";
		$sql.= "LEFT JOIN clientes P ON (A.id_cliente = P.id) ";
		$sql.= "WHERE A.id = $id ";
		$sql.= "AND A.id_empresa = $id_empresa ";
		$q = $this->db->query($sql);
		if ($q->num_rows() == 0) return array();
		$auto = $q->row();
		
		// Obtenemos las imagenes de ese propiedad
		$sql = "SELECT AI.* FROM veh_autos_images AI WHERE AI.id_auto = $id AND AI.id_empresa = $id_empresa ORDER BY AI.orden ASC";
		$q = $this->db->query($sql);
		$auto->images = array();
		foreach($q->result() as $r) {
			$auto->images[] = $r->path;
		}
		return $auto;
	}
	
	function get_by_codigo($codigo) {
		$id_empresa = parent::get_empresa();
		// Obtenemos los datos del propiedad
		$codigo = (int)$codigo;
		$sql = "SELECT A.*, ";
    $sql.= "IF(A.valido_hasta='0000-00-00','',DATE_FORMAT(A.valido_hasta,'%d/%m/%Y')) AS valido_hasta, ";
		$sql.= "IF(P.nombre IS NULL,'',P.nombre) AS cliente, ";
		$sql.= "IF(TI.nombre IS NULL,'',TI.nombre) AS tipo ";
		$sql.= "FROM veh_autos A ";
		$sql.= "LEFT JOIN veh_tipos TI ON (A.id_tipo = TI.id) ";
		$sql.= "LEFT JOIN clientes P ON (A.id_cliente = P.id) ";
		$sql.= "WHERE A.codigo = '$codigo' AND A.id_empresa = '$id_empresa' ";
		$q = $this->db->query($sql);
		if ($q->num_rows() > 0) {
			$auto = $q->row();			
		} else {
			$auto = FALSE;
		}
		return $auto;
	}
	
	function delete($id) {
		// Controlamos que se este borrando un propiedad que pertenece a la empresa de la session
		$id_empresa = parent::get_empresa();
		if ($id_empresa === FALSE) return;
		$q = $this->db->query("SELECT * FROM veh_autos WHERE id = $id AND id_empresa = $id_empresa ");
		if ($q->num_rows()>0) {
			$this->db->query("DELETE FROM veh_autos_images WHERE id_auto = $id");
			$this->db->query("DELETE FROM veh_autos WHERE id = $id");
		}
	}

}