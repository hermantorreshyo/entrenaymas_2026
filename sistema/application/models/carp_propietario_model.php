<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Carp_Propietario_Model extends Abstract_Model {

  private $total;
  
  function __construct() {
    parent::__construct("com_usuarios","id");
  }

  function buscar($config=array()) {

    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : $this->get_empresa();
    $filter = isset($config["filter"]) ? $config["filter"] : "";
    $estado = isset($config["estado"]) ? $config["estado"] : "";
    $mostrar_agencias = isset($config["mostrar_agencias"]) ? $config["mostrar_agencias"] : 0;
    $id_agencia = isset($config["id_agencia"]) ? $config["id_agencia"] : 0;
    $limit = isset($config["limit"]) ? $config["limit"] : 0;
    $activo = isset($config["activo"]) ? $config["activo"] : -1;
    $offset = isset($config["offset"]) ? $config["offset"] : 10;
    $order = isset($config["order"]) ? $config["order"] : "U.nombre ASC ";
    $order = trim($order);
    if (empty($order)) $order = "U.nombre ASC ";

    $sql = "SELECT SQL_CALC_FOUND_ROWS U.*, CH.observaciones, CH.id_agencia, CH.vehiculo, CH.apellido, CH.numero_interno, ";
    $sql.= " IF(AGENCIA.nombre IS NULL,'',AGENCIA.nombre) AS agencia ";
    $sql.= "FROM com_usuarios U ";
    $sql.= "INNER JOIN carp_propietarios CH ON (CH.id_usuario = U.id AND CH.id_empresa = U.id_empresa) ";
    $sql.= "LEFT JOIN com_usuarios AGENCIA ON (CH.id_agencia = AGENCIA.id AND CH.id_empresa = AGENCIA.id_empresa) ";
    $sql.= "WHERE U.id_empresa = $id_empresa ";
    if (!empty($filter)) $sql.= "AND (CONCAT(U.nombre,' ',CH.apellido) LIKE '%$filter%' OR CH.documento = '$filter') ";
    if (!empty($estado)) $sql.= "AND CH.estado = '$estado' ";
    if (!empty($id_agencia)) $sql.= "AND AGENCIA.id = $id_agencia ";
    if ($activo != -1) $sql.= "AND U.activo = $activo ";
    $sql.= "ORDER BY $order ";
    $sql.= "LIMIT $limit, $offset ";
    $query = $this->db->query($sql);

    $q_total = $this->db->query("SELECT FOUND_ROWS() AS total");
    $total = $q_total->row();
    $this->total = $total->total;

    $salida = array();
    foreach($query->result() as $r) {
        $r->nombre = $r->nombre." ".$r->apellido;
        if ($mostrar_agencias == 1) $r->nombre .= " / ".$r->agencia;
        $salida[] = $r;
    }
    $this->db->close();
    return $salida;
  }

  function get_total_results() {
    return $this->total;
  }
    
  function get($id,$config = array()) {
    $id_empresa = (isset($config["id_empresa"])) ? $config["id_empresa"] : $this->get_empresa();
    $sql = "SELECT U.*, CH.observaciones, CH.id_agencia, CH.vehiculo, CH.numero_interno, ";
    $sql.= " CH.apellido, CH.documento, CH.numero_calle, CH.ciudad, ";
    $sql.= " IF(AGENCIA.nombre IS NULL,'',AGENCIA.nombre) AS agencia ";
    $sql.= "FROM com_usuarios U ";
    $sql.= "INNER JOIN carp_propietarios CH ON (CH.id_usuario = U.id AND CH.id_empresa = U.id_empresa) ";
    $sql.= "LEFT JOIN com_usuarios AGENCIA ON (CH.id_agencia = AGENCIA.id AND CH.id_empresa = AGENCIA.id_empresa) ";
    $sql.= "WHERE U.id_empresa = $id_empresa AND U.id = $id ";
    $query = $this->db->query($sql);
    $row = $query->row();
    $this->db->close();
    return $row;
  }

  function insert($data) {
    $id_empresa = $data->id_empresa;
    $observaciones = $data->observaciones;
    $id_agencia = $data->id_agencia;
    $vehiculo = $data->vehiculo;
    $apellido = $data->apellido;
    $documento = $data->documento;
    $numero_calle = $data->numero_calle;
    $numero_interno = $data->numero_interno;
    $ciudad = $data->ciudad;
    $id = parent::insert($data);
    $sql = "INSERT INTO carp_propietarios (id_empresa, id_usuario, observaciones, vehiculo, id_agencia, apellido, documento, numero_calle, ciudad, numero_interno) VALUES (";
    $sql.= " '$id_empresa','$id','$observaciones','$vehiculo','$id_agencia', '$apellido', '$documento', '$numero_calle', '$ciudad', '$numero_interno') ";
    $this->db->query($sql);
    return $id;
  }

  function update($id,$data) {
    $id_empresa = $data->id_empresa;
    $observaciones = $data->observaciones;
    $estado = $data->estado;
    $id_agencia = $data->id_agencia;
    $vehiculo = $data->vehiculo;
    $apellido = $data->apellido;
    $documento = $data->documento;
    $numero_calle = $data->numero_calle;
    $numero_interno = $data->numero_interno;
    $ciudad = $data->ciudad;    
    $af = parent::update($id,$data);    
    $sql = "UPDATE carp_propietarios SET ";
    $sql.= " observaciones = '$observaciones', ";
    $sql.= " id_agencia = '$id_agencia', ";
    $sql.= " apellido = '$apellido', ";
    $sql.= " documento = '$documento', ";
    $sql.= " numero_calle = '$numero_calle', ";
    $sql.= " numero_interno = '$numero_interno', ";
    $sql.= " ciudad = '$ciudad', ";
    $sql.= " vehiculo = '$vehiculo' ";
    $sql.= "WHERE id_empresa = $id_empresa AND id_usuario = $id ";
    $this->db->query($sql);
    return $af;
  }

  function delete($id) {
    $id_empresa = parent::get_empresa();
    $this->db->query("DELETE FROM com_usuarios WHERE id = $id AND id_empresa = $id_empresa");
    $this->db->query("DELETE FROM carp_propietarios WHERE id_usuario = $id AND id_empresa = $id_empresa");
  }

  
}