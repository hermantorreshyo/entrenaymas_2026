<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Objeto_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("clasif_objetos","id");
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
    
	function buscar($conf = array()) {
		
		$id_empresa = parent::get_empresa();
		$id_usuario = isset($conf["id_usuario"]) ? $conf["id_usuario"] : 0;
		
		$sql = "SELECT SQL_CALC_FOUND_ROWS A.*, ";
		$sql.= "IF(P.nombre IS NULL,'',P.nombre) AS cliente ";
		$sql.= "FROM clasif_objetos A ";
		$sql.= "LEFT JOIN clientes P ON (A.id_cliente = P.id) ";
		$sql.= "WHERE 1=1 ";
		$sql.= "AND A.id_empresa = $id_empresa ";
		if (isset($conf["filter"]) && !empty($conf["filter"])) $sql.= "AND A.marca LIKE '%".$conf["filter"]."%' ";
		if (!empty($id_usuario)) $sql.= "AND A.id_usuario = $id_usuario ";
		if (empty($order)) $sql.= "ORDER BY A.fecha DESC ";
		else $sql.= "ORDER BY $order ";
		
		//if ($offset != 0) $sql.= "LIMIT $limit, $offset ";
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
		$sql.= "IF(P.nombre IS NULL,'',P.nombre) AS cliente ";
		$sql.= "FROM clasif_objetos A ";
		$sql.= "LEFT JOIN clientes P ON (A.id_cliente = P.id) ";
		$sql.= "WHERE A.id = $id ";
		$sql.= "AND A.id_empresa = $id_empresa ";
		$q = $this->db->query($sql);
		if ($q->num_rows() == 0) return array();
		$objeto = $q->row();
		
		// Obtenemos las imagenes de ese propiedad
		$sql = "SELECT AI.* FROM clasif_objetos_images AI WHERE AI.id_objeto = $id AND AI.id_empresa = $id_empresa ORDER BY AI.orden ASC";
		$q = $this->db->query($sql);
		$objeto->images = array();
		foreach($q->result() as $r) {
			$objeto->images[] = $r->path;
		}
		return $objeto;
	}
	
	function get_by_codigo($codigo) {
		$id_empresa = parent::get_empresa();
		// Obtenemos los datos del propiedad
		$codigo = (int)$codigo;
		$sql = "SELECT A.*, ";
		$sql.= "IF(P.nombre IS NULL,'',P.nombre) AS cliente ";
		$sql.= "FROM clasif_objetos A ";
		$sql.= "LEFT JOIN clientes P ON (A.id_cliente = P.id) ";
		$sql.= "WHERE A.codigo = '$codigo' AND A.id_empresa = '$id_empresa' ";
		$q = $this->db->query($sql);
		if ($q->num_rows() > 0) {
			$objeto = $q->row();			
		} else {
			$objeto = FALSE;
		}
		return $objeto;
	}
	
	function delete($id) {
		// Controlamos que se este borrando un propiedad que pertenece a la empresa de la session
		$id_empresa = parent::get_empresa();
		if ($id_empresa === FALSE) return;
		$q = $this->db->query("SELECT * FROM clasif_objetos WHERE id = $id AND id_empresa = $id_empresa ");
		if ($q->num_rows()>0) {
			$this->db->query("DELETE FROM clasif_objetos_images WHERE id_objeto = $id");
			$this->db->query("DELETE FROM clasif_objetos WHERE id = $id");
		}
	}

}