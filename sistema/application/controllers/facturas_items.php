<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Facturas_Items extends REST_Controller {

  function __construct() {
    parent::__construct();
	}

  function insert() { echo json_encode(array()); }
  function delete() { echo json_encode(array()); }
  function update() { echo json_encode(array()); }
  function get() { echo json_encode(array()); }

  // Esta funcion es utilizada para mover el stock reservado al stock actual
  function desreservar() {
    $id_empresa = parent::get_empresa();
    $id = parent::get_get("id",0);
    $id_punto_venta = parent::get_get("id_punto_venta",0);
    $id_articulo = parent::get_get("id_articulo",0);
    $id_variante = parent::get_get("id_variante",0);
    $cantidad = parent::get_get("cantidad",0);

    // Obtenemos la sucursal del cual tiene el stock comprometido
    $sql = "SELECT * FROM stock WHERE id_empresa = $id_empresa AND id_articulo = $id_articulo AND reservado > 0";
    $q = $this->db->query($sql);
    if ($q->num_rows()<=0) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"No existe un stock comprometido para ese articulo"
      ));
      exit();
    }
    $row = $q->row();
    $id_sucursal = $row->id_sucursal;
    
    $this->load->model("Stock_Model");
    // Debitamos el reservado
    $this->Stock_Model->reservar(array(
      "id_articulo"=>$id_articulo,
      "id_variante"=>$id_variante,
      "id_almacen"=>$id_sucursal,
      "cantidad"=>($cantidad * -1),
    ));
    // Debitamos el stock actual
    $this->Stock_Model->sacar($id_articulo,$cantidad,$id_sucursal,'B',"","Mov. Stock Reservado",0,$id_variante);

    // Actualizamos el item para que ya no este mas en reservado
    $sql = "UPDATE facturas_items SET custom_3 = '' ";
    $sql.= "WHERE id_empresa = $id_empresa ";
    $sql.= "AND id = $id ";
    if ($id_punto_venta != 0) $sql.= "AND id_punto_venta = $id_punto_venta ";
    if ($id_articulo != 0) $sql.= "AND id_articulo = $id_articulo ";
    if ($id_variante != 0) $sql.= "AND id_variante = $id_variante ";
    $this->db->query($sql);

    echo json_encode(array(
      "error"=>0,
    ));
  }

  // Funcion para acomodar las facturas que se le borraron los items
  function ingresar_borrados() {

    $id_empresa = 249;
    $id_articulo = 1;
    $id_punto_venta = 1054;
    $sql = "SELECT AVG(porc_ganancia) AS porc_ganancia FROM articulos WHERE id_empresa = $id_empresa ";
    $q = $this->db->query($sql);
    $r = $q->row();
    $porc_ganancia = $r->porc_ganancia;
    $cant_insert = 0;

    $sql = "SELECT * FROM facturas F ";
    $sql.= "WHERE F.id_empresa = $id_empresa AND F.id_tipo_comprobante != 0 ";
    $sql.= "AND F.id_punto_venta = $id_punto_venta ";
    $sql.= "AND NOT EXISTS (SELECT * FROM facturas_items FI WHERE (F.id = FI.id_factura AND F.id_empresa = FI.id_empresa AND F.id_punto_venta = FI.id_punto_venta) ) ";
    $q = $this->db->query($sql);
    foreach($q->result() as $f) {
      $costo_final = $f->total / ((100+$porc_ganancia)/100);
      $sql = "INSERT INTO facturas_items (";
      $sql.= " id_tipo_comprobante,id_empresa,id_punto_venta,id_factura,id_articulo,";
      $sql.= " cantidad,porc_iva,id_tipo_alicuota_iva,neto,precio,nombre,iva,total_con_iva,total_sin_iva,";
      $sql.= " tipo,costo_final,id_cliente,anulado,negativo,uploaded,orden";
      $sql.= ") VALUES(";
      $sql.= " '$f->id_tipo_comprobante','$f->id_empresa','$f->id_punto_venta','$f->id',$id_articulo,";
      $sql.= " 1,21,5,'$f->neto','$f->total','','$f->iva','$f->total','$f->neto',";
      $sql.= " 0,'$costo_final','$f->id_cliente',0,0,0,0";
      $sql.= ")";
      $this->db->query($sql);
      $cant_insert++;
    }
    echo "TERMINO $cant_insert ";
  }


  // Funcion para acomodar las facturas que se le borraron los items
  function acomodar_costo() {

    $id_empresa = 249;
    $id_punto_venta = "1050,1051,1052,1053,1054";
    $desde = "2017-12-01";
    $hasta = "2018-01-03";

    $sql = "SELECT AVG(porc_ganancia) AS porc_ganancia FROM articulos WHERE id_empresa = $id_empresa ";
    $q = $this->db->query($sql);
    $r = $q->row();
    $porc_ganancia = $r->porc_ganancia;
    $cant_insert = 0;

    $sql = "SELECT FI.id, FI.id_articulo, A.porc_ganancia, FI.id_punto_venta, FI.id_factura, FI.total_con_iva ";
    $sql.= "FROM facturas_items FI ";
    $sql.= " INNER JOIN articulos A ON (FI.id_empresa = A.id_empresa AND FI.id_articulo = A.id) ";
    $sql.= " INNER JOIN facturas F ON (FI.id_empresa = F.id_empresa AND FI.id_punto_venta = F.id_punto_venta AND FI.id_factura = F.id) ";
    $sql.= "WHERE FI.id_empresa = $id_empresa ";
    $sql.= "AND FI.id_punto_venta IN ($id_punto_venta) ";
    $sql.= "AND F.fecha >= '$desde' AND F.fecha <= '$hasta' ";
    $q = $this->db->query($sql);
    foreach($q->result() as $f) {
      $costo_final = ($f->porc_ganancia > 0) ? ($f->total_con_iva / ((100+$f->porc_ganancia)/100)) : ($f->total_con_iva / ((100+$porc_ganancia)/100));
      $sql = "UPDATE facturas_items SET costo_final = $costo_final ";
      $sql.= "WHERE id_empresa = $id_empresa AND id_punto_venta = $f->id_punto_venta AND id_factura = $f->id_factura AND id_articulo = $f->id_articulo AND id = $f->id ";
      //echo $sql."<br/>"; exit();
      $this->db->query($sql);
      $cant_insert++;
    }
    echo "TERMINO $cant_insert ";
  }  
}