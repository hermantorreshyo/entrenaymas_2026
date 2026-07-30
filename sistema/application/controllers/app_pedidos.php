<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class App_Pedidos extends REST_Controller {

  private $id_empresa = 1234;
  private $fcm_key = "AAAAuQD3UDQ:APA91bHDJQ7TfFrds9l7ShcCtxtgAuMLobXkkqGnUR4mBtrZc3YMgq3x6bqR_F-azpC6lEtFovgxLYGGJl2_Bh5v07uHHPy-9J15bh6gydsOyb2wYoJM2FL4g-ZR4q6IYmQLppfy29ed";

  // TIEMPO DE RECEPCION DEL PEDIDO
  // facturas.fecha

  // TIEMPO DE RETIRO EN EL LOCAL
  // facturas.custom_2

  // TIEMPO DE ACEPTACION DEL COMERCIO
  // facturas.custom_3

  // OBSERVACIONES PARA EL VENDEDOR
  // facturas.custom_4

  // REGLA APLICADA
  // facturas.custom_5

  // OBSERVACIONES RECHAZO COMERCIO
  // facturas.custom_6
  
  // TIEMPO DE ENTREGA AL CLIENTE
  // facturas.vencimiento

  // CREADA DESDE EL PANEL DE CONTROL
  // facturas.reference_id = 1

  // ESTADOS DEL PEDIDO
  // -1: En proceso
  //  0: Pendiente
  //  1: Aceptado por el comercio
  //  2: Aceptado por el repartidor
  //  3: Repartidor en comercio
  //  4: En camino
  //  5: Pedido Listo (NUEVO para que el comercio marque cuando finalizo de preparar) 
  //  6: Finalizado
  //  7: Rechazado

  function __construct() {
    /*
    set_time_limit(0);
    error_reporting(0); // Evita que warnings/deprecations de PHP contaminen la respuesta
    */
    parent::__construct();
  }

  // Esta funcion sincroniza los productos de las empresas
  function sincronizar_productos() {
    $id_empresa = parent::get_get("id_empresa");
    $id_usuario = 0;
    
    // DISTRIBUIDORA MITRE
    if ($id_empresa == 264) $id_usuario = 2399;
    // ESPACIO VIRTUAL
    else if ($id_empresa == 287) $id_usuario = 2494;
    // DISTRIBUIDORA ACCESO
    else if ($id_empresa == 1055) $id_usuario = 2434;

    if (empty($id_usuario)) return;

    $q = $this->db->query("SELECT * FROM articulos WHERE id_empresa = $id_empresa AND custom_5 = '1' ");
    foreach($q->result() as $r) {
      // Buscamos si ya fue creado en nuestra empresa
      $qq = $this->db->query("SELECT * FROM articulos WHERE id_empresa = $this->id_empresa AND id = $r->id");
      if ($qq->num_rows() == 0) {
        // Lo insertamos
        $r->id_usuario = $id_usuario;
        $r->id_empresa = $this->id_empresa;
        $this->db->insert("articulos",$r);
      } else {
        // Actualizamos el precio
        $sql = "UPDATE articulos SET ";
        $sql.= " precio_final = $r->precio_final, ";
        $sql.= " precio_final_dto = $r->precio_final_dto ";
        $sql.= "WHERE id = $r->id ";
        $this->db->query($sql);
      }

      // Actualizamos las fotos
      $this->db->query("DELETE FROM articulos_images WHERE id_empresa = $this->id_empresa AND id_articulo = $r->id ");
      $qq = $this->db->query("SELECT * FROM articulos_images WHERE id_empresa = $id_empresa AND id_articulo = $r->id ");
      foreach($qq->result() as $rr) {
        $rr->id_empresa = $this->id_empresa;
        $this->db->insert("articulos_images",$rr);
      }
    }
    echo "TERMINO";
  }

  function simular_asignar_pedido($id_repartidor = 0) {
    $this->load->model("Repartidor_Model");
    $this->Repartidor_Model->asignar_pedido(array(
      "id_repartidor"=>$id_repartidor,
    ));
  }

  function cambiar_estado_comercio() {
    // TODO: Habilita o deshabilita al comercio
  }

  function convertir_estado($id_estado) {
    $s = "";
    if ($id_estado == -1) $s = "En Proceso";
    else if ($id_estado == 0) $s = "Pendiente";
    else if ($id_estado == 1) $s = "Aceptado por el comercio";
    else if ($id_estado == 2) $s = "Aceptado por el repartidor";
    else if ($id_estado == 3) $s = "Repartidor en comercio";
    else if ($id_estado == 4) $s = "En camino";
    else if ($id_estado == 5) $s = "Pedido Listo";
    else if ($id_estado == 6) $s = "Finalizado";
    else if ($id_estado == 7) $s = "Rechazado";
    return $s;
  }

  function calcular_demora_comercio() {

    $id_empresa = 1234;
    $id_comercio = $this->input->post("id_comercio");
    $latitud_cliente = $this->input->post("latitud_cliente");
    $longitud_cliente = $this->input->post("longitud_cliente");
    $items = json_decode($this->input->post("items"));
    $this->load->model("Usuario_Model");
    $this->load->model("Web_Configuracion_Model");
    $usuario = $this->Usuario_Model->get($id_comercio,array(
      "id_empresa"=>$id_empresa
    ));
    $empresa = $this->Web_Configuracion_Model->get($id_empresa);

    // Tiempo para ir a buscar el producto
    $tiempo_envio = 15;

    // Calculamos la demora total
    if ($usuario->hora_desde == "00:15:00") {
      $tiempo_envio = 15;
    } else if ($usuario->hora_desde == "00:30:00") {
      $tiempo_envio = 30;
    } else if ($usuario->hora_desde == "00:45:00") {
      $tiempo_envio = 45;
    } else if ($usuario->hora_desde == "00:60:00") {
      $tiempo_envio = 60;
    } else if ($usuario->hora_desde == "00:90:00") {
      $tiempo_envio = 90;
    } else if ($usuario->hora_desde == "99:99:99") {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"El comercio no esta aceptando pedidos en este momento.",
      ));
      exit();
    }

    // Demora del servicio
    $tiempo_servicio = (isset($empresa->texto_registro) && !empty($empresa->texto_registro) && is_numeric($empresa->texto_registro)) ? ((float)$empresa->texto_registro) : 0;

    // Coeficiente de seguridad
    $coeficiente_seguridad = (isset($empresa->texto_registro_gracias) && !empty($empresa->texto_registro_gracias) && is_numeric($empresa->texto_registro_gracias)) ? ((float)$empresa->texto_registro_gracias) : 1;

    // Maxima demora de los productos
    
    $maxima_demora_item = 0;
    if (!empty($items)) {
      $this->load->model("Articulo_Model");
      foreach($items as $item) {
        $articulo = $this->Articulo_Model->get($item,$id_empresa);
        $demora_item = is_numeric($articulo->custom_1) ? ((int)$articulo->custom_1) : 0;
        if ($demora_item > $maxima_demora_item) $maxima_demora_item = $demora_item;
      }
    }

    // Tiempo de entrega
    $tiempo_recorrido = 0;
    $distancia = 0;
    if (!empty($latitud_cliente)) {
      include_once("/home/ubuntu/data/sistema/application/helpers/coord_helper.php");
      $coor_comercio = explode(";", $usuario->titulo);
      $latitud_comercio = (float)$coor_comercio[0];
      $longitud_comercio = (float)$coor_comercio[1];
      $latitud_cliente = (float)$latitud_cliente;
      $longitud_cliente = (float)$longitud_cliente;
      if ($latitud_cliente != 0 && $longitud_cliente != 0) {
        $distancia = distance($latitud_comercio,$longitud_comercio,$latitud_cliente,$longitud_cliente) * 1000; // Esta en KM, pasarlo a Mtrs
        $metros_por_minuto = (isset($empresa->texto_staff) && !empty($empresa->texto_staff) && is_numeric($empresa->texto_staff)) ? ((float)$empresa->texto_staff) : 333;
        $tiempo_recorrido = round($distancia / $metros_por_minuto,0);
      }
    }

    $demora = $tiempo_envio;
    // A la demora general del negocio le sumamos cuanto tarda la preparacion del pedido en si
    $demora += $maxima_demora_item;
    // Tambien le sumamos la demora del servicio
    $demora += $tiempo_servicio;
    // Sumamos el tiempo de entrega al domicilio del cliente
    $demora += $tiempo_recorrido;
    // Finalmente lo multiplicamos por el coeficiente de seguridad
    $demora = round($demora * $coeficiente_seguridad,0);

    // =============================

    // Calculamos el costo de envio
    $valor_envio = ($empresa->texto_quienes_somos + 0); // Valor de envio minimo
    $distancia_minima = ($empresa->texto_newsletter + 0);
    $valor_variable_por_metro = ($empresa->texto_contacto + 0);

    // Si la distancia es mayor a la distancia minima
    if ($distancia > $distancia_minima) {
      $valor_envio = $valor_envio + (($distancia - $distancia_minima) * $valor_variable_por_metro);
      $valor_envio = ceil($valor_envio / 10) * 10;
    }

    if ($valor_envio > 1000) $valor_envio = ($empresa->texto_quienes_somos + 0); // Valor de envio minimo

    echo json_encode(array(
      "error"=>0,
      "demora"=>$demora,
      "tiempo_envio"=>$tiempo_envio,
      "tiempo_servicio"=>$tiempo_servicio,
      "demora_maxima_item"=>$maxima_demora_item,
      "tiempo_recorrido"=>$tiempo_recorrido,
      "coeficiente_seguridad"=>$coeficiente_seguridad,
      "valor_envio"=>$valor_envio,
    ));
  }


  // GUARDA UN NUEVO PEDIDO DESDE EL PANEL DE CONTROL
  function insert() {

    error_reporting(0); // Evita que warnings/deprecations de PHP contaminen la respuesta
    
    $this->db->db_debug = FALSE;
    $id_empresa = parent::get_empresa();
    $id_punto_venta = 2070;

    $this->load->helper("fecha_helper");

    // Tomamos los datos
    $array = $this->parse_put();
    $array->id_empresa = $id_empresa;
    $array->fecha = date("Y-m-d");
    $array->hora = date("H:i:s");
    $array->last_update = time();

    $items = $array->items;
    $costo_envio = isset($array->costo_envio) ? $array->costo_envio : 0;
    $tarjetas = isset($array->tarjetas) ? $array->tarjetas : array();
    $cheques = isset($array->cheques) ? $array->cheques : array();
    $cliente = trim($array->cliente);
    $email = trim($array->email);
    $latitud = trim($array->latitud);
    $longitud = trim($array->longitud);
    $direccion = trim($array->direccion);
    $telefono = trim($array->telefono);
    $documento = trim($array->documento);
    $array->id_punto_venta = $id_punto_venta;
    $array->punto_venta = 2;
    $array->estado = 1;
    $array->id_tipo_comprobante = 999;

    $sql = "SELECT IF(MAX(numero) IS NULL,0,MAX(numero)) AS ultimo ";
    $sql.= "FROM facturas ";
    $sql.= "WHERE id_empresa = $id_empresa ";
    $sql.= "AND id_punto_venta = $id_punto_venta ";
    $q_ult = $this->db->query($sql);
    $r_ult = $q_ult->row();
    $array->numero = ($r_ult->ultimo + 1);
    $array->comprobante = "R 0002-".str_pad($array->numero, 8, "0",STR_PAD_LEFT);

    $this->load->model("Pedido_Mesa_Model");
    $id_factura = $this->Pedido_Mesa_Model->insert($array);

    // Guardamos un nuevo cliente
    if ($array->id_cliente == 0 && !empty($cliente) && strtolower($cliente) != "consumidor final") {
      $this->load->model("Cliente_Model");
      $c = new stdClass();
      $c->id_empresa = $array->id_empresa;
      $c->nombre = $cliente;
      $c->email = $email;
      $c->direccion = $direccion;
      // Separamos el telefono en dos
      if (strlen($telefono)>8) {
        $c->telefono = substr($telefono, 0, 3);
        $c->celular = substr($telefono, 3);
      }
      $c->cuit = $documento;
      $c->activo = 1;
      $c->id_tipo_iva = 4;
      $c->forma_pago = "E";
      $c->latitud = $latitud;
      $c->longitud = $longitud;
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
        "id_factura"=>$id_factura,
        "id_punto_venta"=>$id_punto_venta,
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

    require APPPATH.'libraries/Mandrill/Mandrill.php';
    $q_emp = $this->db->query("SELECT * FROM empresas WHERE id = $this->id_empresa");
    $empresa = $q_emp->row();

    $q_conf_web = $this->db->query("SELECT bcc_email FROM web_configuracion WHERE id_empresa = $this->id_empresa ");
    $web_conf = $q_conf_web->row();
    $empresa->bcc_email = $web_conf->bcc_email;

    $bcc_emails = array("basile.matias99@gmail.com");

    $total_con_envio = $array->total;
    if (isset($array->total) && isset($costo_envio)) {
      $total_con_envio = ((float)$array->total + (float)$costo_envio);
    }

    // Si es toque, tomamos los datos del comercio
    $this->load->model("Usuario_Model");
    $comercio = $this->Usuario_Model->get($array->id_usuario,array(
      "id_empresa"=>$this->id_empresa,
    ));
    $comercio_direccion = $comercio->direccion;

    $this->load->model("Cliente_Model");
    $cli = $this->Cliente_Model->get($array->id_cliente,$this->id_empresa,array(
      "buscar_consultas"=>0,
      "buscar_etiquetas"=>0,
    ));

    $this->load->model("Email_Template_Model");
    $template = $this->Email_Template_Model->get_by_key("email-compra-ok-admin",$id_empresa);
    $template = $this->replace_placeholder($template,array(
      "cliente"=>$cliente,
      "usuario"=>$comercio->nombre,
      "comercio_direccion"=>$comercio_direccion,
      "numero"=>$array->comprobante,
      "total"=>(isset($array->total) ? $array->total : ""),
      "total_con_envio"=>$total_con_envio,
      "items"=>$items,
      "direccion"=>(isset($array->direccion) ? $array->direccion : ""),
      "email"=>((isset($cli->email) && !empty($cli->email)) ? $cli->email : ""),
      "forma_pago"=>"Efectivo",
      "costo_envio"=>$costo_envio,
      "observaciones"=>"",
    ));

    mandrill_send(array(
      "to"=>$empresa->email,
      "subject"=>$template->nombre,
      "body"=>$template->texto,
      "bcc"=>$bcc_emails,
    ));
    
    // Y un email al usuario con el link de su pedido
    if (isset($cli->email) && !empty($cli->email)) {
      $template = $this->Email_Template_Model->get_by_key("email-compra-ok",$id_empresa);
      $template = $this->replace_placeholder($template,array(
        "cliente"=>$cliente,
        "usuario"=>$comercio->nombre,
        "comercio_direccion"=>$comercio_direccion,
        "numero"=>$array->comprobante,
        "total"=>(isset($array->total) ? $array->total : ""),
        "total_con_envio"=>$total_con_envio,
        "items"=>$items,
        "direccion"=>(isset($array->direccion) ? $array->direccion : ""),
        "email"=>$cli->email,
        "forma_pago"=>"Efectivo",
        "costo_envio"=>$costo_envio,
        "empresa"=>$empresa->nombre,
        "id_empresa"=>$empresa->id,
        "link_web"=>"https://www.toque.com.ar/",
        "link_pedido_toque"=>"https://www.toque.com.ar/web/finalizar/?id=".$id_factura,
        "id"=>$id_factura,
        "observaciones"=>"",
      ));

      mandrill_send(array(
        "from_name"=>$empresa->nombre,
        "to"=>$cli->email,
        "to_name"=>$cliente,
        "reply_to"=>$empresa->email, // TODO: Podria ser al email del vendedor
        "subject"=>$template->nombre,
        "body"=>$template->texto,
        "bcc"=>$bcc_emails,
      ));
    }
        
    echo json_encode(array(
      "id"=>$id_factura,
      "numero"=>$array->numero,
      "error"=>0,
    ));
  }


  // Reemplaza todos los placeholder del email
  // @param $template: Objeto que viene del web_model->get_email
  // @param config: array con todos los placeholder posibles
  function replace_placeholder($template,$config = array()) {

    if (empty($template)) return FALSE;
    $id = isset($config["id"]) ? $config["id"] : "";
    $empresa = isset($config["empresa"]) ? $config["empresa"] : "";
    $link_web = isset($config["link_web"]) ? $config["link_web"] : "";
    $link_pedido_toque = isset($config["link_pedido_toque"]) ? $config["link_pedido_toque"] : "";
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : "";
    $numero = isset($config["numero"]) ? $config["numero"] : "";
    $cliente = isset($config["cliente"]) ? $config["cliente"] : "";
    $usuario = isset($config["usuario"]) ? $config["usuario"] : "";
    $comercio_direccion = isset($config["comercio_direccion"]) ? $config["comercio_direccion"] : "";
    $direccion = isset($config["direccion"]) ? $config["direccion"] : "";
    $total = isset($config["total"]) ? $config["total"] : "";
    $total_con_envio = isset($config["total_con_envio"]) ? $config["total_con_envio"] : "";
    $items = isset($config["items"]) ? $config["items"] : "";
    $observaciones = isset($config["observaciones"]) ? $config["observaciones"] : "";
    $forma_pago = isset($config["forma_pago"]) ? $config["forma_pago"] : "";
    $costo_envio = isset($config["costo_envio"]) ? $config["costo_envio"] : "";
    $fecha = isset($config["fecha"]) ? $config["fecha"] : date("d/m/Y");
    $hora = isset($config["hora"]) ? $config["hora"] : date("H:i");

    // Primero reemplazamos el asunto
    if (empty($template->nombre)) $template->nombre = "Compra exitosa";
    $template->nombre = str_replace("{{numero}}", $numero, $template->nombre);
    $template->nombre = str_replace("{{cliente}}", $cliente, $template->nombre);

    // Despues hacemos el reemplazo en el cuerpo del email
    if (empty($template->texto)) $template->texto = "La compra de {{cliente}} ha sido pagada.";
    if (!empty($total)) $template->texto = str_replace("{{total}}", $total, $template->texto);
    if (!empty($total_con_envio)) $template->texto = str_replace("{{total_con_envio}}", $total_con_envio, $template->texto);
    $template->texto = str_replace("{{costo_envio}}", $costo_envio, $template->texto);
    if (!empty($cliente)) $template->texto = str_replace("{{cliente}}", mb_convert_encoding($cliente, 'ISO-8859-1', 'UTF-8'), $template->texto);
    $template->texto = str_replace("{{observaciones}}", mb_convert_encoding($observaciones, 'ISO-8859-1', 'UTF-8'), $template->texto);
    if (!empty($usuario)) {
      $template->texto = str_replace("{{usuario}}", $usuario, $template->texto);
      $template->texto = str_replace("{{comercio}}", $usuario, $template->texto);
    }
    if (!empty($direccion)) $template->texto = str_replace("{{direccion}}", mb_convert_encoding($direccion, 'ISO-8859-1', 'UTF-8'), $template->texto);
    if (!empty($comercio_direccion)) $template->texto = str_replace("{{comercio_direccion}}", mb_convert_encoding($comercio_direccion, 'ISO-8859-1', 'UTF-8'), $template->texto);
    if (!empty($forma_pago)) $template->texto = str_replace("{{forma_pago}}", $forma_pago, $template->texto);
    if (!empty($numero)) $template->texto = str_replace("{{numero}}", $numero, $template->texto);
    if (!empty($fecha)) $template->texto = str_replace("{{fecha}}", $fecha, $template->texto);
    if (!empty($hora)) $template->texto = str_replace("{{hora}}", $hora, $template->texto);
    if (!empty($id)) {
      $link_ver_pedido = "https://www.toque.com.ar/sistema/pedidos/function/ver_pdf/".$id."/";
      $template->texto = str_replace("{{link_ver_pedido}}",$link_ver_pedido,$template->texto);
      $template->texto = str_replace("{{link_pedido}}",$link_ver_pedido,$template->texto);
    }
    if (!empty($link_web)) $template->texto = str_replace("{{link_web}}",$link_web,$template->texto);  
    if (!empty($empresa)) $template->texto = str_replace("{{empresa}}",$empresa,$template->texto);
    if (!empty($id_empresa)) $template->texto = str_replace("{{id_empresa}}",$id_empresa,$template->texto);

    $s_items = "<table border='1' cellpadding='5' cellspacing='0'><thead><tr><th>Item</th><th>Cant.</th><th>Precio</th><th>Subtotal</th></tr></thead><tbody>";
    if (isset($items) && !empty($items)) {
      foreach($items as $item) {
        $s_items.="<tr>";
        $s_items.="<td>$item->nombre<br/>$item->descripcion</td>";
        $s_items.="<td>$item->cantidad</td>";
        $s_items.="<td>$ $item->precio</td>";
        $s_items.="<td>$ $item->total_con_iva</td>";
        $s_items.="</tr>";
      }
    }
    $s_items.= "</tbody></table>";
    $template->texto = str_replace("{{items}}", $s_items, $template->texto);
    $template->texto = str_replace("'", "\"", $template->texto);
    return $template;
  }


  function editar_repartidor() {
    set_time_limit(0);
    $id_empresa = parent::get_empresa();
    $ventas = parent::get_post("ventas",array());
    $id_repartidor = parent::get_post("id_repartidor",0);
    $repartidor = parent::get_post("repartidor","");
    if (empty($id_repartidor) || empty($ventas)) {
      echo json_encode(array("error"=>1));
      exit();
    }
    foreach($ventas as $v) {
      $sql = "UPDATE facturas SET id_vendedor = $id_repartidor, vendedor = '$repartidor' ";
      $sql.= "WHERE id = '".$v["id"]."' AND id_punto_venta = '".$v["id_punto_venta"]."' AND id_empresa = '$id_empresa' ";
      $this->db->query($sql);

      // Cada vez que se edita a mano el repartidor, lo logeuamos
      $id_pedido = $v["id"];
      $this->log(array("linea"=>"EDITAR REPARTIDOR MANUAL A $repartidor ID_REPARTIDOR: $id_repartidor","filename"=>$id_pedido.".txt"));
    }
    echo json_encode(array("error"=>0));
  }   

  function editar_observaciones() {
    set_time_limit(0);
    $id_empresa = parent::get_empresa();
    $id = parent::get_post("id",0);
    $id_punto_venta = parent::get_post("id_punto_venta",0);
    $observaciones = parent::get_post("observaciones","");
    $custom_4 = parent::get_post("custom_4","");
    $sql = "UPDATE facturas SET observaciones = '$observaciones', custom_4 = '$custom_4' ";
    $sql.= "WHERE id = '$id' AND id_punto_venta = '$id_punto_venta' AND id_empresa = '$id_empresa' ";
    $this->db->query($sql);

    // Cada vez que se edita a mano el repartidor, lo logeuamos
    $this->log(array("linea"=>"EDITAR OBSERVACIONES MANUAL: '$observaciones'","filename"=>$id.".txt"));

    echo json_encode(array("error"=>0));
  }   

  function editar_estado() {
    set_time_limit(0);
    $id_empresa = parent::get_empresa();
    $ventas = parent::get_post("ventas",array());
    $id_tipo_estado = parent::get_post("id_tipo_estado",0);
    if (empty($id_tipo_estado) || empty($ventas)) {
      echo json_encode(array("error"=>1));
      exit();
    }
    $this->load->model("Toque_Billetera_Movimiento_Model");
    foreach($ventas as $v) {

      $sql = "SELECT * FROM facturas ";
      $sql.= "WHERE id = '".$v["id"]."' AND id_punto_venta = '".$v["id_punto_venta"]."' AND id_empresa = '$id_empresa' ";
      $q = $this->db->query($sql);
      if ($q->num_rows() == 0) continue;
      $factura = $q->row();

      $sql = "UPDATE facturas SET id_tipo_estado = $id_tipo_estado ";
      $sql.= "WHERE id = '".$v["id"]."' AND id_punto_venta = '".$v["id_punto_venta"]."' AND id_empresa = '$id_empresa' ";
      // Cada vez que se edita a mano el estado, lo logeuamos
      $id_pedido = $v["id"];
      $estado = $this->convertir_estado($id_tipo_estado);
      $this->log(array("linea"=>"EDITAR ESTADO MANUAL A $estado ID_ESTADO: $id_tipo_estado","filename"=>$id_pedido.".txt"));
      $this->db->query($sql);

      /*
      // Si el estado anterior del comprobante es RECHAZADO
      if (($factura->id_tipo_estado == 7) && ($factura->tarjeta > 0 || $factura->cta_cte > 0)) {

        // Borramos el movimiento para devolverle el saldo a la billetera
        $this->Toque_Billetera_Movimiento_Model->borrar(array(
          "tipo"=>0,
          "id_cliente"=>$factura->id_cliente,
          "id_factura"=>$factura->id,
          "id_punto_venta"=>$factura->id_punto_venta,
          "id_empresa"=>$id_empresa,
        ));
      
      // Si estamos anulando, y es con tarjeta o con la billetera
      } else if (($id_tipo_estado == 7) && ($factura->tarjeta > 0 || $factura->cta_cte > 0)) {

        // Controlamos que no se haya hecho antes para no volver a sumarle a la billetera
        $sql = "SELECT * FROM toque_billetera_movimientos ";
        $sql.= "WHERE id_empresa = $id_empresa ";
        $sql.= "AND id_cliente = '$factura->id_cliente' ";
        $sql.= "AND id_factura = '$factura->id' ";
        $sql.= "AND id_punto_venta = '$factura->id_punto_venta' ";
        $sql.= "AND tipo = 0 ";
        $qb = $this->db->query($sql);
        if ($qb->num_rows() == 0) {
          $monto = $factura->cta_cte + $factura->tarjeta;
          $this->Toque_Billetera_Movimiento_Model->ingreso(array(
            "id_empresa"=>$id_empresa,
            "id_factura"=>$factura->id,
            "id_cliente"=>$factura->id_cliente,
            "id_punto_venta"=>$factura->id_punto_venta,
            "id_concepto"=>1478, // Anulacion de una venta
            "monto"=>$monto,
            "observaciones"=>"Rechazo Comercio ".$factura->comprobante,
          ));
        }
      }*/      
    }
    echo json_encode(array("error"=>0));
  }   

  function log($config = array()) {
    $dir = "logs/".$this->id_empresa."/";
    $filename = isset($config["filename"]) ? $config["filename"] : "log_toque.txt";
    $linea = isset($config["linea"]) ? $config["linea"] : "";
    file_put_contents($dir.$filename, date("Y-m-d H:i:s")." ".$linea."\n", FILE_APPEND);
  }

  function get_categorias_select($id_padre = 0,$separador = "",$id_empresa) {
    $result = array();
    $q = $this->db->query("SELECT * FROM toque_categorias WHERE id_padre = $id_padre AND id_empresa = $id_empresa ORDER BY id ASC");
    foreach($q->result() as $row) {
      $e = new stdClass();
      $e->id = $row->id;
      $e->id_padre = $id_padre;
      $e->nombre = $separador.$row->nombre;
      $result[] = $e;
      $hijos = $this->get_categorias_select($row->id,$separador."&nbsp;&nbsp;&nbsp;",$id_empresa);
      $result = array_merge($result,$hijos);
    }
    return $result;
  }

  function get_categorias() {
    $id_empresa = parent::get_get("id_empresa",parent::get_empresa());
    $arr = $this->get_categorias_select(0,"",$id_empresa);
    echo json_encode(array(
      "results"=>$arr,
      "total"=>sizeof($arr)
    ));
  }

  function get_pedido() {
    error_reporting(0); // Evita que warnings/deprecations de PHP contaminen la respuesta

    header('Access-Control-Allow-Origin: *');

    $id_empresa = parent::get_post("id_empresa",$this->id_empresa);
    $id_pedido = parent::get_post("id_pedido",0);
    $id_usuario = parent::get_post("id_usuario",0);

    $this->load->model("Punto_Venta_Model");
    $punto_venta = $this->Punto_Venta_Model->get_por_defecto(array(
      "id_empresa"=>$id_empresa,
    ));

    $this->load->model("Factura_Model");
    $factura = $this->Factura_Model->get($id_pedido,$punto_venta->id,array(
      "id_empresa"=>$id_empresa,
      "buscar_consultas"=>0,
      "buscar_etiquetas"=>0,
    ));
    // TODO: es FALSE o el pedido no es pendiente
    if ($factura === FALSE) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"No existe pedido con ID: $id_pedido ",
      ));
      exit();
    } else {

      // Obtenemos los datos del comercio
      $this->load->model("Usuario_Model");
      $usuario = $this->Usuario_Model->get($factura->id_usuario,array(
        "id_empresa"=>$id_empresa,
      ));
      $factura->usuario_direccion = $usuario->direccion;
      $factura->usuario_telefono = $usuario->telefono;

      $this->load->helper("fecha_helper");
      $factura->custom_2 = (!empty($factura->custom_2)) ? fecha_es($factura->custom_2) : "";

      $factura->total = (float)$factura->total + (float)$factura->costo_envio;

      // Le agregamos el piso y departamento a la app
      if (isset($factura->cliente->custom_3) && !empty($factura->cliente->custom_3)) {
        $factura->cliente->direccion = $factura->cliente->direccion." Piso: ".$factura->cliente->custom_3." Dpto: ".$factura->cliente->custom_4;
      }
      echo json_encode($factura);
    }
  }

  function get_pedidos() {
    header('Access-Control-Allow-Origin: *');
    $id_empresa = parent::get_post("id_empresa",0);
    $id = parent::get_post("id",0);
    $limit = parent::get_post("limit",0);
    $offset = parent::get_post("offset",25);
    $orden = parent::get_post("orden",0);
    $this->load->helper("fecha_helper");

    $sql = "SELECT SQL_CALC_FOUND_ROWS F.*, U.nombre AS comercio, U.direccion AS comercio_direccion, U.titulo, ";
    $sql.= " C.direccion AS cliente_direccion, C.latitud AS cliente_latitud, C.longitud AS cliente_longitud, ";
    $sql.= " DATE_FORMAT(F.fecha,'%d/%m/%Y') AS fecha ";
    $sql.= "FROM facturas F ";
    $sql.= "INNER JOIN com_usuarios U ON (F.id_empresa = U.id_empresa AND F.id_usuario = U.id) ";
    $sql.= "INNER JOIN clientes C ON (F.id_empresa = U.id_empresa AND F.id_cliente = C.id) ";
    $sql.= "WHERE F.id_empresa = $id_empresa ";
    $sql.= "AND F.id_usuario = $id ";
    $sql.= "AND F.id_tipo_estado >= 0 ";
    if ($orden == 1) {
      $sql.= "AND F.id_tipo_estado < 6 ";
      $sql.= "ORDER BY F.vencimiento ASC ";
    } else $sql.= "ORDER BY F.fecha DESC, F.hora DESC ";
    $sql.= "LIMIT $limit,$offset ";
    $q = $this->db->query($sql);

    $q_total = $this->db->query("SELECT FOUND_ROWS() AS total");
    $total = $q_total->row();

    $resultado = array();
    foreach($q->result() as $r) {

      $coor_comercio = explode(";", $r->titulo);
      $r->latitud_comercio = (float)$coor_comercio[0];
      $r->longitud_comercio = (float)$coor_comercio[1];

      $r->custom_2 = (!empty($r->custom_2)) ? ($r->custom_2) : "";
      $r->vencimiento = (!empty($r->vencimiento)) ? ($r->vencimiento) : "";

      $resultado[] = $r;
    }
    echo json_encode(array(
      "results"=>$resultado,
      "total"=>$total->total,
    ));
  }

  // Actualiza el TOKEN en la APP de los repartidores, para que se le puedan mandar las notificaciones
  function guardar_configuracion() {
    header('Access-Control-Allow-Origin: *');
    if ($this->input->post("id") === FALSE) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"El parametro ID es obligatorio",
      ));
      exit();
    }
    $id = parent::get_post("id",0);
    $token = parent::get_post("token","");
    $id_empresa = parent::get_post("id_empresa",$this->id_empresa);

    $sql = "UPDATE com_usuarios_extension SET ";
    if (!empty($token)) $sql.= "custom_4 = '$token' ";
    $sql.= "WHERE id_usuario = '$id' AND id_empresa = $id_empresa ";
    file_put_contents("app_pedidos.txt", $sql."\n", FILE_APPEND);
    $this->db->query($sql);
    echo json_encode(array(
      "error"=>0,
    ));
  }

  // UTILIZADO EN LA APP
  function login() {

    header('Access-Control-Allow-Origin: *');
    $email = $this->input->post("email");
    $password = $this->input->post("password");
    $id_empresa = $this->input->post("id_empresa");

    $sql = "SELECT V.* ";
    $sql.= "FROM com_usuarios V ";
    $sql.= "WHERE V.email = '$email' ";
    $sql.= "AND V.password = '$password' ";
    $sql.= "AND V.id_empresa = '$id_empresa' ";
    $sql.= "LIMIT 0,1 ";
    $query = $this->db->query($sql);

    // Datos invalidos
    $resultado = $query->result();
    if (empty($resultado)) {
      // Usuario incorrecto
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"Usuario o clave incorrectos.",
        "id"=>0,
      ));
      return;
    } else {
      $usuario = $query->row();
      echo json_encode(array(
        "error"=>0,
        "mensaje"=>"",
        "id"=>$usuario->id,
        "id_empresa"=>$usuario->id_empresa,
      ));
    }
  }

  // El comercio acepta el pedido, se envia un SMS al cliente
  // y comenzamos con la busqueda de repartidores
  function aceptar_pedido_comercio() {

    error_reporting(0); // Evita que warnings/deprecations de PHP contaminen la respuesta

    header('Access-Control-Allow-Origin: *');
    $id_pedido = parent::get_get("id",0);
    $id_empresa = parent::get_get("id_empresa",$this->id_empresa);
    $this->log(array("linea"=>"Aceptar Pedido Comercio: $id_pedido","filename"=>$id_pedido.".txt"));

    $this->load->helper("fecha_helper");
    $this->load->model("Factura_Model");
    $factura = $this->Factura_Model->get($id_pedido,0,array(
      "id_empresa"=>$id_empresa,
      "buscar_consultas"=>0,
      "buscar_etiquetas"=>0,
    ));
    // TODO: es FALSE o el pedido no es pendiente
    if ($factura === FALSE) {
      $this->log(array("linea"=>"ERROR: COMERCIO ACEPTO NO EXISTE ID_PEDIDO: $id_pedido","filename"=>$id_pedido.".txt"));
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"No existe pedido pendiente con ID: $id_pedido ",
      ));
      exit();
    }

    // PARA ACEPTAR SI O SI TIENE QUE ESTAR EN PEDIDO
    if ($factura->id_tipo_estado >= 1) {
      $this->log(array("linea"=>"ERROR: EL PEDIDO YA FUE ACEPTADO CON ATENRIORIDAD","filename"=>$id_pedido.".txt"));
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"El pedido ya fue aceptado.",
      ));
      exit();      
    }

    // Mandamos un SMS al cliente
    if (isset($factura->cliente->celular) && !empty($factura->cliente->celular)) {
      if ($factura->numero_envio == "pickup") {
        $texto = "$factura->usuario ha aceptado su pedido! Al retirar presente el numero: $factura->numero y el codigo: $factura->link_envio.";
      } else {
        $texto = "$factura->usuario ha aceptado su pedido! Nro. Pedido: $factura->numero.";
      }
      require APPPATH.'libraries/Whatsapp.php';
      $whatsapp = new Whatsapp();
      $whatsapp->send(array(
        "numbers"=>$factura->cliente->fax.$factura->cliente->telefono.$factura->cliente->celular,
        "body"=>$texto,
      ));
    }

    // Cambiamos el estado del pedido a ACEPTADO POR EL COMERCIO
    $this->Factura_Model->cambiar_estado(array(
      "id_empresa"=>$id_empresa,
      "id"=>$factura->id,
      "id_punto_venta"=>$factura->id_punto_venta,
      "estado"=>1, // Estado ACEPTADO POR COMERCIO
    ));
    $this->log(array("linea"=>"Cambiamos estado factura a ACEPTADO (1)","filename"=>$id_pedido.".txt"));

    $this->load->model("Articulo_Model");
    $this->load->model("Empresa_Model");
    $empresa = $this->Empresa_Model->get($id_empresa);

    // Calculamos cuando va a demorar el pedido

    // Tomamos el producto que mas demora (minutos)
    $maxima_demora_producto = 0;
    foreach($factura->items as $item) {
      $articulo = $this->Articulo_Model->get($item->id_articulo,$id_empresa);
      $articulo->custom_1 = (float) $articulo->custom_1;
      $this->log(array("linea"=>"CALCULO DEMORA PRODUCTO $articulo->nombre $articulo->custom_1","filename"=>$id_pedido.".txt"));
      $tiempo_producto = (is_numeric($articulo->custom_1)) ? $articulo->custom_1 : 0;
      if ($tiempo_producto > $maxima_demora_producto) $maxima_demora_producto = $tiempo_producto;
    }
    $this->log(array("linea"=>"MAXIMA DEMORA PRODUCTO $maxima_demora_producto","filename"=>$id_pedido.".txt"));

    // Tomamos la demora del comercio
    $this->load->model("Usuario_Model");
    $comercio = $this->Usuario_Model->get($factura->id_usuario,array(
      "id_empresa"=>$id_empresa,
    ));
    $demora_comercio = 0;
    if ($comercio->hora_desde == "00:15:00") $demora_comercio = 15;
    else if ($comercio->hora_desde == "00:30:00") $demora_comercio = 30;
    else if ($comercio->hora_desde == "00:45:00") $demora_comercio = 45;
    else if ($comercio->hora_desde == "00:60:00") $demora_comercio = 60;
    else if ($comercio->hora_desde == "00:90:00") $demora_comercio = 90;
    $this->log(array("linea"=>"DEMORA COMERCIO $demora_comercio","filename"=>$id_pedido.".txt"));

    // Demora del servicio
    $tiempo_servicio = (isset($empresa->config["texto_registro"]) && !empty($empresa->config["texto_registro"]) && is_numeric($empresa->config["texto_registro"])) ? ((float)$empresa->config["texto_registro"]) : 0;
    $this->log(array("linea"=>"DEMORA DEL SERVICIO $tiempo_servicio","filename"=>$id_pedido.".txt"));    

    // Guardamos el tiempo que debe ir el toquer a retirar el pedido por el comercio
    $original = fecha_mysql($factura->fecha)." ".$factura->hora;
    $time = new DateTime($original);
    $demora_total = ($demora_comercio + $maxima_demora_producto);
    $time->add(new DateInterval("PT".$demora_total."M"));
    $hora_retiro = $time->format("Y-m-d H:i:s");

    // Si el pedido es de la modalidad de pickup, este proceso termina aca
    if ($factura->numero_envio == "pickup") {
      // Usamos el campo vencimiento para poner la fecha y hora que estara hecho el pedido
      $ahora = date("Y-m-d H:i:s");
      $this->db->query("UPDATE facturas SET vencimiento = '$hora_retiro', custom_2 = '$hora_retiro', custom_3 = '$ahora' WHERE id_empresa = $id_empresa AND id = $factura->id ");      
      echo json_encode(array(
        "error"=>0,
        "mensaje"=>""
      ));
      return;
    }    

    // Tomamos la hora original del pedido y le sumamos la cantidad de minutos de demora
    
    $time = new DateTime($original);
    $time->add(new DateInterval("PT".$demora_total."M"));
    $vencimiento = $time->format("Y-m-d H:i:s");
    $this->log(array("linea"=>"FECHA VENCIMIENTO $vencimiento","filename"=>$id_pedido.".txt"));

    // Usamos el campo vencimiento para poner la fecha y hora que estara hecho el pedido
    $ahora = date("Y-m-d H:i:s");
    $this->db->query("UPDATE facturas SET vencimiento = '$vencimiento', custom_2 = '$hora_retiro', custom_3 = '$ahora' WHERE id_empresa = $id_empresa AND id = $factura->id ");

    echo json_encode(array(
      "error"=>0,
      "mensaje"=>""
    ));
  }

  // El comercio rechaza el pedido, se envia un SMS al cliente
  function rechazar_pedido_comercio() {
    header('Access-Control-Allow-Origin: *');
    $id_empresa = parent::get_post("id_empresa",$this->id_empresa);
    $id_pedido = parent::get_post("id",0);
    $motivo = parent::get_post("motivo","");
    $observaciones = parent::get_post("observaciones","");
    $this->log(array("linea"=>"Comercio Rechazo Comercio","filename"=>$id_pedido.".txt"));

    $this->load->model("Factura_Model");
    $factura = $this->Factura_Model->get($id_pedido,0,array(
      "id_empresa"=>$id_empresa,
      "buscar_consultas"=>0,
      "buscar_etiquetas"=>0,
    ));
    // TODO: es FALSE o el pedido no es pendiente
    if ($factura === FALSE) {
      $this->log(array("linea"=>"ERROR: COMERCIO RECHAZO NO EXISTE ID_PEDIDO: $id_pedido","filename"=>$id_pedido.".txt"));
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"No existe pedido pendiente con ID: $id_pedido ",
      ));
      exit();
    }

    if ($factura->id_tipo_estado >= 1) {
      $this->log(array("linea"=>"ERROR: EL PEDIDO YA FUE ACEPTADO ANTERIOMENTE","filename"=>$id_pedido.".txt"));
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"No existe pedido pendiente con ID: $id_pedido ",
      ));
      exit();      
    }

    // Mandamos un SMS al cliente
    if (isset($factura->cliente->celular) && !empty($factura->cliente->celular)) {
      $mensaje = "$factura->usuario ha rechazado tu pedido Nro: $factura->numero. ".$motivo." ".$observaciones." Pedi de nuevo en pedienchacabuco.com.ar";
      if ($factura->tarjeta > 0) {
        $mensaje = "$factura->usuario ha rechazado tu pedido $factura->numero. ".$motivo." ".$observaciones." Pedi de nuevo, el dinero ya esta en tu BILLETERA TOQUE, mas info pedienchacabuco.com.ar/b";
      }
      require APPPATH.'libraries/Whatsapp.php';
      $whatsapp = new Whatsapp();
      $whatsapp->send(array(
        "numbers"=>$factura->cliente->fax.$factura->cliente->telefono.$factura->cliente->celular,
        "body"=>$mensaje,
      ));
    }

    // Cambiamos el estado del pedido a RECHAZADO POR EL COMERCIO
    $this->Factura_Model->cambiar_estado(array(
      "id_empresa"=>$id_empresa,
      "id"=>$factura->id,
      "id_punto_venta"=>$factura->id_punto_venta,
      "estado"=>7, // Estado RECHAZADO
      "set_custom_6"=>$motivo."\n".$observaciones,
    ));
    $this->log(array("linea"=>"CAMBIO ESTADO PEDIDO RECHAZADO ID_PEDIDO: $id_pedido","filename"=>$id_pedido.".txt"));

    // Si el pedido fue en tarjeta o utilizando la billetera de TOQUE
    /*
    if ($factura->tarjeta > 0 || $factura->cta_cte > 0) {
      if ($factura->tarjeta > 0 && $factura->cta_cte == 0) {
        // El pago era solo en tarjeta, devolvemos la tarjeta mas el costo de envio
        $monto = $factura->tarjeta + $factura->costo_envio;
      } else if ($factura->tarjeta == 0 && $factura->cta_cte > 0) {
        // El pago fue usando solo la billetera, devolvemos ese mismo monto
        $monto = $factura->cta_cte;
      } else if ($factura->tarjeta > 0 && $factura->cta_cte > 0) {
        // Es un pago combinado entre tarjeta y biletera
        $monto = $factura->cta_cte + $factura->tarjeta;
      }
      $this->load->model("Toque_Billetera_Movimiento_Model");
      $this->Toque_Billetera_Movimiento_Model->ingreso(array(
        "id_empresa"=>$id_empresa,
        "id_factura"=>$factura->id,
        "id_cliente"=>$factura->id_cliente,
        "id_punto_venta"=>$factura->id_punto_venta,
        "id_concepto"=>1478, // Anulacion de una venta
        "monto"=>$monto,
        "observaciones"=>"Rechazo Comercio ".$factura->comprobante,
      ));
    }
    */

    echo json_encode(array(
      "error"=>0,
    ));
  }  

  // Marca que el pedido esta listo y notifica al cliente para que lo vaya a buscar
  function pedido_listo() {
    header('Access-Control-Allow-Origin: *');
    $id_empresa = $this->id_empresa;
    $id_pedido = parent::get_get("id",0);
    $this->log(array("linea"=>"EL PEDIDO ESTA LISTO! ID_PEDIDO: $id_pedido","filename"=>$id_pedido.".txt"));

    $this->load->model("Factura_Model");
    $factura = $this->Factura_Model->get($id_pedido,0,array(
      "id_empresa"=>$id_empresa,
      "buscar_consultas"=>0,
      "buscar_etiquetas"=>0,
    ));
    // TODO: es FALSE o el pedido no es pendiente
    if ($factura === FALSE) {
      $this->log(array("linea"=>"ERROR: NOTIFICAR NUEVO REPARTIDOR NO EXISTE ID_PEDIDO: $id_pedido","filename"=>$id_pedido.".txt"));
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"No existe pedido pendiente con ID: $id_pedido ",
      ));
      exit();
    }

    if ($factura->numero_envio == "pickup") {

      $this->load->model("Usuario_Model");
      $usuario = $this->Usuario_Model->get($factura->id_usuario,array(
        "id_empresa"=>$id_empresa,
      ));

      // Mandamos un SMS al cliente
      if (isset($factura->cliente->celular) && !empty($factura->cliente->celular)) {
        $this->log(array("linea"=>"WHATSAPP PEDIDO LISTO","filename"=>$id_pedido.".txt"));
        require APPPATH.'libraries/Whatsapp.php';
        $whatsapp = new Whatsapp();
        $whatsapp->send(array(
          "numbers"=>$factura->cliente->fax.$factura->cliente->telefono.$factura->cliente->celular,
          "body"=>"Su pedido en $factura->usuario ya esta listo! Podes pasar a buscarlo por $usuario->direccion.",
        ));
      }      

      // Cambiamos el estado del pedido a listo
      $this->Factura_Model->cambiar_estado(array(
        "id_empresa"=>$id_empresa,
        "id"=>$id_pedido,
        "estado"=>$factura->id_tipo_estado, // No le cambiamos el estado
        "set_codigo_postal"=>date("Y-m-d H:i:s"),
      ));
      $this->log(array("linea"=>"EL PEDIDO SE CAMBIO A ESTADO LISTO.","filename"=>$id_pedido.".txt"));  
    }

    echo json_encode(array(
      "error"=>0,
      "mensaje"=>$result,
    ));
  }


  // Marca que el pedido fue retirado por el cliente en la modalidad de pickup o take away
  function pickup_listo() {
    header('Access-Control-Allow-Origin: *');
    $id_empresa = $this->id_empresa;
    $id_pedido = parent::get_get("id",0);
    $this->log(array("linea"=>"EL PEDIDO FUE RETIRADO DEL COMERCIO POR PICKUP. ID_PEDIDO: $id_pedido","filename"=>$id_pedido.".txt"));

    $this->load->model("Factura_Model");

    // Cambiamos el estado del pedido a listo
    $this->Factura_Model->cambiar_estado(array(
      "id_empresa"=>$id_empresa,
      "id"=>$id_pedido,
      "estado"=>6, // Pasa a finalizado
    ));
    $this->log(array("linea"=>"EL PEDIDO SE CAMBIO A ESTADO FINALIZADO.","filename"=>$id_pedido.".txt"));    

    echo json_encode(array(
      "error"=>0,
      "mensaje"=>$result,
    ));
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
    $conf["fecha_reparto"] = parent::get_get("fecha_reparto","");
    $conf["numero_reparto"] = parent::get_get("numero_reparto","");
    $conf["tipo_cliente"] = parent::get_get("tipo_cliente","");
    $conf["forma_pago"] = parent::get_get("forma_pago","0");
    $conf["tipo_estado"] = parent::get_get("tipo_estado","-1");
    $conf["in_tipos_estados"] = str_replace("-", ",", parent::get_get("in_tipos_estados",""));
    $conf["id_proyecto"] = ($this->input->get("id_proyecto") !== FALSE) ? $this->input->get("id_proyecto") : 0;
    $conf["codigo_articulo"] = ($this->input->get("codigo_articulo") !== FALSE) ? $this->input->get("codigo_articulo") : "";
    $conf["tipos"] = ($this->input->get("tipos") !== FALSE) ? $this->input->get("tipos") : "";
    return $conf;
  }

  function exportar_excel() {
    $this->load->helper("fecha_helper");
    $this->load->model("Venta_Model");
    $conf = $this->get_params();
    $conf["limit"] = FALSE;

    // No tenemos que mostrar los pedidos con -1
    $conf["not_in_tipos_estados"] = "-1";

    $salida = $this->Venta_Model->listado($conf);
    $resultado = array();
    $header = array(
      "Hora Pedido","Comprobante","Cliente","Repartidor","Comercio","Total","Costo Envio","Descuento","Oferta","Pago","Billetera",
      "Hora Aceptacion Comercio","Hora Preparado","Hora Retiro","Hora Retirado","Hora Entrega","Hora Entregado","Hora Repartidor en Comercio",
      "DNI","Email","Direccion","Telefono Cliente","Cliente Lat","Cliente Long","Items"
    );

    foreach($salida["results"] as $r) {
      $row = new stdClass();

      $forma_pago = "";
      if ($r->cta_cte > 0 && ($r->efectivo == 0 && $r->tarjeta == 0)) $forma_pago = "Billetera";
      else if ($r->efectivo > 0) $forma_pago = "Efectivo";
      else if ($r->tarjeta > 0) $forma_pago = "MercadoPago";

      // Obtenemos los datos del cliente
      $sql = "SELECT * FROM clientes WHERE id_empresa = 1234 AND id = $r->id_cliente";
      $qq = $this->db->query($sql);
      if ($qq->num_rows() > 0) {
        $cliente = $qq->row();  
      } else {
        $cliente = new stdClass();
        $cliente->cuit = "";
        $cliente->email = "";
        $cliente->telefono = "";
        $cliente->celular = "";
        $cliente->direccion = "";
        $cliente->custom_3 = "";
        $cliente->custom_4 = "";
        $cliente->latitud = 0;
        $cliente->longitud = 0;
      }

      // Obtenemos los items
      $sql = "SELECT * FROM facturas_items WHERE id_factura = $r->id AND id_empresa = 1234 AND id_punto_venta = $r->id_punto_venta ";
      $qq = $this->db->query($sql);
      $r->item = "";
      foreach($qq->result() as $rr) {
        $r->item .= "$rr->cantidad x $rr->nombre. ";
        if (!empty($rr->descripcion)) $r->item .= " ($rr->descripcion). ";
        $r->item .= "\n";
      }

      // Retirado por el comercio
      $sql = "SELECT fecha FROM repartidores_pedidos WHERE id_factura = $r->id AND id_empresa = 1234 AND id_punto_venta = $r->id_punto_venta ";
      $sql.= "AND estado = 'L' ";
      $qq = $this->db->query($sql);      
      $r->retirado = "";
      if ($qq->num_rows() > 0) {
        $rr = $qq->row();
        $r->retirado = $rr->fecha;
      }

      // Repartidor en el comercio
      $sql = "SELECT fecha FROM repartidores_pedidos WHERE id_factura = $r->id AND id_empresa = 1234 AND id_punto_venta = $r->id_punto_venta ";
      $sql.= "AND estado = 'Y' ";
      $qq = $this->db->query($sql);      
      $r->en_comercio = "";
      if ($qq->num_rows() > 0) {
        $rr = $qq->row();
        $r->en_comercio = $rr->fecha;
      }

      // Entregado al cliente
      $sql = "SELECT fecha FROM repartidores_pedidos WHERE id_factura = $r->id AND id_empresa = 1234 AND id_punto_venta = $r->id_punto_venta ";
      $sql.= "AND estado = 'F' ";
      $qq = $this->db->query($sql);      
      $r->entregado = "";
      if ($qq->num_rows() > 0) {
        $rr = $qq->row();
        $r->entregado = $rr->fecha;
      }

      $resultado[] = array(
        fecha_mysql($r->fecha)." ".$r->hora,
        $r->comprobante,
        $r->cliente,
        $r->vendedor,
        $r->usuario,
        $r->total,
        $r->costo_envio,
        $r->descuento,
        $r->custom_5,
        $forma_pago,
        $r->cta_cte,
        $r->custom_3,
        $r->codigo_postal, // Hora que marca el comercio como que esta listo el pedido
        $r->custom_2,
        $r->retirado,
        $r->vencimiento,
        $r->entregado,
        $r->en_comercio,
        $cliente->cuit,
        $cliente->email,
        $cliente->direccion." ".$cliente->custom_3." ".$cliente->custom_4,
        $cliente->telefono.$cliente->celular,
        $cliente->latitud,
        $cliente->longitud,
        $r->item,
      );
    }
    $this->load->library("Excel");
    $this->excel->create(array(
      "date"=>date("d/m/Y"),
      "filename"=>"listado_ventas",
      "header"=>$header,
      "footer"=>array(),
      "datos"=>$resultado,
      "title"=>"Listado de Ventas",
    ));
  }  


  function estadisticas_ventas() {

    $id_empresa = $this->get_empresa();
    $this->load->helper("fecha_helper");
    $f_desde = $this->input->post("desde");
    $f_hasta = $this->input->post("hasta");
    $intervalo = "D";

    $grafico = array();
    $total_ventas = 0;

    $series = array();
    $desde = new DateTime(fecha_mysql($f_desde));
    $hasta = new DateTime(fecha_mysql($f_hasta));
    $hasta->add(new DateInterval('P1D'));
    $interval = new DateInterval('P1'.$intervalo);
    $range = new DatePeriod($desde,$interval,$hasta);
    $diff = $hasta->diff($desde)->format("%a");
    $id_perfil = $_SESSION["perfil"];
    // Si es un perfil de comercio, filtramos por ese ID_USUARIO
    $id_comercio = (($id_perfil == 661) ? $_SESSION["id"] : 0);

    $res = array(); $cos = array();

    $sql_base = "SELECT ";
    $sql_base.= " SUM((F.total - F.interes) * IF(TC.negativo = 1,-1,1)) AS total, ";
    $sql_base.= " SUM(F.costo_final * IF(TC.negativo = 1,-1,1)) AS costo ";
    $sql_base.= "FROM facturas F ";
    $sql_base.= "INNER JOIN tipos_comprobante TC ON (F.id_tipo_comprobante = TC.id) ";
    $sql_base.= "WHERE F.id_empresa = $id_empresa ";
    $sql_base.= "AND F.id_tipo_comprobante != 0 ";
    $sql_base.= "AND F.tipo != 'C' ";
    $sql_base.= "AND F.anulada = 0 ";
    $sql_base.= "AND F.id_tipo_estado = 6 "; // Solo las finalizadas
    if (!empty($id_comercio)) $sql_base.= "AND F.id_usuario = $id_comercio ";

    // Recorremos cada dia del rango
    foreach($range as $fecha) {
      $sql = $sql_base;
      $sql.= "AND F.fecha = '".$fecha->format("Y-m-d")."' ";
      $q = $this->db->query($sql);
      foreach($q->result() as $rr) {
        // Sumamos los totales
        if (is_null($rr->total)) $rr->total = 0;
        $total = (float)$rr->total;
        $total_ventas += $total;
        $res[] = $total;
      }
    }

    // Cantidad de operaciones
    $sql = "SELECT COUNT(*) AS cantidad, ";
    $sql.= " SUM((efectivo-vuelto) * IF(TC.negativo = 1,-1,1)) AS efectivo, ";
    $sql.= " SUM((tarjeta-interes) * IF(TC.negativo = 1,-1,1)) AS tarjeta ";
    $sql.= "FROM facturas F ";
    $sql.= "INNER JOIN tipos_comprobante TC ON (F.id_tipo_comprobante = TC.id) ";
    $sql.= "WHERE F.id_empresa = $id_empresa ";
    $sql.= "AND F.id_tipo_comprobante != 0 ";
    $sql.= "AND F.tipo != 'C' ";
    $sql.= "AND F.anulada = 0 AND F.pendiente = 0 ";
    $sql.= "AND F.id_tipo_estado = 6 "; // Estado finalizado
    $sql.= "AND F.fecha >= '".$desde->format("Y-m-d")."' AND F.fecha < '".$hasta->format("Y-m-d")."' ";
    if (!empty($id_comercio)) $sql.= "AND F.id_usuario = $id_comercio ";
    $sql_totales = $sql;
    $q = $this->db->query($sql);
    $r = $q->row();
    $cantidad_operaciones = (is_null($r->cantidad)) ? 0 : ((float)$r->cantidad);
    $efectivo = (is_null($r->efectivo)) ? 0 : ((float)$r->efectivo);
    $tarjetas = (is_null($r->tarjeta)) ? 0 : ((float)$r->tarjeta);

    // Venta promedio
    $venta_promedio = ($cantidad_operaciones > 0) ? $total_ventas / $cantidad_operaciones : 0;
    // Venta promedio por dia
    $venta_promedio_por_dia = ($diff <= 0) ? 0 : (float) $total_ventas / $diff;

    $sql_base = "SELECT FI.id_articulo, FI.nombre, SUM(FI.cantidad) AS cantidad, SUM(FI.total_con_iva) AS total ";
    $sql_base.= "FROM facturas F ";
    $sql_base.= "INNER JOIN facturas_items FI ON (F.id_empresa = FI.id_empresa AND F.id_punto_venta = FI.id_punto_venta AND F.id = FI.id_factura) ";
    $sql_base.= "WHERE F.id_empresa = $id_empresa ";
    $sql_base.= "AND F.id_tipo_comprobante != 0 ";
    $sql_base.= "AND F.tipo != 'C' ";
    $sql_base.= "AND F.anulada = 0 ";
    $sql_base.= "AND F.id_tipo_estado = 6 "; // Solo las finalizadas
    if (!empty($id_comercio)) $sql_base.= "AND F.id_usuario = $id_comercio ";
    $sql_base.= "AND F.fecha >= '".$desde->format("Y-m-d")."' AND F.fecha < '".$hasta->format("Y-m-d")."' ";
    $sql_base.= "GROUP BY FI.id_articulo ";
    $sql_base.= "ORDER BY total DESC ";
    $sql_base.= "LIMIT 0,20 ";
    $q = $this->db->query($sql_base);
    $mas_vendidos = $q->result();

    // Agregamos la serie
    $series[] = array(
      "name"=>"Ventas",
      "data"=>$res,
    );

    echo json_encode(array(
      "grafico"=>array(
        "series"=>$series,
        "desde"=>$desde->format("d/m/Y"),
        "hasta"=>$hasta->format("d/m/Y"),
        "intervalo"=>$intervalo,
      ),
      "mas_vendidos"=>$mas_vendidos,
      "total_ventas"=>$total_ventas,
      "cantidad_operaciones"=>$cantidad_operaciones,
      "venta_promedio"=>$venta_promedio,
      "venta_promedio_por_dia"=>$venta_promedio_por_dia,
      "efectivo"=>$efectivo,
      "tarjetas"=>$tarjetas,
      "desde"=>str_replace("-","/",$f_desde),
      "hasta"=>str_replace("-","/",$f_hasta),
      "sql_totales"=>$sql_totales,
    ));

  }  

  function email_devolucion_dinero() {
    $id_empresa = 1234;
    $id_cliente = parent::get_post("id_cliente",0);
    $nombre = parent::get_post("nombre","");
    $email = parent::get_post("email","");
    $saldo = parent::get_post("saldo","");
    $para = "fb@toque.com.ar";
    require APPPATH.'libraries/Mandrill/Mandrill.php';
    $this->load->model("Email_Template_Model");
    $temp = $this->Email_Template_Model->get_by_key("email-pedido-devolucion",$id_empresa);
    if ($temp === FALSE) {
      echo json_encode(array("error"=>1,"mensaje"=>"Error al enviar el email"));
      exit();
    }
    $body = $temp->texto;
    $body = str_replace("{{id_cliente}}", $id_cliente, $body);
    $body = str_replace("{{nombre}}", mb_convert_encoding($nombre, 'UTF-8', 'ISO-8859-1'), $body);
    $body = str_replace("{{email}}", $email, $body);
    $body = str_replace("{{saldo}}", $saldo, $body);
    mandrill_send(array(
      "to"=>$para,
      "from"=>MAIL_FROM_ADDRESS,
      "from_name"=>$nombre,
      "subject"=>$temp->nombre,
      "body"=>$body,
      //"bcc"=>"basile.matias99@gmail.com",
    ));
    echo json_encode(array("error"=>0,"mensaje"=>"Tu solicitud ha sido enviada. Nos contactaremos a la mayor brevedad posible."));
  }

}