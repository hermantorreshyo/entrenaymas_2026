<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Venta_Model extends Abstract_Model {

  public $sql;
  
  function __construct() {
    parent::__construct("ticket","id");
  }

  function articulos($conf = array()) {

    $id_empresa = (isset($conf["id_empresa"])) ? $conf["id_empresa"] : parent::get_empresa();
    $desde = (isset($conf["desde"])) ? $conf["desde"] : date("Y-m-d");
    $hasta = (isset($conf["hasta"])) ? $conf["hasta"] : date("Y-m-d");
    $id_articulo = (isset($conf["id_articulo"])) ? $conf["id_articulo"] : 0;
    $ids_articulos = (isset($conf["ids_articulos"])) ? $conf["ids_articulos"] : "";
    $id_sucursal = (isset($conf["id_sucursal"])) ? $conf["id_sucursal"] : 0;
    $id_punto_venta = (isset($conf["id_punto_venta"])) ? $conf["id_punto_venta"] : -1;
    $en_oferta = (isset($conf["en_oferta"])) ? $conf["en_oferta"] : 0;
    $limit = (isset($conf["limit"])) ? $conf["limit"] : 0;
    $offset = (isset($conf["offset"])) ? $conf["offset"] : 10;
    $codigo_articulo = (isset($conf["codigo_articulo"])) ? urldecode($conf["codigo_articulo"]) : "";
    $codigos_articulos = (isset($conf["codigos_articulos"])) ? $conf["codigos_articulos"] : "";
    $limit = (isset($conf["limit"])) ? $conf["limit"] : 0;
    $anulada = (isset($conf["anulada"])) ? $conf["anulada"] : 0;
    $id_tipo_estado = (isset($conf["id_tipo_estado"])) ? $conf["id_tipo_estado"] : 0;
    $in_tipos_estados = (isset($conf["in_tipos_estados"])) ? $conf["in_tipos_estados"] : 0;

    $sql = "SELECT FI.id_articulo AS id, ";
    $sql.= " IF(A.codigo IS NULL,'',A.codigo) AS codigo, ";
    $sql.= " IF(A.codigo_barra IS NULL,'',A.codigo_barra) AS codigo_barra, ";
    $sql.= " SUM(FI.cantidad) AS cantidad, ";
    $sql.= " IF(MAX(FI.custom_2) IS NULL,'',MAX(FI.custom_2)) AS custom_2, "; // 1 = Tiene descuento | 0 = No tiene descuento
    $sql.= " FI.nombre AS nombre_item, ";
    $sql.= " SUM(FI.total_con_iva) AS total_final, ";
    $sql.= " SUM(FI.costo_final) AS costo_final ";
    $sql.= "FROM facturas F ";
    $sql.= "INNER JOIN facturas_items FI ON (FI.id_empresa = F.id_empresa AND FI.id_punto_venta = F.id_punto_venta AND F.id = FI.id_factura) ";
    $sql.= "LEFT JOIN articulos A ON (A.id_empresa = FI.id_empresa AND A.id = FI.id_articulo) ";
    $sql.= "WHERE F.id_empresa = $id_empresa ";
    if ($anulada == 0) $sql.= "AND FI.anulado = 0 AND F.anulada = 0 ";
    $sql.= "AND F.tipo != 'C' ";
    $sql.= "AND F.fecha >= '$desde' AND F.fecha <= '$hasta' ";
    if (!empty($id_articulo)) $sql.= "AND FI.id_articulo = '$id_articulo' ";
    if ($id_punto_venta != -1) $sql.= "AND FI.id_punto_venta = $id_punto_venta ";
    if (!empty($id_sucursal)) $sql.= "AND F.id_sucursal = $id_sucursal ";
    if (!empty($en_oferta)) $sql.= "AND FI.custom_2 = '1' ";
    if (!empty($ids_articulos)) {
      $ids_articulos = str_replace("-", ",", $ids_articulos);
      $sql.= "AND FI.id_articulo IN ($ids_articulo) ";
    }
    if (!empty($codigos_articulos)) {
      if (is_array($codigos_articulos)) {
        $codigos = explode("-", $codigos_articulos);
        foreach($codigos as $cod) {
          $cod = "'".$cod."'";
        }
        $codigos_articulos = implode(",", $codigos);        
      } else if (is_string($codigos_articulos)) {
        $codigos_articulos = str_replace("-", ",", $codigos_articulos);
      }
      $sql.= "AND A.codigo IN ($codigos_articulos) ";
    }
    if (!empty($in_tipos_estados)) $sql.= "AND F.id_tipo_estado IN ($in_tipos_estados) ";
    $sql.= "GROUP BY FI.id_articulo ";
    //$sql.= "ORDER BY $order_by $order ";
    //$sql.= "LIMIT $limit, $offset ";
    $this->sql = $sql;
    $q = $this->db->query($sql);

    return array(
      "results"=>$q->result(),
    );
  }

  
  function listado($conf = array()) {

    $resultado = array(
      "results"=>array(),
      "total"=>0,
      "meta"=>array(),
    );
    
    $id_empresa = (isset($conf["id_empresa"])) ? $conf["id_empresa"] : parent::get_empresa();
    $id_proyecto = (isset($conf["id_proyecto"])) ? $conf["id_proyecto"] : 0;
    $id = (isset($conf["id"])) ? $conf["id"] : 0;
    $incluir_suma = (isset($conf["incluir_suma"])) ? $conf["incluir_suma"] : 0;
    $desde = (isset($conf["desde"])) ? $conf["desde"] : "";
    $hasta = (isset($conf["hasta"])) ? $conf["hasta"] : "";
    $caja_abierta = (isset($conf["caja_abierta"])) ? $conf["caja_abierta"] : 0;
    $tipo_cliente = (isset($conf["tipo_cliente"])) ? $conf["tipo_cliente"] : "";
    $monto = (isset($conf["monto"])) ? $conf["monto"] : "";
    $monto_tipo = (isset($conf["monto_tipo"])) ? $conf["monto_tipo"] : "";
    $tipo = (isset($conf["tipo"])) ? $conf["tipo"] : "";
    $forma_pago = (isset($conf["forma_pago"])) ? $conf["forma_pago"] : "";
    $id_cliente = (isset($conf["id_cliente"])) ? $conf["id_cliente"] : 0;
    $id_origen = (isset($conf["id_origen"])) ? $conf["id_origen"] : -1;
    $id_vendedor = (isset($conf["id_vendedor"])) ? $conf["id_vendedor"] : 0;
    $id_concepto = (isset($conf["id_concepto"])) ? $conf["id_concepto"] : 0;
    $id_sucursal = (isset($conf["id_sucursal"])) ? $conf["id_sucursal"] : 0;
    $id_punto_venta = (isset($conf["id_punto_venta"])) ? $conf["id_punto_venta"] : -1;
    $in_ids_punto_venta = (isset($conf["in_ids_punto_venta"])) ? $conf["in_ids_punto_venta"] : "";
    $not_in_ids_punto_venta = (isset($conf["not_in_ids_punto_venta"])) ? $conf["not_in_ids_punto_venta"] : "";
    $tipo_estado = (isset($conf["tipo_estado"])) ? $conf["tipo_estado"] : -1;
    $in_tipos_estados = (isset($conf["in_tipos_estados"])) ? $conf["in_tipos_estados"] : "";
    $not_in_tipos_estados = (isset($conf["not_in_tipos_estados"])) ? $conf["not_in_tipos_estados"] : "";
    $id_tarjeta = (isset($conf["id_tarjeta"])) ? $conf["id_tarjeta"] : 0;
    $lote = (isset($conf["lote"])) ? $conf["lote"] : "";
    $cupon = (isset($conf["cupon"])) ? $conf["cupon"] : "";
    $id_usuario = (isset($conf["id_usuario"])) ? $conf["id_usuario"] : 0;
    $numero = (isset($conf["numero"])) ? $conf["numero"] : "";
    $numero_reparto = (isset($conf["numero_reparto"])) ? $conf["numero_reparto"] : "";
    $fecha_reparto = (isset($conf["fecha_reparto"])) ? $conf["fecha_reparto"] : "";
    $incluir_saldo = (isset($conf["incluir_saldo"])) ? $conf["incluir_saldo"] : 0;
    $con_anulados = (isset($conf["con_anulados"])) ? $conf["con_anulados"] : 0;
    $estado = (isset($conf["estado"])) ? $conf["estado"] : 0;
    $pago = (isset($conf["pago"])) ? $conf["pago"] : -1;
    $tipos_comprobantes = (isset($conf["tc"])) ? $conf["tc"] : "";
    $tipos = (isset($conf["tipos"])) ? $conf["tipos"] : "";
    $hora_desde = (isset($conf["hora_desde"])) ? $conf["hora_desde"] : "";
    $hora_hasta = (isset($conf["hora_hasta"])) ? $conf["hora_hasta"] : "";
    $custom_10 = (isset($conf["custom_10"])) ? $conf["custom_10"] : "";
    $custom_orden = (isset($conf["custom_orden"])) ? $conf["custom_orden"] : "";
    $limit = (isset($conf["limit"])) ? $conf["limit"] : 0;
    $offset = (isset($conf["offset"])) ? $conf["offset"] : 10;
    $filter = (isset($conf["filter"])) ? $conf["filter"] : "";

    $this->load->model("Empresa_Model");

    if ($id_empresa == 45) $order = "F.fecha DESC, F.numero DESC ";
    else if ($this->Empresa_Model->es_toque($id_empresa)) $order = "F.fecha DESC, F.hora DESC, F.numero DESC ";
    else $order = "F.fecha DESC, F.hora DESC ";

    $codigo_articulo = (isset($conf["codigo_articulo"])) ? urldecode($conf["codigo_articulo"]) : "";

    // CASO ESPECIAL YEYO, PUEDE VER MAS DE UNA EMPRESA
    $in_ids_empresas = "";
    if ($id_empresa == 980) {
      $in_ids_empresas = implode(",", $this->Empresa_Model->get_ids_empresas_por_vendedor($this->Empresa_Model->get_id_vendedor_don_yeyo()));
    }
    
    $this->load->helper("fecha_helper");
    if (!empty($desde)) $desde = fecha_mysql($desde);
    if (!empty($hasta)) $hasta = fecha_mysql($hasta);
    if (!empty($fecha_reparto)) $fecha_reparto = fecha_mysql($fecha_reparto);

    // Consultamos la cantidad de clientes y la cantidad de facturas de ese reparto        
    if (!empty($numero_reparto) || !empty($fecha_reparto)) {
      $sql = "SELECT IF(COUNT(*) IS NULL,0,COUNT(*)) AS cantidad FROM facturas F WHERE F.anulada = 0 ";
      $sql.= "AND F.fecha_reparto = '$fecha_reparto' ";
      $sql.= "AND F.reparto = $numero_reparto ";
      if (empty($in_ids_empresas)) $sql.= "AND F.id_empresa = $id_empresa ";
      else $sql.= "AND F.id_empresa IN ($in_ids_empresas) ";
      $q = $this->db->query($sql);
      $r_facturas = $q->row();      
      $resultado["meta"]["cantidad_facturas"] = (empty($r_facturas)) ? 0 : $r_facturas->cantidad;
    }
    if (!empty($numero_reparto) || !empty($fecha_reparto)) {
      $sql = "SELECT IF(COUNT(DISTINCT id_cliente) IS NULL,0,COUNT(DISTINCT id_cliente)) AS cantidad FROM facturas F WHERE F.anulada = 0 ";
      $sql.= "AND F.fecha_reparto = '$fecha_reparto' ";
      $sql.= "AND F.reparto = $numero_reparto ";
      if (empty($in_ids_empresas)) $sql.= "AND F.id_empresa = $id_empresa ";
      else $sql.= "AND F.id_empresa IN ($in_ids_empresas) ";
      $q = $this->db->query($sql);
      $r_clientes = $q->row();        
      $resultado["meta"]["cantidad_clientes"] = (empty($r_clientes)) ? 0 : $r_clientes->cantidad;
    }

    // RESTOVAR
    if ($id_proyecto == 10) {

      $sql = "SELECT SQL_CALC_FOUND_ROWS F.*, ";
      $sql.= "TC.negativo AS negativo, ";
      $sql.= "IF(F.fecha='0000-00-00','',DATE_FORMAT(F.fecha,'%d/%m/%Y')) AS fecha, ";
      $sql.= "IF(F.hora='00:00:00','',DATE_FORMAT(F.hora,'%H:%i:%s')) AS hora, ";
      $sql.= "IF(C.nombre IS NULL,'',C.nombre) AS cliente, ";
      $sql.= "IF(C.direccion IS NULL,'',C.direccion) AS direccion, ";
      if ($this->Empresa_Model->es_toque($id_empresa)) $sql.= "CONCAT('549',C.telefono,C.celular) AS telefono, ";
      else $sql.= "IF(C.telefono IS NULL,'',C.telefono) AS telefono, ";
      $sql.= "IF(V.telefono IS NULL,'',V.telefono) AS vendedor_telefono ";
      $sql_where = "FROM facturas F ";
      $sql_where.= "INNER JOIN tipos_comprobante TC ON (F.id_tipo_comprobante = TC.id) ";
      $sql_where.= "LEFT JOIN clientes C ON (F.id_cliente = C.id AND F.id_empresa = C.id_empresa) ";
      if ($this->Empresa_Model->es_toque($id_empresa)) $sql_where.= "LEFT JOIN repartidores V ON (F.id_vendedor = V.id AND F.id_empresa = V.id_empresa) ";
      else $sql_where.= "LEFT JOIN vendedores V ON (F.id_vendedor = V.id AND F.id_empresa = V.id_empresa) ";
      $sql_where.= "WHERE F.tipo != 'P' ";
      if (empty($in_ids_empresas)) $sql_where.= "AND F.id_empresa = $id_empresa ";
      else $sql_where.= "AND F.id_empresa IN ($in_ids_empresas) ";      
      if ($estado == 0) $sql_where.= "AND F.estado = $estado ";
      if (!empty($filter)) $sql_where.= "AND (C.nombre LIKE '%$filter%' OR F.comprobante LIKE '%$filter') ";
      if (!empty($desde)) $sql_where.= "AND F.fecha >= '$desde' ";
      if (!empty($hasta)) $sql_where.= "AND F.fecha <= '$hasta' ";
      if (!empty($tipo)) $sql_where.= "AND F.tipo = '$tipo' ";
      if (!empty($id_cliente)) $sql_where.= "AND F.id_cliente = $id_cliente ";
      if ($id_punto_venta != -1) $sql_where.= "AND F.id_punto_venta = $id_punto_venta ";
      if (!empty($id_vendedor)) {
        if ($id_vendedor == -1) $sql_where.= "AND F.id_vendedor = 0 ";
        else $sql_where.= "AND F.id_vendedor = $id_vendedor ";
      } 
      if (!empty($id_concepto)) $sql_where.= "AND F.id_concepto = $id_concepto ";
      if (!empty($id_usuario)) $sql_where.= "AND F.id_usuario = $id_usuario ";
      if ($caja_abierta == 1) $sql_where.= "AND F.id_caja_diaria = 0 ";
      if (!empty($tipos)) {
        $tipos_array = explode("-",$tipos);
        $nuevo_array = array();
        foreach($tipos_array as $tipo) $nuevo_array[] = "'$tipo'";
        $tipos = implode(",", $nuevo_array);
        $sql_where.= "AND F.tipo IN ($tipos) ";
      }
      if ($this->Empresa_Model->es_toque($id_empresa) && $in_tipos_estados != "M1" && $con_anulados != 2) {
        // TOQUE tenemos que filtrar los pedidos en id_tipo_estado = 0, porque todavia estan en proceso
        // y los comercios no lo tendrian que ver
        $sql_where.= "AND F.id_tipo_estado >= 0 ";
      }

    } else {

      $sql = "SELECT F.*, ";
      $sql.= "TC.negativo AS negativo, ";
      $sql.= "U.nombre as nombre_profesional, U.apellido as apellido_profesional, ";
      $sql.= "F.codigo_reclamado, F.id_profesional, F.codigo_activacion, ";
      $sql.= "IF(F.fecha_activacion IS NULL,'',F.fecha_activacion) AS fecha_activacion, ";
      $sql.= "IF(C.telefono IS NULL,'',C.telefono) AS telefono, ";
      $sql.= "IF(C.codigo IS NULL,'',C.codigo) AS codigo_cliente, ";
      $sql.= "IF(C.direccion IS NULL,'',C.direccion) AS direccion, ";
      $sql.= "IF(F.fecha='0000-00-00','',DATE_FORMAT(F.fecha,'%d/%m/%Y')) AS fecha, ";
      $sql.= "IF(F.fecha_reparto='0000-00-00','',DATE_FORMAT(F.fecha_reparto,'%d/%m/%Y')) AS fecha_reparto, ";
      $sql.= "IF(F.fecha_vto='0000-00-00','',DATE_FORMAT(F.fecha_vto,'%d/%m/%Y')) AS fecha_vto, ";
      $sql.= "PV.tipo_impresion, ";
      $sql.= "IF(TP.nombre IS NULL,'',TP.nombre) AS concepto, ";
      $sql.= "IF(F.hora='00:00:00','',DATE_FORMAT(F.hora,'%H:%i:%s')) AS hora ";
      $sql_where = "FROM facturas F ";
      $sql_where.= "INNER JOIN tipos_comprobante TC ON (F.id_tipo_comprobante = TC.id) ";
      $sql_where.= "INNER JOIN puntos_venta PV ON (F.id_empresa = PV.id_empresa AND F.id_punto_venta = PV.id) ";
      $sql_where.= "LEFT JOIN clientes C ON (F.id_cliente = C.id AND F.id_empresa = C.id_empresa) ";
      $sql_where.= "LEFT JOIN com_usuarios U ON (F.id_profesional = U.id AND F.id_empresa = U.id_empresa) ";
      $sql_where.= "LEFT JOIN tipos_gastos TP ON (F.id_concepto = TP.id AND F.id_empresa = TP.id_empresa) ";
      $sql_where.= "WHERE F.tipo != 'P' ";
      if (empty($in_ids_empresas)) $sql_where.= "AND F.id_empresa = $id_empresa ";
      else $sql_where.= "AND F.id_empresa IN ($in_ids_empresas) ";      
      if ($estado == 0) $sql_where.= "AND F.estado = $estado ";
      if (!empty($filter)) {
        if (is_numeric($filter)) $sql_where.= "AND F.numero = '$filter' ";
        else {
          $f = explode(" ", $filter);
          $filter = "";
          foreach($f as $ff) $filter.= "+".$ff."* ";
          $sql_where.= "AND MATCH (F.cliente) AGAINST ('".$filter."' IN BOOLEAN MODE) ";
        }
      }
      if (!empty($desde)) $sql_where.= "AND F.fecha >= '$desde' ";
      if (!empty($hasta)) $sql_where.= "AND F.fecha <= '$hasta' ";
      if (!empty($id_cliente)) $sql_where.= "AND F.id_cliente = $id_cliente ";
      if (!empty($id_vendedor)) $sql_where.= "AND F.id_vendedor = $id_vendedor ";
      if (!empty($id_concepto)) $sql_where.= "AND F.id_concepto = $id_concepto ";
      if ($id_punto_venta != -1) $sql_where.= "AND F.id_punto_venta = $id_punto_venta ";
      if (!empty($id_sucursal)) $sql_where.= "AND F.id_sucursal = $id_sucursal ";
      if (!empty($id_usuario)) $sql_where.= "AND F.id_usuario = $id_usuario ";
      if (!empty($numero)) $sql_where.= "AND F.comprobante LIKE '%$numero%' ";
      if (!empty($numero_reparto)) $sql_where.= "AND F.reparto = $numero_reparto ";
      if (!empty($numero_reparto)) $sql_where.= "AND F.anulada = 0 "; // Si se consulta por numero de reparto, no se muestran las anuladas
      if (!empty($fecha_reparto)) $sql_where.= "AND F.fecha_reparto = '$fecha_reparto' ";
      if (!empty($tipo)) $sql_where.= "AND F.tipo = '$tipo' ";
      if (!empty($monto) && !empty($monto_tipo)) {
        if ($monto_tipo == "mayor") $monto_tipo = ">";
        else if ($monto_tipo == "menor") $monto_tipo = "<";
        else $monto_tipo = "=";
        $sql_where.= "AND F.total $monto_tipo '$monto' ";
      }
      if ($caja_abierta == 1) $sql_where.= "AND F.id_caja_diaria = 0 ";
      if (!empty($tipos_comprobantes)) {
        $tipos_comprobantes = str_replace("-",",",$tipos_comprobantes);
        $sql_where.= "AND F.id_tipo_comprobante IN ($tipos_comprobantes) ";
      }      
    }

    // Buscamos por un ID en particular
    if (!empty($id)) $sql_where.= "AND F.id = $id ";

    if ($con_anulados == 1) $sql_where.= "AND F.id_tipo_estado = -1 ";
    else if ($con_anulados == 2) $sql_where.= "AND F.anulada = 1 ";
    else if ($con_anulados == 3) $sql_where.= "AND F.anulada = 0 ";

    if ($pago == 1) $sql_where.= "AND F.pagada = 1 ";
    else if ($pago == 0) $sql_where.= "AND F.pagada = 0 ";

    if (!empty($hora_desde)) $sql_where.= "AND F.hora >= '$hora_desde' ";
    if (!empty($hora_hasta)) $sql_where.= "AND F.hora <= '$hora_hasta' ";

    if (!empty($custom_10)) $sql_where.= "AND F.custom_10 = '$custom_10' ";

    if (!empty($in_ids_punto_venta)) $sql_where.= "AND F.id_punto_venta IN ($in_ids_punto_venta) ";
    if (!empty($not_in_ids_punto_venta)) $sql_where.= "AND F.id_punto_venta NOT IN ($not_in_ids_punto_venta) ";

    if ($id_tarjeta != 0 || !empty($lote) || !empty($cupon)) {
      $sql_where.= "AND EXISTS (";
      $sql_where.= " SELECT 1 FROM cupones_tarjetas CT WHERE ";
      $sql_where.= " CT.id_empresa = F.id_empresa AND CT.id_punto_venta = F.id_punto_venta AND CT.id_factura = F.id ";
      if ($id_tarjeta != 0) $sql_where.= " AND CT.id_tarjeta = $id_tarjeta ";
      if ($lote != "") $sql_where.= "AND CT.lote = '$lote' ";
      if ($cupon != "") $sql_where.= "AND CT.cupon = '$cupon' ";
      $sql_where.= ") ";
    }
    if (!empty($codigo_articulo)) {
      $sql_where.= "AND EXISTS (";
      $sql_where.= " SELECT 1 FROM facturas_items FI INNER JOIN articulos A ON (FI.id_articulo = A.id AND FI.id_empresa = A.id_empresa) WHERE ";
      $sql_where.= " FI.id_empresa = F.id_empresa AND FI.id_punto_venta = F.id_punto_venta AND FI.id_factura = F.id ";
      $sql_where.= " AND A.codigo = '$codigo_articulo' ";
      $sql_where.= ") ";
    }

    if ($forma_pago == "E") $sql_where.= "AND F.efectivo != 0 ";
    else if ($forma_pago == "C") $sql_where.= "AND F.cta_cte != 0 ";
    else if ($forma_pago == "T") $sql_where.= "AND F.tarjeta != 0 ";
    else if ($forma_pago == "H") $sql_where.= "AND F.cheque != 0 ";

    if ($tipo_cliente == "CF") $sql_where.= "AND F.id_cliente = 0 ";
    else if ($tipo_cliente == "NCF") $sql_where.= "AND F.id_cliente != 0 ";

    if ($id_origen != -1) $sql_where.= "AND F.id_origen = $id_origen ";

    if ($tipo_estado != -1) $sql_where.= "AND F.id_tipo_estado = $tipo_estado ";
    if (!empty($in_tipos_estados)) {
      $in_tipos_estados = str_replace("M", "-", $in_tipos_estados);
      $sql_where.= "AND F.id_tipo_estado IN ($in_tipos_estados) ";
    }
    if (!empty($not_in_tipos_estados)) {
      $sql_where.= "AND F.id_tipo_estado NOT IN ($not_in_tipos_estados) ";
    }

    if ($custom_orden == "pedidos_listos") $sql_where.= "AND F.codigo_postal != '' ";

    $sql = $sql.$sql_where;

    // ORDENES ESPECIALES
    if ($custom_orden == "mas_retrasados") {
      // Orden especial para TOQUE
      $sql.= "ORDER BY F.vencimiento ASC ";
    } else if ($custom_orden == "pedidos_listos") {
      // Orden especial para TOQUE
      $sql.= "ORDER BY F.codigo_postal DESC ";
    } else if (!empty($order)) {
      $sql.= "ORDER BY $order ";
    }

    if ($limit !== FALSE) $sql.= "LIMIT $limit,$offset ";
    $sql_2 = $sql;
    $q = $this->db->query($sql);
    $lista = $q->result();
    
    if ($incluir_saldo == 1) {
      $this->load->model("Cliente_Model");
      foreach($lista as $l) {
        $l->saldo = $this->Cliente_Model->saldo($l->id_cliente,$id_empresa,date("Y-m-d"));
      }
    }

    // Toque devuelve los pedidos en el mismo listado
    // o tiene configurada la columna "items"
    $this->load->model("Configuracion_Model");
    $tiene_columna_items = $this->Configuracion_Model->tiene_activa_columna(array(
      "id_empresa"=>$id_empresa,
      "tabla"=>"ventas",
      "campo"=>"items",
    ));
    if ($this->Empresa_Model->es_toque($id_empresa) || $tiene_columna_items) {
      foreach($lista as $l) {
        $sql = "SELECT * FROM facturas_items WHERE id_empresa = $l->id_empresa AND id_factura = $l->id AND id_punto_venta = $l->id_punto_venta";
        $qq = $this->db->query($sql);
        $l->items = $qq->result();

        if ($this->Empresa_Model->es_toque($id_empresa)) {
          // Tiempos que fue marcando el repartidor con la APP
          $l->en_comercio = ""; // Cuando llega al comercio
          $l->retirado = ""; // Cuando realmente le entregan la mercaderia
          $l->entregado = ""; // Cuando se entraga finalmente al cliente
          if ($l->id_tipo_estado >= 3 && $l->id_tipo_estado <= 6 
            && $id_empresa == 571) {

            // Retirado por el comercio
            $sql = "SELECT fecha FROM repartidores_pedidos WHERE id_factura = $l->id AND id_empresa = $id_empresa AND id_punto_venta = $l->id_punto_venta ";
            $sql.= "AND estado = 'L' ";
            $qq = $this->db->query($sql);      
            if ($qq->num_rows() > 0) {
              $rr = $qq->row();
              $l->retirado = $rr->fecha;
            }

            // Repartidor en el comercio
            $sql = "SELECT fecha FROM repartidores_pedidos WHERE id_factura = $l->id AND id_empresa = $id_empresa AND id_punto_venta = $l->id_punto_venta ";
            $sql.= "AND estado = 'Y' ";
            $qq = $this->db->query($sql);      
            if ($qq->num_rows() > 0) {
              $rr = $qq->row();
              $l->en_comercio = $rr->fecha;
            }

            // Entregado al cliente
            $sql = "SELECT fecha FROM repartidores_pedidos WHERE id_factura = $l->id AND id_empresa = $id_empresa AND id_punto_venta = $l->id_punto_venta ";
            $sql.= "AND estado = 'F' ";
            $qq = $this->db->query($sql);
            if ($qq->num_rows() > 0) {
              $rr = $qq->row();
              $l->entregado = $rr->fecha;
            }
          }
        }

      }
    }
    
    $q_total = $this->db->query("SELECT IF(COUNT(*) IS NULL,0,COUNT(*)) AS total ".$sql_where);
    $total = $q_total->row();

    $suma = 0;
    if ($incluir_suma == 1) {
      $sql_suma = "SELECT SUM(IF(TC.negativo = 1,-ABS(F.total)-ABS(F.percep_viajes),F.total+F.percep_viajes)) AS suma ".$sql_where;
      $q_suma = $this->db->query($sql_suma);
      $suma_o = $q_suma->row();
      $suma = $suma_o->suma;
    }
    $resultado["meta"]["cantidad"] = $total->total;
    $resultado["meta"]["suma"] = $suma;
    $resultado["total"] = $total->total;
    $resultado["results"] = $lista;
    $resultado["sql"] = $sql_2;
    return $resultado;
  }
  
  function totales($conf = array()) {
    
    $id_empresa = parent::get_empresa();
    
    $salida = array();
    $fecha_desde = isset($conf["fecha_desde"]) ? $conf["fecha_desde"] : date("Y-m-d");
    $fecha_hasta = isset($conf["fecha_hasta"]) ? $conf["fecha_hasta"] : date("Y-m-d");
    $agrupado_por = isset($conf["agrupado_por"]) ? $conf["agrupado_por"] : "";
    $limit = isset($conf["limit"]) ? $conf["limit"] : 0;
    $offset = isset($conf["offset"]) ? $conf["offset"] : 10;
    $estado = (!isset($_SESSION["estado"])) ? 0 : (($_SESSION["estado"]==1)?1:0);
    
    // Dependiendo de como se agrupe la consulta
    if (empty($agrupado_por)) {
      
      $sql_concepto = "'Totales' AS concepto";
      $sql_inner = "";
      $sql_group_by = "";
      
    } else if ($agrupado_por == "clientes") {
      
      $sql_concepto = "IF(C.nombre IS NULL,'Consumidor Final',C.nombre) AS concepto";
      $sql_inner = "LEFT JOIN clientes C ON (F.id_cliente = C.id AND F.id_empresa = C.id_empresa) ";
      $sql_group_by = "GROUP BY F.id_cliente ";
      
    } else if ($agrupado_por == "vendedores") {
      
      $sql_concepto = "IF (V.nombre IS NULL,'Vendedor no definido',V.nombre) AS concepto";
      $sql_inner = "LEFT JOIN vendedores V ON (F.id_vendedor = V.id AND F.id_empresa = V.id_empresa) ";
      $sql_group_by = "GROUP BY F.id_vendedor ";
      
    } else if ($agrupado_por == "articulos") {
      
      $sql_concepto = "IF(A.nombre IS NULL,'Articulo no definido',A.nombre) AS concepto";
      $sql_inner = "LEFT JOIN articulos A ON (FI.id_articulo = A.id) ";
      $sql_group_by = "GROUP BY FI.id_articulo ";
      
    } else if ($agrupado_por == "rubros") {
      
      $sql_concepto = "IF (R.nombre IS NULL,'Rubro no definido',R.nombre) AS concepto";
      $sql_inner = "LEFT JOIN rubros R ON (FI.id_rubro = R.id) ";
      $sql_group_by = "GROUP BY FI.id_rubro ";
      
    }

    $sql = "SELECT SQL_CALC_FOUND_ROWS ";
    $sql.= $sql_concepto.", ";
    $sql.= " IF (SUM(FI.total_con_iva) IS NULL,0,SUM(FI.total_con_iva)) AS total, ";
    $sql.= " IF (SUM(FI.total_sin_iva) IS NULL,0,SUM(FI.total_sin_iva)) AS neto, ";
    $sql.= " IF (SUM(FI.iva) IS NULL,0,SUM(FI.iva)) AS iva ";
    $sql.= "FROM facturas_items FI INNER JOIN facturas F ON (FI.id_factura = F.id AND F.id_punto_venta = FI.id_punto_venta AND F.id_empresa = FI.id_empresa) ";
    $sql.= $sql_inner." ";
    $sql.= "WHERE FI.id_empresa = $id_empresa ";
    if ($estado == 0) $sql.= "AND F.estado = $estado ";
    $sql.= "AND '$fecha_desde' <= F.fecha AND F.fecha <= '$fecha_hasta' ";
    $sql.= "AND F.tipo != 'P' ";
    //$sql.= "AND F.id_punto_venta IN (284,285) ";
    $sql.= "AND F.anulada = 0 ";
    $sql.= "AND F.pendiente = 0 ";
    $sql.= "AND F.id_tipo_comprobante > 0 ";
    $sql.= $sql_group_by." ";
    $sql.= "ORDER BY concepto ASC ";
    $sql.= "LIMIT $limit, $offset ";
    
    $q = $this->db->query($sql);
    
    $q_total = $this->db->query("SELECT FOUND_ROWS() AS total");
    $total = $q_total->row();
    return array(
      "results"=>$q->result(),
      "total"=>$total->total,
      );
  }

  function get_total($conf = array()) {

    $id_empresa = isset($conf["id_empresa"]) ? $conf["id_empresa"] : parent::get_empresa();
    $estado = isset($conf["estado"]) ? $conf["estado"] : 0;
    $desde = isset($conf["desde"]) ? $conf["desde"] : date("Y-m-d");
    $hasta = isset($conf["hasta"]) ? $conf["hasta"] : date("Y-m-d");
    $id_punto_venta = isset($conf["id_punto_venta"]) ? $conf["id_punto_venta"] : -1;
    $id_sucursal = isset($conf["id_sucursal"]) ? $conf["id_sucursal"] : 0;
    $in_sucursales = isset($conf["in_sucursales"]) ? $conf["in_sucursales"] : "";

    $sql = "SELECT SUM(CASE WHEN TC.negativo = 0 THEN F.total ELSE -F.total END) AS total ";
    $sql.= "FROM facturas F ";
    $sql.= " LEFT JOIN tipos_comprobante TC ON (F.id_tipo_comprobante = TC.id) ";
    $sql.= "WHERE F.id_empresa = $id_empresa ";
    $sql.= "AND F.tipo != 'P' ";
    $sql.= "AND F.anulada = 0 ";
    $sql.= "AND F.pendiente = 0 ";
    $sql.= "AND F.id_tipo_comprobante > 0 ";
    if (!empty($id_sucursal)) $sql.= "AND F.id_sucursal = $id_sucursal ";
    if (!empty($in_sucursales)) $sql.= "AND F.id_sucursal IN ($in_sucursales) ";
    if ($id_punto_venta != -1) $sql.= "AND F.id_punto_venta = $id_punto_venta ";
    else $sql.= "AND F.id_punto_venta != 0 ";
    $sql.= "AND '$desde' <= F.fecha AND F.fecha <= '$hasta' ";
    if ($estado == 0) $sql.= "AND F.id_tipo_comprobante < 900 ";
    $q = $this->db->query($sql);
    $row = $q->row();
    if (is_null($row->total)) return 0;
    else return (float)$row->total;
  }

  function resumen($conf = array()) {

    $id_empresa = isset($conf["id_empresa"]) ? $conf["id_empresa"] : parent::get_empresa();
    $estado = isset($conf["estado"]) ? $conf["estado"] : 0;
    $desde = isset($conf["desde"]) ? $conf["desde"] : date("Y-m-d");
    $hasta = isset($conf["hasta"]) ? $conf["hasta"] : date("Y-m-d");
    $tipo_cliente = (isset($conf["tipo_cliente"])) ? $conf["tipo_cliente"] : "";

    $sql = "SELECT SUM(CASE WHEN TC.negativo = 0 THEN F.total ELSE -F.total END) AS total, ";
    $sql.= " IF(COUNT(*) IS NULL,0,COUNT(*)) AS cantidad ";
    $sql.= "FROM facturas F ";
    $sql.= " LEFT JOIN tipos_comprobante TC ON (F.id_tipo_comprobante = TC.id) ";
    $sql.= "WHERE F.id_empresa = $id_empresa ";
    $sql.= "AND F.tipo != 'P' ";
    $sql.= "AND F.anulada = 0 ";
    $sql.= "AND F.pendiente = 0 ";
    $sql.= "AND F.id_tipo_comprobante > 0 ";
    $sql.= "AND F.id_punto_venta != 0 ";
    $sql.= "AND '$desde' <= F.fecha AND F.fecha <= '$hasta' ";
    if ($tipo_cliente == "CF") $sql.= "AND F.id_cliente = 0 ";
    else if ($tipo_cliente == "NCF") $sql.= "AND F.id_cliente != 0 ";
    $q = $this->db->query($sql);
    return $q->row();
  }  



  function get_arbol($config = array()) {
    @session_start();
    $estado = isset($config["estado"]) ? $config["estado"] : ((!isset($_SESSION["estado"])) ? 0 : (($_SESSION["estado"]==1)?1:0));
    $id_padre = isset($config["id_padre"]) ? $config["id_padre"] : 0;
    $id_sucursal = isset($config["id_sucursal"]) ? $config["id_sucursal"] : 0;
    $id_razon_social = isset($config["id_razon_social"]) ? $config["id_razon_social"] : 0;
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
      $a = $this->resumen_ventas_por_concepto(array(
        "id_concepto"=>$row->id,
        "id_empresa"=>$id_empresa,
        "id_sucursal"=>$id_sucursal,
        "id_razon_social"=>$id_razon_social,
        "desde"=>$desde,
        "hasta"=>$hasta,
        "estado"=>$estado,
      ));
      $e->total = $a["total"];
      $e->neto = $a["neto"];
      $e->iva = $a["iva"];
      $e->percepcion_ib = $a["percepcion_ib"];
      $e->percep_viajes = $a["percep_viajes"];
      $this->total = $this->total + $e->total;
      $e->children = $this->get_arbol(array(
        "id_padre"=>$row->id,
        "id_empresa"=>$id_empresa,
        "id_sucursal"=>$id_sucursal,
        "id_razon_social"=>$id_razon_social,
        "desde"=>$desde,
        "hasta"=>$hasta,
        "estado"=>$estado,
      ));
      $elementos[] = $e;
    }
    return $elementos;  
  }

  function resumen_ventas_por_concepto($config = array()) {
    @session_start();
    $estado = isset($config["estado"]) ? $config["estado"] : ((!isset($_SESSION["estado"])) ? 0 : (($_SESSION["estado"]==1)?1:0));
    $id_concepto = isset($config["id_concepto"]) ? $config["id_concepto"] : 0;
    $id_sucursal = isset($config["id_sucursal"]) ? $config["id_sucursal"] : 0;
    $id_razon_social = isset($config["id_razon_social"]) ? $config["id_razon_social"] : 0;
    $desde = isset($config["desde"]) ? $config["desde"] : "";
    $hasta = isset($config["hasta"]) ? $config["hasta"] : "";
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();

    // Tomamos los hijos
    $sql = "SELECT * FROM tipos_gastos WHERE id_padre = $id_concepto AND id_empresa = $id_empresa";
    $q_hijos = $this->db->query($sql);
    $hijos = $q_hijos->result();

    // Calculamos el total de ese concepto
    $sql = "SELECT ";
    $sql.= "  SUM(IF(TC.negativo = 1,-1,1) * C.percepcion_ib) AS percepcion_ib, ";
    $sql.= "  SUM(IF(TC.negativo = 1,-1,1) * C.percep_viajes) AS percep_viajes, ";
    $sql.= "  SUM(IF(TC.negativo = 1,-1,1) * C.neto) AS neto, ";
    $sql.= "  SUM(IF(TC.negativo = 1,-1,1) * C.iva) AS iva, ";
    $sql.= "  SUM(IF(TC.negativo = 1,-1,1) * C.total) AS total ";
    $sql.= "FROM facturas C ";
    $sql.= "INNER JOIN tipos_comprobante TC ON (C.id_tipo_comprobante = TC.id) ";
    $sql.= "INNER JOIN tipos_gastos CO ON (C.id_concepto = CO.id AND C.id_empresa = CO.id_empresa) ";
    if ($id_razon_social != 0) $sql.= "LEFT JOIN almacenes ALM ON (C.id_sucursal = ALM.id AND C.id_empresa = ALM.id_empresa) ";
    $sql.= "WHERE C.id_concepto = $id_concepto ";
    $sql.= "AND C.id_empresa = $id_empresa ";
    if (!empty($desde)) $sql.= "AND C.fecha >= '$desde' ";
    if (!empty($hasta)) $sql.= "AND C.fecha <= '$hasta' ";
    if ($id_sucursal != 0) $sql.= "AND C.id_sucursal = $id_sucursal ";
    if ($id_razon_social != 0) $sql.= "AND ALM.id_razon_social = $id_razon_social ";
    if ($estado == 0) $sql.= "AND C.id_tipo_comprobante > 0 AND C.id_tipo_comprobante < 900 ";
    $q = $this->db->query($sql);
    if ($q->num_rows()>0) {
      $row = $q->row();
      $total = (float) $row->total;
      $iva = (float) $row->iva;
      $neto = (float) $row->neto;
      $percepcion_ib = (float) $row->percepcion_ib;
      $percep_viajes = (float) $row->percep_viajes;
    } else {
      $total = 0;
      $neto = 0;
      $iva = 0;
      $percepcion_ib = 0;
      $percep_viajes = 0;
    }

    // Calculamos el total de todos los hijos
    foreach($hijos as $hijo) {
      $a = $this->resumen_ventas_por_concepto(array(
        "id_concepto"=>$hijo->id,
        "id_empresa"=>$id_empresa,
        "id_sucursal"=>$id_sucursal,
        "id_razon_social"=>$id_razon_social,
        "desde"=>$desde,
        "hasta"=>$hasta,
        "estado"=>$estado,
      ));
      $neto  = $neto  + (float) $a["neto"];
      $iva   = $iva   + (float) $a["iva"];
      $percepcion_ib  = $percepcion_ib  + (float) $a["percepcion_ib"];
      $percep_viajes  = $percep_viajes  + (float) $a["percep_viajes"];
      $total = $total + (float) $a["total"];
    }
    return array(
      "total"=>$total,
      "neto"=>$neto,
      "iva"=>$iva,
      "percepcion_ib"=>$percepcion_ib,
      "percep_viajes"=>$percep_viajes,
    );
  }    
}