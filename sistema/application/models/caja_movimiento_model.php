<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Caja_Movimiento_Model extends Abstract_Model {

  private $total = 0;
  
  function __construct() {
    parent::__construct("cajas_movimientos","id");
  }

  function existe_movimiento($config = array())  {

    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $id_sucursal = isset($config["id_sucursal"]) ? $config["id_sucursal"] : 0;
    $id_caja_diaria = isset($config["id_caja_diaria"]) ? $config["id_caja_diaria"] : 0;
    $fecha = isset($config["fecha"]) ? $config["fecha"] : "";
    $monto = isset($config["monto"]) ? $config["monto"] : 0;
    $id_caja = isset($config["id_caja"]) ? $config["id_caja"] : 0;
    $tipo = isset($config["tipo"]) ? $config["tipo"] : 0;
    $id_concepto = isset($config["id_concepto"]) ? $config["id_concepto"] : 0;
    $id_cheque = isset($config["id_cheque"]) ? $config["id_cheque"] : 0;
    $id_usuario = isset($config["id_usuario"]) ? $config["id_usuario"] : 0;
    $id_orden_pago = isset($config["id_orden_pago"]) ? $config["id_orden_pago"] : 0;
    $id_factura = isset($config["id_factura"]) ? $config["id_factura"] : 0;
    $id_punto_venta = isset($config["id_punto_venta"]) ? $config["id_punto_venta"] : 0;
    $estado = isset($config["estado"]) ? $config["estado"] : 0;

    $sql = "SELECT 1 FROM cajas_movimientos ";
    $sql.= "WHERE id_empresa = $id_empresa ";
    if (!empty($id_sucursal)) $sql.= "AND id_sucursal = '$id_sucursal' ";
    if (!empty($monto)) $sql.= "AND monto = '$monto' ";
    if (!empty($id_caja)) $sql.= "AND id_caja = '$id_caja' ";
    if (!empty($id_caja_diaria)) $sql.= "AND id_caja_diaria = '$id_caja_diaria' ";
    if (!empty($id_punto_venta)) $sql.= "AND id_punto_venta = '$id_punto_venta' ";
    if (!empty($id_concepto)) $sql.= "AND id_concepto = '$id_concepto' ";
    if (!empty($id_factura)) $sql.= "AND id_factura = '$id_factura' ";
    if (!empty($id_orden_pago)) $sql.= "AND id_orden_pago = '$id_orden_pago' ";
    if (!empty($id_usuario)) $sql.= "AND id_usuario = '$id_usuario' ";
    if (!empty($tipo)) $sql.= "AND tipo = '$tipo' ";
    if (!empty($estado)) $sql.= "AND estado = '$estado' ";
    if (!empty($id_cheque)) $sql.= "AND id_cheque = '$id_cheque' ";
    if (!empty($fecha)) $sql.= "AND fecha = '$fecha' ";
    $q = $this->db->query($sql);
    return ($q->num_rows() > 0);
  }

  function registrar_movimiento($config = array())  {

    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $id_sucursal = isset($config["id_sucursal"]) ? $config["id_sucursal"] : 0;
    $id_caja_diaria = isset($config["id_caja_diaria"]) ? $config["id_caja_diaria"] : 0;
    $fecha = isset($config["fecha"]) ? $config["fecha"] : date("Y-m-d H:i:s");
    $monto = isset($config["monto"]) ? $config["monto"] : 0;
    $id_caja = isset($config["id_caja"]) ? $config["id_caja"] : 0;
    $tipo = isset($config["tipo"]) ? $config["tipo"] : 0;
    $id_concepto = isset($config["id_concepto"]) ? $config["id_concepto"] : 0;
    $id_cheque = isset($config["id_cheque"]) ? $config["id_cheque"] : 0;
    $observaciones = isset($config["observaciones"]) ? $config["observaciones"] : '';
    $id_usuario = isset($config["id_usuario"]) ? $config["id_usuario"] : 0;
    $id_orden_pago = isset($config["id_orden_pago"]) ? $config["id_orden_pago"] : 0;
    $id_factura = isset($config["id_factura"]) ? $config["id_factura"] : 0;
    $id_punto_venta = isset($config["id_punto_venta"]) ? $config["id_punto_venta"] : 0;
    $estado = isset($config["estado"]) ? $config["estado"] : 0;

    $sql = "INSERT INTO cajas_movimientos (";
    $sql.= " id_empresa, id_sucursal, id_caja, tipo, monto, id_concepto, observaciones, fecha, id_usuario, id_orden_pago, id_factura, estado, id_punto_venta, id_cheque, id_caja_diaria ";
    $sql.= ") VALUES (";
    $sql.= "'$id_empresa','$id_sucursal','$id_caja','$tipo','$monto','$id_concepto','$observaciones','$fecha', '$id_usuario', '$id_orden_pago', '$id_factura', '$estado', '$id_punto_venta', '$id_cheque', '$id_caja_diaria' ";
    $sql.= ")";
    $this->db->query($sql);
    $id = $this->db->insert_id();
    return $id;
  }

  function ingreso($config = array()) {
    $config["tipo"] = 0;
    $id = $this->registrar_movimiento($config);

    // Si se esta ingresando un cheque
    if (isset($config["id_cheque"]) && $config["id_cheque"] != 0) {
      $id_cheque = $config["id_cheque"];
      $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
      $id_caja = isset($config["id_caja"]) ? $config["id_caja"] : 0;
      $sql = "UPDATE cheques SET id_caja_origen = $id_caja WHERE id_empresa = $id_empresa AND id = $id_cheque";
      $this->db->query($sql);
    }
    return $id;
  }

  function insert($data) {
    $id = parent::insert($data);
    // Si se esta ingresando un cheque
    if (isset($data->id_cheque) && $data->id_cheque != 0) {
      $id_cheque = $data->id_cheque;
      $id_empresa = isset($data->id_empresa) ? $data->id_empresa : parent::get_empresa();
      $id_caja = isset($data->id_caja) ? $data->id_caja : 0;
      $sql = "UPDATE cheques SET id_caja_origen = $id_caja WHERE id_empresa = $id_empresa AND id = $id_cheque";
      $this->db->query($sql);
    }
    return $id;
  }

  function egreso($config = array()) {
    $config["tipo"] = 1;
    $id = $this->registrar_movimiento($config);
    return $id;
  }

  function ajuste($config = array()) {
    $config["tipo"] = 2;
    $id = $this->registrar_movimiento($config);
    return $id;
  }  

  function borrar($config = array()) {
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $id = isset($config["id"]) ? $config["id"] : 0;
    $id_caja = isset($config["id_caja"]) ? $config["id_caja"] : 0;
    $id_factura = isset($config["id_factura"]) ? $config["id_factura"] : 0;
    $id_cheque = isset($config["id_cheque"]) ? $config["id_cheque"] : 0;
    $id_orden_pago = isset($config["id_orden_pago"]) ? $config["id_orden_pago"] : 0;
    $id_punto_venta = isset($config["id_punto_venta"]) ? $config["id_punto_venta"] : 0;
    if ($id_factura != 0 || $id_orden_pago != 0 || $id != 0 || $id_cheque != 0) {
      $sql = "DELETE FROM cajas_movimientos ";
      $sql.= "WHERE id_empresa = $id_empresa ";
      if (!empty($id)) $sql.= "AND id = $id ";
      if (!empty($id_caja)) $sql.= "AND id_caja = $id_caja ";
      if (!empty($id_cheque)) $sql.= "AND id_cheque = $id_cheque ";
      if (!empty($id_factura)) $sql.= "AND id_factura = $id_factura ";
      if (!empty($id_punto_venta)) $sql.= "AND id_punto_venta = $id_punto_venta ";
      if (!empty($id_orden_pago)) $sql.= "AND id_orden_pago = $id_orden_pago ";
      $this->db->query($sql);

      // Si es un cheque, tenemos que limpiar id_caja_depositado
      if ($id_cheque != 0) {
        $sql = "UPDATE cheques SET id_caja_depositado = 0, id_caja_origen = 0 WHERE id_empresa = $id_empresa AND id = $id_cheque";
        $this->db->query($sql);
      }
    }
  }

  function save($data) {
    $this->load->helper("fecha_helper");
    $data->fecha = fecha_mysql($data->fecha);
    if (!empty($data->path)) $data->estado = 0;
    return parent::save($data);
  }

  function transferencia($config = array()) {

    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $fecha = isset($config["fecha"]) ? $config["fecha"] : date("Y-m-d H:i:s");
    $monto = isset($config["monto"]) ? $config["monto"] : 0;
    $id_caja_desde = isset($config["id_caja_desde"]) ? $config["id_caja_desde"] : 0;
    $id_caja_hasta = isset($config["id_caja_hasta"]) ? $config["id_caja_hasta"] : 0;
    $id_sucursal = isset($config["id_sucursal"]) ? $config["id_sucursal"] : 0;
    $id_usuario = isset($config["id_usuario"]) ? $config["id_usuario"] : 0;
    $observaciones = isset($config["observaciones"]) ? $config["observaciones"] : "";

    if (empty($monto)) {
      return array("error"=>1,"mensaje"=>"Por favor ingrese un monto.");  
    }
    $this->load->model("Caja_Model");
    $caja_desde = $this->Caja_Model->get($id_caja_desde);
    if (empty($caja_desde)) {
      return array("error"=>1,"mensaje"=>"No existe la caja de origen.");
    }
    $caja_hasta = $this->Caja_Model->get($id_caja_hasta);
    if (empty($caja_desde)) {
      return array("error"=>1,"mensaje"=>"No existe la caja de destino.");
    }

    // Calculamos los saldo que vamos a transferir
    $saldo_desde = $this->calcular_saldo(array(
      "id_caja"=>$id_caja_desde,
      "id_sucursal"=>$id_sucursal,
    ));
    //if ($saldo_desde < $monto) {
      //return array("error"=>1,"mensaje"=>"No hay suficiente dinero en $caja_desde->nombre para realizar la transferencia.");
    //}

    // Primero el Egreso
    $this->egreso(array(
      "id_empresa" => $id_empresa,
      "id_sucursal" => $id_sucursal,
      "id_caja"=>$id_caja_desde,
      "monto"=>$monto,
      "observaciones"=>(!empty($observaciones) ? $observaciones : "Transf. a $caja_hasta->nombre"),
      "fecha"=>$fecha,
      "id_usuario"=>$id_usuario,
    ));

    // Segundo el ingreso
    $this->ingreso(array(
      "id_empresa" => $id_empresa,
      "id_sucursal" => $id_sucursal,
      "id_caja"=>$id_caja_hasta,
      "monto"=>$monto,
      "observaciones"=>(!empty($observaciones) ? $observaciones : "Transf. de $caja_desde->nombre"),
      "fecha"=>$fecha,
      "id_usuario"=>$id_usuario,
    ));

    return array("error"=>0);
  }

  function calcular_saldo($config = array()) {

    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $desde = isset($config["desde"]) ? $config["desde"] : date("Y-m-d",strtotime(date("Y-m-d")." +1 day"));
    $id_caja = isset($config["id_caja"]) ? $config["id_caja"] : 0;
    $id_sucursal = isset($config["id_sucursal"]) ? $config["id_sucursal"] : 0;
    $id_usuario = isset($config["id_usuario"]) ? $config["id_usuario"] : 0;

    // Tomamos el ultimo ajuste de saldo
    $sql = "SELECT * FROM cajas_movimientos ";
    $sql.= "WHERE tipo = 2 AND id_empresa = $id_empresa ";
    $sql.= "AND fecha < '$desde 00:00:00' ";
    if (!empty($id_caja)) $sql.= "AND id_caja = '$id_caja' ";
    if (!empty($id_sucursal)) $sql.= "AND id_sucursal = '$id_sucursal' ";
    $sql.= "ORDER BY fecha DESC ";
    $sql.= "LIMIT 0,1 ";
    file_put_contents("log_caja_movimiento.txt", "SQL: ".$sql."\n", FILE_APPEND);
    $q = $this->db->query($sql);
    $saldo_inicial = 0;
    $ultimo_ajuste = "";
    if ($q->num_rows() > 0) {
      $r = $q->row();  
      $saldo_inicial = $r->monto;
      $ultimo_ajuste = $r->fecha;
    }
    
    $sql = "SELECT SUM(IF(G.tipo = 0,G.monto,-G.monto)) AS saldo ";
    $sql.= "FROM cajas_movimientos G ";
    $sql.= "WHERE G.fecha < '$desde 00:00:00' ";
    if (!empty($ultimo_ajuste)) $sql.= "AND G.fecha >= '$ultimo_ajuste' ";
    $sql.= "AND G.id_empresa = $id_empresa ";
    $sql.= "AND G.tipo != 2 "; // Por las dudas pero no seria necesario
    $sql.= "AND G.estado = 0 "; // Que este realizado
    if (!empty($id_caja)) $sql.= "AND G.id_caja = '$id_caja' ";
    if (!empty($id_sucursal)) $sql.= "AND G.id_sucursal = '$id_sucursal' ";
    if (!empty($id_usuario)) $sql.= "AND G.id_usuario = '$id_usuario' ";
    file_put_contents("log_caja_movimiento.txt", "SQL: ".$sql."\n", FILE_APPEND);
    $q = $this->db->query($sql);
    $r = $q->row();
    $saldo = ((is_null($r->saldo)) ? 0 : $r->saldo);
    return $saldo_inicial + $saldo;
  }

  function buscar($config = array()) {
  
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $desde = isset($config["desde"]) ? $config["desde"] : "";
    $hasta = isset($config["hasta"]) ? $config["hasta"] : "";
    $estado = isset($config["estado"]) ? $config["estado"] : -1;
    $orden_pago = isset($config["orden_pago"]) ? $config["orden_pago"] : -1;
    $tipo = isset($config["tipo"]) ? $config["tipo"] : -1;
    $id_caja = isset($config["id_caja"]) ? $config["id_caja"] : 0;
    $id_sucursal = isset($config["id_sucursal"]) ? $config["id_sucursal"] : 0;
    $id_usuario = isset($config["id_usuario"]) ? $config["id_usuario"] : 0;
    $id_concepto = isset($config["id_concepto"]) ? $config["id_concepto"] : 0;
    $id_factura = isset($config["id_factura"]) ? $config["id_factura"] : 0;
    $limit = isset($config["limit"]) ? $config["limit"] : 0;
    $ver_saldos = isset($config["ver_saldos"]) ? $config["ver_saldos"] : 0;
    $offset = isset($config["offset"]) ? $config["offset"] : 100;

    // Calculamos el saldo inicial
    $saldo_inicial = 0;
    if ($ver_saldos == 1) {
      $saldo_inicial = $this->calcular_saldo(array(
        "id_caja"=>$id_caja,
        "id_sucursal"=>$id_sucursal,
        "id_usuario"=>$id_usuario,
        "desde"=>$desde,
        "id_empresa"=>$id_empresa,
      ));
    }

    $sql = "SELECT SQL_CALC_FOUND_ROWS G.*, ";
    $sql.= " DATE_FORMAT(G.fecha,'%d/%m/%Y %H:%i') AS fecha, ";
    $sql.= " IF (G.id_concepto = 0,'',TG.nombre) AS concepto ";
    $sql.= "FROM cajas_movimientos G ";
    $sql.= "LEFT JOIN tipos_gastos TG ON (TG.id = G.id_concepto AND TG.id_empresa = G.id_empresa) ";
    $sql.= "WHERE G.id_empresa = $id_empresa ";
    if (!empty($id_sucursal)) $sql.= "AND G.id_sucursal = $id_sucursal ";
    if (!empty($desde)) $sql.= "AND G.fecha >= '$desde' ";
    if (!empty($hasta)) $sql.= "AND G.fecha <= '$hasta' ";
    if (!empty($id_caja)) $sql.= "AND G.id_caja = '$id_caja' ";
    if (!empty($id_usuario)) $sql.= "AND G.id_usuario = '$id_usuario' ";
    if (!empty($id_concepto)) $sql.= "AND G.id_concepto = '$id_concepto' ";
    if (!empty($id_factura)) $sql.= "AND G.id_factura = '$id_factura' ";
    if ($tipo != -1) $sql.= "AND G.tipo = '$tipo' ";
    if ($estado != -1) $sql.= "AND G.estado = '$estado' ";
    if ($orden_pago == 1) $sql.= "AND G.id_orden_pago > 0 ";
    else if ($orden_pago == 0) $sql.= "AND G.id_orden_pago = 0 ";

    $sql.= "ORDER BY G.fecha ASC, G.id ASC ";
    //if (!empty($offset)) $sql.= "LIMIT $limit,$offset ";
    $q = $this->db->query($sql);
    
    $q_total = $this->db->query("SELECT FOUND_ROWS() AS total");
    $total = $q_total->row();

    return array(
      "saldo_inicial"=>$saldo_inicial,
      "results"=>$q->result(),
      "total"=>$total->total,
      "sql"=>$sql,
    );
  }

  function get_arbol_por_cajas($config = array()) {

    @session_start();
    $id_padre = isset($config["id_padre"]) ? $config["id_padre"] : 0;
    $id_sucursal = isset($config["id_sucursal"]) ? $config["id_sucursal"] : 0;
    $tipo = isset($config["tipo"]) ? $config["tipo"] : 1;
    $desde = isset($config["desde"]) ? $config["desde"] : "";
    $hasta = isset($config["hasta"]) ? $config["hasta"] : "";
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $not_ids_conceptos = isset($config["not_ids_conceptos"]) ? $config["not_ids_conceptos"] : "";
    $ids_conceptos = isset($config["ids_conceptos"]) ? $config["ids_conceptos"] : "";
    $filtrar_cero = isset($config["filtrar_cero"]) ? $config["filtrar_cero"] : 0;

    $sql = "SELECT * FROM cajas WHERE id_empresa = $id_empresa ";
    if (!empty($id_sucursal)) $sql.= "AND id_sucursal = $id_sucursal ";
    $q = $this->db->query($sql);
    $cajas = array();
    foreach($q->result() as $caja) $cajas[] = $caja;

    $sql = "SELECT * FROM tipos_gastos ";
    $sql.= "WHERE id_empresa = $id_empresa ";
    $sql.= "AND id_padre = $id_padre ";
    if (!empty($not_ids_conceptos)) $sql.= "AND id NOT IN ($not_ids_conceptos) ";
    if (!empty($ids_conceptos)) $sql.= "AND id IN ($ids_conceptos) ";
    $sql.= "ORDER BY nombre ASC ";
    $query = $this->db->query($sql);
    $result = $query->result();
    $elementos = array();
    foreach($result as $row) {
      $e = new stdClass();
      $e->id = $row->id;
      $e->id_padre = $row->id_padre;
      $e->orden = $row->orden;
      $e->nombre = $row->nombre;
      $e->codigo = $row->codigo;
      $e->descripcion = $row->descripcion;
      $e->cajas = array();
      $e->total = 0;
      foreach($cajas as $caja) {
        $a = $this->resumen_compras_por_concepto(array(
          "id_concepto"=>$row->id,
          "id_empresa"=>$id_empresa,
          "id_sucursal"=>$id_sucursal,
          "id_caja"=>$caja->id,
          "desde"=>$desde,
          "hasta"=>$hasta,
          "tipo"=>$tipo,
        ));
        $t = ((float)$a["total"]);
        if (is_null($t)) $t = 0;
        $e->cajas[] = array(
          "id"=>$caja->id,
          "total"=>$t,
          "tipo"=>$caja->tipo,
        );
        $e->total += $t;
      }
      $this->total = $this->total + $e->total;
      $e->children = $this->get_arbol_por_cajas(array(
        "id_padre"=>$row->id,
        "id_empresa"=>$id_empresa,
        "id_sucursal"=>$id_sucursal,
        "desde"=>$desde,
        "hasta"=>$hasta,
        "tipo"=>$tipo,
      ));
      if ($filtrar_cero == 0) {
        $elementos[] = $e;
      } else if ($filtrar_cero == 1 && $e->total != 0) {
        $elementos[] = $e;
      }
    }
    return $elementos;  
  }

  function get_arbol($config = array()) {

    @session_start();
    $id_padre = isset($config["id_padre"]) ? $config["id_padre"] : 0;
    $id_sucursal = isset($config["id_sucursal"]) ? $config["id_sucursal"] : 0;
    $id_caja = isset($config["id_caja"]) ? $config["id_caja"] : 0;
    $desde = isset($config["desde"]) ? $config["desde"] : "";
    $hasta = isset($config["hasta"]) ? $config["hasta"] : "";
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();

    $sql = "SELECT * FROM tipos_gastos ";
    $sql.= "WHERE id_empresa = $id_empresa ";
    $sql.= "AND id_padre = $id_padre ";
    $sql.= "ORDER BY nombre ASC ";
    $query = $this->db->query($sql);
    $result = $query->result();
    $elementos = array();
    foreach($result as $row) {
      $e = new stdClass();
      $e->id = $row->id;
      $e->id_padre = $row->id_padre;
      $e->orden = $row->orden;
      $e->nombre = $row->nombre;
      $e->codigo = $row->codigo;
      $e->descripcion = $row->descripcion;
      $a = $this->resumen_compras_por_concepto(array(
        "id_concepto"=>$row->id,
        "id_empresa"=>$id_empresa,
        "id_sucursal"=>$id_sucursal,
        "id_caja"=>$id_caja,
        "desde"=>$desde,
        "hasta"=>$hasta,
      ));
      $e->total = $a["total"];
      $this->total = $this->total + $e->total;
      $e->children = $this->get_arbol(array(
        "id_padre"=>$row->id,
        "id_empresa"=>$id_empresa,
        "id_sucursal"=>$id_sucursal,
        "id_caja"=>$id_caja,
        "desde"=>$desde,
        "hasta"=>$hasta,
      ));
      $elementos[] = $e;
    }
    return $elementos;  
  }

  function resumen_compras_por_concepto($config = array()) {

    @session_start();
    $id_concepto = isset($config["id_concepto"]) ? $config["id_concepto"] : 0;
    $id_sucursal = isset($config["id_sucursal"]) ? $config["id_sucursal"] : 0;
    $desde = isset($config["desde"]) ? $config["desde"] : "";
    $hasta = isset($config["hasta"]) ? $config["hasta"] : "";
    $id_caja = isset($config["id_caja"]) ? $config["id_caja"] : 0;
    $tipo = isset($config["tipo"]) ? $config["tipo"] : 1;
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();

    // Tomamos los hijos
    $sql = "SELECT * FROM tipos_gastos WHERE id_padre = $id_concepto AND id_empresa = $id_empresa";
    $q_hijos = $this->db->query($sql);
    $hijos = $q_hijos->result();

    // Calculamos el total de ese concepto
    $sql = "SELECT ";
    $sql.= "  IF(SUM(C.monto) IS NULL,0,SUM(C.monto)) AS total ";
    $sql.= "FROM cajas_movimientos C ";
    $sql.= "INNER JOIN tipos_gastos CO ON (C.id_concepto = CO.id AND C.id_empresa = CO.id_empresa) ";
    $sql.= "WHERE C.id_concepto = $id_concepto ";
    $sql.= "AND C.tipo = $tipo ";
    $sql.= "AND C.id_empresa = $id_empresa ";
    if (!empty($desde)) $sql.= "AND C.fecha >= '$desde 00:00:00' ";
    if (!empty($hasta)) $sql.= "AND C.fecha <= '$hasta 23:59:59' ";
    if ($id_sucursal != 0) $sql.= "AND C.id_sucursal = $id_sucursal ";
    if ($id_caja != 0) $sql.= "AND C.id_caja = $id_caja ";
    $q = $this->db->query($sql);
    if ($q->num_rows()>0) {
      $row = $q->row();
      $total = $row->total;
    } else {
      $total = 0;
    }

    // Calculamos el total de todos los hijos
    foreach($hijos as $hijo) {
      $a = $this->resumen_compras_por_concepto(array(
        "id_concepto"=>$hijo->id,
        "id_empresa"=>$id_empresa,
        "id_sucursal"=>$id_sucursal,
        "id_caja"=>$id_caja,
        "desde"=>$desde,
        "hasta"=>$hasta,
        "tipo"=>$tipo,
      ));
      $total = $total + (float) $a["total"];
    }
    return array(
      "total"=>$total,
    );
  }

  function sumar_movimientos($config = array()) {
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $id_concepto = isset($config["id_concepto"]) ? $config["id_concepto"] : 0;
    $id_orden_pago = isset($config["id_orden_pago"]) ? $config["id_orden_pago"] : 0;
    $id_proveedor = isset($config["id_proveedor"]) ? $config["id_proveedor"] : 0;
    $id_caja = isset($config["id_caja"]) ? $config["id_caja"] : 0;
    $id_sucursal = isset($config["id_sucursal"]) ? $config["id_sucursal"] : 0;
    $tipo = isset($config["tipo"]) ? $config["tipo"] : 0;
    $estado = isset($config["estado"]) ? $config["estado"] : 0;
    $desde = isset($config["desde"]) ? $config["desde"] : "";
    $hasta = isset($config["hasta"]) ? $config["hasta"] : "";
    $sql = "SELECT ";
    $sql.= "  IF(SUM(C.monto) IS NULL,0,SUM(C.monto)) AS total ";
    $sql.= "FROM cajas_movimientos C ";
    $sql.= "INNER JOIN tipos_gastos CO ON (C.id_concepto = CO.id AND C.id_empresa = CO.id_empresa) ";
    $sql.= "WHERE C.id_concepto = $id_concepto ";
    $sql.= "AND C.tipo = $tipo ";
    if ($estado != -1) $sql.= "AND C.estado = $estado ";
    $sql.= "AND C.id_empresa = $id_empresa ";
    if (!empty($desde)) $sql.= "AND C.fecha >= '$desde 00:00:00' ";
    if (!empty($hasta)) $sql.= "AND C.fecha <= '$hasta 23:59:59' ";
    if ($id_sucursal != 0) $sql.= "AND C.id_sucursal = $id_sucursal ";
    if ($id_orden_pago != 0) $sql.= "AND C.id_orden_pago = $id_orden_pago ";
    if ($id_proveedor != 0) {
      $sql.= "AND EXISTS (SELECT 1 FROM compras CO WHERE CO.id_empresa = C.id_empresa AND CO.id = C.id_orden_pago AND CO.id_proveedor = $id_proveedor) ";
    }
    if ($id_caja != 0) $sql.= "AND C.id_caja = $id_caja ";
    $q = $this->db->query($sql);
    if ($q->num_rows()>0) {
      $row = $q->row();
      $total = $row->total;
      return $total;
    } else {
      $total = 0;
    }    
  }


}