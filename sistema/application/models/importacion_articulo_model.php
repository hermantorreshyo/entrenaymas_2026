<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Importacion_Articulo_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("importaciones_articulos","id");
	}
	
	function get_all($config = array()) {
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $desde = isset($config["desde"]) ? $config["desde"] : "";
    $hasta = isset($config["hasta"]) ? $config["hasta"] : "";
    $id_proveedor = isset($config["id_proveedor"]) ? $config["id_proveedor"] : "";
    $id_usuario = isset($config["id_usuario"]) ? $config["id_usuario"] : 0;
    $limit = isset($config["limit"]) ? $config["limit"] : 0;
    $offset = isset($config["offset"]) ? $config["offset"] : 20;
    $in_ids_estados = isset($config["in_ids_estados"]) ? $config["in_ids_estados"] : "";

    $sql = "SELECT SQL_CALC_FOUND_ROWS F.*, ";
    $sql.= "IF(F.fecha_alta='0000-00-00','',DATE_FORMAT(F.fecha_alta,'%d/%m/%Y %H:%i')) AS fecha_alta, ";
    $sql.= "IF(F.fecha_modif='0000-00-00','',DATE_FORMAT(F.fecha_modif,'%d/%m/%Y %H:%i')) AS fecha_modif, ";
    $sql.= "IF(U.nombre IS NULL,'',U.nombre) AS usuario, ";
    $sql.= "IF(C.nombre IS NULL,'',C.nombre) AS proveedor, ";
    $sql.= "IF(C.email IS NULL,'',C.email) AS proveedor_email, ";
    $sql.= "IF(C.telefono IS NULL,'',C.telefono) AS proveedor_telefono, ";
    $sql.= "IF(E.nombre IS NULL,'',E.nombre) AS empresa ";
    $sql.= "FROM importaciones_articulos F ";
    $sql.= "LEFT JOIN proveedores C ON (F.id_proveedor = C.id AND F.id_empresa = C.id_empresa) ";
    $sql.= "LEFT JOIN empresas E ON (F.id_empresa = E.id) ";
    $sql.= "LEFT JOIN com_usuarios U ON (F.id_usuario = U.id AND F.id_empresa = U.id_empresa) ";
    $sql.= "WHERE F.id_empresa = $id_empresa ";
    $sql.= "AND F.eliminado = 0 ";
    if (!empty($desde)) $sql.= "AND F.fecha_alta >= '$desde' ";
    if (!empty($hasta)) $sql.= "AND F.fecha_alta <= '$hasta' ";
    if (!empty($id_proveedor)) $sql.= "AND F.id_proveedor = $id_proveedor ";
    if (!empty($id_usuario)) $sql.= "AND F.id_usuario = '$id_usuario' ";
    if (!empty($in_ids_estados) && !empty($in_ids_estados)) {
      $in_ids_estados = str_replace("-",",",$in_ids_estados);
      $sql.= "AND F.id_tipo_estado IN ($in_ids_estados) ";
    }
    $sql.= "ORDER BY F.fecha_alta DESC ";
    if ($limit !== FALSE) $sql.= "LIMIT $limit,$offset ";
    $q = $this->db->query($sql);
    $lista = $q->result();		
    return $lista;
	}	
	
	function get($id) {
		
		$sql = "SELECT F.*, ";
    $sql.= "IF(F.fecha_alta='0000-00-00','',DATE_FORMAT(F.fecha_alta,'%d/%m/%Y %H:%i')) AS fecha_alta, ";
    $sql.= "IF(F.fecha_modif='0000-00-00','',DATE_FORMAT(F.fecha_modif,'%d/%m/%Y %H:%i')) AS fecha_modif ";
		$sql.= "FROM importaciones_articulos F ";
		$sql.= "LEFT JOIN proveedores C ON (C.id = F.id_proveedor AND C.id_empresa = F.id_empresa) ";
		$sql.= "WHERE F.id = $id ";
    $sql.= "AND F.eliminado = 0 ";
		$query = $this->db->query($sql);
		$row = $query->row();
		
		if (!empty($row)) {
			// Tomamos los items
			$sql = "SELECT FI.*, FI.codigo AS codigo_item, ";
      $sql.= " IF(A.codigo IS NULL,'',A.codigo) AS codigo ";
			$sql.= "FROM importaciones_articulos_items FI ";
      $sql.= "LEFT JOIN articulos A ON (FI.id_articulo = A.id AND FI.id_empresa = A.id_empresa) ";
			$sql.= "WHERE FI.id_importacion = $id ";
			$sql.= "AND FI.id_empresa = $row->id_empresa ";
			$sql.= "ORDER BY FI.codigo, orden ASC";
			$q = $this->db->query($sql);
			$row->items = $q->result();
			
			// Tomamos los datos del proveedor
			$this->load->model("Proveedor_Model");
			$proveedor = $this->Proveedor_Model->get($row->id_proveedor,$row->id_empresa);
			if ($proveedor === FALSE) {
				// Si no existe, es un CF
				$proveedor = new stdClass();
				$proveedor->cuit = 0;
				$proveedor->nombre = "Consumidor Final";
				$proveedor->direccion = "";
				$proveedor->localidad = "";
				$proveedor->provincia = "";
				$proveedor->tipo_iva = "Consumidor Final";
			}
			$row->proveedor = $proveedor;		
		}
		
		$this->db->close();
		return $row;
	}
	
}