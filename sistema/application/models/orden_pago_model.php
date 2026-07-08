<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Orden_Pago_Model extends Abstract_Model {
  
  function __construct() {
    parent::__construct("compras","id");
  }

  function buscar($config = array()) {
    $id_empresa = (isset($config["id_empresa"])) ? $config["id_empresa"] : parent::get_empresa();
    $desde = (isset($config["desde"])) ? $config["desde"] : "";
    $hasta = (isset($config["hasta"])) ? $config["hasta"] : "";
    $order = (isset($config["order"])) ? $config["order"] : "";
    $order_by = (isset($config["order_by"])) ? $config["order_by"] : "";
    $tipo_proveedor = (isset($config["tipo_proveedor"])) ? $config["tipo_proveedor"] : 0;
    $in_tipo_proveedor = (isset($config["in_tipo_proveedor"])) ? $config["in_tipo_proveedor"] : "";
    $limit = (isset($config["limit"])) ? $config["limit"] : 0;
    $offset = (isset($config["offset"])) ? $config["offset"] : 30;
    $filter = (isset($config["filter"])) ? $config["filter"] : 0;
    $id_sucursal = (isset($config["id_sucursal"])) ? $config["id_sucursal"] : 0;    

    if (empty($order)) $order = "DESC";
    if (empty($order_by)) $order_by = "C.fecha";

    $sql = "SELECT SQL_CALC_FOUND_ROWS C.* ";
    $sql.= "FROM compras C ";
    $sql.= " INNER JOIN proveedores P ON (C.id_proveedor = P.id AND C.id_empresa = P.id_empresa) ";
    $sql.= "LEFT JOIN almacenes S ON (C.id_sucursal = S.id AND C.id_empresa = S.id_empresa) ";
    $sql.= "WHERE C.id_empresa = $id_empresa ";
    $sql.= "AND C.id_tipo_comprobante = -1 ";
    if (!empty($desde)) $sql.= "AND '$desde' <= C.fecha ";
    if (!empty($hasta)) $sql.= "AND C.fecha <= '$hasta' ";
    if (!empty($tipo_proveedor)) $sql.= "AND P.tipo_proveedor = $tipo_proveedor ";
    if (!empty($in_tipo_proveedor)) $sql.= "AND P.tipo_proveedor IN ($in_tipo_proveedor) ";
    if (!empty($id_sucursal)) $sql.= "AND C.id_sucursal = $id_sucursal ";
    if (!empty($filter)) {
      if (is_numeric($filter)) {
        $sql.= "AND C.numero_2 = '$filter' ";
      } else {
        $sql.= "AND P.nombre LIKE '%$filter%' ";
      }
    }
    if (!empty($order)) $sql.= "ORDER BY $order_by $order ";
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
    );
  }

  // Utilizado en las estadisticas, lo unico que tiene diferencia con el buscar es que no se llama
  // al metodo get() dentro de foreach
  function get_list($config=array()) {
    $id_empresa = (isset($config["id_empresa"])) ? $config["id_empresa"] : parent::get_empresa();
    $desde = (isset($config["desde"])) ? $config["desde"] : "";
    $hasta = (isset($config["hasta"])) ? $config["hasta"] : "";
    $order = (isset($config["order"])) ? $config["order"] : "";
    $tipo_proveedor = (isset($config["tipo_proveedor"])) ? $config["tipo_proveedor"] : 0;
    $in_tipo_proveedor = (isset($config["in_tipo_proveedor"])) ? $config["in_tipo_proveedor"] : "";
    $limit = (isset($config["limit"])) ? $config["limit"] : 0;
    $offset = (isset($config["offset"])) ? $config["offset"] : 0;
    $id_sucursal = (isset($config["id_sucursal"])) ? $config["id_sucursal"] : 0;
    $sql = "SELECT C.*, P.nombre, ";
    $sql.= " IF(S.nombre IS NULL,'',S.nombre) AS sucursal, ";
    $sql.= "ABS(C.efectivo) AS efectivo, ";
    $sql.= "ABS(C.total_depositos) AS total_depositos, ";
    $sql.= "ABS(C.descuento) AS descuento, ";
    $sql.= "ABS(C.rotura) AS rotura, ";
    $sql.= "ABS(C.total_general) AS total_general, ";
    $sql.= "DATE_FORMAT(C.fecha,'%d/%m/%Y') AS fecha ";
    $sql.= "FROM compras C ";
    $sql.= " INNER JOIN proveedores P ON (C.id_proveedor = P.id AND C.id_empresa = P.id_empresa) ";
    $sql.= "LEFT JOIN almacenes S ON (C.id_sucursal = S.id AND C.id_empresa = S.id_empresa) ";
    $sql.= "WHERE C.id_empresa = $id_empresa ";
    $sql.= "AND C.id_tipo_comprobante = -1 ";
    if (!empty($desde)) $sql.= "AND '$desde' <= C.fecha ";
    if (!empty($hasta)) $sql.= "AND C.fecha <= '$hasta' ";
    if (!empty($tipo_proveedor)) $sql.= "AND P.tipo_proveedor = $tipo_proveedor ";
    if (!empty($in_tipo_proveedor)) $sql.= "AND P.tipo_proveedor IN ($in_tipo_proveedor) ";
    if (!empty($id_sucursal)) $sql.= "AND C.id_sucursal = $id_sucursal ";
    if (!empty($order)) $sql.= "ORDER BY $order ";
    if (!empty($offset)) $sql.= "LIMIT $limit, $offset ";
    $q = $this->db->query($sql);
    return $q->result();
  }
  
  function pendientes($id_proveedor = 0) {
    $id_empresa = parent::get_empresa();
    $sql = "SELECT C.*, P.id AS id_proveedor, ";
    $sql.= "DATE_FORMAT(C.fecha,'%d/%m/%Y') AS fecha, ";
    $sql.= "P.nombre AS proveedor ";
    $sql.= "FROM compras C ";
    $sql.= "INNER JOIN proveedores P ON (C.id_proveedor = P.id AND C.id_empresa = P.id_empresa) ";
    $sql.= "WHERE C.fecha_entregado = '0000-00-00' ";
    $sql.= "AND C.id_empresa = $id_empresa ";
    $sql.= "AND C.id_tipo_comprobante = -1 ";
    if (!empty($id_proveedor)) $sql.= "AND C.id_proveedor = $id_proveedor ";
    $q = $this->db->query($sql);
    return $q->result();
  }

  function get($id) {
    $id_empresa = parent::get_empresa();
    // Obtenemos la compra
    $sql = "SELECT ";
    $sql.= "C.id, C.id_empresa, C.id_proveedor, C.compra_real, ";
    $sql.= "IF(SUC.nombre IS NULL,'',SUC.nombre) AS sucursal, ";
    $sql.= "DATE_FORMAT(C.fecha,'%d/%m/%Y') AS fecha, ";
    $sql.= "P.porc_ret_ib, P.aplica_ret_ganancias, ";
    $sql.= "P.nombre AS proveedor, ";
    $sql.= "C.forma_pago AS forma_pago, ";
    $sql.= "P.tipo_proveedor AS tipo_proveedor, ";
    $sql.= "P.cuit AS cuit, P.direccion AS direccion, ";
    $sql.= "P.convenio_multilateral AS convenio_multilateral, ";
    $sql.= "P.cuenta_bancaria, P.cbu, ";
    $sql.= "IF(B.nombre IS NULL,'',B.nombre) AS banco, ";
    $sql.= "L.nombre AS localidad, ";
    $sql.= "ABS(C.efectivo) AS efectivo, ";
    $sql.= "ABS(C.descuento) AS descuento, ";
    $sql.= "ABS(C.rotura) AS rotura, ";
    $sql.= "ABS(C.ret_ing_brutos) AS ret_ing_brutos, ";
    $sql.= "ABS(C.ret_ganancias) AS ret_ganancias, ";
    $sql.= "ABS(C.total_general) AS total_general, ";
    $sql.= "C.numero_1, C.numero_2, ";
    $sql.= "C.observaciones AS observaciones, ";
    $sql.= "C.numero_certificado_ret_ganancias, ";
    $sql.= "C.numero_certificado_ret_ib, ";
    $sql.= "C.total_depositos ";
    $sql.= "FROM compras C ";
    $sql.= "INNER JOIN proveedores P ON (C.id_proveedor = P.id AND C.id_empresa = P.id_empresa) ";
    $sql.= "LEFT JOIN almacenes SUC ON (C.id_sucursal = SUC.id AND C.id_empresa = SUC.id_empresa) ";
    $sql.= "LEFT JOIN localidades L ON (P.id_localidad = L.id) ";
    $sql.= "LEFT JOIN bancos B ON (P.id_banco = B.id) ";
    $sql.= "WHERE C.id = $id ";
    $sql.= "AND C.id_empresa = $id_empresa ";
    $query = $this->db->query($sql);
    $row = $query->row();

    // Seleccionamos los cheques de la orden de pago
    $sql = "SELECT CH.id, CH.id_orden_pago, CH.titular, ";
    $sql.= "CH.tipo, CH.id_banco, CH.id_chequera, ";
    $sql.= "CH.titular, CH.numero, CH.id_cliente, ";
    $sql.= "CH.monto, CH.sucursal, CH.entregado, ";
    $sql.= "CH.anulado, CH.devuelto, CH.motivo, ";
    $sql.= "DATE_FORMAT(CH.fecha_emision,'%d/%m/%Y') AS fecha_emision, ";
    $sql.= "DATE_FORMAT(CH.fecha_cobro,'%d/%m/%Y') AS fecha_cobro, ";
    $sql.= "IF(B.nombre IS NULL,'',B.nombre) AS banco ";
    $sql.= "FROM cheques CH ";
    $sql.= "LEFT JOIN bancos B ON (CH.id_banco = B.id) ";
    $sql.= "WHERE id_orden_pago = $id ";
    $sql.= "AND CH.id_empresa = $id_empresa ";
    $q_cheques = $this->db->query($sql);
    $cheques = array();
    $total_cheques = 0;
    foreach($q_cheques->result() as $cheque) {
      $cheques[] = $cheque;
      $total_cheques = $total_cheques + $cheque->monto;
    }
    $row->cheques = $cheques;
    $row->total_cheques = (float)$total_cheques;

    // Seleccionamos los depositos
    /*
    $sql = "SELECT CD.*, ";
    $sql.= " IF (CB.nombre IS NULL,'',CB.nombre) AS cuenta ";
    $sql.= "FROM compras_depositos CD ";
    $sql.= "LEFT JOIN cuentas_bancarias CB ON (CD.id_empresa = CB.id_empresa AND CD.id_cuenta = CB.id) ";
    $sql.= "WHERE CD.id_empresa = $id_empresa ";
    $sql.= "AND CD.id_orden_pago = $id ";
    $sql.= "ORDER BY CD.orden ASC ";
    $row->depositos = array();
    $q_depositos = $this->db->query($sql);
    foreach($q_depositos->result() as $dep) {
      $row->depositos[] = $dep;
    }
    */

    $row->pendiente = 0;

    $sql = "SELECT CD.*, CB.nombre AS caja, CB.tipo ";
    $sql.= "FROM cajas_movimientos CD ";
    $sql.= "INNER JOIN cajas CB ON (CD.id_empresa = CB.id_empresa AND CD.id_caja = CB.id) ";
    $sql.= "WHERE CD.id_empresa = $id_empresa ";
    $sql.= "AND CB.tipo = 1 ";
    $sql.= "AND CD.id_orden_pago = $id ";
    $sql.= "ORDER BY CD.id ASC ";
    $row->depositos = array();
    $row->total_depositos = 0;
    $q_depositos = $this->db->query($sql);
    foreach($q_depositos->result() as $dep) {
      if ($dep->estado == 1) $row->pendiente = 1;
      $row->total_depositos += (float)$dep->monto;
      $row->depositos[] = $dep;
    }

    $sql = "SELECT CD.*, CB.nombre AS caja, CB.tipo ";
    $sql.= "FROM cajas_movimientos CD ";
    $sql.= "INNER JOIN cajas CB ON (CD.id_empresa = CB.id_empresa AND CD.id_caja = CB.id) ";
    $sql.= "WHERE CD.id_empresa = $id_empresa ";
    $sql.= "AND CB.tipo = 0 ";
    $sql.= "AND CD.id_orden_pago = $id ";
    $sql.= "ORDER BY CD.id ASC ";
    $row->movimientos_efectivo = array();
    $q_movimientos_efectivo = $this->db->query($sql);
    foreach($q_movimientos_efectivo->result() as $dep) {
      if ($dep->estado == 1) $row->pendiente = 1;
      $row->movimientos_efectivo[] = $dep;
    }

    // Seleccionamos los comprobantes de la orden de pago
    /*
    $sql = "SELECT ";
    $sql.= "DATE_FORMAT(C.fecha,'%d/%m/%Y') AS fecha, ";
    $sql.= "C.id, CONCAT(C.numero_1,'-',C.numero_2) AS numero, ";
    $sql.= "C.id_tipo_comprobante AS id_tipo_comprobante, ";
    $sql.= "TC.nombre AS tipo_comprobante, ";
    $sql.= "ABS(C.total_neto) AS total_neto, ABS(C.total_iva) AS total_iva, ";
    $sql.= "IF(C.total_general>0,C.total_general,0) AS monto, ";
    $sql.= "IF(C.total_general<0,ABS(C.total_general),0) AS pago, ";
    $sql.= "0 AS por_cancelar, 0 AS resto "; // Variables utilizadas en la vista
    $sql.= "FROM compras C ";
    $sql.= "INNER JOIN tipos_comprobante TC ON (TC.id = C.id_tipo_comprobante) ";
    $sql.= "WHERE C.id_orden_pago = $id ";
    $sql.= "AND C.id_empresa = $id_empresa ";
    $sql.= "ORDER BY C.numero_1 ASC, C.numero_2 ASC ";
    */
    $sql = "SELECT ";
    $sql.= "DATE_FORMAT(C.fecha,'%d/%m/%Y') AS fecha, ";
    $sql.= "C.id, CONCAT(C.numero_1,'-',C.numero_2) AS numero, ";
    $sql.= "C.id_tipo_comprobante AS id_tipo_comprobante, ";
    $sql.= "TC.nombre AS tipo_comprobante, ";
    $sql.= "TC.letra AS letra, TC.simbolo AS simbolo, ";
    $sql.= "ABS(C.total_neto) AS total_neto, ABS(C.total_iva) AS total_iva, ";
    $sql.= "IF(C.total_general>0,C.total_general,0) AS monto, ";
    $sql.= "IF(C.total_general<0,ABS(C.total_general),0) AS pago, ";
    $sql.= "CP.monto AS por_cancelar, CP.monto AS resto "; // Variables utilizadas en la vista
    $sql.= "FROM compras C ";
    $sql.= "INNER JOIN compras_pagos CP ON (C.id_empresa = CP.id_empresa AND CP.id_sucursal = C.id_sucursal AND C.id = CP.id_factura) ";
    $sql.= "INNER JOIN tipos_comprobante TC ON (TC.id = C.id_tipo_comprobante) ";
    $sql.= "WHERE CP.id_pago = $id ";
    $sql.= "AND C.id_empresa = $id_empresa ";
    $sql.= "ORDER BY C.numero_1 ASC, C.numero_2 ASC ";
    $q_comprobantes = $this->db->query($sql);
    $comprobantes = array();
    foreach($q_comprobantes->result() as $comprobante) {
      $comprobantes[] = $comprobante;
      //$total_cheques = $total_cheques + $cheque->monto;
    }
    $row->comprobantes = $comprobantes;
    $this->db->close();
    return $row;
  }
}