<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Viaje_Model extends Abstract_Model {
  
  function __construct() {
    parent::__construct("via_viajes","id","nombre ASC");
  }

  function buscar($params = array()) {

    $filter = isset($params["filter"]) ? $params["filter"] : "";
    $limit = isset($params["limit"]) ? $params["limit"] : 0;
    $activo = isset($params["activo"]) ? $params["activo"] : -1;
    $offset = isset($params["offset"]) ? $params["offset"] : 10;
    $order = isset($params["order"]) ? trim($params["order"]) : "A.fecha DESC ";
    if (empty($order)) $order = "A.fecha DESC ";
    $id_empresa = isset($params["id_empresa"]) ? $params["id_empresa"] : parent::get_empresa();
    $id_tripulante = isset($params["id_tripulante"]) ? $params["id_tripulante"] : 0;

    $sql = "SELECT SQL_CALC_FOUND_ROWS A.*, ";
    $sql.= " DATE_FORMAT(A.fecha_creacion,'%d/%m/%Y') AS fecha_creacion, ";
    $sql.= " DATE_FORMAT(A.fecha,'%d/%m/%Y') AS fecha, ";
    $sql.= " DATE_FORMAT(A.fecha_llegada,'%d/%m/%Y') AS fecha_llegada, ";
    $sql.= " IF(C.nombre IS NULL,'',C.nombre) AS categoria ";
    $sql.= "FROM via_viajes A ";
    $sql.= "LEFT JOIN via_viajes_categorias C ON (A.id_categoria = C.id AND A.id_empresa = C.id_empresa) ";
    $sql.= "WHERE 1=1 ";
    $sql.= "AND A.id_empresa = $id_empresa ";
    if ($activo != -1) $sql.= "AND A.activo = $activo ";
    if (!empty($filter)) $sql.= "AND A.nombre LIKE '%$filter%' ";
    if (!empty($id_tripulante)) $sql.= "AND EXISTS (SELECT 1 FROM via_viajes_vehiculos_tripulantes VT WHERE VT.id_empresa = A.id_empresa AND VT.id_viaje = A.id AND VT.id_tripulante = $id_tripulante) ";
    if (!empty($order)) $sql.= "ORDER BY $order ";
    $sql.= "LIMIT $limit, $offset ";
    $query = $this->db->query($sql);

    $q_total = $this->db->query("SELECT FOUND_ROWS() AS total");
    $total = $q_total->row();

    $result = $query->result();
    foreach($result as $r) {

      $r->total_asientos = 0;
      $r->total_ocupados = 0;

      // Tomamos los vehiculos del viaje
      $sql = "SELECT DISTINCT id_vehiculo FROM via_viajes_vehiculos_tripulantes ";
      $sql.= "WHERE id_viaje = $r->id AND id_empresa = $r->id_empresa ";
      $qq = $this->db->query($sql);
      foreach($qq->result() as $vehiculo) {

        // Sumamos la capacidad
        $sql = "SELECT IF(COUNT(*) IS NULL,0,COUNT(*)) AS cantidad FROM via_vehiculos_asientos VA ";
        $sql.= "WHERE VA.id_vehiculo = $vehiculo->id_vehiculo AND VA.id_empresa = $r->id_empresa ";
        $qqq = $this->db->query($sql);
        $rrr = $qqq->row();
        $r->total_asientos += $rrr->cantidad;

        // Sumamos la cantidad 
        $sql = "SELECT IF(COUNT(*) IS NULL,0,COUNT(*)) AS cantidad FROM via_reservas_asientos RA ";
        $sql.= " INNER JOIN via_reservas R ON (RA.id_reserva = R.id AND RA.id_empresa = R.id_empresa) ";
        $sql.= "WHERE RA.id_vehiculo = $vehiculo->id_vehiculo AND RA.id_empresa = $r->id_empresa ";
        $sql.= "AND R.id_viaje = $r->id ";
        $qqq = $this->db->query($sql);
        $rrr = $qqq->row();
        $r->total_ocupados += $rrr->cantidad;
      }
    }
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
    $vehiculos_tripulantes = $data->vehiculos_tripulantes;
    $precios = $data->precios;
    $relacionados = (isset($data->relacionados) ? $data->relacionados : array());
    $images = $data->images;
    $opcionales = $data->opcionales;
    unset($data->vehiculos_tripulantes);
    unset($data->vehiculos);
    unset($data->opcionales);
    unset($data->precios);
    unset($data->pasajeros);
    unset($data->undefined);
    unset($data->total_asientos);
    unset($data->total_ocupados);
    unset($data->categoria);
    $data->fecha = fecha_mysql($data->fecha);
    $data->fecha_creacion = fecha_mysql($data->fecha_creacion);
    $data->fecha_llegada = fecha_mysql($data->fecha_llegada);
    $data->custom_9 = htmlspecialchars_decode($data->custom_9);

    // Guardamos la primera imagen como path
    if (sizeof($images)>0) {
      $ppal = $images[0];
      $data->path = $ppal;
    }

    $id = parent::save($data);

    // Guardamos los vehiculos y tripulantes
    $this->db->query("DELETE FROM via_viajes_vehiculos_tripulantes WHERE id_viaje = $id AND id_empresa = $data->id_empresa ");
    foreach($vehiculos_tripulantes as $pv) {
      $sql = "INSERT INTO via_viajes_vehiculos_tripulantes (id_empresa,id_viaje,id_vehiculo,id_tripulante,comision) VALUES ($data->id_empresa,$id,$pv->id_vehiculo,$pv->id_tripulante,$pv->comision)";
      $this->db->query($sql);
    }

    // Guardamos las imagenes
    $this->db->query("DELETE FROM via_viajes_images WHERE id_viaje = $id AND id_empresa = $id_empresa");
    $k=0;
    foreach($images as $im) {
      $this->db->query("INSERT INTO via_viajes_images (id_empresa,id_viaje,path,orden) VALUES($id_empresa,$id,'$im',$k)");
      $k++;
    }

    // Guardamos los opcionales
    $this->db->query("DELETE FROM via_viajes_opcionales WHERE id_viaje = $id AND id_empresa = $id_empresa");
    $k=0;
    foreach($opcionales as $im) {
      $this->db->query("INSERT INTO via_viajes_opcionales (id_empresa,id_viaje,id_opcional,orden) VALUES($id_empresa,$id,'$im->id',$k)");
      $k++;
    }

    // Guardamos los relacionados
    $this->db->query("DELETE FROM via_viajes_relacionados WHERE id_viaje = $id AND id_empresa = $id_empresa");
    $k=0;
    foreach($relacionados as $im) {
      $this->db->query("INSERT INTO via_viajes_relacionados (id_empresa,id_viaje,id_relacion,orden) VALUES($id_empresa,$id,'$im->id',$k)");
      $k++;
    }

    // Guardamos los precios
    $this->db->query("DELETE FROM via_viajes_vehiculos_precios WHERE id_viaje = $id AND id_empresa = $data->id_empresa ");
    foreach($precios as $item) {
      if (!isset($item->id_tipo_tarifa)) $item->id_tipo_tarifa = 0;
      if (!isset($item->moneda)) $item->moneda = "$";
      $item->fecha_desde = (isset($item->fecha_desde) && !empty($item->fecha_desde)) ? fecha_mysql($item->fecha_desde) : '';
      $item->fecha_hasta = (isset($item->fecha_hasta) && !empty($item->fecha_hasta)) ? fecha_mysql($item->fecha_hasta) : '';
      $item->edad_desde = (isset($item->edad_desde) && !empty($item->edad_desde)) ? $item->edad_desde : '';
      $item->edad_hasta = (isset($item->edad_hasta) && !empty($item->edad_hasta)) ? $item->edad_hasta : '';
      $item->lunes = (isset($item->lunes) && !empty($item->lunes)) ? $item->lunes : 0;
      $item->martes = (isset($item->martes) && !empty($item->martes)) ? $item->martes : 0;
      $item->miercoles = (isset($item->miercoles) && !empty($item->miercoles)) ? $item->miercoles : 0;
      $item->jueves = (isset($item->jueves) && !empty($item->jueves)) ? $item->jueves : 0;
      $item->viernes = (isset($item->viernes) && !empty($item->viernes)) ? $item->viernes : 0;
      $item->sabado = (isset($item->sabado) && !empty($item->sabado)) ? $item->sabado : 0;
      $item->domingo = (isset($item->domingo) && !empty($item->domingo)) ? $item->domingo : 0;
      $item->recargo = (isset($item->recargo) && !empty($item->recargo)) ? $item->recargo : 0;
      $item->recargo_2 = (isset($item->recargo_2) && !empty($item->recargo_2)) ? $item->recargo_2 : 0;
      $item->recargo_3 = (isset($item->recargo_3) && !empty($item->recargo_3)) ? $item->recargo_3 : 0;
      $item->recargo_4 = (isset($item->recargo_4) && !empty($item->recargo_4)) ? $item->recargo_4 : 0;
      $sql = "INSERT INTO via_viajes_vehiculos_precios (";
      $sql.= " id_empresa,id_viaje,id_tipo_tarifa,precio,moneda,edad_desde,edad_hasta,fecha_desde,fecha_hasta,";
      $sql.= " lunes,martes,miercoles,jueves,viernes,sabado,domingo,recargo,recargo_2,recargo_3,recargo_4 ";
      $sql.= ") VALUES (";
      $sql.= " $data->id_empresa,$id,'$item->id_tipo_tarifa','$item->precio','$item->moneda','$item->edad_desde','$item->edad_hasta','$item->fecha_desde','$item->fecha_hasta',";
      $sql.= " '$item->lunes', '$item->martes', '$item->miercoles', '$item->jueves', '$item->viernes', '$item->sabado', '$item->domingo', ";
      $sql.= " '$item->recargo','$item->recargo_2','$item->recargo_3','$item->recargo_4' ";
      $sql.= ") ";
      $this->db->query($sql);
    }

    // Actualizamos el link
    $data->nombre = trim($data->nombre);
    $link = "viaje/".filename($data->nombre,"-",0)."-".$id."/";
    $this->db->query("UPDATE via_viajes SET link = '$link' WHERE id = $id AND id_empresa = $id_empresa");

    return $id;
  }

  function get($id,$config=array()) {

    $row = parent::get($id);
    if (empty($row)) return $row;

    $this->load->helper("fecha_helper");
    $row->fecha = fecha_es($row->fecha);
    $row->fecha_llegada = fecha_es($row->fecha_llegada);
    $row->fecha_creacion = fecha_es($row->fecha_creacion);

    // Obtenemos los vehiculos y tripulantes
    $sql = "SELECT VVT.*, VV.nombre AS vehiculo, VT.nombre AS tripulante, VV.patente, VT.dni, VV.cant_asientos_piso_1 ";
    $sql.= "FROM via_viajes_vehiculos_tripulantes VVT ";
    $sql.= " INNER JOIN via_vehiculos VV ON (VVT.id_vehiculo = VV.id AND VVT.id_empresa = VV.id_empresa) ";
    $sql.= " INNER JOIN via_tripulantes VT ON (VVT.id_tripulante = VT.id AND VVT.id_empresa = VT.id_empresa) ";
    $sql.= "WHERE VVT.id_viaje = $row->id AND VVT.id_empresa = $row->id_empresa ";
    $query = $this->db->query($sql);
    $row->vehiculos_tripulantes = $query->result();

    // Obtenemos los diferentes vehiculos utilizados en el viaje
    $sql = "SELECT DISTINCT VV.id, VV.nombre ";
    $sql.= "FROM via_viajes_vehiculos_tripulantes VVT ";
    $sql.= " INNER JOIN via_vehiculos VV ON (VVT.id_vehiculo = VV.id AND VVT.id_empresa = VV.id_empresa) ";
    $sql.= "WHERE VVT.id_viaje = $row->id AND VVT.id_empresa = $row->id_empresa ";
    $query = $this->db->query($sql);
    $row->vehiculos = $query->result();

    // Obtenemos las reservas realizadas
    $sql = "SELECT RA.*, ";
    $sql.= " IF (VA.piso IS NULL,'',VA.piso) AS piso, ";
    $sql.= " IF (VA.numero_asiento IS NULL,'',VA.numero_asiento) AS numero_asiento, ";
    $sql.= " IF (U.nombre IS NULL,'',U.nombre) AS nombre_usuario ";
    $sql.= "FROM via_reservas R INNER JOIN via_reservas_asientos RA ON (R.id = RA.id_reserva AND R.id_empresa = RA.id_empresa) ";
    $sql.= "INNER JOIN via_vehiculos_asientos VA ON (RA.id_asiento = VA.id AND RA.id_vehiculo = VA.id_vehiculo AND RA.id_empresa = VA.id_empresa) ";    
    $sql.= "LEFT JOIN com_usuarios U ON (R.id_usuario = U.id AND R.id_empresa = U.id_empresa) ";
    $sql.= "WHERE R.id_viaje = $id AND R.id_empresa = $row->id_empresa ";
    $sql.= "ORDER BY VA.numero_asiento ASC ";
    $q = $this->db->query($sql);
    $row->pasajeros = $q->result();

    // Obtenemos los precios
    $sql = "SELECT AI.*, ";
    $sql.= " IF(TT.nombre IS NULL,'',TT.nombre) AS nombre, ";
    $sql.= " IF(AI.fecha_desde = '0000-00-00','',DATE_FORMAT(AI.fecha_desde,'%d/%m/%Y')) AS fecha_desde, ";
    $sql.= " IF(AI.fecha_hasta = '0000-00-00','',DATE_FORMAT(AI.fecha_hasta,'%d/%m/%Y')) AS fecha_hasta ";
    $sql.= "FROM via_viajes_vehiculos_precios AI ";
    $sql.= "LEFT JOIN via_tipos_tarifas TT ON (AI.id_tipo_tarifa = TT.id AND AI.id_empresa = TT.id_empresa) ";
    $sql.= "WHERE AI.id_viaje = $id AND AI.id_empresa = $row->id_empresa ORDER BY AI.id ASC";
    $q = $this->db->query($sql);
    foreach($q->result() as $r) {
      $row->precios[] = $r;
    }

    // Obtenemos las imagenes de ese entrada
    $sql = "SELECT AI.* FROM via_viajes_images AI WHERE AI.id_viaje = $id AND AI.id_empresa = $row->id_empresa ORDER BY AI.orden ASC";
    $q = $this->db->query($sql);
    $row->images = array();
    foreach($q->result() as $r) {
      $row->images[] = $r->path;
    }

    // Obtenemos los opcionales
    $sql = "SELECT O.* ";
    $sql.= "FROM via_viajes_opcionales VO ";
    $sql.= "INNER JOIN via_opcionales O ON (VO.id_opcional = O.id AND VO.id_empresa = O.id_empresa) ";
    $sql.= "WHERE VO.id_viaje = $id AND VO.id_empresa = $row->id_empresa ";
    $sql.= "ORDER BY VO.orden ASC";
    $q = $this->db->query($sql);
    $row->opcionales = array();
    foreach($q->result() as $r) {
      $row->opcionales[] = $r;
    }

    // Obtenemos los relacionados
    $sql = "SELECT V.nombre, V.id ";
    $sql.= "FROM via_viajes_relacionados VO ";
    $sql.= "INNER JOIN via_viajes V ON (VO.id_relacion = V.id AND VO.id_empresa = V.id_empresa) ";
    $sql.= "WHERE VO.id_viaje = $id AND VO.id_empresa = $row->id_empresa ";
    $sql.= "ORDER BY VO.orden ASC";
    $q = $this->db->query($sql);
    $row->relacionados = array();
    foreach($q->result() as $r) {
      $row->relacionados[] = $r;
    }

    $row->custom_9 = htmlspecialchars($row->custom_9);

    return $row;
  }

  function delete($id) {
    $id_empresa = parent::get_empresa();
    $this->db->query("DELETE FROM via_viajes_opcionales WHERE id_empresa = $id_empresa AND id_viaje = $id");
    $this->db->query("DELETE FROM via_viajes_vehiculos_precios WHERE id_empresa = $id_empresa AND id_viaje = $id");
    $this->db->query("DELETE FROM via_viajes_vehiculos_tripulantes WHERE id_empresa = $id_empresa AND id_viaje = $id");
    $this->db->query("DELETE FROM via_viajes_vehiculos WHERE id_empresa = $id_empresa AND id_viaje = $id");
    $this->db->query("DELETE FROM via_viajes_images WHERE id_empresa = $id_empresa AND id_viaje = $id");
    $this->db->query("DELETE FROM via_viajes WHERE id_empresa = $id_empresa AND id = $id");
  }

}