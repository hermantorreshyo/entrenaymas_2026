<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Pedidos_Mesas extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Pedido_Mesa_Model', 'modelo',"fecha DESC, hora DESC");
  }

  // REGISTRA UN PEDIDO A PARTIR DE DELIVERYCLIC
  function registrar() {
    @session_start();
    $pedido = $this->input->post("pedido");
    if ($pedido === FALSE || empty($pedido)) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"No existe ningun pedido.",
      ));
      exit();
    }
    $pedido = json_decode($pedido);

    $this->load->model("Empresa_Model");
    $empresa = $this->Empresa_Model->get($pedido->id_empresa);

    // Buscamos el cliente por el email
    $this->load->model("Cliente_Model");
    $cliente = $this->Cliente_Model->get_by_email($pedido->email,$pedido->id_empresa,array(
      "tipo"=>0,
    ));
    if ($cliente === FALSE) {
      // Debemos insertar un nuevo cliente
      $cliente = new stdClass();
      $cliente->id_empresa = $pedido->id_empresa;
      $cliente->email = $pedido->email;
      $cliente->nombre = $pedido->nombre;
      $cliente->celular = $pedido->telefono;
      $cliente->direccion = $pedido->direccion;
      $cliente->localidad = $pedido->localidad;
      $cliente->id_localidad = $pedido->id_localidad;
      $cliente->fecha_inicial = date("Y-m-d");
      $cliente->tipo = 0; // Cliente
      $id_cliente = $this->Cliente_Model->insert($cliente);
    } else {
      // Debemos actualizar los datos del cliente por si cambio algo
      $cliente->nombre = $pedido->nombre;
      $cliente->celular = $pedido->telefono;
      $cliente->direccion = $pedido->direccion;
      $this->Cliente_Model->save($cliente);
      $id_cliente = $cliente->id;
    }

    $hoy = date("Y-m-d");
    $ahora = date("H:i:s");
    $sql = "INSERT INTO facturas (";
    $sql.= " tipo, id_empresa, fecha, hora, id_cliente, id_tipo_comprobante, id_tipo_estado, ";
    $sql.= " total, subtotal, neto, iva, estado, observaciones, ";
    $sql.= " direccion, localidad, id_localidad, costo_envio ";
    $sql.= ") VALUES (";
    $sql.= " 'D','$pedido->id_empresa', '$hoy', '$ahora', '$id_cliente', 999, 2, "; // 2 = Estado Pendiente
    $sql.= " '$pedido->total', '$pedido->total', '$pedido->total', 0, 1, '$pedido->descripcion', ";
    $sql.= " '$pedido->direccion', '$pedido->localidad', '$pedido->id_localidad', '$pedido->costo_envio' ";
    $sql.= ")";
    $q = $this->db->query($sql);
    $id_pedido = $this->db->insert_id();
    $i=0;
    $pedido_items = "";
    if (sizeof($pedido->items)>0) {
      foreach($pedido->items as $l) {
        $aclaracion = isset($l->aclaracion) ? $l->aclaracion : "";
        $aclaracion = str_replace("'", "", $aclaracion);
        $aclaracion = str_replace("\"", "", $aclaracion);      
        $l->titulo = str_replace("'", "", $l->titulo);
        $l->titulo = str_replace("\"", "", $l->titulo);
        $sql = "INSERT INTO facturas_items (id_empresa,id_factura,id_articulo,cantidad,precio,nombre,total_con_iva,orden,descripcion) VALUES (";
        $sql.= " '$pedido->id_empresa', '$id_pedido', '$l->id', '$l->cantidad', '$l->precio', '$l->titulo', '$l->subtotal', '$i', '$aclaracion' )";
        $this->db->query($sql);
        $i++;
        $pedido_items.="<tr>";
        $pedido_items.="<td>$l->titulo".((!empty($aclaracion))?"<br/>$aclaracion":"")."</td>";
        $pedido_items.="<td>$l->cantidad</td>";
        $pedido_items.="<td>$l->precio</td>";
        $pedido_items.="<td>$l->subtotal</td>";
        $pedido_items.="</tr>";
      }
      $pedido_items = "<table><thead><tr><th>Producto</th><th>Cantidad</th><th>P. Unit</th><th>Subtotal</th></tr></thead><tbody>".$pedido_items."</tbody></table>";
    }

    // Enviamos un email al usuario
    $this->load->model("Email_Template_Model");
    $template = $this->Email_Template_Model->get_by_key("nuevo-pedido-usuario",$pedido->id_empresa);
    if ($template === FALSE) {
      $template = new stdClass();
      $template->nombre = "Pedido enviado";
      $template->texto = "Su pedido ha sido enviado";
    }
    $body = $template->texto;
    $body = str_replace("{{cliente}}",$pedido->nombre,$body);
    $body = str_replace("{{comercio}}",$empresa->nombre,$body);
    $body = str_replace("{{pedido_items}}",$pedido_items,$body);
    $headers = "From: no-repy@pediclick.com\r\n";
    $headers.= "MIME-Version: 1.0\r\n";
    $headers.= "Content-Type: text/html; charset=ISO-8859-1\r\n";
    $headers.= "BCC: basile.matias99@gmail.com\r\n";
    @mail($pedido->email,$template->nombre,$body,$headers);

    $this->load->helper("sms_helper");
    $resultado = send_sms(array(
      "numero"=>$empresa->telefono_delivery,
      "texto"=>"Tiene un nuevo pedido por Pediclick"
    ));

    // Enviamos un email al comercio
    /*
    $this->load->model("Email_Template_Model");
    $template = $this->Email_Template_Model->get_by_key("nuevo-pedido-comercio",$pedido->id_empresa);
    if ($template === FALSE) {
      $template = new stdClass();
      $template->nombre = "Pedido enviado";
      $template->texto = "Su pedido ha sido enviado";
    }
    $body = $template->texto;
    $body = str_replace("{{cliente}}",$pedido->nombre,$body);
    $body = str_replace("{{comercio}}",$empresa->nombre,$body);
    $body = str_replace("{{pedido_items}}",$pedido_items,$body);
    $headers = "From: no-repy@pediclick.com\r\n";
    $headers.= "MIME-Version: 1.0\r\n";
    $headers.= "Content-Type: text/html; charset=ISO-8859-1\r\n";
    $headers.= "BCC: basile.matias99@gmail.com\r\n";
    @mail($pedido->email,$template->nombre,$body,$headers);
    */

    // Limpiamos el pedido de la sesion
    unset($_SESSION["pedido"]);

    echo json_encode(array(
      "error"=>0,
    ));
  }

  function aceptar_pedido($id) {

    $pedido = $this->modelo->get($id);
    $this->load->model("Empresa_Model");
    $empresa = $this->Empresa_Model->get($pedido->id_empresa);
    $this->load->model("Cliente_Model");
    $cliente = $this->Cliente_Model->get($pedido->id_cliente);

    // Enviamos un email al usuario
    $this->load->model("Email_Template_Model");
    $template = $this->Email_Template_Model->get_by_key("aceptar-pedido",$pedido->id_empresa);
    if ($template === FALSE) {
      $template = new stdClass();
      $template->nombre = "Pedido aceptado";
      $template->texto = "Su pedido ha sido aceptado por {{comercio}}.";
    }
    $body = $template->texto;
    $body = str_replace("{{comercio}}",$empresa->nombre,$body);
    $body = str_replace("{{cliente}}",$cliente->nombre,$body);
    
    $headers = "From: no-repy@pediclick.com\r\n";
    $headers.= "MIME-Version: 1.0\r\n";
    $headers.= "Content-Type: text/html; charset=ISO-8859-1\r\n";
    $headers.= "BCC: basile.matias99@gmail.com\r\n";
    @mail($cliente->email,$template->nombre,$body,$headers);
    echo json_encode(array());
  }

  function rechazar_pedido($id) {

    $pedido = $this->modelo->get($id);
    $this->load->model("Empresa_Model");
    $empresa = $this->Empresa_Model->get($pedido->id_empresa);
    $this->load->model("Cliente_Model");
    $cliente = $this->Cliente_Model->get($pedido->id_cliente);

    // Enviamos un email al usuario
    $this->load->model("Email_Template_Model");
    $template = $this->Email_Template_Model->get_by_key("rechazar-pedido",$pedido->id_empresa);
    if ($template === FALSE) {
      $template = new stdClass();
      $template->nombre = "Pedido aceptado";
      $template->texto = "Su pedido ha sido aceptado por {{comercio}}.";
    }
    $body = $template->texto;
    $body = str_replace("{{comercio}}",$empresa->nombre,$body);
    $body = str_replace("{{cliente}}",$cliente->nombre,$body);
    
    $headers = "From: no-repy@pediclick.com\r\n";
    $headers.= "MIME-Version: 1.0\r\n";
    $headers.= "Content-Type: text/html; charset=ISO-8859-1\r\n";
    $headers.= "BCC: basile.matias99@gmail.com\r\n";
    @mail($cliente->email,$template->nombre,$body,$headers);
    echo json_encode(array());
  }

  function imprimir_comanda($id) {
    // Obtenemos los datos para imprimir una COMANDA
    $pedido = $this->modelo->get($id);
    $this->load->model("Empresa_Model");
    $empresa = $this->Empresa_Model->get($pedido->id_empresa);
    $pedido->empresa_nombre = $empresa->nombre;
    $pedido->empresa_telefono = $empresa->telefono;
    //TODO: CANTIDAD DE LINEAS DE LA COMANDERA
    echo json_encode($pedido);
  }

  // MUESTRA LOS ITEMS DE LOS PEDIDOS, EN VEZ DE LOS PEDIDOS COMPLETOS
  // Utilizado en la COCINA, para que vaya tildando lo que se va haciendo
  function buscar_items() {
    $id_empresa = ($this->input->get("e") !== FALSE) ? $this->input->get("e") : parent::get_empresa();
    $desde = $this->input->get("desde");
    $hasta = $this->input->get("hasta");
    $limit = $this->input->get("limit");
    $offset = $this->input->get("offset");
    $filter = $this->input->get("filter");        
    $id_cliente = ($this->input->get("id_cliente") !== FALSE) ? $this->input->get("id_cliente") : 0;
    $id_usuario = ($this->input->get("id_usuario") !== FALSE) ? $this->input->get("id_usuario") : 0;
    $lista = $this->modelo->buscar_items(array(
      "limit"=>$limit,
      "offset"=>$offset,
      "filter"=>$filter,
      "desde"=>$desde,
      "hasta"=>$hasta,
      "id_cliente"=>$id_cliente,
      "id_usuario"=>$id_usuario,
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

  function reasignar() {
    $id_empresa = parent::get_empresa();
    $id = $this->input->post("id");
    $id_proximo = $this->input->post("id_proximo");
    $titulo_proximo = $this->input->post("titulo_proximo");
    $sql = "UPDATE facturas SET ";
    $sql.= " id_referencia = $id_proximo, ";
    $sql.= " reference_id = '$titulo_proximo' ";
    $sql.= "WHERE id = $id AND id_empresa = $id_empresa ";
    $q = $this->db->query($sql);
    echo json_encode(array(
      "error"=>0,
    ));
  }
  
  // BUSCA LOS PEDIDOS
  function buscar() {
    
    $id_empresa = ($this->input->get("e") !== FALSE) ? $this->input->get("e") : parent::get_empresa();
    $desde = $this->input->get("desde");
    $hasta = $this->input->get("hasta");
    $id_cliente = ($this->input->get("id_cliente") !== FALSE) ? $this->input->get("id_cliente") : 0;
    $id_usuario = ($this->input->get("id_usuario") !== FALSE) ? $this->input->get("id_usuario") : 0;
    $numero = $this->input->get("numero");
        
        $limit = $this->input->get("limit");
        $offset = $this->input->get("offset");
        $filter = $this->input->get("filter");        
        $this->load->helper("fecha_helper");
        if (!empty($desde)) $desde = fecha_mysql($desde);
        if (!empty($hasta)) $hasta = fecha_mysql($hasta);
    
    $lista = $this->modelo->buscar(array(
      "limit"=>$limit,
      "offset"=>$offset,
      "filter"=>$filter,
      "desde"=>$desde,
      "hasta"=>$hasta,
      "id_cliente"=>$id_cliente,
      "id_usuario"=>$id_usuario,
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
    unset($array->undefined);
    unset($array->mesa);
    unset($array->cliente);
    unset($array->nombre);
    unset($array->telefono);
    unset($array->direccion);
    unset($array->items);
    unset($array->cheques);
    unset($array->tarjetas);
    unset($array->id_mesa);
    unset($array->titulo);
    unset($array->estado);
    unset($array->provincia);
    unset($array->reparto);
    unset($array->fecha_reparto);
    
    // Redondeamos
    if (isset($array->total)) $array->total = round($array->total,2);
    if (isset($array->subtotal)) $array->subtotal = round($array->subtotal,2);
    if (isset($array->porc_descuento)) $array->porc_descuento = round($array->porc_descuento,2);
    if (isset($array->descuento)) $array->descuento = round($array->descuento,2);
    if (isset($array->costo_envio)) $array->costo_envio = round($array->costo_envio,2);
  }
  
  
  function insert() {
    
    $this->db->db_debug = FALSE;
    $id_empresa = parent::get_empresa();

    $this->load->helper("fecha_helper");

    // Tomamos los datos
    $array = $this->parse_put();
    $array->id_empresa = $id_empresa;
    $array->fecha = date("Y-m-d");
    $array->hora = date("H:i:s");
    $array->last_update = time();
    $array->reference_id = $array->titulo;

    $items = $array->items;
    $tarjetas = $array->tarjetas;
    $cheques = $array->cheques;
    $cliente = trim($array->cliente);
    $direccion = trim($array->direccion);
    $telefono = trim($array->telefono);
    $this->remove_attributes($array);

    // Obtenemos el punto de venta
    $this->load->model("Punto_Venta_Model");
    $punto_venta = $this->Punto_Venta_Model->get_por_defecto(array(
      "id_empresa"=>$id_empresa,
    ));
    $array->id_punto_venta = $punto_venta->id;
    $array->punto_venta = $punto_venta->numero;

    // Obtenemos el numero
    $sql = "SELECT IF(MAX(numero) IS NULL,0,MAX(numero)) AS ultimo ";
    $sql.= "FROM facturas ";
    $sql.= "WHERE id_empresa = $id_empresa ";
    $sql.= "AND id_punto_venta = $array->id_punto_venta ";
    $q_ult = $this->db->query($sql);
    $r_ult = $q_ult->row();
    $array->numero = ($r_ult->ultimo + 1);
    $array->comprobante = "R 0001-".str_pad($array->numero, 8, "0",STR_PAD_LEFT);

    $id_factura = $this->modelo->insert($array);

    // Guardamos un nuevo cliente
    if ($array->id_cliente == 0 && !empty($cliente) && strtolower($cliente) != "consumidor final") {
      $this->load->model("Cliente_Model");
      $c = new stdClass();
      $c->id_empresa = $array->id_empresa;
      $c->nombre = $cliente;
      $c->direccion = $direccion;
      $c->telefono = $telefono;
      $c->activo = 1;
      $c->id_tipo_iva = 4;
      $c->forma_pago = "E";
      // Guardamos el nuevo cliente
      $id_cliente = $this->Cliente_Model->insert($c);

      // Actualizamos el cliente en la factura
      if ($id_cliente != -1) {
        $sql = "UPDATE facturas SET id_cliente = $id_cliente ";
        $sql.= "WHERE id_empresa = $array->id_empresa AND id = $id_factura ";
        $this->db->query($sql);
      }
    }

    foreach($items as $l) {
      $this->db->insert("facturas_items",array(
        "id_empresa"=>$array->id_empresa,
        "id_punto_venta"=>$array->id_punto_venta,
        "id_factura"=>$id_factura,
        "id_articulo"=>$l->id_articulo,
        "cantidad"=>$l->cantidad,
        "descripcion"=>$l->descripcion,
        "precio"=>$l->precio,
        "nombre"=>$l->nombre,
        "tipo"=>$l->tipo,
        "tipo_cantidad"=>$l->tipo_cantidad,
        "total_con_iva"=>$l->total_con_iva,
        "bonificacion"=>$l->bonificacion,
        "orden"=>$l->orden,
      ));
    }

    // Si se finalizo, se debe descontar del stock
    if ($array->id_tipo_estado == 6) {
      foreach($items as $l) {
        $sql = "UPDATE articulos SET stock = stock - ($l->cantidad) ";
        $sql.= "WHERE id_empresa = $array->id_empresa ";
        $sql.= "AND id = $l->id_articulo ";
        $sql.= "AND usa_stock = 1 ";
        $this->db->query($sql);
      }
    }

    if (!empty($tarjetas)) {
      $this->load->model("Cupon_Tarjeta_Model");
      // GUARDAMOS LAS TARJETAS
      foreach($tarjetas as $t) {
        $t->id_factura = $id_factura;
        $t->fecha = date("Y-m-d H:i:s");
        $t->id_empresa = $id_empresa;
        $this->Cupon_Tarjeta_Model->insert($t);
      }      
    }
        
    if (!empty($cheques)) {
      $this->load->model("Cheque_Model");
      // GUARDAMOS LOS CHEQUES
      foreach($cheques as $ch) {
        $ch->id_factura = $id_factura;
        $ch->id_empresa = $id_empresa;
        $ch->fecha_recibido = date("Y-m-d");
        $ch->fecha_emision = fecha_mysql($ch->fecha_emision);
        $ch->fecha_cobro = fecha_mysql($ch->fecha_cobro);
        $ch->tipo = "C";
        $ch->id_cliente = $array->id_cliente;
        $this->Cheque_Model->insert($ch);
      }      
    }

    // Notificamos a la vista para que se actualice
    $this->load->model("Log_Model");
    $this->Log_Model->notify(array(
      "texto"=>'COMANDO:$(".salones_container .tab_link.active").trigger("click");',
      "id_empresa"=>$id_empresa,
    ));

    echo json_encode(array(
      "id"=>$id_factura,
      "numero"=>$array->numero,
      "error"=>0,
    ));
  }
  
  
  function update($id_factura) {
    
    // Si es 0, entonces lo insertamos
    if ($id_factura == 0) { $this->insert($id_factura); return; }    

    $this->db->db_debug = FALSE;
    $id_empresa = parent::get_empresa();

    $this->load->helper("fecha_helper");

    // Tomamos los datos
    $array = $this->parse_put();
    $array->id_empresa = $id_empresa;
    $array->id_punto_venta = (isset($array->id_punto_venta)) ? $array->id_punto_venta : 0;

    // Controlamos el last_update
    $sql = "SELECT last_update FROM facturas WHERE id_empresa = $id_empresa AND id = $id_factura AND id_punto_venta = $array->id_punto_venta ";
    $q = $this->db->query($sql);
    if ($q->num_rows()>0) {
      $fact = $q->row();
      if ($fact->last_update > $array->last_update) {
        $this->show_error("El pedido ha sido actualizado por otro usuario de manera mas reciente. Recargue la pagina y vuelva a intentarlo.");
      }
    }

    // La fecha y la hora se van actualizando a medida que se van guardando
    $array->fecha = date("Y-m-d");
    $array->hora = date("H:i:s");
    $array->last_update = time();
    
    $items = $array->items;
    $array->reference_id = $array->titulo;
    $this->remove_attributes($array);
    $this->modelo->update($id_factura,$array);

    // Guardamos un nuevo cliente
    if ($array->id_cliente == 0 && !empty($cliente) && strtolower($cliente) != "consumidor final") {
      $this->load->model("Cliente_Model");
      $c = new stdClass();
      $c->id_empresa = $array->id_empresa;
      $c->nombre = $cliente;
      $c->direccion = $direccion;
      $c->telefono = $telefono;
      $c->activo = 1;
      $c->id_tipo_iva = 4;
      $c->forma_pago = "E";
      $id_cliente = $this->Cliente_Model->insert($c);

      // Actualizamos el cliente en la factura
      if ($id_cliente != -1) {
        $sql = "UPDATE facturas SET id_cliente = $id_cliente ";
        $sql.= "WHERE id_empresa = $array->id_empresa AND id = $id_factura ";
        $this->db->query($sql);
      }
    }

    $this->db->query("DELETE FROM facturas_items WHERE id_factura = $id_factura AND id_empresa = $id_empresa");
    foreach($items as $l) {
      $this->db->insert("facturas_items",array(
        "id_empresa"=>$array->id_empresa,
        "id_factura"=>$id_factura,
        "id_punto_venta"=>$array->id_punto_venta,
        "id_articulo"=>$l->id_articulo,
        "cantidad"=>$l->cantidad,
        "descripcion"=>$l->descripcion,
        "precio"=>$l->precio,
        "nombre"=>$l->nombre,
        "tipo"=>$l->tipo,
        "tipo_cantidad"=>$l->tipo_cantidad,
        "total_con_iva"=>$l->total_con_iva,
        "orden"=>$l->orden,
      ));
    }

    // Si se finalizo, se debe descontar del stock
    if ($array->id_tipo_estado == 6) {
      foreach($items as $l) {
        $sql = "UPDATE articulos SET stock = stock - ($l->cantidad) ";
        $sql.= "WHERE id_empresa = $array->id_empresa ";
        $sql.= "AND id = $l->id_articulo ";
        $sql.= "AND usa_stock = 1 ";
        $this->db->query($sql);
      }
    }

    $this->db->query("DELETE FROM cupones_tarjetas WHERE id_empresa = $id_empresa AND id_factura = $id_factura");
    if (!empty($tarjetas)) {
      $this->load->model("Cupon_Tarjeta_Model");
      // GUARDAMOS LAS TARJETAS
      foreach($tarjetas as $t) {
        $t->id_factura = $id_factura;
        $t->fecha = date("Y-m-d H:i:s");
        $t->id_empresa = $id_empresa;
        $this->Cupon_Tarjeta_Model->insert($t);
      }      
    }
    
    $this->db->query("DELETE FROM cheques WHERE id_empresa = $id_empresa AND id_factura = $id_factura");
    if (!empty($cheques)) {
      $this->load->model("Cheque_Model");
      // GUARDAMOS LOS CHEQUES
      foreach($cheques as $ch) {
        $ch->id_factura = $id_factura;
        $ch->id_empresa = $id_empresa;
        $ch->fecha_recibido = date("Y-m-d");
        $ch->fecha_emision = fecha_mysql($ch->fecha_emision);
        $ch->fecha_cobro = fecha_mysql($ch->fecha_cobro);
        $ch->tipo = "C";
        $ch->id_cliente = $array->id_cliente;
        $this->Cheque_Model->insert($ch);
      }      
    }

    // Notificamos a la vista para que se actualice
    $this->load->model("Log_Model");
    $this->Log_Model->notify(array(
      "texto"=>'COMANDO:$(".salones_container .tab_link.active").trigger("click");',
      "id_empresa"=>$id_empresa,
    ));

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
    $this->modelo->delete($id);
    echo json_encode(array());
  }

  function eliminar_reserva($id) {
    $id_empresa = parent::get_empresa();
    $sql = "SELECT * FROM facturas WHERE id_empresa = $id_empresa AND id = $id AND id_tipo_estado = 1";
    $q = $this->db->query($sql);
    if ($q->num_rows()>0) {
      $this->modelo->delete($id);
      echo json_encode(array(
        "error"=>0,
      ));
      return;
    } else {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"No existe una reserva pendiente para la mesa.",
      ));
      return;      
    }
  }

}