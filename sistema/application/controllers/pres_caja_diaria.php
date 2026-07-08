<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Pres_Caja_Diaria extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Pres_Caja_Diaria_Model', 'modelo');
  }

  function borrar_movimiento() {
    $id_empresa = parent::get_empresa();
    $id = $this->input->post("id");
    $id_sucursal = $this->input->post("id_sucursal");
    $sql = "DELETE FROM pres_cajas_movimientos WHERE id_empresa = $id_empresa AND id_sucursal = $id_sucursal AND id = $id ";
    $this->db->query($sql);
    echo json_encode(array(
      "error"=>0,
    ));
  }

  function buscar() {
    $this->load->helper("fecha_helper");
    $filter = ($this->input->get("texto") === FALSE) ? "" : $this->input->get("texto");
    $id_cliente = $this->input->get("id_cliente");
    $id_sucursal = parent::get_get("id_sucursal",0);
    $id_concepto = parent::get_get("id_concepto",0);
    $desde = parent::get_get("desde","");
    $desde = (!empty($desde)) ? fecha_mysql($desde) : date("Y-m-d");
    $hasta = parent::get_get("hasta","");
    $hasta = ((!empty($hasta)) ? fecha_mysql($hasta) : date("Y-m-d"))." 23:59:59";
    $limit = $this->input->get("limit");
    $offset = $this->input->get("offset");
    $order_by = $this->input->get("order_by");
    $order = $this->input->get("order");
    if (!empty($order_by) && !empty($order)) $order = $order_by." ".$order;
    else $order = "";
    $r = $this->modelo->buscar(array(
      "filter"=>$filter,
      "desde"=>$desde,
      "hasta"=>$hasta,
      "id_cliente"=>$id_cliente,
      "id_sucursal"=>$id_sucursal,
      "id_concepto"=>$id_concepto,
      "limit"=>$limit,
      "offset"=>$offset,
      "order"=>$order,
    ));
    echo json_encode($r);
  }

  function exportar() {
    $this->load->helper("fecha_helper");
    $filter = ($this->input->get("texto") === FALSE) ? "" : $this->input->get("texto");
    $id_cliente = $this->input->get("id_cliente");
    $id_sucursal = parent::get_get("id_sucursal",0);
    $id_concepto = parent::get_get("id_concepto",0);
    $desde = parent::get_get("desde","");
    $desde = (!empty($desde)) ? fecha_mysql($desde) : date("Y-m-d");
    $hasta = parent::get_get("hasta","");
    $hasta = ((!empty($hasta)) ? fecha_mysql($hasta) : date("Y-m-d"))." 23:59:59";
    $limit = $this->input->get("limit",0);
    $offset = $this->input->get("offset",9999999);
    $order_by = $this->input->get("order_by","");
    $order = $this->input->get("order","");
    if (!empty($order_by) && !empty($order)) $order = $order_by." ".$order;
    else $order = "";
    $r = $this->modelo->buscar(array(
      "filter"=>$filter,
      "desde"=>$desde,
      "hasta"=>$hasta,
      "id_cliente"=>$id_cliente,
      "id_sucursal"=>$id_sucursal,
      "id_concepto"=>$id_concepto,
      "limit"=>$limit,
      "offset"=>$offset,
      "order"=>$order,
    ));
    $datos = array();

    // Primero el Saldo Inicial
    $saldo = $r["meta"]["saldo_inicial"];
    $datos[] = array(
      "fecha"=>"",
      "concepto"=>"Saldo Inicial",
      "observaciones"=>"",
      "usuario"=>"",
      "ingresos"=>"",
      "pagos"=>"",
      "descuentos"=>"",
      "otorgaciones"=>"",
      "retiros"=>"",
      "gastos"=>"",
      "otros"=>"",
      "saldo"=>$saldo,
    );

    foreach($r["results"] as $row) {
      $m = $row->monto - $row->descuento;
      $row->ingresos = (($row->id_concepto == 272) ? $m : "");
      $row->pagos = (($row->id_concepto == 241) ? $m : "");
      $row->otorgaciones = (($row->id_concepto == 271) ? $m : "");
      $row->retiros = (($row->id_concepto == 242) ? $m : "");
      $row->gastos = (($row->id_concepto == 373) ? $m : "");
      $row->otros = (($row->id_concepto != 272 && $row->id_concepto != 241 && $row->id_concepto != 271 && $row->id_concepto != 242 && $row->id_concepto != 373) ? $m : "");
      if ($id_concepto == 0) {
        if ($row->tipo == "S") {
          $saldo = $saldo - $m;
        } else {
          $saldo = $saldo + $m;
        }
      } else {
        // Si se filtra por concepto los saldos no se toman en cuenta
        $saldo = 0;
      }
      $datos[] = array(
        "fecha"=>$row->fecha,
        "concepto"=>$row->concepto,
        "observaciones"=>$row->observaciones,
        "usuario"=>$row->usuario,
        "ingresos"=>$row->ingresos,
        "pagos"=>$row->pagos,
        "descuentos"=>$row->descuento,
        "otorgaciones"=>$row->otorgaciones,
        "retiros"=>$row->retiros,
        "gastos"=>$row->gastos,
        "otros"=>$row->otros,
        "saldo"=>$saldo,
      );
    }

    $header = array("Fecha","Concepto","Observaciones","Usuario","Ingresos","Pagos","Descuentos","Otorgaciones","Retiros","Gastos","Otros","Saldo");
    $this->load->library("Excel");
    $this->excel->create(array(
      "date"=>"DESDE: ".fecha_es($desde)." HASTA: ".fecha_es($hasta),
      "filename"=>"caja_diaria",
      "header"=>$header,
      "data"=>$datos,
      "title"=>"CAJA DIARIA",
    ));        

    echo json_encode($r);
  }  
}