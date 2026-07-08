<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Pedido_Proveedor_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("ped_pedidos_proveedores","id");
	}

  function generar($config = array()) {

    @session_start();    
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $id_proveedor = isset($config["id_proveedor"]) ? $config["id_proveedor"] : 0;
    $id_sucursal = isset($config["id_sucursal"]) ? $config["id_sucursal"] : 0;
    $items = isset($config["items"]) ? $config["items"] : array();

    // Creamos el pedido
    $numero = $this->get_proximo_numero(array("id_empresa"=>$id_empresa));
    $id_usuario = isset($_SESSION["id"]) ? $_SESSION["id"] : 0;
    $fecha = date("Y-m-d");
    $hora = date("H:i:s");
    $sql = "INSERT INTO ped_pedidos_proveedores (";
    $sql.= " id_empresa, fecha, hora, id_usuario, numero, id_proveedor, id_sucursal ";
    $sql.= ") VALUES (";
    $sql.= " '$id_empresa', '$fecha', '$hora', '$id_usuario', '$numero', '$id_proveedor', '$id_sucursal' ";
    $sql.= ")";
    $this->db->query($sql);
    $id = $this->db->insert_id();
    $total = 0;
    foreach($items as $item) {

      // Calculamos los costos del articulo
      $sql = "SELECT APS.*, A.nombre ";
      $sql.= "FROM articulos_precios_sucursales APS ";
      $sql.= "INNER JOIN articulos A ON (APS.id_empresa = A.id_empresa AND APS.id_articulo = A.id) ";
      $sql.= "WHERE APS.id_empresa = $id_empresa ";
      $sql.= "AND APS.id_sucursal = $id_sucursal ";
      $sql.= "AND APS.id_articulo = $item->id_articulo ";
      $qq = $this->db->query($sql);
      if ($qq->num_rows() > 0) {

        $art = $qq->row();
        $item->nombre = $art->nombre;
        $item->precio = $art->costo_final;
        $item->total = $item->cantidad * $item->precio;
        $total += ((float)$item->total);

        $sql = "INSERT INTO ped_pedidos_proveedores_items (";
        $sql.= " id_empresa, id_sucursal, id_pedido, id_articulo, cantidad, nombre, precio, total ";
        $sql.= ") VALUES (";
        $sql.= " '$id_empresa', '$id_sucursal', '$id', '$item->id_articulo', '$item->cantidad', '$item->nombre', '$item->precio', '$item->total' ";
        $sql.= ")";
        $this->db->query($sql);        
      }
    }
    // Actualizamos el total del articulo
    $this->db->query("UPDATE ped_pedidos_proveedores SET total = $total WHERE id = $id AND id_empresa = $id_empresa ");
    return $id;
  }
	
	function get_all($config = array()) {
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $desde = isset($config["desde"]) ? $config["desde"] : "";
    $hasta = isset($config["hasta"]) ? $config["hasta"] : "";
    $id_proveedor = isset($config["id_proveedor"]) ? $config["id_proveedor"] : "";
    $id_usuario = isset($config["id_usuario"]) ? $config["id_usuario"] : 0;
    $numero = isset($config["numero"]) ? $config["numero"] : "";
    $limit = isset($config["limit"]) ? $config["limit"] : 0;
    $offset = isset($config["offset"]) ? $config["offset"] : 20;
    $in_ids_estados = isset($config["in_ids_estados"]) ? $config["in_ids_estados"] : "";

    $sql = "SELECT SQL_CALC_FOUND_ROWS F.*, ";
    $sql.= "IF(F.fecha='0000-00-00','',DATE_FORMAT(F.fecha,'%d/%m/%Y')) AS fecha, ";
    $sql.= "IF(F.hora='00:00:00','',DATE_FORMAT(F.hora,'%H:%i:%s')) AS hora, ";
    $sql.= "IF(TC.nombre IS NULL,'',TC.nombre) AS estado, ";
    $sql.= "IF(C.nombre IS NULL,'',C.nombre) AS proveedor, ";
    $sql.= "IF(C.email IS NULL,'',C.email) AS proveedor_email, ";
    $sql.= "IF(C.telefono IS NULL,'',C.telefono) AS proveedor_telefono, ";
    $sql.= " IF(S.nombre IS NULL,'',S.nombre) AS sucursal, ";
    $sql.= " IF(S.direccion IS NULL,'',S.direccion) AS sucursal_direccion, ";
    $sql.= "IF(E.nombre IS NULL,'',E.nombre) AS empresa ";
    $sql.= "FROM ped_pedidos_proveedores F ";
    $sql.= "LEFT JOIN proveedores C ON (F.id_proveedor = C.id AND F.id_empresa = C.id_empresa) ";
    $sql.= "LEFT JOIN empresas E ON (F.id_empresa = E.id) ";
    $sql.= "LEFT JOIN ped_tipos_estado TC ON (TC.id = F.id_tipo_estado) ";
    $sql.= "LEFT JOIN sucursales S ON (S.id = F.id_sucursal) ";
    $sql.= "WHERE F.id_empresa = $id_empresa ";
    if (!empty($desde)) $sql.= "AND F.fecha >= '$desde' ";
    if (!empty($hasta)) $sql.= "AND F.fecha <= '$hasta' ";
    if (!empty($id_proveedor)) $sql.= "AND F.id_proveedor = $id_proveedor ";
    if (!empty($id_usuario)) $sql.= "AND F.id_usuario = '$id_usuario' ";
    if (!empty($numero)) $sql.= "AND F.numero LIKE '%$numero%' ";
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
		$sql.= " IF(TC.nombre IS NULL,'',TC.nombre) AS estado, ";
		$sql.= " IF(L.nombre IS NULL,'',L.nombre) AS localidad, ";
    $sql.= " IF(S.nombre IS NULL,'',S.nombre) AS sucursal, ";
    $sql.= " IF(S.direccion IS NULL,'',S.direccion) AS sucursal_direccion, ";
		$sql.= " IF(F.fecha IS NULL,'',DATE_FORMAT(F.fecha,'%d/%m/%Y')) AS fecha ";
		$sql.= "FROM ped_pedidos_proveedores F ";
		$sql.= "LEFT JOIN proveedores C ON (C.id = F.id_proveedor AND C.id_empresa = F.id_empresa) ";
		$sql.= "LEFT JOIN ped_tipos_estado TC ON (TC.id = F.id_tipo_estado) ";
		$sql.= "LEFT JOIN com_localidades L ON (L.id = F.id_localidad) ";
    $sql.= "LEFT JOIN sucursales S ON (S.id = F.id_sucursal) ";
		$sql.= "WHERE F.id = $id ";
		$query = $this->db->query($sql);
		$row = $query->row();
		
		if (!empty($row)) {
			// Tomamos los items
			$sql = "SELECT FI.*, ";
      $sql.= " IF(A.codigo IS NULL,'',A.codigo) AS codigo, ";
      $sql.= " IF(A.uxb IS NULL,0,A.uxb) AS uxb, ";
      $sql.= " IF(A.id_rubro IS NULL,0,A.id_rubro) AS id_rubro ";
			$sql.= "FROM ped_pedidos_proveedores_items FI ";
      $sql.= "LEFT JOIN articulos A ON (FI.id_articulo = A.id AND FI.id_empresa = A.id_empresa) ";
			$sql.= "WHERE FI.id_pedido = $id ";
			$sql.= "AND FI.id_empresa = $row->id_empresa ";
			$sql.= "ORDER BY orden ASC";
			$q = $this->db->query($sql);
			$row->items = $q->result();
			
			// Tomamos los datos del proveedor
			$this->load->model("Proveedor_Model");
			$row->proveedor = $this->Proveedor_Model->get($row->id_proveedor,$row->id_empresa);
		}
		
		$this->db->close();
		return $row;
	}

  function get_proximo_numero($config = array()) {
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $sql = "SELECT IF(MAX(numero) IS NULL,0,MAX(numero)) AS numero ";
    $sql.= "FROM ped_pedidos_proveedores ";
    $sql.= "WHERE id_empresa = $id_empresa ";
    $q = $this->db->query($sql);
    $n = $q->row();
    return $n->numero + 1;
  }
	
	function get_by_proveedor($id_proveedor) {
		
		//$id_empresa = parent::get_empresa(); // TODO: arreglar esto
		
		$sql = "SELECT F.*, ";
		$sql.= " IF(TC.nombre IS NULL,'',TC.nombre) AS estado, ";
		$sql.= " IF(F.fecha IS NULL,'',DATE_FORMAT(F.fecha,'%d/%m/%Y')) AS fecha ";
		$sql.= "FROM ped_pedidos_proveedores F ";
		$sql.= "LEFT JOIN proveedores C ON (C.id = F.id_proveedor AND C.id_empresa = F.id_empresa) ";
		$sql.= "LEFT JOIN ped_tipos_estado TC ON (TC.id = F.id_tipo_estado) ";
		$sql.= "WHERE F.id_proveedor = $id_proveedor ";
		$sql.= "AND F.id_tipo_estado IN (0,1,2) "; // Solamente tomamos si estan en los primeros estados
		//$sql.= "AND F.id_empresa = $id_empresa "; // TODO: arreglar esto
		$query = $this->db->query($sql);
		$row = $query->row();
		
		if (!empty($row)) {
			// Tomamos los items
			$sql = "SELECT * ";
			$sql.= "FROM ped_pedidos_proveedores_items FI ";
			$sql.= "WHERE FI.id_pedido = $row->id ";
			$sql.= "AND FI.id_empresa = $row->id_empresa ";
			$sql.= "ORDER BY orden ASC";
			$q = $this->db->query($sql);
			$row->items = $q->result();
		}
		
		$this->db->close();
		return $row;
	}
	
	
}