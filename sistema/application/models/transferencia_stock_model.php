<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Transferencia_Stock_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("transferencias_stock","id","fecha DESC");
	}

  function confirmar_stock($config = array()) {

    $items = $config["items"];
    $id_origen = $config["id_origen"];
    $id_destino = $config["id_destino"];
    $fecha = isset($config["fecha"]) ? $config["fecha"] : date("Y-m-d");

    $this->load->model("Almacen_Model");
    $origen = $this->Almacen_Model->get($id_origen);
    if ($origen === FALSE) return FALSE;

    $destino = $this->Almacen_Model->get($id_destino);
    if ($destino === FALSE) return FALSE;

    // Si la configuracion NO ES LOCAL, tenemos que gestionar STOCK desde ACA
    // Sino, eso se hace desde el cronjob "uploader"
    //$this->load->model("Configuracion_Model");
    //if ($this->Configuracion_Model->es_local()==1) return FALSE;

    $this->load->model("Stock_Model");
    foreach($items as $item) {

      // Tenemos que sacar de una sucursal
      $this->Stock_Model->sacar($item->id_articulo,$item->cantidad,$id_origen,"B",$fecha,"Transferencia a $destino->nombre");

      // Y acreditarselo a la otra
      $this->Stock_Model->agregar($item->id_articulo,$item->cantidad,$id_destino,$fecha,"Transferencia desde $origen->nombre");

    }
    return TRUE;
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

  function buscar($config = array()) {
    $id_empresa = (isset($config["id_empresa"])) ? $config["id_empresa"] : parent::get_empresa();
    $filter = (isset($config["filter"])) ? $config["filter"] : "";
    $id_sucursal = (isset($config["id_sucursal"])) ? $config["id_sucursal"] : 0;
    $id_origen = (isset($config["id_origen"])) ? $config["id_origen"] : 0;
    $id_destino = (isset($config["id_destino"])) ? $config["id_destino"] : 0;
    $limit = (isset($config["limit"])) ? $config["limit"] : 0;
    $offset = (isset($config["offset"])) ? $config["offset"] : 10;
    $sql = "SELECT SQL_CALC_FOUND_ROWS A.*, ";
    $sql.= " S1.nombre AS origen, ";
    $sql.= " S2.nombre AS destino, ";
    $sql.= " IF(A.fecha='0000-00-00','',DATE_FORMAT(A.fecha,'%d/%m/%Y')) AS fecha ";
    $sql.= "FROM transferencias_stock A ";
    $sql.= "INNER JOIN almacenes S1 ON (A.id_origen = S1.id AND A.id_empresa = S1.id_empresa) ";
    $sql.= "INNER JOIN almacenes S2 ON (A.id_destino = S2.id AND A.id_empresa = S2.id_empresa) ";
    $sql.= "WHERE A.id_empresa = $id_empresa ";
    if (!empty($filter)) $sql.= "AND numero_remito = '$filter' ";
    if ($id_sucursal != 0) $sql.= "AND (A.id_origen = $id_sucursal OR A.id_destino = $id_sucursal) ";
    if ($id_origen != 0) $sql.= "AND A.id_origen = $id_origen ";
    if ($id_destino != 0) $sql.= "AND A.id_destino = $id_destino ";
    $sql.= "ORDER BY A.fecha DESC, A.id DESC ";
    if (!empty($offset)) $sql.= "LIMIT $limit,$offset ";
    $query = $this->db->query($sql);

    $q_total = $this->db->query("SELECT FOUND_ROWS() AS total");
    $total = $q_total->row();

    $this->db->close();
    return array(
      "results"=>$query->result(),
      "total"=>$total->total,
    );
  }

  function get_all($limit = null, $offset = null,$order_by = '',$order = '') {
    return $this->buscar(array(
      "limit"=>$limit,
      "offset"=>$offset,
    ));
  } 

  function get($id) {
    
    $id_empresa = parent::get_empresa();
    
    $sql = "SELECT F.*, ";
    $sql.= " S1.nombre AS origen, ";
    $sql.= " S2.nombre AS destino, ";
    $sql.= " IF(F.fecha IS NULL,'',DATE_FORMAT(F.fecha,'%d/%m/%Y')) AS fecha ";
    $sql.= "FROM transferencias_stock F ";
    $sql.= "INNER JOIN almacenes S1 ON (F.id_origen = S1.id AND F.id_empresa = S1.id_empresa) ";
    $sql.= "INNER JOIN almacenes S2 ON (F.id_destino = S2.id AND F.id_empresa = S2.id_empresa) ";
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
      $sql.= "FROM transferencias_stock_items FI ";
      $sql.= " LEFT JOIN articulos A ON (FI.id_articulo = A.id AND FI.id_empresa = A.id_empresa) ";
      $sql.= "WHERE FI.id_transferencia = $id ";
      $sql.= "AND FI.id_empresa = $id_empresa ";
      $sql.= "ORDER BY FI.orden ASC";
      $q = $this->db->query($sql);
      $row->items = $q->result();
    }
    
    $this->db->close();
    return $row;
  }

}