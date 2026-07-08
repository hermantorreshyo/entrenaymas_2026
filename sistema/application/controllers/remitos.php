<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Remitos extends REST_Controller {
  
  function __construct() {
    parent::__construct();
    $this->load->model('Factura_Model', 'modelo');
  }
      
  // Consultamos por el proximo numero de remito
  function next($id_punto_venta = 0) {
    $id_empresa = parent::get_empresa();
    $sql = "SELECT IF(MAX(numero) IS NULL,0,MAX(numero)) AS ultimo ";
    $sql.= "FROM facturas ";
    $sql.= "WHERE id_empresa = $id_empresa AND id_tipo_comprobante = 999 ";
    $sql.= "AND id_punto_venta = $id_punto_venta ";
    $q = $this->db->query($sql);
    $salida = array();
    foreach($q->result() as $r) {
      $salida[999] = $r->ultimo + 1;
    }
    echo json_encode($salida);
  }
  
  function ver_pdf($id_factura,$id_punto_venta) {
    
    $this->load->helper("fecha_helper");
    $this->load->helper("numero_letra_helper");

    $facturas = array();
    $ids = explode("-", $id_factura);
    foreach($ids as $id_factura) {
      $facturas[] = $this->modelo->get($id_factura,$id_punto_venta);
    }

    $titulo = "Remito";
    if (sizeof($facturas)==1) {
      $fact_0 = $facturas[0];
      $titulo = $fact_0->comprobante;
    }
    
    $id_empresa = parent::get_empresa();
    $this->load->model("Empresa_Model");
    $empresa = $this->Empresa_Model->get($id_empresa);
    $disenio = $empresa->config["disenio_factura"];
    if (empty($disenio)) $disenio = "basico";

    $header = $this->load->view("reports/factura/header",null,true);    
    $folder = "/sistema/application/views/reports/factura/$disenio";
    
    $datos = array(
      "facturas"=>$facturas,
      "empresa"=>$empresa,
      "header"=>$header,
      "letras"=> new EnLetras(),
      "folder"=>$folder,
      "titulo"=>$titulo,
    );
    $this->load->view("reports/factura/$disenio/remito.php",$datos);
  }
  
  /**
   * ESTA FUNCION LA USAN LOS CLIENTES DE LA EMPRESA PARA VER SUS COMPROBANTES
   */
  function ver($hash) {
    
    $this->load->helper("fecha_helper");
    $this->load->helper("numero_letra_helper");
    $factura = $this->modelo->get_by_hash($hash);
    
    $this->load->model("Tipo_Comprobante_Model");
    $tipo_comprobante = $this->Tipo_Comprobante_Model->get($factura->id_tipo_comprobante);
    
    $this->load->model("Empresa_Model");
    $empresa = $this->Empresa_Model->get($factura->id_empresa);
    
    $this->load->model("Punto_Venta_Model");
    $punto_venta = $this->Punto_Venta_Model->get($factura->id_punto_venta);
    
    $header = $this->load->view("reports/factura/header",null,true);
    
    $tpl = $empresa->config["facturacion_template_factura"];
    
    // Indicamos que el cliente vio la factura
    $this->db->query("UPDATE facturas SET visto = visto + 1 WHERE id = $factura->id");
    
    $this->load->model("Log_Model");
    $this->Log_Model->notify(array(
      "texto"=>"$factura->cliente ha visto $factura->comprobante",
      "link"=>"facturacion/$factura->id",
      "id_empresa"=>$empresa->id,
    ));
    
    $datos = array(
      "tipo_comprobante"=>$tipo_comprobante,
      "factura"=>$factura,
      "empresa"=>$empresa,
      "header"=>$header,
      "letras"=> new EnLetras(),
      "folder"=>"/application/views/reports/factura/$tpl",
    );
    $this->load->view("reports/factura/$tpl/factura.php",$datos);
    
  }    
  
  private function limpiar($array) {
    
    // Eliminamos los atributos que no se persisten
    unset($array->undefined);
    unset($array->error);
    unset($array->ivas);
    unset($array->mensaje);
    unset($array->items);
    unset($array->tarjetas);
    unset($array->cheques);
    unset($array->cliente);
    unset($array->cliente_email);
    unset($array->cliente_telefono);
    unset($array->codigo_cliente);
    unset($array->nombre_cliente);
    unset($array->tipo_comprobante);
    unset($array->letra);
    unset($array->gestiona_stock);
    
    // Redondeamos
    $array->total = round($array->total,2);
    $array->subtotal = round($array->subtotal,2);
    $array->neto = round($array->neto,2);
    $array->iva = round($array->iva,2);
    $array->porc_descuento = round($array->porc_descuento,2);
    $array->descuento = round($array->descuento,2);
    $array->efectivo = round($array->efectivo,2);
    $array->cta_cte = round($array->cta_cte,2);
    $array->tarjeta = round($array->tarjeta,2);
    $array->cheque = round($array->cheque,2);
    $array->vuelto = round($array->vuelto,2);
    
    return $array;
  }
  
  function show_error($mensaje = "Ocurrio un error al guardar el comprobante") {
    echo json_encode(array(
      "error"=>1,
      "mensaje"=>$mensaje,
      "imprimir"=>0,
    ));
    exit();    
  }
    
  function insert() {
    
    $this->db->db_debug = FALSE;
    $id_empresa = parent::get_empresa();
    
    $this->load->model("Tipo_Comprobante_Model");
    $this->load->model("Empresa_Model");
    $this->load->model("Log_Model");
    $this->load->helper("fecha_helper");
    $this->load->model("Stock_Model");
    $this->load->model("Almacen_Model");
    
    // Comenzamos la transaccion
    $this->db->trans_start();
    
    // Tomamos los datos
    $array = $this->parse_put();

    // Obtenemos el tipo de comprobante
    $tipo_comprobante = $this->Tipo_Comprobante_Model->get($array->id_tipo_comprobante);
    
    if (is_null($array->id_vendedor)) $array->id_vendedor = 0;
    $array->id_empresa = $id_empresa;
    $array->estado = 1;
    $fecha = $array->fecha;
    if (isset($array->fecha)) $array->fecha = fecha_mysql($array->fecha);
    else $array->fecha = date("Y-m-d");
    $array->hora = date("H:i:s");
    
    if (isset($array->fecha_reparto)) $array->fecha_reparto = fecha_mysql($array->fecha_reparto);
    else $array->fecha_reparto = date("Y-m-d");        
    
    $id_usuario = $_SESSION["id"];
    $array->id_usuario = (!empty($id_usuario)) ? $id_usuario : 0;
    
    $array->comprobante = "R 0001-".str_pad($array->numero,8,"0",STR_PAD_LEFT);
    
    $array->hash = md5($array->id_empresa.$array->comprobante);

    $items = $array->items;
    $tarjetas = $array->tarjetas;
    $cheques = $array->cheques;
    $this->limpiar($array);
    
    // Si el comprobante es en EFECTIVO
    if ($array->tipo_pago == "E") {
      $array->pago = -$array->total;
      $array->pagada = 1;
    }
        
    $id_factura = $this->modelo->save($array);
    
    // Guardamos la relacion con vendedores para que se vaya formando
    if ($array->id_cliente != 0 && isset($array->id_vendedor) && $array->id_vendedor != 0) {
      $this->db->query("UPDATE clientes SET id_vendedor = $array->id_vendedor WHERE id = $array->id_cliente");
    }
        
    $ivas = array();
    $i=0;
    foreach($items as $l) {
      $this->db->insert("facturas_items",array(
        "id_empresa"=>$array->id_empresa,
        "id_punto_venta"=>$array->id_punto_venta,
        "id_factura"=>$id_factura,
        "id_articulo"=>$l->id_articulo,
        "cantidad"=>$l->cantidad,
        "precio"=>$l->precio,
        "neto"=>$l->neto,
        "porc_iva"=>$l->porc_iva,
        "id_tipo_alicuota_iva"=>$l->id_tipo_alicuota_iva,
        "nombre"=>$l->nombre,
        "total_con_iva"=>$l->total_con_iva,
        "total_sin_iva"=>$l->total_sin_iva,
        "iva"=>$l->iva,
        "orden"=>$i,
        "id_rubro"=>(isset($l->id_rubro) ? $l->id_rubro : 0),
        "bonificacion"=>$l->bonificacion,
        "costo_final"=>(isset($l->costo_final) ? $l->costo_final : 0),
        "anulado"=>(isset($l->anulado) ? $l->anulado : 0),
        "stamp"=>time(),
      ));

      // SI ES PELUNCHOS, DESCONTAMOS DE STOCK
      if ($id_empresa == 134) {
        $id_sucursal = $this->Almacen_Model->get_sucursal_punto_venta($array->id_punto_venta);
        $this->Stock_Model->sacar($l->id_articulo,$l->cantidad,$id_sucursal);  
      }      

      $i++;
    }
    
    // Guardamos la cotizacion nueva del dolar
    //if (isset($array->cotizacion_dolar) && !empty($array->cotizacion_dolar)) {
    //  $this->db->query("UPDATE fact_configuracion SET cotizacion_dolar = $array->cotizacion_dolar WHERE id_empresa = $array->id_empresa");
    //}
    
    // Finalizamos la transaccion
    $this->db->trans_complete();
    if ($this->db->trans_status() === FALSE) $this->show_error();
    
    // Actualizamos el ultimo numero del comprobante
    if ($array->numero != 0) {
      $this->db->query("UPDATE numeros_comprobantes SET ultimo = $array->numero WHERE id_empresa = $array->id_empresa AND id_tipo_comprobante = $array->id_tipo_comprobante");
    }
    
    $this->Log_Model->log("ha realizado el remito $array->comprobante","app/#remitos/".$id_factura,"I");
    
    echo json_encode(array(
      "id"=>$id_factura,
      "error"=>0,
      "imprimir"=>1,
      "tipo_impresion"=>"P"
    ));
  }
  
  
  function update($id) {
        
    // Si es 0, entonces lo insertamos
    if ($id == 0) { $this->insert($id); return; }
    
    $id_empresa = parent::get_empresa();
        
    $this->load->helper("fecha_helper");
    $this->load->model("Tipo_Comprobante_Model");
    $this->load->model("Log_Model");
    $this->load->model("Stock_Model");
    $this->load->model("Almacen_Model");
    
    // Comenzamos la transaccion
    $this->db->trans_start();    
    
    $array = $this->parse_put();

    // Si es PELUNCHOS
    if ($id_empresa == 134) {
      $remito = $this->modelo->get($array->id,$array->id_punto_venta);
      $id_sucursal = $this->Almacen_Model->get_sucursal_punto_venta($array->id_punto_venta);
      foreach($remito->items as $l) {
        $this->Stock_Model->agregar($l->id_articulo,$l->cantidad,$id_sucursal);
      }
    }
    
    // Obtenemos el tipo de comprobante
    $tipo_comprobante = $this->Tipo_Comprobante_Model->get($array->id_tipo_comprobante);
    
    $array->estado = 1;
    $array->id_empresa = $id_empresa;
    $fecha = $array->fecha;
    if (isset($array->fecha)) $array->fecha = fecha_mysql($array->fecha);
    else $array->fecha = date("Y-m-d");
    $array->hora = date("H:i:s");

    if (isset($array->fecha_reparto)) $array->fecha_reparto = fecha_mysql($array->fecha_reparto);
    else $array->fecha_reparto = date("Y-m-d");

    $id_usuario = $_SESSION["id"];
    $array->id_usuario = (!empty($id_usuario)) ? $id_usuario : 0;    
        
    $array->comprobante = "R 0001-".str_pad($array->numero,8,"0",STR_PAD_LEFT);
    $array->hash = md5($array->id_empresa.$array->comprobante);    
        
    $items = $array->items;
    $tarjetas = $array->tarjetas;
    $cheques = $array->cheques;
    $this->limpiar($array);
    
    // Si el comprobante es en EFECTIVO
    if ($array->tipo_pago == "E") {
      $array->pago = -$array->total;
      $array->pagada = 1;
    }    
    
    $this->modelo->save($array);
    
    // Guardamos la relacion con vendedores
    if ($array->id_cliente != 0 && $array->id_vendedor != 0) {
      $this->db->query("UPDATE clientes SET id_vendedor = $array->id_vendedor WHERE id = $array->id_cliente");
    }    
        
    $this->db->query("DELETE FROM facturas_items WHERE id_factura = $id AND id_empresa = $id_empresa");
    $ivas = array();
    $i=0;
    foreach($items as $l) {
      $this->db->insert("facturas_items",array(
        "id_empresa"=>$array->id_empresa,
        "id_punto_venta"=>$array->id_punto_venta,
        "id_factura"=>$id,
        "id_articulo"=>$l->id_articulo,
        "cantidad"=>$l->cantidad,
        "precio"=>$l->precio,
        "neto"=>$l->neto,
        "porc_iva"=>$l->porc_iva,
        "id_tipo_alicuota_iva"=>$l->id_tipo_alicuota_iva,
        "nombre"=>$l->nombre,
        "total_con_iva"=>$l->total_con_iva,
        "total_sin_iva"=>$l->total_sin_iva,
        "iva"=>$l->iva,
        "orden"=>$i,
        "id_rubro"=>$l->id_rubro,
        "bonificacion"=>$l->bonificacion,
        "costo_final"=>(isset($l->costo_final) ? $l->costo_final : 0),
        "anulado"=>(isset($l->anulado) ? $l->anulado : 0),
        "stamp"=>time(),
      ));

      // SI ES PELUNCHOS, DESCONTAMOS DE STOCK
      if ($id_empresa == 134) {
        $id_sucursal = $this->Almacen_Model->get_sucursal_punto_venta($array->id_punto_venta);
        $this->Stock_Model->sacar($l->id_articulo,$l->cantidad,$id_sucursal);  
      }

      $i++;
    }

    // Guardamos la cotizacion nueva del dolar
    //if (isset($array->cotizacion_dolar) && !empty($array->cotizacion_dolar)) {
    //  $this->db->query("UPDATE fact_configuracion SET cotizacion_dolar = $array->cotizacion_dolar WHERE id_empresa = $array->id_empresa");
    //}
    
    // Finalizamos la transaccion
    $this->db->trans_complete();
    if ($this->db->trans_status() === FALSE) $this->show_error();
    
    $this->Log_Model->log("ha modificado el remito $array->comprobante","app/#remitos/".$id);
    
    $imprimir = 0;  
    $salida = array(
      "id"=>$id,
      "imprimir"=>$imprimir,
      "error"=>0,
      "tipo_impresion"=>"P",
    );
    echo json_encode($salida);
  }
    
  function delete($id = null) {
    $id_empresa = parent::get_empresa();

    // Si es PELUNCHOS
    if ($id_empresa == 134) {
      $this->load->model("Almacen_Model");
      $this->load->model("Stock_Model");
      $remito = $this->modelo->get($id);
      $id_sucursal = $this->Almacen_Model->get_sucursal_punto_venta($remito->id_punto_venta);
      foreach($remito->items as $l) {
        $this->Stock_Model->agregar($l->id_articulo,$l->cantidad,$id_sucursal);
      }
    }

    $this->db->query("DELETE FROM facturas_iva WHERE id_factura = $id AND id_empresa = $id_empresa");
    $this->db->query("DELETE FROM facturas_items WHERE id_factura = $id AND id_empresa = $id_empresa");
    $this->db->query("DELETE FROM facturas WHERE id = $id AND id_empresa = $id_empresa");
    echo json_encode(array());
  }    
    
  function consulta() {
    
    $id_empresa = parent::get_empresa();
    $desde = $this->input->get("desde");
    $hasta = $this->input->get("hasta");
    $id_cliente = $this->input->get("id_cliente");
    $id_vendedor = $this->input->get("id_vendedor");
    $numero = $this->input->get("numero");
    $numero_reparto = $this->input->get("numero_reparto");
    $incluir_saldo = $this->input->get("incluir_saldo");
    $estado = (!isset($_SESSION["estado"])) ? 0 : (($_SESSION["estado"]==1)?1:0);
    $tipos_comprobantes = $this->input->get("tc");
        
    $limit = $this->input->get("limit");
    $offset = $this->input->get("offset");
    $filter = $this->input->get("filter");        
    $this->load->helper("fecha_helper");
    if (!empty($desde)) $desde = fecha_mysql($desde);
    if (!empty($hasta)) $hasta = fecha_mysql($hasta);
    $r_facturas = 0;
    $r_clientes = 0;
    
    // Consultamos la cantidad de clientes y la cantidad de facturas de ese reparto        
    if (!empty($numero_reparto)) {
      $sql = "SELECT IF(COUNT(*) IS NULL,0,COUNT(*)) AS cantidad FROM facturas F WHERE F.anulada = 0 ";
      $sql.= "AND F.fecha_reparto = '$desde' ";
      $sql.= "AND F.reparto = $numero_reparto ";
      $sql.= "AND F.id_empresa = $id_empresa ";
      $q = $this->db->query($sql);
      $r_facturas = $q->row();      
    }
    if (!empty($numero_reparto)) {
      $sql = "SELECT IF(COUNT(DISTINCT id_cliente) IS NULL,0,COUNT(DISTINCT id_cliente)) AS cantidad FROM facturas F WHERE F.anulada = 0 ";
      $sql.= "AND F.fecha_reparto = '$desde' ";
      $sql.= "AND F.reparto = $numero_reparto ";
      $sql.= "AND F.id_empresa = $id_empresa ";
      $q = $this->db->query($sql);
      $r_clientes = $q->row();        
    }
        
    $lista = $this->modelo->get_all(array(
      "limit"=>$limit,
      "offset"=>$offset,
      "filter"=>$filter,
      "desde"=>$desde,
      "hasta"=>$hasta,
      "id_cliente"=>$id_cliente,
      "id_vendedor"=>$id_vendedor,
      "numero"=>$numero,
      "estado"=>$estado,
      "tipos_comprobantes"=>$tipos_comprobantes,
      "numero_reparto"=>$numero_reparto,
      "id_empresa"=>$id_empresa,
    ));

    if ($incluir_saldo == 1) {
      $this->load->model("Cliente_Model");
      foreach($lista as $l) {
        $l->saldo = $this->Cliente_Model->saldo($l->id_cliente,$id_empresa,date("Y-m-d"));
      }
    }
    
    $q_total = $this->db->query("SELECT FOUND_ROWS() AS total");
    $total = $q_total->row();
    $salida = array(
      "total"=> $total->total,
      "results"=>$lista,
      "meta"=>array(
        "cantidad_facturas"=>(empty($r_facturas)) ? 0 : $r_facturas->cantidad,
        "cantidad_clientes"=>(empty($r_clientes)) ? 0 : $r_clientes->cantidad,
      ),
    );
    echo json_encode($salida);
  }
  
}