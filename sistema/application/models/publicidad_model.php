<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Publicidad_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("not_publicidades","id","id DESC");
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
		$filter = isset($conf["filter"]) ? $conf["filter"] : "";
		$order = isset($conf["order"]) ? $conf["order"] : "A.id DESC ";
		$id_categoria = isset($conf["id_categoria"]) ? $conf["id_categoria"] : 0;
		$id_cliente = isset($conf["id_cliente"]) ? $conf["id_cliente"] : 0;
		$id_vendedor = isset($conf["id_vendedor"]) ? $conf["id_vendedor"] : 0;
		$activo = isset($conf["activo"]) ? $conf["activo"] : -1;
		
		$sql = "SELECT SQL_CALC_FOUND_ROWS A.*, ";
		$sql.= " A.valida_desde AS desde, A.valida_hasta AS hasta, ";
		$sql.= " DATE_FORMAT(A.valida_desde,'%d/%m/%Y %H:%i') AS valida_desde, ";
		$sql.= " DATE_FORMAT(A.valida_hasta,'%d/%m/%Y %H:%i') AS valida_hasta, ";
		$sql.= " IF(A.activo = 0,0,DATEDIFF(NOW(),A.valida_hasta)) AS dias_vencimiento, ";
		$sql.= " IF(TE.nombre IS NULL,'',TE.nombre) AS categoria, ";
		$sql.= " IF(V.nombre IS NULL,'',V.nombre) AS vendedor, ";
		$sql.= " IF(C.nombre IS NULL,'',C.nombre) AS cliente ";
		$sql.= "FROM not_publicidades A ";
		$sql.= "LEFT JOIN not_publicidades_categorias TE ON (A.id_categoria = TE.id) ";
		$sql.= "LEFT JOIN clientes C ON (A.id_cliente = C.id) ";
		$sql.= "LEFT JOIN vendedores V ON (A.id_vendedor = V.id) ";
		$sql.= "WHERE 1=1 ";
		$sql.= "AND A.id_empresa = $id_empresa ";
		if (!empty($filter)) $sql.= "AND A.nombre LIKE '%$filter%' ";
        if (!empty($id_categoria)) $sql.= "AND A.id_categoria = $id_categoria ";
        if (!empty($id_cliente)) $sql.= "AND A.id_cliente = $id_cliente ";
        if (!empty($id_vendedor)) $sql.= "AND A.id_vendedor = $id_vendedor ";
        if ($activo != -1) $sql.= "AND A.activo = $activo ";
		$sql.= "ORDER BY $order ";
		
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
		
		$limit = isset($conf["limit"]) ? $conf["limit"] : 0;
		$offset = isset($conf["offset"]) ? $conf["offset"] : 10;
		$desde = isset($conf["desde"]) ? $conf["desde"] : "";
		$hasta = isset($conf["hasta"]) ? $conf["hasta"] : "";
		
		$id_empresa = parent::get_empresa();
		$sql = "SELECT SQL_CALC_FOUND_ROWS A.*, ";
		$sql.= " IF(TE.nombre IS NULL,'',TE.nombre) AS categoria ";
		$sql.= "FROM not_publicidades A ";
		$sql.= "LEFT JOIN not_publicidades_categorias TE ON (A.id_categoria = TE.id) ";
		$sql.= "WHERE 1=1 ";
		$sql.= "AND A.id_empresa = $id_empresa ";
		if (isset($conf["id_categoria"]) && !empty($conf["id_categoria"])) $sql.= "AND A.id_categoria = ".$conf["id_categoria"]." ";
		if (isset($conf["order"]) && !empty($conf["order"])) $sql.= "ORDER BY ".$conf["order"]." ";
		else $sql.= "ORDER BY A.nombre ASC ";
		$sql.= "LIMIT $limit,$offset ";
		
		$q = $this->db->query($sql);
		$result = $q->result();
		
		$q_total = $this->db->query("SELECT FOUND_ROWS() AS total");
		$total = $q_total->row();		
		
		foreach($result as &$r) {
			
			// Contamos la cantidad de impresiones
			$sql = "SELECT IF(COUNT(*) IS NULL,0,COUNT(*)) AS impresiones ";
			$sql.= "FROM not_publicidades_impresiones ";
			$sql.= "WHERE id_publicidad = $r->id AND id_empresa = $id_empresa ";
			if (!empty($desde)) $sql.= "AND stamp >= '$desde' ";
			if (!empty($hasta)) $sql.= "AND stamp <= '$hasta' ";
			$q1 = $this->db->query($sql);
			$imp = $q1->row();
			$r->impresiones = $imp->impresiones;
			
			// Contamos la cantidad de impresiones
			$sql = "SELECT IF(COUNT(*) IS NULL,0,COUNT(*)) AS clicks ";
			$sql.= "FROM not_publicidades_clicks ";
			$sql.= "WHERE id_publicidad = $r->id AND id_empresa = $id_empresa ";
			if (!empty($desde)) $sql.= "AND stamp >= '$desde' ";
			if (!empty($hasta)) $sql.= "AND stamp <= '$hasta' ";
			$q1 = $this->db->query($sql);
			$imp = $q1->row();
			$r->clicks = $imp->clicks;			
			
			$r->promedio_impresiones_dia = 0;
			$r->costo_impresion = ($r->impresiones > 0) ? ($r->costo / $r->impresiones) : 0;
			$r->costo_click = ($r->clicks > 0) ? ($r->costo / $r->clicks) : 0;
		}
		
		return array(
			"results"=>$result,
			"total"=>$total->total,
		);
	}
	
	function get($id) {
		$id_empresa = parent::get_empresa();
		// Obtenemos los datos del publicidad
		$id = (int)$id;
		$sql = "SELECT A.*, ";
		$sql.= " IF(TE.nombre IS NULL,'',TE.nombre) AS categoria, ";
		$sql.= " IF(V.nombre IS NULL,'',V.nombre) AS vendedor, ";
		$sql.= " IF(C.nombre IS NULL,'',C.nombre) AS cliente ";
		$sql.= "FROM not_publicidades A ";
		$sql.= "LEFT JOIN not_publicidades_categorias TE ON (A.id_categoria = TE.id) ";
		$sql.= "LEFT JOIN clientes C ON (A.id_cliente = C.id) ";
		$sql.= "LEFT JOIN vendedores V ON (A.id_vendedor = V.id) ";
		$sql.= "WHERE A.id = $id ";
		$sql.= "AND A.id_empresa = $id_empresa ";
		$q = $this->db->query($sql);
		if ($q->num_rows() == 0) return array();
		$publicidad = $q->row();
		
		// Obtenemos las imagenes de ese publicidad
		$sql = "SELECT AI.* FROM not_publicidades_images AI WHERE AI.id_publicidad = $id AND AI.id_empresa = $id_empresa ORDER BY AI.orden ASC";
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
		$this->db->query("DELETE FROM not_publicidades_images WHERE id_publicidad = $id");
		$this->db->query("DELETE FROM not_publicidades WHERE id = $id");
	}

}