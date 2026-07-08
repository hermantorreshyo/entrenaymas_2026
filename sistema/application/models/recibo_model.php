<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Recibo_Model extends Abstract_Model {
  
  function __construct() {
    parent::__construct("facturas","id","nombre ASC");
  }

  // Marca a una factura como pagada si todos los pagos completan el monto
  function marcar_pagada($config = array()) {
    $id_empresa = (isset($config["id_empresa"])) ? $config["id_empresa"] : parent::get_empresa();
    $id_punto_venta = (isset($config["id_punto_venta"])) ? $config["id_punto_venta"] : 0;
    $id_factura = (isset($config["id_factura"])) ? $config["id_factura"] : 0;
    $sql = "SELECT IF(SUM(FP.monto) IS NULL,0,SUM(FP.monto)) AS monto ";
    $sql.= "FROM facturas_pagos FP ";
    $sql.= "WHERE id_factura = $id_factura AND id_empresa = $id_empresa ";
    if ($id_punto_venta != 0) $sql.= "AND id_punto_venta = $id_punto_venta ";
    $q = $this->db->query($sql);
    $r = $q->row();
    if ($r->monto == 0) return;
    $sql = "UPDATE facturas SET pagada = 1, id_tipo_estado = 6, tipo_pago = 'C', pago = -(efectivo - vuelto + tarjeta + cheque) ";
    $sql.= "WHERE id = $id_factura AND id_empresa = $id_empresa ";
    if ($id_punto_venta != 0) $sql.= "AND id_punto_venta = $id_punto_venta ";
    $sql.= "AND pagada = 0 ";
    $sql.= "AND total = $r->monto ";
    $this->db->query($sql);
  }

  function buscar($config = array()) {

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    $id_empresa = (isset($config["id_empresa"])) ? $config["id_empresa"] : parent::get_empresa();
    $desde = (isset($config["desde"])) ? $config["desde"] : "";
    $hasta = (isset($config["hasta"])) ? $config["hasta"] : "";
    $order = (isset($config["order"])) ? $config["order"] : "";
    $order_by = (isset($config["order_by"])) ? $config["order_by"] : "";
    $limit = (isset($config["limit"])) ? $config["limit"] : 0;
    $offset = (isset($config["offset"])) ? $config["offset"] : 0;
    $id_sucursal = (isset($config["id_sucursal"])) ? $config["id_sucursal"] : 0;
    $id_cliente = (isset($config["id_cliente"])) ? $config["id_cliente"] : 0;
    $id_usuario = (isset($config["id_usuario"])) ? $config["id_usuario"] : 0;
    $filter = (isset($config["filter"])) ? $config["filter"] : "";
    $estado = (isset($config["estado"])) ? $config["estado"] : 1;

    if (empty($order)) $order = "C.numero";
    if (empty($order_by)) $order_by = "DESC";

    $sql = "SELECT SQL_CALC_FOUND_ROWS C.* ";
    $sql.= "FROM facturas C ";
    $sql.= " INNER JOIN clientes P ON (C.id_cliente = P.id AND C.id_empresa = P.id_empresa) ";
    $sql.= "LEFT JOIN almacenes S ON (C.id_sucursal = S.id AND C.id_empresa = S.id_empresa) ";
    $sql.= "WHERE C.id_empresa = $id_empresa ";
    $sql.= "AND C.tipo = 'P' ";
    if ($estado == 0) $sql.= "AND C.estado = 0 ";
    if (!empty($filter)) $sql.= "AND (C.cliente LIKE '%$filter%' OR C.comprobante LIKE '%$filter%') ";
    if (!empty($desde)) $sql.= "AND '$desde' <= C.fecha ";
    if (!empty($hasta)) $sql.= "AND C.fecha <= '$hasta' ";
    if (!empty($id_cliente)) $sql.= "AND C.id_cliente = $id_cliente ";
    if (!empty($id_usuario)) $sql.= "AND C.id_usuario = $id_usuario ";
    if (!empty($id_sucursal)) $sql.= "AND C.id_sucursal = $id_sucursal ";
    if (!empty($order)) $sql.= "ORDER BY $order $order_by ";
    if (!empty($offset)) $sql.= "LIMIT $limit, $offset ";
    $q = $this->db->query($sql);
    $salida = array();

    $q_total = $this->db->query("SELECT FOUND_ROWS() AS total");
    $total = $q_total->row();

    foreach($q->result() as $r) {
      $salida[] = $this->get($r->id);
    }
    return array(
      "results"=>$salida,
      "total"=>$total->total,
      "sql"=>$sql,
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

  function get($id,$config = array()) {

    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $id_punto_venta = isset($config["id_punto_venta"]) ? $config["id_punto_venta"] : 0;
    
    $sql = "SELECT ";
    $sql.= " F.id, F.efectivo, F.id_empresa, F.id_usuario, F.id_cliente, F.numero, F.total, F.vuelto, F.descuento, C.nombre AS cliente, F.comprobante, ";
    $sql.= " F.retencion_suss, F.retencion_iva, F.retencion_otras, F.punto_venta, ";
    $sql.= " DATE_FORMAT(F.fecha,'%d/%m/%Y') AS fecha, F.custom_7, F.custom_8, F.observaciones, F.pago, F.id_sucursal, F.sucursal ";
    $sql.= "FROM facturas F ";
    $sql.= "INNER JOIN clientes C ON (C.id_empresa = F.id_empresa AND C.id = F.id_cliente) ";
    $sql.= "WHERE F.id_empresa = $id_empresa ";
    $sql.= "AND F.id = $id ";
    if ($id_punto_venta != 0) $sql.= "AND F.id_punto_venta = $id_punto_venta ";
    $q = $this->db->query($sql);
    $recibo = $q->row();
    if ($recibo === FALSE) { return FALSE; }

    // TODO: Usamos el custom_7 y custom_8 para las retenciones aplicadas al pago, pero en un futuro debemos separar estos campos
    $recibo->retencion_iibb = (empty($recibo->custom_7) ? 0 : $recibo->custom_7);
    $recibo->retencion_ganancias = (empty($recibo->custom_8) ? 0 : $recibo->custom_8);
    
    // Tomamos los comprobantes
    $sql = "SELECT FP.monto AS haber, 0 AS total_pagado, F.total AS debe, F.total, F.pago, F.comprobante, ";
    $sql.= " IF(TC.nombre IS NULL,'',TC.nombre) AS tipo_comprobante, TC.negativo, ";
    $sql.= " DATE_FORMAT(F.fecha,'%d/%m/%Y') AS fecha ";
    $sql.= "FROM facturas_pagos FP INNER JOIN facturas F ON (FP.id_factura = F.id AND FP.id_empresa = F.id_empresa) ";
    $sql.= "INNER JOIN tipos_comprobante TC ON (TC.id = F.id_tipo_comprobante) ";
    $sql.= "WHERE FP.id_pago = $id ";
    if ($id_punto_venta != 0) $sql.= "AND F.id_punto_venta = $id_punto_venta ";
    $sql.= "AND FP.id_empresa = $recibo->id_empresa ";
    $sql.= "ORDER BY F.fecha DESC, F.comprobante DESC ";
    $q = $this->db->query($sql);
    $recibo->comprobantes = $q->result();
    foreach($recibo->comprobantes as $r) {
      if ($r->negativo == 1) {
        $r->total = - $r->total;
        $r->debe = - $r->debe;
        $r->total_pagado = 0;
      }
    }

    // Tomamos los depositos
    $sql = "SELECT D.*, IF(C.nombre IS NULL,'',C.nombre) AS caja ";
    $sql.= "FROM cajas_movimientos D INNER JOIN cajas C ON (D.id_caja = C.id AND D.id_empresa = C.id_empresa) ";
    $sql.= "WHERE D.id_factura = $id ";
    $sql.= "AND D.id_empresa = $recibo->id_empresa ";
    $sql.= "AND C.tipo = 1 ";
    if ($id_punto_venta != 0) $sql.= "AND D.id_punto_venta = $id_punto_venta ";
    $q = $this->db->query($sql);
    $recibo->depositos = $q->result();
    $recibo->total_depositos = 0;
    foreach($recibo->depositos as $r) {
      $recibo->total_depositos += $r->monto;
    }

    // Tomamos los depositos
    $sql = "SELECT D.*, IF(C.nombre IS NULL,'',C.nombre) AS caja ";
    $sql.= "FROM cajas_movimientos D INNER JOIN cajas C ON (D.id_caja = C.id AND D.id_empresa = C.id_empresa) ";
    $sql.= "WHERE D.id_factura = $id ";
    $sql.= "AND D.id_empresa = $recibo->id_empresa ";
    if ($id_punto_venta != 0) $sql.= "AND D.id_punto_venta = $id_punto_venta ";
    $sql.= "AND C.tipo = 0 ";
    $q = $this->db->query($sql);
    $recibo->movimientos_efectivo = $q->result();
    $recibo->efectivo_2 = 0;
    foreach($recibo->movimientos_efectivo as $r) {
      $recibo->efectivo_2 += $r->monto;
    }
    // TODO: Hack para cuando no tienen caja asignada
    if ($recibo->efectivo == 0 && $recibo->efectivo_2 != 0) {
      $recibo->efectivo = $recibo->efectivo_2;
    }
    
    // Tomamos los cheques
    $sql = "SELECT C.*, IF(B.nombre IS NULL,'',B.nombre) AS banco, ";
    $sql.= " DATE_FORMAT(C.fecha_emision,'%d/%m/%Y') AS fecha_emision, ";
    $sql.= " DATE_FORMAT(C.fecha_cobro,'%d/%m/%Y') AS fecha_cobro ";
    $sql.= "FROM cheques C LEFT JOIN bancos B ON (C.id_banco = B.id) ";
    $sql.= "WHERE C.id_recibo = $id ";
    $sql.= "AND C.id_empresa = $recibo->id_empresa ";
    //if ($id_punto_venta != 0) $sql.= "AND C.id_punto_venta = $id_punto_venta ";
    $q = $this->db->query($sql);
    $recibo->cheques = $q->result();
    $recibo->total_cheques = 0;
    foreach($recibo->cheques as $r) {
      $recibo->total_cheques += $r->monto;
    }

    // Tomamos las tarjetas
    $sql = "SELECT C.*, IF(T.nombre IS NULL,'',T.nombre) AS tarjeta, ";
    $sql.= " DATE_FORMAT(C.fecha,'%d/%m/%Y') AS fecha ";
    $sql.= "FROM cupones_tarjetas C INNER JOIN tarjetas T ON (C.id_tarjeta = T.id AND C.id_empresa = T.id_empresa) ";
    $sql.= "WHERE C.id_factura = $id ";
    $sql.= "AND C.id_empresa = $recibo->id_empresa ";
    if ($id_punto_venta != 0) $sql.= "AND C.id_punto_venta = $id_punto_venta ";
    $q = $this->db->query($sql);
    $recibo->tarjetas = $q->result();
    $recibo->total_tarjetas = 0;
    foreach($recibo->tarjetas as $r) {
      $recibo->total_tarjetas += ((float)$r->importe + (float)$r->interes);
    }
    
    return $recibo;
  }

}