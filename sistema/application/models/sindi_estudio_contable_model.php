<?php defined('BASEPATH') OR exit('No direct script access allowed');
require_once("abstract_model.php");

class Sindi_Estudio_Contable_Model extends Abstract_Model {

  function __construct() {
    parent::__construct("sindi_estudios_contables","id","nombre ASC");
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
    return parent::save($data);
  }

  function buscar($config = array()) {

    $id_empresa = (isset($config["id_empresa"])) ? $config["id_empresa"] : parent::get_empresa();
    $codigo = (isset($config["codigo"])) ? $config["codigo"] : "";
    $filter = (isset($config["filter"])) ? $config["filter"] : "";
    $limit = (isset($config["limit"])) ? $config["limit"] : 0;
    $offset = (isset($config["offset"])) ? $config["offset"] : 10;

    $sql = "SELECT SQL_CALC_FOUND_ROWS SEC.*, IF(L.nombre IS NULL,'', L.nombre) AS localidad ";
    $sql.= "FROM sindi_estudios_contables SEC ";
    $sql.= "LEFT JOIN sindi_localidades L ON (SEC.id_empresa = L.id_empresa AND SEC.id_localidad = L.id) ";
    $sql.= "WHERE SEC.id_empresa = '$id_empresa' ";
    if (!empty($codigo)) $sql.= "AND SEC.codigo = '$codigo' ";
    if (!empty($filter)) $sql.= "AND SEC.nombre LIKE '%$filter%' ";
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

  function get($id) {

    $id_empresa = parent::get_empresa();
    $sql = "SELECT SEC.*, IF(L.nombre IS NULL,'', L.nombre) AS localidad ";
    $sql.= "FROM sindi_estudios_contables SEC ";
    $sql.= "LEFT JOIN sindi_localidades L ON (SEC.id_empresa = L.id_empresa AND SEC.id_localidad = L.id) ";
    $sql.= "WHERE SEC.id_empresa = $id_empresa AND SEC.id = $id";
    $q = $this->db->query($sql);
    $estudio = $q->row();

    $estudio->empresas_activas = array();
    $sql = "SELECT SE.* ";
    $sql.= "FROM sindi_empresas SE ";
    $sql.= "WHERE SE.id_empresa = $id_empresa ";
    $sql.= "AND id_estudio_contable = $estudio->id ";
    //$sql.= "AND SE.estado > 0 ";
    $sql.= "ORDER BY SE.nombre ASC ";
    $q = $this->db->query($sql);
    foreach($q->result() as $rr) {
      $estudio->empresas_activas[] = $rr;
    }
    return $estudio;
  }
}