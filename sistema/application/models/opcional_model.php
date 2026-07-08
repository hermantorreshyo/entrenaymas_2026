<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Opcional_Model extends Abstract_Model {

  function __construct() {
    parent::__construct("via_opcionales","id","nombre ASC");
  }

  function buscar($config = array()) {
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $limit = isset($config["limit"]) ? $config["limit"] : 0;
    $offset = isset($config["offset"]) ? $config["offset"] : 10;
    $filter = isset($config["filter"]) ? $config["filter"] : "";
    $order = isset($config["order"]) ? $config["order"] : "A.orden DESC";
    $sql = "SELECT SQL_CALC_FOUND_ROWS A.*, ";
    $sql.= " IF(C.nombre IS NULL,'',C.nombre) AS categoria ";
    $sql.= "FROM via_opcionales A ";
    $sql.= "LEFT JOIN via_opcionales_categorias C ON (A.id_categoria = C.id) ";
    $sql.= "WHERE 1=1 ";
    $sql.= "AND A.id_empresa = $id_empresa ";
    if (!empty($filter)) $sql.= "AND A.nombre LIKE '%$filter%' ";
    if (!empty($order_by)) $sql.= "ORDER BY $order ";
    $sql.= "LIMIT $limit, $offset ";
    $query = $this->db->query($sql);
    $result = $query->result();

    $q_total = $this->db->query("SELECT FOUND_ROWS() AS total");
    $total = $q_total->row();

    return array(
      "results"=>$result,
      "total"=>$total->total,
    );
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

  function save($data) {
    $this->load->helper("fecha_helper");
    $this->load->helper("file_helper");
    $id_empresa = $data->id_empresa;
    $precios = $data->precios;
    unset($data->undefined);
    unset($data->precios);
    unset($data->categoria);
    $data->nombre_en = htmlentities($data->nombre_en);
    $data->nombre_pt = htmlentities($data->nombre_pt);
    $id = parent::save($data);

    // Guardamos los precios
    $this->db->query("DELETE FROM via_opcionales_precios WHERE id_opcional = $id AND id_empresa = $data->id_empresa ");
    foreach($precios as $item) {
      if (!isset($item->moneda)) $item->moneda = "$";
      $item->fecha_desde = (isset($item->fecha_desde) && !empty($item->fecha_desde)) ? fecha_mysql($item->fecha_desde) : '';
      $item->fecha_hasta = (isset($item->fecha_hasta) && !empty($item->fecha_hasta)) ? fecha_mysql($item->fecha_hasta) : '';
      $sql = "INSERT INTO via_opcionales_precios (id_empresa,id_opcional,precio,moneda,edad_desde,edad_hasta,fecha_desde,fecha_hasta,id_tipo_tarifa";
      $sql.= ") VALUES ($data->id_empresa,$id,'$item->precio','$item->moneda','$item->edad_desde','$item->edad_hasta','$item->fecha_desde','$item->fecha_hasta','$item->id_tipo_tarifa') ";
      $this->db->query($sql);
    }

    return $id;
  }

  function get($id,$config=array()) {

    $row = parent::get($id);
    if ($row === FALSE) return $row;
    $this->load->helper("fecha_helper");

    // Obtenemos los precios
    $sql = "SELECT AI.*, ";
    $sql.= " IF(TT.nombre IS NULL,'',TT.nombre) AS nombre, ";
    $sql.= " IF(AI.fecha_desde = '0000-00-00','',DATE_FORMAT(AI.fecha_desde,'%d/%m/%Y')) AS fecha_desde, ";
    $sql.= " IF(AI.fecha_hasta = '0000-00-00','',DATE_FORMAT(AI.fecha_hasta,'%d/%m/%Y')) AS fecha_hasta ";
    $sql.= "FROM via_opcionales_precios AI ";
    $sql.= "LEFT JOIN via_tipos_tarifas TT ON (AI.id_tipo_tarifa = TT.id AND AI.id_empresa = TT.id_empresa) ";
    $sql.= "WHERE AI.id_opcional = $id AND AI.id_empresa = $row->id_empresa ORDER BY AI.id ASC";
    $q = $this->db->query($sql);
    foreach($q->result() as $r) {
      $row->precios[] = $r;
    }
    return $row;
  }


  function delete($id) {
    $id_empresa = parent::get_empresa();
    $this->db->query("DELETE FROM via_opcionales_precios WHERE id_empresa = $id_empresa AND id_opcional = $id");
    $this->db->query("DELETE FROM via_viajes_opcionales WHERE id_empresa = $id_empresa AND id_opcional = $id");
    $this->db->query("DELETE FROM via_opcionales WHERE id_empresa = $id_empresa AND id = $id");
  }

}