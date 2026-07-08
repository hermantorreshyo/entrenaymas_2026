<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Cheque_Model extends Abstract_Model {

  private $total = 0;
  private $suma = 0;
  private $sql = "";
  
  function __construct() {
    parent::__construct("cheques","id");
  }

  function get_total_cheques($conf = array()) {

    $movimiento = isset($conf["movimiento"]) ? $conf["movimiento"] : ""; // Formato mmYY
    $id_empresa = isset($conf["id_empresa"]) ? $conf["id_empresa"] : parent::get_empresa();
    $desde = (isset($conf["desde"])) ? $conf["desde"] : "";
    $hasta = (isset($conf["hasta"])) ? $conf["hasta"] : "";
    $tipo = (isset($conf["tipo"]) && !empty($conf["tipo"])) ? $conf["tipo"] : "P";

    $sql = "SELECT IF(SUM(CH.monto) IS NULL,0,SUM(CH.monto)) AS total ";
    $sql.= "FROM cheques CH ";
    $sql.= " INNER JOIN compras C ON (CH.id_orden_pago = C.id AND CH.id_empresa = C.id_empresa) ";
    $sql.= " INNER JOIN proveedores P ON (C.id_proveedor = P.id AND CH.id_empresa = P.id_empresa) ";
    $sql.= "WHERE CH.id_empresa = $id_empresa ";
    $sql.= "AND P.tipo_proveedor = 1 "; // Solo los proveedores
    $sql.= "AND CH.tipo = '$tipo' ";
    if (!empty($movimiento)) {
      $mes = substr($movimiento, 0, 2);
      $anio = "20".substr($movimiento, 2);
      $sql.= "AND MONTH(CH.fecha_cobro) = '$mes' AND YEAR(CH.fecha_cobro) = '$anio' ";  
    }
    if (!empty($desde)) $sql.= "AND '$desde' <= CH.fecha_cobro ";
    if (!empty($hasta)) $sql.= "AND CH.fecha_cobro <= '$hasta' ";
    $sql.= "AND CH.anulado = 0 ";
    $sql.= "AND CH.devuelto = 0 ";
    $sql.= "AND CH.id_orden_pago != 0 ";
    $sql.= "AND CH.tipo = 'P' ";
    $q = $this->db->query($sql);
    $row = $q->row();
    if (is_null($row->total)) return 0;
    else return (float)$row->total;
  }

  function save($array) {
    $this->load->helper("fecha_helper");
    unset($array->banco);
    unset($array->cliente);
    unset($array->proveedor);
    unset($array->orden_pago);
    unset($array->error);
    unset($array->mensaje);
    $array->fecha_emision = fecha_mysql($array->fecha_emision);
    $array->fecha_cobro = fecha_mysql($array->fecha_cobro);
    if (isset($array->fecha_debitado) && !empty($array->fecha_debitado)) $array->fecha_debitado = fecha_mysql($array->fecha_debitado);
    return parent::save($array);
  }
  
  function get($id,$array = array()) {
    // Si $id == 0, es porque estamos comprobando si existe alguno con el mismo numero
    $id_empresa = isset($array["id_empresa"]) ? $array["id_empresa"] : parent::get_empresa();
    $numero = isset($array["numero"]) ? $array["numero"] : 0;
    $id_banco = isset($array["id_banco"]) ? $array["id_banco"] : 0;
    $tipo = isset($array["tipo"]) ? $array["tipo"] : "P";
    $sql = "SELECT C.*, ";
    $sql.= "  IF(C.fecha_emision='0000-00-00','',DATE_FORMAT(C.fecha_emision,'%d/%m/%Y')) AS fecha_emision, ";
    $sql.= "  IF(C.fecha_cobro='0000-00-00','',DATE_FORMAT(C.fecha_cobro,'%d/%m/%Y')) AS fecha_cobro, ";
    $sql.= "  IF(C.fecha_debitado='0000-00-00','',DATE_FORMAT(C.fecha_debitado,'%d/%m/%Y')) AS fecha_debitado, ";
    $sql.= "  IF(CL.nombre IS NULL,'',CL.nombre) AS cliente, ";
    $sql.= "  IF(B.nombre IS NULL,'',B.nombre) AS banco, ";
    $sql.= "  IF(CAJ.nombre IS NULL,'',CAJ.nombre) AS caja_depositado ";
    $sql.= "FROM cheques C ";
    $sql.= "LEFT JOIN bancos B ON (C.id_banco = B.id) ";
    $sql.= "LEFT JOIN clientes CL ON (C.id_cliente = CL.id AND C.id_empresa = CL.id_empresa) ";
    $sql.= "LEFT JOIN cajas CAJ ON (C.id_caja_depositado = CAJ.id AND C.id_empresa = CAJ.id_empresa) ";
    $sql.= "WHERE C.id_empresa = $id_empresa ";
    if ($id != 0) $sql.= "AND C.id = $id ";
    if (!empty($numero)) $sql.= "AND C.numero = '$numero' ";
    if (!empty($id_banco)) $sql.= "AND C.id_banco = '$id_banco' ";
    if (!empty($tipo)) $sql.= "AND C.tipo = '$tipo' ";
    $query = $this->db->query($sql);
    $row = $query->row(); 
    $this->db->close();
    return $row;    
  }
  
  /**
   * Devuelve todos los registros de la tabla
   * @return Lista de registros
   */
  function buscar($conf = array()) {
    
    $id_empresa = parent::get_empresa();
    $order_by = (isset($conf["order_by"])) ? $conf["order_by"] : "C.fecha_emision";
    $order = (isset($conf["order"])) ? $conf["order"] : "DESC";
    $limit = (isset($conf["limit"])) ? $conf["limit"] : 0;
    $offset = (isset($conf["offset"])) ? $conf["offset"] : 20;
    $id_banco = (isset($conf["id_banco"])) ? $conf["id_banco"] : 0;
    $id_proveedor = (isset($conf["id_proveedor"])) ? $conf["id_proveedor"] : 0;
    $id_sucursal = (isset($conf["id_sucursal"])) ? $conf["id_sucursal"] : 0;
    $id_cliente = (isset($conf["id_cliente"])) ? $conf["id_cliente"] : 0;
    $titular = (isset($conf["titular"])) ? $conf["titular"] : "";
    $desde = (isset($conf["desde"])) ? $conf["desde"] : "";
    $hasta = (isset($conf["hasta"])) ? $conf["hasta"] : "";
    $fecha_comparacion = (isset($conf["fecha_comparacion"]) && !empty($conf["fecha_comparacion"])) ? $conf["fecha_comparacion"] : "C.fecha_cobro";
    $tipo = (isset($conf["tipo"]) && !empty($conf["tipo"])) ? $conf["tipo"] : "P";
    $entregado = (isset($conf["entregado"])) ? $conf["entregado"] : -1;
    $id_cliente = (isset($conf["id_cliente"])) ? $conf["id_cliente"] : 0;
    $numero = (isset($conf["numero"])) ? $conf["numero"] : "";
    $filter = (isset($conf["filter"])) ? $conf["filter"] : "";
    $mostrar_tipo = (isset($conf["mostrar_tipo"])) ? $conf["mostrar_tipo"] : 0;
    
    $sql = "SELECT SQL_CALC_FOUND_ROWS C.*, ";
    $sql.= "  IF(C.fecha_emision = '0000-00-00','',DATE_FORMAT(C.fecha_emision,'%d/%m/%Y')) AS fecha_emision, ";
    $sql.= "  IF(C.fecha_cobro = '0000-00-00','',DATE_FORMAT(C.fecha_cobro,'%d/%m/%Y')) AS fecha_cobro, ";
    $sql.= "  IF(C.fecha_debitado = '0000-00-00','',DATE_FORMAT(C.fecha_debitado,'%d/%m/%Y')) AS fecha_debitado, ";
    $sql.= "  IF(OP.numero_2 IS NULL,'',OP.numero_2) AS orden_pago, ";
    $sql.= "  IF(CL.nombre IS NULL,'',CL.nombre) AS cliente, ";
    $sql.= "  IF(P.nombre IS NULL,'',P.nombre) AS proveedor, ";
    $sql.= "  IF(F.comprobante IS NULL,'',F.comprobante) AS comprobante, ";
    $sql.= "  IF(REC.id_sucursal IS NULL,0,REC.id_sucursal) AS id_sucursal, ";
    $sql.= "  IF(B.nombre IS NULL,'',B.nombre) AS banco, ";
    $sql.= "  IF(CAJ.nombre IS NULL,'',CAJ.nombre) AS caja_depositado, ";
    $sql.= "  IF(CAJ_OR.nombre IS NULL,'',CAJ_OR.nombre) AS caja_origen ";
    $sql.= "FROM cheques C ";
    $sql.= "LEFT JOIN bancos B ON (C.id_banco = B.id) ";
    $sql.= "LEFT JOIN clientes CL ON (C.id_cliente = CL.id AND C.id_empresa = CL.id_empresa) ";
    $sql.= "LEFT JOIN proveedores P ON (C.id_proveedor = P.id AND C.id_empresa = P.id_empresa) ";
    $sql.= "LEFT JOIN compras OP ON (C.id_orden_pago = OP.id AND C.id_empresa = OP.id_empresa) ";
    $sql.= "LEFT JOIN facturas F ON (C.id_factura = F.id AND C.id_punto_venta = F.id_punto_venta AND C.id_empresa = F.id_empresa) ";
    $sql.= "LEFT JOIN facturas REC ON (C.id_recibo = REC.id AND REC.id_punto_venta = 0 AND C.id_empresa = REC.id_empresa) ";
    $sql.= "LEFT JOIN cajas CAJ ON (C.id_caja_depositado = CAJ.id AND C.id_empresa = CAJ.id_empresa) ";
    $sql.= "LEFT JOIN cajas CAJ_OR ON (C.id_caja_origen = CAJ_OR.id AND C.id_empresa = CAJ_OR.id_empresa) ";
    $sql.= "WHERE C.id_empresa = $id_empresa ";
    if (!empty($tipo)) $sql.= "AND C.tipo = '$tipo' ";
    if ($id_banco != 0) $sql.= "AND C.id_banco = $id_banco ";
    if (!empty($id_sucursal)) {
      $sql.= "AND IF(C.id_recibo != 0 AND C.tipo = 'T',REC.id_sucursal,OP.id_sucursal) = $id_sucursal ";
    }
    if ($id_cliente != 0) $sql.= "AND C.id_cliente = $id_cliente ";
    if ($entregado != -1) $sql.= "AND C.entregado = $entregado ";
    if (!empty($numero)) $sql.= "AND C.numero LIKE '%$numero%' ";
    if (!empty($titular)) $sql.= "AND C.titular LIKE '%$titular%' ";
    if (!empty($filter)) {
      // Si el filtro es todo numero
      if (is_numeric($filter)) $sql.= "AND C.numero LIKE '%$filter%' ";
      else $sql.= "AND (P.nombre LIKE '%$filter%' OR CL.nombre LIKE '%$filter%') ";
    }
    if (!empty($desde)) $sql.= "AND '$desde' <= $fecha_comparacion ";
    if (!empty($hasta)) $sql.= "AND $fecha_comparacion <= '$hasta' ";
    if ($mostrar_tipo == "D") {
        // Solo debitados
        $sql.= "AND C.fecha_debitado != '0000-00-00' ";
    } else if ($mostrar_tipo == "N") {
        // No debitados
        $sql.= "AND C.fecha_debitado = '0000-00-00' ";
    } else if ($mostrar_tipo == "A") {
        // Anulados
        $sql.= "AND C.anulado = 1 ";
    } else if ($mostrar_tipo == "E") {
        // No Entregados
        $sql.= "AND C.id_orden_pago = 0 AND C.id_caja_depositado = 0 ";
    }
    //$sql.= "AND C.anulado = $mostrar_tipo ";
    $sql.= "ORDER BY $order_by $order ";
    if (!is_null($limit) && (strlen($limit)>0) && !is_null($offset) && (strlen($offset)>0)) {
      $sql.= "LIMIT $limit, $offset ";
    }
    $query = $this->db->query($sql);
    $result = $query->result();
    $this->sql = $sql;

    $q_total = $this->db->query("SELECT FOUND_ROWS() AS total");
    $total = $q_total->row();
    $this->total = $total->total;

    // Realizamos la suma
    $sql = "SELECT IF(SUM(C.monto) IS NULL,0,SUM(C.monto)) AS monto ";
    $sql.= "FROM cheques C ";
    $sql.= "LEFT JOIN bancos B ON (C.id_banco = B.id) ";
    $sql.= "LEFT JOIN clientes CL ON (C.id_cliente = CL.id AND C.id_empresa = CL.id_empresa) ";
    $sql.= "LEFT JOIN proveedores P ON (C.id_proveedor = P.id AND C.id_empresa = P.id_empresa) ";
    $sql.= "LEFT JOIN compras OP ON (C.id_orden_pago = OP.id AND C.id_empresa = OP.id_empresa) ";
    $sql.= "LEFT JOIN facturas F ON (C.id_factura = F.id AND C.id_punto_venta = F.id_punto_venta AND C.id_empresa = F.id_empresa) ";
    $sql.= "LEFT JOIN facturas REC ON (C.id_recibo = REC.id AND REC.id_punto_venta = 0 AND C.id_empresa = REC.id_empresa) ";
    $sql.= "WHERE C.id_empresa = $id_empresa ";
    if (!empty($tipo)) $sql.= "AND C.tipo = '$tipo' ";
    if ($id_banco != 0) $sql.= "AND C.id_banco = $id_banco ";
    if (!empty($id_sucursal)) {
      $sql.= "AND IF(C.id_recibo != 0 AND C.tipo = 'P',REC.id_sucursal,OP.id_sucursal) = $id_sucursal ";
    }
    if ($id_cliente != 0) $sql.= "AND C.id_cliente = $id_cliente ";
    if ($entregado != -1) $sql.= "AND C.entregado = $entregado ";
    if (!empty($numero)) $sql.= "AND C.numero LIKE '%$numero%' ";
    if (!empty($titular)) $sql.= "AND C.titular LIKE '%$titular%' ";
    if (!empty($filter)) {
      // Si el filtro es todo numero
      if (is_numeric($filter)) $sql.= "AND C.numero LIKE '%$filter%' ";
      else $sql.= "AND (P.nombre LIKE '%$filter%' OR CL.nombre LIKE '%$filter%') ";
    }
    if (!empty($desde)) $sql.= "AND '$desde' <= $fecha_comparacion ";
    if (!empty($hasta)) $sql.= "AND $fecha_comparacion <= '$hasta' ";
    if ($mostrar_tipo == "D") {
        // Solo debitados
        $sql.= "AND C.fecha_debitado != '0000-00-00' ";
    } else if ($mostrar_tipo == "N") {
        // No debitados
        $sql.= "AND C.fecha_debitado = '0000-00-00' ";
    } else if ($mostrar_tipo == "A") {
        // Anulados
        $sql.= "AND C.anulado = 1 ";
    }
    $query2 = $this->db->query($sql);
    if ($query2->num_rows()>0) {
      $r2 = $query2->row();
      $this->suma = $r2->monto;
    }

    $this->db->close();
    return $result;
  }

  function get_total() {
    return $this->total;
  }
  function get_suma() {
    return $this->suma;
  }
  function get_sql() {
    return $this->sql;
  }
  
  function emitidos($fecha_desde,$fecha_hasta,$id_banco=0,$id_sucursal=0) {
    $id_empresa = parent::get_empresa();
    $sql = "SELECT IF(SUM(CH.monto) IS NULL,0,SUM(CH.monto)) AS total ";
    $sql.= "FROM cheques CH ";
    $sql.= " INNER JOIN compras C ON (CH.id_orden_pago = C.id AND CH.id_empresa = C.id_empresa) ";
    $sql.= "WHERE 1=1 ";
    $sql.= "AND CH.id_empresa = $id_empresa ";
    if ($id_banco != 0) $sql.= "AND CH.id_banco = $id_banco ";
    if ($id_sucursal != 0) $sql.= "AND C.id_sucursal = $id_sucursal ";
    $sql.= "AND CH.tipo = 'P' ";
    $sql.= "AND '$fecha_desde' <= CH.fecha_emision ";
    $sql.= "AND CH.fecha_emision <= '$fecha_hasta' ";
    $sql.= "AND CH.anulado = 0 ";
    $sql.= "AND CH.entregado = 1 AND CH.devuelto = 0 ";
    $sql.= "AND CH.id_orden_pago != 0 ";
    $q = $this->db->query($sql);
    $emitido = $q->row();
    return $emitido->total;
  }

  function get_by_op($id_orden_pago) {
    $id_empresa = parent::get_empresa();
    $sql = "SELECT * ";
    $sql.= "FROM cheques ";
    $sql.= "WHERE 1=1 ";
    $sql.= "AND id_empresa = $id_empresa ";
    $sql.= "AND id_orden_pago = $id_orden_pago ";
    $q = $this->db->query($sql);
    return $q->result();
  }
  
}