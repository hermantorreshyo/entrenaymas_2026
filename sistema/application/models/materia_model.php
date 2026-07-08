<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Materia_Model extends Abstract_Model {
  
  function __construct() {
    parent::__construct("aca_materias","id","nombre ASC");
  }
  
  function get($id) {
    $id_empresa = parent::get_empresa();
    $id = (int)$id;
    $sql = "SELECT R.* ";
    $sql.= "FROM aca_materias R ";
    $sql.= "WHERE R.id = $id ";
    $sql.= "AND R.id_empresa = $id_empresa ";
    $q = $this->db->query($sql);
    if ($q->num_rows() == 0) return array();
    $row = $q->row();
    return $row;
  }
  
  
  function get_all($conf = array()) {
    $id_empresa = isset($conf["id_empresa"]) ? $conf["id_empresa"] : parent::get_empresa();
    $filter = isset($conf["filter"]) ? $conf["filter"] : "";
    $id_carrera = isset($conf["id_carrera"]) ? $conf["id_carrera"] : 0;
    $anio = isset($conf["anio"]) ? $conf["anio"] : 0;
    $cuatrimestre = isset($conf["cuatrimestre"]) ? $conf["cuatrimestre"] : 0;
    $order_by = isset($conf["order_by"]) ? $conf["order_by"] : "anio ASC, cuatrimestre ASC";
    $sql = "SELECT * FROM aca_materias ";
    $sql.= "WHERE id_empresa = $id_empresa ";
    if (!empty($id_carrera)) $sql.= "AND id_carrera = '$id_carrera' ";
    if (!empty($filter)) $sql.= "AND nombre LIKE '%$filter%' ";
    if (!empty($anio)) $sql.= "AND anio = '$anio' ";
    if (!empty($cuatrimestre)) $sql.= "AND cuatrimestre = '$cuatrimestre' ";
    $sql.= "ORDER BY $order_by";
    $q = $this->db->query($sql);
    $result = $q->result();
    return $result;
  }
  
  // Reordena los elementos del arbol
  function reorder($elements,$orden = 0, $id_padre = 0) {
    $id_empresa = parent::get_empresa();

    if (isset($elements["id"])) {
      $id = $elements["id"];
      if (!empty($id)) {
        $sql = "UPDATE aca_materias SET orden = $orden, id_padre = $id_padre ";
        $sql.= "WHERE id = $id AND id_empresa = $id_empresa ";
        $this->db->query($sql);        
      }
    }
    if (isset($elements["children"]) && is_array($elements["children"])){
      for($i=0;$i<sizeof($elements["children"]);$i++) {
        $e = $elements["children"][$i];
        $this->reorder($e,$i,$id);
      }
    }
  }
  
  
    function get_arbol($id_padre = 0,$separador = "",$id_carrera = 0) {
    $id_empresa = parent::get_empresa();
        $result = array();
        $sql = "SELECT * FROM aca_materias ";
        $sql.= "WHERE id_empresa = $id_empresa AND id_padre = $id_padre ";
        if (!empty($id_carrera)) $sql.= "AND id_carrera = $id_carrera ";
        $sql.= "ORDER BY orden ASC ";
        $q = $this->db->query($sql);
        foreach($q->result() as $row) {
      $e = new stdClass();
      $e->id = $row->id;
      $e->id_padre = $id_padre;
      $e->title = $row->nombre;
      $e->nombre_es = $e->title;
      $e->key = $row->id;
      $e->children = $this->get_arbol($row->id,$separador."&nbsp;&nbsp;&nbsp;");
      $result[] = $e;            
        }
        return $result;
    }
  
    function get_select($id_padre = 0,$separador = "",$id_carrera = 0) {
    $id_empresa = parent::get_empresa();
        $result = array();
        $sql = "SELECT * FROM aca_materias WHERE id_empresa = $id_empresa AND id_padre = $id_padre ";
        if (!empty($id_carrera)) $sql.= "AND id_carrera = $id_carrera ";
        $sql.= "ORDER BY nombre ASC";
        $q = $this->db->query($sql);
        foreach($q->result() as $row) {
      $e = new stdClass();
      $e->id = $row->id;
      $e->id_padre = $id_padre;
      $e->nombre = $separador.$row->nombre;
      $result[] = $e;
      $hijos = $this->get_select($row->id,$separador."&nbsp;&nbsp;&nbsp;");
      $result = array_merge($result,$hijos);
        }
        return $result;
    }
  
  function save($data) {
    $this->load->helper("file_helper");
    $data->link = filename($data->nombre,"-",0);
    parent::save($data);
  }
}