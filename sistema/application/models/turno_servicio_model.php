<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Turno_Servicio_Model extends Abstract_Model {

  function __construct() {
    parent::__construct("turnos_servicios","id","nombre ASC");
  }
  
  function save($data) {
    $this->load->helper("fecha_helper");
    if (!isset($data->id_empresa)) $data->id_empresa = parent::get_empresa();
    unset($data->especialidad);
    $horarios = $data->horarios;
    if (isset($data->deshabilitado_desde)) $data->deshabilitado_desde = fecha_mysql($data->deshabilitado_desde);
    if (isset($data->deshabilitado_hasta)) $data->deshabilitado_hasta = fecha_mysql($data->deshabilitado_hasta);
    $id = parent::save($data);

    // Guardamos los horarios
    $minimo = "23:59:00";
    $maximo = "00:00:00";
    $this->db->query("DELETE FROM turnos_servicios_horarios WHERE id_servicio = $id AND id_empresa = $data->id_empresa ");
    foreach($horarios as $item) {
      $sql = "INSERT INTO turnos_servicios_horarios (id_empresa,id_servicio,dia,desde,hasta";
      $sql.= ") VALUES ($data->id_empresa,$id,'$item->dia','$item->desde','$item->hasta') ";
      $this->db->query($sql);
      if ($item->desde <= $minimo) $minimo = $item->desde;
      if ($item->hasta > $maximo) $maximo = $item->hasta;
    }
    if (sizeof($horarios)>0) {
      $data->hora_desde = $minimo;
      $data->hora_hasta = $maximo;      
    }
    $this->load->helper("file_helper");
    $data->link = "servicio/".filename($data->nombre,"-",0)."-".$id."/";
    $this->db->query("UPDATE turnos_servicios SET hora_desde = '$data->hora_desde', hora_hasta = '$data->hora_hasta', link = '$data->link' WHERE id_empresa = $data->id_empresa AND id = $id ");

    return $id;
  }  

  function get($id,$id_empresa = 0) {

    $id_empresa = ($id_empresa == 0) ? parent::get_empresa() : $id_empresa;
    $sql = "SELECT A.*, ";
    $sql.= " IF(A.deshabilitado_desde = '0000-00-00','',DATE_FORMAT(A.deshabilitado_desde,'%d/%m/%Y')) AS deshabilitado_desde, ";
    $sql.= " IF(A.deshabilitado_hasta = '0000-00-00','',DATE_FORMAT(A.deshabilitado_hasta,'%d/%m/%Y')) AS deshabilitado_hasta ";
    $sql.= "FROM turnos_servicios A ";
    $sql.= "WHERE A.id_empresa = $id_empresa ";
    $sql.= "AND A.id = $id ";
    $q = $this->db->query($sql);
    $row = $q->row();
    if ($row === FALSE) return $row;

    // Obtenemos los horarios
    $row->horarios = array();
    $sql = "SELECT AI.* ";
    $sql.= "FROM turnos_servicios_horarios AI ";
    $sql.= "WHERE AI.id_servicio = $id AND AI.id_empresa = $id_empresa ORDER BY AI.dia ASC";
    $q = $this->db->query($sql);
    foreach($q->result() as $r) {
      $row->horarios[] = $r;
    }

    return $row;
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

  function buscar($conf = array()) {
    $id_empresa = isset($conf["id_empresa"]) ? $conf["id_empresa"] : parent::get_empresa();
    $filter = isset($conf["filter"]) ? $conf["filter"] : "";
    $id_usuario = isset($conf["id_usuario"]) ? $conf["id_usuario"] : 0;
    $limit = isset($conf["limit"]) ? $conf["limit"] : 0;
    $offset = isset($conf["offset"]) ? $conf["offset"] : 10;
    $order = isset($conf["order"]) ? $conf["order"] : "A.nombre ASC";
    $sql = "SELECT SQL_CALC_FOUND_ROWS DISTINCT A.*, ";
    $sql.= " IF(U.nombre IS NULL,'',U.nombre) AS usuario ";
    $sql.= "FROM turnos_servicios A ";
    $sql.= "LEFT JOIN com_usuarios U ON (A.id_usuario = U.id AND A.id_empresa = U.id_empresa) ";
    $sql.= "WHERE 1=1 ";
    $sql.= "AND A.id_empresa = $id_empresa ";
    if (!empty($filter)) $sql.= "AND A.nombre LIKE '%$filter%' ";
    if (!empty($id_usuario)) $sql.= "AND A.id_usuario = $id_usuario ";
    $sql.= "ORDER BY $order ";
    $sql.= "LIMIT $limit, $offset ";
    $q = $this->db->query($sql);
    $q_total = $this->db->query("SELECT FOUND_ROWS() AS total");
    $total = $q_total->row();
    $salida = array();
    foreach($q->result() as $row) {
      $row->horarios = array();
      // Obtenemos los horarios
      $sql = "SELECT AI.* ";
      $sql.= "FROM turnos_servicios_horarios AI ";
      $sql.= "WHERE AI.id_servicio = $row->id AND AI.id_empresa = $row->id_empresa ORDER BY AI.dia ASC";
      $qq = $this->db->query($sql);
      foreach($qq->result() as $r) {
        $row->horarios[] = $r;
      }

      // Obtenemos los dias de ese turno
      $sql = "SELECT DISTINCT dia ";
      $sql.= "FROM turnos_servicios_horarios H ";
      $sql.= "WHERE H.id_empresa = $row->id_empresa AND H.id_servicio = $row->id ";
      $qq = $this->db->query($sql);
      $row->dias = array();
      foreach($qq->result() as $rr) {
        $row->dias[] = $rr->dia;
      }

      $salida[] = $row;
    }
    return array(
      "results"=>$salida,
      "total"=>$total->total,
    );
  }

}