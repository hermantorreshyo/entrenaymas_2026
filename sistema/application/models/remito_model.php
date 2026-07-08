<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Remito_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("facturas","id");
	}	
	
	function get_all($limit = null, $offset = null,$order_by = '',$order = '') {

		$id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
		$desde = isset($config["desde"]) ? $config["desde"] : "";
		$hasta = isset($config["hasta"]) ? $config["hasta"] : "";
		$id_cliente = isset($config["id_cliente"]) ? $config["id_cliente"] : "";
		$id_vendedor = isset($config["id_vendedor"]) ? $config["id_vendedor"] : "";
		$numero = isset($config["numero"]) ? $config["numero"] : "";
		$numero_reparto = isset($config["numero_reparto"]) ? $config["numero_reparto"] : "";
		$limit = isset($config["limit"]) ? $config["limit"] : 0;
		$offset = isset($config["offset"]) ? $config["offset"] : 20;
		$in_ids_estados = isset($config["in_ids_estados"]) ? $config["in_ids_estados"] : "";
		$tipos_comprobantes = isset($config["tipos_comprobantes"]) ? $config["tipos_comprobantes"] : "";

		$estado = ($_SESSION["estado"] == 1 ? 1 : 0);
		$sql = "SELECT SQL_CALC_FOUND_ROWS A.*, A.comprobante, ";
		$sql.= "IF(A.id_cliente = 0,'Consumidor Final',IF(C.nombre IS NULL,'',C.nombre)) AS cliente, ";
		$sql.= "IF(C.email IS NULL,'',C.email) AS cliente_email, ";
		$sql.= "IF(C.telefono IS NULL,'',C.telefono) AS cliente_telefono, ";
		$sql.= "IF(TC.nombre IS NULL,'',TC.nombre) AS estado, ";		
		$sql.= "IF(TC.letra IS NULL,'X',TC.letra) AS letra, ";
		$sql.= "IF(PV.tipo_impresion IS NULL,'',PV.tipo_impresion) AS tipo_punto_venta, ";
		$sql.= "IF(TC.nombre IS NULL,'',TC.nombre) AS tipo_comprobante, ";
		$sql.= "IF(V.nombre IS NULL,'',V.nombre) AS vendedor, ";
        $sql.= "IF(A.fecha='0000-00-00','',DATE_FORMAT(A.fecha,'%d/%m/%Y')) AS fecha, ";
		$sql.= "IF(A.fecha_reparto='0000-00-00','',DATE_FORMAT(A.fecha_reparto,'%d/%m/%Y')) AS fecha_reparto, ";
        $sql.= "IF(A.hora='00:00:00','',DATE_FORMAT(A.hora,'%H:%i:%s')) AS hora, ";
        $sql.= "IF(E.nombre IS NULL,'',E.nombre) AS empresa ";
		$sql.= "FROM facturas A ";
		$sql.= "LEFT JOIN tipos_comprobante TC ON (A.id_tipo_comprobante = TC.id) ";
		$sql.= "LEFT JOIN clientes C ON (A.id_cliente = C.id) ";
		$sql.= "LEFT JOIN vendedores V ON (A.id_vendedor = V.id) ";
		$sql.= "LEFT JOIN empresas E ON (A.id_empresa = E.id) ";
		$sql.= "LEFT JOIN puntos_venta PV ON (A.id_punto_venta = PV.id) ";
		$sql.= "WHERE A.tipo != 'P' AND A.id_empresa = $id_empresa ";
		if ($estado == 0) $sql.= "AND A.estado = $estado ";
        if (!empty($desde)) $sql.= "AND A.fecha >= '$desde' ";
        if (!empty($hasta)) $sql.= "AND A.fecha <= '$hasta' ";
        if (!empty($id_cliente)) $sql.= "AND A.id_cliente = $id_cliente ";
        if (!empty($id_vendedor)) $sql.= "AND A.id_vendedor = $id_vendedor ";
		if (!empty($numero)) $sql.= "AND A.numero LIKE '%$numero%' ";
        if (!empty($numero_reparto)) $sql.= "AND A.reparto = $numero_reparto AND A.anulada = 0 "; // Si se consulta por numero de reparto, no se muestran las anuladas
		if (!empty($in_ids_estados) && !empty($in_ids_estados)) {
			$in_ids_estados = str_replace("-",",",$in_ids_estados);
			$sql.= "AND A.id_tipo_estado IN ($in_ids_estados) ";
		}
		if (!empty($tipos_comprobantes)) {
			$tipos_comprobantes = str_replace("-",",",$tipos_comprobantes);
			$sql.= "AND A.id_tipo_comprobante IN ($tipos_comprobantes) ";
		}
        $sql.= "ORDER BY A.fecha DESC, A.hora DESC ";
        if (!empty($limit)) $sql.= "LIMIT $limit,$offset ";
		$query = $this->db->query($sql);
		$result = $query->result();
		$this->db->close();
		return $result;
	}	
	
	function get($id) {
		
		$id_empresa = parent::get_empresa();
		
		$sql = "SELECT F.*, ";
		$sql.= " IF(F.fecha_reparto IS NULL,'',DATE_FORMAT(F.fecha_reparto,'%d/%m/%Y')) AS fecha_reparto, ";
		$sql.= " IF(F.fecha_vto IS NULL,'',DATE_FORMAT(F.fecha_vto,'%d/%m/%Y')) AS fecha_vto, ";
		$sql.= " IF(TC.nombre IS NULL,'',TC.nombre) AS tipo_comprobante, ";
		$sql.= " IF(TC.letra IS NULL,'',TC.letra) AS letra, ";
		$sql.= " IF(F.fecha IS NULL,'',DATE_FORMAT(F.fecha,'%d/%m/%Y')) AS fecha ";
		$sql.= "FROM facturas F ";
		$sql.= "LEFT JOIN clientes C ON (C.id = F.id_cliente AND C.id_empresa = F.id_empresa) ";
		$sql.= "LEFT JOIN tipos_comprobante TC ON (TC.id = F.id_tipo_comprobante) ";
		$sql.= "WHERE F.id = $id ";
		$sql.= "AND F.id_empresa = $id_empresa ";
		$query = $this->db->query($sql);
		$row = $query->row();
		
		if (!empty($row)) {
			// Tomamos los items
			$sql = "SELECT * ";
			$sql.= "FROM facturas_items FI ";
			$sql.= "WHERE FI.id_factura = $id ";
			$sql.= "AND id_empresa = $id_empresa ";
			$sql.= "ORDER BY orden ASC";
			$q = $this->db->query($sql);
			$row->items = $q->result();
			
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
			//$sql.= "AND id_empresa = $id_empresa ";
			$sql.= "ORDER BY orden ASC";
			$q = $this->db->query($sql);
			$row->items = $q->result();
		}
		$this->db->close();
		return $row;
	}	
	
	
	
	function get_by_hash($hash) {
		
		$sql = "SELECT F.*, ";
		$sql.= " IF(F.fecha_reparto IS NULL,'',DATE_FORMAT(F.fecha_reparto,'%d/%m/%Y')) AS fecha_reparto, ";
		$sql.= " IF(TC.nombre IS NULL,'',TC.nombre) AS tipo_comprobante, ";
		$sql.= " IF(TC.letra IS NULL,'',TC.letra) AS letra, ";
		$sql.= " IF(F.fecha IS NULL,'',DATE_FORMAT(F.fecha,'%d/%m/%Y')) AS fecha ";
		$sql.= "FROM facturas F ";
		$sql.= "LEFT JOIN clientes C ON (C.id = F.id_cliente AND C.id_empresa = F.id_empresa) ";
		$sql.= "LEFT JOIN tipos_comprobante TC ON (TC.id = F.id_tipo_comprobante) ";
		$sql.= "WHERE F.hash = '$hash' ";
		$query = $this->db->query($sql);
		$row = $query->row();
		
		if (!empty($row)) {
			// Tomamos los items
			$sql = "SELECT * ";
			$sql.= "FROM facturas_items FI ";
			$sql.= "WHERE FI.id_factura = $row->id ";
			$sql.= "AND id_empresa = $row->id_empresa ";
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
	
	
	
	function get_between_days($desde = "", $hasta = "", $estado = 0) {
		if (empty($desde)) $desde = date("Y-m-d");
		if (empty($hasta)) $hasta = date("Y-m-d");
		$d = new DateTime($desde);
		$h = new DateTime($hasta);
		$interval = new DateInterval('P1D');
		$range = new DatePeriod($d,$interval,$h);
		$id_empresa = parent::get_empresa();
		$salida = array();
		foreach($range as $fecha) {
			$sql = "SELECT ";
			$sql.= " IF(COUNT(*) IS NULL,0,COUNT(*)) AS cantidad, ";
			$sql.= " IF(SUM(total) IS NULL,0,SUM(total)) AS total ";
			$sql.= "FROM facturas F ";
			$sql.= "WHERE F.id_empresa = $id_empresa AND F.tipo != 'P' AND F.anulada = 0 AND F.pendiente = 0 ";
			$sql.= "AND F.fecha = '".$fecha->format("Y-m-d")."' ";
			if ($estado == 0) $sql.= "AND F.estado = 0 ";
			$q = $this->db->query($sql);
			$r = $q->row();
			$r->fecha = $fecha->format("Y-m-d");
			$salida[] = $r;
		}
		return $salida;
	}
	
    
    function get_last() {
		$id_empresa = parent::get_empresa();
        $q = $this->db->query("SELECT id FROM facturas WHERE id_empresa = $id_empresa ORDER BY id DESC LIMIT 0,1");
        if ($q->num_rows() > 0) {
            $r = $q->row();
            $f = $this->get($r->id);
            if ($f->numero == 0) return $f;
            else return FALSE;
        } else {
            return FALSE;
        }
    }
	
}