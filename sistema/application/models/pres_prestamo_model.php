<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Pres_Prestamo_Model extends Abstract_Model {

	function __construct() {
		parent::__construct("pres_prestamos","id","fecha DESC");
	}

  function delete($id) {
    $id_empresa = parent::get_empresa();
    //$this->db->query("DELETE FROM pres_prestamos WHERE id = $id AND id_empresa = $id_empresa");
  }
	
	function get($id,$config = array()) {
		
    @session_start();
    if (empty($id)) return FALSE;
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $fecha = isset($config["fecha"]) ? $config["fecha"] : "";

    $this->load->helper("fecha_helper");
    $this->load->model("Pres_Prestamo_Cuota_Model");

    $sql = "SELECT P.*, PC.nombre AS plan, ";
    $sql.= " IF(P.fecha = '0000-00-00','',DATE_FORMAT(P.fecha,'%d/%m/%Y')) AS fecha ";
    $sql.= "FROM pres_prestamos P ";
    $sql.= "INNER JOIN pres_planes_credito PC ON (P.id_plan = PC.id AND P.id_empresa = PC.id_empresa) ";
    $sql.= "WHERE P.id_empresa = $id_empresa ";
    $sql.= "AND P.id = $id ";
		$query = $this->db->query($sql);
		$row = $query->row(); 
    if ($row !== FALSE) {
      $this->load->model("Pres_Prestamo_Cuota_Model");
      $row->cuotas = $this->Pres_Prestamo_Cuota_Model->buscar(array(
        "id_prestamo"=>$id,
        "id_empresa"=>$id_empresa,
      ));

      // Contamos la cantidad de cuotas pagas
      $sql = "SELECT IF(COUNT(*) IS NULL,0,COUNT(*)) AS cantidad_cuotas_pagas ";
      $sql.= "FROM pres_prestamos_cuotas ";
      $sql.= "WHERE estado = 1 AND id_prestamo = $id ";
      $sql.= "AND id_empresa = $id_empresa ";
      if (!empty($fecha)) $sql.= "AND fecha_vencimiento < '$fecha' ";
      $qq = $this->db->query($sql);
      $rr = $qq->row();
      $row->cantidad_cuotas_pagas = $rr->cantidad_cuotas_pagas;

      // Fecha de ultimo pago
      $sql = "SELECT MAX(fecha_pago) AS fecha_ultimo_pago ";
      $sql.= "FROM pres_prestamos_cuotas ";
      $sql.= "WHERE estado IN (1,2) AND id_prestamo = $id ";
      if (!empty($fecha)) $sql.= "AND fecha_vencimiento < '$fecha' ";
      $sql.= "AND id_empresa = $id_empresa ";
      $qq = $this->db->query($sql);
      $rr = $qq->row();
      $row->fecha_ultimo_pago = is_null($rr->fecha_ultimo_pago) ? '' : fecha_es($rr->fecha_ultimo_pago);

      $sql = "SELECT monto_pagado ";
      $sql.= "FROM pres_prestamos_cuotas ";
      $sql.= "WHERE estado IN (1,2) AND id_prestamo = $id ";
      $sql.= "AND id_empresa = $id_empresa ";
      if (!empty($fecha)) $sql.= "AND fecha_vencimiento < '$fecha' ";
      $sql.= "ORDER BY fecha_pago DESC ";
      $qq = $this->db->query($sql);
      $row->ultimo_pago = 0;
      if ($qq->num_rows() > 0) {
        $rr = $qq->row();
        $row->ultimo_pago = $rr->monto_pagado;
      }

      // Calculamos el proximo vencimiento
      $sql = "SELECT IF(MIN(fecha_vencimiento) IS NULL,'',MIN(fecha_vencimiento)) AS proximo_vencimiento ";
      $sql.= "FROM pres_prestamos_cuotas ";
      $sql.= "WHERE (estado = 0 OR estado = 2) AND id_prestamo = $id ";
      $sql.= "AND id_empresa = $id_empresa ";
      if (!empty($fecha)) $sql.= "AND fecha_vencimiento < '$fecha' ";
      $qq = $this->db->query($sql);
      $rr = $qq->row();
      $row->proximo_vencimiento = empty($rr->proximo_vencimiento) ? "" : fecha_es($rr->proximo_vencimiento);

      // Deuda vencida
      $sql = "SELECT IF(SUM(saldo) IS NULL,0,SUM(saldo)) AS deuda ";
      $sql.= "FROM pres_prestamos_cuotas ";
      $sql.= "WHERE estado != 1 AND id_prestamo = $id ";
      $sql.= "AND id_empresa = $id_empresa ";
      if (!empty($fecha)) $sql.= "AND fecha_vencimiento < '$fecha' ";
      $qq = $this->db->query($sql);
      $rr = $qq->row();
      $row->deuda_vencida = $rr->deuda;

      $row->habilitado_renovacion = $this->esta_habilitado_para_renovacion($row);

      // Si existe la sesion se la adjuntamos
      if (isset($_SESSION["id_cliente_cobrado"]) && $_SESSION["id_cliente_cobrado"] == $row->id_cliente) {
        $row->sesion_id_cliente_prestamo = $_SESSION["id_cliente_cobrado"];
        $row->sesion_cliente_prestamo = $_SESSION["cliente_cobrado"];
        $row->sesion_total_cobrado = $_SESSION["total_cobrado"];
      } else {
        $row->sesion_id_cliente_prestamo = 0;
        $row->sesion_cliente_prestamo = "";
        $row->sesion_total_cobrado = 0;
      }

    }
		return $row;
	}

  function insert($data) {

    $this->load->model("Tipo_Gasto_Model");
    $id_usuario = $_SESSION["id"];
    $this->load->helper("fecha_helper");
    $data->fecha = fecha_mysql($data->fecha);

    // Si estamos renovando un prestamo
    $id_prestamo_renovado = 0;
    $saldo_renovacion = 0;
    if (isset($data->id_prestamo_renovado)) {
      $id_prestamo_renovado = $data->id_prestamo_renovado;
      unset($data->id_prestamo_renovado);
      $saldo_renovacion = $data->saldo_renovacion;
      unset($data->saldo_renovacion);
    }

    $cuotas = $data->cuotas;
    unset($data->cuotas);

    // Controlamos que haya dinero en la caja
    if ($data->id_sucursal != 0 && $data->fecha == date("Y-m-d")) {
      $maniana = date("Y-m-d",strtotime($data->fecha." +1 day"));
      $this->load->model("Pres_Caja_Diaria_Model");  
      $saldo = $this->Pres_Caja_Diaria_Model->calcular_saldo(array(
        "id_sucursal"=>$data->id_sucursal,
        "id_empresa"=>$data->id_empresa,
        "hasta"=>$maniana,
      ));
      if ($data->monto_prestado > $saldo) {
        $mensaje = "No hay suficiente dinero en caja para realizar el prestamo.";
        header($_SERVER['SERVER_PROTOCOL'] . ' 500 '.$mensaje, true, 500);
        exit();    
      }
    }

    // Calculamos el proximo numero de la sucursal
    $sql = "SELECT IF(MAX(numero) IS NULL,0,MAX(numero)) as ultimo ";
    $sql.= "FROM pres_prestamos ";
    $sql.= "WHERE id_empresa = $data->id_empresa ";
    $sql.= "AND id_sucursal = $data->id_sucursal ";
    $q = $this->db->query($sql);
    if ($q->num_rows()>0) {
      $rr = $q->row();
      $data->numero = $rr->ultimo + 1;
    }

    $id_prestamo = parent::insert($data);

    $capital_cuota = (float)($data->monto_prestado / $data->cantidad_cuotas);

    foreach($cuotas as $cuota) {
      $cuota->fecha_vencimiento = fecha_mysql($cuota->fecha_vencimiento);
      $cuota->monto = (float) $cuota->monto;
      $this->db->insert("pres_prestamos_cuotas",array(
        "id_empresa"=>$data->id_empresa,
        "id_sucursal"=>$data->id_sucursal,
        "id_prestamo"=>$id_prestamo,
        "numero"=>$cuota->numero,
        "fecha_vencimiento"=>$cuota->fecha_vencimiento,
        "estado"=>0,
        "monto"=>$cuota->monto,
        "saldo"=>$cuota->monto,
        "saldo_capital"=>$cuota->monto,
        "saldo_interes"=>0,
        "interes"=>0,
        "total"=>$cuota->monto,
        "capital_cuota"=>$capital_cuota,
        "interes_cuota"=>$cuota->monto - $capital_cuota,
      ));
    }

    // Renovamos de un prestamo anterior
    if ($id_prestamo_renovado > 0) {
      // Tenemos que marcar las cuotas como pagas 
      $pres_renovado = $this->get($id_prestamo_renovado);

      // Y agregar el pago en la caja
      $concepto = $this->Tipo_Gasto_Model->get_by_codigo("PAGO");
      if ($concepto !== FALSE) {
        $observaciones = "Renovacion Prestamo #".$pres_renovado->numero;
        foreach($pres_renovado->cuotas as $cuota) {
          if ($cuota->saldo == 0) continue;
          // Insertamos el movimiento en la caja
          $sql = "INSERT INTO pres_cajas_movimientos (id_empresa,id_concepto,monto,fecha,observaciones,id_prestamo,id_cuota,id_sucursal,id_usuario,tipo,cancelacion_capital,cancelacion_interes) VALUES (";
          $sql.= "$data->id_empresa,$concepto->id,'$cuota->saldo','$data->fecha','$observaciones','$cuota->id_prestamo','$cuota->id','$cuota->id_sucursal',$id_usuario,'E','$cuota->saldo_capital','$cuota->saldo_interes') ";
          $this->db->query($sql);
          // Actualizamos el saldo de la cuota
          $sql = "UPDATE pres_prestamos_cuotas SET ";
          $sql.= " saldo = 0, saldo_interes = 0, saldo_capital = 0, ";
          $sql.= " monto_pagado = monto, interes_pagado = interes, estado = 1, fecha_pago = '$data->fecha' ";
          $sql.= "WHERE id_empresa = $cuota->id_empresa ";
          $sql.= "AND id_prestamo = $cuota->id_prestamo ";
          $sql.= "AND id_sucursal = $cuota->id_sucursal ";
          $sql.= "AND id = $cuota->id ";
          $this->db->query($sql);
        }
      }
    }

    // Lo sacamos de la caja
    $concepto = $this->Tipo_Gasto_Model->get_by_codigo("OTORGACION");
    if ($concepto !== FALSE) {
      $sql = "INSERT INTO pres_cajas_movimientos (id_empresa,id_concepto,monto,fecha,observaciones,id_prestamo,id_cuota,id_sucursal,id_usuario,tipo) VALUES (";
      $sql.= "$data->id_empresa,$concepto->id,'$data->monto_prestado','$data->fecha $data->hora','Otorgacion Prestamo #$data->numero','$id_prestamo',0,'$data->id_sucursal',$id_usuario,'S') ";
      $this->db->query($sql);          
    }

    // Marcamos la tarea como realizada
    $this->load->model("Pres_Prestamo_Cuota_Model");
    $this->Pres_Prestamo_Cuota_Model->marcar_tarea(array(
      "id_cliente"=>$data->id_cliente
    ));

    return $id_prestamo;
  }

  function listado_reingreso($config = array()) {

    set_time_limit(0);

    $this->load->model("Pres_Cliente_Model");
    $this->load->helper("fecha_helper");
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $fecha = isset($config["fecha"]) ? $config["fecha"] : date("Y-m-d");
    $id_sucursal = isset($config["id_sucursal"]) ? $config["id_sucursal"] : 0;
    $limit = isset($config["limit"]) ? $config["limit"] : 0;
    $offset = isset($config["offset"]) ? $config["offset"] : 10;
    $order = isset($config["order"]) ? $config["order"] : "";
    $order_by = isset($config["order_by"]) ? $config["order_by"] : "";

    $sql = "SELECT * FROM pres_clientes CLI ";
    $sql.= "WHERE CLI.id_empresa = $id_empresa ";
    if (!empty($id_sucursal)) $sql.= "AND CLI.id_sucursal = $id_sucursal ";
    $q = $this->db->query($sql);
    $salida = array();
    foreach($q->result() as $row) {

      // Obtenemos todos los prestamos de ese cliente
      $sql = "SELECT * FROM pres_prestamos P ";
      $sql.= "WHERE P.id_empresa = $id_empresa ";
      $sql.= "AND P.id_cliente = $row->id ";
      $qq = $this->db->query($sql);

      $cancelados = -1;
      foreach($qq->result() as $pres) {
        if ($pres->cantidad_cuotas_pagas < $pres->cantidad_cuotas) {
          $cancelados = 0; break;
        }
      }
      if ($cancelados == -1 && $qq->num_rows() > 0) {
        $salida[] = $row;
      }
    }
    return array(
      "results"=>$salida,
      "total"=>sizeof($salida),
    );
  }

  function listado_mora($config = array()) {

    $this->load->model("Pres_Cliente_Model");
    $this->load->helper("fecha_helper");
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $fecha = isset($config["fecha"]) ? $config["fecha"] : date("Y-m-d");
    $id_sucursal = isset($config["id_sucursal"]) ? $config["id_sucursal"] : 0;
    $id_plan = isset($config["id_plan"]) ? $config["id_plan"] : 0;
    $limit = isset($config["limit"]) ? $config["limit"] : 0;
    $offset = isset($config["offset"]) ? $config["offset"] : 10;
    $order = isset($config["order"]) ? $config["order"] : "";
    $order_by = isset($config["order_by"]) ? $config["order_by"] : "dias_mora";

    $sql = "SELECT SQL_CALC_FOUND_ROWS P.numero, P.cantidad_cuotas, P.cantidad_cuotas_pagas, P.dias_mora, P.deuda_vencida, ";
    $sql.= " P.valor_cuota, P.fecha_ultimo_pago, P.ultimo_pago, P.proximo_vencimiento,  ";
    $sql.= " CLI.nombre, CLI.apellido, CLI.id, CLI.telefono, CLI.path, ";
    $sql.= " IF (L.nombre IS NULL,'',L.nombre) AS localidad, PC.nombre AS plan ";
    $sql.= "FROM pres_prestamos P ";
    $sql.= " INNER JOIN pres_clientes CLI ON (P.id_cliente = CLI.id AND P.id_empresa = CLI.id_empresa) ";
    $sql.= " INNER JOIN pres_planes_credito PC ON (P.id_plan = PC.id AND P.id_empresa = PC.id_empresa) ";
    $sql.= " LEFT JOIN com_localidades L ON (CLI.id_localidad = L.id) ";
    $sql.= "WHERE PC.id_empresa = $id_empresa ";
    $sql.= "AND P.dias_mora > 0 ";
    if (!empty($id_sucursal)) $sql.= "AND CLI.id_sucursal = $id_sucursal ";
    if (!empty($id_plan)) $sql.= "AND P.id_plan = $id_plan ";
    $sql.= "ORDER BY $order_by $order ";
    $sql.= "LIMIT $limit, $offset ";
    $q = $this->db->query($sql);
    $salida = $q->result();

    $q_total = $this->db->query("SELECT FOUND_ROWS() AS total");
    $total = $q_total->row();

    return array(
      "results"=>$salida,
      "total"=>$total->total,
    );
  }

  function buscar($config = array()) {

    $this->load->helper("fecha_helper");
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $id_cliente = isset($config["id_cliente"]) ? $config["id_cliente"] : 0;
    $estado = isset($config["estado"]) ? $config["estado"] : 0; // 1 = VIGENTES. 2 = CANCELADOS
    $fecha = isset($config["fecha"]) ? $config["fecha"] : "";
    $limit = isset($config["limit"]) ? $config["limit"] : 0;
    $id_plan = isset($config["id_plan"]) ? $config["id_plan"] : 0;
    $offset = isset($config["offset"]) ? $config["offset"] : 10;

    $sql = "SELECT SQL_CALC_FOUND_ROWS P.*, PC.nombre AS plan, ";
    $sql.= " IF(P.fecha = '0000-00-00','',DATE_FORMAT(P.fecha,'%d/%m/%Y')) AS fecha ";
    $sql.= "FROM pres_prestamos P ";
    $sql.= "INNER JOIN pres_planes_credito PC ON (P.id_plan = PC.id AND P.id_empresa = PC.id_empresa) ";
    $sql.= "WHERE P.id_empresa = $id_empresa ";
    if (!empty($id_cliente)) $sql.= "AND P.id_cliente = $id_cliente ";
    if (!empty($id_plan)) $sql.= "AND P.id_plan = $id_plan ";
    $sql.= "LIMIT $limit,$offset ";
    $q = $this->db->query($sql);

    $q_total = $this->db->query("SELECT FOUND_ROWS() AS total");
    $total = $q_total->row();

    $salida = array();
    foreach($q->result() as $row) {

      // Contamos la cantidad de cuotas pagas
      $sql = "SELECT IF(COUNT(*) IS NULL,0,COUNT(*)) AS cantidad_cuotas_pagas ";
      $sql.= "FROM pres_prestamos_cuotas ";
      $sql.= "WHERE estado = 1 AND id_prestamo = $row->id ";
      $sql.= "AND id_empresa = $id_empresa ";
      $qq = $this->db->query($sql);
      $rr = $qq->row();
      $row->cantidad_cuotas_pagas = $rr->cantidad_cuotas_pagas;

      // Calculamos el proximo vencimiento
      $sql = "SELECT MIN(fecha_vencimiento) AS proximo_vencimiento ";
      $sql.= "FROM pres_prestamos_cuotas ";
      $sql.= "WHERE (estado = 0 OR estado = 2) AND id_prestamo = $row->id ";
      $sql.= "AND id_empresa = $id_empresa ";
      $qq = $this->db->query($sql);
      $rr = $qq->row();
      $row->proximo_vencimiento = fecha_es($rr->proximo_vencimiento);

      // Deuda vencida
      $sql = "SELECT IF(SUM(saldo) IS NULL,0,SUM(saldo)) AS deuda ";
      $sql.= "FROM pres_prestamos_cuotas ";
      $sql.= "WHERE estado != 1 AND id_prestamo = $row->id ";
      $sql.= "AND id_empresa = $id_empresa ";
      if (!empty($fecha)) $sql.= "AND fecha_vencimiento < '$fecha' ";
      $qq = $this->db->query($sql);
      $rr = $qq->row();
      $row->deuda_vencida = $rr->deuda;

      $row->habilitado_renovacion = $this->esta_habilitado_para_renovacion($row);

      if ($estado == 0) {
        $salida[] = $row;
      } else if ($estado == 1 && $row->cantidad_cuotas_pagas < $row->cantidad_cuotas) {
        $salida[] = $row;
      } else if ($estado == 2 && $row->cantidad_cuotas == $row->cantidad_cuotas_pagas) {
        $salida[] = $row;
      }
    }
    return array(
      "results"=>$salida,
      "total"=>$total->total,
    );
  }

  function esta_habilitado_para_renovacion($pres) {
    if (!isset($pres->cantidad_cuotas) || !isset($pres->cantidad_cuotas_pagas)) return 0;
    if ($pres->cantidad_cuotas == $pres->cantidad_cuotas_pagas) return 0;
    else if ($pres->cantidad_cuotas == 3 && $pres->cantidad_cuotas_pagas >= 2) return 1;
    else if ($pres->cantidad_cuotas == 4 && $pres->cantidad_cuotas_pagas >= 3) return 1;
    else if ($pres->cantidad_cuotas == 5 && $pres->cantidad_cuotas_pagas >= 4) return 1;
    else if ($pres->cantidad_cuotas == 6 && $pres->cantidad_cuotas_pagas >= 4) return 1;
    else if ($pres->cantidad_cuotas == 7 && $pres->cantidad_cuotas_pagas >= 5) return 1;
    else if ($pres->cantidad_cuotas == 8 && $pres->cantidad_cuotas_pagas >= 6) return 1;
    else if ($pres->cantidad_cuotas == 9 && $pres->cantidad_cuotas_pagas >= 7) return 1;
    else if ($pres->cantidad_cuotas == 10 && $pres->cantidad_cuotas_pagas >= 7) return 1;
    else if ($pres->cantidad_cuotas == 11 && $pres->cantidad_cuotas_pagas >= 8) return 1;
    return 0;
  }

  function buenos_clientes($config = array()) {

    $this->load->model("Pres_Cliente_Model");
    $this->load->helper("fecha_helper");
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $fecha = isset($config["fecha"]) ? $config["fecha"] : date("Y-m-d");
    $id_sucursal = isset($config["id_sucursal"]) ? $config["id_sucursal"] : 0;
    $limit = isset($config["limit"]) ? $config["limit"] : 0;
    $offset = isset($config["offset"]) ? $config["offset"] : 10;
    $filter = isset($config["filter"]) ? $config["filter"] : "";
    $order = isset($config["order"]) ? $config["order"] : "";
    $order_by = isset($config["order_by"]) ? $config["order_by"] : "";

    $sql = "SELECT SQL_CALC_FOUND_ROWS ";
    $sql.= " CLI.nombre, CLI.apellido, CLI.id, CLI.telefono, CLI.path, CLI.documento, ";
    $sql.= " IF (L.nombre IS NULL,'',L.nombre) AS localidad ";
    $sql.= " FROM pres_clientes CLI ";
    $sql.= " LEFT JOIN com_localidades L ON (CLI.id_localidad = L.id) ";
    $sql.= "WHERE CLI.id_empresa = $id_empresa ";
    if (!empty($id_sucursal)) $sql.= "AND CLI.id_sucursal = $id_sucursal ";
    if (!empty($filter)) $sql.= "AND ((CONCAT(CLI.apellido,' ',CLI.nombre) LIKE '%$filter%') OR (CONCAT(CLI.nombre,' ',CLI.apellido) LIKE '%$filter%') OR (CLI.documento LIKE '%$filter%')) ";
    $sql.= "AND 0 >= (";
    $sql.= " SELECT SUM(P.deuda_vencida) ";
    $sql.= " FROM pres_prestamos P ";
    $sql.= " WHERE P.id_empresa = $id_empresa ";
    $sql.= " AND P.id_cliente = CLI.id ";
    $sql.= ") ";
    $sql.= "AND 7 >= (";
    $sql.= " SELECT SUM(P.dias_mora) ";
    $sql.= " FROM pres_prestamos P ";
    $sql.= " WHERE P.id_empresa = $id_empresa ";
    $sql.= " AND P.id_cliente = CLI.id ";
    $sql.= ") ";
    $sql.= "LIMIT $limit,$offset ";

    /*
    $sql = "SELECT SQL_CALC_FOUND_ROWS ";
    $sql.= " CLI.nombre, CLI.apellido, CLI.id, CLI.telefono, CLI.path, CLI.documento, ";
    $sql.= " IF (L.nombre IS NULL,'',L.nombre) AS localidad ";
    $sql.= "FROM pres_prestamos P ";
    $sql.= " INNER JOIN pres_clientes CLI ON (P.id_cliente = CLI.id AND P.id_empresa = CLI.id_empresa) ";
    $sql.= " LEFT JOIN com_localidades L ON (CLI.id_localidad = L.id) ";
    $sql.= "WHERE P.id_empresa = $id_empresa ";
    if (!empty($filter)) $sql.= "AND ((CONCAT(CLI.apellido,' ',CLI.nombre) LIKE '%$filter%') OR (CONCAT(CLI.nombre,' ',CLI.apellido) LIKE '%$filter%') OR (CLI.documento LIKE '%$filter%')) ";
    // Si tiene el credito cancelado, o bien no tiene deuda
    if (!empty($id_sucursal)) $sql.= "AND P.id_sucursal = $id_sucursal ";
    $sql.= "GROUP BY P.id_cliente ";
    $sql.= "HAVING (SUM(P.deuda_vencida) <= 0 ";
    $sql.= "AND SUM(P.dias_mora) <= 7 ";
    $sql.= "AND SUM(P.cantidad_cuotas) >= SUM(P.cantidad_cuotas_pagas)) ";
    $sql.= "LIMIT $limit,$offset ";
    //$sql.= "ORDER BY $order_by $order";
    */
    $q = $this->db->query($sql);
    $salida = $q->result();

    $q_total = $this->db->query("SELECT FOUND_ROWS() AS total");
    $total = $q_total->row();

    return array(
      "results"=>$salida,
      "total"=>$total->total,
      "sql"=>$sql,
    );
  }


}