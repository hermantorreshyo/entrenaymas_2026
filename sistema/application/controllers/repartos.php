<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Repartos extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Reparto_Model', 'modelo');
  }
  
  function exportar($fecha = "",$numero = 0) {
    
    $this->load->helper("fecha_helper");
    if (!empty($fecha)) $fecha = fecha_mysql($fecha);    
    $lista = $this->modelo->get($fecha,$numero);
    include("resources/php/Excel/PHPExcel.php");
    $objPHPExcel = new PHPExcel();
    $objPHPExcel->setActiveSheetIndex(0);
    $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Listado de Repartos');
    $objPHPExcel->getActiveSheet()->SetCellValue('A2', 'Fecha: '.fecha_es($fecha));
    $objPHPExcel->getActiveSheet()->SetCellValue('D2', 'Numero: '.$numero);
    
    // Encabezado
    $objPHPExcel->getActiveSheet()->SetCellValue('A4', 'Codigo');
    $objPHPExcel->getActiveSheet()->SetCellValue('B4', 'Articulo');
    $objPHPExcel->getActiveSheet()->SetCellValue('C4', 'Facturado');
    $objPHPExcel->getActiveSheet()->SetCellValue('D4', 'Devolucion');
    $objPHPExcel->getActiveSheet()->SetCellValue('E4', 'Bonificado');
    $objPHPExcel->getActiveSheet()->SetCellValue('F4', 'Total');
    $objPHPExcel->getActiveSheet()->SetCellValue('G4', 'Bultos');
    
    $total = 0; $total_facturado = 0; $total_devolucion = 0; $total_bonificacion = 0;
    $i=5;
    foreach($lista as $l) {
      $objPHPExcel->getActiveSheet()->SetCellValue('A'.$i, $l->codigo);
      $objPHPExcel->getActiveSheet()->SetCellValue('B'.$i, $l->descripcion);
      $objPHPExcel->getActiveSheet()->SetCellValue('C'.$i, $l->facturado);
      $objPHPExcel->getActiveSheet()->SetCellValue('D'.$i, $l->devolucion);
      $objPHPExcel->getActiveSheet()->SetCellValue('E'.$i, $l->bonificacion);
      $tot = $l->facturado + $l->bonificacion + $l->devolucion;
      $objPHPExcel->getActiveSheet()->SetCellValue('F'.$i, $tot);
      if ($l->uxb > 1) {
        $s = number_format(floor($tot / $l->uxb),0)." Bul.".((($tot % $l->uxb)!=0) ? ("+".$tot % $l->uxb." unid.") : "");
        $objPHPExcel->getActiveSheet()->SetCellValue('G'.$i, $s);
      }
      $total += $tot;
      $total_facturado += $l->facturado;
      $total_devolucion += $l->devolucion;
      $total_bonificacion += $l->bonificacion;
      $i++;
    }
    $objPHPExcel->getActiveSheet()->SetCellValue('A'.$i, 'Totales');
    $objPHPExcel->getActiveSheet()->SetCellValue('C'.$i, $total_facturado);
    $objPHPExcel->getActiveSheet()->SetCellValue('D'.$i, $total_devolucion);
    $objPHPExcel->getActiveSheet()->SetCellValue('E'.$i, $total_bonificacion);
    $objPHPExcel->getActiveSheet()->SetCellValue('F'.$i, $total);

    $filename = "reparto-".$fecha."-".$numero.".xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="'.$filename.'"');
    header('Cache-Control: max-age=0');
    $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
    $objWriter->save('php://output');    
  }
  
  function imprimir($fecha = "",$numero = 0) {
    $this->load->helper("fecha_helper");
    if (!empty($fecha)) $fecha = fecha_mysql($fecha);    
    $lista = $this->modelo->get($fecha,$numero);

    $header = $this->load->view("reports/pedido/header",null,true);

    $this->load->view('reports/repartos',array(
      "header"=>$header,
      "fecha"=>fecha_es($fecha),
      "numero"=>$numero,
      "results"=>$lista,
    ));
  }

  function imprimir_facturas($fecha = "",$numero = 0) {

    $this->load->helper("fecha_helper");
    if (!empty($fecha)) $fecha = fecha_mysql($fecha);    
    $this->load->model("Venta_Model");
    $conf = array(
      "estado"=>-1,
      "forma_pago"=>-1,
      "numero_reparto"=>$numero,
      "fecha_reparto"=>$fecha,
      "offset"=>999999,
      "order"=>"F.cliente ASC, F.numero DESC ",
    );
    $lista = $this->Venta_Model->listado($conf);
    $header = $this->load->view("reports/pedido/header",null,true);

    $this->load->view('reports/repartos_facturas',array(
      "header"=>$header,
      "fecha"=>fecha_es($fecha),
      "numero"=>$numero,
      "results"=>$lista["results"],
    ));

  }
  
  function consulta() {
    $this->load->helper("fecha_helper");
    $fecha = $this->input->post("fecha");
    $fecha = fecha_mysql($fecha);
    $numero = $this->input->post("numero");
    $lista = $this->modelo->get($fecha,$numero);    
    echo json_encode(array(
      "total"=>sizeof($lista),
      "results"=>$lista
    ));
  }
  
  function get($fecha = "",$numero = 0, $id_punto_venta = 0) {
    
    $id_empresa = parent::get_empresa();
    $this->load->helper("fecha_helper");
    if (!empty($fecha)) $fecha = fecha_mysql($fecha);
    $q = $this->db->query("SELECT * FROM repartos WHERE id_empresa = $id_empresa AND numero = $numero AND fecha = '$fecha' LIMIT 0,1");
    if ($q->num_rows()>0) {
      $reparto = $q->row();
    } else {
      $reparto = new stdClass();
      $reparto->id = 0;
      $reparto->fecha = $fecha;
      $reparto->numero = $numero;
      $reparto->estado = "A";
      $reparto->efectivo_inicial = 0;
      $reparto->total_gastos = 0;
      $reparto->total_pagos = 0;
      $reparto->total = 0;
    }
    
    // Obtenemos los pagos
    $sql = "SELECT F.*, ";
    $sql.= "  DATE_FORMAT(fecha,'%d/%m/%Y') AS fecha, ";
    $sql.= "  DATE_FORMAT(fecha_reparto,'%d/%m/%Y') AS fecha_reparto, ";
    $sql.= "  IF(C.id IS NULL,0,C.id) AS id_cliente, ";
    $sql.= "  IF(C.nombre IS NULL,'Consumidor Final',C.nombre) AS cliente ";
    $sql.= "FROM facturas F ";
    $sql.= "LEFT JOIN clientes C ON (C.id = F.id_cliente AND C.id_empresa = F.id_empresa) ";
    $sql.= "WHERE F.id_empresa = $id_empresa ";
    $sql.= "AND F.fecha_reparto = '$fecha' AND anulada = 0 ";
    if (!empty($numero)) $sql.= "AND F.reparto = $numero ";
    if ($id_punto_venta != 0) $sql.= "AND F.id_punto_venta = $id_punto_venta ";
    $sql.= "ORDER BY C.nombre ASC, F.numero ASC ";
    $q = $this->db->query($sql);
    if ($q->num_rows() == 0) $reparto->pagos = array();
    else $reparto->pagos = $q->result();
    
    // Obtenemos los gastos
    $sql = "SELECT G.id_tipo_gasto AS id_concepto, ";
    $sql.= " TG.nombre AS concepto, G.total, TG.codigo ";
    $sql.= "FROM gastos G ";
    $sql.= "INNER JOIN tipos_gastos TG ON (G.id_tipo_gasto = TG.id AND G.id_empresa = TG.id_empresa) ";
    $sql.= "WHERE G.id_empresa = $id_empresa AND G.id_reparto = $reparto->id ";
    $q = $this->db->query($sql);
    if ($q->num_rows() == 0) $reparto->gastos = array();
    else $reparto->gastos = $q->result();
    
    echo json_encode($reparto);
  }
  
  function guardar_procesar_pagos() {
    
    $id_empresa = parent::get_empresa();
    $this->load->model("Cliente_Model");
    $this->load->helper("fecha_helper");
    $facturas = json_decode($this->input->post("facturas"));
    $gastos = json_decode($this->input->post("gastos"));
    $cobranzas = json_decode($this->input->post("cobranzas"));
    $clientes = json_decode($this->input->post("clientes"));
    $fecha = $this->input->post("fecha");
    $fecha = fecha_mysql($fecha);
    $numero = $this->input->post("numero");
    $efectivo_inicial = $this->input->post("efectivo_inicial");
    $efectivo_1 = $this->input->post("efectivo_1");
    $efectivo_2 = $this->input->post("efectivo_2");
    $diferencia = $this->input->post("diferencia");
    $total_pagos = $this->input->post("total_pagos");
    $total_gastos = $this->input->post("total_gastos");
    $total_cobranzas = $this->input->post("total_cobranzas");
    $total = $this->input->post("total");
    $id_usuario = $this->input->post("id_usuario");
    $nro_recibo = 0;
    
    // Debemos agrupar por cliente
    $clientes = array();
    foreach($facturas as $a) {
      if (!isset($clientes[$a->id_cliente])) {
        $clientes[$a->id_cliente] = array(
          "facturas"=> array(),
          "monto"=> 0,
        );
      }
      $clientes[$a->id_cliente]["monto"] += $a->efectivo;
      if ($a->pagada == 1) {
        $clientes[$a->id_cliente]["facturas"][] = array(
          "id"=>$a->id_factura,
          "efectivo"=>$a->efectivo,
        );
      }
    }
    
    // Procesamos los pagos
    foreach($clientes as $id_cliente => $value) {
      // Si es consumidor final, no lo procesamos
      if ($id_cliente != 0) {
        $monto = $value["monto"];

        if ($monto > 0) {
          // Tomamos el numero de recibo
          $sql = "INSERT INTO facturas (";
          $sql.= "fecha,hora,pago,cta_cte,efectivo,tipo,tipo_pago,";
          $sql.= "numero,comprobante,";
          $sql.= "id_cliente,id_empresa,id_usuario,pagada";
          $sql.= ") VALUES(";
          $sql.= "'$fecha','".date("H:i:s")."',-$monto,-$monto,$monto,'P','C',";
          $sql.= "$nro_recibo,'P ".str_pad($nro_recibo,8,"0",STR_PAD_LEFT)."',";
          $sql.= "$id_cliente,$id_empresa,$id_usuario,1) ";
          $this->db->query($sql);
          $id_pago = $this->db->insert_id();

          foreach($value["facturas"] as $f) {
            $sql = "INSERT INTO facturas_pagos (";
            $sql.= " id_empresa, id_pago, id_factura, monto ";
            $sql.= ") VALUES (";
            $sql.= " $id_empresa, $id_pago, ".$f["id"].", ".$f["efectivo"]." ";
            $sql.= ")";
            $this->db->query($sql);
          }          
        }
      }
    }
    
    // Procesamos las cobranzas
    if (sizeof($cobranzas)>0) {
      foreach($cobranzas as $cob) {
        $sql = "INSERT INTO facturas (";
        $sql.= "fecha,hora,total,cta_cte,tipo,";
        $sql.= "numero,comprobante,";
        $sql.= "id_cliente,id_empresa,id_usuario,pagada";
        $sql.= ") VALUES(";
        $sql.= "'$fecha','".date("H:i:s")."',-$cob->total,$cob->total,'P',";
        $sql.= "$nro_recibo,'P ".str_pad($nro_recibo,8,"0",STR_PAD_LEFT)."',";
        $sql.= "$cob->codigo,$id_empresa,$id_usuario,1) ";
        $this->db->query($sql);
        $nro_recibo++;
      }
    }
    
    // Cerramos el reparto
    $sql = "INSERT INTO repartos (efectivo_1,efectivo_2,diferencia,fecha,numero,estado,efectivo_inicial,total_gastos,total_pagos,total,id_usuario,total_cobranzas,id_empresa) VALUES(";
    $sql.= "$efectivo_1,$efectivo_2,$diferencia,'$fecha',$numero,'C',$efectivo_inicial,$total_gastos,$total_pagos,$total,$id_usuario,$total_cobranzas,$id_empresa)";
    $q = $this->db->query($sql);
    $id_reparto = $this->db->insert_id();
    
    // Guardamos los gastos asociados a ese reparto
    foreach($gastos as $g) {
      $sql = "INSERT INTO gastos (id_tipo_gasto,fecha,total,id_reparto,id_empresa) VALUES (";
      $sql.= "$g->id_concepto,'$fecha',$g->total,$id_reparto,$id_empresa)";
      $this->db->query($sql);
    }
    
    echo json_encode(array("error"=>0));
  }
  
  
}