<?php defined('BASEPATH') OR exit('No direct script access allowed');
require_once("abstract_model.php");

class Sindi_Consulta_Model extends Abstract_Model {
 
  function __construct() {
    parent::__construct("sindi_consultas","id","numero DESC");
  }

  function save($data) {

    $id_consulta = parent::save($data);

    if ($data->anulada == 0) {
      // Actualizamos el numero
      $sql = "SELECT MAX(numero) AS numero FROM sindi_consultas WHERE id_empresa = $data->id_empresa ";
      $q = $this->db->query($sql);
      $r = $q->row();
      $maximo = (is_null($r->numero) ? 1 : ($r->numero + 1));
      $sql = "UPDATE sindi_consultas SET numero = $maximo WHERE id_empresa = $data->id_empresa AND id = $id_consulta ";
      $this->db->query($sql);
    }

    return $id_consulta;
  }


 function buscar($config = array()) {

    $id_empresa = (isset($config["id_empresa"])) ? $config["id_empresa"] : parent::get_empresa();
    $codigo = (isset($config["codigo"])) ? $config["codigo"] : "";
    $id_afiliado = (isset($config["id_afiliado"])) ? $config["id_afiliado"] : 0;
    $limit = (isset($config["limit"])) ? $config["limit"] : 0;
    $offset = (isset($config["offset"])) ? $config["offset"] : 10;

    $sql = "SELECT SQL_CALC_FOUND_ROWS SC.*, IF(CE.nombre IS NULL,'', CE.nombre) AS condicionespecial, IF(SA.nombre IS NULL,'', SA.nombre) AS nombreafiliado, IF(SA.codigo IS NULL,'', SA.codigo) AS codigoafiliado, IF(SA.identificador IS NULL,'', SA.identificador) AS identificadorafiliado ";
    $sql.= "FROM sindi_consultas SC ";
    $sql.= "LEFT JOIN sindi_condiciones_especiales CE ON (SC.id_empresa = CE.id_empresa AND SC.id_condicion_especial = CE.id) ";
    $sql.= "LEFT JOIN sindi_afiliados SA ON (SC.id_empresa = SA.id_empresa AND SC.id_paciente = SA.id) ";  
    $sql.= "WHERE SC.id_empresa = '$id_empresa' ";
    if (!empty($codigo)) $sql.= "AND SC.codigo = '$codigo' ";
    if (!empty($id_afiliado)) $sql.= "AND SC.id_paciente = '$id_afiliado' ";
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
    $sql = "SELECT SC.*, SA.domicilio AS domicilio, SA.nombre AS nombreafiliado, SA.codigo AS codigoafiliado, SA.identificador AS identificadorafiliado, CE.nombre AS condicionespecial ";
    $sql.= "FROM sindi_consultas SC ";
    $sql.= "INNER JOIN sindi_afiliados SA ON (SC.id_empresa AND SA.id_empresa AND SC.id_paciente = SA.id) ";
    $sql.= "LEFT JOIN sindi_condiciones_especiales CE ON (SC.id_empresa = CE.id_empresa AND SC.id_condicion_especial = CE.id) ";
    $sql.= "WHERE SC.id_empresa = $id_empresa AND SC.id = $id";
    $q = $this->db->query($sql);
    return $q->row();
  }
}