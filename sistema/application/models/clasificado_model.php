<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Clasificado_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("clasificados","id","fecha DESC");
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
	 * Obtiene los entradas a partir de diferentes parametros
	 */
	function buscar($conf = array()) {
		
		$id_empresa = isset($conf["id_empresa"]) ? (!empty($conf["id_empresa"]) ? $conf["id_empresa"] : parent::get_empresa()) : parent::get_empresa();
		$filter = isset($conf["filter"]) ? $conf["filter"] : "";
		$limit = isset($conf["limit"]) ? $conf["limit"] : 0;
		$offset = isset($conf["offset"]) ? $conf["offset"] : 10;
		$activo = isset($conf["activo"]) ? $conf["activo"] : -1;
		$id_categoria = isset($conf["id_categoria"]) ? $conf["id_categoria"] : 0;		
		
		$sql = "SELECT SQL_CALC_FOUND_ROWS DISTINCT A.*, ";
		$sql.= "  IF(fecha='0000-00-00 00:00:00','',DATE_FORMAT(fecha,'%d/%m/%Y %H:%i')) AS fecha, ";
		$sql.= "  IF(activo_desde='0000-00-00 00:00:00','',DATE_FORMAT(activo_desde,'%d/%m/%Y %H:%i')) AS activo_desde, ";
		$sql.= "  IF(activo_hasta='0000-00-00 00:00:00','',DATE_FORMAT(activo_hasta,'%d/%m/%Y %H:%i')) AS activo_hasta, ";
		$sql.= "  IF(U.nombre IS NULL,'',U.nombre) AS usuario, ";
		$sql.= "  IF(P.path IS NULL,'',P.path) AS publicidad_path, ";
		$sql.= "  IF(P.link IS NULL,'',P.link) AS publicidad_link, ";
		$sql.= "  IF(R.nombre IS NULL,'Sin definir',R.nombre) AS categoria ";
		$sql.= "FROM clasificados A ";
		$sql.= "LEFT JOIN clasificados_categorias R ON (A.id_categoria = R.id) ";
		$sql.= "LEFT JOIN com_usuarios U ON (A.id_usuario = U.id) ";
		$sql.= "LEFT JOIN not_publicidades P ON (A.id_publicidad = P.id) ";
		$sql.= "WHERE 1=1 ";
		$sql.= "AND A.id_empresa = $id_empresa ";
		if ($activo != -1) $sql.= "AND A.activo = $activo ";
        if (!empty($filter)) $sql.= "AND A.titulo LIKE '%$filter%' ";
		if (!empty($id_categoria)) $sql.= "AND A.id_categoria = '$id_categoria' ";		
		if (isset($conf["fecha"]) && !empty($conf["fecha"])) $sql.= "AND DATE_FORMAT(A.fecha,'%d/%m/%Y') = '$fecha' ";
		if (isset($conf["order"]) && !empty($conf["order"])) $sql.= "ORDER BY ".$conf["order"]." ";
		else $sql.= "ORDER BY A.activo DESC, A.titulo ASC ";
		$sql.= "LIMIT $limit, $offset ";
		$q = $this->db->query($sql);
		
		$q_total = $this->db->query("SELECT FOUND_ROWS() AS total");
		$total = $q_total->row();

		return array(
			"results"=>$q->result(),
			"total"=>$total->total
		);
	}
	
	function get($id,$id_empresa=0) {
		$id_empresa = ($id_empresa == 0) ? parent::get_empresa() : $id_empresa;
		// Obtenemos los datos del entrada
		$id = (int)$id;
		$sql = "SELECT A.*, ";
		$sql.= "  IF(U.nombre IS NULL,'',U.nombre) AS usuario, ";
		$sql.= "  IF(R.nombre IS NULL,'Sin definir',R.nombre) AS categoria, ";
		$sql.= "  IF(P.path IS NULL,'',P.path) AS publicidad_path, ";
		$sql.= "  IF(P.link IS NULL,'',P.link) AS publicidad_link, ";
		$sql.= "  IF(A.activo_desde='0000-00-00 00:00:00','',DATE_FORMAT(A.activo_desde,'%d/%m/%Y %H:%i')) AS activo_desde, ";
		$sql.= "  IF(A.activo_hasta='0000-00-00 00:00:00','',DATE_FORMAT(A.activo_hasta,'%d/%m/%Y %H:%i')) AS activo_hasta, ";
		$sql.= "  IF(A.fecha='0000-00-00 00:00:00','',DATE_FORMAT(A.fecha,'%d/%m/%Y %H:%i')) AS fecha ";
		$sql.= "FROM clasificados A ";
		$sql.= "LEFT JOIN clasificados_categorias R ON (A.id_categoria = R.id) ";
		$sql.= "LEFT JOIN com_usuarios U ON (A.id_usuario = U.id) ";
		$sql.= "LEFT JOIN not_publicidades P ON (A.id_publicidad = P.id) ";
		$sql.= "WHERE A.id = $id ";
		$sql.= "AND A.id_empresa = $id_empresa ";
		$q = $this->db->query($sql);
		if ($q->num_rows() == 0) return array();
		$entrada = $q->row();
		
		// Obtenemos las imagenes de ese entrada
		$sql = "SELECT AI.* FROM clasificados_images AI WHERE AI.id_clasificado = $id AND AI.id_empresa = $id_empresa ORDER BY AI.orden ASC";
		$q = $this->db->query($sql);
		$entrada->images = array();
		foreach($q->result() as $r) {
			$entrada->images[] = $r->path;
		}
		return $entrada;
	}
	
	function delete($id) {
		// Controlamos que se este borrando un entrada que pertenece a la empresa de la session
		$id_empresa = parent::get_empresa();
		if ($id_empresa === FALSE) return;
		$q = $this->db->query("SELECT * FROM clasificados WHERE id = $id AND id_empresa = $id_empresa ");
		if ($q->num_rows()>0) {
			$this->db->query("DELETE FROM clasificados_comentarios WHERE id_clasificado = $id ");
			$this->db->query("DELETE FROM clasificados_images WHERE id_clasificado = $id");
			$this->db->query("DELETE FROM clasificados WHERE id = $id");
		}
	}

}