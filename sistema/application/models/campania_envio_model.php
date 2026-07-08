<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Campania_Envio_Model extends Abstract_Model {
	
  function __construct() {
    parent::__construct("crm_campanias","id","fecha_inicio DESC, hora DESC");
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

  function get_all($limit = null, $offset = null,$order_by = '',$order = '') {
    $id_empresa = parent::get_empresa();
    $sql = "SELECT *, ";
    $sql.= " IF(fecha='0000-00-00','',DATE_FORMAT(fecha,'%d/%m/%Y')) AS fecha, ";
    $sql.= " IF(fecha_inicio='0000-00-00','',DATE_FORMAT(fecha_inicio,'%d/%m/%Y')) AS fecha_inicio, ";
    $sql.= " IF(fecha_fin='0000-00-00','',DATE_FORMAT(fecha_fin,'%d/%m/%Y')) AS fecha_fin, ";
    $sql.= " IF(comienzo_ejecucion='0000-00-00 00:00:00','',DATE_FORMAT(comienzo_ejecucion,'%d/%m/%Y %H:%i')) AS comienzo_ejecucion, ";
    $sql.= " IF(fin_ejecucion='0000-00-00 00:00:00','',DATE_FORMAT(fin_ejecucion,'%d/%m/%Y %H:%i')) AS fin_ejecucion ";
    $sql.= "FROM crm_campanias ";
    $sql.= "WHERE id_empresa = $id_empresa ";
    if (!empty($order_by)) $sql.= "ORDER BY $order_by $order ";
    if (!is_null($limit) && !is_null($offset)) $sql.= "LIMIT $limit, $offset ";
    $query = $this->db->query($sql);
    $result = $query->result();
    $this->db->close();
    return $result;
  }

  function get($id) {
    $id_empresa = parent::get_empresa();
    $sql = "SELECT *, ";
    $sql.= " IF(fecha='0000-00-00','',DATE_FORMAT(fecha,'%d/%m/%Y')) AS fecha, ";
    $sql.= " IF(fecha_inicio='0000-00-00','',DATE_FORMAT(fecha_inicio,'%d/%m/%Y')) AS fecha_inicio, ";
    $sql.= " IF(fecha_fin='0000-00-00','',DATE_FORMAT(fecha_fin,'%d/%m/%Y')) AS fecha_fin, ";
    $sql.= " IF(comienzo_ejecucion='0000-00-00 00:00:00','',DATE_FORMAT(comienzo_ejecucion,'%d/%m/%Y %H:%i')) AS comienzo_ejecucion, ";
    $sql.= " IF(fin_ejecucion='0000-00-00 00:00:00','',DATE_FORMAT(fin_ejecucion,'%d/%m/%Y %H:%i')) AS fin_ejecucion ";
    $sql.= "FROM crm_campanias ";
    $sql.= "WHERE id = $id ";
    $sql.= "AND id_empresa = $id_empresa ";
    $query = $this->db->query($sql);
    $row = $query->row(); 
    $this->db->close();
    return $row;
  }  

  function ver($conf=array()) {
    $destinatarios = isset($conf["destinatarios"]) ? $conf["destinatarios"] : "";
    if (empty($destinatarios)) return FALSE;
    $operacion = isset($conf["operacion"]) ? $conf["operacion"] : "";
    $filtros = isset($conf["filtros"]) ? $conf["filtros"] : "";
    $valores = isset($conf["valores"]) ? $conf["valores"] : "";
    $id_empresa = isset($conf["id_empresa"]) ? $conf["id_empresa"] : parent::get_empresa();
    $sql = "SELECT $destinatarios.email, $destinatarios.nombre ";
    $sql.= "FROM $destinatarios ";
    if ($operacion == "PERTENECE") $sql.= "WHERE $filtros IN ($valores) ";
    else if ($operacion == "NO_PERTENECE") $sql.= "WHERE $filtros NOT IN ($valores) ";
    $sql.= "AND id_empresa = $id_empresa ";
    //else if ($operacion == "")
  }

  function save($data) {
    $this->load->helper("fecha_helper");
    $data->fecha = fecha_mysql($data->fecha);
    $data->fecha_inicio = fecha_mysql($data->fecha_inicio);
    $data->fecha_fin = fecha_mysql($data->fecha_fin);
    return parent::save($data);
  }
}