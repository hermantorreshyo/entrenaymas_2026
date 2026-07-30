<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Distrivar extends REST_Controller {
  
  function __construct() {
    parent::__construct();
  }

  function get_posiciones() {
    $this->load->helper("fecha_helper");
    $vista = parent::get_post("vista",1);
    $id_vendedor = parent::get_post("id_vendedor",0);
    $id_empresa = parent::get_post("id_empresa",parent::get_empresa());
    $fecha_desde = parent::get_post("fecha_desde",date("d/m/Y"));
    $fecha_desde = fecha_mysql($fecha_desde);
    $fecha_hasta = parent::get_post("fecha_hasta",date("d/m/Y"));
    $fecha_hasta = fecha_mysql($fecha_hasta);
    $hora_desde = parent::get_post("hora_desde","00:00:00");
    $hora_hasta = parent::get_post("hora_hasta","23:59:59");
    $sql = "SELECT latitud,longitud,tiempo FROM vendedores_posiciones ";
    $sql.= "WHERE id_empresa = $id_empresa ";
    $sql.= "AND id_vendedor = $id_vendedor ";
    $sql.= "AND '$fecha_desde $hora_desde' <= tiempo ";
    $sql.= "AND tiempo <= '$fecha_hasta $hora_hasta' ";
    if ($vista == 2) $sql.= "ORDER BY tiempo DESC LIMIT 0,1 ";
    $q = $this->db->query($sql);
    $salida = $q->result();
    echo json_encode(array(
      "results"=>$salida,
    ));
  }

  // Utilizado en la nueva app para registrar el seguimiento
  function guardar_coordenadas() {
    header('Access-Control-Allow-Origin: *');
    $this->load->model("Log_Model");
    $id_vendedor = parent::get_post("id_vendedor",0);
    $id_empresa = parent::get_post("id_empresa",0);
    $lat = parent::get_post("lat",0);
    $lon = parent::get_post("lon",0);
    if ($lat != 0 && $lon != 0) {
      $sql = "INSERT INTO vendedores_posiciones (id_empresa,id_vendedor,latitud,longitud,tiempo ";
      $sql.= ") VALUES (";
      $sql.= " '$id_empresa','$id_vendedor','$lat','$lon',NOW() ";
      $sql.= ")";
      $this->db->query($sql);
      $linea = "LAT: $lat - LON: $lon";
      $this->Log_Model->imprimir(array(
        "id_empresa"=>$id_empresa,
        "id_usuario"=>0,
        "file"=>"pos_".$id_vendedor.".txt",
        "texto"=>$linea
      ));
    }
    echo json_encode(array("error"=>0));
  }  

  function guardar_posicion_get() {
    header('Access-Control-Allow-Origin: *');
    $this->load->model("Log_Model");
    $id_vendedor = parent::get_get("id_vendedor",0);
    $id_empresa = parent::get_get("id_empresa",0);
    $lat = parent::get_get("lat",0);
    $lon = parent::get_get("lon",0);
    if ($lat != 0 && $lon != 0) {
      $sql = "INSERT INTO vendedores_posiciones (id_empresa,id_vendedor,latitud,longitud,tiempo ";
      $sql.= ") VALUES (";
      $sql.= " '$id_empresa','$id_vendedor','$lat','$lon',NOW() ";
      $sql.= ")";
      $this->db->query($sql);
      $linea = "LAT: $lat - LON: $lon";
      $this->Log_Model->imprimir(array(
        "id_empresa"=>$id_empresa,
        "id_usuario"=>0,
        "file"=>"pos_".$id_vendedor.".txt",
        "texto"=>$linea
      ));
    }
    echo json_encode(array("error"=>0));
  }  

  function guardar_posicion() {
    header('Access-Control-Allow-Origin: *');
    $this->load->model("Log_Model");
    $id_vendedor = parent::get_post("id_vendedor",0);
    $id_empresa = parent::get_post("id_empresa",0);
    $lat = parent::get_post("lat",0);
    $lon = parent::get_post("lon",0);
    if ($lat != 0 && $lon != 0) {
      $sql = "INSERT INTO vendedores_posiciones (id_empresa,id_vendedor,latitud,longitud,tiempo ";
      $sql.= ") VALUES (";
      $sql.= " '$id_empresa','$id_vendedor','$lat','$lon',NOW() ";
      $sql.= ")";
      $this->db->query($sql);
      $linea = "LAT: $lat - LON: $lon";
      $this->Log_Model->imprimir(array(
        "id_empresa"=>$id_empresa,
        "id_usuario"=>0,
        "file"=>"pos_".$id_vendedor.".txt",
        "texto"=>$linea
      ));
    }
    echo json_encode(array("error"=>0));
  }  

  private function get_params() {    
    $conf = array();
    $this->load->helper("fecha_helper");
    $desde = $this->input->get("desde");
    if ($desde !== FALSE) $conf["desde"] = fecha_mysql($desde);
    $hasta = $this->input->get("hasta");
    if ($hasta !== FALSE) $conf["hasta"] = fecha_mysql($hasta);
    $id_cliente = $this->input->get("id_cliente");
    if ($id_cliente !== FALSE) $conf["id_cliente"] = $id_cliente;
    $id_vendedor = $this->input->get("id_vendedor");
    if ($id_vendedor !== FALSE) $conf["id_vendedor"] = $id_vendedor;
    $id_tarjeta = $this->input->get("id_tarjeta");
    if ($id_tarjeta !== FALSE) $conf["id_tarjeta"] = $id_tarjeta;
    $lote = $this->input->get("lote");
    if ($lote !== FALSE) $conf["lote"] = $lote;
    $cupon = $this->input->get("cupon");
    if ($cupon !== FALSE) $conf["cupon"] = $cupon;
    $id_sucursal = $this->input->get("id_sucursal");
    if ($id_sucursal !== FALSE) $conf["id_sucursal"] = $id_sucursal;
    $id_punto_venta = $this->input->get("id_punto_venta");
    if ($id_punto_venta !== FALSE) $conf["id_punto_venta"] = $id_punto_venta;
    $con_anulados = $this->input->get("con_anulados");
    if ($con_anulados !== FALSE) $conf["con_anulados"] = $con_anulados;
    $id_usuario = $this->input->get("id_usuario");
    if ($id_usuario !== FALSE) $conf["id_usuario"] = $id_usuario;
    $numero = $this->input->get("numero");
    if ($numero !== FALSE) $conf["numero"] = $numero;
    $monto = $this->input->get("monto");
    if ($monto !== FALSE) $conf["monto"] = $monto;
    $monto_tipo = $this->input->get("monto_tipo");
    if ($monto_tipo !== FALSE) $conf["monto_tipo"] = $monto_tipo;
    $caja_abierta = $this->input->get("caja_abierta");
    if ($caja_abierta !== FALSE) $conf["caja_abierta"] = $caja_abierta;
    $numero_reparto = $this->input->get("numero_reparto");
    if ($numero_reparto !== FALSE) $conf["numero_reparto"] = $numero_reparto;
    $incluir_saldo = $this->input->get("incluir_saldo");
    if ($incluir_saldo !== FALSE) $conf["incluir_saldo"] = $incluir_saldo;
    $conf["estado"] = (!isset($_SESSION["estado"])) ? 0 : (($_SESSION["estado"]==1)?1:0);
    $tipos_comprobantes = $this->input->get("tc");
    if ($tipos_comprobantes !== FALSE) $conf["tc"] = $tipos_comprobantes;
    $limit = $this->input->get("limit");
    if ($limit !== FALSE) $conf["limit"] = $limit;
    $offset = $this->input->get("offset");
    if ($offset !== FALSE) $conf["offset"] = $offset;
    $filter = $this->input->get("filter");
    if ($filter !== FALSE) $conf["filter"] = $filter;
    $conf["incluir_suma"] = parent::get_get("incluir_suma",0);
    $conf["fecha_reparto"] = parent::get_get("fecha_reparto","");
    $conf["numero_reparto"] = parent::get_get("numero_reparto","");
    $conf["tipo_cliente"] = parent::get_get("tipo_cliente","");
    $conf["forma_pago"] = parent::get_get("forma_pago","0");
    $conf["id_concepto"] = parent::get_get("id_concepto","0");
    $conf["tipo_estado"] = parent::get_get("tipo_estado","-1");
    $conf["in_tipos_estados"] = str_replace("-", ",", parent::get_get("in_tipos_estados",""));
    $conf["id_proyecto"] = ($this->input->get("id_proyecto") !== FALSE) ? $this->input->get("id_proyecto") : 0;
    $conf["codigo_articulo"] = ($this->input->get("codigo_articulo") !== FALSE) ? $this->input->get("codigo_articulo") : "";
    $conf["tipos"] = ($this->input->get("tipos") !== FALSE) ? $this->input->get("tipos") : "";
    return $conf;
  }  

  // UTILIZADA PARA DON YEYO
  function exportar_excel() {
    $this->load->model("Venta_Model");
    $conf = $this->get_params();
    $resultado = array();
    $header = array(
      "Empresa","Fecha","Hora","Comprobante","Numero Pedido","Cod.Vendedor","Vendedor","Cod.Cliente","Cliente","Direccion","Art. Codigo","Rubro","Art. Nombre","Precio Unit.","Venta","Cambio","Bonificado","Devolucion","Subtotal","Entregado","Observaciones",
    );

    $this->load->model("Empresa_Model");
    $ids_empresas = $this->Empresa_Model->get_ids_empresas_por_vendedor($this->Empresa_Model->get_id_vendedor_don_yeyo());

    $sql = "SELECT FI.*, DATE_FORMAT(F.fecha,'%d/%m/%Y') AS fecha, F.comprobante, ";
    $sql.= " F.empresa, F.hora, F.vendedor, F.cliente, A.codigo AS articulo_codigo, A.nombre AS articulo_nombre, ";
    $sql.= " C.direccion AS direccion_cliente, F.observaciones, F.numero, ";
    $sql.= " IF(R.nombre IS NULL,'',R.nombre) AS articulo_rubro, ";
    $sql.= " C.codigo AS codigo_cliente, V.codigo AS codigo_vendedor, IF(F.coordinar_envio = 1,'Entregado','No entregado') AS entregado ";
    $sql.= "FROM facturas_items FI ";
    $sql.= "INNER JOIN facturas F ON (F.id_empresa = FI.id_empresa AND F.id_punto_venta = FI.id_punto_venta AND F.id = FI.id_factura) ";
    $sql.= "INNER JOIN clientes C ON (F.id_cliente = C.id AND F.id_empresa = C.id_empresa) ";
    $sql.= "INNER JOIN articulos A ON (FI.id_articulo = A.id AND FI.id_empresa = A.id_empresa) ";
    $sql.= "LEFT JOIN rubros R ON (A.id_empresa = R.id_empresa AND A.id_rubro = R.id) ";
    $sql.= "INNER JOIN vendedores V ON (F.id_vendedor = V.id AND V.id_empresa = F.id_empresa) ";
    $sql.= "WHERE F.anulada = 0 ";
    if (!empty($ids_empresas)) {
      $ids_empresas = implode(",", $ids_empresas);
      $sql.= "AND F.id_empresa IN ($ids_empresas) ";
    }
    if (!empty($conf["desde"])) $sql.= "AND F.fecha >= '".$conf["desde"]."' ";
    if (!empty($conf["hasta"])) $sql.= "AND F.fecha <= '".$conf["hasta"]."' ";
    $sql.= "ORDER BY F.fecha ASC, F.hora ASC, FI.orden ASC ";
    $q = $this->db->query($sql);
    foreach($q->result() as $r) {
      $row = new stdClass();
      $resultado[] = array(
        $r->empresa,
        $r->fecha,
        $r->hora,
        $r->comprobante,
        $r->numero,
        $r->codigo_vendedor,
        $r->vendedor,
        $r->codigo_cliente,
        $r->cliente,
        $r->direccion_cliente,
        $r->articulo_codigo,
        $r->articulo_rubro,
        $r->articulo_nombre,
        $r->precio,
        (empty($r->tipo_cantidad) ? abs($r->cantidad) : "0"),
        (($r->tipo_cantidad == "C") ? abs($r->cantidad) : "0"),
        (($r->tipo_cantidad == "B") ? abs($r->cantidad) : "0"),
        (($r->tipo_cantidad == "D") ? abs($r->cantidad) : "0"),
        $r->total_con_iva,
        $r->entregado,
        $r->observaciones,
      );
    }
    $this->load->library("Excel");
    $this->excel->create(array(
      "date"=>date("d/m/Y"),
      "filename"=>"listado_ventas",
      "header"=>$header,
      "footer"=>array(),
      "datos"=>$resultado,
      "title"=>"Exportacion",
    ));    
  }  

  function get_repartos() {
    header('Access-Control-Allow-Origin: *');
    $id_empresa = parent::get_post("id_empresa",0);
    $reparto = parent::get_post("reparto",0);
    $fecha_reparto = parent::get_post("fecha_reparto","");
    $filter = parent::get_post("filter","");
    $this->load->helper("fecha_helper");
    if (!empty($fecha_reparto)) $fecha_reparto = fecha_mysql($fecha_reparto);
    $sql = "SELECT F.*, C.nombre AS cliente, C.direccion AS direccion, ";
    $sql.= " IF(F.fecha_reparto = '0000-00-00','',DATE_FORMAT(F.fecha_reparto,'%d/%m/%Y')) AS fecha_reparto ";
    $sql.= "FROM facturas F INNER JOIN clientes C ON (F.id_cliente = C.id AND F.id_empresa = C.id_empresa) ";
    $sql.= "WHERE F.id_empresa = $id_empresa ";
    if (!empty($filter)) $sql.= "AND (C.nombre LIKE '%$filter%' OR C.direccion LIKE '%$filter%') ";
    if (!empty($reparto)) $sql.= "AND F.reparto = '$reparto' ";
    if (!empty($fecha_reparto)) $sql.= "AND F.fecha_reparto = '$fecha_reparto' ";
    $sql.= "AND F.id_tipo_estado != 7 ";
    $sql.= "ORDER BY F.fecha_reparto DESC ";
    $sql.= "LIMIT 0,100 ";
    $q = $this->db->query($sql);
    echo json_encode(array("repartos"=>$q->result()));
  }

  function get_pedido() {
    error_reporting(0); // Evita que warnings/deprecations de PHP contaminen la respuesta
    header('Access-Control-Allow-Origin: *');
    $id_empresa = parent::get_post("id_empresa",0);
    $id = parent::get_post("id",0);
    $id_punto_venta = parent::get_post("id_punto_venta",0);
    $this->load->model("Factura_Model");
    $factura = $this->Factura_Model->get($id,$id_punto_venta,array(
      "id_empresa"=>$id_empresa,
      "buscar_consultas"=>0,
      "buscar_etiquetas"=>0,
    ));
    if (empty($factura)) {
      echo json_encode(array("error"=>0));
    } else {
      $f = new stdClass();
      $f->id = $factura->id;
      $f->id_cliente = $factura->id_cliente;
      $f->id_empresa = $factura->id_empresa;
      $f->id_recorrido = 0;
      $f->id_tipo_estado = $factura->id_tipo_estado;
      $f->uploaded = 1;
      $f->reparto = $factura->reparto;
      $f->total = $factura->total;
      $f->fecha = $factura->fecha;
      $f->hora = $factura->hora;
      $f->observaciones = $factura->observaciones;
      $f->fecha_reparto = $factura->fecha_reparto;
      $f->latitud = $factura->custom_7;
      $f->longitud = $factura->custom_8;
      $f->id_vendedor = $factura->id_vendedor;
      $f->error = 0;
      $f->facturaItemsArray = array();
      foreach($factura->items as $item) {
        $it = new stdClass();
        $it->id_facturaItem = $item->id;
        $it->id_factura = $factura->id;
        $it->codigoArticulo = $item->codigo;
        $it->descripcion = $item->nombre;
        
        if ($item->tipo_cantidad == "B") $it->tipoCantidad = "Bonificado";
        else if ($item->tipo_cantidad == "C") $it->tipoCantidad = "Cambio";
        else if ($item->tipo_cantidad == "D") $it->tipoCantidad = "Devolucion";
        else $item->tipoCantidad = $item->tipo_cantidad;

        $it->id_articulo = $item->id_articulo;
        $it->cantidad = $item->cantidad;
        $it->porc_bonif = $item->bonificacion;
        $it->lista_precios = 1; // TODO: Falta esto
        $it->neto = ((float)$item->neto);
        $it->precio = ((float)$item->precio);
        $it->subtotal = ((float)$item->total_con_iva);
        $it->stock = 0;
        $it->uploaded = 1;
        $f->facturaItemsArray[] = $it;
      }
      $c = $factura->cliente;
      echo json_encode(array(
        "factura"=>$f,
        "cliente"=>$c,
      ));
    }
  }

  function marcar_reparto_entregado() {
    header('Access-Control-Allow-Origin: *');
    $id = parent::get_post("id",0);
    $id_empresa = parent::get_post("id_empresa",0);
    $id_punto_venta = parent::get_post("id_punto_venta",0);
    $entregado = parent::get_post("entregado",1);
    $observaciones = parent::get_post("observaciones","");
    $sql = "UPDATE facturas SET coordinar_envio = '$entregado', custom_6 = '$entregado' ";
    if (!empty($observaciones)) $sql.= ", observaciones = CONCAT(observaciones,' $observaciones') ";
    $sql.= "WHERE id = $id AND id_punto_venta = $id_punto_venta AND id_empresa = $id_empresa ";
    $q = $this->db->query($sql);
    echo json_encode(array("error"=>0));
  }

  function sincronizar() {
    header('Access-Control-Allow-Origin: *');

    $this->load->model("Dispositivo_Model");
    $version = ($this->input->post("version") !== FALSE) ? $this->input->post("version") : 4;
    $id_empresa_asignada = parent::get_post("id_empresa_asignada",-1);
    $id_vendedor = $this->input->post("id_vendedor");
    $this->load->model("Vendedor_Model");
    $this->load->model("Factura_Model");

    $id_vendedor = (int) $id_vendedor;
    $vendedor = $this->Vendedor_Model->get($id_vendedor,array(
      // Si es -1, se busca el vendedor sin filtrar por empresa. Si tiene un valor busca ese vendedor de esa empresa en particular
      "id_empresa"=>$id_empresa_asignada 
    ));
    if ($vendedor === FALSE) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"Error: Vendedor no encontrador",
      ));
      return;        
    }
    $id_empresa = $vendedor->id_empresa;
    $vendedor_nombre = $vendedor->nombre;
    $id_punto_venta_vendedor = ($vendedor === FALSE) ? 0 : $vendedor->id_punto_venta;

    $facturas = $this->input->post("facturas");
    if ($facturas === FALSE) {
      echo json_encode(array("error"=>1)); exit();
    }

    $sql = "SELECT * FROM empresas WHERE id = $id_empresa";
    $q = $this->db->query($sql);
    if ($q->num_rows() == 0) {
      echo json_encode(array("error"=>1)); exit();
    }
    $empresa = $q->row();
    $nombre_empresa = $empresa->nombre;

    $this->load->model("Cliente_Model");
    $this->load->model("Log_Model");
    $facturas = json_decode($facturas);
    $this->Log_Model->imprimir(array(
      "id_empresa"=>$id_empresa,
      "id_usuario"=>0,
      "file"=>date("Ymd")."_pedidos.txt",
      "texto"=>"========================\n\n".print_r($facturas,TRUE)
    ));

    $clientes = $this->input->post("clientes");
    if (!empty($clientes)) {

      $clientes = json_decode($clientes);
      $this->Log_Model->imprimir(array(
        "id_empresa"=>$id_empresa,
        "id_usuario"=>0,
        "file"=>date("Ymd")."_pedidos.txt",
        "texto"=>"Nuevos clientes\n".print_r($clientes,TRUE),
      ));

      foreach($clientes as $cliente) {
        $id_cliente = $cliente->id;
        unset($cliente->id);

        // TODO: Arreglar esto desp
        unset($cliente->limite_bonif);

        if (!empty($cliente->direccion)) {
          $existe_cliente = $this->Cliente_Model->buscar_por_nombre($cliente->nombre,array(
            "id_empresa"=>$id_empresa,
            "direccion"=>$cliente->direccion,
          ));
        } else {
          // Si la direccion esta vacia, por las dudas no buscamos si existe el cliente
          // ya que se podria mezclar dos clientes que se llamen iguales pero que no sean los mismos
          // en ese caso es preferible duplicarlos
          $existe_cliente = FALSE;
        }

        if ($existe_cliente === FALSE) {
          $cliente->codigo = $this->Cliente_Model->next(array(
            "id_empresa"=>$id_empresa
          ));
          $cliente->id_empresa = $id_empresa;
          $cliente->tipo = 0;
          $cliente->id_tipo_documento = 80;
          $cliente->activo = 1;
          $cliente->lista = 0;
          $cliente->id_vendedor = $id_vendedor;
          $cliente->forma_pago = "C";
          $cliente->fecha_inicial = date("Y-m-d");
          $cliente->fecha_ult_operacion = date("Y-m-d");
          $cliente->uploaded = 1;
          $this->db->insert("clientes",$cliente);
          $id_cliente_nuevo = $this->db->insert_id();
        } else {
          $id_cliente_nuevo = $existe_cliente->id;
        }

        // Debemos buscar las facturas que tienen ese ID de cliente para reemplazarlo con el ID nuevo creado
        foreach($facturas as $f) {
          if ($f->id_cliente == $id_cliente) $f->id_cliente = $id_cliente_nuevo;
        }
      }
    }

    $this->load->model("Articulo_Model");
    $this->load->helper("fecha_helper");

    // Tomamos el punto de venta por defecto
    $sql = "SELECT PV.*, IF(ALM.nombre IS NULL,'',ALM.nombre) AS sucursal ";
    $sql.= "FROM puntos_venta PV LEFT JOIN almacenes ALM ON (PV.id_empresa = ALM.id_empresa AND PV.id_sucursal = ALM.id) ";
    $sql.= "WHERE PV.id_empresa = $id_empresa ";
    if ($id_punto_venta_vendedor != 0) {
      $sql.= "AND PV.id = $id_punto_venta_vendedor ";
    } else {
      $sql.= "AND PV.por_default = 1 ";
    }
    $sql.= "LIMIT 0,1 ";
    $q_pv = $this->db->query($sql);
    if ($q_pv->num_rows()>0) {
      $pv = $q_pv->row();
      $id_punto_venta = $pv->id;
      $pv_numero = $pv->numero;      
      $tipo_punto_venta = $pv->tipo_impresion;
      $sucursal = $pv->sucursal;
    } else {
      $id_punto_venta = 0;
      $pv_numero = 0;
      $tipo_punto_venta = "";
      $sucursal = "";
    }

    // TODO: Por ahora se envia todo como remito, y se convierte a factura a mano
    $id_tipo_comprobante = 999;
    $tipo_comprobante = "Remito";
    $letra = "R";

    // Tomamos el proximo numero
    $sql = "SELECT * FROM numeros_comprobantes WHERE id_empresa = $id_empresa AND id_punto_venta = $id_punto_venta AND id_tipo_comprobante = $id_tipo_comprobante LIMIT 0,1";
    $q_numero = $this->db->query($sql);
    $r_numero = $q_numero->row();
    $numero = $r_numero->ultimo + 1;

    foreach($facturas as $f) {

      $pesable = FALSE;
      $tiene_descuento = FALSE;

      $fecha_fact = (isset($f->fecha)) ? fecha_mysql($f->fecha) : date("Y-m-d");
      $fecha_reparto = (isset($f->fecha_reparto)) ? fecha_mysql($f->fecha_reparto) : date("Y-m-d");
      if (!isset($f->hora)) $f->hora = date("H:i:s");
      if (!isset($f->reparto)) $f->reparto = 1;

      // TODO: Poner un reparto por defecto del vendedor
      if ($id_vendedor == 111 && $f->reparto == 1) $f->reparto = 23;

      $observaciones = (isset($f->observaciones)) ? $f->observaciones : "";
      $comprobante = $letra." ".str_pad($pv_numero,4,"0",STR_PAD_LEFT)."-".str_pad($numero,8,"0",STR_PAD_LEFT);

      // Controlamos si la factura ya existe para evitar el error de duplicados
      $sql = "SELECT * FROM facturas ";
      $sql.= "WHERE id_empresa = $id_empresa ";
      $sql.= "AND id_punto_venta = $id_punto_venta ";
      $sql.= "AND id_cliente = $f->id_cliente ";
      $sql.= "AND id_vendedor = $id_vendedor ";
      $sql.= "AND fecha = '$fecha_fact' ";
      $sql.= "AND hora = '$f->hora' ";
      $q_repetido = $this->db->query($sql);
      if ($q_repetido->num_rows()>0) continue;

      $ivas = array();

      $cliente = $this->Cliente_Model->get($f->id_cliente,$id_empresa);
      $cliente_canasta_basica = false;
      if (($id_empresa == 229 || $id_empresa == 230 || $id_empresa == 1355) && $cliente->custom_5 == "1") $cliente_canasta_basica = true;

      $id_tipo_estado = (isset($f->id_tipo_estado) ? ($f->id_tipo_estado == "undefined" ? 0 : $f->id_tipo_estado) : 0);

      $sql = "INSERT INTO facturas (";
      $sql.= " id_cliente,id_empresa,id_punto_venta,id_vendedor,numero, punto_venta, ";
      $sql.= " id_tipo_estado,uploaded,fecha_reparto,reparto,";
      $sql.= " fecha,hora,comprobante,id_tipo_comprobante,observaciones,cliente, nueva, impresa, id_origen, tipo_punto_venta, empresa, tipo_comprobante, vendedor, sucursal, ";
      $sql.= " custom_7, custom_8, numero_referencia "; // Latitud, longitud, ID en la app
      $sql.= ") VALUES (";
      $sql.= " '$f->id_cliente','$id_empresa','$id_punto_venta','$id_vendedor','$numero', '$pv_numero', ";
      $sql.= " '$id_tipo_estado',0,'$fecha_reparto','$f->reparto',";
      $sql.= " '$fecha_fact','$f->hora','$comprobante',$id_tipo_comprobante,'$observaciones','$cliente->nombre', 1, 0, 3, '$tipo_punto_venta', '$nombre_empresa', '$tipo_comprobante', '$vendedor_nombre', '$sucursal', ";
      $sql.= " '$f->latitud','$f->longitud', '$f->id' ";
      $sql.= ")";
      $this->db->query($sql);
      $id_factura = $this->db->insert_id();
      $t_neto = 0; $t_iva = 0; $t_total = 0; $t_costo_final = 0;
      for($i=0;$i<sizeof($f->facturaItemsArray);$i++) {
        $item = $f->facturaItemsArray[$i];
        $item->porc_bonif = (isset($item->porc_bonif)) ? $item->porc_bonif : 0;
        if ($item->porc_bonif > 100) $item->porc_bonif = 100;
        $item->tipoCantidad = (isset($item->tipoCantidad)) ? $item->tipoCantidad : "";

        // Buscamos el articulo por el codigo
        $articulo = $this->Articulo_Model->get_by_codigo($item->codigoArticulo,array(
          "id_empresa"=>$id_empresa,
        ));
        if ($articulo === FALSE) continue;

        // Si el articulo y el cliente estan marcados
        $producto_exento = false;
        if (($id_empresa == 229 || $id_empresa == 230 || $id_empresa == 1355) && $articulo->custom_5 == "1" && $cliente_canasta_basica) {
          $producto_exento = true;
          $articulo->porc_iva = 0;
          $articulo->id_tipo_alicuota_iva = 3;
        }

        // Tenemos algun articulo pesable, hay que marcar la fila
        if ($articulo->no_totalizar_reparto == 1) $pesable = TRUE;

        if (empty($item->cantidad)) $item->cantidad = 1;

        // Dependiendo de la lista de cada cliente
        $precio_final = 0;
        if ($item->lista == 0) {
          $precio_final = ($producto_exento) ? $articulo->precio_neto : $articulo->precio_final_dto;
        } else if ($item->lista == 1) {
          $precio_final = ($producto_exento) ? $articulo->precio_neto_2 : $articulo->precio_final_dto_2;
        } else if ($item->lista == 2) {
          $precio_final = ($producto_exento) ? $articulo->precio_neto_3 : $articulo->precio_final_dto_3;
        } else if ($item->lista == 3) {
          $precio_final = ($producto_exento) ? $articulo->precio_neto_4 : $articulo->precio_final_dto_4;
        } else if ($item->lista == 4) {
          $precio_final = ($producto_exento) ? $articulo->precio_neto_5 : $articulo->precio_final_dto_5;
        } else if ($item->lista == 5) {
          $precio_final = ($producto_exento) ? $articulo->precio_neto_6 : $articulo->precio_final_dto_6;
        }

        // BASILE
        if ($id_empresa == 972 && $item->tipoCantidad == "Venta") {
          if ($item->cantidad > 100 && $item->cantidad <= 300) $precio_final = $precio_final - 10;
          else if ($item->cantidad > 300) $precio_final = $precio_final - 15;
        }

        if ($item->tipoCantidad == "Bonificado") {
          // Si es bonificacion, no tiene costo
          $articulo->costo_final = 0;
          $precio_neto = 0;
          $precio_final = 0;
          $item->porc_bonif = 100; // Se bonifica el 100% del costo
        }
        else if ($item->tipoCantidad == "Devolucion") {
          // Si es una devolucion, la cantidad es en negativo
          $item->cantidad = abs($item->cantidad) * -1;
          // y no tiene costo
          $articulo->costo_final = 0;
          $precio_neto = 0;
          $precio_final = 0;
        }
        else if ($item->tipoCantidad == "Cambio") {
          // El cambio no tiene costo y no afecta al stock
          $articulo->costo_final = 0;
          $precio_neto = 0;
          $precio_final = 0;
        }

        $porc_bonif = ((100 - $item->porc_bonif)/100);
        if ($item->porc_bonif > 0) $tiene_descuento = TRUE;
        $precio_neto = round($precio_final / ((100+$articulo->porc_iva)/100),2);

        if ($item->porc_bonif == 100) {
          $item->tipo_cantidad = "B";
        } else if ($item->cantidad < 0) {
          $item->tipo_cantidad = "D";
        } else if ($item->tipoCantidad == "Cambio") {
          $item->tipo_cantidad = "C";
        } else {
          $item->tipo_cantidad = "";
        }

        // Calculamos los totales de la fila y sumamos a los totales generales
        $costo_final = $item->cantidad * $articulo->costo_final;
        $total_sin_iva = $item->cantidad * $precio_neto * $porc_bonif;
        $total_con_iva = $item->cantidad * $precio_final * $porc_bonif;
        $iva = $total_con_iva - $total_sin_iva;
        $t_neto += $total_sin_iva;
        $t_iva += $iva;
        $t_total += $total_con_iva;
        $t_costo_final += $costo_final;
        $stamp = time();

        // Insertamos la fila
        $sql = "INSERT INTO facturas_items (";
        $sql.= " id_empresa,id_punto_venta,id_factura,";
        $sql.= " id_articulo,cantidad,";
        $sql.= " porc_iva,id_tipo_alicuota_iva,neto,precio,";
        $sql.= " nombre,orden,id_rubro,iva,";
        $sql.= " total_sin_iva,total_con_iva,costo_final,uploaded,bonificacion, ";
        $sql.= " id_cliente, id_vendedor, id_proveedor, anulado, negativo, stamp, tipo_cantidad ";
        $sql.= ") VALUES (";
        $sql.= " '$id_empresa','$id_punto_venta','$id_factura',";
        $sql.= " '$articulo->id','$item->cantidad', ";
        $sql.= " '$articulo->porc_iva,','$articulo->id_tipo_alicuota_iva','$precio_neto','$precio_final', ";
        $sql.= " '$articulo->nombre','$i','$articulo->id_rubro','$iva', ";
        $sql.= " '$total_sin_iva','$total_con_iva','$costo_final',0,'$item->porc_bonif', ";
        $sql.= " '$f->id_cliente', '$id_vendedor', 0, 0, 0, '$stamp', '$item->tipo_cantidad' ";
        $sql.= ")";
        $this->db->query($sql);

        if (isset($ivas[$articulo->id_tipo_alicuota_iva])) {
          $ivas[$articulo->id_tipo_alicuota_iva]["neto"] += $total_sin_iva;
          $ivas[$articulo->id_tipo_alicuota_iva]["iva"] += $iva;
        } else {
          $ivas[$articulo->id_tipo_alicuota_iva] = array(
            "neto"=>$total_sin_iva,
            "iva"=>$iva,
          );
        }
      }

      // Si el pedido tiene algun producto pesable o algun descuento aplicado, se debe marcar para que el usuario lo vea
      if ($id_tipo_estado != 7 && ($pesable || $tiene_descuento || !empty($observaciones))) {
        $sql = "UPDATE facturas SET ";
        $sql.= " id_tipo_estado = -1 ";
        $sql.= "WHERE id = $id_factura ";
        $sql.= "AND id_punto_venta = $id_punto_venta ";
        $sql.= "AND id_empresa = $id_empresa ";
        $this->db->query($sql);
      }

      // Actualizamos los totales de la factura
      $sql = "UPDATE facturas SET ";
      $sql.= " total = '$t_total', neto = '$t_neto', iva = '$t_iva', subtotal = '$t_neto', costo_final = '$t_costo_final' ";
      $sql.= "WHERE id = $id_factura ";
      $sql.= "AND id_punto_venta = $id_punto_venta ";
      $sql.= "AND id_empresa = $id_empresa ";
      $this->db->query($sql);

      // Guardamos los IVAS
      foreach ($ivas as $id_tipo_iva => $ti) {

        // ARREGLO POR EL TEMA DE LOS AJUSTES
        $s = $this->Factura_Model->calcular_iva_segun_alicuota($id_tipo_iva,$ti["neto"]);

        $sql = "INSERT INTO facturas_iva (id_factura, id_empresa, id_punto_venta, id_alicuota_iva, neto, iva, uploaded) VALUES (";
        $sql.= " $id_factura, $id_empresa, $id_punto_venta,$id_tipo_iva,".$ti["neto"].",".$s.",0)";
        $this->db->query($sql);        
      }

      $numero++;
    } // Fin FOR

    // Actualizamos los numeros de comprobantes
    $ultimo = $numero--;
    $sql = "UPDATE numeros_comprobantes SET ultimo = $ultimo WHERE id_empresa = $id_empresa AND id_punto_venta = $id_punto_venta AND id_tipo_comprobante = $id_tipo_comprobante";
    $this->db->query($sql);

    // Finalmente procesamos el stock
    $this->load->model("Stock_Model");
    $this->Stock_Model->procesar($id_empresa,$pv_numero);

    echo json_encode(array("error"=>0));
  }
  
}