<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Alquileres extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Alquiler_Model', 'modelo');
  }

  // ENVIAMOS LOS EMAILS DE LOS CUPONES DE PAGO DE LOS ALQUILERES
  function enviar_emails() {

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    $this->load->helper("fecha_helper");
    $mes = parent::get_get("mes",date("m"));
    $anio = parent::get_get("anio",date("Y"));
    $mes = get_mes($mes);
    $id_propiedad = parent::get_get("id_propiedad",0);
    $cantidad = 0;

    $log_file = "logs/email_alquileres.txt";
    $this->load->model("Factura_Model");
    $this->load->model("Empresa_Model");
    $this->load->model("Email_Template_Model");
    $this->load->model("Log_Model");
    require APPPATH.'libraries/Mandrill/Mandrill.php';

    $sql = "SELECT F.id_empresa, C.nombre AS cliente, F.id, F.pago, C.telefono, C.celular, F.hash, C.email, ";
    $sql.= " F.comprobante, C.id AS id_cliente, DATE_FORMAT(F.fecha,'%d/%m/%Y') AS fecha, ";
    $sql.= " AC.pagada, AC.corresponde_a, A.id AS id_alquiler, ";
    $sql.= " DATE_FORMAT(AC.vencimiento,'%d/%m/%Y') AS vencimiento, AC.monto, AC.expensa, (AC.monto+AC.expensa) AS total, ";
    $sql.= " P.nombre AS propiedad, CONCAT(P.calle,' ',P.altura,' ',P.piso,' ',P.numero) AS direccion ";
    $sql.= "FROM facturas F ";
    $sql.= "INNER JOIN inm_alquileres A ON (A.id = F.id_referencia AND A.id_empresa = F.id_empresa) ";
    $sql.= "INNER JOIN inm_alquileres_cuotas AC ON (F.numero_referencia = AC.numero AND AC.id_alquiler = A.id AND F.id_empresa = AC.id_empresa) ";        
    $sql.= "INNER JOIN clientes C ON (C.id = A.id_cliente AND F.id_empresa = C.id_empresa) ";
    $sql.= "INNER JOIN inm_propiedades P ON (P.id = A.id_propiedad AND F.id_empresa = P.id_empresa) ";
    $sql.= "WHERE AC.pagada = 0 ";  // Que no haya sido pagada
    $sql.= "AND AC.corresponde_a = '$mes $anio' ";  // Que corresponda al mes
    $sql.= "AND A.enviar_recordatorios = 1 ";
    if (!empty($id_propiedad)) $sql.= "AND A.id_propiedad = $id_propiedad ";  // Que sea de la propiedad determinada
    echo $sql."<br/>";
    $q = $this->db->query($sql);
    foreach($q->result() as $r) {

      // Obtenemos la empresa
      $empresa = $this->Empresa_Model->get($r->id_empresa);

      // Obtenemos el template para enviar
      $template = $this->Email_Template_Model->get_by_key("email-cupon-pago",$r->id_empresa);
      if (empty($template)) {
        $this->Log_Model->log_file($log_file,"ERROR: No existe el template de enviar $empresa->nombre.");
        exit();
      }

      $body = $template->texto;
      $body = str_replace("{{nombre}}", $r->cliente, $body);
      $body = str_replace("{{periodo}}", $mes." ".$anio, $body);
      $body = str_replace("{{vencimiento}}", $r->vencimiento, $body);
      $body = str_replace("{{direccion}}", $r->direccion, $body);
      $body = str_replace("{{total}}", $r->total, $body);
      $body = str_replace("{{link_factura}}", "https://www.varcreative.com/sistema/alquileres/function/cupon_pago/".$r->hash, $body);

      $bcc_array = array();
      $bcc_array[] = "basile.matias99@gmail.com";

      mandrill_send(array(
        "to"=>$r->email,
        "from"=>MAIL_FROM_ADDRESS,
        "from_name"=>$empresa->nombre,
        "subject"=>$template->nombre,
        "body"=>$body,
        "reply_to"=>$empresa->email,
        "bcc"=>$bcc_array,
      ));   
      $cantidad++;     
    }
    echo "ENVIADOS: $cantidad";
  }  
  
  function save_file() {
    $this->load->helper("file_helper");
    $id_empresa = $this->get_empresa();
    if (!isset($_FILES['path']) || empty($_FILES['path'])) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"No se ha enviado ningun archivo."
      ));
      return;
    }
    $filename = filename($_FILES["path"]["name"],"-");
    $path = "uploads/$id_empresa/propiedades/$filename";
    @move_uploaded_file($_FILES["path"]["tmp_name"],$path);
    echo json_encode(array(
      "path"=>$path,
      "error"=>0,
    ));
  }  
    
  private function remove_properties($array) {
    unset($array->venc_prox_cuota);
    unset($array->cliente);
    unset($array->expensas);
    unset($array->cuotas);
    unset($array->desde);
    unset($array->hasta);
    unset($array->dias_para_vencimiento);
    unset($array->propiedad);
    unset($array->propiedad_codigo);
    unset($array->propiedad_localidad);
    unset($array->propiedad_path);
  }
    
  function update($id) {
    
    if ($id == 0) { $this->insert(); return; }
    $this->load->helper("file_helper");
    $array = $this->parse_put();
    
    $id_empresa = parent::get_empresa();
    $array->id_empresa = $id_empresa;
    $hoy = date("Y-m-d");
    $hora = date("H:i:s");

    // Acomodamos las fechas
    $this->load->helper("fecha_helper");
    $array->fecha_inicio = fecha_mysql($array->fecha_inicio);
    $array->fecha_fin = fecha_mysql($array->fecha_fin);
    $array->fecha_cancelacion_contrato = fecha_mysql($array->fecha_cancelacion_contrato);
    
    $cuotas = $array->cuotas;
    $expensas = $array->expensas;
    $this->remove_properties($array);
    $this->modelo->save($array);
        
    // LAS CUOTAS SOLO SE PUEDEN EDITAR, NO SE CREAN NUEVAS
    foreach($cuotas as $im) {

      // Controlamos si existe
      $sql = "SELECT * FROM inm_alquileres_cuotas ";
      $sql.= "WHERE id_empresa = $id_empresa ";
      $sql.= "AND id_alquiler = $id ";
      $sql.= "AND numero = $im->numero ";
      $q_cuota = $this->db->query($sql);
      if ($q_cuota->num_rows()>0) {

        // Actualizamos
        $sql = "UPDATE inm_alquileres_cuotas SET ";
        $sql.= "monto = '$im->monto', ";
        $sql.= "vencimiento = '$im->vencimiento' ";
        $sql.= "WHERE id_empresa = $id_empresa ";
        $sql.= "AND id_alquiler = $id ";
        $sql.= "AND numero = $im->numero ";
        $this->db->query($sql);

      } else {

        // Insertamos la cuota
        $this->db->query("INSERT INTO inm_alquileres_cuotas (id_empresa,id_alquiler,monto,numero,vencimiento,pagada,corresponde_a) VALUES($id_empresa,$id,'$im->monto','$im->numero','$im->vencimiento','$im->pagada','$im->corresponde_a')");
        $id_cuota = $this->db->insert_id();

        // Creamos el remito

        // Obtenemos el ultimo remito
        $remito = new stdClass();
        $sql = "SELECT IF(MAX(numero) IS NULL,0,MAX(numero)) AS numero ";
        $sql.= "FROM facturas ";
        $sql.= "WHERE id_tipo_comprobante = 999 ";
        $sql.= "AND id_empresa = $id_empresa ";
        $q_remito = $this->db->query($sql);
        $row = $q_remito->row();
        $numero_remito = $row->numero;
        $comprobante = "R 0001-".str_pad($numero_remito, 8, "0", STR_PAD_LEFT);
        $hash = md5($id_empresa.$comprobante);

        // Estas variables:
        //   id_referencia = id_alquiler
        //   numero_referencia = numero de cuota
        // Sirven al momento del pago, poder identificar que cuota se esta pagando
        $sql = "INSERT INTO facturas (";
        $sql.= " id_empresa, fecha, hora, punto_venta, numero, comprobante, ";
        $sql.= " id_cliente, id_tipo_comprobante, total, subtotal, ";
        $sql.= " tipo_pago, estado, hash, id_referencia, numero_referencia ";
        $sql.= ") VALUES (";
        $sql.= " '$id_empresa', '$hoy', '$hora', 1, $numero_remito, '$comprobante', ";
        $sql.= " '$array->id_cliente', '999', $im->monto, $im->monto,  ";
        $sql.= " 'C',1,'$hash', $id, $im->numero ";
        $sql.= ")";
        $this->db->query($sql);
      }
    }

    // Al cliente lo marcamos como inquilino
    $this->db->query("UPDATE clientes SET custom_4 = '1' WHERE id_empresa = $id_empresa AND id = '$array->id_cliente' ");

    $this->db->query("DELETE FROM inm_alquileres_expensas WHERE id_alquiler = $id AND id_empresa = $id_empresa");
    $k=0;
    foreach($expensas as $im) {
      $this->db->query("INSERT INTO inm_alquileres_expensas (id_empresa,id_alquiler,monto,nombre,orden) VALUES($id_empresa,$id,'$im->monto','$im->nombre','$k')");
      $k++;
    }
    $salida = array(
      "id"=>$id,
      "error"=>0,
    );
    echo json_encode($salida);        
  }
    
    
  function insert() {
        
    $this->load->helper("file_helper");
    $array = $this->parse_put();
        
    $id_empresa = parent::get_empresa();
    $array->id_empresa = $id_empresa;
    $hoy = date("Y-m-d");
    $hora = date("H:i:s");
    
    // Acomodamos las fechas
    $this->load->helper("fecha_helper");
    $array->fecha_inicio = fecha_mysql($array->fecha_inicio);
    $array->fecha_fin = fecha_mysql($array->fecha_fin);
    $array->fecha_cancelacion_contrato = fecha_mysql($array->fecha_cancelacion_contrato);
    
    $cuotas = $array->cuotas;
    $expensas = $array->expensas;
    $this->remove_properties($array);
    
    // Insertamos el publicidad
    $insert_id = $this->modelo->save($array);

    // Obtenemos el ultimo remito
    $remito = new stdClass();
    $sql = "SELECT IF(MAX(numero) IS NULL,0,MAX(numero)) AS numero ";
    $sql.= "FROM facturas ";
    $sql.= "WHERE id_tipo_comprobante = 999 ";
    $sql.= "AND id_empresa = $id_empresa ";
    $q_remito = $this->db->query($sql);
    $row = $q_remito->row();
    $numero_remito = $row->numero;

    // INSERTAMOS LAS CUOTAS
    $k=0;
    foreach($cuotas as $im) {

      // Insertamos la cuota
      $this->db->query("INSERT INTO inm_alquileres_cuotas (id_empresa,id_alquiler,monto,numero,vencimiento,pagada,corresponde_a) VALUES($id_empresa,$insert_id,'$im->monto','$im->numero','$im->vencimiento','$im->pagada','$im->corresponde_a')");
      $id_cuota = $this->db->insert_id();

      // Creamos el remito
      $numero_remito++;
      $comprobante = "R 0001-".str_pad($numero_remito, 8, "0", STR_PAD_LEFT);
      $hash = md5($id_empresa.$comprobante);

      // Estas variables:
      //   id_referencia = id_alquiler
      //   numero_referencia = numero de cuota
      // Sirven al momento del pago, poder identificar que cuota se esta pagando
      $sql = "INSERT INTO facturas (";
      $sql.= " id_empresa, fecha, hora, punto_venta, numero, comprobante, ";
      $sql.= " id_cliente, id_tipo_comprobante, total, subtotal, ";
      $sql.= " tipo_pago, estado, hash, id_referencia, numero_referencia ";
      $sql.= ") VALUES (";
      $sql.= " '$id_empresa', '$hoy', '$hora', 1, $numero_remito, '$comprobante', ";
      $sql.= " '$array->id_cliente', '999', $im->monto, $im->monto,  ";
      $sql.= " 'C',1,'$hash', $insert_id, $im->numero ";
      $sql.= ")";
      $this->db->query($sql);

      $k++;
    }

    // Al cliente lo marcamos como inquilino
    $this->db->query("UPDATE clientes SET custom_4 = '1' WHERE id_empresa = $id_empresa AND id = '$array->id_cliente' ");

    // INSERTAMOS LAS EXPENSAS
    $k=0;
    foreach($expensas as $im) {
      $this->db->query("INSERT INTO inm_alquileres_expensas (id_empresa,id_alquiler,monto,nombre,orden) VALUES($id_empresa,$insert_id,'$im->monto','$im->nombre','$k')");
      $k++;
    }
        
    $salida = array(
      "id"=>$insert_id,
      "error"=>0,
    );
    echo json_encode($salida);        
  }
    
  function get($id) {
    $id_empresa = parent::get_empresa();
    // Obtenemos el listado
    if ($id == "index") {
      $this->ver();
    } else {
      $publicidad = $this->modelo->get($id);
      echo json_encode($publicidad);
    }   
  }

  function listado_recibos() {

    $id_empresa = parent::get_empresa();
    $filter = $this->input->get("filter");
    $filter = urldecode($filter);
    $mes = $this->input->get("mes");
    $anio = $this->input->get("anio");
    $limit = $this->input->get("limit");
    $offset = $this->input->get("offset");
    $order_by = $this->input->get("order_by");
    $order = $this->input->get("order");
    $order_by = (empty($order_by)) ? "C.nombre ASC " : $order_by." ".$order;

    $id_propiedad = ($this->input->get("id_propiedad") === FALSE) ? 0 : $this->input->get("id_propiedad");
    $estado = ($this->input->get("estado") === FALSE) ? -1 : $this->input->get("estado");

    $sql = "SELECT SQL_CALC_FOUND_ROWS C.nombre AS cliente, F.id, F.pago, C.telefono, C.celular, F.hash, F.id_punto_venta, ";
    $sql.= " F.comprobante, C.id AS id_cliente, DATE_FORMAT(F.fecha,'%d/%m/%Y') AS fecha, ";
    $sql.= " AC.pagada, AC.corresponde_a, A.id AS id_alquiler, ";
    $sql.= " DATE_FORMAT(AC.vencimiento,'%d/%m/%Y') AS vencimiento, AC.monto, AC.expensa, (AC.monto+AC.expensa) AS total, ";
    $sql.= " P.nombre AS propiedad, CONCAT(P.calle,' ',P.altura,' ',P.piso,' ',P.numero) AS direccion ";
    $sql.= "FROM facturas F ";
    $sql.= "INNER JOIN inm_alquileres A ON (A.id = F.id_referencia AND A.id_empresa = F.id_empresa) ";
    $sql.= "INNER JOIN inm_alquileres_cuotas AC ON (F.numero_referencia = AC.numero AND AC.id_alquiler = A.id AND F.id_empresa = AC.id_empresa) ";        
    $sql.= "INNER JOIN clientes C ON (C.id = A.id_cliente AND F.id_empresa = C.id_empresa) ";
    $sql.= "INNER JOIN inm_propiedades P ON (P.id = A.id_propiedad AND F.id_empresa = P.id_empresa) ";
    $sql.= "WHERE F.id_empresa = $id_empresa ";
    if (!empty($id_propiedad)) $sql.= "AND A.id_propiedad = $id_propiedad ";
    if ($estado != -1) $sql.= "AND AC.pagada = $estado ";
    if (!empty($filter)) $sql.= "AND C.nombre LIKE '%$filter%' ";
    if (!empty($mes) && !empty($anio)) $sql.= "AND AC.corresponde_a = '$mes $anio' ";
    if (!empty($order_by)) $sql.= "ORDER BY $order_by ";
    if (!empty($offset)) $sql.= "LIMIT $limit, $offset ";
    $q = $this->db->query($sql);

    $q_total = $this->db->query("SELECT FOUND_ROWS() AS total");
    $total = $q_total->row();

    echo json_encode(array(
      "results"=>$q->result(),
      "total"=>$total->total,
    ));
  }

  function imprimir($id_factura) {

    $id_empresa = parent::get_empresa();
    $this->load->helper("fecha_helper");
    $this->load->helper("numero_letra_helper");

    $sql = "SELECT C.nombre AS cliente, F.id, F.pago, C.cuit AS cuit, F.id_referencia AS id_alquiler, ";
    $sql.= " F.comprobante, C.id AS id_cliente, DATE_FORMAT(F.fecha,'%d/%m/%Y') AS fecha, ";
    $sql.= " AC.pagada, AC.corresponde_a, ";
    $sql.= " DATE_FORMAT(AC.vencimiento,'%d/%m/%Y') AS vencimiento, AC.monto, AC.expensa, (AC.monto+AC.expensa) AS total, ";
    $sql.= " P.nombre AS propiedad, CONCAT(P.calle,' ',P.altura,' ',P.piso,' ',P.numero) AS direccion, ";
    $sql.= " IF(PRO.nombre IS NULL,'',PRO.nombre) AS propietario ";
    $sql.= "FROM facturas F ";
    $sql.= "INNER JOIN inm_alquileres A ON (A.id = F.id_referencia AND F.id_empresa = A.id_empresa) ";
    $sql.= "INNER JOIN inm_alquileres_cuotas AC ON (F.numero_referencia = AC.numero AND AC.id_alquiler = A.id AND AC.id_empresa = F.id_empresa) ";        
    $sql.= "INNER JOIN clientes C ON (C.id = A.id_cliente AND F.id_empresa = C.id_empresa) ";
    $sql.= "INNER JOIN inm_propiedades P ON (P.id = A.id_propiedad AND F.id_empresa = P.id_empresa) ";
    $sql.= "LEFT JOIN inm_propietarios PRO ON (P.id_propietario = PRO.id AND F.id_empresa = PRO.id_empresa) ";
    $sql.= "WHERE F.id = $id_factura ";
    $sql.= "AND F.id_empresa = $id_empresa ";
    $q = $this->db->query($sql);
    $factura = $q->row();

    $sql = "SELECT * FROM inm_alquileres_expensas ";
    $sql.= "WHERE id_alquiler = $factura->id_alquiler ";
    $sql.= "AND id_empresa = $id_empresa ";
    $sql.= "ORDER BY orden ASC ";
    $q_items = $this->db->query($sql);
    $factura->items = $q_items->result();
    
    $this->load->model("Empresa_Model");
    $empresa = $this->Empresa_Model->get($id_empresa);
    
    $header = $this->load->view("reports/factura/header",null,true);
    
    $datos = array(
      "facturas"=>array($factura),
      "empresa"=>$empresa,
      "header"=>$header,
      "letras"=> new EnLetras(),
    );
    $this->load->view("reports/factura/recibo_inmo/remito.php",$datos);
  }

  function cupon_pago($id_empresa,$id_factura,$id_punto_venta) {

    $this->load->helper("fecha_helper");
    $this->load->helper("numero_letra_helper");

    //echo "ERROR EN EL SERVIDOR"; exit();

    $sql = "SELECT C.nombre AS cliente, C.email, C.telefono AS cliente_telefono, F.id_cliente, F.id_empresa, ";
    $sql.= " F.id, F.pago, F.numero, C.cuit AS cuit, F.id_referencia AS id_alquiler, ";
    $sql.= " F.comprobante, C.id AS id_cliente, DATE_FORMAT(F.fecha,'%d/%m/%Y') AS fecha, ";
    $sql.= " AC.pagada, AC.corresponde_a, ";
    $sql.= " DATE_FORMAT(AC.vencimiento,'%d/%m/%Y') AS vencimiento, AC.monto, AC.expensa, (AC.monto+AC.expensa) AS total, ";
    $sql.= " P.nombre AS propiedad, CONCAT(P.calle,' ',P.altura,' ',P.piso,' ',P.numero) AS direccion, ";
    $sql.= " IF(PRO.nombre IS NULL,'',PRO.nombre) AS propietario ";
    $sql.= "FROM facturas F ";
    $sql.= "INNER JOIN inm_alquileres A ON (A.id = F.id_referencia AND F.id_empresa = A.id_empresa) ";
    $sql.= "INNER JOIN inm_alquileres_cuotas AC ON (F.numero_referencia = AC.numero AND AC.id_alquiler = A.id AND AC.id_empresa = F.id_empresa) ";        
    $sql.= "INNER JOIN clientes C ON (C.id = A.id_cliente AND F.id_empresa = C.id_empresa) ";
    $sql.= "INNER JOIN inm_propiedades P ON (P.id = A.id_propiedad AND F.id_empresa = P.id_empresa) ";
    $sql.= "LEFT JOIN inm_propietarios PRO ON (P.id_propietario = PRO.id AND F.id_empresa = PRO.id_empresa) ";
    $sql.= "WHERE F.id_empresa = '$id_empresa' ";
    $sql.= "AND F.id = '$id_factura' ";
    $sql.= "AND F.id_punto_venta = '$id_punto_venta' ";
    $q = $this->db->query($sql);
    $factura = $q->row();
    if (empty($factura)) {
      echo "El comprobante no es valido.";
      exit();
    }

    $sql = "SELECT * FROM inm_alquileres_expensas ";
    $sql.= "WHERE id_alquiler = $factura->id_alquiler ";
    $sql.= "AND id_empresa = $factura->id_empresa ";
    $sql.= "ORDER BY orden ASC ";
    $q_items = $this->db->query($sql);
    $factura->items = $q_items->result();
    
    $this->load->model("Empresa_Model");
    $empresa = $this->Empresa_Model->get($factura->id_empresa);
    
    $header = $this->load->view("reports/propiedad/header",null,true);

    // Obtenemos la configuracion de MP y el objeto (si fue configurado)
    $mp = FALSE;
    $preference_data = FALSE;
    require_once("../models/mercadopago.php");
    $q = $this->db->query("SELECT * FROM medios_pago_configuracion WHERE id_empresa = $empresa->id ");
    if ($q->num_rows()>0) {
      $medio = $q->row();
      if ($medio->habilitar_mp == 1) {
        // La configuracion de las dos cuentas esta separada por ;;;
        // Dependiendo del carrito, tomamos una u otra configuracion
        $clients_id = explode(";;;", $medio->mp_client_id);
        $clients_secret = explode(";;;", $medio->mp_client_secret);

        // Dependiendo de cual carrito estamos haciendo el checkout
        $mp_client_id = trim($clients_id[0]);
        $mp_client_secret = trim($clients_secret[0]);
        if (!empty($mp_client_id) && !empty($mp_client_secret)) {

          // Creamos el objeto de MercadoPago
          $mp = new MP($mp_client_id, $mp_client_secret); 

          // Creamos el objeto de preferencia
          $items = array();
          $items[] = array(
            "id"=>0,
            "title"=>"Alquiler ".$factura->direccion,
            "currency_id"=>"ARS",
            "quantity"=>1,
            "unit_price"=>$factura->total+0,
          );            
          $current_url = "https://www.varcreative.com/sistema/alquileres/function/cupon_pago/".$id_empresa."/".$factura->id."/".$factura->id_punto_venta."/";
          $preference_data = array(
            "items" => $items,
            "payer" => array(
              "name" => $factura->cliente,
              "email" => $factura->email,
            ),
            "back_urls" => array(
              "success" => $current_url,
              "failure" => $current_url,
              "pending" => $current_url,
            ),
            "auto_return" => "all",
            "notification_url" => "https://www.varcreative.com/ipn_alquiler.php",
            "external_reference" => $factura->id."_".$factura->id_empresa."_".$factura->id_punto_venta,
          );
        }
      }
    }    
    
    $datos = array(
      "folder"=>"/sistema/application/views/reports/factura/cupon_pago/blue",
      "facturas"=>array($factura),
      "empresa"=>$empresa,
      "header"=>$header,
      "letras"=> new EnLetras(),
      "mp"=>$mp,
      "preference_data"=>$preference_data,
    );
    $this->load->view("reports/factura/cupon_pago/remito.php",$datos);
  }

  function borrar_recibo($id = null) {
    $id_empresa = parent::get_empresa();
    $corresponde_a = $this->input->post("corresponde_a");
    $id_alquiler = $this->input->post("id_alquiler");
    $this->db->query("UPDATE cheques SET fecha_recibido = '0000-00-00', id_recibo = 0 WHERE id_empresa = $id_empresa AND id_recibo = $id");
    $this->db->query("DELETE FROM depositos WHERE id_recibo = $id AND id_empresa = $id_empresa");
    $this->db->query("DELETE FROM facturas_pagos WHERE id_pago = $id AND id_empresa = $id_empresa");
    $this->db->query("DELETE FROM cupones_tarjetas WHERE id_factura = $id AND id_empresa = $id_empresa");
    $this->db->query("UPDATE facturas SET pagada = 0, pago = 0 WHERE id = $id AND id_empresa = $id_empresa ");
    $this->db->query("UPDATE inm_alquileres_cuotas SET pagada = 0 WHERE id_alquiler = $id_alquiler AND id_empresa = $id_empresa AND corresponde_a = '$corresponde_a' ");
    echo json_encode(array());
  }
    
    
  /**
   *  Muestra todos los publicidades filtrando segun distintos parametros
   *  El resultado esta paginado
   */
  function ver() {
    $filter = $this->input->get("filter");
    $limit = $this->input->get("limit");
    $offset = $this->input->get("offset");
    $order_by = $this->input->get("order_by");
    $estado = ($this->input->get("estado") === FALSE) ? "A" : $this->input->get("estado");
    $id_cliente = ($this->input->get("id_cliente") === FALSE) ? 0 : $this->input->get("id_cliente");
    $id_propiedad = ($this->input->get("id_propiedad") === FALSE) ? 0 : $this->input->get("id_propiedad");
    $order = $this->input->get("order");
    if (!empty($order_by) && !empty($order)) $order = $order_by." ".$order;
    else $order = "";
    
    $conf = array(
      "filter"=>$filter,
      "order"=>$order,
      "id_cliente"=>$id_cliente,
      "id_propiedad"=>$id_propiedad,
      "estado"=>$estado,
      "limit"=>$limit,
      "offset"=>$offset,
    );
    $r = $this->modelo->buscar($conf);
    echo json_encode($r);
  }
    
}
