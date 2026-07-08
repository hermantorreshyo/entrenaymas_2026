<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Iva extends REST_Controller {

  function __construct() {
    parent::__construct();
  }
  
  function ventas($fecha_desde = "", $fecha_hasta = "", $pagina_desde = 0, $id_razon_social = 0) {

    $result = array();
    $id_empresa = $this->get_empresa();
    $this->load->helper("fecha_helper");
    $excel = parent::get_get("excel",0);
    $fecha_desde = fecha_mysql(str_replace("-","/",$fecha_desde));
    $fecha_hasta = fecha_mysql(str_replace("-","/",$fecha_hasta));
    $id_punto_venta = parent::get_get("id_punto_venta",0);
    $usa_zetas = 0;

    // Usado solamente en JAVIER
    // El CF lo sacamos por diferencia
    $neto_0_21_ri = 0;
    $neto_0_105_ri = 0;    
    $neto_0_21_mon = 0;
    $neto_0_105_mon = 0;    
    
    $this->load->model("Empresa_Model");
    $empresa = $this->Empresa_Model->get($id_empresa);

    if ($id_razon_social != 0) {
      $this->load->model("Razon_Social_Model");
      $razon = $this->Razon_Social_Model->get($id_razon_social);
      $empresa->razon_social = $razon->nombre;
      $empresa->direccion = $razon->direccion;
      $empresa->cuit = $razon->cuit;
    }    

    $sql = "SELECT F.*, ";
    $sql.= " IF(C.nombre IS NULL,'Consumidor Final',C.nombre) AS cliente, ";
    $sql.= " IF(C.id_tipo_documento IS NULL,'99',C.id_tipo_documento) AS id_tipo_documento, ";
    $sql.= " IF(C.cuit IS NULL,'',C.cuit) AS cuit, ";
    $sql.= " IF(C.id_tipo_iva IS NULL,4,C.id_tipo_iva) AS id_tipo_iva, ";
    $sql.= " IF(C.direccion IS NULL,'',C.direccion) AS domicilio_cliente, ";
    $sql.= " IF(TC.nombre IS NULL,'Ticket Z',TC.nombre) AS tipo_comprobante, ";
    $sql.= " IF(TC.negativo IS NULL,0,TC.negativo) AS negativo, ";
    $sql.= " IF(TC.letra IS NULL,'Z',TC.letra) AS letra, ";
    $sql.= " DATE_FORMAT(F.fecha,'%d/%m/%Y') AS fecha ";
    $sql.= "FROM zetas F ";
    $sql.= "LEFT JOIN clientes C ON (F.id_cliente = C.id AND F.id_empresa = C.id_empresa) ";
    $sql.= "LEFT JOIN tipos_comprobante TC ON (F.id_tipo_comprobante = TC.id) ";
    $sql.= "LEFT JOIN puntos_venta PV ON (F.id_punto_venta = PV.id AND F.id_empresa = PV.id_empresa) ";
    $sql.= "LEFT JOIN almacenes S ON (PV.id_sucursal = S.id AND PV.id_empresa = S.id_empresa) ";
    $sql.= "WHERE F.id_empresa = $id_empresa ";
    $sql.= "AND F.fecha >= '$fecha_desde' ";
    $sql.= "AND F.fecha <= '$fecha_hasta' ";
    if ($id_punto_venta > 0) $sql.= "AND F.id_punto_venta = $id_punto_venta ";
    if ($id_razon_social != 0) $sql.= "AND S.id_razon_social = $id_razon_social ";
    if ($id_empresa == 121) $sql.= "AND F.id_cliente != 60993 "; // Empleados cooperativa
    
    $sql.= "ORDER BY F.fecha ASC, F.punto_venta ASC ";
    $q = $this->db->query($sql);
    if ($q->num_rows()>0) {

      // TODO: Por ahora solo PELUNCHOS USA ZETAS, y tiene todo al 21%
      $iva_21 = new stdClass();
      $iva_21->id = 5;
      $iva_21->porcentaje = 21;
      $iva_21->nombre = "21%";
      $iva_105 = new stdClass();
      $iva_105->id = 4;
      $iva_105->porcentaje = 10.5;
      $iva_105->nombre = "10.5%";
      $iva_0 = new stdClass();
      $iva_0->id = 3;
      $iva_0->porcentaje = 0;
      $iva_0->nombre = "0%";
      $alicuotas = array($iva_21,$iva_105,$iva_0);
      $usa_zetas = 1;

      // TOMAMOS LOS ZETAS
      foreach($q->result() as $row) {

        $row->desde = $row->letra." ".str_pad($row->punto_venta, 4, "0", STR_PAD_LEFT)."-".str_pad($row->numero, 8, "0", STR_PAD_LEFT);
        $row->percepcion_ib = 0;
        $row->percep_viajes = 0;
        $row->pago = 0;
        $row->anulada = $row->anulada;
        $row->iva_5 = $row->iva;
        $row->neto_5 = $row->neto;
        $row->iva_4 = $row->iva_105;
        $row->neto_4 = $row->neto_105;
        $row->neto_3 = $row->neto_0;
        $row->iva_3 = 0;
        $row->subtotal = $row->neto;
        $result[] = $row;
      }
    }

    if ($usa_zetas == 0 || $id_empresa == 135) {

      // Tomamos las ventas directamente

      // Alicuotas de IVA utilizadas en ese periodo
      $sql = "SELECT DISTINCT TA.id, TA.nombre, TA.porcentaje ";
      $sql.= "FROM tipos_alicuotas_iva TA ";
      $sql.= "INNER JOIN facturas_iva FI ON (FI.id_alicuota_iva = TA.id) ";
      $sql.= "INNER JOIN facturas F ON (FI.id_factura = F.id AND FI.id_empresa = F.id_empresa AND FI.id_punto_venta = F.id_punto_venta) ";
      $sql.= "LEFT JOIN puntos_venta PV ON (F.id_punto_venta = PV.id AND F.id_empresa = PV.id_empresa) ";
      $sql.= "LEFT JOIN almacenes S ON (PV.id_sucursal = S.id AND PV.id_empresa = S.id_empresa) ";
      $sql.= "WHERE F.pendiente = 0 ";
      $sql.= "AND F.id_tipo_comprobante <= 300 AND F.id_tipo_comprobante > 0 ";
      $sql.= "AND F.fecha >= '$fecha_desde' ";
      $sql.= "AND F.fecha <= '$fecha_hasta' ";
      $sql.= "AND F.id_empresa = $id_empresa ";
      if ($id_punto_venta > 0) $sql.= "AND F.id_punto_venta = $id_punto_venta ";     
      if ($id_razon_social != 0) $sql.= "AND S.id_razon_social = $id_razon_social ";
      $q_alicuotas = $this->db->query($sql);
      $alicuotas = $q_alicuotas->result();

      $sql = "SELECT F.*, ";
      $sql.= " DATE_FORMAT(F.fecha,'%d/%m/%Y') AS fecha, ";
      $sql.= " TC.nombre AS tipo_comprobante, TC.letra, TC.negativo, ";
      $sql.= " IF(C.id IS NULL,'Consumidor Final',C.nombre) AS cliente, ";
      $sql.= " IF(C.id IS NULL,'',C.cuit) AS cuit, ";
      $sql.= " IF(C.direccion IS NULL,'',C.direccion) AS domicilio_cliente, ";
      $sql.= " IF(C.id IS NULL,'',C.id_tipo_documento) AS id_tipo_documento, ";
      $sql.= " IF(C.id IS NULL,4,C.id_tipo_iva) AS id_tipo_iva ";
      $sql.= "FROM facturas F ";
      $sql.= "INNER JOIN tipos_comprobante TC ON (F.id_tipo_comprobante = TC.id) ";
      $sql.= "LEFT JOIN clientes C ON (F.id_cliente = C.id AND F.id_empresa = C.id_empresa) ";
      $sql.= "LEFT JOIN puntos_venta PV ON (F.id_punto_venta = PV.id AND F.id_empresa = PV.id_empresa) ";
      $sql.= "LEFT JOIN almacenes S ON (PV.id_sucursal = S.id AND PV.id_empresa = S.id_empresa) ";      
      $sql.= "WHERE F.pendiente = 0 ";
      $sql.= "AND F.anulada = 0 ";
      $sql.= "AND F.id_tipo_comprobante <= 300 AND F.id_tipo_comprobante > 0 ";
      $sql.= "AND F.fecha >= '$fecha_desde' ";
      $sql.= "AND F.fecha <= '$fecha_hasta' ";
      $sql.= "AND F.id_empresa = $id_empresa ";
      if ($id_punto_venta > 0) $sql.= "AND F.id_punto_venta = $id_punto_venta ";
      if ($id_razon_social != 0) $sql.= "AND S.id_razon_social = $id_razon_social ";
      if ($id_empresa == 121) $sql.= "AND F.id_cliente != 60993 "; // Empleados cooperativa
      $sql.= "ORDER BY F.fecha ASC, F.punto_venta ASC, F.numero ASC ";
      $q = $this->db->query($sql);
      
      $anterior = new stdClass();
      $anterior->fecha = "";
      $anterior->id_punto_venta = -1;
      $anterior->id_cliente = -1;
      $anterior->id_tipo_iva = 0;

      $j=0;
      foreach($q->result() as $r) {

        foreach($alicuotas as $a) {
          $r->{"iva_".$a->id} = 0;
          $r->{"neto_".$a->id} = 0;
        }

        // IMPORTANTE: En facturas_iva, se guarda las alicuotas de IVA
        // y los netos de cada comprobante. Pero si la factura
        // tiene un descuento global, NO esta contemplado, por lo que
        // hay que calcularlo aparte para que sea correcto.
        
        $sql = "SELECT * FROM facturas_iva ";
        $sql.= "WHERE id_empresa = $id_empresa AND id_factura = $r->id AND id_punto_venta = $r->id_punto_venta ";
        $qq = $this->db->query($sql);
        foreach($qq->result() as $rr) {
          $r->{"iva_".$rr->id_alicuota_iva} += ($rr->iva * (100 - $r->porc_descuento)/100);
          $r->{"neto_".$rr->id_alicuota_iva} += ($rr->neto * (100 - $r->porc_descuento)/100);
        }

        if ($r->id_tipo_iva == 4 && $anterior->id_tipo_iva == 4 &&
            $r->id_cliente == 0 &&
            $r->fecha && $anterior->fecha &&
            $r->anulada == 0 &&
            $id_empresa != 264 && // TODO: Hacer esto dinamico de agrupar CF. MITRE NO AGRUPA
            $id_empresa != 135 && 
            $r->id_punto_venta && $anterior->id_punto_venta) {

          $anterior->total += $r->total;
          $anterior->subtotal += $r->subtotal;
          $anterior->neto += $r->neto;
          $anterior->iva += $r->iva;
          $anterior->percepcion_ib += $r->percepcion_ib;
          $anterior->percep_viajes += $r->percep_viajes;
          $anterior->pago += $r->pago;
          $anterior->hasta = $r->comprobante;
          foreach($alicuotas as $a) {
            $anterior->{"iva_".$a->id} += $r->{"iva_".$a->id};
            $anterior->{"neto_".$a->id} += $r->{"neto_".$a->id};
          }

        } else {

          if ($anterior->id_punto_venta != -1) $result[] = $anterior;
          $anterior = $r;
          $anterior->desde = $r->comprobante;

        }
        $j++;
      }

      // Agregamos el ultimo registro
      if ($j!=0) $result[] = $anterior;

    }    

    // Si tenemos que exportar a Excel
    if ($excel == 1) {

      $encabezado = array(
        "Letra",
        "Numero",
        "Tipo Comprobante",
        "Fecha",
        "Fecha Contable",
        "Razon Social",
        "Tipo Documento",
        "Documento",
        "Condicion IVA",
        "Nro. IIBB",
        "Provincia",
        "Sujeto Vinculado",
        "Operacion",
        "Calle",
        "Localidad",
        "Piso",
        "Departamento",
        "Codigo Postal",
        "Operacion Sujeto Vinculado",
        "Tipo Comprobante AFIP",
        "Operacion AFIP",
        "Iva No Computable",
        "Nro de Despacho",
        "Comprobante Electronico",
        "CAE",
        "Fecha Venc. CAE",
        "Operacion con Intermediario",
        "CUIT Intermediario",
        "Denominacion Intermediario",
        "Cotizacion",
        "Neto Gravado 21",
        "Iva 21",
        "Neto Gravado 10.5",
        "Iva 10.5",
        "Neto Gravado 27",
        "Iva 27",
        "Iva RNI 10.5",
        "Perc. IVA",
        "Perc. IIBB",
        "Exento",
        "No Gravado",
        "Imp. Interno",
        "Perc. Ret IIBB",
        "Total",
      );
      $datos = array();
      foreach($result as $r) {
        $o = new stdClass();
        $o->letra = $r->letra;
        $o->numero = substr($r->comprobante, strpos($r->comprobante," ")+1);
        if ($r->id_tipo_comprobante == 1 || $r->id_tipo_comprobante == 6 || $r->id_tipo_comprobante == 11) {
          $o->tipo_comprobante = "FCV";
        } else if ($r->tipo_comprobante == 2 || $r->id_tipo_comprobante == 7 || $r->id_tipo_comprobante == 12) {
          $o->tipo_comprobante = "NDV";
        } else if ($r->id_tipo_comprobante == 3 || $r->id_tipo_comprobante == 8 || $r->id_tipo_comprobante == 13) {
          $o->id_tipo_comprobante = "NCV";
        } else {
          $o->id_tipo_comprobante = "";
        }
        $o->fecha = $r->fecha;
        $o->fecha_contable = $r->fecha;
        $o->razon_social = $r->cliente;
        $o->tipo_documento = $r->id_tipo_documento;
        $o->documento = $r->cuit;
        if (strlen($o->documento) == 11 && strpos($o->documento, "-") === FALSE) {
          // Si tiene 11 caracteres pero no tiene guiones, se los agregamos
          $o->documento = substr($o->documento, 0, 2)."-".substr($o->documento, 2, 8)."-".substr($o->documento, 10, 1);
        }
        if ($r->id_tipo_iva == 1) {
          $o->condicion_iva = "RI";
        } else if ($r->id_tipo_iva == 2) {
          $o->condicion_iva = "RS";
        } else if ($r->id_tipo_iva == 3) {
          $o->condicion_iva = "EX";
        } else if ($r->id_tipo_iva == 4) {
          $o->condicion_iva = "CF";
        } else {
          $o->condicion_iva = "";
        }
        $o->iibb = "";
        $o->provincia = "01";
        $o->sujeto_vinculado = "N";
        $o->operacion = "01";
        $o->calle = $r->domicilio_cliente;
        $o->localidad = "";
        $o->piso = "";
        $o->depto = "";
        $o->codigo_postal = "";
        $o->operacion_sujeto_vinculado = "";
        $o->tipo_comprobante_afip = "001";
        $o->operacion_afip = "0";
        $o->iva_no_computable = "";
        $o->nro_despacho = "";
        $o->comprobante_electronico = "S";
        $o->cae = $r->cae;
        $o->fecha_venc_cae = fecha_es($r->fecha_vto);
        $o->operacion_con_intermediario = "";
        $o->cuit_intermediario = "";
        $o->denominacion_intermediario = "";
        $o->cotizacion = 1;
        $o->neto_21 = $r->neto_5;
        $o->iva_21 = $r->iva_5;
        $o->neto_105 = $r->neto_4;
        $o->iva_105 = $r->iva_4;
        $o->neto_27 = 0;
        $o->iva_27 = 0;
        $o->iva_rni_105 = 0;
        $o->perc_iva = 0;
        $o->perc_agip = 0;
        $o->perc_san_luis = 0;
        $o->perc_iibb = $r->percepcion_ib;
        $o->exento = ((isset($r->neto_3)) ? $r->neto_3 : 0);
        $o->no_gravado = 0;
        $o->imp_interno = 0;
        $o->perc_ret_iibb = 0;
        $o->total = $r->total;
        $datos[] = $o;
      }

      $this->load->library("Excel");
      $this->excel->create(array(
        "date"=>"",
        "filename"=>"Libro de IVA Ventas",
        "footer"=>array(),
        "header"=>$encabezado,
        "data"=>$datos,
        "title"=>"",
      ));        

    } else {
    
      $header = $this->load->view("reports/iva/header",null,true);
      if ($pagina_desde == 0) $pagina_desde = 1;
      $data = array(
        "datos"=>$result,
        "pagina_desde"=>$pagina_desde,
        "fecha_desde"=>fecha_es($fecha_desde),
        "fecha_hasta"=>fecha_es($fecha_hasta),
        "alicuotas"=>$alicuotas,
        "empresa"=>$empresa,
        "header"=>$header,
        "usa_zetas"=>$usa_zetas,

        // Solamente para javier
        "neto_0_21_ri"=>$neto_0_21_ri,
        "neto_0_105_ri"=>$neto_0_105_ri,
        "neto_0_21_mon"=>$neto_0_21_mon,
        "neto_0_105_mon"=>$neto_0_105_mon,        
      );
      $this->load->view("reports/iva/ventas",$data);
    }
  }
  
  function compras($movimiento,$cerrar = 0,$ultima_pagina = 0, $id_razon_social = 0) {

    $id_empresa = $this->get_empresa();
    $mes = substr($movimiento,0,2);
    $anio = "20".substr($movimiento,2,4);
    $ids_sucursales = "";
    $excel = parent::get_get("excel",0);

    if ($id_empresa == 249 || $id_empresa == 868) {
      $this->load->model("Razon_Social_Model");
      $ids_sucursales = $this->Razon_Social_Model->get_sucursales_by_razon_social($id_razon_social);
    }

    // Obtenemos el los dias de inicio y fin de mes
    $this->load->helper("fecha_helper");
    $fecha_desde = "$anio-$mes-01";
    $fecha_hasta = date("Y-m-d",(mktime(0,0,0,$mes+1,1,$anio)-1));
    
    $this->load->model("Empresa_Model");
    $empresa = $this->Empresa_Model->get($id_empresa);
    
    $sql = "SELECT ";
    $sql.= "  C.id, C.id_proveedor, ";
    $sql.= "  DATE_FORMAT(C.fecha,'%d/%m/%Y') AS fecha, ";
    $sql.= "  C.id_tipo_comprobante, ";
    $sql.= "  C.numero_1, C.numero_2, ";
    $sql.= "  CN.neto_dto AS neto, CN.porc_iva, CN.iva, ";
    $sql.= "  C.perc_ing_brutos, C.perc_iva, C.perc_agip, C.perc_san_luis, ";
    $sql.= "  C.impuesto_interno, C.exento, C.no_gravado, C.ret_ing_brutos, ";
    $sql.= "  C.total_iva, C.total_neto, C.total_general, ";
    $sql.= "  IF(TC.letra IS NULL,'',TC.letra) AS letra, ";
    $sql.= "  IF(P.razon_social != '',P.razon_social,P.nombre) AS nombre, ";
    $sql.= "  P.cuit, P.convenio_multilateral, P.id_tipo_iva, P.direccion ";
    $sql.= "FROM compras C ";
    $sql.= "INNER JOIN proveedores P ON (C.id_proveedor = P.id AND C.id_empresa = P.id_empresa) ";
    $sql.= "INNER JOIN compras_netos CN ON (C.id = CN.id_compra AND C.id_empresa = CN.id_empresa) ";
    $sql.= "LEFT JOIN tipos_comprobante TC ON (C.id_tipo_comprobante = TC.id) ";
    $sql.= "WHERE C.id_empresa = $id_empresa ";
    if (!empty($ids_sucursales)) $sql.= "AND C.id_sucursal IN ($ids_sucursales) ";
    $sql.= "AND C.incluido_libro_iva = 1 ";
    $sql.= "AND movimiento = '$movimiento' ";
    $sql.= "AND id_tipo_comprobante > 0 AND id_tipo_comprobante < 900 ";
    $sql.= "ORDER BY C.fecha ASC, C.numero_2 ASC ";
    $q = $this->db->query($sql);
    $result = $q->result();

    $header = $this->load->view("reports/iva/header",null,true);

    if ($id_razon_social != 0) {
      $razon_social = $this->Razon_Social_Model->get($id_razon_social);
      $empresa->razon_social = $razon_social->nombre;
      $empresa->cuit = $razon_social->cuit;
      $empresa->direccion = $razon_social->direccion;
    }

    // Si tenemos que exportar a Excel
    if ($excel == 1) {

      // EL MEGA USA UNO DISTINTO
      if ($id_empresa == 249 || $id_empresa == 868) {
        $encabezado = array(
          "Fecha",
          "Proveedor",
          "CUIT",
          "Tipo de comprobante",
          "Numero",
          "Concepto",
          "Neto 10.50%",
          "IVA 10.50%",
          "Neto 21%",
          "IVA 21%",
          "Neto 27%",
          "IVA 27%",
          "No Gravados",
          "Perc/Ret IVA",
          "Perc/Ret IIBB",
          "Impuestos Internos",
          "Total",
        );
        $datos = array();
        foreach($result as $r) {

          // Sumamos los conceptos
          $ids_iva = array(0,21,10.5,27);
          foreach($ids_iva as $id_iva) {
            $sql = "SELECT IF(SUM(neto_dto) IS NULL,0,SUM(neto_dto)) AS neto, ";
            $sql.= " IF(SUM(iva) IS NULL,0,SUM(iva)) AS iva ";
            $sql.= "FROM compras_netos ";
            $sql.= "WHERE id_compra = $r->id AND id_empresa = $id_empresa ";
            $sql.= "AND ABS(porc_iva) = $id_iva ";
            $qq = $this->db->query($sql);
            if ($id_iva == 0) $id_iva = 3;
            else if ($id_iva == 21) $id_iva = 5;
            else if ($id_iva == 10.5) $id_iva = 4;
            else if ($id_iva == 27) $id_iva = 6;
            if ($qq->num_rows() > 0) {
              $rr = $qq->row();
              $r->{"neto_".$id_iva} = $rr->neto;
              $r->{"iva_".$id_iva} = $rr->iva;
            } else {
              $r->{"neto_".$id_iva} = 0;
            }
          }

          $sql = "SELECT * FROM tipos_comprobante WHERE id = $r->id_tipo_comprobante ";
          $qq = $this->db->query($sql);
          $tipo_comprobante = $qq->row();

          // Tomamos el primer concepto
          $sql = "SELECT * FROM compras_netos ";
          $sql.= "WHERE id_compra = $r->id AND id_empresa = $id_empresa ";
          $qq = $this->db->query($sql);
          $rr = $qq->row();

          $o = new stdClass();
          $o->fecha = $r->fecha;
          $o->razon_social = $r->nombre;
          $o->documento = (strlen($r->cuit) == 11) ? (substr($r->cuit, 0, 2)."-".substr($r->cuit, 2, 8)."-".substr($r->cuit, -1, 1)) : $r->cuit;
          $o->tipo_comprobante = $tipo_comprobante->nombre;
          $o->numero = str_pad($r->numero_1,2,"0",STR_PAD_LEFT)."-".str_pad($r->numero_2,8,"0",STR_PAD_LEFT);
          $o->concepto = $rr->nombre_concepto;
          $o->neto_105 = isset($r->neto_4) ? $r->neto_4 : 0;
          $o->iva_105 = isset($r->iva_4) ? $r->iva_4 : 0;
          $o->neto_21 = isset($r->neto_5) ? $r->neto_5 : 0;
          $o->iva_21 = isset($r->iva_5) ? $r->iva_5 : 0;
          $o->neto_27 = isset($r->neto_6) ? $r->neto_6 : 0;
          $o->iva_27 = isset($r->iva_6) ? $r->iva_6 : 0;
          $o->no_gravado = $r->no_gravado + $r->exento + ((isset($r->neto_3)) ? $r->neto_3 : 0);
          $o->perc_iva = $r->perc_iva;
          $o->perc_iibb = $r->perc_ing_brutos + $r->perc_san_luis + $r->perc_agip + $r->ret_ing_brutos;
          $o->imp_interno = $r->impuesto_interno;
          $o->total = $r->total_general;
          $datos[] = $o;
        }     
        // Fin MEGAHSOP   

      } else {

        $encabezado = array(
          "Letra",
          "Numero",
          "Tipo Comprobante",
          "Fecha",
          "Fecha Contable",
          "Razon Social",
          "Tipo Documento",
          "Documento",
          "Condicion IVA",
          "Nro. IIBB",
          "Provincia",
          "Sujeto Vinculado",
          "Operacion",
          "Calle",
          "Localidad",
          "Piso",
          "Departamento",
          "Codigo Postal",
          "Operacion Sujeto Vinculado",
          "Tipo Comprobante AFIP",
          "Operacion AFIP",
          "Iva No Computable",
          "Nro de Despacho",
          "Comprobante Electronico",
          "CAE",
          "Fecha Venc. CAE",
          "Operacion con Intermediario",
          "CUIT Intermediario",
          "Denominacion Intermediario",
          "Cotizacion",
          "Neto Gravado 21",
          "Iva 21",
          "Neto Gravado 10.5",
          "Iva 10.5",
          "Neto Gravado 27",
          "Iva 27",
          "Iva RNI 10.5",
          "Perc. IVA",
          "Perc. IIBB",
          "Exento",
          "No Gravado",
          "Imp. Interno",
          "Perc. Ret IIBB",
          "Total",
        );
        $datos = array();
        foreach($result as $r) {

          // Sumamos los conceptos
          $ids_iva = array(3,4,5,6);
          foreach($ids_iva as $id_iva) {
            $sql = "SELECT IF(SUM(neto_dto) IS NULL,0,SUM(neto_dto)) AS neto, ";
            $sql.= " IF(SUM(iva) IS NULL,0,SUM(iva)) AS iva ";
            $sql.= "FROM compras_netos ";
            $sql.= "WHERE id_compra = $r->id AND id_empresa = $id_empresa ";
            $sql.= "AND id_tipo_alicuota_iva = $id_iva ";
            $qq = $this->db->query($sql);
            if ($qq->num_rows() > 0) {
              $rr = $qq->row();
              $r->{"neto_".$id_iva} = $rr->neto;
              $r->{"iva_".$id_iva} = $rr->iva;
            } else {
              $r->{"neto_".$id_iva} = 0;
            }
          }

          $o = new stdClass();
          $o->letra = $r->letra;
          $o->numero = $r->numero_1."-".$r->numero_2;
          if ($r->id_tipo_comprobante == 1 || $r->id_tipo_comprobante == 6 || $r->id_tipo_comprobante == 11) {
            $o->tipo_comprobante = "FCC";
          } else if ($r->tipo_comprobante == 2 || $r->id_tipo_comprobante == 7 || $r->id_tipo_comprobante == 12) {
            $o->tipo_comprobante = "NDC";
          } else if ($r->id_tipo_comprobante == 3 || $r->id_tipo_comprobante == 8 || $r->id_tipo_comprobante == 13) {
            $o->id_tipo_comprobante = "NCC";
          } else {
            $o->id_tipo_comprobante = "";
          }
          $o->fecha = $r->fecha;
          $o->fecha_contable = $r->fecha;
          $o->razon_social = $r->nombre;
          // TODO: HACER ESTO DINAMICO
          $o->tipo_documento = 80;// $r->id_tipo_documento;
          $o->documento = $r->cuit;
          if (strlen($o->documento) == 11 && strpos($o->documento, "-") === FALSE) {
            // Si tiene 11 caracteres pero no tiene guiones, se los agregamos
            $o->documento = substr($o->documento, 0, 2)."-".substr($o->documento, 2, 8)."-".substr($o->documento, 10, 1);
          }
          if ($r->id_tipo_iva == 1) {
            $o->condicion_iva = "RI";
          } else if ($r->id_tipo_iva == 2) {
            $o->condicion_iva = "RS";
          } else if ($r->id_tipo_iva == 3) {
            $o->condicion_iva = "EX";
          } else if ($r->id_tipo_iva == 4) {
            $o->condicion_iva = "CF";
          } else {
            $o->condicion_iva = "";
          }
          $o->iibb = "";
          $o->provincia = "01";
          $o->sujeto_vinculado = "N";
          $o->operacion = "02";
          $o->calle = $r->direccion;
          $o->localidad = "";
          $o->piso = "";
          $o->depto = "";
          $o->codigo_postal = "";
          $o->operacion_sujeto_vinculado = "";
          $o->tipo_comprobante_afip = "001";
          $o->operacion_afip = "0";
          $o->iva_no_computable = "";
          $o->nro_despacho = "";
          $o->comprobante_electronico = "S";
          $o->cae = "";
          $o->fecha_venc_cae = "";
          $o->operacion_con_intermediario = "";
          $o->cuit_intermediario = "";
          $o->denominacion_intermediario = "";
          $o->cotizacion = 1;
          $o->neto_21 = $r->neto_5;
          $o->iva_21 = $r->iva_5;
          $o->neto_105 = $r->neto_4;
          $o->iva_105 = $r->iva_4;
          $o->neto_27 = $r->neto_6;
          $o->iva_27 = $r->iva_6;
          $o->iva_rni_105 = 0;
          $o->perc_iva = $r->perc_iva;
          $o->perc_agip = $r->perc_agip;
          $o->perc_san_luis = $r->perc_san_luis;
          $o->perc_iibb = $r->perc_ing_brutos;
          $o->exento = $r->exento + ((isset($r->neto_3)) ? $r->neto_3 : 0);
          $o->no_gravado = $r->no_gravado;
          $o->imp_interno = $r->impuesto_interno;
          $o->ret_ing_brutos = $r->ret_ing_brutos;
          $o->total = $r->total_general;
          $datos[] = $o;
        }
      }

      $this->load->library("Excel");
      $this->excel->create(array(
        "date"=>"",
        "filename"=>"Libro de IVA Compras",
        "footer"=>array(),
        "header"=>$encabezado,
        "data"=>$datos,
        "title"=>"",
      ));

    } else {    

      $data = array(
        "db"=>$this->db,
        "datos"=>$result,
        "cerrar"=>$cerrar,
        "inicio"=>($ultima_pagina-1),
        "empresa"=>$empresa,
        "header"=>$header,
        "fecha_desde"=>fecha_es($fecha_desde),
        "fecha_hasta"=>fecha_es($fecha_hasta),
        );
      $this->load->view("reports/iva/compras",$data);
      /*
      if ($cerrar == 1) {
        $filas = $result->num_rows();
        $ultima_pagina = $ultima_pagina + ceil($filas / 33) + 1;
      }
      */
    }
  }

}