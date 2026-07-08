<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Pedidos extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Pedido_Model', 'modelo',"fecha DESC, hora DESC");
  }


  function importal() {
    $id_empresa = 249;
    $id_proveedor = 1557;
    $sucursales = array(7);

    $salida = array();
    $sql = "SELECT AP.id_articulo, A.codigo, A.nombre FROM articulos_proveedores AP ";
    $sql.= "INNER JOIN articulos A ON (AP.id_empresa = A.id_empresa AND AP.id_articulo = A.id) ";
    $sql.= "WHERE AP.id_empresa = $id_empresa AND AP.id_proveedor = $id_proveedor ";
    $q_articulos = $this->db->query($sql);
    foreach($q_articulos->result() as $art) {
      
      $id_articulo = $art->id_articulo;

      foreach($sucursales as $id_sucursal) {

        $obj = new stdClass();
        $obj->id_articulo = $id_articulo;
        $obj->nombre = $art->nombre;
        $obj->codigo = $art->codigo;

        // Contamos la cantidad de compras
        $sql = "SELECT ";
        $sql.= " IF(SUM(IPI.cantidad) IS NULL,0,SUM(IPI.cantidad)) AS compra ";
        $sql.= "FROM ingresos_proveedores IP ";
        $sql.= "INNER JOIN ingresos_proveedores_items IPI ON (IP.id_empresa = IPI.id_empresa AND IPI.id_ingreso = IP.id) ";
        $sql.= "WHERE IP.id_empresa = $id_empresa ";
        $sql.= "AND IP.id_almacen = $id_sucursal ";
        $sql.= "AND IPI.id_articulo = $id_articulo ";
        $sql.= "AND IP.estado = 1 ";
        $q_compra = $this->db->query($sql);
        $r_compra = $q_compra->row();
        $obj->compra = $r_compra->compra;

        // Contamos la cantidad de ventas
        $sql = "SELECT ";
        $sql.= " SUM(IF(FI.tipo_cantidad = '' OR FI.tipo_cantidad = 'X',FI.cantidad,0)) AS cantidad, ";
        $sql.= " SUM(IF(FI.tipo_cantidad = 'B',FI.cantidad,0)) AS bonificado, ";
        $sql.= " SUM(IF(FI.tipo_cantidad = 'D',FI.cantidad,0)) AS devolucion ";
        $sql.= "FROM facturas F ";
        $sql.= "INNER JOIN facturas_items FI ON (FI.id_empresa = F.id_empresa AND FI.id_punto_venta = F.id_punto_venta AND F.id = FI.id_factura) ";
        $sql.= "LEFT JOIN articulos A ON (A.id_empresa = FI.id_empresa AND A.id = FI.id_articulo) ";
        $sql.= "WHERE F.id_empresa = $id_empresa ";
        //$sql.= "AND F.fecha >= '$desde' AND F.fecha <= '$hasta' ";
        $sql.= "AND F.anulada = 0 ";
        $sql.= "AND F.id_sucursal = $id_sucursal ";
        $sql.= "AND FI.anulado = 0 ";
        $sql.= "AND F.tipo != 'C' ";
        $sql.= "AND FI.id_articulo = $id_articulo ";
        $q_venta = $this->db->query($sql);
        $r_venta = $q_venta->row();
        $obj->venta = $r_venta->cantidad;

        // Consultamos el stock
        $sql = "SELECT stock_actual ";
        $sql.= "FROM stock ";
        $sql.= "WHERE id_empresa = $id_empresa ";
        $sql.= "AND id_articulo = $id_articulo ";
        $sql.= "AND id_sucursal = $id_sucursal ";
        $q_stock = $this->db->query($sql);
        if ($q_stock->num_rows()<=0) {
          $obj->stock = 0;
        } else {
          $r_stock = $q_stock->row();
          $obj->stock = $r_stock->stock_actual;          
        }

        $salida[] = $obj;
      }
    }

    echo "<table>";
    echo "<tr>";
    echo "<td>Codigo</td><td>Articulo</td><td>Compra</td><td>Venta</td><td>Stock</td>";
    echo "</tr>";
    foreach($salida as $s) {
      echo "<tr>";
      echo "<td>$s->codigo</td>";
      echo "<td>$s->nombre</td>";
      echo "<td>$s->compra</td>";
      echo "<td>$s->venta</td>";
      echo "<td>$s->stock</td>";
      echo "</tr>";
    }


  }


  function estadistica_por_proveedor() {

    set_time_limit(0);
    $id_empresa = 134;
    $this->load->model("Stock_Model");
    $this->load->model("Articulo_Model");
    $this->load->model("Proveedor_Model");
    $proveedores = $this->Proveedor_Model->buscar(array(
      "tipo_proveedor"=>1,
    ));
    echo "<table>";
    foreach($proveedores["results"] as $prov) {

      $compra = 0;
      $venta = 0;

      $articulos = $this->Articulo_Model->buscar(array(
        "id_proveedor"=>$prov->id
      ));
      foreach($articulos["results"] as $art) {

        // Entradas
        $sql = "SELECT SUM(SM.cantidad * A.costo_final) AS costo_final, ";
        $sql.= " SUM(SM.cantidad) AS cantidad ";
        $sql.= "FROM stock_movimientos SM ";
        $sql.= "INNER JOIN articulos A ON (SM.id_articulo = A.id AND SM.id_empresa = A.id_empresa) ";
        $sql.= "WHERE SM.id_empresa = $id_empresa ";
        $sql.= "AND SM.movimiento = 'A' ";
        $sql.= "AND A.id = $art->id ";
        $q = $this->db->query($sql);
        $alta = $q->row();
        $alta_cantidad = is_null($alta->cantidad) ? 0 : $alta->cantidad;
        $alta_costo_final = is_null($alta->costo_final) ? 0 : $alta->costo_final;

        // Bajas
        $sql = "SELECT SUM(SM.cantidad * A.costo_final) AS costo_final, ";
        $sql.= " SUM(SM.cantidad) AS cantidad ";
        $sql.= "FROM stock_movimientos SM ";
        $sql.= "INNER JOIN articulos A ON (SM.id_articulo = A.id AND SM.id_empresa = A.id_empresa) ";
        $sql.= "WHERE SM.id_empresa = $id_empresa ";
        $sql.= "AND SM.movimiento = 'B' ";
        $sql.= "AND A.id = $art->id ";
        $q = $this->db->query($sql);
        $baja = $q->row();
        $baja_cantidad = is_null($baja->cantidad) ? 0 : $baja->cantidad;
        $baja_costo_final = is_null($baja->costo_final) ? 0 : $baja->costo_final;

        $compra += $alta_costo_final;
        $venta += $baja_costo_final;
      }

      $porc = ($compra >0) ? round($venta * 100 / $compra,2) : 0;
      if ($porc > 0) {
        echo "<tr>";
        echo "<td>$prov->nombre</td>";
        echo "<td>$porc</td>";
        echo "</tr>";
      }
    }
    echo "</table>";

  }

  function ver_procesar() {
    $this->load->helper("fecha_helper");
    $this->load->model("Articulo_Model");
    $this->load->model("Stock_Model");
    $id_proveedor = $this->input->post("id_proveedor");
    $id_sucursal = $this->input->post("id_sucursal");
    $fecha = fecha_mysql($this->input->post("fecha"));
    $salida = array();
    $articulos = $this->Articulo_Model->buscar(array(
      "id_proveedor"=>$id_proveedor,
    ));
    foreach($articulos["results"] as $art) {
      $a = new stdClass();
      $a->id_articulo = $art->id;
      $a->articulo = $art->nombre;
      $a->codigo = $art->codigo;
      $a->costo_final = $art->costo_final;
      $a->codigo_prov = $art->codigo_proveedor;
      $a->stock_ant = $this->Stock_Model->get_saldo(array(
        "id_sucursal"=>$id_sucursal,
        "id_articulo"=>$art->id,
        "fecha"=>$fecha,
      ));
      $a->stock_act = $this->Stock_Model->get_saldo(array(
        "id_sucursal"=>$id_sucursal,
        "id_articulo"=>$art->id,
        "fecha"=>date("Y-m-d"),
      ));
      $salida[] = $a;
    }
    echo json_encode(array(
      "results"=>$salida
    ));
  }
  
  // Mantiene el pedido en la session del usuario
  function actualizar($persistir = 0) {
    if (isset($_POST["pedido"])) {
      $pedido = filter_var($_POST["pedido"],FILTER_SANITIZE_STRING);
      $_SESSION["pedido"] = $pedido;
    }
    
    // Si el cliente esta logueado, debemos persistirlo SIEMPRE
    if (isset($_SESSION["id_cliente"])) $this->persistir();
    else {
      if ($persistir == 1) {
        $this->persistir();
      } else {
        echo json_encode(array("error"=>0,"id"=>0));
      }    
    }    
  }
  
  // Borra el pedido de la base de datos
  function borrar() {
    $id_empresa = parent::get_empresa();
    $pedido = json_decode(htmlspecialchars_decode($_SESSION["pedido"]));
    // El pedido ha sido cancelado: No lo borramos, cambiamos de estado para que figure
    // en el listado de Carritos Abandonados
    $this->db->query("UPDATE id_tipo_estado = 7 FROM facturas WHERE id = $pedido->id AND id_empresa = $id_empresa");
    // Lo borramos de la session
    unset($_SESSION["pedido"]);
    echo json_encode(array(
      "error"=>0
    ));
  }
  
  // Guarda el pedido en la base de datos
  function persistir() {
    $pedido = json_decode(htmlspecialchars_decode($_SESSION["pedido"]));
    if (empty($pedido)) {
      echo json_encode(array("error"=>0,"id"=>0));
      return;
    }
    $items = $pedido->items;
    $pedido->porc_descuento = 0;
    $pedido->descuento = 0;
    $pedido->costo_envio = 0;
    $pedido->subtotal = $pedido->total;
    $this->remove_attributes($pedido);
    $pedido->id_cliente = $_SESSION["id_cliente"];
    $pedido->id_empresa = $pedido->id_empresa;
    $pedido->fecha = date("Y-m-d");
    $pedido->hora = date("H:i:s");
    $pedido->id_tipo_estado = $pedido->id_tipo_estado; // Pendiente
    
    if (!isset($pedido->id) || $pedido->id == 0) {
      // Debemos insertarlo
      $id_factura = $this->modelo->insert($pedido);
    } else {
      // Debemos actualizarlo
      $id_factura = $pedido->id;
      $this->modelo->update($id_factura,$pedido);
      $this->db->query("DELETE FROM facturas_items WHERE id_factura = $id_factura AND id_empresa = $pedido->id_empresa");
    }
    $i=0;
    foreach($items as $l) {
      $this->db->insert("facturas_items",array(
        "id_empresa"=>$l->id_empresa,
        "id_factura"=>$id_factura,
        "id_articulo"=>$l->id_articulo,
        "cantidad"=>$l->cantidad,
        "precio"=>$l->precio,
        "nombre"=>$l->nombre,
        "total_con_iva"=>$l->total,
        "orden"=>$i,
      ));
      $i++;
    }
    
    // Guardamos en la session
    $pedido->id = $id_factura;
    $pedido->items = $items;
    $_SESSION["pedido"] = json_encode($pedido);
    
    echo json_encode(array("error"=>0,"id"=>$id_factura));
  }
  
  function ver() {
    echo $_SESSION["pedido"];
  }

  function guardar_session() {
    $pedido = $this->input->post("pedido");
    $_SESSION["pedido"] = $pedido;
    echo json_encode(array());
  }
  
  function ver_pdf($id_factura) {
    
    $this->load->helper("fecha_helper");
    $this->load->helper("numero_letra_helper");
    $pedido = $this->modelo->get($id_factura);
    if ($pedido === FALSE || empty($pedido)) {
      echo "Lo sentimos pero la compra ha sido eliminada.";
      exit();
    }
    
    $id_empresa = $pedido->id_empresa;
    $this->load->model("Empresa_Model");
    $empresa = $this->Empresa_Model->get($id_empresa);
    
    $header = $this->load->view("reports/pedido/header",null,true);
    
    $tpl = "modelo1";
    $folder = "/sistema/application/views/reports/pedido/$tpl/red";
    
    $datos = array(
      "pedido"=>$pedido,
      "empresa"=>$empresa,
      "header"=>$header,
      "folder"=>$folder,
    );
    $this->load->view("reports/pedido/$tpl/pedido.php",$datos);
  }
  
  
  // Limpia el pedido
  function limpiar() {
    unset($_SESSION["pedido"]);
    echo json_encode(array("error"=>0));
  }
  
    function consulta() {
    
    $id_empresa = ($this->input->get("e") !== FALSE) ? $this->input->get("e") : parent::get_empresa();
    $desde = $this->input->get("desde");
    $hasta = $this->input->get("hasta");
    $id_cliente = $this->input->get("id_cliente");
    $id_usuario = ($this->input->get("id_usuario") !== FALSE) ? $this->input->get("id_usuario") : 0;
    $id_vendedor = $this->input->get("id_vendedor");
    $numero = $this->input->get("numero");
    $numero_reparto = $this->input->get("numero_reparto");
        
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
      "id_cliente"=>$id_cliente,
      "id_usuario"=>$id_usuario,
      "id_vendedor"=>$id_vendedor,
      "numero"=>$numero,
      "numero_reparto"=>$numero_reparto,
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
    unset($array->gestiona_stock);
    unset($array->id_proveedor);
    unset($array->proveedor);
    
    // Redondeamos
    $array->total = round($array->total,2);
    $array->subtotal = round($array->subtotal,2);
    $array->porc_descuento = round($array->porc_descuento,2);
    $array->descuento = round($array->descuento,2);
    $array->costo_envio = round($array->costo_envio,2);
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

    if (isset($array->fecha_reparto)) $array->fecha_reparto = fecha_mysql($array->fecha_reparto);
    else $array->fecha_reparto = date("Y-m-d");        

    $items = $array->items;
    $this->remove_attributes($array);
    $id_factura = $this->modelo->insert($array);

    $i=0;
    foreach($items as $l) {
      $this->db->insert("facturas_items",array(
        "id_empresa"=>$array->id_empresa,
        "id_factura"=>$id_factura,
        "id_articulo"=>$l->id_articulo,
        "cantidad"=>$l->cantidad,
        "precio"=>$l->precio,
        "nombre"=>$l->nombre,
        "total_con_iva"=>$l->total_con_iva,
        "bonificacion"=>$l->bonificacion,
        "orden"=>$i,
      ));
    $i++;
    }

    echo json_encode(array(
    "id"=>$id_factura,
    "error"=>0,
    ));
  }
  
  
  function update($id_factura) {
    
    // Si es 0, entonces lo insertamos
    if ($id_factura == 0) { $this->insert($id_factura); return; }    
    
    $this->db->db_debug = FALSE;
    $id_empresa = parent::get_empresa();
    
    $this->load->model("Empresa_Model");
    $this->load->helper("fecha_helper");

    $anterior = $this->modelo->get($id_factura);
    
    // Tomamos los datos
    $array = $this->parse_put();
    $array->id_empresa = $id_empresa;
    $fecha = $array->fecha;
    if (isset($array->fecha)) $array->fecha = fecha_mysql($array->fecha);
    else $array->fecha = date("Y-m-d");
    $array->hora = date("H:i:s");
    
    if (isset($array->fecha_reparto)) $array->fecha_reparto = fecha_mysql($array->fecha_reparto);
    else $array->fecha_reparto = date("Y-m-d");        
    
    $items = $array->items;
    $this->remove_attributes($array);
    $this->modelo->update($id_factura,$array);

    $i=0;
    $this->db->query("DELETE FROM facturas_items WHERE id_factura = $id_factura AND id_empresa = $id_empresa");
    foreach($items as $l) {
      $this->db->insert("facturas_items",array(
        "id_empresa"=>$array->id_empresa,
        "id_factura"=>$id_factura,
        "id_articulo"=>$l->id_articulo,
        "cantidad"=>$l->cantidad,
        "precio"=>$l->precio,
        "nombre"=>$l->nombre,
        "total_con_iva"=>$l->total_con_iva,
        "orden"=>$i,
      ));
      $i++;
    }

    // Si se cambio de cualquier otro estado a FINALIZADO
    if ($anterior->id_tipo_estado != 6 && $array->id_tipo_estado == 6) {

      foreach($items as $a) {
        // Descontamos el stock del producto   
        $sql = "UPDATE articulos SET stock = stock - $a->cantidad ";   
        $sql.= "WHERE id_empresa = $id_empresa AND id = $a->id_articulo ";
        $this->db->query($sql);
        
        // Descontamos el stock de la variante en caso de tenerla
        if ($a->id_opcion_1 != 0 || $a->id_opcion_2 != 0 || $a->id_opcion_3 != 0) {
          $sql = "UPDATE articulos_variantes ";
          $sql.= "SET stock = stock - $a->cantidad ";
          $sql.= "WHERE id_empresa = $id_empresa AND id = $a->id_articulo ";
          $sql.= "AND id_opcion_1 = $a->id_opcion_1 ";
          $sql.= "AND id_opcion_2 = $a->id_opcion_2 ";
          $sql.= "AND id_opcion_3 = $a->id_opcion_3 ";
          $this->db->query($sql);
        }
      }
    }

    echo json_encode(array(
      "id"=>$id_factura,
      "error"=>0,
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
    $this->db->query("DELETE FROM crm_consultas WHERE id_empresa = $id_empresa AND id_relacion = $id");
    $this->db->query("DELETE FROM facturas_items WHERE id_factura = $id AND id_empresa = $id_empresa");
    $this->db->query("DELETE FROM facturas WHERE id = $id AND id_empresa = $id_empresa");
    echo json_encode(array());
  }

}