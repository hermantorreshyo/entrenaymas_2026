<?php defined('BASEPATH') OR exit('No direct script access allowed');
require_once("abstract_model.php");

class Sindi_Reintegro_Model extends Abstract_Model {

  function __construct() {
    parent::__construct("sindi_reintegros","id","numero DESC");
  }

  function save($data) {

    $id_reintegro = parent::save($data);

    if ($data->anulada == 0) {
      // Actualizamos el numero
      $sql = "SELECT MAX(numero) AS numero FROM sindi_reintegros WHERE id_empresa = $data->id_empresa ";
      $q = $this->db->query($sql);
      $r = $q->row();
      $maximo = (is_null($r->numero) ? 1 : ($r->numero + 1));
      $sql = "UPDATE sindi_reintegros SET numero = $maximo WHERE id_empresa = $data->id_empresa AND id = $id_reintegro ";
      $this->db->query($sql);
    }

    return $id_reintegro;
  }

  function buscar($config = array()) {

    $id_empresa = (isset($config["id_empresa"])) ? $config["id_empresa"] : parent::get_empresa();
    $codigo = (isset($config["codigo"])) ? $config["codigo"] : "";
    $id_afiliado = (isset($config["id_afiliado"])) ? $config["id_afiliado"] : 0;
    $limit = (isset($config["limit"])) ? $config["limit"] : 0;
    $offset = (isset($config["offset"])) ? $config["offset"] : 10;

    $sql = "SELECT SQL_CALC_FOUND_ROWS SR.*, IF(SA.nombre IS NULL,'', SA.nombre) AS nombreafiliado, IF(SA.codigo IS NULL,'', SA.codigo) AS codigoafiliado, IF(SA.identificador IS NULL,'', SA.identificador) AS identificadorafiliado, IF(STR.nombre IS NULL,'', STR.nombre) AS nombrereintegro ";
    $sql.= "FROM sindi_reintegros SR ";
    $sql.= "LEFT JOIN sindi_tipos_reintegros STR ON (SR.id_empresa = STR.id_empresa AND SR.id_tipo_reintegro = STR.id) ";
    $sql.= "LEFT JOIN sindi_afiliados SA ON (SR.id_empresa = SA.id_empresa AND SR.id_afiliado = SA.id) ";
    $sql.= "WHERE SR.id_empresa = '$id_empresa' ";
    if (!empty($codigo)) $sql.= "AND codigo = '$codigo' ";
    if (!empty($id_afiliado)) $sql.= "AND SR.id_paciente = '$id_afiliado' ";
    $sql.= "ORDER BY numero DESC ";
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

  function find($filter) {
    $id_empresa = parent::get_empresa();
    $this->db->where("id_empresa",$id_empresa);
    $this->db->like("nombre",$filter);
    $query = $this->db->get($this->tabla);
    $result = $query->result();
    $this->db->close();
    return $result;
  }

  function get($id) {
    $id_empresa = parent::get_empresa();
    $sql = "SELECT SR.*, SA.domicilio AS domicilio, SA.nombre AS nombreafiliado, ";
    $sql.= " SA.codigo AS codigoafiliado, SA.identificador AS identificadorafiliado, ";
    $sql.= " IF (STR.nombre IS NULL,'',STR.nombre) AS nombrereintegro ";
    $sql.= "FROM sindi_reintegros SR ";
    $sql.= "INNER JOIN sindi_afiliados SA ON (SR.id_empresa AND SA.id_empresa AND SR.id_afiliado = SA.id) ";
    $sql.= "LEFT JOIN sindi_tipos_reintegros STR ON (SR.id_empresa AND STR.id_empresa AND SR.id_tipo_reintegro = STR.id) ";
    $sql.= "WHERE SR.id_empresa = $id_empresa AND SR.id = $id ";
    $q = $this->db->query($sql);
    return $q->row();
  }

}