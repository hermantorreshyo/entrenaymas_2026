<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Toque_Billetera_Movimiento_Model extends Abstract_Model {

  private $total = 0;
  
  function __construct() {
    parent::__construct("toque_billetera_movimientos","id");
  }

  function registrar_movimiento($config = array())  {

    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $fecha = isset($config["fecha"]) ? $config["fecha"] : date("Y-m-d H:i:s");
    $monto = isset($config["monto"]) ? $config["monto"] : 0;
    $id_cliente = isset($config["id_cliente"]) ? $config["id_cliente"] : 0;
    $tipo = isset($config["tipo"]) ? $config["tipo"] : 0;
    $id_concepto = isset($config["id_concepto"]) ? $config["id_concepto"] : 0;
    $observaciones = isset($config["observaciones"]) ? $config["observaciones"] : '';
    $id_usuario = isset($config["id_usuario"]) ? $config["id_usuario"] : 0;
    $id_factura = isset($config["id_factura"]) ? $config["id_factura"] : 0;
    $id_punto_venta = isset($config["id_punto_venta"]) ? $config["id_punto_venta"] : 0;

    $sql = "INSERT INTO toque_billetera_movimientos (";
    $sql.= " id_empresa, id_cliente, tipo, monto, id_concepto, observaciones, fecha, id_usuario, id_factura, id_punto_venta ";
    $sql.= ") VALUES (";
    $sql.= "'$id_empresa','$id_cliente','$tipo','$monto','$id_concepto','$observaciones','$fecha', '$id_usuario', '$id_factura', '$id_punto_venta' ";
    $sql.= ")";
    $this->db->query($sql);
    $id = $this->db->insert_id();

    // Despues de cualquier movimiento, actualizamos el saldo del cliente
    $this->actualizar_saldo_cliente(array(
      "id_empresa"=>$id_empresa,
      "id_cliente"=>$id_cliente,
    ));

    return $id;
  }

  function actualizar_saldo_cliente($config = array()) {
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $id_cliente = isset($config["id_cliente"]) ? $config["id_cliente"] : 0;
    $saldo = $this->calcular_saldo(array(
      "id_empresa"=>$id_empresa,
      "id_cliente"=>$id_cliente,
    ));
    $sql = "UPDATE clientes SET saldo_inicial = $saldo ";
    $sql.= "WHERE id_empresa = $id_empresa ";
    $sql.= "AND id = $id_cliente ";
    $this->db->query($sql);
  }

  function ingreso($config = array()) {
    $config["tipo"] = 0;
    $id = $this->registrar_movimiento($config);
    return $id;
  }

  function insert($data) {
    $id = parent::insert($data);
    return $id;
  }

  function egreso($config = array()) {
    $config["tipo"] = 1;
    $id = $this->registrar_movimiento($config);
    return $id;
  }

  function borrar($config = array()) {
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $id = isset($config["id"]) ? $config["id"] : 0;
    $id_cliente = isset($config["id_cliente"]) ? $config["id_cliente"] : 0;
    $id_factura = isset($config["id_factura"]) ? $config["id_factura"] : 0;
    $id_punto_venta = isset($config["id_punto_venta"]) ? $config["id_punto_venta"] : 0;
    $tipo = isset($config["tipo"]) ? $config["tipo"] : -1;

    $sql = "SELECT * FROM toque_billetera_movimientos ";
    $sql.= "WHERE id_empresa = $id_empresa ";
    if (!empty($id)) $sql.= "AND id = $id ";
    if (!empty($id_cliente)) $sql.= "AND id_cliente = $id_cliente ";
    if (!empty($id_factura)) $sql.= "AND id_factura = $id_factura ";
    if (!empty($id_punto_venta)) $sql.= "AND id_punto_venta = $id_punto_venta ";
    if ($tipo != -1) $sql.= "AND tipo = '$tipo' ";
    $q = $this->db->query($sql);
    if ($q->num_rows() <= 0) return;
    $r = $q->row();

    if ($id_factura != 0 || $id != 0) {
      $sql = "DELETE FROM toque_billetera_movimientos ";
      $sql.= "WHERE id_empresa = $id_empresa ";
      if (!empty($id)) $sql.= "AND id = $id ";
      if (!empty($id_cliente)) $sql.= "AND id_cliente = $id_cliente ";
      if (!empty($id_factura)) $sql.= "AND id_factura = $id_factura ";
      if (!empty($id_punto_venta)) $sql.= "AND id_punto_venta = $id_punto_venta ";
      if ($tipo != -1) $sql.= "AND tipo = '$tipo' ";
      $this->db->query($sql);
    }
    // Despues de cualquier movimiento, actualizamos el saldo del cliente
    $this->actualizar_saldo_cliente(array(
      "id_empresa"=>$id_empresa,
      "id_cliente"=>$r->id_cliente,
    ));    
  }

  function save($data) {
    $this->load->helper("fecha_helper");
    $data->fecha = fecha_mysql($data->fecha);
    $id = parent::save($data);

    // Despues de cualquier movimiento, actualizamos el saldo del cliente
    $this->actualizar_saldo_cliente(array(
      "id_empresa"=>$data->id_empresa,
      "id_cliente"=>$data->id_cliente,
    ));
    return $id;
  }

  function calcular_saldo($config = array()) {

    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $desde = isset($config["desde"]) ? $config["desde"] : date("Y-m-d",strtotime(date("Y-m-d")." +1 day"));
    $id_cliente = isset($config["id_cliente"]) ? $config["id_cliente"] : 0;

    $sql = "SELECT SUM(IF(G.tipo = 0,G.monto,-G.monto)) AS saldo ";
    $sql.= "FROM toque_billetera_movimientos G ";
    $sql.= "WHERE G.fecha < '$desde 00:00:00' ";
    $sql.= "AND G.id_empresa = $id_empresa ";
    if (!empty($id_cliente)) $sql.= "AND G.id_cliente = '$id_cliente' ";
    $q = $this->db->query($sql);
    $r = $q->row();
    return (is_null($r->saldo)) ? 0 : $r->saldo;
  }

  function buscar($config = array()) {
  
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $desde = isset($config["desde"]) ? $config["desde"] : "";
    $hasta = isset($config["hasta"]) ? $config["hasta"] : "";
    $orden_pago = isset($config["orden_pago"]) ? $config["orden_pago"] : -1;
    $tipo = isset($config["tipo"]) ? $config["tipo"] : -1;
    $id_cliente = isset($config["id_cliente"]) ? $config["id_cliente"] : 0;
    $id_concepto = isset($config["id_concepto"]) ? $config["id_concepto"] : 0;
    $id_factura = isset($config["id_factura"]) ? $config["id_factura"] : 0;
    $limit = isset($config["limit"]) ? $config["limit"] : 0;
    $offset = isset($config["offset"]) ? $config["offset"] : 100;

    // Calculamos el saldo inicial
    $saldo_inicial = $this->calcular_saldo(array(
      "id_cliente"=>$id_cliente,
      "desde"=>$desde,
      "id_empresa"=>$id_empresa,
    ));

    $sql = "SELECT SQL_CALC_FOUND_ROWS G.*, ";
    $sql.= " DATE_FORMAT(G.fecha,'%d/%m/%Y %H:%i') AS fecha, ";
    $sql.= " IF (G.id_concepto = 0,'',TG.nombre) AS concepto ";
    $sql.= "FROM toque_billetera_movimientos G ";
    $sql.= "LEFT JOIN tipos_gastos TG ON (TG.id = G.id_concepto AND TG.id_empresa = G.id_empresa) ";
    $sql.= "WHERE G.id_empresa = $id_empresa ";
    if (!empty($desde)) $sql.= "AND G.fecha >= '$desde' ";
    if (!empty($hasta)) $sql.= "AND G.fecha <= '$hasta' ";
    if (!empty($id_cliente)) $sql.= "AND G.id_cliente = '$id_cliente' ";
    if (!empty($id_concepto)) $sql.= "AND G.id_concepto = '$id_concepto' ";
    if (!empty($id_factura)) $sql.= "AND G.id_factura = '$id_factura' ";
    if ($tipo != -1) $sql.= "AND G.tipo = '$tipo' ";

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

}