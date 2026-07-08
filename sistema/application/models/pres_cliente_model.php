<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Pres_Cliente_Model extends Abstract_Model {

	function __construct() {
		parent::__construct("pres_clientes","id","apellido ASC, nombre ASC");
	}

  function get_by_documento($documento) {
    $sql = "SELECT C.*, ";
    $sql.= "  IF (ALM.nombre IS NULL,'',ALM.nombre) AS sucursal ";
    $sql.= " FROM pres_clientes C ";
    $sql.= " LEFT JOIN almacenes ALM ON (ALM.id_empresa = C.id_empresa AND ALM.id = C.id_sucursal) ";
    $sql.= "WHERE C.documento = '$documento' ";
    $q = $this->db->query($sql);
    if ($q->num_rows() == 0) return FALSE;
    else return $q->row();
  }

  function delete($id) {
    $id_empresa = parent::get_empresa();
    $this->db->query("DELETE FROM pres_clientes WHERE id = $id AND id_empresa = $id_empresa");
  }
	
	function get($id,$id_empresa = 0) {
		if (empty($id)) return FALSE;
		if ($id_empresa == 0) $id_empresa = parent::get_empresa();
		$sql = "SELECT C.*, ";
		$sql.= "  DATE_FORMAT(C.fecha_inicial,'%d/%m/%Y') AS fecha_inicial, ";
    $sql.= "  DATE_FORMAT(C.fecha_nac,'%d/%m/%Y') AS fecha_nac, ";
    $sql.= "  IF(C.fecha_ult_operacion = '0000-00-00 00:00:00','',DATE_FORMAT(C.fecha_ult_operacion,'%d/%m/%Y')) AS fecha_ult_operacion, ";
    $sql.= "  IF (ALM.nombre IS NULL,'',ALM.nombre) AS sucursal, ";
		$sql.= "  IF (L.nombre IS NULL,'',L.nombre) AS localidad ";
		$sql.= "FROM pres_clientes C ";
		$sql.= " LEFT JOIN com_localidades L ON (C.id_localidad = L.id) ";
    $sql.= " LEFT JOIN almacenes ALM ON (ALM.id_empresa = C.id_empresa AND ALM.id = C.id_sucursal) ";
		$sql.= "WHERE C.id = $id ";
		$sql.= "AND C.id_empresa = $id_empresa ";
		$query = $this->db->query($sql);
		$row = $query->row(); 
    if ($row !== FALSE) {
      $this->load->model("Consulta_Model");
      $res = $this->Consulta_Model->buscar(array(
        "id_empresa"=>$id_empresa,
        "id_contacto"=>$row->id,
        "buscar_respuestas"=>0,
        "buscar_adjuntos"=>0,
        "offset"=>999999,
      ));
      $row->consultas = $res["results"];

      $this->load->model("Pres_Prestamo_Model");
      $res2 = $this->Pres_Prestamo_Model->buscar(array(
        "id_empresa"=>$id_empresa,
        "id_cliente"=>$row->id,
        "offset"=>999999,
        "fecha"=>date("Y-m-d",strtotime("+1 days")),
      ));
      $row->prestamos = $res2["results"];

      // Obtenemos los estados laborales
      $sql = "SELECT EL.*, ";
      $sql.= " IF(EL.fecha_inicio = '0000-00-00','',DATE_FORMAT(EL.fecha_inicio,'%d/%m/%Y')) AS fecha_inicio, ";
      $sql.= " IF(EL.fecha_fin = '0000-00-00','',DATE_FORMAT(EL.fecha_fin,'%d/%m/%Y')) AS fecha_fin ";
      $sql.= "FROM pres_clientes_estados_laborales EL ";
      $sql.= "WHERE EL.id_cliente = $id ";
      $sql.= "AND EL.id_empresa = $id_empresa ";
      $sql.= "ORDER BY EL.orden ASC ";
      $qq = $this->db->query($sql);
      $row->estados_laborales = $qq->result();

      // Obtenemos las documentaciones
      $sql = "SELECT EL.*, D.nombre AS documentacion, ";
      $sql.= " IF(EL.fecha = '0000-00-00','',DATE_FORMAT(EL.fecha,'%d/%m/%Y')) AS fecha ";
      $sql.= "FROM pres_clientes_documentaciones EL ";
      $sql.= "INNER JOIN pres_documentacion D ON (EL.id_documentacion = D.id AND EL.id_empresa = D.id_empresa) ";
      $sql.= "WHERE EL.id_cliente = $id ";
      $sql.= "AND EL.id_empresa = $id_empresa ";
      $sql.= "ORDER BY EL.fecha DESC, EL.id DESC ";
      $qq = $this->db->query($sql);
      $row->documentaciones = $qq->result();
    }
		return $row;
	}
	
	function buscar($params = array()) {
		$id_empresa = (isset($params["id_empresa"])) ? $params["id_empresa"] : $this->get_empresa();
		$filter = isset($params["filter"]) ? $params["filter"] : "";
    $garante = isset($params["garante"]) ? $params["garante"] : 0;
    $id_sucursal = isset($params["id_sucursal"]) ? $params["id_sucursal"] : 0;
		$limit = isset($params["limit"]) ? $params["limit"] : 0;
		$offset = isset($params["offset"]) ? $params["offset"] : 0;
		$order = (isset($params["order"]) && !empty($params["order"])) ? $params["order"] : "C.nombre ASC ";
    $in_ids = isset($params["in_ids"]) ? $params["in_ids"] : array();
    $not_id = isset($params["not_id"]) ? $params["not_id"] : 0;
    $filtro_especial = isset($params["filtro_especial"]) ? $params["filtro_especial"] : 0;
    $id_plan = isset($params["id_plan"]) ? $params["id_plan"] : 0;
    $estado = isset($params["estado"]) ? $params["estado"] : 0;
    $numero_prestamo = isset($params["numero_prestamo"]) ? $params["numero_prestamo"] : "";
    $fecha_vencimiento = isset($params["fecha_vencimiento"]) ? $params["fecha_vencimiento"] : "";
    $this->load->helper("fecha_helper");
    if (!empty($fecha_vencimiento)) $fecha_vencimiento = fecha_mysql($fecha_vencimiento);

		$sql = "SELECT SQL_CALC_FOUND_ROWS C.*, ";
		$sql.= "  DATE_FORMAT(C.fecha_inicial,'%d/%m/%Y') AS fecha_inicial, ";
    $sql.= "  DATE_FORMAT(C.fecha_nac,'%d/%m/%Y') AS fecha_nac, ";
    $sql.= "  (MATCH(C.nombre_completo) AGAINST ('$filter' IN NATURAL LANGUAGE MODE)) AS relevance, ";
		$sql.= " IF(L.nombre IS NULL,'',L.nombre) AS localidad ";
		$sql.= "FROM pres_clientes C ";
		$sql.= "LEFT JOIN com_localidades L ON (C.id_localidad = L.id) ";
		$sql.= "WHERE C.id_empresa = $id_empresa ";
    if ($garante > -1) $sql.= "AND C.garante = $garante ";
    if (!empty($id_sucursal)) $sql.= "AND C.id_sucursal = $id_sucursal ";
		if (!empty($filter)) $sql.= "AND ( (MATCH(C.nombre_completo) AGAINST ('$filter' IN NATURAL LANGUAGE MODE)) OR (C.documento LIKE '%$filter%')) ";
    if (!empty($not_id)) $sql.= "AND C.id != $not_id ";
    if (!empty($in_ids)) {
      if (is_array($in_ids)) $in_ids_str = implode(",", $in_ids);
      else $in_ids_str = str_replace("-", ",", $in_ids);
      $sql.= "AND C.id IN (".$in_ids_str.")";
    }
    if (!empty($numero_prestamo) || !empty($id_plan) || !empty($estado) || !empty($fecha_vencimiento)) {
      if ($estado == 3) {
        $sql.= "AND NOT EXISTS (SELECT 1 FROM pres_prestamos PP1 ";
        $sql.= "WHERE PP1.id_empresa = C.id_empresa AND PP1.id_cliente = C.id ";
        $sql.= ") ";
        $sql.= "AND NOT EXISTS (SELECT 1 FROM pres_prestamos PP1 ";
        $sql.= "WHERE PP1.id_empresa = C.id_empresa AND PP1.id_garante = C.id ";
        $sql.= ") ";
      } else {
        $sql.= "AND EXISTS (SELECT 1 FROM pres_prestamos PP1 ";
        if (!empty($fecha_vencimiento)) $sql.= "INNER JOIN pres_prestamos_cuotas PPC ON (PP1.id_empresa = PPC.id_empresa AND PP1.id = PPC.id_prestamo) ";
        $sql.= "WHERE PP1.id_empresa = C.id_empresa AND PP1.id_cliente = C.id ";
        if (!empty($numero_prestamo)) $sql.= "AND PP1.numero = '$numero_prestamo' ";
        if (!empty($id_plan)) $sql.= "AND PP1.id_plan = '$id_plan' ";
        if ($estado == 1) $sql.= "AND PP1.cantidad_cuotas_pagas < PP1.cantidad_cuotas ";
        else if ($estado == 2) $sql.= "AND PP1.cantidad_cuotas_pagas = PP1.cantidad_cuotas ";
        if (!empty($fecha_vencimiento)) $sql.= "AND PPC.fecha_vencimiento = '$fecha_vencimiento' ";
        $sql.= ") ";
      }
    }

    if ($filtro_especial == 1) {

      // HAY QUE BUSCAR LOS QUE TIENEN HABILITADO PARA PEDIR UN PRESTAMO PARALELO

      $sql.= "AND EXISTS ( ";
      $sql.= " SELECT 1 FROM pres_prestamos PP WHERE C.id = PP.id_cliente AND C.id_empresa = PP.id_empresa ";
      $sql.= " AND PP.cantidad_cuotas_pagas < PP.cantidad_cuotas "; // Si no esta terminado
      $sql.= " AND PP.deuda_vencida = 0 ";
      $sql.= " AND PP.cantidad_cuotas_pagas > 1 ";
      $sql.= " AND IF(PP.cantidad_cuotas >= 6,";
      $sql.= "   IF(PP.cantidad_cuotas_pagas >= 4,1,0),";
      $sql.= "   IF((PP.cantidad_cuotas_pagas / PP.cantidad_cuotas) >= 0.5,1,0) ";
      $sql.= "  ) = 1 ";
      $sql.= ") ";
      
      // Solamente que tenga un unico prestamo activo
      $sql.= "AND ( ";
      $sql.= " SELECT COUNT(*) FROM pres_prestamos PP WHERE C.id = PP.id_cliente AND C.id_empresa = PP.id_empresa ";
      $sql.= " AND PP.cantidad_cuotas_pagas < PP.cantidad_cuotas ";
      $sql.= ") = 1 ";

    } else if ($filtro_especial == 2) {

      // Tiene que tener un unico prestamo vigente
      $sql.= "AND EXISTS ( ";
      $sql.= " SELECT 1 FROM pres_prestamos PP WHERE C.id = PP.id_cliente AND C.id_empresa = PP.id_empresa ";
      $sql.= " AND PP.cantidad_cuotas_pagas < PP.cantidad_cuotas "; // Si no esta terminado
      $sql.= " AND PP.deuda_vencida = 0 ";
      $sql.= "AND (CASE ";
      $sql.= " WHEN (PP.cantidad_cuotas = 3 AND PP.cantidad_cuotas_pagas >= 2) THEN TRUE ";
      $sql.= " WHEN (PP.cantidad_cuotas = 4 AND PP.cantidad_cuotas_pagas >= 3) THEN TRUE ";
      $sql.= " WHEN (PP.cantidad_cuotas = 5 AND PP.cantidad_cuotas_pagas >= 4) THEN TRUE ";
      $sql.= " WHEN (PP.cantidad_cuotas = 6 AND PP.cantidad_cuotas_pagas >= 4) THEN TRUE ";
      $sql.= " WHEN (PP.cantidad_cuotas = 7 AND PP.cantidad_cuotas_pagas >= 5) THEN TRUE ";
      $sql.= " WHEN (PP.cantidad_cuotas = 8 AND PP.cantidad_cuotas_pagas >= 6) THEN TRUE ";
      $sql.= " WHEN (PP.cantidad_cuotas = 9 AND PP.cantidad_cuotas_pagas >= 7) THEN TRUE ";
      $sql.= " WHEN (PP.cantidad_cuotas = 10 AND PP.cantidad_cuotas_pagas >= 7) THEN TRUE ";
      $sql.= " WHEN (PP.cantidad_cuotas = 11 AND PP.cantidad_cuotas_pagas >= 8) THEN TRUE ";
      $sql.= " ELSE FALSE END) ";
      $sql.= ") ";

      // Y que no tenga deuda
      $sql.= "AND ( ";
      $sql.= " SELECT SUM(PP.deuda_vencida) FROM pres_prestamos PP WHERE C.id = PP.id_cliente AND C.id_empresa = PP.id_empresa ";
      $sql.= " AND PP.cantidad_cuotas_pagas < PP.cantidad_cuotas ";
      $sql.= ") = 0 ";

    } else if ($filtro_especial == 3) {
      $sql.= "AND C.estudio = 1 ";
    }
    if (!empty($filter)) $sql.= "ORDER BY relevance DESC ";
		else $sql.= "ORDER BY $order ";
		if (!empty($offset)) $sql.= "LIMIT $limit, $offset ";
    $q = $this->db->query($sql);
    $q_total = $this->db->query("SELECT FOUND_ROWS() AS total");
    $total = $q_total->row();
		return array(
      "results"=>$q->result(),
      "total"=>$total->total,
      "sql"=>$sql,
		);	
	}    
}