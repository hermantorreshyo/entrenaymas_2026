<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Bobina_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("custom_bobinas","id","fecha_alta DESC");
	}

  function get($id) {
    $id_empresa = parent::get_empresa();
    $sql = "SELECT B.*, ";
    $sql.= " IF(TB.nombre IS NULL,'',TB.nombre) AS tipo, ";
    $sql.= " IF(B.fecha_alta = '0000-00-00','',DATE_FORMAT(B.fecha_alta,'%d/%m/%Y')) AS fecha_alta, ";
    $sql.= " IF(B.fecha_baja = '0000-00-00','',DATE_FORMAT(B.fecha_baja,'%d/%m/%Y')) AS fecha_baja ";
    $sql.= "FROM custom_bobinas B LEFT JOIN custom_tipos_bobinas TB ON (B.id_tipo_bobina = TB.id AND B.id_empresa = TB.id_empresa) ";
    $sql.= "WHERE B.id = $id AND B.id_empresa = $id_empresa ";
    $q = $this->db->query($sql);
    return $q->row();
  }

  function get_list($conf = array()) {
    $fecha_alta = isset($conf["fecha_alta"]) ? $conf["fecha_alta"] : "";
    $fecha_baja = isset($conf["fecha_baja"]) ? $conf["fecha_baja"] : "";
    $filter = isset($conf["filter"]) ? $conf["filter"] : "";
    $estado = isset($conf["estado"]) ? $conf["estado"] : 0;
    $limit = isset($conf["limit"]) ? $conf["limit"] : 0;
    $offset = isset($conf["offset"]) ? $conf["offset"] : 0;
    $id_empresa = parent::get_empresa();
    $sql = "SELECT SQL_CALC_FOUND_ROWS B.*, ";
    $sql.= " IF(TB.nombre IS NULL,'',TB.nombre) AS tipo, ";
    $sql.= " IF(B.fecha_alta = '0000-00-00','',DATE_FORMAT(B.fecha_alta,'%d/%m/%Y')) AS fecha_alta, ";
    $sql.= " IF(B.fecha_baja = '0000-00-00','',DATE_FORMAT(B.fecha_baja,'%d/%m/%Y')) AS fecha_baja ";
    $sql.= "FROM custom_bobinas B LEFT JOIN custom_tipos_bobinas TB ON (B.id_tipo_bobina = TB.id AND B.id_empresa = TB.id_empresa) ";
    $sql.= "WHERE B.id_empresa = $id_empresa ";
    if (!empty($fecha_alta)) $sql.= "AND B.fecha_alta = '$fecha_alta' ";
    if (!empty($fecha_baja)) $sql.= "AND B.fecha_baja = '$fecha_baja' ";
    if (!empty($filter)) $sql.= "AND B.numero = '$filter' ";
    $sql.= "ORDER BY B.fecha_alta DESC ";
    if ($offset != 0) $sql.= "LIMIT $limit, $offset ";
    $q = $this->db->query($sql);

    $q_total = $this->db->query("SELECT FOUND_ROWS() AS total");
    $total = $q_total->row();

    return array(
      "results"=>$q->result(),
      "total"=>$total->total,
    );
  }

  function save($data) {
    $this->load->helper("fecha_helper");
    $data->fecha_alta = (!empty($data->fecha_alta)) ? fecha_mysql($data->fecha_alta) : "";
    if (isset($data->fecha_baja)) {
      $data->fecha_baja = (!empty($data->fecha_baja)) ? fecha_mysql($data->fecha_baja) : "";
    }
    return parent::save($data);
  }
    
	function find($filter) {
		$id_empresa = parent::get_empresa();
		$this->db->where("id_empresa",$id_empresa);
		$this->db->like("numero",$filter);
		$query = $this->db->get($this->tabla);
		$result = $query->result();
		$this->db->close();
		return $result;
	}    

}