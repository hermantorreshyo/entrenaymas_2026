<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Almacen_Model extends Abstract_Model {
  
  function __construct() {
    parent::__construct("almacenes","id","orden ASC");
  }
    
  function find($filter) {
    $id_empresa = parent::get_empresa();
    $sql = "SELECT A.*, CC.nombre AS centro_costo ";
    $sql.= "FROM almacenes A LEFT JOIN centros_costos CC ON (A.id_empresa = CC.id_empresa AND A.id_centro_costo = CC.id) ";
    $sql.= "WHERE A.id_empresa = $id_empresa ";    
    if (!empty($filter)) $sql.= "AND A.nombre LIKE '%$filter%' ";
    $q = $this->db->query($sql);
    $result = $q->result();
    $this->db->close();
    return $result;
  }

  function get_sucursal_punto_venta($id_punto_venta,$config = array()) {
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $sql = "SELECT id_almacen ";
    $sql.= "FROM almacenes_puntos_venta ";
    $sql.= "WHERE id_empresa = $id_empresa ";
    $sql.= "AND id_punto_venta = $id_punto_venta ";
    $q = $this->db->query($sql);
    if ($q->num_rows()>0) {
      $r = $q->row();
      return $r->id_almacen;
    } else return 0;
  }

  function save($data) {
    $puntos_venta = $data->puntos_venta;
    unset($data->puntos_venta);
    $id = parent::save($data);
    // Actualizamos los puntos de venta relacionados
    $this->db->query("DELETE FROM almacenes_puntos_venta WHERE id_almacen = $id AND id_empresa = $data->id_empresa ");
    foreach($puntos_venta as $pv) {
      $sql = "INSERT INTO almacenes_puntos_venta (id_empresa,id_almacen,id_punto_venta) VALUES ($data->id_empresa,$id,$pv)";
      $this->db->query($sql);
    }
    return $id;
  }

  function get($id,$config = array()) {
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $sql = "SELECT A.*, CC.nombre AS centro_costo ";
    $sql.= "FROM almacenes A LEFT JOIN centros_costos CC ON (A.id_empresa = CC.id_empresa AND A.id_centro_costo = CC.id) ";
    $sql.= "WHERE A.id = $id AND A.id_empresa = $id_empresa";
    $q = $this->db->query($sql);
    $row = $q->row();
    if (!empty($row)) {
      $sql = "SELECT id_punto_venta AS id FROM almacenes_puntos_venta WHERE id_almacen = $row->id AND id_empresa = $row->id_empresa ";
      $query = $this->db->query($sql);
      $row->puntos_venta = $query->result();
    }
    return $row;
  }

  function get_all($limit = null, $offset = null,$order_by = '',$order = '') {
    $id_empresa = parent::get_empresa();
    $sql = "SELECT A.*, CC.nombre AS centro_costo ";
    $sql.= "FROM almacenes A LEFT JOIN centros_costos CC ON (A.id_empresa = CC.id_empresa AND A.id_centro_costo = CC.id) ";
    $sql.= "WHERE A.id_empresa = $id_empresa ";    
    if (!empty($filter)) $sql.= "AND A.nombre LIKE '%$filter%' ";
    if (!empty($limit) && !empty($offset)) $sql.= "LIMIT $limit, $offset ";
    $q = $this->db->query($sql);
    $result = $q->result();
    $this->db->close();
    return $result;
  }

  function delete($id) {
    $id_empresa = parent::get_empresa();
    $this->db->query("DELETE FROM almacenes_puntos_venta WHERE id_empresa = $id_empresa AND id_almacen = $id");
    $this->db->query("DELETE FROM almacenes WHERE id_empresa = $id_empresa AND id = $id");
    echo json_encode(array());
  }

}