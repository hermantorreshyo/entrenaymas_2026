<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Rotura_Mercaderia_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("roturas_mercaderias","id","fecha DESC");
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

  function confirmar($rotura,$linea) {

    if (!isset($rotura->estado) || $rotura->estado == 0) return FALSE;
    if (!isset($rotura->id)) return FALSE;
    if (!isset($rotura->id_empresa)) return FALSE;
    if (!isset($linea->no_editar_stock)) $linea->no_editar_stock = 0;

    $this->load->model("Stock_Model");
    if ($linea->no_editar_stock == 0) {
      $this->Stock_Model->sacar($linea->id_articulo,$linea->cantidad,$rotura->id_almacen,'R',$rotura->fecha,"Baja Remito $rotura->numero_remito");
    }

    $this->load->model("Empresa_Model");
    $usa_meli = $this->Empresa_Model->usa_mercadolibre($rotura->id_empresa);
    // Si el articulo esta compartido en mercadolibre
    if ($usa_meli) {
      $this->Articulo_Model->update_publicacion_mercadolibre($linea->id_articulo);
    }
    return TRUE;
  }

  function buscar($config=array()) {
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $desde = isset($config["desde"]) ? $config["desde"] : "";
    $hasta = isset($config["hasta"]) ? $config["hasta"] : "";
    $id_sucursal = isset($config["id_sucursal"]) ? $config["id_sucursal"] : 0;
    $filter = isset($config["filter"]) ? $config["filter"] : "";
    $estado = isset($config["estado"]) ? $config["estado"] : 0;
    $limit = isset($config["limit"]) ? $config["limit"] : 0;
    $offset = isset($config["offset"]) ? $config["offset"] : 10;
    $in_ids_estados = isset($config["in_ids_estados"]) ? $config["in_ids_estados"] : "";
    $sql = "SELECT SQL_CALC_FOUND_ROWS A.*, ";
    $sql.= " IF(S.nombre IS NULL,'',S.nombre) AS almacen, ";
    $sql.= " IF(A.fecha='0000-00-00','',DATE_FORMAT(A.fecha,'%d/%m/%Y')) AS fecha ";
    $sql.= "FROM roturas_mercaderias A ";
    $sql.= "LEFT JOIN almacenes S ON (A.id_almacen = S.id AND A.id_empresa = S.id_empresa) ";
    $sql.= "WHERE A.id_empresa = $id_empresa ";
    if (!empty($filter)) $sql.= " AND (A.numero_remito = '$filter' OR P.nombre LIKE '%$filter%') ";
    if (!empty($id_sucursal)) $sql.= " AND A.id_almacen = $id_sucursal ";
    if (!empty($desde)) $sql.= " AND A.fecha >= '$desde' ";
    if (!empty($hasta)) $sql.= " AND A.fecha <= '$hasta' ";
    if (!empty($in_ids_estados)) $sql.= " AND A.estado IN ($in_ids_estados) ";
    if ($estado != -1) $sql.= "AND A.estado = $estado ";
    $sql.= "ORDER BY A.fecha DESC, A.id DESC ";
    if (!empty($offset)) $sql.= "LIMIT $limit,$offset ";
    $query = $this->db->query($sql);
    $result = $query->result();

    $q_total = $this->db->query("SELECT FOUND_ROWS() AS total");
    $total = $q_total->row();

    return array(
      "results"=>$result,
      "total"=>$total->total,
    );
  }


  function get($id) {
    
    $id_empresa = parent::get_empresa();
    
    $sql = "SELECT F.*, ";
    $sql.= " IF(A.nombre IS NULL,'',A.nombre) AS almacen, ";
    $sql.= " IF(F.fecha IS NULL,'',DATE_FORMAT(F.fecha,'%d/%m/%Y')) AS fecha ";
    $sql.= "FROM roturas_mercaderias F ";
    $sql.= " LEFT JOIN almacenes A ON (F.id_almacen = A.id AND F.id_empresa = A.id_empresa) ";
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
      $sql.= "FROM roturas_mercaderias_items FI ";
      $sql.= " LEFT JOIN articulos A ON (FI.id_articulo = A.id AND FI.id_empresa = A.id_empresa) ";
      $sql.= "WHERE FI.id_rotura = $id ";
      $sql.= "AND FI.id_empresa = $id_empresa ";
      $sql.= "ORDER BY FI.orden ASC";
      $q = $this->db->query($sql);
      $row->items = $q->result();
    }
    
    $this->db->close();
    return $row;
  }


}