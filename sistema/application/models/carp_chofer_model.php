<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Carp_Chofer_Model extends Abstract_Model {

  private $total;
  
  function __construct() {
    parent::__construct("com_usuarios","id");
  }

  function buscar($config=array()) {

    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : $this->get_empresa();
    $filter = isset($config["filter"]) ? $config["filter"] : "";
    $estado = isset($config["estado"]) ? $config["estado"] : "";
    $limit = isset($config["limit"]) ? $config["limit"] : 0;
    $id_agencia = isset($config["id_agencia"]) ? $config["id_agencia"] : 0;
    $id_propietario = isset($config["id_propietario"]) ? $config["id_propietario"] : 0;
    $activo = isset($config["activo"]) ? $config["activo"] : -1;
    $offset = isset($config["offset"]) ? $config["offset"] : 10;
    $bolsa_trabajo = isset($config["bolsa_trabajo"]) ? $config["bolsa_trabajo"] : 0;
    $order = isset($config["order"]) ? $config["order"] : "U.nombre ASC ";
    $order = trim($order);
    if (empty($order)) $order = "U.nombre ASC ";

    $sql = "SELECT SQL_CALC_FOUND_ROWS U.*, CH.observaciones, CH.estado, CH.vehiculo, CH.id_propietario, CH.apellido, ";
    $sql.= " CH.bolsa_trabajo, CH.latitud, CH.longitud, CH.numero_calle, CH.ciudad, ";
    $sql.= " IF(CH.fecha_alta = '0000-00-00','',DATE_FORMAT(CH.fecha_alta,'%d/%m/%Y')) AS fecha_alta, ";
    $sql.= " IF(CH.fecha_baja = '0000-00-00','',DATE_FORMAT(CH.fecha_baja,'%d/%m/%Y')) AS fecha_baja, ";    
    $sql.= " IF(AGENCIA.nombre IS NULL,'',AGENCIA.nombre) AS agencia, ";
    $sql.= " IF(AGENCIA.cargo IS NULL,'',AGENCIA.cargo) AS agencia_codigo, ";
    $sql.= " IF(AGENCIA.id IS NULL,'',AGENCIA.id) AS id_agencia, ";
    $sql.= " IF(PROP.nombre IS NULL,'',CONCAT(PROP.nombre,' ',PROP2.apellido)) AS propietario ";
    $sql.= "FROM com_usuarios U ";
    $sql.= "INNER JOIN carp_choferes CH ON (CH.id_usuario = U.id AND CH.id_empresa = U.id_empresa) ";
    $sql.= "LEFT JOIN com_usuarios PROP ON (CH.id_propietario = PROP.id AND CH.id_empresa = PROP.id_empresa) ";
    $sql.= "LEFT JOIN carp_propietarios PROP2 ON (CH.id_propietario = PROP2.id_usuario AND CH.id_empresa = PROP2.id_empresa) ";
    $sql.= "LEFT JOIN com_usuarios AGENCIA ON (PROP2.id_agencia = AGENCIA.id AND PROP2.id_empresa = AGENCIA.id_empresa) ";
    $sql.= "WHERE U.id_empresa = $id_empresa ";
    $sql.= "AND CH.bolsa_trabajo = $bolsa_trabajo ";
    if (!empty($id_agencia)) {
        if (!empty($filter)) {
            // Buscamos solamente el DNI de todos los choferes
            $sql.= "AND U.dni = '$filter' ";
        } else {
            $sql.= "AND PROP2.id_agencia = $id_agencia ";
        }
    } else if (!empty($id_propietario)) {
        if (!empty($filter)) {
            // Buscamos solamente el DNI de todos los choferes
            $sql.= "AND U.dni = '$filter' ";
        } else {
            $sql.= "AND CH.id_propietario = $id_propietario ";
        }
    } else if (!empty($filter)) $sql.= "AND (CONCAT(U.nombre,' ',CH.apellido) LIKE '%$filter%' OR U.dni = '$filter' OR CONCAT(U.direccion,' ',CH.numero_calle,' ',CH.ciudad) LIKE '%$filter%') ";
    if (!empty($estado)) $sql.= "AND CH.estado = '$estado' ";
    if ($activo != -1) $sql.= "AND U.activo = $activo ";
    $sql.= "ORDER BY $order ";
    $sql.= "LIMIT $limit, $offset ";
    $query = $this->db->query($sql);

    $q_total = $this->db->query("SELECT FOUND_ROWS() AS total");
    $total = $q_total->row();
    $this->total = $total->total;

    $salida = array();
    foreach($query->result() as $r) {
        if (!empty($id_agencia) && $id_agencia != $r->id_agencia) {
            $r->agencia = "";
            $r->propietario = "";
            $r->telefono = "";
        }
        if (!empty($id_propietario) && $id_propietario != $r->id_propietario) {
            $r->propietario = "";
            $r->telefono = "";
        }
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
    $sql = "SELECT U.*, CH.observaciones, CH.estado, CH.vehiculo, CH.id_propietario, ";
    $sql.= " IF(CH.fecha_alta = '0000-00-00','',DATE_FORMAT(CH.fecha_alta,'%d/%m/%Y')) AS fecha_alta, ";
    $sql.= " IF(CH.fecha_baja = '0000-00-00','',DATE_FORMAT(CH.fecha_baja,'%d/%m/%Y')) AS fecha_baja, ";
    $sql.= " CH.apellido, CH.numero_calle, CH.ciudad, ";
    $sql.= " CH.bolsa_trabajo, CH.latitud, CH.longitud, ";
    $sql.= " IF(PROP.nombre IS NULL,'',PROP.nombre) AS propietario ";
    $sql.= "FROM com_usuarios U ";
    $sql.= "INNER JOIN carp_choferes CH ON (CH.id_usuario = U.id AND CH.id_empresa = U.id_empresa) ";
    $sql.= "LEFT JOIN com_usuarios PROP ON (CH.id_propietario = PROP.id AND CH.id_empresa = PROP.id_empresa) ";
    $sql.= "WHERE U.id_empresa = $id_empresa AND U.id = $id ";
    $query = $this->db->query($sql);
    $row = $query->row();
    $this->db->close();
    return $row;
  }

  function insert($data) {
    $id_empresa = $data->id_empresa;
    $observaciones = $data->observaciones;
    $estado = $data->estado;
    $id_propietario = $data->id_propietario;
    $vehiculo = $data->vehiculo;
    $apellido = $data->apellido;
    $numero_calle = $data->numero_calle;
    $bolsa_trabajo = $data->bolsa_trabajo;
    $latitud = (isset($data->latitud) ? $data->latitud : 0);
    $longitud = (isset($data->longitud) ? $data->longitud : 0);
    $ciudad = $data->ciudad;
    $this->load->helper("fecha_helper");
    $fecha_alta = fecha_mysql($data->fecha_alta);
    $fecha_baja = fecha_mysql($data->fecha_baja);
    $id = parent::insert($data);
    $sql = "INSERT INTO carp_choferes (id_empresa, id_usuario, observaciones, estado, vehiculo, id_propietario, apellido, numero_calle, ciudad, fecha_alta, fecha_baja, bolsa_trabajo, latitud, longitud) VALUES (";
    $sql.= " '$id_empresa','$id','$observaciones','$estado','$vehiculo','$id_propietario', '$apellido', '$numero_calle', '$ciudad', '$fecha_alta', '$fecha_baja', '$bolsa_trabajo', '$latitud', '$longitud') ";
    $this->db->query($sql);
    return $id;
  }

  function update($id,$data) {
    $id_empresa = $data->id_empresa;
    $observaciones = $data->observaciones;
    $estado = $data->estado;
    $id_propietario = $data->id_propietario;
    $vehiculo = $data->vehiculo;
    $apellido = $data->apellido;
    $bolsa_trabajo = $data->bolsa_trabajo;
    $numero_calle = $data->numero_calle;
    $latitud = (isset($data->latitud) ? $data->latitud : 0);
    $longitud = (isset($data->longitud) ? $data->longitud : 0);
    $ciudad = $data->ciudad;
    $this->load->helper("fecha_helper");
    $fecha_alta = fecha_mysql($data->fecha_alta);
    $fecha_baja = fecha_mysql($data->fecha_baja);    
    $af = parent::update($id,$data);    
    $sql = "UPDATE carp_choferes SET ";
    $sql.= " observaciones = '$observaciones', ";
    $sql.= " estado = '$estado', ";
    $sql.= " id_propietario = '$id_propietario', ";
    $sql.= " apellido = '$apellido', ";
    $sql.= " numero_calle = '$numero_calle', ";
    $sql.= " ciudad = '$ciudad', ";
    $sql.= " fecha_alta = '$fecha_alta', ";
    $sql.= " fecha_baja = '$fecha_baja', ";
    $sql.= " bolsa_trabajo = '$bolsa_trabajo', ";
    $sql.= " latitud = '$latitud', ";
    $sql.= " longitud = '$longitud', ";
    $sql.= " vehiculo = '$vehiculo' ";
    $sql.= "WHERE id_empresa = $id_empresa AND id_usuario = $id ";
    $this->db->query($sql);
    return $af;
  }

  function delete($id) {
    $id_empresa = parent::get_empresa();
    $this->db->query("DELETE FROM com_usuarios WHERE id = $id AND id_empresa = $id_empresa");
    $this->db->query("DELETE FROM carp_choferes WHERE id_usuario = $id AND id_empresa = $id_empresa");
  }

  
}