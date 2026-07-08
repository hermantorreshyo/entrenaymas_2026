<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Transferencias_Stock extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Transferencia_Stock_Model', 'modelo');
  }

  function ver() {
    $filter = parent::get_get("filter");
    $id_sucursal = parent::get_get("id_sucursal",0);
    $limit = parent::get_get("limit",0);
    $offset = parent::get_get("offset",10);
    $r = $this->modelo->buscar(array(
      "filter"=>$filter,
      "id_sucursal"=>$id_sucursal,
      "limit"=>$limit,
      "offset"=>$offset,
    ));
    echo json_encode($r);
  }

  function imprimir($id) {
    
    $this->load->helper("fecha_helper");
    $transferencia = $this->modelo->get($id);
    if ($transferencia === FALSE || empty($transferencia)) {
      echo "Lo sentimos pero la compra ha sido eliminada.";
      exit();
    }
    $con_precio = ($this->input->get("con_precio") !== FALSE) ? $this->input->get("con_precio") : 1;
    
    $id_empresa = $transferencia->id_empresa;
    $this->load->model("Empresa_Model");
    $empresa = $this->Empresa_Model->get($id_empresa);
    
    $header = $this->load->view("reports/transferencia/header",null,true);
    
    $tpl = "modelo1";
    $folder = "/sistema/application/views/reports/transferencia/$tpl/dark_blue";
    
    $datos = array(
      "pedido"=>$transferencia,
      "empresa"=>$empresa,
      "header"=>$header,
      "folder"=>$folder,
      "con_precio"=>$con_precio,
    );
    $this->load->view("reports/transferencia/$tpl/pedido.php",$datos);
  }

  function update($id) {
        
    // Si es 0, entonces lo insertamos
    if ($id == 0) { $this->insert($id); return; }
    
    $id_empresa = parent::get_empresa();
    $this->load->helper("fecha_helper");
    $array = $this->parse_put();
    $array->id_empresa = $id_empresa;
    $confirmar = $array->confirmar;
    $array->estado = $confirmar;
    $fecha = $array->fecha;
    if (isset($array->fecha)) $array->fecha = fecha_mysql($array->fecha);
    else $array->fecha = date("Y-m-d");
    //$id_usuario = $_SESSION["id"];
    //$array->id_usuario = (!empty($id_usuario)) ? $id_usuario : 0;    
    $items = $array->items;
    $this->modelo->save($array);
    
    $this->db->query("DELETE FROM transferencias_stock_items WHERE id_transferencia = $id AND id_empresa = $id_empresa");
    $i=0;
    foreach($items as $l) {
      $this->db->insert("transferencias_stock_items",array(
        "id_empresa"=>$array->id_empresa,
        "id_transferencia"=>$id,
        "id_articulo"=>$l->id_articulo,
        "cantidad"=>$l->cantidad,
        "costo_final"=>$l->costo_final,
        "costo_neto"=>$l->costo_neto,
        "porc_iva"=>$l->porc_iva,
        "precio_neto"=>$l->precio_neto,
        "precio_final"=>$l->precio_final,
        "orden"=>$i,
        "total_neto"=>$l->total_neto,
        "total_final"=>$l->total_final,
      ));
      $i++;
    }

    if ($confirmar == 1) {
      $this->modelo->confirmar_stock(array(
        "items"=>$items,
        "id_origen"=>$array->id_origen,
        "id_destino"=>$array->id_destino,
        "fecha"=>$array->fecha,
      ));
    }

    $salida = array(
      "id"=>$id,
      "error"=>0,
    );
    echo json_encode($salida);
  }


  function insert() {

    $id_empresa = parent::get_empresa();
    $this->load->model("Empresa_Model");
    $this->load->helper("fecha_helper");
    
    // Tomamos los datos
    $array = $this->parse_put();
    $array->id_empresa = $id_empresa;
    $fecha = $array->fecha;
    $confirmar = $array->confirmar;
    $array->estado = $confirmar;
    if (isset($array->fecha)) $array->fecha = fecha_mysql($array->fecha);
    else $array->fecha = date("Y-m-d");
    
    $items = $array->items;
    $id_transferencia = $this->modelo->save($array);
    
    $i=0;
    foreach($items as $l) {
      $this->db->insert("transferencias_stock_items",array(
        "id_empresa"=>$array->id_empresa,
        "id_transferencia"=>$id_transferencia,
        "id_articulo"=>$l->id_articulo,
        "cantidad"=>$l->cantidad,
        "costo_final"=>$l->costo_final,
        "costo_neto"=>$l->costo_neto,
        "porc_iva"=>$l->porc_iva,
        "precio_neto"=>$l->precio_neto,
        "precio_final"=>$l->precio_final,
        "orden"=>$i,
        "total_neto"=>$l->total_neto,
        "total_final"=>$l->total_final,
      ));
      $i++;
    }

    if ($confirmar == 1) {
      $this->modelo->confirmar_stock(array(
        "items"=>$items,
        "id_origen"=>$array->id_origen,
        "id_destino"=>$array->id_destino,
        "fecha"=>$array->fecha,
      ));
    }
    
    echo json_encode(array(
      "id"=>$id_transferencia,
      "error"=>0,
    ));
  }

  function delete($id) {

    $id_empresa = parent::get_empresa();
    $transferencia = $this->modelo->get($id);
    if ($transferencia === FALSE) {
      echo json_encode(array(
        "error"=>1,
      ));
      exit();
    }

    // Si el ingreso fue confirmado y queremos eliminarlo, tenemos que volver a sumar los elementos en el stock de la sucursal
    if ($transferencia->estado == 1) {

      $fecha = date("Y-m-d");
      $this->load->model("Stock_Model");

      foreach($transferencia->items as $item) {

        // Tenemos que sacar del destino
        $this->Stock_Model->sacar($item->id_articulo,$item->cantidad,$transferencia->id_destino,"B",$fecha,"Eliminacion rotacion");

        // Y acreditarselo al origen de nuevo
        $this->Stock_Model->agregar($item->id_articulo,$item->cantidad,$transferencia->id_origen,$fecha,"Eliminacion rotacion");
      }
    }

    $sql = "DELETE FROM transferencias_stock_items WHERE id_transferencia = $id AND id_empresa = $id_empresa ";
    $this->db->query($sql);

    $sql = "DELETE FROM transferencias_stock WHERE id = $id AND id_empresa = $id_empresa ";
    $this->db->query($sql);

    echo json_encode(array());
  }

}