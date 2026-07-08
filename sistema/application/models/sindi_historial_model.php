<?php defined('BASEPATH') OR exit('No direct script access allowed');
require_once("abstract_model.php");

class Sindi_Historial_Model extends Abstract_Model {

  function __construct() {
    parent::__construct("sindi_historial","id","nombre ASC");
  }

  function registrar($config = array()) {

    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $id_sindi_empresa = isset($config["id_sindi_empresa"]) ? $config["id_sindi_empresa"] : 0;
    $id_afiliado = isset($config["id_afiliado"]) ? $config["id_afiliado"] : 0;
    $id_titular = isset($config["id_titular"]) ? $config["id_titular"] : 0;
    $evento = isset($config["evento"]) ? $config["evento"] : "";
    $motivo = isset($config["motivo"]) ? $config["motivo"] : "";
    $nivel = isset($config["nivel"]) ? $config["nivel"] : 0;
    $fecha = isset($config["fecha"]) ? $config["fecha"] : date("Y-m-d");
    $fecha_registro = date("Y-m-d H:i:s");
    $id_usuario = isset($config["id_usuario"]) ? $config["id_usuario"] : (isset($_SESSION["id"]) ? $_SESSION["id"] : 0);

    if ($id_afiliado != 0 && $id_titular == 0) {
      // Buscamos el titular
      $sql = "SELECT * FROM sindi_afiliados WHERE id = $id_afiliado AND id_empresa = '$id_empresa'";
      $q = $this->db->query($sql);
      $r = $q->row();
      $sql = "SELECT * FROM sindi_afiliados WHERE codigo = '$r->codigo' AND identificador = '0' AND id_empresa = '$id_empresa' ";
      $q = $this->db->query($sql);
      $r = $q->row();
      $id_titular = $r->id;
    }

    //Cargamos en el historial
    $sql = "INSERT INTO sindi_historial (";
    $sql.= " id_empresa, id_sindi_empresa, id_afiliado, id_titular, evento, motivo, fecha, id_usuario, fecha_registro, nivel ";
    $sql.= ") VALUES (";
    $sql.= " $id_empresa, $id_sindi_empresa, $id_afiliado, $id_titular, '$evento', '$motivo', '$fecha', '$id_usuario', '$fecha_registro', '$nivel' )";
    $this->db->query($sql);
  }

  function buscar_historial_afiliado($config = array()) {

    $id_empresa = (isset($config["id_empresa"])) ? $config["id_empresa"] : parent::get_empresa();
    $id_afiliado = (isset($config["id_afiliado"])) ? $config["id_afiliado"] : "";
    $filter = (isset($config["filter"])) ? $config["filter"] : "";
    $limit = (isset($config["limit"])) ? $config["limit"] : 0;
    $offset = (isset($config["offset"])) ? $config["offset"] : 10;

    $sql = "SELECT SQL_CALC_FOUND_ROWS SH.*, IF(SA.nombre IS NULL,'', SA.nombre) AS nombreafiliado,  IF(SE.nombre IS NULL,'', SE.nombre) AS nombreempresa ";
    $sql.= "FROM sindi_historial SH ";
    $sql.= "LEFT JOIN sindi_afiliados SA ON (SH.id_empresa = SA.id_empresa AND SH.id_afiliado = SA.id) ";
    $sql.= "LEFT JOIN sindi_empresas SE ON (SH.id_empresa = SE.id_empresa AND SH.id_sindi_empresa = SE.id) ";
    $sql.= "WHERE SH.id_titular = '$id_afiliado' ";
    $sql.= "ORDER BY SH.fecha ASC, SH.id_afiliado ASC ";
    $sql.= "LIMIT $limit, $offset ";
    $q = $this->db->query($sql);
    $salida = array();

    foreach($q->result() as $r) {
      $afiliado = $this->get($r->id);
      $afiliado->nombreafiliado = $r->nombreafiliado;
      $afiliado->nombreempresa = $r->nombreempresa;
      $salida[] = $afiliado;
    }

    $q_total = $this->db->query("SELECT FOUND_ROWS() AS total");
    $total = $q_total->row();
    return array(
      "results"=>$salida,
      "total"=>$total->total,
    );
  }

  function buscar_historial_empresa($config = array()) {

    $id_empresa = (isset($config["id_empresa"])) ? $config["id_empresa"] : parent::get_empresa();
    $id_empresa_transporte = (isset($config["id_empresa_transporte"])) ? $config["id_empresa_transporte"] : "";
    $filter = (isset($config["filter"])) ? $config["filter"] : "";
    $limit = (isset($config["limit"])) ? $config["limit"] : 0;
    $offset = (isset($config["offset"])) ? $config["offset"] : 10;

    $sql = "SELECT SQL_CALC_FOUND_ROWS SH.*, IF(SA.nombre IS NULL,'', SA.nombre) AS nombreafiliado, IF(SE.nombre IS NULL,'', SE.nombre) AS nombreempresa ";
    $sql.= "FROM sindi_historial SH ";
    $sql.= "LEFT JOIN sindi_afiliados SA ON (SH.id_empresa = SA.id_empresa AND SH.id_afiliado = SA.id) ";
    $sql.= "LEFT JOIN sindi_empresas SE ON (SH.id_empresa = SE.id_empresa AND SH.id_sindi_empresa = SE.id) ";
    $sql.= "WHERE SH.id_sindi_empresa = '$id_empresa_transporte' ";
    $sql.= "ORDER BY SH.fecha ASC ";
    $sql.= "LIMIT $limit, $offset ";
    $q = $this->db->query($sql);
    $salida = array();

    foreach($q->result() as $r) {
      $afiliado = $this->get($r->id);
      $afiliado->nombreafiliado = $r->nombreafiliado;
      $afiliado->nombreempresa = $r->nombreempresa;
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



}