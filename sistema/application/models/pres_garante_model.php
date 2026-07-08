<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Pres_Garante_Model extends Abstract_Model {

	function __construct() {
		parent::__construct("pres_garantes","id","apellido ASC, nombre ASC");
	}

  function delete($id) {
    $id_empresa = parent::get_empresa();
    $this->db->query("DELETE FROM pres_garantes WHERE id = $id AND id_empresa = $id_empresa");
  }
	
	function get($id,$id_empresa = 0) {
		if (empty($id)) return FALSE;
		if ($id_empresa == 0) $id_empresa = parent::get_empresa();
		$sql = "SELECT C.*, ";
		$sql.= "  DATE_FORMAT(C.fecha_inicial,'%d/%m/%Y') AS fecha_inicial, ";
    $sql.= "  DATE_FORMAT(C.fecha_nac,'%d/%m/%Y') AS fecha_nac, ";
		$sql.= "  IF (L.nombre IS NULL,'',L.nombre) AS localidad ";
		$sql.= "FROM pres_garantes C ";
		$sql.= " LEFT JOIN com_localidades L ON (C.id_localidad = L.id) ";
		$sql.= "WHERE C.id = $id ";
		$sql.= "AND C.id_empresa = $id_empresa ";
		$query = $this->db->query($sql);
		$row = $query->row(); 
    if ($row !== FALSE) {
      $this->load->model("Consulta_Model");
      $res = $this->Consulta_Model->buscar(array(
        "id_empresa"=>$id_empresa,
        "id_contacto"=>$row->id,
        "offset"=>999999,
      ));
      $row->consultas = $res["results"];

      $this->load->model("Pres_Prestamo_Model");
      $res2 = $this->Pres_Prestamo_Model->buscar(array(
        "id_empresa"=>$id_empresa,
        "id_garante"=>$row->id,
        "offset"=>999999,
      ));
      $row->prestamos = $res2["results"];

      // Obtenemos los estados laborales
      $sql = "SELECT EL.*, ";
      $sql.= " IF(EL.fecha_inicio = '0000-00-00','',DATE_FORMAT(EL.fecha_inicio,'%d/%m/%Y')) AS fecha_inicio, ";
      $sql.= " IF(EL.fecha_fin = '0000-00-00','',DATE_FORMAT(EL.fecha_fin,'%d/%m/%Y')) AS fecha_fin ";
      $sql.= "FROM pres_garantes_estados_laborales EL ";
      $sql.= "WHERE EL.id_garante = $id ";
      $sql.= "AND EL.id_empresa = $id_empresa ";
      $sql.= "ORDER BY EL.orden ASC ";
      $qq = $this->db->query($sql);
      $row->estados_laborales = $qq->result();

      // Obtenemos las documentaciones
      $sql = "SELECT EL.*, D.nombre AS documentacion, ";
      $sql.= " IF(EL.fecha = '0000-00-00','',DATE_FORMAT(EL.fecha,'%d/%m/%Y')) AS fecha ";
      $sql.= "FROM pres_garantes_documentaciones EL ";
      $sql.= "INNER JOIN pres_documentacion D ON (EL.id_documentacion = D.id AND EL.id_empresa = D.id_empresa) ";
      $sql.= "WHERE EL.id_garante = $id ";
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
		$limit = isset($params["limit"]) ? $params["limit"] : 0;
		$offset = isset($params["offset"]) ? $params["offset"] : 0;
		$order = (isset($params["order"]) && !empty($params["order"])) ? $params["order"] : "C.nombre ASC ";
		$sql = "SELECT SQL_CALC_FOUND_ROWS C.*, ";
		$sql.= "  DATE_FORMAT(C.fecha_inicial,'%d/%m/%Y') AS fecha_inicial, ";
    $sql.= "  DATE_FORMAT(C.fecha_nac,'%d/%m/%Y') AS fecha_nac, ";
		$sql.= " IF(L.nombre IS NULL,'',L.nombre) AS localidad ";
		$sql.= "FROM pres_garantes C ";
		$sql.= "LEFT JOIN com_localidades L ON (C.id_localidad = L.id) ";
		$sql.= "WHERE C.id_empresa = $id_empresa ";
		if (!empty($filter)) $sql.= "AND ((CONCAT(C.apellido,' ',C.nombre) LIKE '%$filter%') OR (C.documento LIKE '%$filter%')) ";
		$sql.= "ORDER BY $order ";
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