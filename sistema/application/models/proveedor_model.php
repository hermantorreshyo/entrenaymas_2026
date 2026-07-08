<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Proveedor_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("proveedores","id","nombre ASC");
	}

  function listado_deuda($config = array()) {

    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $filtrar_en_cero = isset($config["filtrar_en_cero"]) ? $config["filtrar_en_cero"] : 0;
    $tipo_proveedor = isset($config["tipo_proveedor"]) ? $config["tipo_proveedor"] : 0;
    $id_sucursal = isset($config["id_sucursal"]) ? $config["id_sucursal"] : 0;
    $fecha_desde = isset($config["fecha_desde"]) ? $config["fecha_desde"] : 0;
    $order_by = isset($config["order_by"]) ? $config["order_by"] : 0;
    $order_direction = isset($config["order"]) ? $config["order"] : 0;
    $estado = isset($config["estado"]) ? $config["estado"] : (($_SESSION["estado"]==1)?1:0);

    $ff = new DateTime($fecha_desde);
    $ff->add(new DateInterval('P1D'));
    $fecha_desde = $ff->format("Y-m-d");

    $salida = array();
    $sql = "SELECT id, codigo, nombre, dias_pago FROM proveedores WHERE id_empresa = $id_empresa ";
    if ($tipo_proveedor != 0) $sql.= "AND tipo_proveedor = $tipo_proveedor ";
    if ($order_by != "saldo" && $order_by != "ultima_compra" && $order_by != "ultimo_pago") $sql.= "ORDER BY ".$order_by." ".$order_direction;

    $q = $this->db->query($sql);
    foreach($q->result() as $r) {

      // El saldo anterior lo tomamos en 30, 60, 90 y +90
      $fecha_90 = new DateTime($fecha_desde);
      $fecha_90->sub(new DateInterval("P90D"));
      $fecha_60 = new DateTime($fecha_desde);
      $fecha_60->sub(new DateInterval("P60D"));
      $fecha_30 = new DateTime($fecha_desde);
      $fecha_30->sub(new DateInterval("P30D"));
      
      $r->saldo_mas_90 = $this->saldo_pendiente($r->id,array(
        "fecha_hasta"=>$fecha_90->format("Y-m-d"),
        "estado"=>$estado,
        "id_sucursal"=>$id_sucursal,
      ));

      $r->saldo_90 = $this->saldo_pendiente($r->id,array(
        "fecha_desde"=>$fecha_90->format("Y-m-d"),
        "fecha_hasta"=>$fecha_60->format("Y-m-d"),
        "estado"=>$estado,
        "id_sucursal"=>$id_sucursal,
      ));

      $r->saldo_60 = $this->saldo_pendiente($r->id,array(
        "fecha_desde"=>$fecha_60->format("Y-m-d"),
        "fecha_hasta"=>$fecha_30->format("Y-m-d"),
        "estado"=>$estado,
        "id_sucursal"=>$id_sucursal,
      ));

      $r->saldo_30 = $this->saldo_pendiente($r->id,array(
        "fecha_desde"=>$fecha_30->format("Y-m-d"),
        "fecha_hasta"=>$fecha_desde,
        "estado"=>$estado,
        "id_sucursal"=>$id_sucursal,
      ));

      // Tomamos el saldo hasta el dia anterior
      $r->saldo = $this->saldo($r->id,array(
        "fecha"=>$fecha_desde,
        "estado"=>$estado,
        "id_sucursal"=>$id_sucursal,
        "incluir_dia"=>0,
      ));

      if ($r->saldo != ($r->saldo_mas_90 + $r->saldo_90 + $r->saldo_60 + $r->saldo_30)) {
        $r->saldo_mas_90 = 0;
        $r->saldo_90 = 0;
        $r->saldo_60 = 0;
        $r->saldo_30 = 0;
      }

      $r->total_pagos = 0;
      $r->total_compras = 0;

      // Ultimo pago
      $sql = "SELECT C.fecha AS ultimo_pago, C.total_general AS monto_ultimo_pago ";
      $sql.= "FROM compras C ";
      $sql.= "WHERE C.id_proveedor = $r->id ";
      //$sql.= "AND C.fecha <= '$fecha_hasta' "; // Que sea menor a la fecha que estamos buscando
      //$sql.= "AND '$fecha_desde' <= C.fecha ";
      $sql.= "AND C.id_tipo_comprobante = -1 ";
      $sql.= "AND C.id_empresa = $id_empresa "; // Que pertenezca a la empresa
      if ($id_sucursal != 0) $sql.= "AND C.id_sucursal = $id_sucursal ";
      if ($estado == 0) $sql.= "AND C.estado = $estado ";
      if ($id_empresa == 868) $sql.= "AND C.id_proveedor != 2112 ";
      $sql.= "AND C.compra_real = 1 ";
      $sql.= "ORDER BY C.fecha DESC ";
      $sql.= "LIMIT 0,1 ";

      $qq = $this->db->query($sql);
      if ($qq->num_rows()>0) {
        $rr = $qq->row();
        $r->ultimo_pago = fecha_es($rr->ultimo_pago);
        $r->ultimo_pago_mysql = $rr->ultimo_pago;
        $r->monto_ultimo_pago = $rr->monto_ultimo_pago;
      } else {
        $r->ultimo_pago = "";
        $r->ultimo_pago_mysql = "0000-00-00";
        $r->monto_ultimo_pago = 0;
      }

      // Ultima compra
      $sql = "SELECT C.fecha AS ultima_compra, C.total_general AS monto_ultima_compra ";
      $sql.= "FROM compras C ";
      $sql.= "WHERE C.id_proveedor = $r->id ";
      //$sql.= "AND C.fecha <= '$fecha_hasta' "; // Que sea menor a la fecha que estamos buscando
      //$sql.= "AND '$fecha_desde' <= C.fecha ";
      $sql.= "AND C.id_tipo_comprobante != -1 ";
      $sql.= "AND C.id_empresa = $id_empresa "; // Que pertenezca a la empresa
      if ($id_sucursal != 0) $sql.= "AND C.id_sucursal = $id_sucursal ";
      if ($estado == 0) $sql.= "AND C.estado = $estado ";
      if ($id_empresa == 868) $sql.= "AND C.id_proveedor != 2112 ";
      $sql.= "AND C.compra_real = 1 ";
      $sql.= "ORDER BY C.fecha DESC ";
      $sql.= "LIMIT 0,1 ";

      $qq = $this->db->query($sql);
      if ($qq->num_rows()>0) {
        $rr = $qq->row();
        $r->ultima_compra = fecha_es($rr->ultima_compra);
        $r->ultima_compra_mysql = $r->ultima_compra;
        $r->monto_ultima_compra = $rr->monto_ultima_compra;        
      } else {
        $r->ultima_compra = "";
        $r->ultima_compra_mysql = "0000-00-00";
        $r->monto_ultima_compra = 0;
      }

      if ($filtrar_en_cero == 1) {
        if (abs($r->saldo) > 50 || $r->total_pagos != 0 || $r->total_compras != 0) {
          $salida[] = $r;  
        }
      } else {
        $salida[] = $r;
      }

    }

    if ($order_by == "saldo") {
      if ($order_direction == "asc") usort($salida,array("Proveedor_Model", "ordenar_saldos"));
      else usort($salida,array("Proveedor_Model", "ordenar_saldos_desc"));
    } else if ($order_by == "ultima_compra") {
      if ($order_direction == "asc") usort($salida,array("Proveedor_Model", "ordenar_ultima_compra"));
      else usort($salida,array("Proveedor_Model", "ordenar_ultima_compra_desc"));      
    } else if ($order_by == "ultimo_pago") {
      if ($order_direction == "asc") usort($salida,array("Proveedor_Model", "ordenar_ultimo_pago"));
      else usort($salida,array("Proveedor_Model", "ordenar_ultimo_pago_desc"));      
    }

    return $salida;
  }

  // Funciones utilizadas para ordenar por saldos
  static function ordenar_saldos($a,$b) {
    return ($a->saldo > $b->saldo) ? 1 : -1;
  }
  static function ordenar_saldos_desc($a,$b) {
    return ($a->saldo > $b->saldo) ? -1 : 1;
  }
  static function ordenar_ultima_compra($a,$b) {
    return ($a->ultima_compra_mysql > $b->ultima_compra_mysql) ? 1 : -1;
  }
  static function ordenar_ultima_compra_desc($a,$b) {
    return ($a->ultima_compra_mysql > $b->ultima_compra_mysql) ? -1 : 1;
  }
  static function ordenar_ultimo_pago($a,$b) {
    return ($a->ultimo_pago_mysql > $b->ultimo_pago_mysql) ? 1 : -1;
  }
  static function ordenar_ultimo_pago_desc($a,$b) {
    return ($a->ultimo_pago_mysql > $b->ultimo_pago_mysql) ? -1 : 1;
  }  

	function get_by_codigo($codigo) {
		$id_empresa = parent::get_empresa();
		$sql = "SELECT C.*, ";
		$sql.= "	IF (TI.nombre IS NULL,'',TI.nombre) AS tipo_iva, ";
		$sql.= "	IF (L.nombre IS NULL,'',L.nombre) AS localidad ";
		$sql.= "FROM proveedores C ";
		$sql.= " LEFT JOIN tipos_iva TI ON (C.id_tipo_iva = TI.id) ";
		$sql.= " LEFT JOIN com_localidades L ON (C.id_localidad = L.id) ";
		$sql.= "WHERE C.codigo = '$codigo' AND C.id_empresa = $id_empresa ";
		$query = $this->db->query($sql);
		$row = $query->row(); 
		$this->db->close();
		return $row;
	}

  function save($data) {
    $relacionados = $data->relacionados;
    $cuentas_bancarias = $data->cuentas_bancarias;
    unset($data->relacionados);
    unset($data->cuentas_bancarias);
    $id = parent::save($data);

    // Actualizamos los puntos de venta relacionados
    $orden = 0;
    $this->db->query("DELETE FROM proveedores_relacionados WHERE id_proveedor = $id AND id_empresa = $data->id_empresa ");
    foreach($relacionados as $pv) {
      $sql = "INSERT INTO proveedores_relacionados (id_empresa,id_proveedor,id_relacionado,orden) VALUES ($data->id_empresa,$id,$pv,$orden)";
      $this->db->query($sql);
      $orden++;
    }

    // Actualizamos los puntos de venta relacionados
    $this->db->query("DELETE FROM proveedores_cuentas_bancarias WHERE id_proveedor = $id AND id_empresa = $data->id_empresa ");
    foreach($cuentas_bancarias as $pv) {
      $sql = "INSERT INTO proveedores_cuentas_bancarias (id_empresa,id_proveedor,id_banco,banco,cuenta,cbu) VALUES ('$data->id_empresa','$id','$pv->id_banco','$pv->banco','$pv->cuenta','$pv->cbu')";
      $this->db->query($sql);
    }
    return $id;
  }

	function saldo($id_proveedor,$config = array()) {

		@session_start();
		$fecha = isset($config["fecha"]) ? $config["fecha"] : date("Y-m-d");
    $fecha_desde = isset($config["fecha_desde"]) ? $config["fecha_desde"] : "";
		$id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $id_sucursal = isset($config["id_sucursal"]) ? $config["id_sucursal"] : 0;
		$estado = isset($config["estado"]) ? $config["estado"] : (($_SESSION["estado"]==1)?1:0);
		$incluir_dia = isset($config["incluir_dia"]) ? $config["incluir_dia"] : 0;

    // Obtenemos los datos del proveedor
    $proveedor = $this->get($id_proveedor);
    if (sizeof($proveedor->relacionados)>0) {
      $ids = array($proveedor->id);
      foreach($proveedor->relacionados as $rel) {
        $ids[] = $rel->id;
      }
      $id_proveedor = implode(",",$ids);
    }
	
		$saldo_inicial = 0;
		$sql = "SELECT saldo_inicial FROM proveedores WHERE id IN($id_proveedor) AND id_empresa = $id_empresa ";
		$q = $this->db->query($sql);
		if ($q->num_rows()>0) {
			$pr = $q->row();
      $saldo_inicial = $pr->saldo_inicial;
			//if ($estado == 1) $saldo_inicial = $proveedor->saldo_inicial_2;
			//else $saldo_inicial = $proveedor->saldo_inicial;
		}

    $menor = ($incluir_dia == 1) ? "<=" : "<";
    if (empty($fecha_desde)) {
      $sql = "SELECT SUM(IF(C.id_tipo_comprobante = -1,C.total_general,C.total_general - C.pago)) AS saldo ";
      $sql.= "FROM compras C ";
      $sql.= "WHERE C.id_proveedor IN ($id_proveedor) ";
      if (!empty($fecha)) {
        $sql.= "AND C.fecha $menor '$fecha' "; // Que sea menor a la fecha que estamos buscando
      }
    } else {
      $sql = "SELECT SUM(IF(C.id_tipo_comprobante = -1,C.total_general,C.total_general - C.pago)) AS saldo ";
      $sql.= "FROM compras C ";
      $sql.= "WHERE C.id_proveedor IN ($id_proveedor) ";
      if (!empty($fecha)) {
        $sql.= "AND C.fecha $menor '$fecha' "; // Que sea menor a la fecha que estamos buscando
        $sql.= "AND '$fecha_desde' $menor C.fecha ";
        $sql.= "AND C.id_tipo_comprobante != -1 ";
      }
    }
    $sql.= "AND C.id_empresa = $id_empresa "; // Que pertenezca a la empresa
    if ($id_sucursal != 0) $sql.= "AND C.id_sucursal = $id_sucursal ";
    if ($estado == 0) $sql.= "AND C.estado = $estado ";
    if ($id_empresa == 868) $sql.= "AND C.id_proveedor != 2112 ";
    // IMPORTANTE
    $sql.= "AND (C.compra_real = 1 OR C.ver_en_cuenta = 1) ";
		
		$query = $this->db->query($sql);
		$row = $query->row();
		return (is_null($row->saldo) ? $saldo_inicial : ($saldo_inicial + $row->saldo));
	}


  function saldo_pendiente($id_proveedor,$config = array()) {

    @session_start();
    $fecha_desde = isset($config["fecha_desde"]) ? $config["fecha_desde"] : "";
    $fecha_hasta = isset($config["fecha_hasta"]) ? $config["fecha_hasta"] : date("Y-m-d");
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $id_sucursal = isset($config["id_sucursal"]) ? $config["id_sucursal"] : 0;
    $estado = isset($config["estado"]) ? $config["estado"] : (($_SESSION["estado"]==1)?1:0);

    // Obtenemos los datos del proveedor
    $proveedor = $this->get($id_proveedor);
    if (sizeof($proveedor->relacionados)>0) {
      $ids = array($proveedor->id);
      foreach($proveedor->relacionados as $rel) {
        $ids[] = $rel->id;
      }
      $id_proveedor = implode(",",$ids);
    }

    $saldo_inicial = 0;
    if (empty($fecha_desde)) {
      $sql = "SELECT saldo_inicial FROM proveedores WHERE id IN($id_proveedor) AND id_empresa = $id_empresa ";
      $q = $this->db->query($sql);
      if ($q->num_rows()>0) {
        $pr = $q->row();
        $saldo_inicial = (float)$pr->saldo_inicial;
        //if ($estado == 1) $saldo_inicial = $proveedor->saldo_inicial_2;
        //else $saldo_inicial = $proveedor->saldo_inicial;
      }
    }

    $sql = "SELECT ";
    $sql.= " SUM(C.total_general - C.cancelado) AS saldo ";
    $sql.= "FROM compras C ";
    $sql.= "WHERE id_empresa = $id_empresa ";
    $sql.= "AND C.id_proveedor IN ($id_proveedor) ";
    $sql.= "AND C.id_tipo_comprobante != -1 ";
    if (!empty($fecha_desde)) $sql.= "AND C.fecha >= '$fecha_desde' ";
    if (!empty($fecha_hasta)) $sql.= "AND C.fecha < '$fecha_hasta' ";
    if ($id_sucursal != 0) $sql.= "AND C.id_sucursal = $id_sucursal ";
    if ($estado == 0) $sql.= "AND C.estado = $estado ";
    if ($id_empresa == 868) $sql.= "AND C.id_proveedor != 2112 ";
    // IMPORTANTE
    $sql.= "AND C.compra_real = 1 ";
    $query = $this->db->query($sql);
    $row = $query->row();
    return (is_null($row->saldo) ? 0 : (0 + $row->saldo)) + $saldo_inicial;
  }
	
	function get($id,$config = array()) {
		$id_empresa = (isset($config["id_empresa"])) ? $config["id_empresa"] : parent::get_empresa();
		$id = (int)$id;
		$sql = "SELECT P.*, ";
		$sql.= "IF(L.nombre IS NULL,'',L.nombre) AS localidad ";
		$sql.= "FROM proveedores P ";
		$sql.= "LEFT JOIN com_localidades L ON (P.id_localidad = L.id) ";
		$sql.= "WHERE P.id = $id ";
		$sql.= "AND P.id_empresa = $id_empresa ";
		$q = $this->db->query($sql);
		if ($q->num_rows() == 0) return array();
		$row = $q->row();

    if ($row !== FALSE) {
      $sql = "SELECT id_relacionado AS id FROM proveedores_relacionados WHERE id_proveedor = $row->id AND id_empresa = $row->id_empresa ";
      $query = $this->db->query($sql);
      $row->relacionados = $query->result();

      $sql = "SELECT * FROM proveedores_cuentas_bancarias WHERE id_proveedor = $row->id AND id_empresa = $row->id_empresa ";
      $query = $this->db->query($sql);
      $row->cuentas_bancarias = $query->result();
    }

		return $row;
	}
	

	function buscar($conf = array()) {

		$id_empresa = (isset($conf["id_empresa"])) ? $conf["id_empresa"] : parent::get_empresa();
    $filter = (isset($conf["filter"])) ? $conf["filter"] : "";
    $limit = (isset($conf["limit"])) ? $conf["limit"] : 0;
    $offset = (isset($conf["offset"])) ? $conf["offset"] : 0;
    $tipo_proveedor = (isset($conf["tipo_proveedor"])) ? $conf["tipo_proveedor"] : 0;
    $order = (isset($conf["order"]) && !empty($conf["order"])) ? $conf["order"] : "nombre ASC ";

    $sql = "SELECT SQL_CALC_FOUND_ROWS * ";
    $sql.= "FROM proveedores ";
    $sql.= "WHERE id_empresa = $id_empresa ";
    if (!empty($filter)) $sql.= "AND (nombre LIKE '%$filter%' OR codigo LIKE '%$filter%' OR cuit LIKE '%$filter%') ";
    if (!empty($tipo_proveedor)) $sql.= "AND tipo_proveedor = $tipo_proveedor ";
    if (!empty($order)) $sql.= "ORDER BY $order ";
    if ($offset != 0) $sql.= "LIMIT $limit, $offset ";    
    $q = $this->db->query($sql);

    $q_total = $this->db->query("SELECT FOUND_ROWS() AS total");
    $total = $q_total->row();
    return array(
      "total"=>$total->total,
      "results"=>$q->result(),
    );
	}

}