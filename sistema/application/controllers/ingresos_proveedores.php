<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Ingresos_Proveedores extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Ingreso_Proveedor_Model', 'modelo');
  }

  function pasar_cuenta() {
    $id_empresa = 868; // MEGASHOP CENTRAL
    $id_sucursal = 529; // PINAMAR
    $sql = "SELECT * FROM ingresos_proveedores WHERE estado = 1 AND id_empresa = $id_empresa AND id_almacen = $id_sucursal ";
    $q = $this->db->query($sql);
    $this->load->helper("fecha_helper");
    foreach($q->result() as $ing) {
      $ingreso = $this->modelo->get($ing->id);
      $ingreso->fecha = fecha_mysql($ingreso->fecha);
      $this->modelo->guardar_cuenta_corriente($ingreso);
    }
    echo "TERMINO";
  }

  function arreglar_central() {
    $sql = "SELECT * FROM ingresos_proveedores WHERE id_empresa = 868 ";
    $q = $this->db->query($sql);
    foreach($q->result() as $ingreso) {
      $sql = "SELECT * FROM ingresos_proveedores_items WHERE id_empresa = 868 AND id_ingreso = $ingreso->id";
      $qq = $this->db->query($sql);
      $valor = 0;
      foreach($qq->result() as $item) {
        $valor += ($item->precio_final_central * $item->cantidad);
      }
      $this->db->query("UPDATE ingresos_proveedores SET valor = $valor WHERE id = $ingreso->id AND id_empresa = 868");
    }
    echo "TERMINO";
  }

  function imprimir_remito($id) {

    error_reporting(0); // Evita que warnings/deprecations de PHP contaminen la respuesta
    
    $id_empresa = parent::get_empresa();
    $ingreso_proveedor = $this->modelo->get($id);
    
    $this->load->model("Empresa_Model");
    $empresa = $this->Empresa_Model->get($id_empresa);

    $this->load->model("Proveedor_Model");
    $proveedor = $this->Proveedor_Model->get($ingreso_proveedor->id_proveedor);

    $this->load->model("Tipo_Comprobante_Model");
    $tipo_comprobante = $this->Tipo_Comprobante_Model->get(999);
        
    $header = $this->load->view("reports/factura/header",null,true);
    
    $datos = array(
      "proveedor"=>$proveedor,
      "tipo_comprobante"=>$tipo_comprobante,
      "compra"=>$ingreso_proveedor,
      "empresa"=>$empresa,
      "header"=>$header,
    );
    $this->load->view("reports/factura/basico/ingreso_compra.php",$datos);
  }

  function imprimir_sato($id = 0) {
    $id_empresa = parent::get_empresa();
    $this->load->model("Articulo_Model");
    $ingreso = $this->modelo->get($id);

    $salida = "EAN,DESCRIPCION,PRECIO\n";
    foreach($ingreso->items as $item) {
      $articulo = $this->Articulo_Model->get($item->id_articulo);
      $lineas = ceil($item->cantidad / 3);
      for($i=0;$i<$lineas;$i++) {
        $salida.= '"'.str_pad($articulo->codigo, 12, "0", STR_PAD_LEFT).'",';
        $salida.= '"'.str_pad(substr($articulo->nombre, 0, 30), 30, " ", STR_PAD_RIGHT).'",';
        $salida.= '"$'.str_pad(substr($articulo->precio_final_dto, 0, 9), 9, " ", STR_PAD_LEFT).'",';
        $salida.= '"'.$articulo->fecha_mov.'","                    "'."\n";        
      }
    }
    header("Content-disposition: attachment; filename=etiquetr.txt");
    header("Content-type: application/octet-stream");
    echo $salida;
  }

  function buscar() {
    $this->load->helper("fecha_helper");
    $filter = (parent::get_get("filter") === FALSE) ? "" : parent::get_get("filter");
    $id_proveedor = parent::get_get("id_proveedor",0);
    $id_sucursal = parent::get_get("id_sucursal",0);
    $estado = parent::get_get("estado",0);
    $codigo_articulo = parent::get_get("codigo_articulo","");
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
      "id_proveedor"=>$id_proveedor,
      "codigo_articulo"=>$codigo_articulo,
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
    $ingreso = $this->modelo->get($id);
    if ($ingreso === FALSE || empty($ingreso)) {
      echo "Lo sentimos pero la compra ha sido eliminada.";
      exit();
    }
    $id_empresa = $ingreso->id_empresa;
    $this->load->model("Empresa_Model");
    $empresa = $this->Empresa_Model->get($id_empresa);
    $header = $this->load->view("reports/ingreso/header",null,true);
    $datos = array(
      "pedido"=>$ingreso,
      "empresa"=>$empresa,
      "header"=>$header,
      "con_precio"=>$con_precio,
    );
    $this->load->view("reports/ingreso/modelo1/pedido.php",$datos);
  }


  // TODO: Funcion de ayuda
  // Toma todos los ingresos de la sucursal y vuelve a cambiar los precios
  function recalcular_precios() {
    $id_empresa = 249;
    $id_sucursal = 21;
    $sql = "SELECT * FROM ingresos_proveedores WHERE id_empresa = $id_empresa AND id_almacen = $id_sucursal ORDER BY id ASC";
    $q = $this->db->query($sql);
    foreach($q->result() as $row) {
      $ingreso = $this->modelo->get($row->id);
      foreach($ingreso->items as $linea) {
        $this->modelo->confirmar($ingreso,$linea);
      }
    }
    echo "TERMINO";
  }

  function insert() {

    $id_empresa = parent::get_empresa();
    $this->load->model("Empresa_Model");
    $this->load->helper("fecha_helper");
    $this->load->model("Stock_Model");
    
    // Tomamos los datos
    $array = $this->parse_put();
    $array->id_empresa = $id_empresa;


    // Si no existe el comprobante
    $existe = $this->modelo->existe_comprobante(array(
      "numero_remito"=>$array->numero_remito,
      "id_empresa"=>$id_empresa,
      "id_proveedor"=>$array->id_proveedor,
      "id_almacen"=>$array->id_almacen,
    ));
    if ($existe == 1) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"ERROR: Comprobante duplicado Nro: '$array->numero_remito'.",
      ));
      exit();
    }

    $fecha = $array->fecha;
    if (isset($array->fecha)) $array->fecha = fecha_mysql($array->fecha);
    else $array->fecha = date("Y-m-d");
    
    $items = $array->items;
    $id_ingreso = $this->modelo->save($array);
    $array->id = $id_ingreso;
    
    if ($array->id_empresa == 868) {
      $valor = 0;
      foreach($items as $l) {
        if (isset($l->precio_final_central)) {
          $valor += ($l->precio_final_central * $l->cantidad);
        }
      }
      $this->db->query("UPDATE ingresos_proveedores SET valor = $valor WHERE id = $id_ingreso AND id_empresa = $id_empresa");
      $array->valor = $valor;
    }
    
    $i=0;
    foreach($items as $l) {
      $this->db->insert("ingresos_proveedores_items",array(
        "id_empresa"=>$array->id_empresa,
        "id_ingreso"=>$id_ingreso,
        "id_articulo"=>$l->id_articulo,
        "cantidad"=>$l->cantidad,
        "tipo_etiqueta"=>$l->tipo_etiqueta,
        "costo_final"=>$l->costo_final,
        "costo_neto"=>$l->costo_neto,
        "costo_neto_inicial"=>$l->costo_neto_inicial,
        "dto_prov"=>$l->dto_prov,
        "dto_prov_2"=>(isset($l->dto_prov_2) ? $l->dto_prov_2 : 0),
        "dto_prov_3"=>(isset($l->dto_prov_3) ? $l->dto_prov_3 : 0),
        "dto_prov_4"=>(isset($l->dto_prov_4) ? $l->dto_prov_4 : 0),
        "dto_prov_5"=>(isset($l->dto_prov_5) ? $l->dto_prov_5 : 0),
        "id_tipo_alicuota_iva"=>$l->id_tipo_alicuota_iva,
        "porc_iva"=>$l->porc_iva,
        "porc_ganancia"=>$l->porc_ganancia,
        "precio_neto"=>$l->precio_neto,
        "precio_final"=>$l->precio_final,
        "orden"=>$i,
        "total_neto"=>$l->total_neto,
        "total_final"=>$l->total_final,
        "bonificado"=>(isset($l->bonificado) ? $l->bonificado : 0),
        "no_editar_precios"=>(isset($l->no_editar_precios) ? $l->no_editar_precios : 0),
        "no_editar_stock"=>(isset($l->no_editar_stock) ? $l->no_editar_stock : 0),
        "porc_ganancia_sucursal"=>(isset($l->porc_ganancia_sucursal) ? $l->porc_ganancia_sucursal : 0),
        "precio_final_central"=>(isset($l->precio_final_central) ? $l->precio_final_central : 0),
      ));

      // Confirmar el ingreso significa actualizar el stock y los precios
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
      "id"=>$id_ingreso,
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
    $valor = 0;
    
    $this->db->query("DELETE FROM ingresos_proveedores_items WHERE id_ingreso = $id AND id_empresa = $id_empresa");
    $i=0;
    foreach($items as $l) {
      $this->db->insert("ingresos_proveedores_items",array(
        "id_empresa"=>$array->id_empresa,
        "id_ingreso"=>$id,
        "id_articulo"=>$l->id_articulo,
        "cantidad"=>$l->cantidad,
        "tipo_etiqueta"=>$l->tipo_etiqueta,
        "costo_final"=>$l->costo_final,
        "costo_neto"=>$l->costo_neto,
        "costo_neto_inicial"=>$l->costo_neto_inicial,
        "dto_prov"=>$l->dto_prov,
        "dto_prov_2"=>(isset($l->dto_prov_2) ? $l->dto_prov_2 : 0),
        "dto_prov_3"=>(isset($l->dto_prov_3) ? $l->dto_prov_3 : 0),
        "dto_prov_4"=>(isset($l->dto_prov_4) ? $l->dto_prov_4 : 0),
        "dto_prov_5"=>(isset($l->dto_prov_5) ? $l->dto_prov_5 : 0),
        "id_tipo_alicuota_iva"=>$l->id_tipo_alicuota_iva,
        "porc_iva"=>$l->porc_iva,
        "porc_ganancia"=>$l->porc_ganancia,
        "precio_neto"=>$l->precio_neto,
        "precio_final"=>$l->precio_final,
        "orden"=>$i,
        "total_neto"=>$l->total_neto,
        "total_final"=>$l->total_final,
        "bonificado"=>(isset($l->bonificado) ? $l->bonificado : 0),
        "no_editar_precios"=>(isset($l->no_editar_precios) ? $l->no_editar_precios : 0),
        "no_editar_stock"=>(isset($l->no_editar_stock) ? $l->no_editar_stock : 0),
        "porc_ganancia_sucursal"=>(isset($l->porc_ganancia_sucursal) ? $l->porc_ganancia_sucursal : 0),
        "precio_final_central"=>(isset($l->precio_final_central) ? $l->precio_final_central : 0),
      ));

      if (isset($l->precio_final_central)) {
        $valor += ($l->precio_final_central * $l->cantidad);
      }

      // Confirmar el ingreso significa actualizar el stock y los precios
      $this->modelo->confirmar($array,$l);

      $i++;
    }

    $this->db->query("UPDATE ingresos_proveedores SET valor = $valor WHERE id = $id AND id_empresa = $id_empresa");

    $salida = array(
      "id"=>$id,
      "error"=>0,
    );
    echo json_encode($salida);
  }

  function delete($id) {

    $id_empresa = parent::get_empresa();
    $ingreso = $this->modelo->get($id);
    if ($ingreso === FALSE) {
      echo json_encode(array(
        "error"=>1,
      ));
      exit();
    }

    // Si el ingreso fue confirmado y queremos eliminarlo, tenemos que volver a sumar los elementos en el stock de la sucursal
    if ($ingreso->estado == 1) {
      $this->load->helper("fecha_helper");
      $this->load->model("Stock_Model");
      foreach($ingreso->items as $item) {
        $fecha_eliminacion = fecha_mysql($ingreso->fecha);
        $this->Stock_Model->sacar($item->id_articulo,$item->cantidad,$ingreso->id_almacen,"B",$fecha_eliminacion,"Eliminacion ingreso",$ingreso->id_proveedor);

        // Recalculamos el stock
        $this->Stock_Model->recalcular_stock(array(
          "id_articulo"=>$item->id_articulo,
          "id_sucursal"=>$ingreso->id_almacen,
          "id_empresa"=>$ingreso->id_empresa,
        ));

      }
    }

    $sql = "DELETE FROM ingresos_proveedores_items WHERE id_ingreso = $id AND id_empresa = $id_empresa ";
    $this->db->query($sql);

    $sql = "DELETE FROM ingresos_proveedores WHERE id = $id AND id_empresa = $id_empresa ";
    $this->db->query($sql);

    // Si es un ingreso de MEGASHOP CENTRAL, tenemos que borrar las compras asociadas al mismo
    if ($ingreso->id_empresa == 868) {
      $sql = "DELETE FROM compras WHERE id_empresa = 868 AND custom_1 = '$id' AND id_sucursal = $ingreso->id_almacen ";
      $this->db->query($sql);
    }

    echo json_encode(array());
  }
    
}