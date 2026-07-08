<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Alquiler_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("inm_alquileres","id","id DESC");
	}
	
	function buscar($conf = array()) {
		
		$id_empresa = parent::get_empresa();
		$filter = isset($conf["filter"]) ? $conf["filter"] : "";
		$limit = isset($conf["limit"]) ? $conf["limit"] : 0;
		$offset = isset($conf["offset"]) ? $conf["offset"] : 30;
		$order = isset($conf["order"]) ? $conf["order"] : "A.id DESC ";
		$id_cliente = isset($conf["id_cliente"]) ? $conf["id_cliente"] : 0;
		$id_propiedad = isset($conf["id_propiedad"]) ? $conf["id_propiedad"] : 0;
		$estado = isset($conf["estado"]) ? $conf["estado"] : "A";
		
		$sql = "SELECT SQL_CALC_FOUND_ROWS A.*, ";
		$sql.= " A.fecha_inicio AS desde, A.fecha_fin AS hasta, ";
		$sql.= " DATE_FORMAT(A.fecha_inicio,'%d/%m/%Y') AS fecha_inicio, ";
		$sql.= " DATE_FORMAT(A.fecha_fin,'%d/%m/%Y') AS fecha_fin, ";
		$sql.= " IF(A.estado = 0,0,DATEDIFF(NOW(),A.fecha_fin)) AS dias_vencimiento, ";
		$sql.= " IF(P.nombre IS NULL,'',P.nombre) AS propiedad, ";
		$sql.= " IF(P.codigo IS NULL,'',P.codigo) AS propiedad_codigo, ";
		$sql.= " IF(P.calle IS NULL,'',CONCAT(P.calle,' ',P.altura,' ',P.piso,' ',P.numero)) AS propiedad_direccion, ";
		$sql.= " IF(L.nombre IS NULL,'',L.nombre) AS propiedad_localidad, ";
		$sql.= " IF(P.path IS NULL,'',P.path) AS propiedad_path, ";
		$sql.= " IF(C.nombre IS NULL,'',C.nombre) AS cliente ";
		$sql.= "FROM inm_alquileres A ";
		$sql.= "LEFT JOIN clientes C ON (A.id_cliente = C.id AND A.id_empresa = C.id_empresa) ";
		$sql.= "LEFT JOIN inm_propiedades P ON (A.id_propiedad = P.id AND A.id_empresa = P.id_empresa) ";
		$sql.= "LEFT JOIN com_localidades L ON (P.id_localidad = L.id) ";
		$sql.= "WHERE 1=1 ";
		$sql.= "AND A.id_empresa = $id_empresa ";
		if (!empty($filter)) $sql.= "AND C.nombre LIKE '%$filter%' ";
    if (!empty($id_cliente)) $sql.= "AND A.id_cliente = $id_cliente ";
    if (!empty($id_propiedad)) $sql.= "AND A.id_propiedad = $id_propiedad ";
    if ($estado != -1) $sql.= "AND A.estado = $estado ";
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
		// Obtenemos los datos del alquiler
		$id = (int)$id;
		$sql = "SELECT A.*, ";
		$sql.= " A.fecha_inicio AS desde, A.fecha_fin AS hasta, ";
		$sql.= " DATE_FORMAT(A.fecha_inicio,'%d/%m/%Y') AS fecha_inicio, ";
		$sql.= " DATE_FORMAT(A.fecha_fin,'%d/%m/%Y') AS fecha_fin, ";		
		$sql.= " IF(P.nombre IS NULL,'',P.nombre) AS propiedad, ";
		$sql.= " IF(P.codigo IS NULL,'',P.codigo) AS propiedad_codigo, ";
		$sql.= " IF(L.nombre IS NULL,'',L.nombre) AS propiedad_localidad, ";
		$sql.= " IF(P.path IS NULL,'',P.path) AS propiedad_path, ";
		$sql.= " IF(C.nombre IS NULL,'',C.nombre) AS cliente ";
		$sql.= "FROM inm_alquileres A ";
		$sql.= "LEFT JOIN clientes C ON (A.id_cliente = C.id AND A.id_empresa = C.id_empresa) ";
		$sql.= "LEFT JOIN inm_propiedades P ON (A.id_propiedad = P.id AND A.id_empresa = P.id_empresa) ";
		$sql.= "LEFT JOIN com_localidades L ON (P.id_localidad = L.id) ";
		$sql.= "WHERE A.id = $id ";
		$sql.= "AND A.id_empresa = $id_empresa ";
		$q = $this->db->query($sql);
		if ($q->num_rows() == 0) return array();
		$alquiler = $q->row();
		
		// Obtenemos las cuotas de ese alquiler
		$sql = "SELECT AI.* ";
		$sql.= "FROM inm_alquileres_cuotas AI ";
		$sql.= "WHERE AI.id_alquiler = $id ";
		$sql.= "AND AI.id_empresa = $id_empresa ";
		$sql.= "ORDER BY AI.numero ASC ";
		$q = $this->db->query($sql);
		$alquiler->cuotas = array();
		foreach($q->result() as $r) {
			$alquiler->cuotas[] = $r;
		}

		// Obtenemos las expensas de ese alquiler
		$sql = "SELECT AI.* ";
		$sql.= "FROM inm_alquileres_expensas AI ";
		$sql.= "WHERE AI.id_alquiler = $id ";
		$sql.= "AND AI.id_empresa = $id_empresa ";
		$sql.= "ORDER BY AI.orden ASC ";
		$q = $this->db->query($sql);
		$alquiler->expensas = array();
		foreach($q->result() as $r) {
			$alquiler->expensas[] = $r;
		}

		return $alquiler;
	}
	
	function delete($id) {
		// Controlamos que se este borrando un alquiler que pertenece a la empresa de la session
		$id_empresa = parent::get_empresa();
		if ($id_empresa === FALSE) return;
		$this->db->query("DELETE FROM inm_alquileres_cuotas WHERE id_alquiler = $id AND id_empresa = $id_empresa");
		$this->db->query("DELETE FROM inm_alquileres_expensas WHERE id_alquiler = $id AND id_empresa = $id_empresa");
		$this->db->query("DELETE FROM inm_alquileres WHERE id = $id AND id_empresa = $id_empresa");
	}

}