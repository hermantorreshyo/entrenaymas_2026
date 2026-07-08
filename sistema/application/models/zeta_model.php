<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Zeta_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("zetas","id","numero ASC");
	}

	function save($data) {
		$this->load->helper("fecha_helper");
		$data->fecha = fecha_mysql($data->fecha);
		return parent::save($data);
	}

	function get($id) {
		$id_empresa = parent::get_empresa();
		$sql = "SELECT F.*, ";
    $sql.= " IF(C.nombre IS NULL,'',C.nombre) AS cliente, ";
    $sql.= "DATE_FORMAT(F.fecha,'%d/%m/%Y') AS fecha ";
		$sql.= "FROM zetas F ";
    $sql.= "LEFT JOIN clientes C ON (F.id_cliente = C.id AND F.id_empresa = C.id_empresa) ";
		$sql.= "WHERE F.id_empresa = $id_empresa AND F.id = $id ";
		$q = $this->db->query($sql);
		return $q->row();
	}

  function buscar($config=array()) {
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $limit = isset($config["limit"]) ? $config["limit"] : 0;
    $offset = isset($config["offset"]) ? $config["offset"] : 100;
    $id_sucursal = isset($config["id_sucursal"]) ? $config["id_sucursal"] : 0;
    $id_razon_social = isset($config["id_razon_social"]) ? $config["id_razon_social"] : 0;
    $id_punto_venta = isset($config["id_punto_venta"]) ? $config["id_punto_venta"] : 0;
    $fecha_desde = isset($config["fecha_desde"]) ? $config["fecha_desde"] : "";
    $fecha_hasta = isset($config["fecha_hasta"]) ? $config["fecha_hasta"] : "";
    $sql = "SELECT SQL_CALC_FOUND_ROWS Z.*, DATE_FORMAT(fecha,'%d/%m/%Y') AS fecha, ";
    $sql.= " IF(C.nombre IS NULL,'',C.nombre) AS cliente ";
    $sql.= "FROM zetas Z ";
    $sql.= "LEFT JOIN clientes C ON (Z.id_cliente = C.id AND Z.id_empresa = C.id_empresa) ";
    $sql.= "LEFT JOIN puntos_venta PV ON (Z.id_punto_venta = PV.id AND Z.id_empresa = PV.id_empresa) ";
    $sql.= "LEFT JOIN almacenes S ON (PV.id_sucursal = S.id AND PV.id_empresa = S.id_empresa) ";
    $sql.= "WHERE Z.id_empresa = $id_empresa ";
    if (!empty($id_sucursal)) $sql.= "AND PV.id_sucursal = $id_sucursal ";
    if (!empty($fecha_desde)) $sql.= "AND Z.fecha >= '$fecha_desde' ";
    if (!empty($fecha_hasta)) $sql.= "AND Z.fecha <= '$fecha_hasta' ";
    if (!empty($id_razon_social)) $sql.= "AND S.id_razon_social = $id_razon_social ";
    if ($id_punto_venta>0) $sql.= "AND Z.id_punto_venta = $id_punto_venta ";
    $sql.= "ORDER BY Z.fecha DESC, Z.punto_venta DESC ";
    $sql.= "LIMIT $limit,$offset ";
    $q = $this->db->query($sql);
    $result = $q->result();

    $q_total = $this->db->query("SELECT FOUND_ROWS() AS total");
    $total = $q_total->row();

    return array(
      "results"=>$result,
      "total"=>$total->total,
    );
  }

	function get_all($limit = null, $offset = null,$order_by = '',$order = '') {
		$id_empresa = parent::get_empresa();
		$sql = "SELECT Z.*, DATE_FORMAT(fecha,'%d/%m/%Y') AS fecha, ";
    $sql.= " IF(C.nombre IS NULL,'',C.nombre) AS cliente ";
		$sql.= "FROM zetas Z ";
    $sql.= "LEFT JOIN clientes C ON (Z.id_cliente = C.id AND Z.id_empresa = C.id_empresa) ";
		$sql.= "WHERE Z.id_empresa = $id_empresa ";
		$sql.= "ORDER BY Z.fecha DESC, Z.punto_venta DESC ";
		$q = $this->db->query($sql);
		$result = $q->result();
		return $result;
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