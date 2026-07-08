<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Pedido_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("facturas","id");
	}
	
	function get_all($config = array()) {
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $desde = isset($config["desde"]) ? $config["desde"] : "";
    $hasta = isset($config["hasta"]) ? $config["hasta"] : "";
    $id_cliente = isset($config["id_cliente"]) ? $config["id_cliente"] : "";
    $id_vendedor = isset($config["id_vendedor"]) ? $config["id_vendedor"] : "";
    $id_usuario = isset($config["id_usuario"]) ? $config["id_usuario"] : 0;
    $numero = isset($config["numero"]) ? $config["numero"] : "";
    $numero_reparto = isset($config["numero_reparto"]) ? $config["numero_reparto"] : "";
    $limit = isset($config["limit"]) ? $config["limit"] : 0;
    $offset = isset($config["offset"]) ? $config["offset"] : 20;
    $in_ids_estados = isset($config["in_ids_estados"]) ? $config["in_ids_estados"] : "";

    $sql = "SELECT SQL_CALC_FOUND_ROWS F.*, ";
    $sql.= "IF(F.fecha='0000-00-00','',DATE_FORMAT(F.fecha,'%d/%m/%Y')) AS fecha, ";
    $sql.= "IF(F.fecha_reparto='0000-00-00','',DATE_FORMAT(F.fecha_reparto,'%d/%m/%Y')) AS fecha_reparto, ";
    $sql.= "IF(F.hora='00:00:00','',DATE_FORMAT(F.hora,'%H:%i:%s')) AS hora, ";
    $sql.= "IF(TC.nombre IS NULL,'',TC.nombre) AS estado, ";
    $sql.= "IF(C.nombre IS NULL,'',C.nombre) AS cliente, ";
    $sql.= "IF(C.email IS NULL,'',C.email) AS cliente_email, ";
    $sql.= "IF(C.telefono IS NULL,'',C.telefono) AS cliente_telefono, ";
    $sql.= "IF(V.nombre IS NULL,'',V.nombre) AS vendedor, ";
    $sql.= "IF(E.nombre IS NULL,'',E.nombre) AS empresa ";
    $sql.= "FROM facturas F ";
    $sql.= "LEFT JOIN clientes C ON (F.id_cliente = C.id AND F.id_empresa = C.id_empresa) ";
    $sql.= "LEFT JOIN vendedores V ON (F.id_vendedor = V.id AND F.id_empresa = V.id_empresa) ";
    $sql.= "LEFT JOIN empresas E ON (F.id_empresa = E.id) ";
    $sql.= "LEFT JOIN ped_tipos_estado TC ON (TC.id = F.id_tipo_estado) ";
    $sql.= "WHERE F.id_empresa = $id_empresa ";
    if (!empty($desde)) $sql.= "AND F.fecha >= '$desde' ";
    if (!empty($hasta)) $sql.= "AND F.fecha <= '$hasta' ";
    if (!empty($id_cliente)) $sql.= "AND F.id_cliente = $id_cliente ";
    if (!empty($id_vendedor)) $sql.= "AND F.id_vendedor = $id_vendedor ";
    if (!empty($id_usuario)) $sql.= "AND F.id_usuario = '$id_usuario' ";
    if (!empty($numero)) $sql.= "AND F.numero LIKE '%$numero%' ";
    if (!empty($numero_reparto)) $sql.= "AND F.reparto = $numero_reparto ";
    if (!empty($in_ids_estados) && !empty($in_ids_estados)) {
      $in_ids_estados = str_replace("-",",",$in_ids_estados);
      $sql.= "AND F.id_tipo_estado IN ($in_ids_estados) ";
    }
    $sql.= "ORDER BY F.fecha DESC, F.hora DESC ";
    if ($limit !== FALSE) $sql.= "LIMIT $limit,$offset ";
    $q = $this->db->query($sql);
    $lista = $q->result();		
    return $lista;
	}	
	
	function get($id) {
		
		$sql = "SELECT F.*, ";
		$sql.= " IF(F.fecha_reparto IS NULL,'',DATE_FORMAT(F.fecha_reparto,'%d/%m/%Y')) AS fecha_reparto, ";
		$sql.= " IF(TC.nombre IS NULL,'',TC.nombre) AS estado, ";
		$sql.= " IF(L.nombre IS NULL,'',L.nombre) AS localidad, ";
		$sql.= " IF(PR.nombre IS NULL,'',PR.nombre) AS provincia, ";
		$sql.= " IF(F.fecha IS NULL,'',DATE_FORMAT(F.fecha,'%d/%m/%Y')) AS fecha ";
		$sql.= "FROM facturas F ";
		$sql.= "LEFT JOIN clientes C ON (C.id = F.id_cliente AND C.id_empresa = F.id_empresa) ";
		$sql.= "LEFT JOIN ped_tipos_estado TC ON (TC.id = F.id_tipo_estado) ";
		$sql.= "LEFT JOIN com_localidades L ON (L.id = F.id_localidad) ";
		$sql.= "LEFT JOIN com_departamentos D ON (D.id = L.id_departamento) ";
		$sql.= "LEFT JOIN com_provincias PR ON (PR.id = D.id_provincia) ";
		$sql.= "WHERE F.id = $id ";
		$query = $this->db->query($sql);
		$row = $query->row();
		
		if (!empty($row)) {
			// Tomamos los items
			$sql = "SELECT FI.*, ";
      $sql.= " IF(A.codigo IS NULL,'',A.codigo) AS codigo ";
			$sql.= "FROM facturas_items FI ";
      $sql.= "LEFT JOIN articulos A ON (FI.id_articulo = A.id AND FI.id_empresa = A.id_empresa) ";
			$sql.= "WHERE FI.id_factura = $id ";
			$sql.= "AND FI.id_empresa = $row->id_empresa ";
			$sql.= "ORDER BY orden ASC";
			$q = $this->db->query($sql);
			$row->items = $q->result();
			
			// Tomamos los datos del cliente
			$this->load->model("Cliente_Model");
			$cliente = $this->Cliente_Model->get($row->id_cliente,$row->id_empresa);
			if ($cliente === FALSE) {
				// Si no existe, es un CF
				$cliente = new stdClass();
				$cliente->cuit = 0;
				$cliente->nombre = "Consumidor Final";
				$cliente->direccion = "";
				$cliente->localidad = "";
				$cliente->provincia = "";
				$cliente->tipo_iva = "Consumidor Final";
			}
			$row->cliente = $cliente;		
		}
		
		$this->db->close();
		return $row;
	}
	
	function get_by_cliente($id_cliente) {
		
		//$id_empresa = parent::get_empresa(); // TODO: arreglar esto
		
		$sql = "SELECT F.*, ";
		$sql.= " IF(F.fecha_reparto IS NULL,'',DATE_FORMAT(F.fecha_reparto,'%d/%m/%Y')) AS fecha_reparto, ";
		$sql.= " IF(TC.nombre IS NULL,'',TC.nombre) AS estado, ";
		$sql.= " IF(F.fecha IS NULL,'',DATE_FORMAT(F.fecha,'%d/%m/%Y')) AS fecha ";
		$sql.= "FROM facturas F ";
		$sql.= "LEFT JOIN clientes C ON (C.id = F.id_cliente AND C.id_empresa = F.id_empresa) ";
		$sql.= "LEFT JOIN ped_tipos_estado TC ON (TC.id = F.id_tipo_estado) ";
		$sql.= "WHERE F.id_cliente = $id_cliente ";
		$sql.= "AND F.id_tipo_estado IN (0,1,2) "; // Solamente tomamos si estan en los primeros estados
		//$sql.= "AND F.id_empresa = $id_empresa "; // TODO: arreglar esto
		$query = $this->db->query($sql);
		$row = $query->row();
		
		if (!empty($row)) {
			// Tomamos los items
			$sql = "SELECT * ";
			$sql.= "FROM facturas_items FI ";
			$sql.= "WHERE FI.id_factura = $row->id ";
			$sql.= "AND FI.id_empresa = $row->id_empresa ";
			$sql.= "ORDER BY orden ASC";
			$q = $this->db->query($sql);
			$row->items = $q->result();
			
			/*
			// Tomamos los datos del cliente
			$this->load->model("Cliente_Model");
			$cliente = $this->Cliente_Model->get($row->id_cliente);
			if ($cliente === FALSE) {
				// Si no existe, es un CF
				$cliente = new stdClass();
				$cliente->cuit = 0;
				$cliente->nombre = "Consumidor Final";
				$cliente->direccion = "";
				$cliente->localidad = "";
				$cliente->provincia = "";
				$cliente->tipo_iva = "Consumidor Final";
			}
			$row->cliente = $cliente;
			*/
		}
		
		$this->db->close();
		return $row;
	}
	
	
}