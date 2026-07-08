<?php defined('BASEPATH') OR exit('No direct script access allowed');
require_once("abstract_model.php");

class Sindi_Empresa_Model extends Abstract_Model {

	function __construct() {
		parent::__construct("sindi_empresas","id","nombre ASC");
    $this->load->model("Sindi_Historial_Model");       
	}

	function buscar($config = array()) {

		$id_empresa = (isset($config["id_empresa"])) ? $config["id_empresa"] : parent::get_empresa();
		$limit = (isset($config["limit"])) ? $config["limit"] : 0;
		$offset = (isset($config["offset"])) ? $config["offset"] : 10;
    $filter = (isset($config["filter"])) ? $config["filter"] : "";

    $sql = "SELECT SQL_CALC_FOUND_ROWS SE.*, IF(L.nombre IS NULL,'', L.nombre) AS localidad ";
    $sql.= "FROM sindi_empresas SE ";
    $sql.= "LEFT JOIN sindi_localidades L ON (SE.id_empresa = L.id_empresa AND SE.id_localidad = L.id) ";
    $sql.= "WHERE SE.id_empresa = '$id_empresa' ";
    if (!empty($filter)) {
      if (is_numeric($filter)) $sql.= "AND SE.codigo = '$filter' ";
      else $sql.= "AND SE.nombre LIKE '%$filter%' ";
    }
    $sql.= "LIMIT $limit, $offset ";
    $q = $this->db->query($sql);
    $salida = array();

    foreach($q->result() as $r) {
      $afiliado = $this->get($r->id);
      $salida[] = $afiliado;
    }

    $q_total = $this->db->query("SELECT FOUND_ROWS() AS total");
    $total = $q_total->row();
    return array(
      "results"=>$salida,
      "total"=>$total->total,
    );
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

	function save($data) {
		$data->nombre = ucwords(strtolower($data->nombre));
		$data->domicilio = ucwords(strtolower($data->domicilio));
		$data->titular1 = ucwords(strtolower($data->titular1));
		$data->titular2 = ucwords(strtolower($data->titular2));
		$data->titular3 = ucwords(strtolower($data->titular3));
    $fecha_alta = $data->fecha_alta;
    $es_nuevo = (!isset($data->id) || $data->id == 0);
    $id_empresa = parent::get_empresa();    

    if ($data->id == 0) {
      $sql = "SELECT * FROM sindi_empresas WHERE subzona = '$data->subzona' AND codigo = '$data->codigo' AND identificador = '$data->identificador' ";
      $quest = $this->db->query($sql);
      if ($quest->num_rows()>0) {
        $this->send_error("La subzona ".$data->subzona." con el codigo ".$data->codigo." y el identificador ".$data->identificador." ya se encuentra en uso.");
      }
    }
  	$id = parent::save($data);
    if ($es_nuevo) {
      $this->Sindi_Historial_Model->registrar(array(
        "id_sindi_empresa"=>$id,
        "evento"=>"Alta de la Empresa en Sistema",
        "motivo"=>"Carga Inicial",
      ));  
    }
    return $id;
  }

  function get($id) {

    $id_empresa = parent::get_empresa();
    $sql = "SELECT SE.*, IF(L.nombre IS NULL,'', L.nombre) AS localidad, IF(SEC.nombre IS NULL,'', SEC.nombre) AS estudiocontable ";
    $sql.= "FROM sindi_empresas SE ";
    $sql.= "LEFT JOIN sindi_localidades L ON (SE.id_empresa = L.id_empresa AND SE.id_localidad = L.id) ";
    $sql.= "LEFT JOIN sindi_estudios_contables SEC ON (SE.id_empresa = SEC.id_empresa AND SE.id_estudio_contable = SEC.id) ";
    $sql.= "WHERE SE.id_empresa = $id_empresa AND SE.id = $id";
    $q = $this->db->query($sql);
    $empresa = $q->row();

    $empresa->afiliados_activos = array();
    

    /* $sql = "SELECT SA.*, SAE.fecha_ingreso AS fecha_ingreso_empresa, SAE.fecha_baja AS fecha_baja_empresa, ";
    $sql.= " IF(TA.nombre IS NULL,'',TA.nombre) AS tipo_afiliado ";
    $sql.= "FROM sindi_afiliados SA ";
    $sql.= "INNER JOIN sindi_afiliados_empresas SAE ON (SA.id_empresa = SAE.id_empresa AND SA.id = SAE.id_afiliado) ";
    $sql.= "LEFT JOIN sindi_tipos_afiliados TA ON (SA.id_empresa = TA.id_empresa AND SA.id_tipo_afiliado = TA.id) ";
    $sql.= "WHERE SAE.id_empresa = $id_empresa ";
    $sql.= "AND SAE.id_sindi_empresa = $empresa->id ";
    $sql.= "AND SA.id_empresa_transporte = $empresa->id ";
    $sql.= "AND SAE.fecha_ingreso != '0000-00-00' AND SAE.fecha_baja = '0000-00-00' ";
    //$sql.= "ORDER BY SAE.fecha_ingreso DESC ";
    $sql.= "ORDER BY SA.nombre ASC "; */

    $sql = "SELECT SA.* ";
    $sql.= "FROM sindi_afiliados SA WHERE id_empresa = $id_empresa ";
    $sql.= "AND id_empresa_transporte = '$empresa->id' ";
    $sql.= "ORDER BY nombre ASC ";


    $q = $this->db->query($sql);
    foreach($q->result() as $rr) {
      $empresa->afiliados_activos[] = $rr;
    }

    $empresa->afiliados_inactivos = array();
    $sql = "SELECT SA.*, SAE.fecha_ingreso AS fecha_ingreso_empresa, SAE.fecha_baja AS fecha_baja_empresa, ";
    $sql.= " IF(TA.nombre IS NULL,'',TA.nombre) AS tipo_afiliado ";
    $sql.= "FROM sindi_afiliados SA ";
    $sql.= "INNER JOIN sindi_afiliados_empresas SAE ON (SA.id_empresa = SAE.id_empresa AND SA.id = SAE.id_afiliado) ";
    $sql.= "LEFT JOIN sindi_tipos_afiliados TA ON (SA.id_empresa = TA.id_empresa AND SA.id_tipo_afiliado = TA.id) ";
    $sql.= "WHERE SAE.id_empresa = $id_empresa ";
    $sql.= "AND SAE.id_sindi_empresa = $empresa->id ";
    $sql.= "AND SAE.fecha_baja != '0000-00-00' ";
    $sql.= "ORDER BY SAE.fecha_baja DESC ";
    $q = $this->db->query($sql);
    foreach($q->result() as $rr) {
      $empresa->afiliados_inactivos[] = $rr;
    }

    $empresa->historial = array();
    $sql = "SELECT SQL_CALC_FOUND_ROWS SH.*, IF(SA.nombre IS NULL,'', SA.nombre) AS nombreafiliado,  IF(SE.nombre IS NULL,'', SE.nombre) AS nombreempresa ";
    $sql.= "FROM sindi_historial SH ";
    $sql.= "LEFT JOIN sindi_afiliados SA ON (SH.id_empresa = SA.id_empresa AND SH.id_afiliado = SA.id) ";
    $sql.= "LEFT JOIN sindi_empresas SE ON (SH.id_empresa = SE.id_empresa AND SH.id_sindi_empresa = SE.id) ";
    $sql.= "WHERE SH.id_sindi_empresa = '$empresa->id' ";
    $sql.= "ORDER BY SH.fecha ASC ";  
    $q = $this->db->query($sql);
    foreach($q->result() as $rr) {
      $empresa->historial[] = $rr;
    }

    return $empresa;
  }

  function get_empleados($config = array()) {

    $limit = isset($config["limit"]) ? $config["limit"] : 0;
    $offset = isset($config["offset"]) ? $config["offset"] : 10;
    $id_empresa_transporte = isset($config["id_empresa_transporte"]) ? $config["id_empresa_transporte"] : 0;
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $sql = "SELECT SQL_CALC_FOUND_ROWS AE.*, E.nombre ";
    $sql.= "FROM sindi_afiliados_empresas AE ";
    $sql.= "INNER JOIN sindi_empresas E ON (AE.id_empresa = E.id_empresa AND AE.id_sindi_empresa = E.id) ";
    $sql.= "WHERE AE.id_sindi_empresa = $id_empresa_transporte ";
    $sql.= "AND AE.id_empresa = $id_empresa ";
    $sql.= "ORDER BY AE.id DESC ";
    if (!empty($offset)) $sql.= "LIMIT $limit, $offset ";
    $q = $this->db->query($sql);

    $q_total = $this->db->query("SELECT FOUND_ROWS() AS total");
    $total = $q_total->row();

    return array(
      "results"=>$q->result(),
      "total"=>$total->total,
    );
  }

}