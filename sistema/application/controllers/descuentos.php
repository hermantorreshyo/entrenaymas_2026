<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Descuentos extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Descuento_Model', 'modelo');
  }

  function guardar_multiple() {
    $id_empresa = parent::get_empresa();
    $datos = $this->input->post("datos");
    if ($datos === FALSE) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"ERROR: No se enviaron datos.",
      ));
      return;
    }
    $this->load->helper("fecha_helper");
    $datos = json_decode($datos);
    foreach($datos->sucursales as $suc) {
      foreach($datos->items as $item) {
        $this->db->insert("articulos_descuentos_sucursales",array(
          "id_empresa"=>$id_empresa,
          "id_sucursal"=>$suc->id_sucursal,
          "id_articulo"=>$item->id_articulo,
          "precio_final"=>$item->precio_final,
          "desde"=>fecha_mysql($item->desde),
          "hasta"=>fecha_mysql($item->hasta),
        ));
      }
    }
    echo json_encode(array("error"=>0));
  }

  function eliminar_multiple() {
    $id_empresa = parent::get_empresa();
    $ids = $this->input->post("ids");
    if ($ids === FALSE) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"ERROR: No se enviaron datos.",
      ));
      return;
    }
    $ids = json_decode($ids);
    foreach($ids as $id) {
      $this->db->query("DELETE FROM articulos_descuentos_sucursales WHERE id_empresa = $id_empresa AND id = $id");
    }
    echo json_encode(array("error"=>0));
  }

  function ver() {
    
    $this->load->helper("fecha_helper");
    $filter = $this->input->get("filter");
    $id_sucursal = $this->input->get("id_sucursal");
    $limit = $this->input->get("limit");
    $offset = $this->input->get("offset");
    $fecha = $this->input->get("fecha");
    if (!empty($fecha)) $fecha = fecha_mysql($fecha);
    
    $array = $this->modelo->buscar(array(
      "filter"=>$filter,
      "fecha"=>$fecha,
      "id_sucursal"=>$id_sucursal,
      "limit"=>$limit,
      "offset"=>$offset,
    ));
    echo json_encode($array);
  }

  function exportar() {

    $this->load->helper("fecha_helper");
    $filter = $this->input->get("filter");
    $id_sucursal = $this->input->get("id_sucursal");
    $fecha = $this->input->get("fecha","");
    if (!empty($fecha)) $fecha = fecha_mysql($fecha);
    
    $array = $this->modelo->buscar(array(
      "filter"=>$filter,
      "fecha"=>$fecha,
      "id_sucursal"=>$id_sucursal,
      "offset"=>99999999,
    ));
    $datos = array();
    foreach($array["results"] as $r) {
      $codigos_barra = explode("###", $r->codigo_barra);
      $i=0;
      foreach($codigos_barra as $c) {
        if ($i==0) {
          $datos[] = array(
            "almacen"=>$r->almacen,
            "codigo"=>$r->codigo,
            "codigo_barra"=>$c,
            "custom_10"=>$r->custom_10,
            "nombre"=>$r->nombre,
            "desde"=>$r->desde,
            "hasta"=>$r->hasta,
            "precio_final"=>$r->precio_final,
          );
        } else {
          $datos[] = array(
            "almacen"=>"",
            "codigo"=>"",
            "codigo_barra"=>$c,
            "custom_10"=>"",
            "nombre"=>"",
            "desde"=>"",
            "hasta"=>"",
            "precio_final"=>"",
          );
        }
        $i++;
      }
    }
    $this->load->library("Excel");
    $this->excel->create(array(
      "date"=>"",
      "filename"=>"descuentos",
      "footer"=>array(),
      "header"=>array("Sucursal","Codigo","EAN","Prov.","Descripcion","Desde","Hasta","Precio"),
      "data"=>$datos,
      "title"=>"Descuentos",
    ));        
  }


  function imprimir() {

    $this->load->helper("fecha_helper");
    $filter = $this->input->get("filter");
    $id_sucursal = $this->input->get("id_sucursal");
    $fecha = $this->input->get("fecha","");
    if (!empty($fecha)) $fecha = fecha_mysql($fecha);
    
    $array = $this->modelo->buscar(array(
      "filter"=>$filter,
      "fecha"=>$fecha,
      "id_sucursal"=>$id_sucursal,
      "offset"=>99999999,
    ));
    $datos = array();
    foreach($array["results"] as $r) {
      $datos[] = array(
        "almacen"=>$r->almacen,
        "codigo"=>$r->codigo,
        "codigo_barra"=>$r->codigo_barra,
        "custom_10"=>$r->custom_10,
        "nombre"=>$r->nombre,
        "desde"=>$r->desde,
        "hasta"=>$r->hasta,
        "precio_final"=>$r->precio_final,
        "costo_final"=>$r->costo_final,
        "precio_anterior"=>$r->precio_anterior,
      );
    }
    $id_empresa = parent::get_empresa();
    $this->load->model("Empresa_Model");
    $empresa = $this->Empresa_Model->get($id_empresa);
    $header = $this->load->view("reports/descuento/header",null,true);
    $datos = array(
      "items"=>$datos,
      "empresa"=>$empresa,
      "header"=>$header,
    );
    $this->load->view("reports/descuento/modelo1/pedido.php",$datos);
  }
    
}