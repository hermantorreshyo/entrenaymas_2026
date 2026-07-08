<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Repartidor_Caja_Movimiento_Model extends Abstract_Model {

  private $total = 0;
  
  function __construct() {
    parent::__construct("repartidores_cajas_movimientos","id");
  }

  function depositar_en_comercio($config = array()) {

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $fecha = isset($config["fecha"]) ? $config["fecha"] : date("Y-m-d H:i:s");
    $monto = isset($config["monto"]) ? $config["monto"] : 0;
    $id_repartidor = isset($config["id_repartidor"]) ? $config["id_repartidor"] : 0;
    $observaciones = isset($config["observaciones"]) ? $config["observaciones"] : "";
    $id_usuario = isset($config["id_usuario"]) ? $config["id_usuario"] : 0;
    $id_concepto = 1509;
    $id_articulo = 1; // ARTICULO ESPECIAL QUE IDENTIFICA UN DEPOSITO

    // Obtenemos los datos del comercio
    $this->load->model("Usuario_Model");
    $usuario = $this->Usuario_Model->get($id_usuario,array(
      "id_empresa"=>$id_empresa,
    ));
    $observaciones = "Anticipo a $usuario->nombre. ".((!empty($observaciones)) ? "Obs. Repartidor: ".$observaciones : "");

    // Obtenemos los datos del repartidor
    $this->load->model("Repartidor_Model");
    $repartidor = $this->Repartidor_Model->get($id_repartidor,array(
      "id_empresa"=>$id_empresa,
    ));

    // Registramos en el comercio como un pedido en negativo
    $id_punto_venta = 2444;
    $this->load->model("Factura_Model");
    $factura = $this->Factura_Model->crear(array(
      "id_empresa"=>$id_empresa,
      "id_punto_venta"=>$id_punto_venta,
      "id_usuario"=>$id_usuario,
      "usuario"=>$usuario->nombre,
      "observaciones"=>"Deposito de ".$repartidor->nombre,
      "tipo_pago"=>"E",
      "id_tipo_estado"=>0,
      "id_vendedor"=>$id_repartidor,
      "items"=>array(
        array(
          "id_articulo"=>1,
          "cantidad"=>$monto,
        )
      ),
    ));

    // Agregamos el egreso de la caja del repartidor, en estado PENDIENTE
    $c = array(
      "fecha"=>$fecha,
      "monto"=>$monto,
      "id_repartidor"=>$id_repartidor,
      "id_concepto"=>$id_concepto,
      "id_factura"=>$factura["id_factura"],
      "id_punto_venta"=>$id_punto_venta,
      "observaciones"=>$factura["comprobante"]." ".$observaciones,
      "estado"=>1, // QUEDA EN PENDIENTE HASTA QUE EL COMERCIO LO ACEPTE
      "id_empresa"=>$id_empresa,
    );
    $this->egreso($c);
    return array("error"=>0);
  }

  function registrar_movimiento($config = array())  {

    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $fecha = isset($config["fecha"]) ? $config["fecha"] : date("Y-m-d H:i:s");
    $monto = isset($config["monto"]) ? $config["monto"] : 0;
    $id_repartidor = isset($config["id_repartidor"]) ? $config["id_repartidor"] : 0;
    $tipo = isset($config["tipo"]) ? $config["tipo"] : 0;
    $id_concepto = isset($config["id_concepto"]) ? $config["id_concepto"] : 0;
    $observaciones = isset($config["observaciones"]) ? $config["observaciones"] : '';
    $id_usuario = isset($config["id_usuario"]) ? $config["id_usuario"] : 0;
    $id_factura = isset($config["id_factura"]) ? $config["id_factura"] : 0;
    $id_punto_venta = isset($config["id_punto_venta"]) ? $config["id_punto_venta"] : 0;

    // 0 = REALIZADO // 1 = PENDIENTE
    $estado = isset($config["estado"]) ? $config["estado"] : 0;

    $sql = "INSERT INTO repartidores_cajas_movimientos (";
    $sql.= " id_empresa, id_repartidor, tipo, monto, id_concepto, observaciones, fecha, id_usuario, id_factura, id_punto_venta, estado ";
    $sql.= ") VALUES (";
    $sql.= "'$id_empresa','$id_repartidor','$tipo','$monto','$id_concepto','$observaciones','$fecha', '$id_usuario', '$id_factura', '$id_punto_venta','$estado' ";
    $sql.= ")";
    $this->db->query($sql);
    $id = $this->db->insert_id();
    return $id;
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
    $id_repartidor = isset($config["id_repartidor"]) ? $config["id_repartidor"] : 0;
    $id_factura = isset($config["id_factura"]) ? $config["id_factura"] : 0;
    $id_punto_venta = isset($config["id_punto_venta"]) ? $config["id_punto_venta"] : 0;
    if ($id_factura != 0 || $id != 0) {
      $sql = "DELETE FROM repartidores_cajas_movimientos ";
      $sql.= "WHERE id_empresa = $id_empresa ";
      if (!empty($id)) $sql.= "AND id = $id ";
      if (!empty($id_repartidor)) $sql.= "AND id_repartidor = $id_repartidor ";
      if (!empty($id_factura)) $sql.= "AND id_factura = $id_factura ";
      if (!empty($id_punto_venta)) $sql.= "AND id_punto_venta = $id_punto_venta ";
      $this->db->query($sql);
    }
  }

  function save($data) {
    $this->load->helper("fecha_helper");
    $data->fecha = fecha_mysql($data->fecha);
    return parent::save($data);
  }

  function calcular_saldo($config = array()) {

    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $desde = isset($config["desde"]) ? $config["desde"] : date("Y-m-d",strtotime(date("Y-m-d")." +1 day"));
    $id_repartidor = isset($config["id_repartidor"]) ? $config["id_repartidor"] : 0;

    $sql = "SELECT SUM(IF(G.tipo = 0,G.monto,-G.monto)) AS saldo ";
    $sql.= "FROM repartidores_cajas_movimientos G ";
    $sql.= "WHERE G.fecha < '$desde 00:00:00' ";
    $sql.= "AND G.id_empresa = $id_empresa ";
    $sql.= "AND G.estado = 0 ";
    if (!empty($id_repartidor)) $sql.= "AND G.id_repartidor = '$id_repartidor' ";
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
    $id_repartidor = isset($config["id_repartidor"]) ? $config["id_repartidor"] : 0;
    $id_concepto = isset($config["id_concepto"]) ? $config["id_concepto"] : 0;
    $id_factura = isset($config["id_factura"]) ? $config["id_factura"] : 0;
    $limit = isset($config["limit"]) ? $config["limit"] : 0;
    $offset = isset($config["offset"]) ? $config["offset"] : 100;

    // Calculamos el saldo inicial
    $saldo_inicial = $this->calcular_saldo(array(
      "id_repartidor"=>$id_repartidor,
      "desde"=>$desde,
      "id_empresa"=>$id_empresa,
    ));

    $sql = "SELECT SQL_CALC_FOUND_ROWS G.*, ";
    $sql.= " DATE_FORMAT(G.fecha,'%d/%m/%Y %H:%i') AS fecha, ";
    $sql.= " IF (G.id_concepto = 0,'',TG.nombre) AS concepto ";
    $sql.= "FROM repartidores_cajas_movimientos G ";
    $sql.= "LEFT JOIN tipos_gastos TG ON (TG.id = G.id_concepto AND TG.id_empresa = G.id_empresa) ";
    $sql.= "WHERE G.id_empresa = $id_empresa ";
    if (!empty($desde)) $sql.= "AND G.fecha >= '$desde' ";
    if (!empty($hasta)) $sql.= "AND G.fecha <= '$hasta' ";
    if (!empty($id_repartidor)) $sql.= "AND G.id_repartidor = '$id_repartidor' ";
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