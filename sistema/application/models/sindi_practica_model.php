<?php defined('BASEPATH') OR exit('No direct script access allowed');
require_once("abstract_model.php");

class Sindi_Practica_Model extends Abstract_Model {

	function __construct() {
		parent::__construct("sindi_practicas","id","numero ASC");
	}

  function save($data) {
    $items = $data->items;
    $id_practica = parent::save($data);

    if ($data->anulada == 0) {

      $this->db->query("DELETE FROM sindi_practicas_items WHERE id_empresa = $data->id_empresa AND id_practica = $id_practica");
      foreach($items as $item) {
        $sql = "INSERT INTO sindi_practicas_items (";
        $sql.= "id_empresa,id_practica,id_nomenclador,cantidad,importe_unitario";
        $sql.= ") VALUES(";
        $sql.= "$data->id_empresa,$id_practica,$item->id_nomenclador,$item->cantidad,$item->importe_unitario) ";
        $this->db->query($sql);
      }

      // Actualizamos el numero
      $sql = "SELECT MAX(numero) AS numero FROM sindi_practicas WHERE id_empresa = $data->id_empresa ";
      $q = $this->db->query($sql);
      $r = $q->row();
      $maximo = (is_null($r->numero) ? 1 : ($r->numero + 1));
      $sql = "UPDATE sindi_practicas SET numero = $maximo WHERE id_empresa = $data->id_empresa AND id = $id_practica ";
      $this->db->query($sql);
    }

    return $id_practica;
  }

	function buscar($config = array()) {

		$id_empresa = (isset($config["id_empresa"])) ? $config["id_empresa"] : parent::get_empresa();
		$codigo = (isset($config["codigo"])) ? $config["codigo"] : "";
    $id_afiliado = (isset($config["id_afiliado"])) ? $config["id_afiliado"] : 0;
		$limit = (isset($config["limit"])) ? $config["limit"] : 0;
		$offset = (isset($config["offset"])) ? $config["offset"] : 10;

    $sql = "SELECT SQL_CALC_FOUND_ROWS SP.*, IF(CE.nombre IS NULL,'', CE.nombre) AS condicionespecial, IF(SA.nombre IS NULL,'', SA.nombre) AS nombreafiliado, IF(SA.codigo IS NULL,'', SA.codigo) AS codigoafiliado, IF(SA.identificador IS NULL,'', SA.identificador) AS identificadorafiliado ";
    $sql.= "FROM sindi_practicas SP ";
    $sql.= "LEFT JOIN sindi_condiciones_especiales CE ON (SP.id_empresa = CE.id_empresa AND SP.id_condicion_especial = CE.id) ";
    $sql.= "LEFT JOIN sindi_afiliados SA ON (SP.id_empresa = SA.id_empresa AND SP.id_paciente = SA.id) ";
    $sql.= "WHERE SP.id_empresa = '$id_empresa' ";
    if (!empty($codigo)) $sql.= "AND codigo = '$codigo' ";
    if (!empty($id_afiliado)) $sql.= "AND SP.id_paciente = '$id_afiliado' ";
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

  function get($id) {

    $id_empresa = parent::get_empresa();
    $sql = "SELECT P.*, SA.nombre AS nombreafiliado, SA.codigo AS codigoafiliado, SA.identificador AS identificadorafiliado, CE.nombre AS condicionespecial, STP.nombre AS nombretipopractica ";
    $sql.= "FROM sindi_practicas P ";
    $sql.= "INNER JOIN sindi_afiliados SA ON (P.id_empresa = SA.id_empresa AND P.id_paciente = SA.id) ";
    $sql.= "LEFT JOIN sindi_condiciones_especiales CE ON (P.id_empresa = CE.id_empresa AND P.id_condicion_especial = CE.id) ";
    $sql.= "LEFT JOIN sindi_tipos_practicas STP ON (P.id_empresa = STP.id_empresa AND P.id_tipo_practica = STP.id) ";
    $sql.= "WHERE P.id_empresa = $id_empresa ";
    $sql.= "AND P.id = $id ";
    $q = $this->db->query($sql);
    $r = $q->row();
    $r->items = array();

    $sql = "SELECT PI.*, N.codigo, N.nombre AS nombre_nomenclador ";
    $sql.= "FROM sindi_practicas_items PI ";
    $sql.= "INNER JOIN sindi_nomencladores N ON (PI.id_nomenclador = N.id AND PI.id_empresa = N.id_empresa) ";
    $sql.= "WHERE PI.id_empresa = $id_empresa ";
    $sql.= "AND PI.id_practica = $id ";
    $q = $this->db->query($sql);
    foreach($q->result() as $item) {
      $r->items[] = $item;
    }

    return $r;
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