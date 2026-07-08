<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Roturas_Mercaderias extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Rotura_Mercaderia_Model', 'modelo');
  }

  function buscar() {
    $this->load->helper("fecha_helper");
    $filter = (parent::get_get("filter") === FALSE) ? "" : parent::get_get("filter");
    $id_sucursal = parent::get_get("id_sucursal",0);
    $estado = parent::get_get("estado",0);
    $desde = parent::get_get("desde","");
    if (!empty($desde)) $desde = fecha_mysql($desde);
    $hasta = parent::get_get("hasta","");
    if (!empty($hasta)) $hasta = fecha_mysql($hasta);
    $limit = parent::get_get("limit","");
    $offset = parent::get_get("offset","");
    $order_by = parent::get_get("order_by","");
    $order = parent::get_get("order","");
    if (!empty($order_by) && !empty($order)) $order = $order_by." ".$order;
    else $order = "";
    $r = $this->modelo->buscar(array(
      "id_sucursal"=>$id_sucursal,
      "estado"=>$estado,
      "desde"=>$desde,
      "hasta"=>$hasta,
      "filter"=>$filter,
      "limit"=>$limit,
      "offset"=>$offset,
      "order"=>$order,
    ));
    echo json_encode($r);
  }


  function imprimir($id) {
    $this->load->helper("fecha_helper");
    $con_precio = ($this->input->get("con_precio") !== FALSE) ? $this->input->get("con_precio") : 1;
    $rotura = $this->modelo->get($id);
    if ($rotura === FALSE || empty($rotura)) {
      echo "Lo sentimos pero el elemento ha sido eliminado.";
      exit();
    }
    $id_empresa = $rotura->id_empresa;
    $this->load->model("Empresa_Model");
    $empresa = $this->Empresa_Model->get($id_empresa);
    $header = $this->load->view("reports/rotura/header",null,true);
    $datos = array(
      "pedido"=>$rotura,
      "empresa"=>$empresa,
      "header"=>$header,
      "con_precio"=>$con_precio,
    );
    $this->load->view("reports/rotura/modelo1/pedido.php",$datos);
  }


  function insert() {

    $id_empresa = parent::get_empresa();
    $this->load->model("Empresa_Model");
    $this->load->helper("fecha_helper");
    $this->load->model("Stock_Model");
    
    // Tomamos los datos
    $array = $this->parse_put();
    $array->id_empresa = $id_empresa;

    $fecha = $array->fecha;
    if (isset($array->fecha)) $array->fecha = fecha_mysql($array->fecha);
    else $array->fecha = date("Y-m-d");
    
    $items = $array->items;
    $id_rotura = $this->modelo->save($array);
    $array->id = $id_rotura;
    
    $i=0;
    foreach($items as $l) {
      $this->db->insert("roturas_mercaderias_items",array(
        "id_empresa"=>$array->id_empresa,
        "id_rotura"=>$id_rotura,
        "id_articulo"=>$l->id_articulo,
        "cantidad"=>$l->cantidad,
        "costo_final"=>$l->costo_final,
        "precio_final"=>$l->precio_final,
        "orden"=>$i,
        "total_final"=>$l->total_final,
        "no_editar_stock"=>(isset($l->no_editar_stock) ? $l->no_editar_stock : 0),
      ));

      // Confirmar el rotura significa actualizar el stock y los precios
      $this->modelo->confirmar($array,$l);

      /*
      // Si la configuracion NO ES LOCAL, tenemos que gestionar STOCK desde ACA
      // Sino, eso se hace desde el cronjob "uploader"
      if ($this->Configuracion_Model->es_local()==0) {
        $this->load->model("Stock_Model");
        $this->Stock_Model->procesar($array->id_empresa,$array->punto_venta);
      }
      */
      $i++;
    }
    echo json_encode(array(
      "id"=>$id_rotura,
      "error"=>0,
    ));
  }


  function update($id) {
        
    // Si es 0, entonces lo insertamos
    if ($id == 0) { $this->insert($id); return; }
    
    $id_empresa = parent::get_empresa();
    $this->load->helper("fecha_helper");
    $array = $this->parse_put();
    $array->id_empresa = $id_empresa;
    $fecha = $array->fecha;
    if (isset($array->fecha)) $array->fecha = fecha_mysql($array->fecha);
    else $array->fecha = date("Y-m-d");
    //$id_usuario = $_SESSION["id"];
    //$array->id_usuario = (!empty($id_usuario)) ? $id_usuario : 0;    
    $items = $array->items;
    $this->modelo->save($array);
    
    $this->db->query("DELETE FROM roturas_mercaderias_items WHERE id_rotura = $id AND id_empresa = $id_empresa");
    $i=0;
    foreach($items as $l) {
      $this->db->insert("roturas_mercaderias_items",array(
        "id_empresa"=>$array->id_empresa,
        "id_rotura"=>$id,
        "id_articulo"=>$l->id_articulo,
        "cantidad"=>$l->cantidad,
        "costo_final"=>$l->costo_final,
        "precio_final"=>$l->precio_final,
        "orden"=>$i,
        "total_final"=>$l->total_final,
        "no_editar_stock"=>(isset($l->no_editar_stock) ? $l->no_editar_stock : 0),
      ));

      // Confirmar el rotura significa actualizar el stock y los precios
      $this->modelo->confirmar($array,$l);

      $i++;
    }
    $salida = array(
      "id"=>$id,
      "error"=>0,
    );
    echo json_encode($salida);
  }

  function delete($id) {

    $id_empresa = parent::get_empresa();
    $rotura = $this->modelo->get($id);
    if ($rotura === FALSE) {
      echo json_encode(array(
        "error"=>1,
      ));
      exit();
    }

    // Si el rotura fue confirmado y queremos eliminarlo, tenemos que volver a sumar los elementos en el stock de la sucursal
    if ($rotura->estado == 1) {
      $this->load->model("Stock_Model");
      foreach($rotura->items as $item) {
        $this->Stock_Model->agregar($item->id_articulo,$item->cantidad,$rotura->id_almacen,date("Y-m-d"),"Eliminacion Remito ".$rotura->numero_remito);
      }
    }

    $sql = "DELETE FROM roturas_mercaderias_items WHERE id_rotura = $id AND id_empresa = $id_empresa ";
    $this->db->query($sql);

    $sql = "DELETE FROM roturas_mercaderias WHERE id = $id AND id_empresa = $id_empresa ";
    $this->db->query($sql);

    echo json_encode(array());
  }
    
}