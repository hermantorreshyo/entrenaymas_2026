<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Cajas extends REST_Controller {

  function get($id) {
    if ($id == "index") {
      $activo = parent::get_get("activo",0);
      $id_sucursal = parent::get_get("id_sucursal",0);
      $limit = parent::get_get("limit",0);
      $tipo = parent::get_get("tipo",-1);
      $offset = parent::get_get("offset",9999);
      $filter = parent::get_get("filter","");
      $salida = $this->modelo->buscar(array(
        "activo"=>$activo,
        "id_sucursal"=>$id_sucursal,
        "limit"=>$limit,
        "tipo"=>$tipo,
        "offset"=>$offset,
        "filter"=>$filter,
      ));
      echo json_encode($salida);
    } else {
      parent::get($id);
    }
  }

  function crear_movimientos_orden_pago() {
    $id_empresa = 134;
    $id_caja_banco = 366;
    $id_caja_efectivo = 40;
    $q = $this->db->query("SELECT * FROM compras WHERE id_tipo_comprobante = -1 AND id_empresa = $id_empresa");
    $this->load->model("Proveedor_Model");
    foreach($q->result() as $orden_pago) {
      if ($orden_pago->efectivo != 0) {
        $proveedor = $this->Proveedor_Model->get($orden_pago->id_proveedor,array(
          "id_empresa"=>$id_empresa
        ));
        $observaciones = $proveedor->nombre." - OP ".$orden_pago->numero_1."-".$orden_pago->numero_2;
        $observaciones = str_replace("'", "", $observaciones);
        $efectivo = ($orden_pago->efectivo) * -1;
        $sql = "INSERT INTO cajas_movimientos (id_empresa, fecha, id_caja, monto, id_usuario, id_orden_pago, tipo, observaciones) VALUES (";
        $sql.= "$id_empresa, '$orden_pago->fecha', $id_caja_efectivo, $efectivo, $orden_pago->id_usuario, $orden_pago->id, 1, '$observaciones')";
        $this->db->query($sql);        
      }
      if ($orden_pago->total_depositos != 0) {
        $proveedor = $this->Proveedor_Model->get($orden_pago->id_proveedor,array(
          "id_empresa"=>$id_empresa
        ));
        $observaciones = $proveedor->nombre." - OP ".$orden_pago->numero_1."-".$orden_pago->numero_2;
        $observaciones = str_replace("'", "", $observaciones);
        $sql = "INSERT INTO cajas_movimientos (id_empresa, fecha, id_caja, monto, id_usuario, id_orden_pago, tipo, observaciones) VALUES (";
        $sql.= "$id_empresa, '$orden_pago->fecha', $id_caja_banco, $orden_pago->total_depositos, $orden_pago->id_usuario, $orden_pago->id, 1, '$observaciones')";
        $this->db->query($sql);
      }
    }
    echo "TERMINO";
  }

  function crear_movimientos_recibos() {
    $id_empresa = 134;
    $id_caja_banco = 366;
    $id_caja_efectivo = 40;
    $this->load->model("Cliente_Model");
    $q = $this->db->query("SELECT * FROM facturas WHERE tipo = 'P' AND id_empresa = $id_empresa");
    foreach($q->result() as $factura) {
      if ($factura->efectivo != 0) {
        $cliente = $this->Cliente_Model->get($factura->id_cliente,$id_empresa,array(
          "buscar_consultas"=>0,
          "buscar_etiquetas"=>0,
        ));
        $observaciones = $cliente->nombre." - ".$factura->comprobante;
        $observaciones = str_replace("'", "", $observaciones);
        $sql = "INSERT INTO cajas_movimientos (id_empresa, fecha, id_caja, monto, id_usuario, id_factura, tipo, id_punto_venta, id_sucursal, observaciones) VALUES (";
        $sql.= "$id_empresa, '$factura->fecha', $id_caja_efectivo, $factura->efectivo, $factura->id_usuario, $factura->id, 0, $factura->id_punto_venta, $factura->id_sucursal, '$observaciones')";
        $this->db->query($sql);        
      }
      /*
      if ($factura->total_depositos != 0) {
        $sql = "INSERT INTO cajas_movimientos (id_empresa, fecha, id_caja, monto, id_usuario, id_factura, tipo) VALUES (";
        $sql.= "$id_empresa, '$factura->fecha', $id_caja_banco, $factura->total_depositos, $factura->id_usuario, $orden_pago->id, 0)";
        $this->db->query($sql);
      }
      */
    }
    echo "TERMINO";
  }

  function __construct() {
    parent::__construct();
    $this->load->model('Caja_Model', 'modelo');
  }
    
}