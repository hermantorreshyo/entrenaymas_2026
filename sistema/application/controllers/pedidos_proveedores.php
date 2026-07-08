<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Pedidos_Proveedores extends REST_Controller {

	function __construct() {
		parent::__construct();
		$this->load->model('Pedido_Proveedor_Model', 'modelo',"fecha DESC, hora DESC");
	}

  function consulta() {

    $id_empresa = ($this->input->get("e") !== FALSE) ? $this->input->get("e") : parent::get_empresa();
    $desde = $this->input->get("desde");
    $hasta = $this->input->get("hasta");
    $id_proveedor = $this->input->get("id_proveedor");
    $id_usuario = ($this->input->get("id_usuario") !== FALSE) ? $this->input->get("id_usuario") : 0;
    $numero = $this->input->get("numero");

    $limit = $this->input->get("limit");
    $offset = $this->input->get("offset");
    $filter = $this->input->get("filter");        
    $this->load->helper("fecha_helper");
    if (!empty($desde)) $desde = fecha_mysql($desde);
    if (!empty($hasta)) $hasta = fecha_mysql($hasta);

    $lista = $this->modelo->get_all(array(
      "limit"=>$limit,
      "offset"=>$offset,
      "filter"=>$filter,
      "desde"=>$desde,
      "hasta"=>$hasta,
      "id_proveedor"=>$id_proveedor,
      "id_usuario"=>$id_usuario,
      "numero"=>$numero,
      "id_empresa"=>$id_empresa,
    ));

    $q_total = $this->db->query("SELECT FOUND_ROWS() AS total");
    $total = $q_total->row();
    $salida = array(
      "total"=> $total->total,
      "results"=>$lista,
    );
    echo json_encode($salida);
  }


  private function remove_attributes($array) {

    // Eliminamos los atributos que no se persisten
    unset($array->link);
    unset($array->undefined);
    unset($array->error);
    unset($array->ivas);
    unset($array->mensaje);
    unset($array->items);
    unset($array->tarjetas);
    unset($array->cheques);
    unset($array->cliente);
    unset($array->cliente_telefono);
    unset($array->cliente_email);
    unset($array->codigo_cliente);
    unset($array->nombre_cliente);
    unset($array->tipo_comprobante);
    unset($array->letra);
    unset($array->neto);
    unset($array->localidad);
    unset($array->provincia);
    unset($array->estado);
    unset($array->sucursal);
    unset($array->sucursal_direccion);
    unset($array->gestiona_stock);
    unset($array->proveedor);

		// Redondeamos
    $array->total = round($array->total,2);
    $array->subtotal = round($array->subtotal,2);
  }


  function insert() {

    $this->db->db_debug = FALSE;
    $id_empresa = parent::get_empresa();

    $this->load->model("Empresa_Model");
    $this->load->helper("fecha_helper");

    // Tomamos los datos
    $array = $this->parse_put();
    $array->id_empresa = $id_empresa;
    $fecha = $array->fecha;
    if (isset($array->fecha)) $array->fecha = fecha_mysql($array->fecha);
    else $array->fecha = date("Y-m-d");
    $array->hora = date("H:i:s");

    // Obtenemos el ultimo numero de pedido
    $array->numero = $this->modelo->get_proximo_numero();

    $items = $array->items;
    $this->remove_attributes($array);
    $id_pedido = $this->modelo->insert($array);

    $i=0;
    foreach($items as $l) {
      $this->db->insert("ped_pedidos_proveedores_items",array(
        "id_empresa"=>$array->id_empresa,
        "id_pedido"=>$id_pedido,
        "id_articulo"=>$l->id_articulo,
        "cantidad"=>$l->cantidad,
        "tipo_cantidad"=>$l->tipo_cantidad,
        "uxb"=>$l->uxb,
        "precio"=>$l->precio,
        "nombre"=>$l->nombre,
        "total"=>$l->total,
        "orden"=>$i,
      ));
      $i++;
    }

    echo json_encode(array(
      "id"=>$id_pedido,
      "error"=>0,
    ));
  }


  function update($id_pedido) {

    // Si es 0, entonces lo insertamos
    if ($id_pedido == 0) { $this->insert($id_pedido); return; }	  

    $this->db->db_debug = FALSE;
    $id_empresa = parent::get_empresa();

    $this->load->model("Empresa_Model");
    $this->load->helper("fecha_helper");

		// Tomamos los datos
    $array = $this->parse_put();
    $array->id_empresa = $id_empresa;
    $fecha = $array->fecha;
    if (isset($array->fecha)) $array->fecha = fecha_mysql($array->fecha);
    else $array->fecha = date("Y-m-d");
    $array->hora = date("H:i:s");

    $items = $array->items;
    $this->remove_attributes($array);
    $this->modelo->update($id_pedido,$array);

    $i=0;
    $this->db->query("DELETE FROM ped_pedidos_proveedores_items WHERE id_pedido = $id_pedido AND id_empresa = $id_empresa");
    foreach($items as $l) {
      $this->db->insert("ped_pedidos_proveedores_items",array(
        "id_empresa"=>$array->id_empresa,
        "id_pedido"=>$id_pedido,
        "id_articulo"=>$l->id_articulo,
        "cantidad"=>$l->cantidad,
        "tipo_cantidad"=>$l->tipo_cantidad,
        "uxb"=>$l->uxb,
        "precio"=>$l->precio,
        "nombre"=>$l->nombre,
        "total"=>$l->total,
        "orden"=>$i,
      ));
      $i++;
    }
    echo json_encode(array(
      "id"=>$id_pedido,
      "error"=>0,
    ));
  }

  function imprimir($id) {
    $pedido = $this->modelo->get($id);
    if ($pedido === FALSE) {
      echo "No existe un pedido con el id: $id";
      exit();
    }
    $this->load->model("Empresa_Model");
    $empresa = $this->Empresa_Model->get($pedido->id_empresa);
    // Las devoluciones las ponemos aparte
    $devoluciones = array();
    // Agrupamos los items de acuerdo al rubro
    $this->load->model("Rubro_Model");
    $set = array();
    foreach($pedido->items as $item) {
      if ($item->cantidad < 0) {
        $devoluciones[] = $item;
      } else {
        if ($item->id_rubro != 0) {
          $rubro = $this->Rubro_Model->get($item->id_rubro);
          if ($rubro->id_padre == 0) {
            if (!isset($set[$rubro->nombre])) {
              $set[$rubro->nombre] = array();
            }
            $set[$rubro->nombre][$rubro->nombre][] = $item;
            ksort($set[$rubro->nombre]);
          } else {
            $rubro_padre = $this->Rubro_Model->get($rubro->id_padre);
            if (!isset($set[$rubro_padre->nombre])) {
              $set[$rubro_padre->nombre] = array();
            }
            $set[$rubro_padre->nombre][$rubro->nombre][] = $item;
            ksort($set[$rubro_padre->nombre]);
          }
        } else {
          if (!isset($set["Sin rubro"])) $set["Sin rubro"] = array();
          $set["Sin rubro"][] = $item;
        }
      }
    }
    ksort($set);
    // Tomamos los datos del proveedor
    $this->load->model("Proveedor_Model");
    $proveedor = $this->Proveedor_Model->get($pedido->id_proveedor);

    $header = $this->load->view("reports/factura/header",null,true);

    $salida = $this->load->view("reports/pedidos_proveedores",array(
      "header"=>$header,
      "pedido"=>$pedido,
      "empresa"=>$empresa,
      "devoluciones"=>$devoluciones,
      "resultados"=>$set,
      "proveedor"=>$proveedor,
    ));
  }
    
    
  function show_error($mensaje = "Ocurrio un error al guardar el comprobante") {
    echo json_encode(array(
      "error"=>1,
      "mensaje"=>$mensaje,
      "imprimir"=>0,
    ));
    exit();		
  }	

  function delete($id = null) {
    $id_empresa = parent::get_empresa();
    $this->db->query("DELETE FROM ped_pedidos_proveedores_items WHERE id_pedido = $id AND id_empresa = $id_empresa");
    $this->db->query("DELETE FROM ped_pedidos_proveedores WHERE id = $id AND id_empresa = $id_empresa");
    echo json_encode(array());
  }

}