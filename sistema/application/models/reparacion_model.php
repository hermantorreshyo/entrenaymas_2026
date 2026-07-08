<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Reparacion_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("reparaciones","id","numero DESC");
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

  function get_all($limit = null, $offset = null,$order_by = '',$order = '') {
    $id_empresa = parent::get_empresa();
    $sql = "SELECT SQL_CALC_FOUND_ROWS A.*, ";
    $sql.= " IF(C.nombre IS NULL,'Consumidor Final',C.nombre) AS cliente, ";
    $sql.= " IF(RE.nombre IS NULL,'',RE.nombre) AS estado, ";
    $sql.= " IF(A.fecha='0000-00-00 00:00:00','',DATE_FORMAT(A.fecha,'%d/%m/%Y %H:%i')) AS fecha, ";
    $sql.= " IF(A.fecha_entrega='0000-00-00 00:00:00','',DATE_FORMAT(A.fecha_entrega,'%d/%m/%Y %H:%i')) AS fecha_entrega ";
    $sql.= "FROM reparaciones A ";
    $sql.= "LEFT JOIN clientes C ON (A.id_cliente = C.id AND A.id_empresa = C.id_empresa) ";
    $sql.= "LEFT JOIN reparaciones_estados RE ON (A.id_estado = RE.id AND A.id_empresa = RE.id_empresa) ";
    $sql.= "WHERE A.id_empresa = $id_empresa ";
    $sql.= "ORDER BY A.numero DESC ";
    if (!empty($limit)) $sql.= "LIMIT $limit,$offset ";
    $query = $this->db->query($sql);
    $result = $query->result();
    $this->db->close();
    return $result;
  } 

  function get($id) {
    
    $id_empresa = parent::get_empresa();
    
    $sql = "SELECT F.*, ";
    $sql.= " IF(C.nombre IS NULL,'Consumidor Final',C.nombre) AS cliente, ";
    $sql.= " IF(RE.nombre IS NULL,'',RE.nombre) AS estado, ";
    $sql.= " IF(F.fecha='0000-00-00 00:00:00','',DATE_FORMAT(F.fecha,'%d/%m/%Y %H:%i')) AS fecha, ";
    $sql.= " IF(F.fecha_entrega='0000-00-00 00:00:00','',DATE_FORMAT(F.fecha_entrega,'%d/%m/%Y %H:%i')) AS fecha_entrega ";
    $sql.= "FROM reparaciones F ";
    $sql.= "LEFT JOIN clientes C ON (F.id_cliente = C.id AND F.id_empresa = C.id_empresa) ";
    $sql.= "LEFT JOIN reparaciones_estados RE ON (F.id_estado = RE.id AND F.id_empresa = RE.id_empresa) ";
    $sql.= "WHERE F.id = $id ";
    $sql.= "AND F.id_empresa = $id_empresa ";
    $query = $this->db->query($sql);
    $row = $query->row();
    
    if (!empty($row)) {
      // Tomamos los items
      $sql = "SELECT FI.*, ";
      $sql.= " IF(A.nombre IS NULL,'',A.nombre) AS nombre, ";
      $sql.= " IF(A.codigo_barra IS NULL,'',A.codigo_barra) AS codigo_barra, ";
      $sql.= " IF(A.codigo IS NULL,'',A.codigo) AS codigo ";
      $sql.= "FROM reparaciones_items FI ";
      $sql.= " LEFT JOIN articulos A ON (FI.id_articulo = A.id AND FI.id_empresa = A.id_empresa) ";
      $sql.= "WHERE FI.id_reparacion = $id ";
      $sql.= "AND FI.id_empresa = $id_empresa ";
      $sql.= "ORDER BY FI.orden ASC";
      $q = $this->db->query($sql);
      $row->items = $q->result();
    }
    
    $this->db->close();
    return $row;
  }

}