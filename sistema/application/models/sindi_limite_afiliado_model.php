<?php defined('BASEPATH') OR exit('No direct script access allowed');
require_once("abstract_model.php");

class Sindi_Limite_Afiliado_Model extends Abstract_Model {

  function __construct() {
    parent::__construct("sindi_limites_afiliados","id","id_tipo_practica ASC");
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

  function buscar($config = array()) {

    $id_empresa = (isset($config["id_empresa"])) ? $config["id_empresa"] : parent::get_empresa();
    $codigo = (isset($config["codigo"])) ? $config["codigo"] : "";
    $filter = (isset($config["filter"])) ? $config["filter"] : "";
    $tipo = (isset($config["tipo"])) ? $config["tipo"] : "";
    $limit = (isset($config["limit"])) ? $config["limit"] : 0;
    $offset = (isset($config["offset"])) ? $config["offset"] : 10;

    $sql = "SELECT SQL_CALC_FOUND_ROWS SLA.*, IF(SA.nombre IS NULL,'', SA.nombre) AS nombreafiliado, IF(STP.nombre IS NULL,'', STP.nombre) AS nombrepractica ";
    $sql.= "FROM sindi_limites_afiliados SLA ";
    $sql.= "LEFT JOIN sindi_afiliados SA ON (SLA.id_empresa = SA.id_empresa AND SLA.id_afiliado = SA.id) ";
    $sql.= "LEFT JOIN sindi_tipos_practicas STP ON (SLA.id_empresa = STP.id_empresa AND SLA.id_tipo_practica = STP.id) ";
    $sql.= "WHERE SLA.id_empresa = '$id_empresa' ";
    if (!empty($filter)) $sql.= "AND SA.nombre LIKE '%$filter%' ";
    if (!empty($codigo)) $sql.= "AND SLA.codigo = '$codigo' ";
    if (!empty($tipo)) $sql.= "AND SLA.tipo = '$tipo' ";
    if ($tipo == 1 || $tipo == 2) $sql.= "AND SLA.cantidad > 2 ";
    else if ($tipo == 3 || $tipo == 4) $sql.= "AND SLA.cantidad > 2 ";
    $sql.= "ORDER BY SA.nombre ";
    $sql.= "LIMIT $limit, $offset ";
    $q = $this->db->query($sql);
    $salida = array();

    foreach($q->result() as $r) {
      $salida[] = $r;
    }

    $q_total = $this->db->query("SELECT FOUND_ROWS() AS total");
    $total = $q_total->row();

    return array(
      "results"=>$salida,
      "total"=>$total->total,
    );
  }

}