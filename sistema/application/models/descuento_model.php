<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Descuento_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("articulos_descuentos_sucursales","id","desde DESC");
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

  function buscar($conf=array()) {
    $id_empresa = isset($conf["id_empresa"]) ? $conf["id_empresa"] : parent::get_empresa();
    $filter = isset($conf["filter"]) ? $conf["filter"] : "";
    $fecha = isset($conf["fecha"]) ? $conf["fecha"] : "";
    $limit = isset($conf["limit"]) ? $conf["limit"] : 0;
    $offset = isset($conf["offset"]) ? $conf["offset"] : 10;
    $order = isset($conf["order"]) ? $conf["order"] : "A.desde DESC";
    $id_sucursal = isset($conf["id_sucursal"]) ? $conf["id_sucursal"] : 0;
    $fecha = isset($conf["fecha"]) ? $conf["fecha"] : "";
    
    $sql = "SELECT SQL_CALC_FOUND_ROWS A.*, E.codigo, E.nombre, E.codigo_barra, ALM.nombre AS almacen, ";
    $sql.= " E.costo_final, E.custom_10, ";
    $sql.= " IF(APS.precio_final_dto IS NULL,E.precio_final_dto,APS.precio_final_dto) AS precio_anterior, ";
    $sql.= " DATE_FORMAT(A.desde,'%d/%m/%Y') AS desde, ";
    $sql.= " DATE_FORMAT(A.hasta,'%d/%m/%Y') AS hasta ";
    $sql.= "FROM articulos_descuentos_sucursales A ";
    $sql.= "INNER JOIN articulos E ON (A.id_empresa = E.id_empresa AND A.id_articulo = E.id) ";
    $sql.= "INNER JOIN almacenes ALM ON (A.id_empresa = ALM.id_empresa AND A.id_sucursal = ALM.id) ";
    $sql.= "LEFT JOIN articulos_precios_sucursales APS ON (A.id_articulo = APS.id_articulo AND A.id_empresa = APS.id_empresa AND APS.id_sucursal = ALM.id) ";
    $sql.= "WHERE 1=1 ";
    $sql.= "AND A.id_empresa = $id_empresa ";
    if (!empty($filter)) $sql.= "AND (E.nombre LIKE '%$filter%' OR E.codigo = '$filter' OR E.codigo_barra LIKE '%$filter%' ) ";
    if (!empty($id_sucursal)) $sql.= "AND A.id_sucursal = $id_sucursal ";
    if (!empty($fecha)) $sql.= "AND A.desde <= '$fecha' AND '$fecha' <= A.hasta ";
    $sql.= "ORDER BY $order ";
    $sql.= "LIMIT $limit, $offset ";
    $q = $this->db->query($sql);

    $q_total = $this->db->query("SELECT FOUND_ROWS() AS total");
    $total = $q_total->row();
    return array(
      "results"=>$q->result(),
      "total"=>$total->total,
    );
  }

  function get($id) {
    $id_empresa = parent::get_empresa();
    $sql = "SELECT A.*, ";
    $sql.= " DATE_FORMAT(A.desde,'%d/%m/%Y %H:%i') AS desde, ";
    $sql.= " DATE_FORMAT(A.hasta,'%d/%m/%Y %H:%i') AS hasta ";
    $sql.= "FROM articulos_descuentos_sucursales A ";
    $sql.= "WHERE A.id = $id ";
    $sql.= "AND A.id_empresa = $id_empresa ";
    $q = $this->db->query($sql);
    if ($q->num_rows()<=0) return FALSE;
    $row = $q->row();

    // Los articulos estan agrupados por orden
    $row->articulos = array();
    $sql = "SELECT DISTINCT orden ";
    $sql.= "FROM articulos_descuentos_sucursales_articulos ";
    $sql.= "WHERE id_empresa = $id_empresa AND id_regla = $id ";
    $sql.= "ORDER BY orden ASC ";
    $qq = $this->db->query($sql);
    foreach($qq->result() as $rr) {

      $sql = "SELECT RO.*, A.codigo, A.nombre, 0 AS actual ";
      $sql.= "FROM articulos_descuentos_sucursales_articulos RO ";
      $sql.= "INNER JOIN articulos A ON (RO.id_empresa = A.id_empresa AND RO.id_articulo = A.id) ";
      $sql.= "WHERE RO.id_empresa = $id_empresa ";
      $sql.= "AND RO.id_regla = $id ";
      $sql.= "AND RO.orden = $rr->orden ";
      $qqq = $this->db->query($sql);
      $row->articulos[] = $qqq->result();
    }

    $sql = "SELECT * FROM articulos_descuentos_sucursales_sucursales WHERE id_empresa = $id_empresa AND id_regla = $id ORDER BY id ASC ";
    $qq = $this->db->query($sql);
    $row->sucursales = $qq->result();    

    return $row;
  }

  function insert($data) {
    $this->load->helper("fecha_helper");
    $data->desde = fecha_mysql($data->desde);
    $data->hasta = fecha_mysql($data->hasta);
    $articulos = $data->articulos;
    $sucursales = $data->sucursales;
    unset($data->articulos);
    unset($data->sucursales);
    $id = parent::insert($data);

    foreach($articulos as $art) {
      $this->db->insert("articulos_descuentos_sucursales_articulos",array(
        "id_empresa"=>$data->id_empresa,
        "id_regla"=>$id,
        "id_articulo"=>$art->id_articulo,
        "minimo"=>$art->minimo,
        "orden"=>$art->orden,
      ));
    }

    foreach($sucursales as $art) {
      $this->db->insert("articulos_descuentos_sucursales_sucursales",array(
        "id_empresa"=>$data->id_empresa,
        "id_regla"=>$id,
        "id_sucursal"=>$art->id_sucursal,
      ));
    }

    return $id;
  }

  function update($id,$data) {
    $this->load->helper("fecha_helper");
    $data->desde = fecha_mysql($data->desde);
    $data->hasta = fecha_mysql($data->hasta);
    $articulos = $data->articulos;
    $sucursales = $data->sucursales;
    unset($data->articulos);
    unset($data->sucursales);
    $res = parent::update($id,$data);

    $this->db->query("DELETE FROM articulos_descuentos_sucursales_articulos WHERE id_empresa = $data->id_empresa AND id_regla = $id");
    foreach($articulos as $art) {
      $this->db->insert("articulos_descuentos_sucursales_articulos",array(
        "id_empresa"=>$data->id_empresa,
        "id_regla"=>$id,
        "id_articulo"=>$art->id_articulo,
        "minimo"=>$art->minimo,
        "orden"=>$art->orden,
      ));
    }
    $this->db->query("DELETE FROM articulos_descuentos_sucursales_sucursales WHERE id_empresa = $data->id_empresa AND id_regla = $id");
    foreach($sucursales as $art) {
      $this->db->insert("articulos_descuentos_sucursales_sucursales",array(
        "id_empresa"=>$data->id_empresa,
        "id_regla"=>$id,
        "id_sucursal"=>$art->id_sucursal,
      ));
    }

    return $res;
  }

  function delete($id) {
    // Controlamos que se este borrando un articulo que pertenece a la empresa de la session
    $id_empresa = parent::get_empresa();
    if ($id_empresa === FALSE) return;
    $q = $this->db->query("SELECT * FROM articulos_descuentos_sucursales WHERE id = $id AND id_empresa = $id_empresa ");
    if ($q->num_rows()>0) {
      $this->db->query("DELETE FROM articulos_descuentos_sucursales WHERE id = $id AND id_empresa = $id_empresa");
    }
  }

}