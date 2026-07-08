<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Reserva_Asiento_Model extends Abstract_Model {
	
  private $total = 0;
  
	function __construct() {
		parent::__construct("via_reservas","id","id DESC",1);
	}

  function get_asientos($config = array()) {
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $id_viaje = isset($config["id_viaje"]) ? $config["id_viaje"] : 0;
    $id_vehiculo = isset($config["id_vehiculo"]) ? $config["id_vehiculo"] : 0;
    $sql = "SELECT RA.*, ";
    $sql.= " IF(VA.numero_asiento IS NULL,'',VA.numero_asiento) AS numero_asiento, ";
    $sql.= " IF(VA.piso IS NULL,'',VA.piso) AS piso, ";
    $sql.= " IF(VA.id_tipo_tarifa IS NULL,0,VA.id_tipo_tarifa) AS id_tipo_tarifa ";
    $sql.= "FROM via_reservas_asientos RA ";
    $sql.= "LEFT JOIN via_reservas R ON (RA.id_reserva = R.id AND RA.id_empresa = R.id_empresa) ";
    $sql.= "LEFT JOIN via_vehiculos_asientos VA ON (RA.id_asiento = VA.id AND RA.id_vehiculo = VA.id_vehiculo AND RA.id_empresa = VA.id_empresa) ";
    $sql.= "WHERE RA.id_empresa = $id_empresa ";
    if (!empty($id_viaje)) $sql.= "AND R.id_viaje = $id_viaje ";
    if (!empty($id_vehiculo)) $sql.= "AND R.id_vehiculo = $id_vehiculo ";
    $sql.= "ORDER BY RA.tipo_habitacion DESC, RA.hotel ASC, RA.numero_habitacion ASC ";
    $q = $this->db->query($sql);
    return $q->result();
  }

  function actualizar_asientos($config = array()) {
    $asientos = isset($config["asientos"]) ? $config["asientos"] : array();
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    foreach($asientos as $asiento) {
      $sql = "UPDATE via_reservas_asientos SET ";
      $sql.= " hotel = '$asiento->hotel', ";
      $sql.= " tipo_habitacion = '$asiento->tipo_habitacion', ";
      $sql.= " numero_habitacion = '$asiento->numero_habitacion' ";
      $sql.= "WHERE id_empresa = $id_empresa AND id_reserva = $asiento->id_reserva AND id = $asiento->id ";
      $this->db->query($sql);
    }
    return TRUE;
  }

  function get_all($config = array()) {
    $id_empresa = (isset($config["id_empresa"])) ? $config["id_empresa"] : parent::get_empresa();
    $filter = (isset($config["filter"])) ? $config["filter"] : "";
    $order = (isset($config["order"]) && !empty($config["order"])) ? $config["order"] : "R.fecha_realizacion DESC ";
    $limit = isset($config["limit"]) ? $config["limit"] : 0;
    $offset = isset($config["offset"]) ? $config["offset"] : 10;
    $in_ids_estados = isset($config["in_ids_estados"]) ? $config["in_ids_estados"] : "";
    $sql = "SELECT SQL_CALC_FOUND_ROWS R.*, DATE_FORMAT(R.fecha_reserva,'%d/%m/%Y') AS fecha_reserva, ";
    $sql.= " DATE_FORMAT(R.fecha_llegada_hotel,'%d/%m/%Y') AS fecha_llegada_hotel, ";
    $sql.= " DATE_FORMAT(R.fecha_realizacion,'%d/%m/%Y %H:%i:%s') AS fecha_realizacion, ";
    $sql.= " IF(TE.nombre IS NULL,'',TE.nombre) AS estado, ";
    $sql.= " IF(C.nombre IS NULL,'',C.nombre) AS cliente, ";
    $sql.= " IF(C.telefono IS NULL,'',C.telefono) AS cliente_telefono, ";
    $sql.= " IF(C.email IS NULL,'',C.email) AS cliente_email, ";
    $sql.= " IF(V.nombre IS NULL,'',V.nombre) AS viaje ";
    $sql.= "FROM via_reservas R ";
    $sql.= "LEFT JOIN clientes C ON (R.id_cliente = C.id AND R.id_empresa = C.id_empresa) ";
    $sql.= "LEFT JOIN via_viajes V ON (R.id_viaje = V.id AND R.id_empresa = V.id_empresa) ";
    $sql.= "LEFT JOIN ped_tipos_estado TE ON (R.id_tipo_estado = TE.id) ";
    $sql.= "WHERE R.id_empresa = $id_empresa ";
    if (!empty($filter)) $sql.= "AND C.nombre LIKE '%$filter%' ";
    if (!empty($in_ids_estados) && !empty($in_ids_estados)) {
      $in_ids_estados = str_replace("-",",",$in_ids_estados);
      $sql.= "AND R.id_tipo_estado IN ($in_ids_estados) ";
    }
    $sql.= "ORDER BY $order ";
    $sql.= "LIMIT $limit, $offset ";
    $q = $this->db->query($sql);

    $q_total = $this->db->query("SELECT FOUND_ROWS() AS total");
    $total = $q_total->row();
    $this->total = $total->total;

    $salida = $q->result();
    return $salida;
  }

  function get_total_results() {
    return $this->total;
  }

  function get($id,$config = array()) {
    $id_empresa = (isset($config["id_empresa"])) ? $config["id_empresa"] : parent::get_empresa();
    $sql = "SELECT R.*, DATE_FORMAT(R.fecha_reserva,'%d/%m/%Y') AS fecha_reserva, ";
    $sql.= " DATE_FORMAT(R.fecha_llegada_hotel,'%d/%m/%Y') AS fecha_llegada_hotel, ";
    $sql.= " DATE_FORMAT(R.fecha_realizacion,'%d/%m/%Y %H:%i:%s') AS fecha_realizacion, ";
    $sql.= " IF(TE.nombre IS NULL,'',TE.nombre) AS estado, ";
    $sql.= " IF(C.nombre IS NULL,'',C.nombre) AS cliente, ";
    $sql.= " IF(C.telefono IS NULL,'',C.telefono) AS cliente_telefono, ";
    $sql.= " IF(C.email IS NULL,'',C.email) AS cliente_email, ";
    $sql.= " IF(VE.nombre IS NULL,'',VE.nombre) AS vendedor, ";
    $sql.= " IF(VE.id_sucursal IS NULL,0,VE.id_sucursal) AS id_sucursal, ";
    $sql.= " IF(V.nombre IS NULL,'',V.nombre) AS viaje ";
    $sql.= "FROM via_reservas R ";
    $sql.= "LEFT JOIN clientes C ON (R.id_cliente = C.id AND R.id_empresa = C.id_empresa) ";
    $sql.= "LEFT JOIN via_viajes V ON (R.id_viaje = V.id AND R.id_empresa = V.id_empresa) ";
    $sql.= "LEFT JOIN ped_tipos_estado TE ON (R.id_tipo_estado = TE.id) ";
    $sql.= "LEFT JOIN vendedores VE ON (R.id_vendedor = VE.id AND R.id_empresa = VE.id_empresa) ";
    $sql.= "WHERE R.id = $id AND R.id_empresa = $id_empresa ";
    $q = $this->db->query($sql);
    if ($q->num_rows()<=0) return FALSE;
    $row = $q->row();

    // Obtenemos los asientos y pasajeros
    $sql = "SELECT RA.*, ";
    $sql.= " IF(VA.numero_asiento IS NULL,'',VA.numero_asiento) AS numero_asiento, ";
    $sql.= " IF(VA.piso IS NULL,'',VA.piso) AS piso, ";
    $sql.= " IF(VA.id_tipo_tarifa IS NULL,0,VA.id_tipo_tarifa) AS id_tipo_tarifa ";
    $sql.= "FROM via_reservas_asientos RA ";
    $sql.= "LEFT JOIN via_vehiculos_asientos VA ON (RA.id_asiento = VA.id AND RA.id_vehiculo = VA.id_vehiculo AND RA.id_empresa = VA.id_empresa) ";
    $sql.= "WHERE RA.id_reserva = $id AND RA.id_empresa = $id_empresa ";
    $q = $this->db->query($sql);
    $row->asientos = $q->result();

    // Obtenemos los pagos
    $sql = "SELECT RP.* ";
    $sql.= "FROM via_reservas_pagos RP ";
    $sql.= "WHERE RP.id_reserva = $id AND RP.id_empresa = $id_empresa ";
    $q = $this->db->query($sql);
    $row->pagos = $q->result();

    // Obtenemos los opcionales
    $sql = "SELECT RP.*, ";
    $sql.= " IF(O.nombre IS NULL,'',O.nombre) AS opcional ";
    $sql.= "FROM via_reservas_opcionales RP ";
    $sql.= "LEFT JOIN via_opcionales O ON (RP.id_opcional = O.id AND RP.id_empresa = O.id_empresa) ";
    $sql.= "WHERE RP.id_reserva = $id AND RP.id_empresa = $id_empresa ";
    $q = $this->db->query($sql);
    $row->opcionales = $q->result();
    
    return $row;
  }

	function save($data) {
    $id_empresa = parent::get_empresa();
		$this->load->helper("fecha_helper");
		$data->fecha_reserva = fecha_mysql($data->fecha_reserva);
    $data->fecha_llegada_hotel = fecha_mysql($data->fecha_llegada_hotel);
    $data->fecha_realizacion = fecha_mysql($data->fecha_realizacion);
    $pagos = $data->pagos;
    $asientos = $data->asientos;
    $opcionales = $data->opcionales;
		unset($data->cliente);
    unset($data->cliente_telefono);
    unset($data->cliente_email);
    unset($data->pagos);
    unset($data->estado);
    unset($data->viaje);
    unset($data->vendedor);
    unset($data->opcionales);
    unset($data->asientos);
		$id = parent::save($data);

    // Guardamos los asientos
    $this->db->query("DELETE FROM via_reservas_asientos WHERE id_empresa = $id_empresa AND id_reserva = $id ");
    foreach($asientos as $a) {
      $this->db->insert("via_reservas_asientos",array(
        "id_empresa"=>$id_empresa,
        "id_reserva"=>$id,
        "id_asiento"=>$a->id_asiento,
        "id_vehiculo"=>$a->id_vehiculo,
        "nombre"=>$a->nombre,
        "apellido"=>$a->apellido,
        "menor"=>((isset($a->menor)) ? $a->menor : 0),
        "dni"=>$a->dni,
        "fecha_nac"=>$a->fecha_nac,
        "nacionalidad"=>$a->nacionalidad,
        "precio"=>$a->precio,
        "recargo"=>$a->recargo,
        "hotel"=>$a->hotel,
        "tipo_habitacion"=>$a->tipo_habitacion,
        "numero_habitacion"=>$a->numero_habitacion,
        "fecha_mov"=>$data->fecha_reserva,
        "id_vendedor"=>$data->id_vendedor,
      ));
    }

    // Guardamo los pagos
    $this->db->query("DELETE FROM via_reservas_pagos WHERE id_empresa = $id_empresa AND id_reserva = $id ");
    foreach($pagos as $a) {
      $this->db->insert("via_reservas_pagos",array(
        "id_empresa"=>$id_empresa,
        "id_reserva"=>$id,
        "metodo"=>$a->metodo,
        "total"=>$a->total,
        "fecha"=>$a->fecha,
      ));
    }

    // Guardamo los opcionales
    $this->db->query("DELETE FROM via_reservas_opcionales WHERE id_empresa = $id_empresa AND id_reserva = $id ");
    foreach($opcionales as $a) {
      $this->db->insert("via_reservas_opcionales",array(
        "id_empresa"=>$id_empresa,
        "id_reserva"=>$id,
        "id_opcional"=>$a->id_opcional,
        "total"=>$a->total,
      ));
    }

    return $id;
	}

  function delete($id) {
    $id_empresa = parent::get_empresa();
    $this->db->query("DELETE FROM via_reservas_opcionales WHERE id_empresa = $id_empresa AND id_reserva = $id ");    
    $this->db->query("DELETE FROM via_reservas_pagos WHERE id_empresa = $id_empresa AND id_reserva = $id ");    
    $this->db->query("DELETE FROM via_reservas_asientos WHERE id_empresa = $id_empresa AND id_reserva = $id ");
    $this->db->query("DELETE FROM via_reservas WHERE id_empresa = $id_empresa AND id = $id ");
  }
}