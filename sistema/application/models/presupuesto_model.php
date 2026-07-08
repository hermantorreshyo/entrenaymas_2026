<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Presupuesto_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("presupuestos","id");
	}
	
	function get_all($limit = null, $offset = null,$order_by = '',$order = '') {
		$id_empresa = parent::get_empresa();
		$sql = "SELECT A.id, E.nombre AS cliente, A.numero, ";
		$sql.= " IF(A.fecha IS NULL,'',DATE_FORMAT(A.fecha,'%d/%m/%Y')) AS fecha ";
		$sql.= "FROM presupuestos A ";
		$sql.= "LEFT JOIN empresas E ON (A.id_empresa = E.id) ";
		$sql.= "WHERE id_empresa = $id_empresa ";
		$sql.= "ORDER BY fecha DESC ";
		if (!is_null($limit) && (strlen($limit)>0) && !is_null($offset) && (strlen($offset)>0)) {
			$sql.= "LIMIT $limit, $offset ";
		}
		$query = $this->db->query($sql);
		$result = $query->result();
		$this->db->close();
		return $result;
	}	
	
	function get($id) {
		
		$id_empresa = parent::get_empresa();
		
		$sql = "SELECT F.*, ";
		$sql.= " IF(F.fecha_hasta IS NULL,'',DATE_FORMAT(F.fecha_hasta,'%d/%m/%Y')) AS fecha_hasta, ";
		$sql.= " IF(F.fecha IS NULL,'',DATE_FORMAT(F.fecha,'%d/%m/%Y')) AS fecha ";
		$sql.= "FROM presupuestos F ";
		$sql.= "LEFT JOIN clientes C ON (C.id = F.id_cliente AND C.id_empresa = F.id_empresa) ";
		$sql.= "WHERE F.id = $id ";
		$sql.= "AND F.id_empresa = $id_empresa ";
		$query = $this->db->query($sql);
		$row = $query->row();
		
		if (!empty($row)) {
			// Tomamos los items
			$sql = "SELECT FI.*, IF(A.codigo_barra IS NULL,'',A.codigo_barra) AS codigo_barra, ";
			$sql.= " IF(A.moneda IS NULL,1,A.moneda) AS moneda ";
			$sql.= "FROM presupuestos_items FI ";
			$sql.= "LEFT JOIN articulos A ON (FI.id_articulo = A.id AND FI.id_empresa = A.id_empresa) ";
			$sql.= "WHERE FI.id_presupuesto = $id ";
			$sql.= "AND FI.id_empresa = $id_empresa ";
			$sql.= "ORDER BY FI.orden ASC";
			$q = $this->db->query($sql);
			$row->items = $q->result();

			if ($row->id_sucursal != 0) {
				foreach($row->items as $item) {
					$item->costo_final = 0;
					$sql = "SELECT moneda, costo_final FROM articulos_precios_sucursales APS ";
					$sql.= "WHERE APS.id_empresa = $id_empresa ";
					$sql.= "AND APS.id_sucursal = $row->id_sucursal ";
					$sql.= "AND APS.id_articulo = $item->id_articulo ";
					$qq = $this->db->query($sql);
					if ($qq->num_rows() > 0) {
						$ii = $qq->row();
						$item->costo_final = ($ii->costo_final * $item->cantidad);
						$item->moneda = $ii->moneda;
					}
				}
			}
			
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
		}
		
		$this->db->close();
		return $row;
	}
	
}