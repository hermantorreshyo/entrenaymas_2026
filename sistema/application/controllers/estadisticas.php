<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Estadisticas extends REST_Controller {

  function __construct() {  
    parent::__construct();
  }

  function ver_detalle_propiedad() {
    $id_empresa = parent::get_empresa();
    $this->load->helper("fecha_helper");
    $id_propiedad = parent::get_post("id_propiedad",0);
    $desde = fecha_mysql(parent::get_post("desde",date("d/m/Y")));
    $hasta = fecha_mysql(parent::get_post("hasta",date("d/m/Y")));
    $hasta = date('Y-m-d', strtotime($hasta. '+1 days'));

    $grafico = array();
    $fecha_desde = new DateTime($desde);
    $fecha_hasta = new DateTime($hasta);
    $interval = DateInterval::createFromDateString('1 day');
    $period = new DatePeriod($fecha_desde, $interval, $fecha_hasta);
    foreach($period as $dt) {
      $sql = "SELECT IF(COUNT(*) IS NULL,0,COUNT(*)) as cantidad ";
      $sql.= "FROM inm_propiedades_contactos ";
      $sql.= "WHERE id_empresa = $id_empresa AND id_propiedad = $id_propiedad ";
      $sql.= "AND DATE_FORMAT(fecha,'%Y-%m-%d') = '".$dt->format("Y-m-d")."' ";
      $q = $this->db->query($sql);
      $r = $q->row();
      $grafico[] = (float)$r->cantidad;
    }

    $sql = "SELECT PC.*, C.nombre, DATE_FORMAT(PC.fecha,'%d/%m/%Y %H:%i') AS fecha ";
    $sql.= "FROM inm_propiedades_contactos PC ";
    $sql.= "INNER JOIN clientes C ON (PC.id_contacto = C.id AND PC.id_empresa = C.id_empresa) ";
    $sql.= "WHERE PC.id_empresa = $id_empresa ";
    $sql.= "AND PC.id_propiedad = $id_propiedad ";
    $sql.= "AND PC.fecha >= '$desde' AND PC.fecha <= '$hasta' ";
    $sql.= "ORDER BY PC.fecha DESC ";
    $q = $this->db->query($sql);

    echo json_encode(array(
      "tabla"=>$q->result(),
      "grafico"=>$grafico,
    ));
  }

  function evolucion_stock() {
    $id_empresa = parent::get_empresa();
    $this->load->helper("fecha_helper");
    $this->load->model("Stock_Model");
    $id_articulo = parent::get_post("id_articulo",0);
    $id_sucursal = parent::get_post("id_sucursal",0);
    $desde = fecha_mysql(parent::get_post("desde",date("d/m/Y")));
    $hasta = fecha_mysql(parent::get_post("hasta",date("d/m/Y")));
    $hasta = date('Y-m-d', strtotime($hasta. '+1 days'));

    $salida = array();
    $fecha_desde = new DateTime($desde);
    $fecha_hasta = new DateTime($hasta);
    $interval = DateInterval::createFromDateString('1 day');
    $period = new DatePeriod($fecha_desde, $interval, $fecha_hasta);
    foreach($period as $dt) {
      $saldo = $this->Stock_Model->get_saldo(array(
        "id_empresa"=>$id_empresa,
        "id_sucursal"=>$id_sucursal,
        "id_articulo"=>$id_articulo,
        "fecha"=>$dt->format("Y-m-d"),
      ));
      $salida[] = (float)$saldo;
    }
    echo json_encode(array(
      "results"=>$salida,
    ));
  }

  function compras_ventas_por_articulos() {

    set_time_limit(0);

    $id_empresa = $this->get_empresa();
    $codigos_articulos = parent::get_post("codigos_articulos","");
    $filter = parent::get_post("filter","");
    $desde = parent::get_post("desde",date("d/m/Y"));
    $hasta = parent::get_post("hasta",date("d/m/Y"));
    $order = parent::get_post("order","asc");
    $order_by = parent::get_post("order_by","nombre");
    $id_sucursal = parent::get_post("id_sucursal",0);
    $this->load->helper("fecha_helper");
    $desde = fecha_mysql(str_replace("-","/",$desde));
    $hasta = fecha_mysql(str_replace("-","/",$hasta));
    $this->load->model("Venta_Model");

    $salida = array();

    // Obtenemos los articulos con esos codigos
    $codigos = explode("-", $codigos_articulos);
    $articulos = array();
    foreach($codigos as $cod) {
      $sql = "SELECT A.id AS id_articulo, A.codigo, A.codigo_barra, IF(AP.codigo IS NULL,'',AP.codigo) AS codigo_prov, A.nombre, IF(AP.id_proveedor IS NULL,0,AP.id_proveedor) AS id_proveedor ";
      $sql.= "FROM articulos A LEFT JOIN articulos_proveedores AP ON (A.id_empresa = AP.id_empresa AND A.id = AP.id_articulo) ";
      $sql.= "WHERE A.id_empresa = $id_empresa ";
      $sql.= "AND A.codigo = '$cod' ";
      $q = $this->db->query($sql);
      if ($q->num_rows() > 0) {
        $articulos[] = $q->row();
      }
    }

    // Obtenemos las sucursales
    $sql = "SELECT * FROM almacenes WHERE id_empresa = $id_empresa ";
    if ($id_empresa == 249) $sql.= "AND id != 25 "; // Sacamos CENTRAL
    $q_suc = $this->db->query($sql);
    $sucursales = $q_suc->result();
    foreach($sucursales as $sucursal) {

      // Ponemos la sucursal para que separe
      $suc = new stdClass();
      $suc->codigo = $sucursal->nombre;
      $salida[] = $suc;

      for($i=0;$i<sizeof($articulos);$i++) {
        $row = clone $articulos[$i];

        $sql = "SELECT IF(SUM(FI.cantidad) IS NULL,0,SUM(FI.cantidad)) AS cantidad_compra, ";
        $sql.= " IF(MAX(F.fecha) IS NULL,'',DATE_FORMAT(MAX(F.fecha),'%d/%m/%Y')) AS fecha_compra ";
        $sql.= "FROM ingresos_proveedores_items FI ";
        $sql.= "INNER JOIN ingresos_proveedores F ON (FI.id_ingreso = F.id AND FI.id_empresa = F.id_empresa) ";
        $sql.= "WHERE FI.id_empresa = $id_empresa ";
        $sql.= "AND F.fecha >= '$desde' AND F.fecha <= '$hasta' ";
        $sql.= "AND F.id_proveedor = $row->id_proveedor ";
        $sql.= "AND F.id_almacen = $sucursal->id ";
        $sql.= "AND FI.id_articulo = $row->id_articulo ";
        $q_compra = $this->db->query($sql);
        if ($q_compra->num_rows() > 0) {
          $r_compra = $q_compra->row();
          $row->cantidad_compra = $r_compra->cantidad_compra;
          $row->fecha_compra = $r_compra->fecha_compra;
        } else {
          $row->cantidad_compra = 0;
          $row->fecha_compra = "";
        }

        $vendido = $this->Venta_Model->articulos(array(
          "desde"=>$desde,
          "hasta"=>$hasta,
          "id_empresa"=>$id_empresa,
          "id_sucursal"=>$sucursal->id,
          "id_articulo"=>$row->id_articulo,
        ));
        if (sizeof($vendido["results"])>0) {
          $rr = $vendido["results"][0];
          $row->cantidad_venta = (float)$rr->cantidad;
        } else {
          $row->cantidad_venta = 0;
        }

        // Fecha de ultima venta
        $sql = "SELECT IF(MAX(fecha) IS NULL,'',MAX(fecha)) AS fecha ";
        $sql.= "FROM stock_movimientos ";
        $sql.= "WHERE id_empresa = $id_empresa ";
        $sql.= "AND id_articulo = $row->id_articulo ";
        $sql.= "AND id_sucursal = $sucursal->id ";
        $sql.= "AND movimiento = 'B' ";
        $sql.= "ORDER BY fecha DESC, id DESC ";
        $q_venta = $this->db->query($sql);
        $r_venta = $q_venta->row();
        $row->fecha_venta = (!empty($r_venta->fecha)) ? fecha_es($r_venta->fecha) : "";

        $row->costo_final = 0;
        $row->precio_final_dto = 0;
        $row->stock = 0;

        // Tomamos el costo y el precio de ese articulo
        $sql = "SELECT * FROM articulos_precios_sucursales APS ";
        $sql.= "WHERE APS.id_empresa = $id_empresa ";
        $sql.= "AND APS.id_sucursal = $sucursal->id ";
        $sql.= "AND APS.id_articulo = $row->id_articulo ";
        $q_precio = $this->db->query($sql);
        if ($q_precio->num_rows()>0) {
          $r_precio = $q_precio->row();
          $row->costo_final = $r_precio->costo_final;
          $row->precio_final_dto = $r_precio->precio_final_dto;
        }

        // Tomamos el stock
        $sql = "SELECT stock_actual FROM stock ";
        $sql.= "WHERE id_empresa = $id_empresa ";
        $sql.= "AND id_sucursal = $sucursal->id ";
        $sql.= "AND id_articulo = $row->id_articulo ";
        $q_stock = $this->db->query($sql);
        if ($q_stock->num_rows()>0) {
          $r_stock = $q_stock->row();
          $row->stock = $r_stock->stock_actual;
        }

        // Calculamos el %
        $row->porcentaje = ($row->cantidad_compra > 0) ? number_format(($row->cantidad_venta / $row->cantidad_compra)*100,2) : 0;
        $salida[] = $row;
      }
    }
    echo json_encode(array(
      "results"=>$salida,
    ));    
  }

  function compras_ventas_periodo() {

    set_time_limit(0);

    $id_empresa = $this->get_empresa();
    $id_proveedor = parent::get_post("id_proveedor",0);
    $filter = parent::get_post("filter","");
    $desde = parent::get_post("desde",date("d/m/Y"));
    $hasta = parent::get_post("hasta",date("d/m/Y"));
    $order = parent::get_post("order","asc");
    $order_by = parent::get_post("order_by","nombre");
    $id_sucursal = parent::get_post("id_sucursal",0);
    $this->load->helper("fecha_helper");
    $desde = fecha_mysql(str_replace("-","/",$desde));
    $hasta = fecha_mysql(str_replace("-","/",$hasta));
    $this->load->model("Venta_Model");

    $sql = "SELECT A.id AS id_articulo, A.codigo, A.codigo_barra, A.nombre, ";
    $sql.= "A.custom_10 AS codigo_prov ";
    $sql.= "FROM articulos A ";
    // No se hace inner join porque hay veces que los proveedores estan dos veces con distinto codigo
    //$sql.= "INNER JOIN articulos_proveedores AP ON (A.id_empresa = AP.id_empresa AND A.id = AP.id_articulo) ";
    $sql.= "WHERE A.id_empresa = $id_empresa ";
    if (!empty($filter)) $sql.= "AND A.nombre LIKE '%$filter%' ";
    if (!empty($id_proveedor)) $sql.= "AND EXISTS (SELECT 1 FROM articulos_proveedores AP WHERE A.id_empresa = AP.id_empresa AND A.id = AP.id_articulo AND AP.id_proveedor = $id_proveedor) ";
    $sql.= "ORDER BY A.nombre ASC ";
    $q = $this->db->query($sql);
    foreach($q->result() as $row) {

      $sql = "SELECT IF(SUM(FI.cantidad) IS NULL,0,SUM(FI.cantidad)) AS cantidad_compra, ";
      $sql.= " IF(MAX(F.fecha) IS NULL,'',DATE_FORMAT(MAX(F.fecha),'%d/%m/%Y')) AS fecha_compra ";
      $sql.= "FROM ingresos_proveedores_items FI ";
      $sql.= "INNER JOIN ingresos_proveedores F ON (FI.id_ingreso = F.id AND FI.id_empresa = F.id_empresa) ";
      $sql.= "WHERE FI.id_empresa = $id_empresa ";
      $sql.= "AND F.fecha >= '$desde' AND F.fecha <= '$hasta' ";
      $sql.= "AND F.id_proveedor = $id_proveedor ";
      if (!empty($id_sucursal)) $sql.= "AND F.id_almacen = $id_sucursal ";
      $sql.= "AND FI.id_articulo = $row->id_articulo ";
      $q_compra = $this->db->query($sql);
      $r_compra = $q_compra->row();
      $row->cantidad_compra = $r_compra->cantidad_compra;
      $row->fecha_compra = $r_compra->fecha_compra;

      $vendido = $this->Venta_Model->articulos(array(
        "desde"=>$desde,
        "hasta"=>$hasta,
        "id_empresa"=>$id_empresa,
        "id_sucursal"=>$id_sucursal,
        "id_articulo"=>$row->id_articulo,
      ));
      if (sizeof($vendido["results"])>0) {
        $rr = $vendido["results"][0];
        $row->cantidad_venta = (float)$rr->cantidad;
      } else {
        $row->cantidad_venta = 0;
      }

      // Fecha de ultima venta
      $sql = "SELECT IF(MAX(fecha) IS NULL,'',MAX(fecha)) AS fecha ";
      $sql.= "FROM stock_movimientos ";
      $sql.= "WHERE id_empresa = $id_empresa ";
      $sql.= "AND id_articulo = $row->id_articulo ";
      if (!empty($id_sucursal)) $sql.= "AND id_sucursal = $id_sucursal ";
      $sql.= "AND movimiento = 'B' ";
      $sql.= "ORDER BY fecha DESC, id DESC ";
      $q_venta = $this->db->query($sql);
      $r_venta = $q_venta->row();
      $row->fecha_venta = (!empty($r_venta->fecha)) ? fecha_es($r_venta->fecha) : "";

      $row->costo_final = 0;
      $row->precio_final_dto = 0;
      $row->stock = 0;
      if (!empty($id_sucursal)) {
        // Tomamos el costo y el precio de ese articulo
        $sql = "SELECT * FROM articulos_precios_sucursales APS ";
        $sql.= "WHERE APS.id_empresa = $id_empresa ";
        $sql.= "AND APS.id_sucursal = $id_sucursal ";
        $sql.= "AND APS.id_articulo = $row->id_articulo ";
        $q_precio = $this->db->query($sql);
        if ($q_precio->num_rows()>0) {
          $r_precio = $q_precio->row();
          $row->costo_final = $r_precio->costo_final;
          $row->precio_final_dto = $r_precio->precio_final_dto;
        }

        // Tomamos el stock
        $sql = "SELECT stock_actual FROM stock ";
        $sql.= "WHERE id_empresa = $id_empresa ";
        $sql.= "AND id_sucursal = $id_sucursal ";
        $sql.= "AND id_articulo = $row->id_articulo ";
        $q_stock = $this->db->query($sql);
        if ($q_stock->num_rows()>0) {
          $r_stock = $q_stock->row();
          $row->stock = $r_stock->stock_actual;
        }
      }

      // Calculamos el %
      $row->porcentaje = ($row->cantidad_compra > 0) ? number_format(($row->cantidad_venta / $row->cantidad_compra)*100,2) : 0;
      $salida[] = $row;
    }
    echo json_encode(array(
      "results"=>$salida,
    ));    
  }

  function ventas_por_ingresos() {

    set_time_limit(0);

    $id_empresa = $this->get_empresa();
    $ids_ingresos = parent::get_post("ingresos");
    $ids_ingresos = str_replace("-", ",", $ids_ingresos);
    $desde = parent::get_post("desde",date("d/m/Y"));
    $hasta = parent::get_post("hasta",date("d/m/Y"));
    $order = parent::get_post("order","asc");
    $order_by = parent::get_post("order_by","nombre");
    $id_sucursal = parent::get_post("id_sucursal",0);
    $this->load->helper("fecha_helper");
    $desde = fecha_mysql(str_replace("-","/",$desde));
    $hasta = fecha_mysql(str_replace("-","/",$hasta));
    $this->load->model("Venta_Model");

    $salida = array();
    $sql = "SELECT IF(SUM(FI.cantidad) IS NULL,0,SUM(FI.cantidad)) AS cantidad_compra, ";
    $sql.= " IF(SUM(FI.total_final) IS NULL,0,SUM(FI.total_final)) AS total_compra, ";
    $sql.= " IF(MAX(F.fecha) IS NULL,'',DATE_FORMAT(MAX(F.fecha),'%d/%m/%Y')) AS fecha_compra, ";
    $sql.= " FI.id_articulo, F.id_almacen, ALM.nombre AS sucursal, ART.nombre, ART.codigo, ART.codigo_barra, ART.custom_10 AS codigo_prov, ";
    $sql.= " ART.costo_final, ART.precio_final_dto, 0 AS stock ";
    $sql.= "FROM ingresos_proveedores_items FI ";
    $sql.= "INNER JOIN ingresos_proveedores F ON (FI.id_ingreso = F.id AND FI.id_empresa = F.id_empresa) ";
    $sql.= "INNER JOIN articulos ART ON (FI.id_articulo = ART.id AND FI.id_empresa = ART.id_empresa) ";
    $sql.= "INNER JOIN almacenes ALM ON (F.id_empresa = ALM.id_empresa AND F.id_almacen = ALM.id) ";
    $sql.= "WHERE FI.id_empresa = $id_empresa ";
    $sql.= "AND FI.id_ingreso IN ($ids_ingresos) ";
    $sql.= "GROUP BY FI.id_articulo, F.id_almacen ";
    $sql.= "ORDER BY ALM.nombre ASC, $order_by $order ";
    $q = $this->db->query($sql);
    foreach($q->result() as $row) {
      $vendido = $this->Venta_Model->articulos(array(
        "desde"=>$desde,
        "hasta"=>$hasta,
        "id_empresa"=>$id_empresa,
        "id_sucursal"=>$row->id_almacen,
        "id_articulo"=>$row->id_articulo,
      ));
      if (sizeof($vendido["results"])>0) {
        $rr = $vendido["results"][0];
        $row->cantidad_venta = (float)$rr->cantidad;
        $row->total_venta = (float)$rr->total_final;
      } else {
        $row->cantidad_venta = 0;
        $row->total_venta = 0;
      }

      // Fecha de ultima venta
      $sql = "SELECT IF(MAX(fecha) IS NULL,'',MAX(fecha)) AS fecha ";
      $sql.= "FROM stock_movimientos ";
      $sql.= "WHERE id_empresa = $id_empresa ";
      $sql.= "AND id_articulo = $row->id_articulo ";
      $sql.= "AND id_sucursal = $row->id_almacen ";
      $sql.= "AND movimiento = 'B' ";
      $sql.= "ORDER BY fecha DESC, id DESC ";
      $q_venta = $this->db->query($sql);
      $r_venta = $q_venta->row();
      $row->fecha_venta = (!empty($r_venta->fecha)) ? fecha_es($r_venta->fecha) : "";

      // Tomamos el costo y el precio de ese articulo
      if ($row->id_almacen != 0) {
        $sql = "SELECT * FROM articulos_precios_sucursales APS ";
        $sql.= "WHERE APS.id_empresa = $id_empresa ";
        $sql.= "AND APS.id_sucursal = $row->id_almacen ";
        $sql.= "AND APS.id_articulo = $row->id_articulo ";
        $q_precio = $this->db->query($sql);
        if ($q_precio->num_rows()>0) {
          $r_precio = $q_precio->row();
          $row->costo_final = $r_precio->costo_final;
          $row->precio_final_dto = $r_precio->precio_final_dto;
        }

        // Tomamos el stock
        $sql = "SELECT stock_actual FROM stock ";
        $sql.= "WHERE id_empresa = $id_empresa ";
        $sql.= "AND id_sucursal = $row->id_almacen ";
        $sql.= "AND id_articulo = $row->id_articulo ";
        $q_stock = $this->db->query($sql);
        if ($q_stock->num_rows()>0) {
          $r_stock = $q_stock->row();
          $row->stock = $r_stock->stock_actual;
        }
      }

      // Calculamos el %
      $row->porcentaje = ($row->cantidad_compra > 0) ? number_format(($row->cantidad_venta / $row->cantidad_compra)*100,2) : 0;
      $salida[] = $row;
    }
    echo json_encode(array(
      "results"=>$salida,
    ));
  }

  function prestamos() {

    @session_start();
    $id_empresa = $this->get_empresa();
    $estado = isset($_SESSION["estado"]) ? $_SESSION["estado"] : 0;
    $this->load->helper("fecha_helper");
    $f_desde = $this->input->post("desde");
    $f_hasta = $this->input->post("hasta");
    $id_sucursal = ($this->input->post("id_sucursal") === FALSE) ? 0 : $this->input->post("id_sucursal");
    $intervalo = "D";

    $grafico = array();

    $series = array();
    $desde = new DateTime(fecha_mysql($f_desde));
    $hasta = new DateTime(fecha_mysql($f_hasta));
    $hasta->add(new DateInterval('P1D'));
    $interval = new DateInterval('P1'.$intervalo);
    $range = new DatePeriod($desde,$interval,$hasta);
    $diff = $hasta->diff($desde)->format("%a");

    $pagos = array(); 
    $otorgaciones = array();

    // Sumamos todos los pagos
    $cantidad_operaciones = 0;
    $total_ventas = 0;
    $cancelacion_capital = 0;
    $cancelacion_interes = 0;
    $sql_base = "SELECT IF(SUM(F.monto - F.descuento) IS NULL,0,SUM(F.monto - F.descuento)) AS total, ";
    $sql_base.= " IF(SUM(F.cancelacion_capital) IS NULL,0,SUM(F.cancelacion_capital)) AS cancelacion_capital, ";
    $sql_base.= " IF(SUM(F.cancelacion_interes) IS NULL,0,SUM(F.cancelacion_interes)) AS cancelacion_interes, ";
    $sql_base.= " IF(COUNT(*) IS NULL,0,COUNT(*)) AS cantidad ";
    $sql_base.= "FROM pres_cajas_movimientos F ";
    $sql_base.= "WHERE F.id_empresa = $id_empresa ";
    $sql_base.= "AND F.id_concepto = 241 "; // PAGO
    if (!empty($id_sucursal)) $sql_base.= "AND F.id_sucursal = $id_sucursal ";
    foreach($range as $fecha) {
      $sql = $sql_base;
      $sql.= "AND DATE_FORMAT(F.fecha,'%Y-%m-%d') = '".$fecha->format("Y-m-d")."' ";
      $q = $this->db->query($sql);
      foreach($q->result() as $rr) {
        $total = (float)$rr->total;
        $cantidad = (float)$rr->cantidad;
        $total_ventas += $total;
        $cancelacion_capital += ((float) $rr->cancelacion_capital);
        $cancelacion_interes += ((float) $rr->cancelacion_interes);
        $cantidad_operaciones += $cantidad;
        $pagos[] = $total;
      }
    }
    $total_pagos = $total_ventas;
    $cantidad_pagos = $cantidad_operaciones;

    // Sumamos todas las otorgaciones
    $cantidad_operaciones = 0;
    $total_ventas = 0;
    $sql_base = "SELECT IF(SUM(F.monto - F.descuento) IS NULL,0,SUM(F.monto - F.descuento)) AS total, ";
    $sql_base.= " IF(COUNT(*) IS NULL,0,COUNT(*)) AS cantidad ";
    $sql_base.= "FROM pres_cajas_movimientos F ";
    $sql_base.= "WHERE F.id_empresa = $id_empresa ";
    $sql_base.= "AND F.id_concepto = 271 "; // OTORGACION
    if (!empty($id_sucursal)) $sql_base.= "AND F.id_sucursal = $id_sucursal ";
    foreach($range as $fecha) {
      $sql = $sql_base;
      $sql.= "AND DATE_FORMAT(F.fecha,'%Y-%m-%d') = '".$fecha->format("Y-m-d")."' ";
      $q = $this->db->query($sql);
      foreach($q->result() as $rr) {
        $total = (float)$rr->total;
        $cantidad = (float)$rr->cantidad;
        $total_ventas += $total;
        $cantidad_operaciones += $cantidad;
        $otorgaciones[] = $total;
      }
    }
    $cantidad_otorgaciones = $cantidad_operaciones;
    $total_otorgaciones = $total_ventas;

    $grafico[] = array(
      "series"=>array(
        array(
          "name"=>"Pagos",
          "data"=>$pagos,
        ),
        array(
          "name"=>"Otorgaciones",
          "data"=>$otorgaciones,
        ),
      ),
      "desde"=>$desde->format("d/m/Y"),
      "hasta"=>$hasta->format("d/m/Y"),
      "intervalo"=>$intervalo,
    ); 

    echo json_encode(array(
      "grafico"=>$grafico,
      "total_pagos"=>$total_pagos,
      "total_otorgaciones"=>$total_otorgaciones,
      "cancelacion_capital"=>$cancelacion_capital,
      "cancelacion_interes"=>$cancelacion_interes,
      "cantidad_pagos"=>$cantidad_pagos,
      "cantidad_otorgaciones"=>$cantidad_otorgaciones,
      "fecha_desde"=>str_replace("-","/",$f_desde),
      "fecha_hasta"=>str_replace("-","/",$f_hasta),
      "id_sucursal"=>$id_sucursal,
    ));
  }


  function otorgaciones() {

    @session_start();
    $id_empresa = $this->get_empresa();
    $estado = isset($_SESSION["estado"]) ? $_SESSION["estado"] : 0;
    $this->load->helper("fecha_helper");
    $f_desde = $this->input->post("desde");
    $f_hasta = $this->input->post("hasta");
    $id_sucursal = ($this->input->post("id_sucursal") === FALSE) ? 0 : $this->input->post("id_sucursal");
    $desde = fecha_mysql($f_desde);
    $hasta = fecha_mysql($f_hasta);

    $todos = array();
    $nuevos = array();
    $reingresos = array();
    $paralelos = array();
    $renovaciones = array();

    // Obtenemos los prestamos otorgados
    $sql = "SELECT C.*, P.id AS id_prestamo, P.numero, P.cantidad_cuotas, P.cantidad_cuotas_pagas, P.monto_prestado, P.valor_cuota, ";
    $sql.= " P.dias_mora, P.deuda_vencida, P.fecha_ultimo_pago AS fecha_ultimo_pago_en, ";
    $sql.= " IF(P.fecha_ultimo_pago = '0000-00-00','',DATE_FORMAT(P.fecha_ultimo_pago,'%d/%m/%Y')) AS fecha_ultimo_pago ";
    $sql.= "FROM pres_prestamos P ";
    $sql.= "INNER JOIN pres_clientes C ON (P.id_empresa = C.id_empresa AND P.id_cliente = C.id) ";
    $sql.= "WHERE P.id_empresa = $id_empresa ";
    $sql.= "AND P.fecha >= '$desde' AND P.fecha <= '$hasta' ";
    if ($id_sucursal != 0) $sql.= "AND P.id_sucursal = $id_sucursal ";
    $q = $this->db->query($sql);
    foreach($q->result() as $prestamo) {

      $sql = "SELECT * FROM pres_prestamos P ";
      $sql.= "WHERE P.id_empresa = $id_empresa ";
      $sql.= "AND P.id_cliente = $prestamo->id ";
      $sql.= "AND P.id != $prestamo->id_prestamo ";
      $qq = $this->db->query($sql);
      $fechas_pago = array();
      if ($qq->num_rows() == 0) {
        // Si no tiene un prestamo anterior, entonces es CLIENTE NUEVO
        $todos[] = $prestamo;
        $nuevos[] = $prestamo;
      } else {
        // Examinamos los otros prestamos que tiene el cliente
        $cantidad_cancelados_otros_prestamos = 0;
        $otros_prestamos = $qq->result();
        foreach($otros_prestamos as $otro_prestamo) {
          if ($otro_prestamo->cantidad_cuotas == $otro_prestamo->cantidad_cuotas_pagas) {
            $fechas_pago[] = $otro_prestamo->fecha_ultimo_pago;
            $cantidad_cancelados_otros_prestamos++;
          } else {
            // Si tiene al menos un prestamo activo, directamente es un PARALELO
            $todos[] = $prestamo;
            $paralelos[] = $prestamo;
            break;
          }
        }

        // Si todos los otros prestamos estan cancelados, es un REINGRESO
        if (sizeof($otros_prestamos) == $cantidad_cancelados_otros_prestamos) {

          // Si la fecha de ultimo pago de un prestamo anterior coincide con la fecha de otorgacion de un prestamo nuevo
          // entonces es una renovacion
          if (in_array($prestamo->fecha_ultimo_pago_en, $fechas_pago)) {
            $todos[] = $prestamo;
            $renovaciones[] = $prestamo;
          } else {
            // Sino, es un reingreso (tiene todo cancelado y volvio a pedir otro prestamo diferente)
            $todos[] = $prestamo;
            $reingresos[] = $prestamo;
          }
        }

      }
    }

    echo json_encode(array(
      "todos"=>$todos,
      "nuevos"=>$nuevos,
      "reingresos"=>$reingresos,
      "paralelos"=>$paralelos,
      "renovaciones"=>$renovaciones,
    ));
  }


  function prestamos_activos() {

    @session_start();
    $id_empresa = $this->get_empresa();
    $id_sucursal = parent::get_get("id_sucursal",0);
    $this->load->helper("fecha_helper");
    $salida = array();

    $sql = "SELECT PC.id AS id_plan, PC.nombre AS plan, IF(COUNT(*) IS NULL,0,COUNT(*)) AS cantidad ";
    $sql.= "FROM pres_prestamos P ";
    $sql.= "INNER JOIN pres_planes_credito PC ON (P.id_plan = PC.id AND P.id_empresa = PC.id_empresa) ";
    $sql.= "WHERE P.cantidad_cuotas_pagas < cantidad_cuotas "; // Que aun este activo
    $sql.= "AND P.id_empresa = $id_empresa ";
    if (!empty($id_sucursal)) $sql.= "AND P.id_sucursal = $id_sucursal ";
    $sql.= "GROUP BY P.id_plan ";
    $sql.= "ORDER BY cantidad DESC ";
    $q = $this->db->query($sql);
    foreach($q->result() as $row) {

      // Contamos la cantidad de prestamos que estan en mora de ese plan

      $sql = "SELECT IF(COUNT(*) IS NULL,0,COUNT(*)) AS cantidad ";
      $sql.= "FROM pres_prestamos P ";
      $sql.= "WHERE P.id_empresa = $id_empresa ";
      $sql.= "AND P.id_plan = $row->id_plan ";
      $sql.= "AND P.dias_mora > 0 ";
      if (!empty($id_sucursal)) $sql.= "AND P.id_sucursal = $id_sucursal ";
      $qq = $this->db->query($sql);
      $rr = $qq->row();
      $row->cantidad_mora = $rr->cantidad;

      $sql = "SELECT IF(COUNT(*) IS NULL,0,COUNT(*)) AS cantidad ";
      $sql.= "FROM pres_prestamos P ";
      $sql.= "WHERE P.id_empresa = $id_empresa ";
      $sql.= "AND P.id_plan = $row->id_plan ";
      $sql.= "AND P.dias_mora > 0 AND P.dias_mora <= 30 ";
      if (!empty($id_sucursal)) $sql.= "AND P.id_sucursal = $id_sucursal ";
      $qq = $this->db->query($sql);
      $rr = $qq->row();
      $row->cantidad_mora_30 = $rr->cantidad;

      $sql = "SELECT IF(COUNT(*) IS NULL,0,COUNT(*)) AS cantidad ";
      $sql.= "FROM pres_prestamos P ";
      $sql.= "WHERE P.id_empresa = $id_empresa ";
      $sql.= "AND P.id_plan = $row->id_plan ";
      $sql.= "AND P.dias_mora > 30 AND P.dias_mora <= 60 ";
      if (!empty($id_sucursal)) $sql.= "AND P.id_sucursal = $id_sucursal ";
      $qq = $this->db->query($sql);
      $rr = $qq->row();
      $row->cantidad_mora_60 = $rr->cantidad;

      $sql = "SELECT IF(COUNT(*) IS NULL,0,COUNT(*)) AS cantidad ";
      $sql.= "FROM pres_prestamos P ";
      $sql.= "WHERE P.id_empresa = $id_empresa ";
      $sql.= "AND P.id_plan = $row->id_plan ";
      $sql.= "AND P.dias_mora > 60 AND P.dias_mora <= 90 ";
      if (!empty($id_sucursal)) $sql.= "AND P.id_sucursal = $id_sucursal ";
      $qq = $this->db->query($sql);
      $rr = $qq->row();
      $row->cantidad_mora_90 = $rr->cantidad;

      $sql = "SELECT IF(COUNT(*) IS NULL,0,COUNT(*)) AS cantidad ";
      $sql.= "FROM pres_prestamos P ";
      $sql.= "WHERE P.id_empresa = $id_empresa ";
      $sql.= "AND P.id_plan = $row->id_plan ";
      $sql.= "AND P.dias_mora > 90 ";
      if (!empty($id_sucursal)) $sql.= "AND P.id_sucursal = $id_sucursal ";
      $qq = $this->db->query($sql);
      $rr = $qq->row();
      $row->cantidad_mora_mas_90 = $rr->cantidad;

      

      // Cuota mas elegida por plan
      $sql = "SELECT P.cantidad_cuotas, COUNT(*) AS cantidad ";
      $sql.= "FROM pres_prestamos P ";
      $sql.= "WHERE P.id_empresa = $id_empresa ";
      $sql.= "AND P.id_plan = $row->id_plan ";
      if (!empty($id_sucursal)) $sql.= "AND P.id_sucursal = $id_sucursal ";
      $sql.= "GROUP BY P.cantidad_cuotas ";
      $sql.= "ORDER BY COUNT( * ) DESC ";
      $sql.= "LIMIT 1";
      $qq = $this->db->query($sql);
      $rr = $qq->row();
      $row->cuota_mas_elegida = $rr->cantidad_cuotas;
      $row->veces_mas_elegida = $rr->cantidad;

      $salida[] = $row;
    }
    echo json_encode(array(
      "results"=>$salida,
      "total"=>sizeof($salida),
    ));
  }


  function resumen() {

    @session_start();
    $id_empresa = parent::get_empresa();
    $this->load->helper("fecha_helper");
    $this->load->model("Stock_Model");
    $this->load->model("Venta_Model");
    $this->load->model("Compra_Model");
    $this->load->model("Cheque_Model");
    $this->load->model("Cupon_Tarjeta_Model");
    $this->load->model("Orden_Pago_Model");
    $desde = fecha_mysql($this->input->post("desde"));
    $hasta = fecha_mysql($this->input->post("hasta"));
    $estado = isset($_SESSION["estado"]) ? $_SESSION["estado"] : 0;

    $fecha_desde = new DateTime($desde);
    $fecha_hasta = new DateTime($hasta);
    $interval = DateInterval::createFromDateString('1 month');
    $period = new DatePeriod($fecha_desde, $interval, $fecha_hasta);
    $parametros = array();
    $meses = array();
    foreach($period as $dt) {
      $parametros[] = array(
        "desde"=>$dt->format("Y-m-01"),
        "hasta"=>$dt->format("Y-m-t"),
        "movimiento"=>$dt->format("my"),
      );
      $meses[] = ucwords(substr(get_mes($dt->format("n")),0,3))." ".$dt->format("y");
    }

    /*
    $parametros = array(
      array("desde"=>"2016-11-01","hasta"=>"2016-11-30","movimiento"=>"1116"),
      array("desde"=>"2016-12-01","hasta"=>"2016-12-31","movimiento"=>"1216"),
      array("desde"=>"2017-01-01","hasta"=>"2017-01-31","movimiento"=>"0117"),
      array("desde"=>"2017-02-01","hasta"=>"2017-02-31","movimiento"=>"0217"),
      array("desde"=>"2017-03-01","hasta"=>"2017-03-31","movimiento"=>"0317"),
      array("desde"=>"2017-04-01","hasta"=>"2017-04-31","movimiento"=>"0417"),
      array("desde"=>"2017-05-01","hasta"=>"2017-05-31","movimiento"=>"0517"),
      array("desde"=>"2017-06-01","hasta"=>"2017-06-31","movimiento"=>"0617"),
      array("desde"=>"2017-07-01","hasta"=>"2017-07-31","movimiento"=>"0717"),
      array("desde"=>"2017-08-01","hasta"=>"2017-08-31","movimiento"=>"0817"),
      array("desde"=>"2017-09-01","hasta"=>"2017-09-31","movimiento"=>"0917"),
      array("desde"=>"2017-10-01","hasta"=>"2017-10-31","movimiento"=>"1017"),
    );
    */
    $ventas = array();
    $compras = array();
    $gastos = array();
    $pagos_efectivo = array();
    $pagos_cheques = array();
    $stock_inicial = array();
    $stock_final = array();

    // Debemos tomar todos los conceptos que pertenecen al concepto por el cual estamos filtrando
    $this->load->model("Tipo_Gasto_Model");
    $ids_conceptos_compras = $this->Tipo_Gasto_Model->get_ids_totaliza_en("C");
    $ids_conceptos_gastos = $this->Tipo_Gasto_Model->get_ids_totaliza_en("G");

    foreach($parametros as $p) {

      $desde_menos_uno = date_create($p["desde"]);
      date_sub($desde_menos_uno,date_interval_create_from_date_string("1 day"));
      $stock_inicial_total = 0;
      $stocks_iniciales = $this->Stock_Model->valoracion(array(
        "id_empresa"=>$id_empresa,
        "fecha"=>$desde_menos_uno->format("Y-m-d"),
      ));
      foreach($stocks_iniciales as $sin) {
        $stock_inicial_total += $sin->costo_final;
      }
      $stock_inicial[] = $stock_inicial_total;

      $stock_final_total = 0;
      $stocks_finales = $this->Stock_Model->valoracion(array(
        "id_empresa"=>$id_empresa,
        "fecha"=>$p["hasta"],
      ));
      foreach($stocks_finales as $sin) {
        $stock_final_total += $sin->costo_final;
      }
      $stock_final[] = $stock_final_total;


      // Calculamos las ventas
      $ventas[] = $this->Venta_Model->get_total(array(
        "id_empresa"=>$id_empresa,
        "estado"=>$estado,
        "desde"=>$p["desde"],
        "hasta"=>$p["hasta"],
      ));

      // Calculamos las compras
      $compras[] = $this->Compra_Model->get_total(array(
        "id_empresa"=>$id_empresa,
        "estado"=>$estado,
        "movimiento"=>$p["movimiento"],
        "ids_conceptos"=>$ids_conceptos_compras,
      ));

      // Calculamos los gastos
      $gastos[] = $this->Compra_Model->get_total(array(
        "id_empresa"=>$id_empresa,
        "estado"=>$estado,
        "movimiento"=>$p["movimiento"],
        "ids_conceptos"=>$ids_conceptos_gastos,
      ));

      // Ordenes de pago
      $total_efectivo = 0;
      $ordenes_pago = $this->Orden_Pago_Model->get_list(array(
        "desde"=>$p["desde"],
        "hasta"=>$p["hasta"],
        "order"=>"C.fecha DESC ",
        "offset"=>999999,
        "in_tipo_proveedor"=>"1,5",
      ));
      foreach($ordenes_pago as $o) {
        $total_efectivo += (float) $o->efectivo + (float) $o->total_depositos + (float) $o->descuento + (float) $o->rotura;
      }
      // Compras realizadas en efectivo
      $comprobantes_efectivo = $this->Compra_Model->listado(array(
        "desde"=>$p["desde"],
        "hasta"=>$p["hasta"],
        "ids_conceptos"=>$ids_conceptos_compras,
        "forma_pago"=>"E",
        "offset"=>999999,
      ));
      foreach($comprobantes_efectivo["results"] as $o) {
        $total_efectivo += (float) $o->total_general;
      }

      // Compras pagadas en efectivo
      $pagos_efectivo[] = $total_efectivo;

      // Compras pagadas en cheques
      $pagos_cheques[] = $this->Cheque_Model->get_total_cheques(array(
        "id_empresa"=>$id_empresa,
        "desde"=>$p["desde"],
        "hasta"=>$p["hasta"],
      ));

    }
    echo json_encode(array(
      "series"=>array(
        array("name"=>"Ventas","data"=>$ventas),
        array("name"=>"Compras","data"=>$compras),
        array("name"=>"Gastos","data"=>$gastos),
        array("name"=>"Pagos Efectivo","data"=>$pagos_efectivo),
        array("name"=>"Pagos Cheque","data"=>$pagos_cheques),
        array("name"=>"Stock Inicial","data"=>$stock_inicial),
        array("name"=>"Stock Final","data"=>$stock_final),
      ),
      "meses"=>$meses,
    ));
  }

  function compras() {

    @session_start();
    $id_empresa = $this->get_empresa();
    $estado = isset($_SESSION["estado"]) ? $_SESSION["estado"] : 0;
    $this->load->helper("fecha_helper");
    $this->load->model("Compra_Model");

    $tipo = $this->input->post("tipo_proveedor");
    if (!empty($tipo)) {
      $this->load->model("Tipo_Gasto_Model");
      $ids_conceptos = $this->Tipo_Gasto_Model->get_ids_totaliza_en($tipo);      
    } else $ids_conceptos = "";

    // Parametro que estamos sumando
    $parametro = $this->input->post("parametro");
    $label = "Totales";
    $campo = "FI.total_con_iva";
    if ($parametro == "N") {
      $campo = "FI.total_sin_iva";
      $label = "Netos";
    }

    // Agrupacion de intervalo de fechas
    // D = Dias; W = Semanas; M = Meses
    $intervalo = "M";//($this->input->post("intervalo") !== FALSE) ? $this->input->post("intervalo") : "D";

    // Indica si hay que comparar
    $comparar = ($this->input->post("comparar") !== FALSE) ? $this->input->post("comparar") : "";

    // Rangos de fechas
    $fechas = $this->input->post("fechas");

    // Tipo de proveedor
    $tipo_proveedor = $this->input->post("tipo_proveedor");

    $salida = array();
    foreach($fechas as $fech) {

      $series = array();
      $desde = new DateTime(fecha_mysql($fech["desde"]));
      $hasta = new DateTime(fecha_mysql($fech["hasta"]));
      $hasta->add(new DateInterval('P1D'));
      $interval = new DateInterval('P1'.$intervalo);
      $range = new DatePeriod($desde,$interval,$hasta);

      // Recorremos cada dia del rango
      $res = array(); $pag = array();
      foreach($range as $fecha) {
        $movimiento = $fecha->format("my");
        $total = $this->Compra_Model->get_total(array(
          "id_empresa"=>$id_empresa,
          "estado"=>$estado,
          "movimiento"=>$movimiento,
          "tipo_proveedor"=>$tipo_proveedor,
          "ids_conceptos"=>$ids_conceptos,
          ));
        $res[] = (float)$total;
      }
      // Agregamos la serie
      $series[] = array(
       "name"=>$label,
       "data"=>$res,
       );

      $salida[] = array(
        "series"=>$series,
        "desde"=>$desde->format("d/m/Y"),
        "hasta"=>$hasta->format("d/m/Y"),
        "intervalo"=>$intervalo,
        );
    }

    echo json_encode(array(
      "results"=>$salida,
      ));
  }



  function ventas_2() {

    @session_start();
    $id_empresa = $this->get_empresa();

    // CASO ESPECIAL YEYO, PUEDE VER MAS DE UNA EMPRESA
    $this->load->model("Empresa_Model");
    $in_ids_empresas = "";
    if ($id_empresa == 980) {
      $in_ids_empresas = implode(",", $this->Empresa_Model->get_ids_empresas_por_vendedor($this->Empresa_Model->get_id_vendedor_don_yeyo()));
    }

    $estado = isset($_SESSION["estado"]) ? $_SESSION["estado"] : 0;
    $this->load->helper("fecha_helper");
    $f_desde = $this->input->post("desde");
    $f_hasta = $this->input->post("hasta");
    $id_proyecto = $this->input->post("id_proyecto");
    $id_punto_venta = ($this->input->post("id_punto_venta") === FALSE) ? -1 : $this->input->post("id_punto_venta");
    $id_sucursal = ($this->input->post("id_sucursal") === FALSE) ? 0 : $this->input->post("id_sucursal");
    $id_usuario = ($this->input->post("id_usuario") === FALSE) ? 0 : $this->input->post("id_usuario");
    $in_sucursales = ($this->input->post("in_sucursales") === FALSE) ? "" : str_replace("-", ",", $this->input->post("in_sucursales"));
    $reparto = parent::get_post("reparto",0);
    $intervalo = "D";

    $grafico = array();
    $costo_mercaderia_vendida = 0;
    $total_ventas = 0;
    $total_clientes = 0;

    $series = array();
    $desde = new DateTime(fecha_mysql($f_desde));
    $hasta = new DateTime(fecha_mysql($f_hasta));
    $hasta->add(new DateInterval('P1D'));
    $interval = new DateInterval('P1'.$intervalo);
    $range = new DatePeriod($desde,$interval,$hasta);
    $diff = $hasta->diff($desde)->format("%a");

    $res = array(); $cos = array();

    $sql_base = "SELECT ";
    $sql_base.= " SUM((F.total - F.interes) * IF(TC.negativo = 1,-1,1)) AS total, ";
    $sql_base.= " SUM(F.costo_final * IF(TC.negativo = 1,-1,1)) AS costo ";
    $sql_base.= "FROM facturas F ";
    $sql_base.= "INNER JOIN tipos_comprobante TC ON (F.id_tipo_comprobante = TC.id) ";
    if (empty($in_ids_empresas)) $sql_base.= "WHERE F.id_empresa = $id_empresa ";
    else $sql_base.= "WHERE F.id_empresa IN ($in_ids_empresas) ";
    $sql_base.= "AND F.id_tipo_comprobante != 0 ";
    $sql_base.= "AND F.tipo != 'C' ";
    if ($id_punto_venta != -1) $sql_base.= "AND F.id_punto_venta = $id_punto_venta ";
    if (!empty($id_sucursal)) $sql_base.= "AND F.id_sucursal = $id_sucursal ";
    if (!empty($in_sucursales)) $sql_base.= "AND F.id_sucursal IN ($in_sucursales) ";
    if (!empty($reparto)) $sql_base.= "AND F.reparto = '$reparto' ";
    $sql_base.= "AND F.anulada = 0 AND F.pendiente = 0 ";
    if ($estado == 0) $sql_base.= "AND F.estado = $estado ";
    $sql_base.= "AND IF(F.id_origen = 1,IF(F.id_tipo_estado >= 4 AND F.id_tipo_estado != 7,1,0),1) = 1 ";    
    if (!empty($id_usuario)) $sql_base.= "AND F.id_usuario = $id_usuario ";
    /*    
    // TODO: Unificar esto
    if ($id_proyecto == 1) {
      $sql_base.= "AND F.anulada = 0 AND F.pendiente = 0 ";
      if ($estado == 0) $sql_base.= "AND F.estado = $estado ";      
      $sql_base.= "AND F.id_tipo_estado != 7 "; // Carrito Abandonado
    } else {
      $sql_base.= "AND F.id_tipo_estado = 6 "; // Estado finalizado
    }
    */

    // Recorremos cada dia del rango
    foreach($range as $fecha) {
      $sql = $sql_base;
      $sql.= "AND F.fecha = '".$fecha->format("Y-m-d")."' ";
      $q = $this->db->query($sql);
      foreach($q->result() as $rr) {
        // Sumamos los totames
        if (is_null($rr->total)) $rr->total = 0;
        $total = (float)$rr->total;
        $total_ventas += $total;
        $res[] = $total;
        // Sumamos los costos
        if (is_null($rr->costo)) $rr->costo = 0;
        $costo = (float)$rr->costo;
        $costo_mercaderia_vendida += $costo;
        $cos[] = $costo;
      }
    }

    // Cantidad de operaciones
    $sql = "SELECT COUNT(*) AS cantidad, ";
    $sql.= " SUM((efectivo-vuelto) * IF(TC.negativo = 1,-1,1)) AS efectivo, ";
    $sql.= " SUM((tarjeta-interes) * IF(TC.negativo = 1,-1,1)) AS tarjeta, ";
    $sql.= " SUM(cta_cte * IF(TC.negativo = 1,-1,1)) AS cta_cte, ";
    $sql.= " SUM(F.en_oferta * IF(TC.negativo = 1,-1,1)) AS en_oferta, ";
    $sql.= " SUM(descuento * IF(TC.negativo = 1,-1,1)) AS descuento ";
    $sql.= "FROM facturas F ";
    $sql.= "INNER JOIN tipos_comprobante TC ON (F.id_tipo_comprobante = TC.id) ";
    if (empty($in_ids_empresas)) $sql.= "WHERE F.id_empresa = $id_empresa ";
    else $sql.= "WHERE F.id_empresa IN ($in_ids_empresas) ";
    $sql.= "AND F.id_tipo_comprobante != 0 ";
    $sql.= "AND F.tipo != 'C' ";
    if ($id_punto_venta != -1) $sql.= "AND F.id_punto_venta = $id_punto_venta ";
    if (!empty($id_sucursal)) $sql.= "AND F.id_sucursal = $id_sucursal ";
    if (!empty($in_sucursales)) $sql.= "AND F.id_sucursal IN ($in_sucursales) ";
    $sql.= "AND F.anulada = 0 AND F.pendiente = 0 ";
    $sql.= "AND IF(F.id_origen = 1,IF(F.id_tipo_estado >= 4 AND F.id_tipo_estado != 7,1,0),1) = 1 ";    
    $sql.= "AND F.fecha >= '".$desde->format("Y-m-d")."' AND F.fecha < '".$hasta->format("Y-m-d")."' ";
    if (!empty($reparto)) $sql.= "AND F.reparto = '$reparto' ";
    if (!empty($id_usuario)) $sql.= "AND F.id_usuario = $id_usuario ";
    $sql_totales = $sql;
    $q = $this->db->query($sql);
    $r = $q->row();
    $cantidad_operaciones = (is_null($r->cantidad)) ? 0 : ((float)$r->cantidad);
    $efectivo = (is_null($r->efectivo)) ? 0 : ((float)$r->efectivo);
    $tarjetas = (is_null($r->tarjeta)) ? 0 : ((float)$r->tarjeta);
    $descuento = (is_null($r->descuento)) ? 0 : ((float)$r->descuento);
    $cuenta_corriente = (is_null($r->cta_cte)) ? 0 : ((float)$r->cta_cte);
    $total_ofertas = (is_null($r->en_oferta)) ? 0 : ((float)$r->en_oferta);

    // Sumamos los descuentos aplicados como ITEMS
    $sql_descuentos = "";
    if ($id_empresa == 249 || $id_empresa == 868) {
      $sql = "SELECT ";
      $sql.= " SUM(FI.total_con_iva * IF(TC.negativo = 1,-1,1)) AS descuento ";
      $sql.= "FROM facturas F INNER JOIN facturas_items FI ON (F.id_empresa = FI.id_empresa AND F.id_punto_venta = FI.id_punto_venta AND F.id = FI.id_factura) ";
      $sql.= "INNER JOIN tipos_comprobante TC ON (F.id_tipo_comprobante = TC.id) ";
      if (empty($in_ids_empresas)) $sql.= "WHERE F.id_empresa = $id_empresa ";
      else $sql.= "WHERE F.id_empresa IN ($in_ids_empresas) ";
      $sql.= "AND F.id_tipo_comprobante != 0 ";
      $sql.= "AND F.tipo != 'C' ";
      $sql.= "AND FI.id_articulo = 10213586 "; // CODIGO 2222
      if (!empty($reparto)) $sql.= "AND F.reparto = '$reparto' ";
      if ($id_punto_venta != -1) $sql.= "AND F.id_punto_venta = $id_punto_venta ";
      if (!empty($id_sucursal)) $sql.= "AND F.id_sucursal = $id_sucursal ";
      if (!empty($in_sucursales)) $sql.= "AND F.id_sucursal IN ($in_sucursales) ";
      $sql.= "AND F.anulada = 0 AND F.pendiente = 0 ";
      $sql.= "AND IF(F.id_origen = 1,IF(F.id_tipo_estado >= 4 AND F.id_tipo_estado != 7,1,0),1) = 1 ";    
      $sql.= "AND F.fecha >= '".$desde->format("Y-m-d")."' AND F.fecha < '".$hasta->format("Y-m-d")."' ";
      if (!empty($id_usuario)) $sql.= "AND F.id_usuario = $id_usuario ";
      $sql_descuentos = $sql;
      $q = $this->db->query($sql);
      $r_desc = $q->row();
      $r_desc->descuento = (is_null($r_desc->descuento) ? 0 : (float)$r_desc->descuento);
      $descuento += abs($r_desc->descuento);
    }

    // VENTA DE CLIENTES (NO CONSUMIDORES FINALES)
    if ($id_proyecto == 1) {
      $sql = "SELECT SUM((F.total - F.interes) * IF(TC.negativo = 1,-1,1)) AS total ";
      $sql.= "FROM facturas F ";
      $sql.= "INNER JOIN tipos_comprobante TC ON (F.id_tipo_comprobante = TC.id) ";
      if (empty($in_ids_empresas)) $sql.= "WHERE F.id_empresa = $id_empresa ";
      else $sql.= "WHERE F.id_empresa IN ($in_ids_empresas) ";
      $sql.= "AND F.id_tipo_comprobante != 0 ";
      $sql.= "AND F.tipo != 'C' ";
      $sql.= "AND F.fecha >= '".$desde->format("Y-m-d")."' AND F.fecha < '".$hasta->format("Y-m-d")."' ";
      if (!empty($reparto)) $sql.= "AND F.reparto = '$reparto' ";
      if ($id_punto_venta != -1) $sql.= "AND F.id_punto_venta = $id_punto_venta ";
      if (!empty($id_sucursal)) $sql.= "AND F.id_sucursal = $id_sucursal ";
      if (!empty($in_sucursales)) $sql.= "AND F.id_sucursal IN ($in_sucursales) ";
      $sql.= "AND F.anulada = 0 AND F.pendiente = 0 ";
      if ($estado == 0) $sql.= "AND F.estado = $estado ";
      $sql.= "AND IF(F.id_origen = 1,IF(F.id_tipo_estado >= 4 AND F.id_tipo_estado != 7,1,0),1) = 1 ";    
      $sql.= "AND F.id_cliente != 0 ";
      if (!empty($id_usuario)) $sql.= "AND F.id_usuario = $id_usuario ";
      $q = $this->db->query($sql);
      $r = $q->row();
      $total_clientes = (is_null($r->total)) ? 0 : ((float)$r->total);
    }

    // Venta promedio
    $venta_promedio = ($cantidad_operaciones > 0) ? $total_ventas / $cantidad_operaciones : 0;
    // Venta promedio por dia
    $venta_promedio_por_dia = ($diff <= 0) ? 0 : (float) $total_ventas / $diff;

    if ($id_proyecto == 1) {
      // Marcacion promedio
      $marcacion_promedio = ($costo_mercaderia_vendida == 0) ? 0 : (float) (($total_ventas / $costo_mercaderia_vendida) - 1) * 100;
      // Ganancia bruta
      $ganancia_bruta = (float) $total_ventas - $costo_mercaderia_vendida;
    }

    $this->load->model("Articulo_Model");
    // Articulos mas vendidos
    $mas_vendidos = array();
    $mayor_ganancia = array();
    /*
    $mas_vendidos = $this->Articulo_Model->mas_vendidos(array(
      "offset"=>10,
      "id_proyecto"=>$id_proyecto,
      "id_sucursal"=>$id_sucursal,
      "in_sucursales"=>$in_sucursales,
      "desde"=>fecha_mysql($f_desde),
      "hasta"=>fecha_mysql($f_hasta),
      "not_in_articulos"=>"1",
    ));
    // Articulos con mayor ganancia
    if ($id_proyecto == 1) { 
      $mayor_ganancia = $this->Articulo_Model->mayor_ganancia(array(
        "offset"=>10,
        "id_sucursal"=>$id_sucursal,
        "in_sucursales"=>$in_sucursales,
        "desde"=>fecha_mysql($f_desde),
        "hasta"=>fecha_mysql($f_hasta),
        "not_in_articulos"=>"1",
      ));
    }
    */

    // Agregamos la serie
    $series[] = array(
      "name"=>"Ventas",
      "data"=>$res,
    );
    if ($id_proyecto == 1) {
      $series[] = array(
        "name"=>"Costo Mercaderia Vendida",
        "data"=>$cos,
      );
    }

    $grafico[] = array(
      "series"=>$series,
      "desde"=>$desde->format("d/m/Y"),
      "hasta"=>$hasta->format("d/m/Y"),
      "intervalo"=>$intervalo,
    ); 

    echo json_encode(array(
      "grafico"=>$grafico,
      "total_clientes"=>$total_clientes,
      "costo_mercaderia_vendida"=>(isset($costo_mercaderia_vendida) ? $costo_mercaderia_vendida : 0),
      "total_ventas"=>$total_ventas,
      "cantidad_operaciones"=>$cantidad_operaciones,
      "venta_promedio"=>$venta_promedio,
      "venta_promedio_por_dia"=>$venta_promedio_por_dia,
      "efectivo"=>$efectivo,
      "tarjetas"=>$tarjetas,
      "total_descuentos"=>$descuento,
      "total_ofertas"=>$total_ofertas,
      "cuenta_corriente"=>$cuenta_corriente,
      "ganancia_bruta"=> (isset($ganancia_bruta) ? $ganancia_bruta : 0),
      "marcacion_promedio"=> (isset($marcacion_promedio) ? $marcacion_promedio : 0),
      "productos_mayor_ganancia"=> (isset($mayor_ganancia) ? $mayor_ganancia : array()),
      "productos_mas_vendidos"=>$mas_vendidos,
      "fecha_desde"=>str_replace("-","/",$f_desde),
      "fecha_hasta"=>str_replace("-","/",$f_hasta),
      "id_punto_venta"=>$id_punto_venta,
      "reparto"=>$reparto,
      "id_sucursal"=>$id_sucursal,
      "in_sucursales"=>$in_sucursales,
      "sql_totales"=>$sql_totales,
    ));

  }



  function tarjetas() {

    @session_start();
    $id_empresa = $this->get_empresa();
    $estado = isset($_SESSION["estado"]) ? $_SESSION["estado"] : 0;
    $this->load->helper("fecha_helper");
    $f_desde = $this->input->post("desde");
    $f_hasta = $this->input->post("hasta");
    $id_proyecto = $this->input->post("id_proyecto");
    $id_punto_venta = ($this->input->post("id_punto_venta") === FALSE) ? -1 : $this->input->post("id_punto_venta");
    $id_sucursal = ($this->input->post("id_sucursal") === FALSE) ? 0 : $this->input->post("id_sucursal");
    $in_sucursales = ($this->input->post("in_sucursales") === FALSE) ? "" : str_replace("-", ",", $this->input->post("in_sucursales"));

    $salida = array();
    $desde = new DateTime(fecha_mysql($f_desde));
    $hasta = new DateTime(fecha_mysql($f_hasta));
    $hasta->add(new DateInterval('P1D'));

    $sql_base = "FROM cupones_tarjetas CT ";
    $sql_base.= "INNER JOIN facturas F ON (CT.id_empresa = F.id_empresa AND CT.id_factura = F.id AND CT.id_punto_venta = F.id_punto_venta) ";
    $sql_base.= "INNER JOIN tarjetas T ON (CT.id_empresa = T.id_empresa AND CT.id_tarjeta = T.id) ";
    $sql_base.= "WHERE CT.id_empresa = $id_empresa ";
    $sql_base.= "AND F.tipo != 'P' ";
    if ($estado == 0) $sql_base.= "AND F.estado = $estado ";
    if (!empty($id_sucursal)) $sql_base.= "AND F.id_sucursal = $id_sucursal ";
    if (!empty($in_sucursales)) $sql_base.= "AND F.id_sucursal IN ($in_sucursales) ";
    if ($id_punto_venta != -1) $sql_base.= "AND F.id_punto_venta = $id_punto_venta ";
    // TODO: Unificar esto
    if ($id_proyecto == 1) {
      $sql_base.= "AND F.anulada = 0 AND F.pendiente = 0 ";
      $sql_base.= "AND F.id_tipo_estado != 7 "; // Carrito Abandonado
    } else {
      $sql_base.= "AND F.id_tipo_estado = 6 "; // Estado finalizado
    }
    $sql_base.= "AND F.fecha >= '".$desde->format("Y-m-d")."' AND F.fecha < '".$hasta->format("Y-m-d")."' ";

    // Calculamos los totales
    $sql_total = "SELECT ";
    $sql_total.= " COUNT(*) AS cantidad, ";
    $sql_total.= " SUM(CT.importe) AS importe, ";
    $sql_total.= " SUM(CT.interes) AS interes, ";
    $sql_total.= " SUM(CT.total) AS total ";
    $q = $this->db->query($sql_total.$sql_base);
    $r = $q->row();
    $salida["total_con_interes"] = is_null($r->total) ? 0 : $r->total;
    $salida["interes"] = is_null($r->interes) ? 0 : $r->interes;
    $salida["total_sin_interes"] = is_null($r->importe) ? 0 : $r->importe;
    $salida["cantidad_operaciones"] = is_null($r->cantidad) ? 0 : $r->cantidad;
    $salida["tarjeta_promedio"] = is_null($r->cantidad) ? 0 : ($r->total / $r->cantidad);

    $this->load->model("Venta_Model");
    $total_ventas = $this->Venta_Model->get_total(array(
      "id_empresa"=>$id_empresa,
      "desde"=>$desde->format("Y-m-d"),
      "hasta"=>$hasta->format("Y-m-d"),
      "id_sucursal"=>$id_sucursal,
      "id_punto_venta"=>$id_punto_venta,
      "in_sucursales"=>$in_sucursales,
      "estado"=>$estado,
    ));

    $salida["porcentaje_venta_tarjetas"] = (!empty($total_ventas)) ? ($salida["total_con_interes"] / $total_ventas * 100) : 0;
    $salida["total_ventas"] = $total_ventas;

    // Calculamos los totales por tarjeta
    $group = "GROUP BY T.id ";
    $group.= "ORDER BY cantidad DESC ";
    $sql = $sql_total.", T.nombre, T.id ".$sql_base.$group;
    $q = $this->db->query($sql);
    $salida["operaciones_por_tarjeta"] = $q->result();

    // Mostramos todos los comprobantes
    $sql = "SELECT CT.*, F.comprobante, T.nombre AS tarjeta, ";
    $sql.= " DATE_FORMAT(CT.fecha,'%d/%m/%Y %H:%i') AS fecha ";
    $q = $this->db->query($sql.$sql_base);
    $salida["listado"] = $q->result();

    $salida["grafico_tarjetas"] = array();

    echo json_encode($salida);
  }


  function ventas_por_dia() {

    @session_start();
    $id_empresa = $this->get_empresa();
    $estado = isset($_SESSION["estado"]) ? $_SESSION["estado"] : 0;
    $this->load->helper("fecha_helper");
    $f_desde = $this->input->post("desde");
    $f_hasta = $this->input->post("hasta");
    $id_proyecto = $this->input->post("id_proyecto");
    $id_punto_venta = ($this->input->post("id_punto_venta") === FALSE) ? -1 : $this->input->post("id_punto_venta");
    $id_sucursal = ($this->input->post("id_sucursal") === FALSE) ? 0 : $this->input->post("id_sucursal");
    $intervalo = "D";

    $desde = new DateTime(fecha_mysql($f_desde));
    $hasta = new DateTime(fecha_mysql($f_hasta));
    $hasta->add(new DateInterval('P1D'));
    $interval = new DateInterval('P1'.$intervalo);
    $range = new DatePeriod($desde,$interval,$hasta);
    $diff = $hasta->diff($desde)->format("%a");

    $res = array();

    $sql_base = "SELECT ";
    $sql_base.= " F.id_sucursal, F.sucursal, ";
    $sql_base.= " IF(COUNT(*) IS NULL,0,COUNT(*)) AS cantidad, ";
    $sql_base.= " IF(F.total IS NULL,0,SUM(F.total - F.interes)) AS total, ";
    $sql_base.= " IF(F.total IS NULL,0,SUM(F.efectivo - F.vuelto)) AS efectivo, ";
    $sql_base.= " IF(F.costo_final IS NULL,0,SUM(F.costo_final)) AS costo, ";
    $sql_base.= " IF(F.total IS NULL,0,SUM(F.tarjeta)) AS tarjetas, ";
    $sql_base.= " IF(F.total IS NULL,0,SUM(F.interes)) AS intereses ";
    $sql_base.= "FROM facturas F ";
    $sql_base.= "WHERE F.id_empresa = $id_empresa ";
    $sql_base.= "AND F.tipo != 'C' ";
    if ($id_punto_venta != -1) $sql_base.= "AND F.id_punto_venta = $id_punto_venta ";
    if (!empty($id_sucursal)) $sql_base.= "AND F.id_sucursal = $id_sucursal ";
    if ($id_proyecto == 1) {
      $sql_base.= "AND F.anulada = 0 AND F.pendiente = 0 ";
      if ($estado == 0) $sql_base.= "AND F.estado = $estado ";      
    } else {
      $sql_base.= "AND F.id_tipo_estado = 6 "; // Estado finalizado
    }

    // Recorremos cada dia del rango
    foreach($range as $fecha) {
      $sql = $sql_base;
      $sql.= "AND F.fecha = '".$fecha->format("Y-m-d")."' ";
      $sql.= "GROUP BY F.id_sucursal ";
      $q = $this->db->query($sql);
      $rr = $q->row();
      if ($rr->cantidad == 0) {
        continue;
      }
      $rr->fecha = $fecha->format("d/m/Y");
      $res[] = $rr;
    }

    echo json_encode($res);
  }


  function resumen_sucursal() {

    error_reporting(0); // Evita que warnings/deprecations de PHP contaminen la respuesta

    @session_start();
    $id_empresa = $this->get_empresa();
    $estado = isset($_SESSION["estado"]) ? $_SESSION["estado"] : 0;
    $this->load->helper("fecha_helper");
    $f_desde = $this->input->get("desde");
    $f_hasta = $this->input->get("hasta");
    $id_proyecto = $this->input->get("id_proyecto");
    $id_sucursal = ($this->input->get("id_sucursal") === FALSE) ? 0 : $this->input->get("id_sucursal");
    $imprimir = parent::get_get("imprimir",0);
    $intervalo = "D";

    $desde = new DateTime(fecha_mysql($f_desde));
    $hasta = new DateTime(fecha_mysql($f_hasta));
    $hasta->add(new DateInterval('P1D'));
    $interval = new DateInterval('P1'.$intervalo);
    $range = new DatePeriod($desde,$interval,$hasta);
    $diff = $hasta->diff($desde)->format("%a");

    $res = array(
      "ventas"=>array(),
      "venta_anterior"=>0,
      "gastos"=>array(),
      "deuda_proveedores"=>array(),
      "deuda_cheques"=>array(),
      "stock_inicial"=>0,
      "stock_final"=>0,
    );

    // SQL PARA VENTAS
    $sql_base_ventas = "SELECT ";
    $sql_base_ventas.= " F.id_sucursal, F.sucursal, ";
    $sql_base_ventas.= " IF(COUNT(*) IS NULL,0,COUNT(*)) AS cantidad, ";
    $sql_base_ventas.= " IF(F.total IS NULL,0,SUM(F.total - F.interes)) AS total, ";
    $sql_base_ventas.= " IF(F.total IS NULL,0,SUM(F.efectivo - F.vuelto)) AS efectivo, ";
    $sql_base_ventas.= " IF(F.costo_final IS NULL,0,SUM(F.costo_final)) AS costo, ";
    $sql_base_ventas.= " IF(F.total IS NULL,0,SUM(F.tarjeta)) AS tarjetas, ";
    $sql_base_ventas.= " IF(F.total IS NULL,0,SUM(F.interes)) AS intereses ";
    $sql_base_ventas.= "FROM facturas F ";
    $sql_base_ventas.= "WHERE F.id_empresa = $id_empresa ";
    $sql_base_ventas.= "AND F.tipo != 'C' ";
    if (!empty($id_sucursal)) $sql_base_ventas.= "AND F.id_sucursal = $id_sucursal ";
    if ($id_proyecto == 1) {
      $sql_base_ventas.= "AND F.anulada = 0 AND F.pendiente = 0 ";
      if ($estado == 0) $sql_base_ventas.= "AND F.estado = $estado ";      
    } else {
      $sql_base_ventas.= "AND F.id_tipo_estado = 6 "; // Estado finalizado
    }

    // Recorremos cada dia del rango
    foreach($range as $fecha) {

      // Sacamos las ventas
      $sql = $sql_base_ventas;
      $sql.= "AND F.fecha = '".$fecha->format("Y-m-d")."' ";
      $sql.= "GROUP BY F.id_sucursal ";
      $q = $this->db->query($sql);
      if ($q->num_rows() > 0) {
        $rr = $q->row();
      } else {
        $rr = new stdClass();
        $rr->id_sucursal = $id_sucursal;
        $rr->nombre = "";
        $rr->cantidad = 0;
        $rr->total = 0;
        $rr->efectivo = 0;
        $rr->costo = 0;
        $rr->tarjetas = 0;
        $rr->intereses = 0;
      }
      $rr->fecha = $fecha->format("d/m/Y");
      $res["ventas"][] = $rr;
    }

    // Calculamos la venta del año anterior
    $desde_ant = new DateTime(fecha_mysql($f_desde));
    $hasta_ant = new DateTime(fecha_mysql($f_hasta));
    $desde_ant->sub(new DateInterval('P1Y'));
    $hasta_ant->sub(new DateInterval('P1Y'));
    $sql_ant = "SELECT IF(F.total IS NULL,0,SUM(F.total - F.interes)) AS total ";
    $sql_ant.= "FROM facturas F ";
    $sql_ant.= "WHERE F.id_empresa = $id_empresa ";
    $sql_ant.= "AND F.tipo != 'C' ";
    $sql_ant.= "AND F.fecha >= '".$desde_ant->format("Y-m-d")."' ";
    $sql_ant.= "AND F.fecha <= '".$hasta_ant->format("Y-m-d")."' ";
    $sql_ant.= "AND F.id_sucursal = $id_sucursal ";
    if ($id_proyecto == 1) {
      $sql_ant.= "AND F.anulada = 0 AND F.pendiente = 0 ";
      if ($estado == 0) $sql_ant.= "AND F.estado = $estado ";      
    } else {
      $sql_ant.= "AND F.id_tipo_estado = 6 "; // Estado finalizado
    }
    $q_ant = $this->db->query($sql_ant);
    $r_ant = $q_ant->row();
    $res["venta_anterior"] = $r_ant->total;


    // Calculamos las cajas
    $this->load->model("Caja_Movimiento_Model");
    $res["gastos"] = $this->Caja_Movimiento_Model->get_arbol_por_cajas(array(
      "id_padre"=>(($id_empresa == 249) ? 168 : 0), // Solamente los gastos
      "id_empresa"=>$id_empresa,
      "id_sucursal"=>$id_sucursal,
      "desde"=>fecha_mysql($f_desde),
      "hasta"=>fecha_mysql($f_hasta),
      "not_ids_conceptos"=>"1232,1233,1234,1231,1507", // NO INCLUIR: Pago a proveedores y retiros de socios
      "filtrar_cero"=>1,
    ));

    // OTROS INGRESOS QUE NO SEAN POR VENTAS
    $res["ingresos_cajas"] = $this->Caja_Movimiento_Model->get_arbol_por_cajas(array(
      "id_padre"=>(($id_empresa == 249) ? 1228 : 0), // Solamente los INGRESOS
      "id_empresa"=>$id_empresa,
      "id_sucursal"=>$id_sucursal,
      "desde"=>fecha_mysql($f_desde),
      "hasta"=>fecha_mysql($f_hasta),
      "tipo"=>0,
      "not_ids_conceptos"=>"1232,1233,1234,1231,1489,1507", // NO INCLUIR: Pago a proveedores y retiros de socios NI VENTAS POR CAJA
      "filtrar_cero"=>1,
    ));

    // Pago a SOCIOS y AHORRO de CARGAS SOCIALES
    $res["pagos"] = $this->Caja_Movimiento_Model->get_arbol_por_cajas(array(
      "id_padre"=>(($id_empresa == 249) ? 168 : 0), // Solamente los gastos
      "id_empresa"=>$id_empresa,
      "id_sucursal"=>$id_sucursal,
      "desde"=>fecha_mysql($f_desde),
      "hasta"=>fecha_mysql($f_hasta),
      "ids_conceptos"=>"1232,1233,1234,1507",
    ));
    
    // Obtenemos los stock inicial y final
    // A la fecha desde le sacamos un dia, porque en realidad el stock se calcula al cierre del dia anterior
    $desde_stock = new DateTime(fecha_mysql($f_desde));
    $hasta_stock = new DateTime(fecha_mysql($f_hasta));
    $desde_stock->sub(new DateInterval('P1D'));    
    $this->load->model("Stock_Model");
    $stock_desde = $this->Stock_Model->get_historial(array(
      "id_empresa"=>$id_empresa,
      "id_sucursal"=>$id_sucursal,
      "fecha"=>$desde_stock->format("Y-m-d"),
    ));
    if ($stock_desde !== FALSE) {
      $res["stock_inicial"] = $stock_desde->costo_final;
    }
    $stock_hasta = $this->Stock_Model->get_historial(array(
      "id_empresa"=>$id_empresa,
      "id_sucursal"=>$id_sucursal,
      "fecha"=>$hasta_stock->format("Y-m-d"),
    ));
    if ($stock_hasta !== FALSE) {
      $res["stock_final"] = $stock_hasta->costo_final;
    }

    // Ingresos de mercaderia
    $this->load->model("Ingreso_Proveedor_Model");
    $ingresos = $this->Ingreso_Proveedor_Model->buscar(array(
      "id_empresa"=>$id_empresa,
      "id_sucursal"=>$id_sucursal,
      "desde"=>fecha_mysql($f_desde),
      "hasta"=>fecha_mysql($f_hasta),
      "estado"=>1,
      "offset"=>999999,
    ));
    $res["ingresos"] = $ingresos["results"];

    // DEUDA DE PROVEEDORES
    $this->load->model("Proveedor_Model");
    $res["deuda_proveedores"] = $this->Proveedor_Model->listado_deuda(array(
      "id_sucursal"=>$id_sucursal,
      "filtrar_en_cero"=>1,
      "tipo_proveedor"=>1,
      "fecha_desde"=>fecha_mysql($f_hasta),
      "order_by"=>"ultimo_pago",
    ));

    // TOTAL DE PAGOS
    $total_pagos = 0; $cantidad_operaciones = 0; $total_pago_efectivo = 0; $total_pago_transferencias = 0;
    $this->load->model("Cheque_Model");
    $this->load->model("Caja_Model");
    $this->load->model("Orden_Pago_Model");

    // Buscamos las cajas de esa sucursal
    $cajas = $this->Caja_Model->buscar(array(
      "id_empresa"=>$id_empresa,
      "id_sucursal"=>$id_sucursal,
      "buscar_saldo"=>0,
    ))["results"];

    // SUMAMOS LAS VENTAS POR CAJA DE ESA SUCURSAL
    $ventas_por_caja = 0;
    foreach($cajas as $caja) {
      if ($caja->tipo == 0) {
        $ventas_por_caja += $this->Caja_Movimiento_Model->sumar_movimientos(array(
          "id_empresa"=>$id_empresa,
          "tipo"=>0, // Ingresos
          "id_caja"=>$caja->id,
          "id_concepto"=>1489, // VENTAS POR CAJA
          "desde"=>fecha_mysql($f_desde),
          "hasta"=>fecha_mysql($f_hasta),
        ));
      }
    }
    $res["ventas_por_caja"] = $ventas_por_caja;

    if ($desde->format("Y-m-d") >= '2020-05-01') {
      $ordenes_pago = array();

      // Buscamos los proveedores
      $proveedores = $this->Proveedor_Model->buscar(array(
        "tipo_proveedor"=>0, // Por las dudas todos
      ))["results"];

      foreach($proveedores as $prov) {
        $op_efectivo = 0;
        $op_banco = 0;
        foreach($cajas as $caja) {
          $t = $this->Caja_Movimiento_Model->sumar_movimientos(array(
            "id_empresa"=>$id_empresa,
            "tipo"=>1, // Egresos
            "id_caja"=>$caja->id,
            "id_concepto"=>1231,
            "id_proveedor"=>$prov->id,
            "desde"=>$desde->format("Y-m-d"),
            "hasta"=>$hasta->format("Y-m-d"),
          ));
          if ($caja->tipo == 0) $op_efectivo += $t; // Sumamos al efectivo
          else $op_banco += $t; // Sumamos al banco
        }
        $tg = $op_efectivo + $op_banco;
        if ($tg != 0) {
          $o = new stdClass();
          $o->id_proveedor = $prov->id;
          $o->observaciones = "";
          $o->efectivo = $op_efectivo;
          $o->nombre = $prov->nombre;
          $o->total_depositos = $op_banco;
          $o->total_general = $tg;
          $ordenes_pago[] = $o;

          $total_pago_efectivo += (float) $o->efectivo;
          $total_pago_transferencias += (float) $o->total_depositos;          
        }
      }

    } else {
      $ordenes_pago = $this->Orden_Pago_Model->get_list(array(
        "desde"=>fecha_mysql($f_desde),
        "hasta"=>fecha_mysql($f_hasta),
        "id_sucursal"=>$id_sucursal,
        "order"=>"C.fecha DESC ",
        "offset"=>999999,
        "in_tipo_proveedor"=>"1,5",
      ));
      foreach($ordenes_pago as $o) {

        $o->cheques = $this->Cheque_Model->get_by_op($o->id);
        $o->total_cheques = 0;
        if (!empty($o->cheques)) {
          foreach($o->cheques as $c) {
            $o->total_cheques += $c->monto;
          }
        }
        $total_pago_efectivo += (float) $o->efectivo;
        $total_pago_transferencias += (float) $o->total_depositos;
        $cantidad_operaciones++;
      }
    }

    $res["total_pago_efectivo"] = $total_pago_efectivo;
    $res["total_pago_transferencias"] = $total_pago_transferencias;
    $res["cantidad_operaciones"] = $cantidad_operaciones;
    $res["ordenes_pago"] = $ordenes_pago;      

    // El total de pago con cheques no salen de las OP, sino de los cheques cubiertos (cobrados)
    $this->Cheque_Model->buscar(array(
      "id_sucursal"=>$id_sucursal,
      "fecha_comparacion"=>"C.fecha_debitado",
      "desde"=>fecha_mysql($f_desde),
      "hasta"=>fecha_mysql($f_hasta),
      "tipo"=>"P",
      "mostrar_tipo"=>"D", // DEBITADOS
    ));
    $sql = $this->Cheque_Model->get_sql();
    $res["total_pago_cheques"] = $this->Cheque_Model->get_suma();
    $res["total_pagos"] = $res["total_pago_cheques"] + $res["total_pago_efectivo"] + $res["total_pago_transferencias"];


    // DEUDA EN CHEQUES
    $total_deuda_cheques = 0;
    $fecha_desde_cheques = new DateTime(fecha_mysql($f_desde));
    $fecha_hasta_cheques = new DateTime(fecha_mysql($f_hasta));
    $fecha_hasta_cheques->add(new DateInterval('P11M'));    
    $interval = DateInterval::createFromDateString('1 month');
    $period = new DatePeriod($fecha_desde_cheques, $interval, $fecha_hasta_cheques);
    foreach($period as $dt) {
      $this->Cheque_Model->buscar(array(
        "id_sucursal"=>$id_sucursal,
        "fecha_comparacion"=>"C.fecha_cobro",
        "desde"=>$dt->format("Y-m-d"),
        "hasta"=>$dt->format("Y-m-t"), // Ultimo dia de ese mes
        "tipo"=>"P",
        "mostrar_tipo"=>"N", // NO DEBITADOS
      ));
      $sql = $this->Cheque_Model->get_sql();
      $total_cheque = $this->Cheque_Model->get_suma();
      $res["deuda_cheques"][] = array(
        "mes"=>get_mes($dt->format("m")),
        "total"=>$total_cheque,
        "sql"=>$sql,
      );
      $total_deuda_cheques += $total_cheque;
    }
    $res["total_deuda_cheques"] = $total_deuda_cheques;

    // SALDOS BANCARIOS
    $sql = "SELECT * FROM estadisticas_sucursales_valores WHERE id_empresa = $id_empresa AND id_sucursal = $id_sucursal AND fecha = '".fecha_mysql($f_desde)."'";
    $q = $this->db->query($sql);
    if ($q->num_rows() > 0) {
      $r = $q->row();
      $res["banco_inicial"] = $r->monto;
      $res["efectivo_inicial"] = $r->efectivo;
      //$res["cargas_sociales"] = $r->cargas_sociales;
    } else {
      $res["banco_inicial"] = 0;
      $res["efectivo_inicial"] = 0;
      //$res["cargas_sociales"] = 0;
    }    
    $sql = "SELECT * FROM estadisticas_sucursales_valores WHERE id_empresa = $id_empresa AND id_sucursal = $id_sucursal AND fecha = '".fecha_mysql($f_hasta)."'";
    $q = $this->db->query($sql);
    if ($q->num_rows() > 0) {
      $r = $q->row();
      $res["banco_final"] = $r->monto;
      $res["efectivo_final"] = $r->efectivo;
    } else {
      $res["banco_final"] = 0;
      $res["efectivo_final"] = 0;
    }


    // Retiros de socios
    $socio_1_efectivo = 0;
    $socio_2_efectivo = 0;
    $socio_3_efectivo = 0;
    $socio_1_banco = 0;
    $socio_2_banco = 0;
    $socio_3_banco = 0;
    $ahorro_cargas_sociales = 0;
    for($i=0;$i<sizeof($res["pagos"]);$i++) {
      $pago = $res["pagos"][$i];
      if ($pago->id == "1232") {
        for($j=0;$j<sizeof($pago->cajas);$j++) {
          $p = $pago->cajas[$j];
          if ($p["tipo"] == 0) $socio_1_efectivo += $p["total"];  
          else $socio_1_banco += $p["total"];
        }
      } else if ($pago->id == "1233") {
        for($j=0;$j<sizeof($pago->cajas);$j++) {
          $p = $pago->cajas[$j];
          if ($p["tipo"] == 0) $socio_2_efectivo += $p["total"];  
          else $socio_2_banco += $p["total"];
        }
      } else if ($pago->id == "1234") {
        for($j=0;$j<sizeof($pago->cajas);$j++) {
          $p = $pago->cajas[$j];
          if ($p["tipo"] == 0) $socio_3_efectivo += $p["total"];  
          else $socio_3_banco += $p["total"];
        }              
      } else if ($pago->id == "1507") {
        for($j=0;$j<sizeof($pago->cajas);$j++) {
          $p = $pago->cajas[$j];
          $ahorro_cargas_sociales += $p["total"];
        }              
      }
    }
    $res["socio_1_efectivo"] = $socio_1_efectivo;
    $res["socio_2_efectivo"] = $socio_2_efectivo;
    $res["socio_3_efectivo"] = $socio_3_efectivo;
    $res["socio_1_banco"] = $socio_1_banco;
    $res["socio_2_banco"] = $socio_2_banco;
    $res["socio_3_banco"] = $socio_3_banco;
    $res["ahorro_cargas_sociales"] = $ahorro_cargas_sociales;

    /*
    $saldo_efectivo_inicial = 0;
    $saldo_efectivo_final = 0;
    foreach($cajas as $caja) {
      if ($caja->tipo == 0) {
        $saldo_efectivo_inicial = $this->Caja_Movimiento_Model->calcular_saldo(array(
          "id_empresa"=>$id_empresa,
          "id_sucursal"=>$id_sucursal,
          "id_caja"=>$caja->id,
          "desde"=>$desde->format("Y-m-d"),
        ));
        $saldo_efectivo_final = $this->Caja_Movimiento_Model->calcular_saldo(array(
          "id_empresa"=>$id_empresa,
          "id_sucursal"=>$id_sucursal,
          "id_caja"=>$caja->id,
          "desde"=>$hasta->format("Y-m-d"),
        ));
      }
    }
    $res["saldo_efectivo_inicial"] = $saldo_efectivo_inicial;
    $res["saldo_efectivo_final"] = $saldo_efectivo_final;
    */

    if ($imprimir == 1) {

      $this->load->model("Almacen_Model");
      $res["sucursal"] = $this->Almacen_Model->get($id_sucursal);
      $this->load->model("Caja_Model");
      $cajas_gastos = $this->Caja_Model->buscar(array(
        "id_empresa"=>$id_empresa,
        "id_sucursal"=>$id_sucursal,
        "activo"=>1,
      ));
      $res["cajas_gastos"] = $cajas_gastos["results"];

      $res["desde"] = $f_desde;
      $res["hasta"] = $f_hasta;

      $this->load->view("reports/estadisticas/sucursales",$res);
    } else {
      echo json_encode($res);
    }
  }


  // Funcion utilizada para guardar los saldos bancarios en Estadisticas / Sucursales (MEGA)
  function guardar_saldos_bancarios() {

    $id_empresa = parent::get_post("id_empresa",parent::get_empresa());
    $desde = parent::get_post("desde","");
    $hasta = parent::get_post("hasta","");
    $id_sucursal = parent::get_post("id_sucursal",0);
    $banco_inicial = parent::get_post("banco_inicial",0);
    $banco_final = parent::get_post("banco_final",0);
    $efectivo_inicial = parent::get_post("efectivo_inicial",0);
    $efectivo_final = parent::get_post("efectivo_final",0);
    $cargas_sociales_b = parent::get_post("cargas_sociales_b",0);
    $this->load->helper("fecha_helper");
    $desde = fecha_mysql($desde);
    $hasta = fecha_mysql($hasta);

    /*
    // Fecha Desde    
    $sql = "SELECT * FROM estadisticas_sucursales_valores WHERE id_empresa = $id_empresa AND id_sucursal = $id_sucursal AND fecha = '$desde'";
    $q = $this->db->query($sql);
    if ($q->num_rows() > 0) {
      $r = $q->row();
      $sql = "UPDATE estadisticas_sucursales_valores SET monto = '$banco_inicial', efectivo = '$efectivo_inicial', cargas_sociales = '$cargas_sociales_b' WHERE id = $r->id AND id_empresa = $id_empresa AND id_sucursal = $id_sucursal ";
    } else {
      $sql = "INSERT INTO estadisticas_sucursales_valores (id_empresa, id_sucursal, monto, fecha, efectivo, cargas_sociales) VALUES ('$id_empresa', '$id_sucursal', '$banco_inicial', '$desde', '$efectivo_inicial', '$cargas_sociales_b') ";
    }
    $this->db->query($sql);
    */

    // Fecha Hasta
    $sql = "SELECT * FROM estadisticas_sucursales_valores WHERE id_empresa = $id_empresa AND id_sucursal = $id_sucursal AND fecha = '$hasta'";
    $q = $this->db->query($sql);
    if ($q->num_rows() > 0) {
      $r = $q->row();
      $sql = "UPDATE estadisticas_sucursales_valores SET monto = '$banco_final', efectivo = '$efectivo_final' WHERE id = $r->id AND id_empresa = $id_empresa AND id_sucursal = $id_sucursal ";
    } else {
      $sql = "INSERT INTO estadisticas_sucursales_valores (id_empresa, id_sucursal, monto, fecha, efectivo) VALUES ('$id_empresa', '$id_sucursal', '$banco_final', '$hasta', '$efectivo_final') ";
    }
    $this->db->query($sql);

    // La misma fecha hasta se pone como inicial del proximo mes
    $h = new DateTime($hasta);
    $h->add(new DateInterval('P1D'));
    $hasta = $h->format("Y-m-d");
    $sql = "SELECT * FROM estadisticas_sucursales_valores WHERE id_empresa = $id_empresa AND id_sucursal = $id_sucursal AND fecha = '$hasta'";
    $q = $this->db->query($sql);
    if ($q->num_rows() > 0) {
      $r = $q->row();
      $sql = "UPDATE estadisticas_sucursales_valores SET monto = '$banco_final', efectivo = '$efectivo_final' WHERE id = $r->id AND id_empresa = $id_empresa AND id_sucursal = $id_sucursal ";
      $this->db->query($sql);
    } else {
      $sql = "INSERT INTO estadisticas_sucursales_valores (id_empresa, id_sucursal, monto, fecha, efectivo) VALUES ('$id_empresa', '$id_sucursal', '$banco_final', '$hasta', '$efectivo_final') ";
      $this->db->query($sql);
    }

    // EMPEZAMOS LA CAJA CON ESE VALOR
    $sql = "SELECT * FROM cajas WHERE id_empresa = $id_empresa AND tipo = 0 AND id_sucursal = $id_sucursal ";
    $q = $this->db->query($sql);
    $caja = $q->row();
    $sql = "SELECT * FROM cajas_movimientos WHERE id_empresa = $id_empresa AND id_caja = $caja->id AND id_sucursal = $id_sucursal AND tipo = 2 AND fecha = '$hasta 00:00:00' ";
    $q = $this->db->query($sql);
    if ($q->num_rows()>0) {
      $r = $q->row();
      $sql = "UPDATE cajas_movimientos SET monto = $efectivo_final ";
      $sql.= "WHERE id_empresa = $id_empresa AND id_caja = $caja->id AND id_sucursal = $id_sucursal AND tipo = 2 AND id = $r->id ";
      $this->db->query($sql);
    } else {
      $sql = "INSERT INTO cajas_movimientos (id_empresa, id_sucursal, tipo, id_caja, fecha, observaciones, monto) VALUES (";
      $sql.= " '$id_empresa', '$id_sucursal', 2, '$caja->id', '$hasta 00:00:00', 'Saldo inicial del mes', '$efectivo_final') ";
      $this->db->query($sql);
    }

    echo json_encode(array("error"=>0));
  }

  function test22() {
    error_reporting(0); // Evita que warnings/deprecations de PHP contaminen la respuesta
    $id_empresa = 249;
    $fecha = "2020-05-01";
    $sql = "select * from estadisticas_sucursales_valores where fecha = '$fecha' AND id_empresa = $id_empresa ";
    $q = $this->db->query($sql);
    foreach($q->result() as $r) {
      $sql = "SELECT * FROM cajas WHERE id_empresa = $id_empresa AND id_sucursal = $r->id_sucursal AND tipo = 0 LIMIT 0,1 ";
      $qq = $this->db->query($sql);
      $caja = $qq->row();

      $sql = "INSERT INTO cajas_movimientos (id_empresa, id_sucursal, id_caja, tipo, fecha, monto) VALUES (";
      $sql.= " $id_empresa, '$r->id_sucursal', '$caja->id', 2, '$fecha 00:00:01', '$r->efectivo') ";
      $this->db->query($sql);
    }
    echo "TERMINO";
  }


  function cobranzas() {

    @session_start();
    $id_empresa = $this->get_empresa();
    $estado = isset($_SESSION["estado"]) ? $_SESSION["estado"] : 0;
    $this->load->helper("fecha_helper");
    $this->load->model("Cheque_Model");
    $f_desde = fecha_mysql($this->input->post("desde"));
    $f_hasta = fecha_mysql($this->input->post("hasta"));
    $id_sucursal = $this->input->post("id_sucursal",0);
    $id_proyecto = $this->input->post("id_proyecto");

    $this->load->model("Recibo_Model");
    $sql = "SELECT F.*, C.nombre, ";
    $sql.= " DATE_FORMAT(F.fecha,'%d/%m/%Y') AS fecha ";
    $sql.= "FROM facturas F ";
    $sql.= "INNER JOIN clientes C ON (F.id_empresa = C.id_empresa AND F.id_cliente = C.id) ";
    $sql.= "WHERE F.id_empresa = $id_empresa ";
    $sql.= "AND F.tipo = 'P' ";
    $sql.= "AND '$f_desde' <= F.fecha ";
    $sql.= "AND F.fecha <= '$f_hasta' ";
    $sql.= "ORDER BY F.fecha ASC ";
    $q = $this->db->query($sql);
    $pagos = array();
    foreach($q->result() as $r) {
      $recibo = $this->Recibo_Model->get($r->id);
      if ($recibo === FALSE) continue;
      $r->total_depositos = $recibo->total_depositos;
      $r->total_cheques = $recibo->total_cheques;
      $r->total_tarjetas = $recibo->total_tarjetas;
      $pagos[] = $r;
    }

    echo json_encode(array(
      "id_sucursal"=>$id_sucursal,
      "pagos"=>$pagos,
      "fecha_desde"=>fecha_es($f_desde),
      "fecha_hasta"=>fecha_es($f_hasta),
    ));
  }



  function pagos() {

    @session_start();
    $id_empresa = $this->get_empresa();
    $estado = isset($_SESSION["estado"]) ? $_SESSION["estado"] : 0;
    $this->load->helper("fecha_helper");
    $this->load->model("Cheque_Model");
    $f_desde = fecha_mysql($this->input->post("desde"));
    $f_hasta = fecha_mysql($this->input->post("hasta"));
    $id_sucursal = $this->input->post("id_sucursal",0);
    $id_proyecto = $this->input->post("id_proyecto");
    $total_pagos = 0; $cantidad_operaciones = 0;
    $total_efectivo = 0;

    // Ordenes de pago
    $this->load->model("Orden_Pago_Model");
    $ordenes_pago = $this->Orden_Pago_Model->get_list(array(
      "desde"=>$f_desde,
      "hasta"=>$f_hasta,
      "id_sucursal"=>$id_sucursal,
      "order"=>"C.fecha DESC ",
      "offset"=>999999,
      "in_tipo_proveedor"=>"1,5",
    ));
    foreach($ordenes_pago as $o) {
      $o->cheques = $this->Cheque_Model->get_by_op($o->id);
      $o->total_cheques = 0;
      if (!empty($o->cheques)) {
        foreach($o->cheques as $c) {
          $o->total_cheques += $c->monto;
        }
      }
      $total_pagos += (float) $o->total_general;
      $total_efectivo += (float) $o->efectivo;
      $cantidad_operaciones++;
    }

    // Compras realizadas en efectivo
    $this->load->model("Compra_Model");
    $comprobantes_efectivo = $this->Compra_Model->listado(array(
      "desde"=>$f_desde,
      "hasta"=>$f_hasta,
      "id_sucursal"=>$id_sucursal,
      "offset"=>999999,
    ));
    foreach($comprobantes_efectivo["results"] as $o) {
      $total_pagos += (float) $o->total_general;
      $total_efectivo += (float) $o->total_general;
      $cantidad_operaciones++;
    }

    $sql = "SELECT CH.*, ";
    $sql.= " IF(S.nombre IS NULL,'',S.nombre) AS sucursal, ";
    $sql.= " IF(B.nombre IS NULL,'',B.nombre) AS banco, ";
    $sql.= " DATE_FORMAT(CH.fecha_cobro,'%d/%m/%Y') AS fecha_cobro, P.nombre AS proveedor ";
    $sql.= "FROM cheques CH ";
    $sql.= " INNER JOIN compras C ON (CH.id_orden_pago = C.id AND CH.id_empresa = C.id_empresa) ";
    $sql.= " INNER JOIN proveedores P ON (C.id_proveedor = P.id AND CH.id_empresa = P.id_empresa) ";
    $sql.= " LEFT JOIN bancos B ON (CH.id_banco = B.id) ";
    $sql.= " LEFT JOIN almacenes S ON (C.id_sucursal = S.id AND C.id_empresa = S.id_empresa) ";
    $sql.= "WHERE CH.id_empresa = $id_empresa ";
    $sql.= "AND P.tipo_proveedor = 1 "; // Solo los proveedores
    $sql.= "AND '$f_desde' <= CH.fecha_cobro ";
    if ($id_sucursal != 0) $sql.= "AND C.id_sucursal = $id_sucursal ";
    $sql.= "AND CH.fecha_cobro <= '$f_hasta' ";
    $sql.= "AND CH.anulado = 0 ";
    $sql.= "AND CH.devuelto = 0 ";
    $sql.= "AND CH.id_orden_pago != 0 ";
    $sql.= "AND CH.tipo = 'P' ";
    $sql.= "ORDER BY CH.fecha_cobro DESC ";
    $q = $this->db->query($sql);
    $cheques_por_cobrar = $q->result();

    // Total de cheques emitidos
    $this->load->model("Cheque_Model");
    $cheques_emitidos = $this->Cheque_Model->emitidos($f_desde,$f_hasta);

    echo json_encode(array(
      "id_sucursal"=>$id_sucursal,
      "cheques_por_cobrar"=>$cheques_por_cobrar,
      "total_pagos"=>$total_pagos,
      "total_efectivo"=>$total_efectivo,
      "cantidad_operaciones"=>$cantidad_operaciones,
      "ordenes_pago"=>$ordenes_pago,
      "cheques_emitidos"=>$cheques_emitidos,
      "comprobantes_efectivo"=>$comprobantes_efectivo["results"],
      "fecha_desde"=>fecha_es($f_desde),
      "fecha_hasta"=>fecha_es($f_hasta),
    ));

  }

  function whatsapp() {

    @session_start();
    $id_empresa = $this->get_empresa();
    $this->load->helper("fecha_helper");
    $f_desde = $this->input->post("desde");
    $f_hasta = $this->input->post("hasta");
    $id_usuario = $this->input->post("id_usuario");
    $intervalo = "D";

    $grafico = array();
    $total_clicks = 0;
    $cantidad_dias = 0;

    $series = array();
    $desde = new DateTime(fecha_mysql($f_desde));
    $hasta = new DateTime(fecha_mysql($f_hasta));
    $hasta->add(new DateInterval('P1D'));
    $interval = new DateInterval('P1'.$intervalo);
    $range = new DatePeriod($desde,$interval,$hasta);
    $res = array();
    $sql_base = "SELECT IF(COUNT(*) IS NULL,0,COUNT(*)) AS cantidad ";
    $sql_base.= "FROM whatsapp_clicks F ";
    $sql_base.= "WHERE F.id_empresa = $id_empresa ";
    if (!empty($id_usuario)) $sql_base.= "AND F.id_usuario = $id_usuario ";

    // Recorremos cada dia del rango
    foreach($range as $fecha) {
      $sql = $sql_base;
      $from = strtotime($fecha->format("Y-m-d 00:00:00"));
      $to = strtotime($fecha->format("Y-m-d 23:59:59"));
      $sql.= "AND '$from' <= F.stamp AND F.stamp <= '$to' ";
      $q = $this->db->query($sql);
      foreach($q->result() as $rr) {
        $cantidad = (float)$rr->cantidad;
        $total_clicks += $cantidad;
        $res[] = $cantidad;
      }
      $cantidad_dias++;
    }
    // Agregamos la serie
    $series[] = array(
      "name"=>"Interacciones",
      "data"=>$res,
    );
    $grafico[] = array(
      "series"=>$series,
      "desde"=>$desde->format("d/m/Y"),
      "hasta"=>$hasta->format("d/m/Y"),
      "intervalo"=>$intervalo,
    ); 

    // Obtenemos las paginas
    $sql = "SELECT F.pagina AS nombre, IF(COUNT(*) IS NULL,0,COUNT(*)) AS cantidad ";
    $sql.= "FROM whatsapp_clicks F ";
    $sql.= "WHERE F.id_empresa = $id_empresa ";
    $sql.= "AND UNIX_TIMESTAMP('".fecha_mysql($f_desde)." 00:00:00') <= F.stamp AND F.stamp <= UNIX_TIMESTAMP('".fecha_mysql($f_hasta)." 23:59:59') ";
    $sql.= "GROUP BY F.pagina ";
    $sql.= "ORDER BY COUNT(*) DESC ";
    $sql.= "LIMIT 0,10 ";
    $q = $this->db->query($sql);
    $paginas = array();
    foreach($q->result() as $row) {
      $row->porcentaje = ($total_clicks > 0) ? round(($row->cantidad / $total_clicks) * 100,2) : 0;
      $paginas[] = $row;
    }

    $this->load->model("Consulta_Model");
    $consultas_fuera_linea = $this->Consulta_Model->contar(array(
      "id_empresa"=>$id_empresa,
      "in_origenes"=>31,
      "desde"=>$desde->format("d/m/Y"),
      "hasta"=>$hasta->format("d/m/Y"),
    ));

    $promedio_por_dia = ($cantidad_dias > 0) ? ($total_clicks / $cantidad_dias) : 0;

    echo json_encode(array(
      "paginas"=>$paginas,
      "grafico"=>$grafico,
      "id_usuario"=>$id_usuario,
      "total_clicks"=>$total_clicks,
      "consultas_fuera_linea"=>$consultas_fuera_linea,
      "promedio_por_dia"=>$promedio_por_dia,
      "fecha_desde"=>str_replace("-","/",$f_desde),
      "fecha_hasta"=>str_replace("-","/",$f_hasta),
    ));
  }


function ventas() {
  @session_start();
  $id_empresa = $this->get_empresa();
  $estado = isset($_SESSION["estado"]) ? $_SESSION["estado"] : 0;
  $this->load->helper("fecha_helper");

  // Parametro que estamos sumando
  $parametro = $this->input->post("parametro");
  $label = "Totales";
  $campo = "FI.total_con_iva";
  if ($parametro == "N") {
    $campo = "FI.total_sin_iva";
    $label = "Netos";
  } else if ($parametro == "C") {
    $campo = "FI.cantidad";
    $label = "Cantidades";
  }

  // Agrupacion de intervalo de fechas
  // D = Dias; W = Semanas; M = Meses
  $intervalo = "D";//($this->input->post("intervalo") !== FALSE) ? $this->input->post("intervalo") : "D";

  // Indica si hay que comparar
  $comparar = ($this->input->post("comparar") !== FALSE) ? $this->input->post("comparar") : "";

  // Rangos de fechas
  $fechas = $this->input->post("fechas");

  // Filtros
  $rubros = ($this->input->post("rubros") !== FALSE) ? $this->input->post("rubros") : array();
  $articulos = ($this->input->post("articulos") !== FALSE) ? $this->input->post("articulos") : array();
  $vendedores = ($this->input->post("vendedores") !== FALSE) ? $this->input->post("vendedores") : array();
  $clientes = ($this->input->post("clientes") !== FALSE) ? $this->input->post("clientes") : array();
  $proveedores = ($this->input->post("proveedores") !== FALSE) ? $this->input->post("proveedores") : array();

  $in_rubros = array();
  foreach($rubros as $r) { $in_rubros[] = $r["id"]; }
  $in_rubros = (sizeof($rubros)>0) ? implode(",", $in_rubros) : "";

  $in_articulos = array();
  foreach($articulos as $r) { $in_articulos[] = $r["id"]; }
  $in_articulos = (sizeof($articulos)>0) ? implode(",", $in_articulos) : "";

  $in_vendedores = array();
  foreach($vendedores as $r) { $in_vendedores[] = $r["id"]; }
  $in_vendedores = (sizeof($vendedores)>0) ? implode(",", $in_vendedores) : "";

  $in_clientes = array();
  foreach($clientes as $r) { $in_clientes[] = $r["id"]; }
  $in_clientes = (sizeof($clientes)>0) ? implode(",", $in_clientes) : "";

  $in_proveedores = array();
  foreach($proveedores as $r) { $in_proveedores[] = $r["id"]; }
  $in_proveedores = (sizeof($proveedores)>0) ? implode(",", $in_proveedores) : "";

  $salida = array();
  foreach($fechas as $fech) {

    $series = array();
    $desde = new DateTime(fecha_mysql($fech["desde"]));
    $hasta = new DateTime(fecha_mysql($fech["hasta"]));
    $hasta->add(new DateInterval('P1D'));
    $interval = new DateInterval('P1'.$intervalo);
    $range = new DatePeriod($desde,$interval,$hasta);

    // BASE DE LAS CONSULTAS
    /*
    $sql_base = "SELECT IF(SUM($campo) IS NULL,0,SUM($campo)) AS campo ";
    $sql_base.= "FROM facturas_items FI ";
    $sql_base.= "INNER JOIN facturas F ON (FI.id_factura = F.id AND FI.id_empresa = F.id_empresa AND FI.id_punto_venta = F.id_punto_venta) ";
    $sql_base.= "LEFT JOIN articulos A ON (FI.id_articulo = A.id AND FI.id_empresa = A.id_empresa) ";
    $sql_base.= "WHERE F.id_empresa = $id_empresa ";
    $sql_base.= "AND F.anulada = 0 AND F.pendiente = 0 ";
    $sql_base.= "AND F.id_punto_venta != 0 ";
    */

    $sql_base = "SELECT IF(F.total IS NULL,0,SUM(F.total)) AS campo ";
    $sql_base.= "FROM facturas F ";
    $sql_base.= "WHERE F.id_empresa = $id_empresa ";
    $sql_base.= "AND F.anulada = 0 AND F.pendiente = 0 ";
    $sql_base.= "AND F.id_punto_venta != 0 ";
    $sql_base.= "AND F.tipo != 'C' ";
    if ($estado == 0) $sql_base.= "AND F.estado = $estado ";

    if (empty($comparar)) {

      if (!empty($in_rubros)) $sql_base.="AND FI.id_rubro IN ($in_rubros) ";
      if (!empty($in_articulos)) $sql_base.="AND FI.id_articulo IN ($in_articulos) ";
      if (!empty($in_vendedores)) $sql_base.="AND F.id_vendedor IN ($in_vendedores) ";
      if (!empty($in_clientes)) $sql_base.="AND F.id_cliente IN ($in_clientes) ";
        //if (!empty($in_proveedores)) $sql_base.="AND FI.id_rubro IN ($in_proveedores) ";

      $res = array(); $cos = array();
        // Recorremos cada dia del rango
      foreach($range as $fecha) {
        $sql = $sql_base;
        $sql.= "AND F.fecha = '".$fecha->format("Y-m-d")."' ";
        $q = $this->db->query($sql);
        foreach($q->result() as $rr) $res[] = (float)$rr->campo;

        // Costo de la mercaderia vendida
        $sql_costo = "SELECT SUM(FI.cantidad * A.costo_final) AS costo ";
        $sql_costo.= "FROM facturas_items FI ";
        $sql_costo.= "INNER JOIN facturas F ON (FI.id_factura = F.id AND FI.id_empresa = F.id_empresa AND FI.id_punto_venta = F.id_punto_venta) ";
        $sql_costo.= "INNER JOIN articulos A ON (FI.id_articulo = A.id AND FI.id_empresa = A.id_empresa) ";
        $sql_costo.= "WHERE F.id_empresa = $id_empresa ";
        $sql_costo.= "AND F.anulada = 0 AND F.pendiente = 0 ";
        $sql_costo.= "AND F.tipo != 'C' ";
        $sql_costo.= "AND F.id_punto_venta != 0 ";
        $sql_costo.= "AND F.fecha = '".$fecha->format("Y-m-d")."' ";
        $q_costo = $this->db->query($sql_costo);
        if ($q_costo->num_rows() == 0) $cos[] = 0;
        else {
          $costo = $q_costo->row();
          $cos[] = (is_null($costo->costo)) ? 0 : (float)$costo->costo;
        }
      }

      // Agregamos la serie
      $series[] = array(
       "name"=>$label,
       "data"=>$res,
       );
      $series[] = array(
       "name"=>"Costo Mercaderia Vendida",
       "data"=>$cos,
       );
      
    }
    /*

     } else if ($comparar == "rubros") {

            // Agregamos una serie por cada elemento que se compara
       foreach($rubros as $r) {

         $res = array();
         foreach($range as $fecha) {
          $sql = $sql_base;
          if (!empty($in_articulos)) $sql.="AND FI.id_articulo IN ($in_articulos) ";
          if (!empty($in_vendedores)) $sql.="AND F.id_vendedor IN ($in_vendedores) ";
          if (!empty($in_clientes)) $sql.="AND F.id_cliente IN ($in_clientes) ";
              //if (!empty($in_proveedores)) $sql.="AND FI.id_rubro IN ($in_proveedores) ";
          $sql.= "AND FI.id_rubro = ".$r["id"]." ";
          $sql.= "AND F.fecha = '".$fecha->format("Y-m-d")."' ";
          $q = $this->db->query($sql);
          foreach($q->result() as $rr) $res[] = (float)$rr->campo;
        }
        $series[] = array(
          "name"=>$r["label"],
          "data"=>$res,
          );
      }
    } else if ($comparar == "articulos") {

            // Agregamos una serie por cada elemento que se compara
     foreach($articulos as $r) {

       $res = array();
       foreach($range as $fecha) {
        $sql = $sql_base;
        if (!empty($in_rubros)) $sql.="AND FI.id_rubro IN ($in_rubros) ";
        if (!empty($in_vendedores)) $sql.="AND F.id_vendedor IN ($in_vendedores) ";
        if (!empty($in_clientes)) $sql.="AND F.id_cliente IN ($in_clientes) ";
              //if (!empty($in_proveedores)) $sql.="AND FI.id_rubro IN ($in_proveedores) ";
        $sql.= "AND FI.id_articulo = ".$r["id"]." ";
        $sql.= "AND F.fecha = '".$fecha->format("Y-m-d")."' ";
        $q = $this->db->query($sql);
        foreach($q->result() as $rr) $res[] = (float)$rr->campo;
      }
      $series[] = array(
        "name"=>$r["label"],
        "data"=>$res,
        );

    }

    } else if ($comparar == "clientes") {

      // Agregamos una serie por cada elemento que se compara
      foreach($clientes as $r) {

        $res = array();
        foreach($range as $fecha) {
          $sql = $sql_base;
          if (!empty($in_rubros)) $sql.="AND FI.id_rubro IN ($in_rubros) ";
          if (!empty($in_articulos)) $sql.="AND FI.id_articulo IN ($in_articulos) ";
          if (!empty($in_vendedores)) $sql.="AND F.id_vendedor IN ($in_vendedores) ";
          //if (!empty($in_proveedores)) $sql.="AND FI.id_rubro IN ($in_proveedores) ";
          $sql.= "AND F.id_cliente = ".$r["id"]." ";
          $sql.= "AND F.fecha = '".$fecha->format("Y-m-d")."' ";
          $q = $this->db->query($sql);
          foreach($q->result() as $rr) $res[] = (float)$rr->campo;
        }
        $series[] = array(
          "name"=>$r["label"],
          "data"=>$res,
        );
      }

    } else if ($comparar == "proveedores") {

    } else if ($comparar == "vendedores") {

      // Agregamos una serie por cada elemento que se compara
      foreach($vendedores as $r) {
        $res = array();
        foreach($range as $fecha) {
          $sql = $sql_base;
          if (!empty($in_rubros)) $sql.="AND FI.id_rubro IN ($in_rubros) ";
          if (!empty($in_articulos)) $sql.="AND FI.id_articulo IN ($in_articulos) ";
          if (!empty($in_clientes)) $sql.="AND F.id_cliente IN ($in_clientes) ";
          //if (!empty($in_proveedores)) $sql.="AND FI.id_rubro IN ($in_proveedores) ";
          $sql.= "AND F.id_vendedor = ".$r["id"]." ";
          $sql.= "AND F.fecha = '".$fecha->format("Y-m-d")."' ";
          $q = $this->db->query($sql);
          foreach($q->result() as $rr) $res[] = (float)$rr->campo;
        }
        $series[] = array(
          "name"=>$r["label"],
          "data"=>$res,
        );
      }
    }
    */

    $salida[] = array(
      "series"=>$series,
      "desde"=>$desde->format("d/m/Y"),
      "hasta"=>$hasta->format("d/m/Y"),
      "intervalo"=>$intervalo,
      );
  }

  echo json_encode(array(
    "results"=>$salida,
    ));
}


  function test() {

    error_reporting(0); // Evita que warnings/deprecations de PHP contaminen la respuesta
    
    @session_start();
    $id_empresa = $this->get_empresa();
    $this->load->helper("fecha_helper");
    $desde = isset($fecha_desde) ? new DateTime(fecha_mysql($fecha_desde)) : new DateTime("-1 month");
    $hasta = isset($fecha_hasta) ? new DateTime(fecha_mysql($fecha_hasta)) : new DateTime();

    $this->load->model("Web_Configuracion_Model");
    $conf = $this->Web_Configuracion_Model->get($id_empresa);

    if (!empty($conf->view_id)) {

      set_include_path(get_include_path().PATH_SEPARATOR.APPPATH.'libraries/Google/');
      require APPPATH.'libraries/Google/Client.php';
      require APPPATH.'libraries/Google/Service/Analytics.php';

      $client_id = '785533219608-9f9ncmps76g85amiipji77k48r9kul3q.apps.googleusercontent.com'; //Client ID
      $service_account_name = '785533219608-9f9ncmps76g85amiipji77k48r9kul3q@developer.gserviceaccount.com'; //Email Address 
      $key_file_location = APPPATH.'libraries/Google/key.p12';
      
      $client = new Google_Client();
      $client->setApplicationName("Client_Library_Examples");
      $service = new Google_Service_Analytics($client);
      
      if (isset($_SESSION['service_token'])) {
        $client->setAccessToken($_SESSION['service_token']);
      }
      $key = file_get_contents($key_file_location);
      $cred = new Google_Auth_AssertionCredentials(
        $service_account_name,
        array('https://www.googleapis.com/auth/analytics.readonly'),
        $key
      );
      $client->setAssertionCredentials($cred);
      if($client->getAuth()->isAccessTokenExpired()) {
        $client->getAuth()->refreshTokenWithAssertion($cred);
      }
      $_SESSION['service_token'] = $client->getAccessToken();
      
      $view_id = "ga:".$conf->view_id;
      $fecha_desde = $desde->format("Y-m-d");
      $fecha_hasta = $hasta->format("Y-m-d");
      
      try {

        // SESIONES
        $results = $service->data_ga->get($view_id,$fecha_desde,$fecha_hasta,'ga:totalEvents',array(
          //'filters'=>"ga:eventValue==114",
        ));
        print_r($results);
      } catch(Exception $e) {
        print_r($e);
      }
    }
  }

  function consultas() {
    error_reporting(0); // Evita que warnings/deprecations de PHP contaminen la respuesta JSON
    $id_empresa = $this->get_empresa();
    $this->load->model("Consulta_Model");
    $this->load->helper("fecha_helper");
    $fecha_desde = parent::get_get("desde","");
    $fecha_hasta = parent::get_get("hasta","");
    $id_usuario = parent::get_get("id_usuario",0);
    $desde = (!empty($fecha_desde)) ? new DateTime(fecha_mysql($fecha_desde)) : new DateTime("-1 month");
    $hasta = (!empty($fecha_hasta)) ? new DateTime(fecha_mysql($fecha_hasta)) : new DateTime();
    $intervalo = "D";

    $clientes_unicos = array();
    $consultas = array();
    $hasta->add(new DateInterval('P1D'));
    $interval = new DateInterval('P1'.$intervalo);
    $range = new DatePeriod($desde,$interval,$hasta);
    $total_clientes_unicos = 0;
    $total_consultas = 0;

    // Recorremos las fechas y contamos las consultas
    foreach($range as $fecha) {

      // Contamos la cantidad de clientes unicos
      $r = $this->Consulta_Model->contar(array(
        "clientes_unicos"=>1, // Cuenta la cantidad de clientes unicos (no cuenta si el mismo cliente consulto 2 veces)
        "not_ids_origen"=>"20,18,32,13,17,19,21,22", // Que sean consultas reales de usuarios
        "tipo"=>0, // Entrada
        "fecha"=>$fecha->format("Y-m-d"),
        "id_empresa"=>$id_empresa,
        "id_usuario"=>$id_usuario,
      ));
      $total_clientes_unicos += (int)$r;
      $clientes_unicos[] = (int)$r;

      // Contamos la cantidad de consultas
      $r = $this->Consulta_Model->contar(array(
        "clientes_unicos"=>0,
        "not_ids_origen"=>"20,18,32,13,17,19,21,22", // Que sean consultas reales de usuarios
        "tipo"=>0, // Entrada
        "fecha"=>$fecha->format("Y-m-d"),
        "id_empresa"=>$id_empresa,
        "id_usuario"=>$id_usuario,
      ));
      $total_consultas += (int)$r;
      $consultas[] = (int)$r;
    }

    $grafico_por_origen_whatsapp = $this->Consulta_Model->contar(array(
      "clientes_unicos"=>1, // Cuenta la cantidad de clientes unicos (no cuenta si el mismo cliente consulto 2 veces)
      "not_ids_origen"=>"20,18,32,13,17,19,21,22", // Que sean consultas reales de usuarios
      "in_origenes"=>$this->Consulta_Model->get_origenes_consultas_whatsapp(),
      "tipo"=>0, // Entrada
      "desde"=>fecha_mysql($fecha_desde),
      "hasta"=>fecha_mysql($fecha_hasta),
      "id_empresa"=>$id_empresa,
      "id_usuario"=>$id_usuario,
    ));

    $grafico_por_origen_web = $this->Consulta_Model->contar(array(
      "clientes_unicos"=>1, // Cuenta la cantidad de clientes unicos (no cuenta si el mismo cliente consulto 2 veces)
      "not_ids_origen"=>"20,18,32,13,17,19,21,22", // Que sean consultas reales de usuarios
      "in_origenes"=>$this->Consulta_Model->get_origenes_consultas_web(),
      "tipo"=>0, // Entrada
      "desde"=>fecha_mysql($fecha_desde),
      "hasta"=>fecha_mysql($fecha_hasta),
      "id_empresa"=>$id_empresa,
      "id_usuario"=>$id_usuario,
    ));

    $grafico_por_origen_manual = $this->Consulta_Model->contar(array(
      "clientes_unicos"=>1, // Cuenta la cantidad de clientes unicos (no cuenta si el mismo cliente consulto 2 veces)
      "not_ids_origen"=>"20,18,32,13,17,19,21,22", // Que sean consultas reales de usuarios
      "in_origenes"=>$this->Consulta_Model->get_origenes_consultas_manuales(),
      "tipo"=>0, // Entrada
      "desde"=>fecha_mysql($fecha_desde),
      "hasta"=>fecha_mysql($fecha_hasta),
      "id_empresa"=>$id_empresa,
      "id_usuario"=>$id_usuario,
    ));

    $grafico_por_origen_turno = $this->Consulta_Model->contar(array(
      "clientes_unicos"=>1, // Cuenta la cantidad de clientes unicos (no cuenta si el mismo cliente consulto 2 veces)
      "not_ids_origen"=>"20,18,32,13,17,19,21,22", // Que sean consultas reales de usuarios
      "in_origenes"=>$this->Consulta_Model->get_origenes_turnos(),
      "tipo"=>0, // Entrada
      "desde"=>fecha_mysql($fecha_desde),
      "hasta"=>fecha_mysql($fecha_hasta),
      "id_empresa"=>$id_empresa,
      "id_usuario"=>$id_usuario,
    ));

    $total_referencia = $this->Consulta_Model->contar(array(
      "referencia_unica"=>1,
      "not_ids_origen"=>"20,18,32,13,17,19,21,22", // Que sean consultas reales de usuarios
      "tipo"=>0, // Entrada
      "desde"=>fecha_mysql($fecha_desde),
      "hasta"=>fecha_mysql($fecha_hasta),
      "id_empresa"=>$id_empresa,
      "id_usuario"=>$id_usuario,
    ));

    // Consultamos los estados y sacamos las cantidades a cada uno
    $this->load->model("Consulta_Tipo_Model");
    $grafico_estado = array();
    $sql = "SELECT R.* ";
    $sql.= "FROM crm_consultas_tipos R ";
    $sql.= "WHERE R.id_empresa = $id_empresa ";
    $q = $this->db->query($sql);
    foreach($q->result() as $estado) {
      $e = new stdClass();
      $e->id = $estado->id;
      $e->nombre = $estado->nombre;
      $e->color = $this->Consulta_Tipo_Model->traducir_color($estado->color);
      $e->cantidad = $this->Consulta_Model->contar(array(
        "clientes_unicos"=>1, // Cuenta la cantidad de clientes unicos (no cuenta si el mismo cliente consulto 2 veces)
        "not_ids_origen"=>"20,18,32,13,17,19,21,22", // Que sean consultas reales de usuarios
        "tipo"=>0, // Entrada
        "tipo_estado"=>$estado->id,
        "desde"=>fecha_mysql($fecha_desde),
        "hasta"=>fecha_mysql($fecha_hasta),
        "id_empresa"=>$id_empresa,
        "id_usuario"=>$id_usuario,
      ));
      $e->cantidad = intval($e->cantidad);
      $grafico_estado[] = $e;
    }

    $grafico_usuarios = array();
    $sql = "SELECT * FROM com_usuarios WHERE id_empresa = $id_empresa ORDER BY nombre ASC ";
    $q = $this->db->query($sql);
    foreach($q->result() as $usuario) {
      $e = new stdClass();
      $e->id = $usuario->id;
      $e->nombre = $usuario->nombre;
      $e->cantidad = $this->Consulta_Model->contar(array(
        "clientes_unicos"=>1, // Cuenta la cantidad de clientes unicos (no cuenta si el mismo cliente consulto 2 veces)
        "not_ids_origen"=>"20,18,32,13,17,19,21,22", // Que sean consultas reales de usuarios
        "tipo"=>0, // Entrada
        "id_usuario"=>$usuario->id,
        "desde"=>fecha_mysql($fecha_desde),
        "hasta"=>fecha_mysql($fecha_hasta),
        "id_empresa"=>$id_empresa,
        "id_usuario"=>$id_usuario,
      ));
      $e->cantidad = intval($e->cantidad);
      $grafico_usuarios[] = $e;
    }

    // Enviamos los datos
    if ($id_empresa == 1319) {
      // Entrena y mas no tiene clientes unicos
      $grafico = array(
        array(
          "name"=>"Consultas",
          "data"=>$consultas,
        ),
      );
    } else {
      $grafico = array(
        array(
          "name"=>"Clientes Unicos",
          "data"=>$clientes_unicos,
        ),
        array(
          "name"=>"Consultas",
          "data"=>$consultas,
        ),
      );
    }


    $f_desde = fecha_mysql($fecha_desde);
    $f_hasta = fecha_mysql($fecha_hasta);

    $redes_sociales = array();
    $cantidad_visitas_redes_sociales = 0;

    $sql = "SELECT COUNT(*) as cantidad, CR.red ";
    $sql.= "FROM click_redes CR ";
    $sql.= "WHERE CR.id_empresa = '$id_empresa' ";
    if (!empty($id_usuario)) $sql.= "AND CR.id_usuario = '$id_usuario' ";
    $sql.= "AND CR.fecha >= '$f_desde' AND CR.fecha <= '$f_hasta' ";
    $sql.= "GROUP BY CR.red ";

    $q = $this->db->query($sql);
    
    foreach($q->result() as $cr) {
      $redes_sociales[] = array(
        "nombre"=>$cr->red,
        "cantidad"=>$cr->cantidad,
      );

      $cantidad_visitas_redes_sociales += intval($cr->cantidad);
    }



    echo json_encode(array(
      "total_clientes_unicos"=>$total_clientes_unicos,
      "total_consultas"=>$total_consultas,
      "total_referencia"=>intval($total_referencia),
      "grafico_estado"=>$grafico_estado,
      "grafico_usuarios"=>$grafico_usuarios,
      "grafico"=>$grafico,
      "grafico_por_origen_whatsapp"=>intval($grafico_por_origen_whatsapp),
      "grafico_por_origen_web"=>intval($grafico_por_origen_web),
      "grafico_por_origen_manual"=>intval($grafico_por_origen_manual),
      "grafico_por_origen_turno"=>intval($grafico_por_origen_turno),
      "redes_sociales"=>$redes_sociales,
      "cantidad_visitas_redes_sociales"=>intval($cantidad_visitas_redes_sociales),
      "desde_anio"=>intval($desde->format("Y")),
      "desde_mes"=>intval($desde->format("m"))-1,
      "desde_dia"=>intval($desde->format("d")),
    ));
  }

  function web($fecha_desde = "",$fecha_hasta = "") {
    /*
    error_reporting(0); // Evita que warnings/deprecations de PHP contaminen la respuesta
    */
    @session_start();
    $id_empresa = $this->get_empresa();
    $this->load->helper("fecha_helper");
    $salida = array(
     "total_sesiones"=>0,
     "total_usuarios"=>0,
     "total_usuarios_nuevos"=> 0,
     "total_usuarios_recurrentes"=> 0,
     "paginas_vistas"=> 0,
     "porcentaje_rebote"=> 0,
     "desktop"=> 0,
     "mobile"=> 0,
     "tablet"=> 0,
     "error"=> "",
     "ciudades"=>array(),
     "fuentes"=>array(),
     "paginas_mas_vistas"=>array(),
     "usuarios"=>array(),
     "sesiones"=>array(),
     "usuarios_nuevos"=>array(),
     "usuarios_recurrentes"=>array(),
    );
    $desde = isset($fecha_desde) ? new DateTime(fecha_mysql($fecha_desde)) : new DateTime("-1 month");
    $hasta = isset($fecha_hasta) ? new DateTime(fecha_mysql($fecha_hasta)) : new DateTime();

    $this->load->model("Web_Configuracion_Model");
    $conf = $this->Web_Configuracion_Model->get($id_empresa);

    if (isset($conf->estadisticas_factor)) {
      $factor = ($conf->estadisticas_factor + 100) / 100;
    } else $factor = 1;

    if (!empty($conf->view_id)) {

      set_include_path(get_include_path().PATH_SEPARATOR.APPPATH.'libraries/Google/');
      require APPPATH.'libraries/Google/Client.php';
      require APPPATH.'libraries/Google/Service/Analytics.php';

      if ($id_empresa == 730 || $id_empresa == 225) {
        $service_account_name = 'analytics@api-project-785533219608.iam.gserviceaccount.com';
        $key_file_location = APPPATH.'libraries/Google/key2.p12';
      } else {
        $service_account_name = '785533219608-9f9ncmps76g85amiipji77k48r9kul3q@developer.gserviceaccount.com'; //Email Address 
        $key_file_location = APPPATH.'libraries/Google/key.p12';
      }

      $client = new Google_Client();
      $client->setApplicationName("Client_Library_Examples");
      $service = new Google_Service_Analytics($client);
      
      if (isset($_SESSION['service_token'])) {
        $client->setAccessToken($_SESSION['service_token']);
      }
      $key = file_get_contents($key_file_location);
      $cred = new Google_Auth_AssertionCredentials(
        $service_account_name,
        array('https://www.googleapis.com/auth/analytics.readonly'),
        $key
      );
      $client->setAssertionCredentials($cred);
      if($client->getAuth()->isAccessTokenExpired()) {
        $client->getAuth()->refreshTokenWithAssertion($cred);
      }
      $_SESSION['service_token'] = $client->getAccessToken();
      
      $view_id = "ga:".$conf->view_id;
      $fecha_desde = $desde->format("Y-m-d");
      $fecha_hasta = $hasta->format("Y-m-d");

      try {

        // SESIONES
        $results = $service->data_ga->get($view_id,$fecha_desde,$fecha_hasta,'ga:sessions');
        if (count($results->getRows()) > 0) {
          $rows = $results->getRows();
          $salida["total_sesiones"] = round($rows[0][0] * $factor,0);
        }
        
        // PAGINAS VISTAS
        $results = $service->data_ga->get($view_id,$fecha_desde,$fecha_hasta,'ga:pageviews');
        if (count($results->getRows()) > 0) {
          $rows = $results->getRows();
          $salida["paginas_vistas"] = round($rows[0][0] * $factor,0);
        }
        
        // PORCENTAJE DE REBOTE
        $results = $service->data_ga->get($view_id,$fecha_desde,$fecha_hasta,'ga:bounceRate');
        if (count($results->getRows()) > 0) {
          $rows = $results->getRows();
          $salida["porcentaje_rebote"] = number_format($rows[0][0],2);
        }
        
        // USUARIOS Y SESIONES ENTRE LAS FECHAS
        // PARA GENERAR EL GRAFICO DE VISION GENERAL
        $results = $service->data_ga->get($view_id,$fecha_desde,$fecha_hasta,'ga:users,ga:sessions',array(
          "dimensions" => "ga:date"
          ));
        if (count($results->getRows()) > 0) {
          foreach ($results->getRows() as $r) {
            $salida["usuarios"][] = round((int) $r[1] * $factor,0);
            $salida["sesiones"][] = round((int) $r[2] * $factor,0);
          }
        }
        
        // USUARIOS NUEVOS VS USUARIOS RECURRENTES
        $results = $service->data_ga->get($view_id,$fecha_desde,$fecha_hasta,'ga:newUsers,ga:users',array(
          "dimensions" => "ga:date"
          ));
        if (count($results->getRows()) > 0) {
          foreach ($results->getRows() as $r) {
            $salida["usuarios_nuevos"][] = round((int) ($r[1] * $factor),0);
            $salida["total_usuarios_nuevos"] += round((int) ($r[1] * $factor),0);
            $salida["usuarios_recurrentes"][] = round(((int) $r[2] - (int) $r[1]) * $factor,0);
            $salida["total_usuarios_recurrentes"] += round(((int) $r[2] - (int) $r[1]) * $factor,0);
          }
        }
        
        // CIUDADES
        if (!(($id_empresa == 256 || $id_empresa == 257 || $id_empresa == 403 || $id_empresa == 448 || $id_empresa == 493))) { 
          $results = $service->data_ga->get($view_id,$fecha_desde,$fecha_hasta,'ga:sessions',array(
            "sort" => "-ga:sessions",
            "max-results" => 9,
            "dimensions" => "ga:city"
            ));
          if (count($results->getRows()) > 0) {
            foreach ($results->getRows() as $r) {
              $o = new stdClass();
              $o->nombre = ($r[0] != "(not set)") ? $r[0] : "No definida";
              $o->cantidad = round($r[1] * $factor,0);
              if (!empty($salida["total_sesiones"])) {
                $o->porcentaje = number_format(($o->cantidad / $salida["total_sesiones"])*100,0);
              } else {
                $o->porcentaje = 0;
              }
              $salida["ciudades"][] = $o;
            }
          }
        } else {
          // PAISES
          $results = $service->data_ga->get($view_id,$fecha_desde,$fecha_hasta,'ga:sessions',array(
            "sort" => "-ga:sessions",
            "max-results" => 9,
            "dimensions" => "ga:country"
            ));
          if (count($results->getRows()) > 0) {
            foreach ($results->getRows() as $r) {
              $o = new stdClass();
              $o->nombre = ($r[0] != "(not set)") ? $r[0] : "No definida";
              $o->cantidad = round($r[1] * $factor,0);
              if (!empty($salida["total_sesiones"])) {
                $o->porcentaje = number_format(($o->cantidad / $salida["total_sesiones"])*100,0);
              } else {
                $o->porcentaje = 0;
              }
              $salida["ciudades"][] = $o;
            }
          }          
        }
        
        // ORIGENES
        $results = $service->data_ga->get($view_id,$fecha_desde,$fecha_hasta,'ga:sessions',array(
          "sort" => "-ga:sessions",
          "max-results" => 9,
          "dimensions" => "ga:source"
          ));
        if (count($results->getRows()) > 0) {
          foreach ($results->getRows() as $r) {
            $o = new stdClass();
            $o->nombre = ($r[0] != "(direct)") ? $r[0] : "Directo";
            $o->cantidad = round($r[1] * $factor,0);
            if (!empty($salida["total_sesiones"])) {
              $o->porcentaje = number_format(($o->cantidad / $salida["total_sesiones"])*100,0);
            } else {
              $o->porcentaje = 0;
            }
            $salida["fuentes"][] = $o;
          }
        }
        
        // PAGINAS MAS VISTAS
        $results = $service->data_ga->get($view_id,$fecha_desde,$fecha_hasta,'ga:pageviews',array(
          "sort" => "-ga:pageviews",
          "max-results" => 9,
          "dimensions" => "ga:pagePath"
          ));
        if (count($results->getRows()) > 0) {
          foreach ($results->getRows() as $r) {
            $o = new stdClass();
            $o->nombre = ($r[0] != "(direct)") ? $r[0] : "Directo";
            $o->cantidad = round($r[1] * $factor,0);
            if (!empty($salida["paginas_vistas"])) {
              $o->porcentaje = number_format(($o->cantidad / $salida["paginas_vistas"])*100,0);
            } else {
              $o->porcentaje = 0;
            }
            $salida["paginas_mas_vistas"][] = $o;
          }
        }        
        
        // DISPOSITIVOS
        $results = $service->data_ga->get($view_id,$fecha_desde,$fecha_hasta,'ga:users',array(
          "dimensions" => "ga:deviceCategory"
        ));
        if (count($results->getRows()) > 0) {
          foreach ($results->getRows() as $r) {
            if ($r[0] == "desktop") $salida["desktop"] = round((int)($r[1] * $factor),0);
            if ($r[0] == "mobile") $salida["mobile"] = round((int)($r[1] * $factor),0);
            if ($r[0] == "tablet") $salida["tablet"] = round((int)($r[1] * $factor),0);
          }
        }

        // TOTAL DE USUARIOS
        $results = $service->data_ga->get($view_id,$fecha_desde,$fecha_hasta,'ga:users');
        if (count($results->getRows()) > 0) {
          $rows = $results->getRows();
          $salida["total_usuarios"] = round($rows[0][0] * $factor,0);
        }

      } catch(Exception $e) {
        $salida["error"] = $e;
      }  
    } else {
      $salida["error"] = "No tiene configurado el VIEW ID";
    }
    echo json_encode($salida);
  }
  
  
  function articulos_totales($codigo = 0, $desde = 0, $hasta = 0, $id_vendedor = 0) {

    $id_empresa = $_SESSION["id_empresa"];
    $estado = $_SESSION["estado"];
    $this->load->helper("fecha_helper");
    $desde = fecha_mysql(str_replace("-","/",$desde));
    $hasta = fecha_mysql(str_replace("-","/",$hasta));

    $limit = $this->input->get("limit");
    $offset = $this->input->get("offset");
    $filter = $this->input->get("filter");
    $order_by = $this->input->get("order_by");
    $order = $this->input->get("order");    

    $sql = "SELECT A.codigo, A.descripcion, ";
    $sql.= " SUM(FI.cantidad) AS cantidad, ";
    $sql.= " COUNT(DISTINCT F.id) AS cantidad_ventas, ";
    $sql.= " SUM(IF(FI.tipo_cantidad = 'B',FI.cantidad,0)) AS bonificado, ";
    $sql.= " SUM(IF(FI.tipo_cantidad = 'D',FI.cantidad,0)) AS recambio, ";
    $sql.= " SUM(FI.total_con_iva) AS total ";
    $sql.= "FROM facturas F ";
    $sql.= "INNER JOIN facturas_items FI ON (F.id = FI.id_factura) ";
    $sql.= "INNER JOIN articulos A ON (FI.id_articulo = A.id) ";
    $sql.= "WHERE F.anulada = 0 ";
    $sql.= "AND F.tipo != 'C' ";
    if (!empty($codigo)) $sql.= "AND A.codigo = $codigo ";
    $sql.= "AND F.id_tipo_comprobante != 0 ";
    if ($estado == 0) $sql.= "AND F.tipo != 'R' ";        
    $sql.= "AND F.fecha >= '$desde' AND F.fecha <= '$hasta' ";
    if (!empty($id_vendedor)) $sql.= "AND F.id_vendedor = $id_vendedor ";
    if (!empty($id_empresa)) $sql.= "AND F.id_empresa = $id_empresa ";
    $sql.= "GROUP BY A.id ";
    $sql.= "ORDER BY $order_by $order";
    //echo $sql; exit();
    $q = $this->db->query($sql);

    echo json_encode(array(
      "results" => $q->result(),
      "total"=>sizeof($q->result())
      ));
  }

  function ventas_por_proveedor() {
    $id_empresa = parent::get_empresa();
    $desde = parent::get_get("desde",date("d-m-Y"));
    $hasta = parent::get_get("hasta",date("d-m-Y"));
    $id_sucursal = parent::get_get("id_sucursal",0);
    $limit = parent::get_get("limit",0);
    $offset = parent::get_get("offset",20);
    $order = parent::get_get("order","total_final");
    $order_by = parent::get_get("order_by","DESC");

    $this->load->helper("fecha_helper");
    $desde = fecha_mysql(str_replace("-","/",$desde));
    $hasta = fecha_mysql(str_replace("-","/",$hasta));

    $sql = "SELECT SQL_CALC_FOUND_ROWS ";
    $sql.= " FI.id_proveedor, ";
    $sql.= " IF(P.codigo IS NULL,'',P.codigo) AS codigo, ";
    $sql.= " IF(P.nombre IS NULL,'Sin definir',P.nombre) AS proveedor, ";
    $sql.= " IF(SUM(FI.cantidad) IS NULL,0,SUM(FI.cantidad)) AS cantidad, ";
    $sql.= " IF(SUM(FI.total_con_iva) IS NULL,0,SUM(FI.total_con_iva)) AS total_final, ";
    $sql.= " IF(SUM(FI.costo_final) IS NULL,0,SUM(FI.costo_final)) AS costo_final ";
    $sql.= "FROM facturas F ";
    $sql.= "INNER JOIN facturas_items FI ON (FI.id_empresa = F.id_empresa AND FI.id_punto_venta = F.id_punto_venta AND F.id = FI.id_factura) ";
    $sql.= "LEFT JOIN proveedores P ON (FI.id_proveedor = P.id AND FI.id_empresa = P.id_empresa) ";
    $sql.= "WHERE F.id_empresa = $id_empresa ";
    $sql.= "AND FI.anulado = 0 ";
    $sql.= "AND F.anulada = 0 ";
    $sql.= "AND F.tipo != 'C' ";
    $sql.= "AND F.fecha >= '$desde' AND F.fecha <= '$hasta' ";
    if (!empty($id_sucursal)) $sql.= "AND F.id_sucursal = $id_sucursal ";
    $sql.= "GROUP BY FI.id_proveedor ";
    $sql.= "ORDER BY $order $order_by ";
    $sql.= "LIMIT $limit, $offset ";
    $q = $this->db->query($sql);

    $q_total = $this->db->query("SELECT FOUND_ROWS() AS total");
    $total = $q_total->row();

    $total_final = 0;
    $costo_final = 0;
    $cantidad = 0;
    $salida = array();
    foreach($q->result() as $r) {
      $total_final += (float) $r->total_final;
      $cantidad += (float) $r->cantidad;
      $costo_final += (float) $r->costo_final;
      $salida[] = $r;
    }

    echo json_encode(array(
      "results"=>$salida,
      "total"=>$total->total,
      "sql"=>$sql,
      "meta"=>array(
        "total_final"=>$total_final,
        "costo_final"=>$costo_final,
        "cantidad"=>$cantidad,
      )
    ));
  }

  function ventas_por_departamento() {

    $id_empresa = parent::get_empresa();
    $desde = parent::get_get("desde",date("d-m-Y"));
    $hasta = parent::get_get("hasta",date("d-m-Y"));
    $id_sucursal = parent::get_get("id_sucursal",0);
    $order = parent::get_get("order","departamento");
    $order_by = parent::get_get("order_by","ASC");

    $this->load->helper("fecha_helper");
    $desde = fecha_mysql(str_replace("-","/",$desde));
    $hasta = fecha_mysql(str_replace("-","/",$hasta));

    // Sumamos la venta por departamento
    $sql = "SELECT A.id_departamento, ";
    $sql.= " IF (DC.nombre IS NULL,'No Definido',DC.nombre) AS departamento, ";
    $sql.= " IF(SUM(FI.total_con_iva) IS NULL,0,SUM(FI.total_con_iva)) AS total_final, ";
    $sql.= " IF(SUM(FI.cantidad) IS NULL,0,SUM(FI.cantidad)) AS cantidad ";
    $sql.= "FROM facturas F ";
    $sql.= "INNER JOIN facturas_items FI ON (F.id = FI.id_factura AND F.id_punto_venta = FI.id_punto_venta AND F.id_empresa = FI.id_empresa) ";
    $sql.= "LEFT JOIN articulos A ON (FI.id_articulo = A.id AND FI.id_empresa = A.id_empresa) ";
    $sql.= "LEFT JOIN departamentos_comerciales DC ON (A.id_departamento = DC.id AND A.id_empresa = DC.id_empresa) ";
    $sql.= "WHERE F.id_empresa = $id_empresa ";
    $sql.= "AND FI.anulado = 0 ";
    $sql.= "AND F.anulada = 0 ";
    $sql.= "AND F.tipo != 'C' ";
    $sql.= "AND F.fecha >= '$desde' AND F.fecha <= '$hasta' ";
    if (!empty($id_sucursal)) $sql.= "AND F.id_sucursal = $id_sucursal ";
    $sql.= "GROUP BY A.id_departamento ";
    //$sql.= "ORDER BY $order $order_by ";
    $q = $this->db->query($sql);

    $q_total = $this->db->query("SELECT FOUND_ROWS() AS total");
    $total = $q_total->row();

    $total_final = 0;
    $cantidad = 0;
    $salida = array();
    foreach($q->result() as $r) {
      $total_final += (float) $r->total_final;
      $cantidad += (float) $r->cantidad;
      $salida[] = $r;
    }
    foreach($salida as $r) { 
      $r->porcentaje = ($total_final > 0) ? ($r->total_final / $total_final * 100) : 0;
    }

    usort($salida,function($a,$b) use ($order,$order_by) {
       if ($order == "asc") {
        return $a->{$order_by} > $b->{$order_by};
      } else {
        return $a->{$order_by} < $b->{$order_by};
      }
    });

    echo json_encode(array(
      "results"=>$salida,
      "total"=>$total->total,
      "sql"=>$sql,
      "meta"=>array(
        "total_final"=>$total_final,
        "cantidad"=>$cantidad,
      )
    ));
  }


  function ventas_por_sucursal() {

    error_reporting(0); // Evita que warnings/deprecations de PHP contaminen la respuesta

    $id_empresa = parent::get_empresa();
    $desde = parent::get_get("desde",date("d-m-Y"));
    $hasta = parent::get_get("hasta",date("d-m-Y"));
    $order = parent::get_get("order","departamento");
    $order_by = parent::get_get("order_by","ASC");
    $impresion = parent::get_get("impresion",0);

    $this->load->helper("fecha_helper");
    $desde = fecha_mysql(str_replace("-","/",$desde));
    $hasta = fecha_mysql(str_replace("-","/",$hasta));

    $desde_pasado = new DateTime($desde);
    $hasta_pasado = new DateTime($hasta);
    $desde_pasado->sub(new DateInterval("P1Y"));
    $hasta_pasado->sub(new DateInterval("P1Y"));

    if ($id_empresa == 249) $id_empresa = "249,868";

    // Sumamos la venta por sucursal
    $sql = "SELECT ";
    $sql.= " F.id_sucursal, ALM.nombre AS sucursal, F.id_empresa, ";
    $sql.= " IF(COUNT(*) IS NULL,0,COUNT(*)) AS cantidad, ";
    $sql.= " IF(SUM(F.costo_final) IS NULL,0,SUM(F.costo_final)) AS costo, ";
    $sql.= " IF(SUM(F.en_oferta) IS NULL,0,SUM(F.en_oferta)) AS oferta, ";
    $sql.= " IF(SUM(F.descuento) IS NULL,0,SUM(F.descuento)) AS descuento, ";
    $sql.= " IF(F.total IS NULL,0,SUM(F.total - F.interes)) AS total ";
    $sql.= "FROM facturas F ";
    $sql.= "LEFT JOIN almacenes ALM ON (F.id_empresa = ALM.id_empresa AND F.id_sucursal = ALM.id) ";
    $sql.= "WHERE F.id_empresa IN ($id_empresa) ";
    $sql.= "AND F.id_sucursal != 531 "; // NO TOMAR CENTRAL DE MEGASHOP 868
    $sql.= "AND F.anulada = 0 ";
    $sql.= "AND F.tipo != 'C' ";
    $sql.= "AND F.fecha >= '$desde' AND F.fecha <= '$hasta' ";
    $sql.= "GROUP BY F.id_sucursal ";
    $sql.= "ORDER BY total DESC ";
    $q = $this->db->query($sql);

    $total = 0;
    $salida = array();
    foreach($q->result() as $row) {

      if ($row->id_empresa == 249) {
        $s = explode(" - ", $row->sucursal);
        if (sizeof($s)>1) {
          $row->sucursal = trim($s[1]);
        }
      }

      $sql = "SELECT ";
      $sql.= " IF(F.total IS NULL,0,SUM(F.total - F.interes)) AS total ";
      $sql.= "FROM facturas F ";
      $sql.= "WHERE F.id_empresa IN ($id_empresa) ";
      $sql.= "AND F.anulada = 0 ";
      $sql.= "AND F.tipo != 'C' ";
      $sql.= "AND F.fecha >= '".$desde_pasado->format("Y-m-d")."' AND F.fecha <= '".$hasta_pasado->format("Y-m-d")."' ";
      $sql.= "AND F.id_sucursal = $row->id_sucursal ";
      $qq = $this->db->query($sql);
      if ($qq->num_rows()>0) {
        $rr = $qq->row();
        $row->venta_pasada = $rr->total;
        $row->variacion_venta = (($rr->total != 0) ? round((($row->total - $rr->total) / $rr->total) * 100,2) : 0);
      } else {
        $row->venta_pasada = 0;
        $row->variacion_venta = 0;
      }

      // Venta promedio
      $row->ticket_promedio = ($row->cantidad > 0) ? $row->total / $row->cantidad : 0;

      // Marcacion promedio
      $row->marcacion = ($row->costo == 0) ? 0 : (float) (($row->total / $row->costo) - 1) * 100;

      // Ganancia bruta
      $row->ganancia = (float)($row->total - $row->costo);

      $total += (float)($row->total);
      $salida[] = $row;
    }

    foreach($salida as $row) {
      $row->porcentaje = ($total > 0) ? (($row->total / $total) * 100) : 0;
    }

    if ($impresion == 1) {
      $this->load->view("reports/ventas_por_sucursal",array(
        "desde"=>fecha_es($desde),
        "hasta"=>fecha_es($hasta),
        "data"=>$salida,
      ));
    } else {
      echo json_encode(array(
        "results"=>$salida,
      ));      
    }
  }


  function articulos_vendidos() {

    @session_start();
    set_time_limit(0);
    $id_empresa = parent::get_empresa();
    $desde = parent::get_get("desde",date("d-m-Y"));
    $hasta = parent::get_get("hasta",date("d-m-Y"));
    $id_vendedor = parent::get_get("id_vendedor",0);
    $id_cliente = parent::get_get("id_cliente",0);
    $id_punto_venta = parent::get_get("id_punto_venta",0);
    $id_sucursal = parent::get_get("id_sucursal",0);
    $id_proveedor = parent::get_get("id_proveedor",0);
    $id_departamento = parent::get_get("id_departamento",0);
    $id_rubro = parent::get_get("id_rubro",0);
    $id_marca = parent::get_get("id_marca",0);
    $id_usuario = parent::get_get("id_usuario",0);
    $ids_articulos = parent::get_get("ids_articulos","");
    $codigos_articulos = parent::get_get("articulos","");
    $limit = parent::get_get("limit",0);
    $agrupado = parent::get_get("agrupado","A");
    $offset = parent::get_get("offset",20);
    $order = parent::get_get("order","cantidad");
    $order_by = parent::get_get("order_by","ASC");
    $en_oferta = parent::get_get("en_oferta",0);
    $incluir_stock = parent::get_get("incluir_stock",0);
    $not_in_estados = parent::get_get("not_in_estados","");
    $reparto = parent::get_get("reparto",0);

    $this->load->model("Empresa_Model");

    // CASO ESPECIAL YEYO, PUEDE VER MAS DE UNA EMPRESA
    $in_ids_empresas = "";
    if ($id_empresa == 980) {
      $in_ids_empresas = implode(",", $this->Empresa_Model->get_ids_empresas_por_vendedor($this->Empresa_Model->get_id_vendedor_don_yeyo()));
    }

    $ids_rubro = "";
    if ($id_rubro != 0) {
      $this->load->model("Rubro_Model");
      $ids = $this->Rubro_Model->get_ids_rubros($id_rubro);
      $ids_rubro = implode(",", $ids);
    }

    $this->load->model("Stock_Model");
    $this->load->helper("fecha_helper");
    $desde = fecha_mysql(str_replace("-","/",$desde));
    $hasta = fecha_mysql(str_replace("-","/",$hasta));
    //$hasta = date("Y-m-d",strtotime($hasta." +1 day")); // Sumamos un dia a la fecha de hasta

    $date_desde = new DateTime($desde);
    $date_hasta = new DateTime($hasta);
    $dias = $date_hasta->diff($date_desde)->format("%a");

    if (!($agrupado == "A" || $agrupado == "R" || $agrupado == "V" || $agrupado == "C" || $agrupado == "D")) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"Parametros incorrectos.",
      ));
      exit();
    }

    $empresa = $this->Empresa_Model->get_min($id_empresa);

    $sql_from = "SELECT SQL_CALC_FOUND_ROWS ";
    if ($agrupado == "A") {
      $sql_from.= " FI.id_articulo AS id, ";
      $sql_from.= " FI.nombre AS nombre_item, ";
    } else if ($agrupado == "R") {
      $sql_from.= " FI.id_rubro AS id, ";
      $sql_from.= " FI.nombre AS nombre_item, ";
    } else if ($agrupado == "V") {
      $sql_from.= " F.id_vendedor AS id, ";
      $sql_from.= " F.vendedor AS nombre_item, ";
    } else if ($agrupado == "C") {
      $sql_from.= " F.id_cliente AS id, ";
      $sql_from.= " F.cliente AS nombre_item, ";
    } else if ($agrupado == "D") {
      $sql_from.= " A.id_departamento AS id, ";
      $sql_from.= " FI.nombre AS nombre_item, ";
    }
    $sql_from.= " IF(A.codigo IS NULL,'',A.codigo) AS codigo, ";
    $sql_from.= " IF(A.codigo_barra IS NULL,'',A.codigo_barra) AS codigo_barra, ";
    $sql_from.= " IF(A.lista_precios IS NULL,0,IF(A.lista_precios > 0,1,0)) AS activo, ";
    $sql_from.= " IF(A.custom_10 IS NULL,'',A.custom_10) AS codigo_prov, ";
    $sql_from.= " IF(A.custom_6 IS NULL,'',A.custom_6) AS proveedor, ";
    if ($empresa->id_proyecto != 10) {
      $sql_from.= " SUM(IF(FI.tipo_cantidad = '' OR FI.tipo_cantidad = 'X',FI.cantidad,0) * IF(TC.negativo = 1,-1,1)) AS cantidad, ";
      $sql_from.= " SUM(IF(FI.tipo_cantidad = 'B',FI.cantidad,0) * IF(TC.negativo = 1,-1,1)) AS bonificado, ";
      $sql_from.= " SUM(IF(FI.tipo_cantidad = 'D',FI.cantidad,0) * IF(TC.negativo = 1,-1,1)) AS devolucion, ";      
      $sql_from.= " SUM(IF(FI.tipo_cantidad = 'C',FI.cantidad,0) * IF(TC.negativo = 1,-1,1)) AS cambio, ";
    } else {
      // Restovar usamos tipo_cantidad para otra cosa
      $sql_from.= " SUM(FI.cantidad * IF(TC.negativo = 1,-1,1)) AS cantidad, ";
      $sql_from.= " 0 AS bonificado, ";
      $sql_from.= " 0 AS devolucion, ";      
      $sql_from.= " 0 AS cambio, ";      
    }
    $sql_from.= " IF(MAX(FI.custom_2) IS NULL,'',MAX(FI.custom_2)) AS custom_2, "; // 1 = Tiene descuento | 0 = No tiene descuento
    $sql_from.= " SUM(FI.total_con_iva * IF(TC.negativo = 1,-1,1)) AS total_final, ";
    $sql_from.= " SUM(FI.costo_final * IF(TC.negativo = 1,-1,1)) AS costo_final ";
    $sql_from.= "FROM facturas F ";
    $sql_from.= "INNER JOIN facturas_items FI ON (FI.id_empresa = F.id_empresa AND FI.id_punto_venta = F.id_punto_venta AND F.id = FI.id_factura) ";
    $sql_from.= "INNER JOIN tipos_comprobante TC ON (F.id_tipo_comprobante = TC.id) ";
    $sql_from.= "LEFT JOIN articulos A ON (A.id_empresa = FI.id_empresa AND A.id = FI.id_articulo) ";

    if (empty($in_ids_empresas)) $sql_where.= "WHERE F.id_empresa = $id_empresa ";
    else $sql_where.= "WHERE F.id_empresa IN ($in_ids_empresas) ";
    $sql_where.= "AND FI.anulado = 0 ";
    $sql_where.= "AND F.anulada = 0 ";
    $sql_where.= "AND F.tipo != 'C' ";
    $sql_where.= "AND F.fecha >= '$desde' AND F.fecha <= '$hasta' ";
    $sql_where.= "AND IF(F.id_origen = 1,IF(F.id_tipo_estado >= 4 AND F.id_tipo_estado != 7,1,0),1) = 1 ";
    if (!empty($id_vendedor)) $sql_where.= "AND F.id_vendedor = $id_vendedor ";
    if (!empty($ids_rubro)) $sql_where.= "AND FI.id_rubro IN($ids_rubro) ";
    if (!empty($id_marca)) $sql_where.= "AND A.id_marca = $id_marca ";
    if (!empty($id_cliente)) $sql_where.= "AND FI.id_cliente = $id_cliente ";
    if (!empty($id_proveedor)) $sql_where.= "AND FI.id_proveedor = $id_proveedor ";
    if (!empty($id_departamento)) $sql_where.= "AND A.id_departamento = $id_departamento ";
    if (!empty($id_punto_venta)) $sql_where.= "AND FI.id_punto_venta = $id_punto_venta ";
    if (!empty($id_sucursal)) $sql_where.= "AND F.id_sucursal = $id_sucursal ";
    if (!empty($en_oferta)) $sql_where.= "AND FI.custom_2 = '1' ";
    if (!empty($reparto)) $sql_where.= "AND F.reparto = '$reparto' ";
    if (!empty($id_usuario)) $sql_where.= "AND F.id_usuario = $id_usuario ";
    if (!empty($not_in_estados)) {
      $not_in_estados = str_replace("-", ",", $not_in_estados);
      $sql_where.= "AND F.id_tipo_estado NOT IN ($not_in_estados) ";
    }
    if (!empty($ids_articulos)) {
      $ids_articulos = str_replace("-", ",", $ids_articulos);
      $sql_where.= "AND FI.id_articulo IN ($ids_articulo) ";
    }
    if (!empty($codigos_articulos)) {
      $codigos = explode("-", $codigos_articulos);
      foreach($codigos as $cod) {
        $cod = "'".$cod."'";
      }
      $codigos_articulos = implode(",", $codigos);
      $sql_where.= "AND A.codigo IN ($codigos_articulos) ";
    }

    $sql = "";
    if ($agrupado == "A") {
      $sql.= "GROUP BY FI.id_articulo ";
      $tabla = "articulos";
    } else if ($agrupado == "R") {
      $sql.= "GROUP BY FI.id_rubro ";
      $tabla = "rubros";
    } else if ($agrupado == "V") {
      $sql.= "GROUP BY F.id_vendedor ";
      $tabla = "vendedores";
    } else if ($agrupado == "C") {
      $sql.= "GROUP BY F.id_cliente ";
      $tabla = "clientes";
    } else if ($agrupado == "D") {
      $sql.= "GROUP BY A.id_departamento ";
      $tabla = "departamentos_comerciales";
    }
    $sql.= "ORDER BY $order_by $order ";
    $sql.= "LIMIT $limit, $offset ";
    $sql_salida = $sql_from.$sql_where.$sql;
    file_put_contents("log_estadisticas.txt", "Usuario: ".$_SESSION["nombre"]."\n", FILE_APPEND);
    file_put_contents("log_estadisticas.txt", "Inicio: ".date("Y-m-d H:i:s")."\n", FILE_APPEND);
    file_put_contents("log_estadisticas.txt", $sql_salida."\n", FILE_APPEND);
    $q = $this->db->query($sql_salida);

    $q_total = $this->db->query("SELECT FOUND_ROWS() AS total");
    $total = $q_total->row();

    $salida = array();
    foreach($q->result() as $row) {

      if ($row === FALSE || empty($row->id)) continue;
      $row->stock = 0;
      $row->dias_stock = 0;

      if ($tabla == "articulos") {
        $row->nombre = $row->nombre_item;

        // Si estabamos haciendo estadisticas sobre la tabla articulos
        // y la fecha hasta es igual a hoy (por si esta viendo periodos pasados, que no confunda el stock)
        if ($incluir_stock == 1) {
          $row->stock = $this->Stock_Model->get_saldo(array(
            "id_empresa"=>$id_empresa,
            "id_articulo"=>$row->id,
            "id_sucursal"=>$id_sucursal,
          ));
          $venta_diaria = (($dias != 0) ? ($row->cantidad / $dias) : 0);
          $row->dias = $dias;
          $row->dias_stock = (($venta_diaria != 0) ? ($row->stock / $venta_diaria) : 0);
        }

      } else {
        // Dependiendo de que parametro estamos agrupando
        $sql = "SELECT nombre ";
        if ($tabla != "articulos") $sql.= ", codigo "; // Porque si es articulos, el codigo es propiamente del articulo
        $sql.= "FROM $tabla ";
        if (empty($in_ids_empresas)) $sql.= "WHERE id_empresa = $id_empresa ";
        else $sql.= "WHERE id_empresa IN ($in_ids_empresas) ";
        $sql.= "AND id = $row->id LIMIT 0,1 ";
        $q_rr = $this->db->query($sql);
        if ($q_rr->num_rows()>0) {
          $rr = $q_rr->row();
          $row->nombre = $rr->nombre;
          if ($tabla != "articulos") $row->codigo = $rr->codigo;
        } else {
          $row->nombre = $row->nombre_item;
        }
      }

      if ($tabla == "vendedores" && $row->id == 0) {
        $row->nombre_item = "Sin definir";
        $row->nombre = "Sin definir";
        $row->codigo = "";
      }
      if ($tabla == "clientes" && $row->id == 0) {
        $row->nombre_item = "Consumidor Final";
        $row->nombre = "Consumidor Final";
        $row->codigo = "";
        $row->proveedor = "";
      }
      if ($tabla == "departamentos_comerciales" && $row->id == 0) {
        $row->nombre_item = "Sin Definir";
        $row->nombre = "Sin Definir";
        $row->codigo = "";
        $row->proveedor = "";
      }

      if (!isset($row->nombre)) $row->nombre = $row->nombre_item;

      $row->ganancia = $row->total_final - $row->costo_final;
      $salida[] = $row;
    }

    // SUMAMOS LOS TOTALES

    $sql = "SELECT ";
    $sql.= " SUM(IF(FI.tipo_cantidad = '' OR FI.tipo_cantidad = 'X',FI.cantidad,0) * IF(TC.negativo = 1,-1,1)) AS cantidad, ";
    $sql.= " SUM(IF(FI.tipo_cantidad = 'B',FI.cantidad,0) * IF(TC.negativo = 1,-1,1)) AS bonificado, ";
    $sql.= " SUM(IF(FI.tipo_cantidad = 'D',FI.cantidad,0) * IF(TC.negativo = 1,-1,1)) AS devolucion, ";
    $sql.= " SUM(IF(FI.tipo_cantidad = 'B',FI.precio,0) * IF(TC.negativo = 1,-1,1)) AS total_bonificado, ";
    $sql.= " SUM(FI.total_con_iva * IF(TC.negativo = 1,-1,1)) AS total_final, ";
    $sql.= " SUM(FI.costo_final * IF(TC.negativo = 1,-1,1)) AS costo_final ";
    $sql.= "FROM facturas F ";
    $sql.= "INNER JOIN tipos_comprobante TC ON (F.id_tipo_comprobante = TC.id) ";
    $sql.= "INNER JOIN facturas_items FI ON (FI.id_empresa = F.id_empresa AND FI.id_punto_venta = F.id_punto_venta AND F.id = FI.id_factura) ";
    $sql.= "LEFT JOIN articulos A ON (A.id_empresa = FI.id_empresa AND A.id = FI.id_articulo) ";
    $sql_totales = $sql.$sql_where;
    file_put_contents("log_estadisticas.txt", "Usuario: ".$_SESSION["nombre"]."\n", FILE_APPEND);
    file_put_contents("log_estadisticas.txt", "Inicio: ".date("Y-m-d H:i:s")."\n", FILE_APPEND);
    file_put_contents("log_estadisticas.txt", $sql_totales."\n", FILE_APPEND);    

    $q_totales = $this->db->query($sql_totales);
    $totales = $q_totales->row();
    echo json_encode(array(
      "results"=>$salida,
      "total"=>$total->total,
      "sql"=>$sql_salida,
      "sql_totales"=>$sql_totales,
      "meta"=>array(
        "cantidad"=>$totales->cantidad,
        "bonificado"=>$totales->bonificado,
        "total_bonificado"=>$totales->total_bonificado,
        "devolucion"=>$totales->devolucion,
        "total_final"=>$totales->total_final,
        "costo_final"=>$totales->costo_final,
      ),
    ));
  }


  function comparacion() {
    @session_start();
    set_time_limit(0);
    $id_empresa = parent::get_empresa();
    $this->load->model("Empresa_Model");

    // CASO ESPECIAL YEYO, PUEDE VER MAS DE UNA EMPRESA
    $in_ids_empresas = "";
    if ($id_empresa == 980) {
      $in_ids_empresas = implode(",", $this->Empresa_Model->get_ids_empresas_por_vendedor($this->Empresa_Model->get_id_vendedor_don_yeyo()));
    }

    $desde = parent::get_get("desde",date("d-m-Y"));
    $hasta = parent::get_get("hasta",date("d-m-Y"));
    $desde_2 = parent::get_get("desde_2",date("d-m-Y"));
    $hasta_2 = parent::get_get("hasta_2",date("d-m-Y"));
    $id_vendedor = parent::get_get("id_vendedor",0);
    $id_cliente = parent::get_get("id_cliente",0);
    $id_punto_venta = parent::get_get("id_punto_venta",0);
    $id_sucursal = parent::get_get("id_sucursal",0);
    $id_proveedor = parent::get_get("id_proveedor",0);
    $id_departamento = parent::get_get("id_departamento",0);
    $id_rubro = parent::get_get("id_rubro",0);
    $id_marca = parent::get_get("id_marca",0);
    $ids_articulos = parent::get_get("ids_articulos","");
    $codigos_articulos = parent::get_get("articulos","");
    $limit = parent::get_get("limit",0);
    $agrupado = parent::get_get("agrupado","A");
    $offset = parent::get_get("offset",20);
    $order = parent::get_get("order","cantidad");
    $order_by = parent::get_get("order_by","ASC");
    $filtrar_en_cero = parent::get_get("filtrar_en_cero",0);
    $not_in_estados = parent::get_get("not_in_estados","");

    $ids_rubro = "";
    if ($id_rubro != 0) {
      $this->load->model("Rubro_Model");
      $ids = $this->Rubro_Model->get_ids_rubros($id_rubro);
      $ids_rubro = implode(",", $ids);
    }

    $this->load->helper("fecha_helper");
    $desde = fecha_mysql(str_replace("-","/",$desde));
    $hasta = fecha_mysql(str_replace("-","/",$hasta));
    $desde_2 = fecha_mysql(str_replace("-","/",$desde_2));
    $hasta_2 = fecha_mysql(str_replace("-","/",$hasta_2));

    $cantidad_1 = 0;
    $devolucion_1 = 0;
    $bonificado_1 = 0;
    $total_final_1 = 0;
    $costo_final_1 = 0;
    $cantidad_2 = 0;
    $devolucion_2 = 0;
    $bonificado_2 = 0;
    $total_final_2 = 0;
    $costo_final_2 = 0;

    // IMPORTANTE:
    // Consultamos los distintos articulos, rubros, vendedores o clientes
    // que se facturaron en el rango completo
    // (desde fecha desde 1 hasta fecha hasta 2)
    // Esto se hace asi porque puede que se hayan eliminado elementos en el rango
    // entonces despues no coincide con la consulta por articulos particular
    if ($agrupado == "A") {
      $sql = "SELECT DISTINCT FI.id_articulo AS id, ";
      $sql.= " IF(A.codigo IS NULL,'',A.codigo) AS codigo, ";
      $sql.= " IF(A.nombre IS NULL,'Art. Eliminado',A.nombre) AS nombre_item ";
      $sql.= "FROM facturas F ";
      $sql.= "INNER JOIN facturas_items FI ON (FI.id_empresa = F.id_empresa AND FI.id_punto_venta = F.id_punto_venta AND F.id = FI.id_factura) ";
      $sql.= "INNER JOIN tipos_comprobante TC ON (F.id_tipo_comprobante = TC.id) ";
      $sql.= "LEFT JOIN articulos A ON (FI.id_articulo = A.id AND FI.id_empresa = A.id_empresa) ";
      if (empty($in_ids_empresas)) $sql.= "WHERE F.id_empresa = $id_empresa ";
      else $sql.= "WHERE F.id_empresa IN ($in_ids_empresas) ";

      $sql.= "AND F.fecha >= '$desde' AND F.fecha <= '$hasta_2' ";      
      $sql.= "ORDER BY codigo ASC ";      
    } else if ($agrupado == "R") {
      $sql = "SELECT DISTINCT FI.id_rubro AS id, ";
      $sql.= " IF(A.codigo IS NULL,'',A.codigo) AS codigo, ";
      $sql.= " IF(A.nombre IS NULL,'Rubro Eliminado',A.nombre) AS nombre_item ";
      $sql.= "FROM facturas F ";
      $sql.= "INNER JOIN facturas_items FI ON (FI.id_empresa = F.id_empresa AND FI.id_punto_venta = F.id_punto_venta AND F.id = FI.id_factura) ";
      $sql.= "INNER JOIN tipos_comprobante TC ON (F.id_tipo_comprobante = TC.id) ";
      $sql.= "LEFT JOIN rubros A ON (FI.id_rubro = A.id AND FI.id_empresa = A.id_empresa) ";
      if (empty($in_ids_empresas)) $sql.= "WHERE F.id_empresa = $id_empresa ";
      else $sql.= "WHERE F.id_empresa IN ($in_ids_empresas) ";
      $sql.= "AND F.fecha >= '$desde' AND F.fecha <= '$hasta_2' ";      
      if (!empty($ids_rubro)) $sql_int.= "AND FI.id_rubro IN($ids_rubro) ";
      $sql.= "ORDER BY A.nombre ASC ";      
    } else if ($agrupado == "V") {
      $sql = "SELECT DISTINCT F.id_vendedor AS id, ";
      $sql.= " IF(A.codigo IS NULL,'',A.codigo) AS codigo, ";
      $sql.= " IF(A.nombre IS NULL,'Sin Vendedor',A.nombre) AS nombre_item ";
      $sql.= "FROM facturas F ";
      $sql.= "INNER JOIN facturas_items FI ON (FI.id_empresa = F.id_empresa AND FI.id_punto_venta = F.id_punto_venta AND F.id = FI.id_factura) ";
      $sql.= "INNER JOIN tipos_comprobante TC ON (F.id_tipo_comprobante = TC.id) ";
      $sql.= "LEFT JOIN vendedores A ON (F.id_vendedor = A.id AND FI.id_empresa = A.id_empresa) ";
      if (empty($in_ids_empresas)) $sql.= "WHERE F.id_empresa = $id_empresa ";
      else $sql.= "WHERE F.id_empresa IN ($in_ids_empresas) ";
      $sql.= "AND F.fecha >= '$desde' AND F.fecha <= '$hasta_2' ";      
      if (!empty($id_vendedor)) $sql.= "AND F.id_vendedor = $id_vendedor ";
      $sql.= "ORDER BY A.nombre ASC ";
    } else if ($agrupado == "C") {
      $sql = "SELECT DISTINCT F.id_cliente AS id, ";
      $sql.= " IF(A.codigo IS NULL,'',A.codigo) AS codigo, ";
      $sql.= " IF(A.nombre IS NULL,'Consumidor Final',A.nombre) AS nombre_item ";
      $sql.= "FROM facturas F ";
      $sql.= "INNER JOIN facturas_items FI ON (FI.id_empresa = F.id_empresa AND FI.id_punto_venta = F.id_punto_venta AND F.id = FI.id_factura) ";
      $sql.= "INNER JOIN tipos_comprobante TC ON (F.id_tipo_comprobante = TC.id) ";
      $sql.= "LEFT JOIN clientes A ON (F.id_cliente = A.id AND FI.id_empresa = A.id_empresa) ";
      if (empty($in_ids_empresas)) $sql.= "WHERE F.id_empresa = $id_empresa ";
      else $sql.= "WHERE F.id_empresa IN ($in_ids_empresas) ";
      $sql.= "AND F.fecha >= '$desde' AND F.fecha <= '$hasta_2' ";      
      if (!empty($id_cliente)) $sql.= "AND FI.id_cliente = $id_cliente ";
      $sql.= "ORDER BY A.nombre ASC ";
    }
    $q = $this->db->query($sql);
    $salida = array();
    foreach($q->result() as $row) {

      $sql_int = "FROM facturas F ";
      $sql_int.= "INNER JOIN facturas_items FI ON (FI.id_empresa = F.id_empresa AND FI.id_punto_venta = F.id_punto_venta AND F.id = FI.id_factura) ";
      $sql_int.= "INNER JOIN tipos_comprobante TC ON (F.id_tipo_comprobante = TC.id) ";
      $sql_int.= "LEFT JOIN articulos A ON (FI.id_articulo = A.id AND FI.id_empresa = A.id_empresa) ";
      if (empty($in_ids_empresas)) $sql_int.= "WHERE F.id_empresa = $id_empresa ";
      else $sql_int.= "WHERE F.id_empresa IN ($in_ids_empresas) ";
      $sql_int.= "AND FI.anulado = 0 ";
      $sql_int.= "AND F.anulada = 0 ";
      $sql_int.= "AND F.tipo != 'C' ";
      if (!empty($id_vendedor)) $sql_int.= "AND F.id_vendedor = $id_vendedor ";
      if (!empty($id_cliente)) $sql_int.= "AND FI.id_cliente = $id_cliente ";
      if (!empty($id_proveedor)) $sql_int.= "AND FI.id_proveedor = $id_proveedor ";
      if (!empty($id_punto_venta)) $sql_int.= "AND FI.id_punto_venta = $id_punto_venta ";
      if (!empty($id_sucursal)) $sql_int.= "AND F.id_sucursal = $id_sucursal ";
      if (!empty($en_oferta)) $sql_int.= "AND FI.custom_2 = '1' ";
      if (!empty($not_in_estados)) {
        $not_in_estados = str_replace("-", ",", $not_in_estados);
        $sql_int.= "AND F.id_tipo_estado NOT IN ($not_in_estados) ";
      }
      if (!empty($ids_rubro)) $sql_int.= "AND A.id_rubro IN($ids_rubro) ";
      if (!empty($id_marca)) $sql_int.= "AND A.id_marca = $id_marca ";
      if (!empty($id_departamento)) $sql_int.= "AND A.id_departamento = $id_departamento ";
      if (!empty($ids_articulos)) {
        $ids_articulos = str_replace("-", ",", $ids_articulos);
        $sql_int.= "AND A.id IN ($ids_articulo) ";
      }
      if (!empty($codigos_articulos)) {
        $codigos = explode("-", $codigos_articulos);
        foreach($codigos as $cod) {
          $cod = "'".$cod."'";
        }
        $codigos_articulos = implode(",", $codigos);
        $sql_int.= "AND A.codigo IN ($codigos_articulos) ";
      }

      // Dependiendo que estamos filtrando
      if ($agrupado == "A") {
        $sql_int.= "AND FI.id_articulo = $row->id ";
      } else if ($agrupado == "R") {
        $sql_int.= "AND FI.id_rubro = $row->id ";
      } else if ($agrupado == "V") {
        $sql_int.= "AND F.id_vendedor = $row->id ";
      } else if ($agrupado == "C") {
        $sql_int.= "AND F.id_cliente = $row->id ";
      }

      // Filtros por los dos periodos
      $sql_fecha_1 = "AND F.fecha >= '$desde' AND F.fecha <= '$hasta' ";
      $sql_fecha_2 = "AND F.fecha >= '$desde_2' AND F.fecha <= '$hasta_2' ";

      $sql = "SELECT ";
      $sql.= " (SELECT SUM(IF(FI.tipo_cantidad = '' OR FI.tipo_cantidad = 'X',FI.cantidad,0) * IF(TC.negativo = 1,-1,1))) AS cantidad, ";
      $sql.= " (SELECT SUM(IF(FI.tipo_cantidad = 'B',FI.cantidad,0) * IF(TC.negativo = 1,-1,1))) AS bonificado, ";
      $sql.= " (SELECT SUM(IF(FI.tipo_cantidad = 'D',FI.cantidad,0) * IF(TC.negativo = 1,-1,1))) AS devolucion, ";
      $sql.= " (SELECT SUM(FI.total_con_iva * IF(TC.negativo = 1,-1,1))) AS total_final, ";
      $sql.= " (SELECT SUM(FI.costo_final * IF(TC.negativo = 1,-1,1))) AS costo_final ";

      // Calculamos el primer periodo
      $qq = $this->db->query($sql.$sql_int.$sql_fecha_1);
      $rr = $qq->row();
      $row->cantidad_1 = is_null($rr->cantidad) ? 0 : $rr->cantidad;
      $row->bonificado_1 = is_null($rr->bonificado) ? 0 : $rr->bonificado;
      $row->devolucion_1 = is_null($rr->devolucion) ? 0 : $rr->devolucion;
      $row->total_final_1 = is_null($rr->total_final) ? 0 : $rr->total_final;
      $row->costo_final_1 = is_null($rr->costo_final) ? 0 : $rr->costo_final;

      // Calculamos el segundo periodo
      $qq = $this->db->query($sql.$sql_int.$sql_fecha_2);
      $rr = $qq->row();
      $row->cantidad_2 = is_null($rr->cantidad) ? 0 : $rr->cantidad;
      $row->bonificado_2 = is_null($rr->bonificado) ? 0 : $rr->bonificado;
      $row->devolucion_2 = is_null($rr->devolucion) ? 0 : $rr->devolucion;
      $row->total_final_2 = is_null($rr->total_final) ? 0 : $rr->total_final;
      $row->costo_final_2 = is_null($rr->costo_final) ? 0 : $rr->costo_final;

      $row->variacion_cantidad_2 = (($row->cantidad_1 != 0) ? round((($row->cantidad_2 - $row->cantidad_1) / $row->cantidad_1) * 100,2) : 0);
      $row->variacion_total_final_2 = (($row->total_final_1 != 0) ? round((($row->total_final_2 - $row->total_final_1) / $row->total_final_1) * 100,2) : 0);

      $cantidad_1 += $row->cantidad_1;
      $bonificado_1 += $row->bonificado_1;
      $devolucion_1 += $row->devolucion_1;
      $costo_final_1 += $row->costo_final_1;
      $total_final_1 += $row->total_final_1;
      $cantidad_2 += $row->cantidad_2;
      $bonificado_2 += $row->bonificado_2;
      $devolucion_2 += $row->devolucion_2;
      $costo_final_2 += $row->costo_final_2;
      $total_final_2 += $row->total_final_2;

      $salida[] = $row;
      /*if ($filtrar_en_cero == 0) $salida[] = $row;
      else if ($row->cantidad_1 != 0 || $row->bonificado_1 != 0 || $row->devolucion_1 != 0 || $row->costo_final_1 != 0 || $row->total_final_1 != 0
        || $row->cantidad_2 != 0 || $row->bonificado_2 != 0 || $row->devolucion_2 != 0 || $row->costo_final_2 != 0 || $row->total_final_2 != 0) {
        $salida[] = $row;
      }*/
    }

    $variacion_cantidad_2 = (($cantidad_1 != 0) ? round((($cantidad_2 - $cantidad_1) / $cantidad_1) * 100,2) : 0);
    $variacion_total_final_2 = (($total_final_1 != 0) ? round((($total_final_2 - $total_final_1) / $total_final_1) * 100,2) : 0);

    echo json_encode(array(
      "results"=>$salida,
      "total"=>sizeof($salida),
      "meta"=>array(
        "cantidad_1" => $cantidad_1,
        "bonificado_1" => $bonificado_1,
        "devolucion_1" => $devolucion_1,
        "costo_final_1" => $costo_final_1,
        "total_final_1" => $total_final_1,
        "cantidad_2" => $cantidad_2,
        "bonificado_2" => $bonificado_2,
        "devolucion_2" => $devolucion_2,
        "costo_final_2" => $costo_final_2,
        "total_final_2" => $total_final_2,
        "variacion_cantidad_2" => $variacion_cantidad_2,
        "variacion_total_final_2" => $variacion_total_final_2,
      )      
    ));
  }


  // FUNCION USADA POR MEGASHOP PARA COMPARAR LOS ARTICULOS VENDIDOS ENTRE DISTINTAS SUCURSALES
  function articulos_sucursales() {

    @session_start();
    set_time_limit(0);
    $id_empresa = parent::get_empresa();
    $desde = parent::get_get("desde",date("d-m-Y"));
    $hasta = parent::get_get("hasta",date("d-m-Y"));
    $id_sucursal_1 = parent::get_get("id_sucursal_1",0);
    $id_sucursal_2 = parent::get_get("id_sucursal_2",0);
    $offset = parent::get_get("offset",20);
    $order = parent::get_get("order","DESC");
    $order_by = parent::get_get("order_by","cantidad");    

    $this->load->helper("fecha_helper");
    $desde = fecha_mysql(str_replace("-","/",$desde));
    $hasta = fecha_mysql(str_replace("-","/",$hasta));    

    $sql_from = "SELECT ";
    $sql_from.= " FI.id_articulo AS id, ";
    $sql_from.= " FI.nombre AS nombre, ";
    $sql_from.= " SUM(IF(FI.tipo_cantidad = '' OR FI.tipo_cantidad = 'X',FI.cantidad,0) * IF(TC.negativo = 1,-1,1)) AS cantidad, ";
    $sql_from.= " IF(MAX(FI.custom_2) IS NULL,'',MAX(FI.custom_2)) AS custom_2, "; // 1 = Tiene descuento | 0 = No tiene descuento
    $sql_from.= " SUM(FI.total_con_iva * IF(TC.negativo = 1,-1,1)) AS total, ";
    $sql_from.= " SUM(FI.costo_final * IF(TC.negativo = 1,-1,1)) AS costo_final ";
    $sql_from.= "FROM facturas F ";
    $sql_from.= "INNER JOIN facturas_items FI ON (FI.id_empresa = F.id_empresa AND FI.id_punto_venta = F.id_punto_venta AND F.id = FI.id_factura) ";
    $sql_from.= "INNER JOIN tipos_comprobante TC ON (F.id_tipo_comprobante = TC.id) ";
    $sql_from.= "LEFT JOIN articulos A ON (A.id_empresa = FI.id_empresa AND A.id = FI.id_articulo) ";

    $sql_where = "WHERE F.id_empresa = $id_empresa ";
    $sql_where.= "AND FI.anulado = 0 ";
    $sql_where.= "AND F.anulada = 0 ";
    $sql_where.= "AND F.tipo != 'C' ";
    $sql_where.= "AND F.fecha >= '$desde' AND F.fecha <= '$hasta' ";
    //if (!empty($id_proveedor)) $sql_where.= "AND FI.id_proveedor = $id_proveedor ";
    //if (!empty($id_departamento)) $sql_where.= "AND A.id_departamento = $id_departamento ";

    $sql_group = "GROUP BY FI.id_articulo ";
    $sql_group.= "ORDER BY $order_by $order ";
    $sql_group.= "LIMIT 0, $offset ";
    $q = $this->db->query($sql_from.$sql_where."AND F.id_sucursal = $id_sucursal_1 ".$sql_group);
    $suc1 = array();
    $suc2 = array();
    foreach($q->result() as $row) {
      if ($row === FALSE || empty($row->id)) continue;

      // Buscamos el mismo producto pero en la otra sucursal
      $qq = $this->db->query($sql_from.$sql_where."AND FI.id_articulo = $row->id AND F.id_sucursal = $id_sucursal_2 GROUP BY FI.id_articulo");
      if ($qq->num_rows()>0) {
        $rr = $qq->row();
        $rr->ganancia = $rr->total - $rr->costo_final;
      } else {
        $rr = new stdClass();
        $rr->nombre = $row->nombre;
        $rr->cantidad = 0;
        $rr->custom_2 = "";
        $rr->total = 0;
        $rr->costo_final = 0;
        $rr->ganancia = 0;
      }
      $row->ganancia = $row->total - $row->costo_final;
      $suc1[] = $row;
      $suc2[] = $rr;
    }

    echo json_encode(array(
      "results_1"=>$suc1,
      "results_2"=>$suc2,
    ));
  }  


  /*
  function comparacion($periodo1_desde=0,$periodo1_hasta=0,$periodo2_desde=0,$periodo2_hasta=0,$comparacion=0,$id_vendedor=0) {

    $id_empresa = $_SESSION["id_empresa"];
    $estado = $_SESSION["estado"];
    $order_by = $this->input->get("order_by");
    $order = $this->input->get("order");

    $this->load->helper("fecha_helper");
    $resultado = array();

        // Acomodamos los datos de entrada
    $periodo1_desde = fecha_mysql(str_replace("-","/",$periodo1_desde));
    $periodo1_hasta = fecha_mysql(str_replace("-","/",$periodo1_hasta));
    $periodo2_desde = fecha_mysql(str_replace("-","/",$periodo2_desde));
    $periodo2_hasta = fecha_mysql(str_replace("-","/",$periodo2_hasta));

    if ($comparacion == "A") {

     $sql = "SELECT * FROM articulos ORDER BY descripcion ASC ";
     $q = $this->db->query($sql);
     foreach($q->result() as $r) {

      $fila = new stdClass();
      $fila->descripcion = $r->descripcion;
      $fila->id = $r->id;
      $fila->cantidad_1 = 0;
      $fila->total_1 = 0;
      $fila->cantidad_2 = 0;
      $fila->total_2 = 0;
      $fila->variacion = 0;

        // Primer periodo
      $sql = "SELECT SUM(FI.cantidad) AS cantidad, SUM(FI.total_con_iva) AS total ";
      $sql.= "FROM facturas_items FI INNER JOIN facturas F ON (FI.id_factura = F.id) ";
      $sql.= "WHERE FI.id_articulo = $r->id AND F.anulada = 0 ";
      $sql.= "AND F.fecha >= '$periodo1_desde' AND F.fecha <= '$periodo1_hasta' ";
      $sql.= "AND F.id_tipo_comprobante != 0 ";
      if ($estado == 0) $sql.= "AND F.tipo != 'R' ";
      if (!empty($id_vendedor)) $sql.= "AND F.id_vendedor = $id_vendedor ";
      if (!empty($id_empresa)) $sql.= "AND F.id_empresa = $id_empresa ";
      $q1 = $this->db->query($sql);
      if ($q1->num_rows() > 0) {
       $r1 = $q1->row();
       $fila->cantidad_1 = $r1->cantidad;
       if (is_null($fila->cantidad_1)) $fila->cantidad_1 = 0;
       $fila->total_1 = $r1->total;
       if (is_null($fila->total_1)) $fila->total_1 = 0;
     }

        // Segundo periodo
     $sql = "SELECT SUM(FI.cantidad) AS cantidad, SUM(FI.total_con_iva) AS total ";
     $sql.= "FROM facturas_items FI INNER JOIN facturas F ON (FI.id_factura = F.id) ";
     $sql.= "WHERE FI.id_articulo = $r->id AND F.anulada = 0 ";
     $sql.= "AND F.fecha >= '$periodo2_desde' AND F.fecha <= '$periodo2_hasta' ";
     $sql.= "AND F.id_tipo_comprobante != 0 ";
     if ($estado == 0) $sql.= "AND F.tipo != 'R' ";
     if (!empty($id_vendedor)) $sql.= "AND F.id_vendedor = $id_vendedor ";
     if (!empty($id_empresa)) $sql.= "AND F.id_empresa = $id_empresa ";
     $q2 = $this->db->query($sql);
     if ($q2->num_rows() > 0) {
       $r2 = $q2->row();
       $fila->cantidad_2 = $r2->cantidad;
       if (is_null($fila->cantidad_2)) $fila->cantidad_2 = 0;
       $fila->total_2 = $r2->total;
       if (is_null($fila->total_2)) $fila->total_2 = 0;
     }

     if ($fila->total_1 != 0 || $fila->total_2 != 0) {
       $resultado[] = $fila;
     }

   }

 } else if ($comparacion == "R") {

   $sql = "SELECT * FROM rubros ORDER BY nombre ASC ";
   $q = $this->db->query($sql);
   foreach($q->result() as $r) {

    $fila = new stdClass();
    $fila->descripcion = $r->nombre;
    $fila->id = $r->id;
    $fila->cantidad_1 = 0;
    $fila->total_1 = 0;
    $fila->cantidad_2 = 0;
    $fila->total_2 = 0;
    $fila->variacion = 0;

        // Primer periodo
    $sql = "SELECT SUM(FI.cantidad) AS cantidad, SUM(FI.total_con_iva) AS total ";
    $sql.= "FROM facturas_items FI INNER JOIN facturas F ON (FI.id_factura = F.id) ";
    $sql.= "INNER JOIN articulos A ON (FI.id_articulo = A.id) ";
    $sql.= "WHERE A.id_rubro = $r->id AND F.anulada = 0 ";
    $sql.= "AND F.id_tipo_comprobante != 0 ";
    if ($estado == 0) $sql.= "AND F.tipo != 'R' ";        
    $sql.= "AND F.fecha >= '$periodo1_desde' AND F.fecha <= '$periodo1_hasta' ";
    if (!empty($id_vendedor)) $sql.= "AND F.id_vendedor = $id_vendedor ";
    if (!empty($id_empresa)) $sql.= "AND F.id_empresa = $id_empresa ";
    $q1 = $this->db->query($sql);
    if ($q1->num_rows() > 0) {
     $r1 = $q1->row();
     $fila->cantidad_1 = $r1->cantidad;
     $fila->total_1 = $r1->total;
   }

        // Segundo periodo
   $sql = "SELECT SUM(FI.cantidad) AS cantidad, SUM(FI.total_con_iva) AS total ";
   $sql.= "FROM facturas_items FI INNER JOIN facturas F ON (FI.id_factura = F.id) ";
   $sql.= "INNER JOIN articulos A ON (FI.id_articulo = A.id) ";
   $sql.= "WHERE A.id_rubro = $r->id AND F.anulada = 0 ";
   $sql.= "AND F.id_tipo_comprobante != 0 ";
   if ($estado == 0) $sql.= "AND F.tipo != 'R' ";        
   $sql.= "AND F.fecha >= '$periodo2_desde' AND F.fecha <= '$periodo2_hasta' ";
   if (!empty($id_vendedor)) $sql.= "AND F.id_vendedor = $id_vendedor ";
   if (!empty($id_empresa)) $sql.= "AND F.id_empresa = $id_empresa ";
   $q2 = $this->db->query($sql);
   if ($q2->num_rows() > 0) {
     $r2 = $q2->row();
     $fila->cantidad_2 = $r2->cantidad;
     $fila->total_2 = $r2->total;
   }

   if ($fila->total_1 != 0 || $fila->total_2 != 0) {
     $resultado[] = $fila;
   }

 }      

} else if ($comparacion == "C") {

 $sql = "SELECT * FROM clientes ORDER BY nombre ASC ";
 $q = $this->db->query($sql);
 $cf = new stdClass();
 $cf->id = 0;
 $cf->nombre = "Consumidor Final";
 $array = array($cf);
 $array = array_merge($array,$q->result());
 foreach($array as $r) {

  $fila = new stdClass();
  $fila->descripcion = $r->nombre;
  $fila->id = $r->id;
  $fila->cantidad_1 = 0;
  $fila->total_1 = 0;
  $fila->cantidad_2 = 0;
  $fila->total_2 = 0;
  $fila->variacion = 0;

        // Primer periodo
  $sql = "SELECT COUNT(*) AS cantidad, SUM(F.total) AS total ";
  $sql.= "FROM facturas F ";
  $sql.= "WHERE F.id_cliente = $r->id AND F.anulada = 0 ";
  $sql.= "AND F.id_tipo_comprobante != 0 ";
  if ($estado == 0) $sql.= "AND F.tipo != 'R' ";
  $sql.= "AND F.fecha >= '$periodo1_desde' AND F.fecha <= '$periodo1_hasta' ";
  if (!empty($id_vendedor)) $sql.= "AND F.id_vendedor = $id_vendedor ";
  if (!empty($id_empresa)) $sql.= "AND F.id_empresa = $id_empresa ";
  $q1 = $this->db->query($sql);
  if ($q1->num_rows() > 0) {
   $r1 = $q1->row();
   $fila->cantidad_1 = $r1->cantidad;
   $fila->total_1 = $r1->total;
 }

        // Segundo periodo
 $sql = "SELECT COUNT(*) AS cantidad, SUM(F.total) AS total ";
 $sql.= "FROM facturas F ";
 $sql.= "WHERE F.id_cliente = $r->id AND F.anulada = 0 ";
 $sql.= "AND F.id_tipo_comprobante != 0 ";
 if ($estado == 0) $sql.= "AND F.tipo != 'R' ";
 $sql.= "AND F.fecha >= '$periodo2_desde' AND F.fecha <= '$periodo2_hasta' ";
 if (!empty($id_vendedor)) $sql.= "AND F.id_vendedor = $id_vendedor ";
 if (!empty($id_empresa)) $sql.= "AND F.id_empresa = $id_empresa ";
 $q2 = $this->db->query($sql);
 if ($q2->num_rows() > 0) {
   $r2 = $q2->row();
   $fila->cantidad_2 = $r2->cantidad;
   $fila->total_2 = $r2->total;
 }

 if ($fila->total_1 != 0 || $fila->total_2 != 0) {
   $resultado[] = $fila;
 }

}      
}


usort($resultado,function($a,$b) use ($order,$order_by) {
 if ($order == "asc") {
  return $a->{$order_by} > $b->{$order_by};
} else {
  return $a->{$order_by} < $b->{$order_by};
}
});

echo json_encode(array(
  "results" => $resultado,
  "total"=>sizeof($resultado)
  ));
}
*/
}