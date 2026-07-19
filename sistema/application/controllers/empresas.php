<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Empresas extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Empresa_Model', 'modelo');
  }

  function export($id_empresa = 0) {
    if ($id_empresa == 0) { echo gzdeflate("0"); exit(); }
    $sql = "SELECT A.* ";
    $sql.= "FROM empresas A ";
    $sql.= "WHERE A.id = $id_empresa ";

    $q = $this->db->query($sql);
    if ($q->num_rows() == 0) { echo gzdeflate("0"); exit(); }

    $this->load->helper("import_helper");
    $salida = create_string_to_export($q);
    
    // Enviamos la cadena comprimida para ahorrar ancho de banda
    echo gzdeflate($salida);
  }  

  // Controla los vencimientos de las empresas:
  // Si la empresa vence el dia de hoy, y nunca se genero una factura periodica anteriormente
  // crea la primer factura periodica y envia el email
  // (el resto de los emails se envian desde la facturacion periodica)
  function controlar_vencimientos() {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    $id_empresa_varcreative = 936;
    $this->load->model("Email_Template_Model");
    $this->load->model("Factura_Model");
    $this->load->model("Log_Model");
    $hoy = date("Y-m-d");
    $bcc_array = array("basile.matias99@gmail.com","misticastudio@gmail.com");
    include_once APPPATH.'libraries/Mandrill/Mandrill.php';

    // Consultamos las empresas que vencen hoy
    $sql = "SELECT E.*, P.id_articulo, PRO.nombre AS proyecto ";
    $sql.= "FROM empresas E INNER JOIN planes P ON (E.id_plan = P.id) ";
    $sql.= "INNER JOIN com_proyectos PRO ON (E.id_proyecto = PRO.id) ";
    $sql.= "where E.fecha_prox_venc = '$hoy' AND E.administrar_pagos = 1";
    $q = $this->db->query($sql);
    foreach($q->result() as $r) {
      // Si la empresa todavia no fue nunca facturada
      $sql = "SELECT * FROM facturas WHERE id_empresa = $id_empresa_varcreative AND id_cliente = $r->id ";
      $qq = $this->db->query($sql);
      if ($qq->num_rows() == 0) {
        // Creamos la primera factura
        $s = $this->Factura_Model->crear(array(
          "id_empresa"=>$id_empresa_varcreative,
          "es_periodica"=>1,
          "id_cliente"=>$r->id,
          "items"=>array(
            array("id_articulo"=>$r->id_articulo),
          )
        ));
        if ($s["error"] == 1) {
          $this->Log_Model->imprimir(array(
            "id_empresa"=>$id_empresa_varcreative,
            "id_usuario"=>0,
            "file"=>"controlar_vencimientos.txt",
            "texto"=>"Error al generar la factura $r->nombre ",
          ));
          continue;
        }
        $this->Log_Model->imprimir(array(
          "id_empresa"=>$id_empresa_varcreative,
          "id_usuario"=>0,
          "file"=>"controlar_vencimientos.txt",
          "texto"=>"Genero factura $r->nombre: ".$s["id_factura"]
        ));
        $template = $this->Email_Template_Model->get_by_proyecto("emision-factura",$r->id_proyecto);
        $body = $template->texto;
        $body = str_replace("{{email}}",$r->email,$body);
        $nombre = htmlentities($r->nombre,ENT_QUOTES);
        $nombre = ucwords(strtolower($nombre));
        $body = str_replace("{{nombre}}",$nombre,$body);
        $body = str_replace("{{link_factura}}", "https://www.varcreative.com/sistema/facturas/function/ver_pdf/".$s["id_factura"]."/".$s["id_punto_venta"]."/".$s["id_empresa"]."/", $body);

        if (strpos($body, "{{preference}}")) {
          $this->load->model("Medio_Pago_Configuracion_Model");
          $preference = $this->Medio_Pago_Configuracion_Model->create_preference_mp(array(
            "id_empresa"=>$id_empresa_varcreative,
            "id_factura"=>$s["id_factura"],
            "id_punto_venta"=>$s["id_punto_venta"],
            "titulo"=>$s["comprobante"],
            "monto"=>($s["total"] + 0),
            "email"=>$r->email,
          ));
          if (!empty($preference)) $body = str_replace("{{preference}}", $preference["response"]["init_point"], $body);
        }

        mandrill_send(array(
          "to"=>$r->email,
          "from"=>MAIL_FROM_ADDRESS,
          "from_name"=>$r->proyecto,
          "subject"=>$template->nombre,
          "body"=>$body,
          "reply_to"=>MAIL_REPLY_TO_ADDRESS,
          "bcc"=>$bcc_array,
        ));
      }
    }

    // CUENTAS QUE ESTAN POR VENCER (5 DIAS ANTES DE LA SUSPENSION)
    $d = new DateTime();
    $d->modify("+5 days");
    $this->modelo->enviar_recordatorios_pagos(array(
      "fecha"=>$d->format("Y-m-d"),
      "clave_template"=>"cuenta-por-vencer",
    ));

    // CUENTAS QUE ESTAN POR VENCER (1 DIA ANTES DE LA SUSPENSION)
    $d = new DateTime();
    $d->modify("+1 day");
    $this->modelo->enviar_recordatorios_pagos(array(
      "fecha"=>$d->format("Y-m-d"),
      "clave_template"=>"cuenta-por-vencer",
    ));

    // CUENTAS QUE VENCEN HOY
    $d = new DateTime();
    $this->modelo->enviar_recordatorios_pagos(array(
      "fecha"=>$d->format("Y-m-d"),
      "clave_template"=>"cuenta-por-vencer",
    ));

    // CUENTAS QUE VENCIERON AYER
    $d = new DateTime();
    $d->modify("-1 day");
    $this->modelo->enviar_recordatorios_pagos(array(
      "fecha"=>$d->format("Y-m-d"),
      "clave_template"=>"cuenta-vencida",
    ));

    echo "Termino";
  }

  // Cambia el plan de una empresa determinada
  function cambiar_plan() {
    $id_empresa = parent::get_empresa();
    $id_plan = parent::get_post("id_plan",0);
    if (!is_numeric($id_plan) || $id_plan == 0) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"El parametro id_plan es incorrecto.",
      ));
      exit();
    }

    $empresa = $this->modelo->get_min($id_empresa);

    // Cambiamos el campo en la base de datos
    $sql = "UPDATE empresas SET id_plan = $id_plan WHERE id = $id_empresa ";
    $this->db->query($sql);

    // Enviamos un nuevo email
    $this->load->model("Email_Template_Model");
    $template = $this->Email_Template_Model->get_by_proyecto("cambio-plan",$empresa->id_proyecto);

    $bcc_array = array("basile.matias99@gmail.com","misticastudio@gmail.com");
    require APPPATH.'libraries/Mandrill/Mandrill.php';
    $body = (html_entity_decode($template->texto,ENT_QUOTES));
    $body = str_replace("{{email}}",$empresa->email,$body);
    $body = str_replace("{{nombre}}",htmlentities($empresa->nombre,ENT_QUOTES),$body);
    mandrill_send(array(
      "to"=>$empresa->email,
      "from"=>MAIL_FROM_ADDRESS,
      "from_name"=>$empresa->proyecto,
      "subject"=>$template->nombre,
      "body"=>$body,
      "reply_to"=>MAIL_REPLY_TO_ADDRESS,
      "bcc"=>$bcc_array,
    ));

    echo json_encode(array(
      "error"=>0,
      "mensaje"=>"Hemos actualizado tu plan correctamente. Muchas gracias!",
    ));
  }

  function arreglar_editores() {
    $this->load->helper("file_helper");
    $sql = "SELECT * FROM empresas ";
    $q = $this->db->query($sql);
    foreach($q->result() as $e) {
      // Copiamos todos los archivos del editor a la carpeta de la empresa
      if (!file_exists("uploads/$e->id/editor")) copy_all("uploads/editor","uploads/$e->id/editor");
    }
    echo "TERMINO";
  }

  function get_by_descripcion() {
    $resultado = array();
    $id_perfil = $_SESSION["perfil"];
    if ($id_perfil == -1) {    
      $descripcion = $this->input->get("term");
      $sql = "SELECT A.* ";
      $sql.= "FROM empresas A ";
      $sql.= "WHERE A.nombre LIKE '%$descripcion%' OR A.razon_social LIKE '%$descripcion%' OR A.id = '$descripcion' ";
      $sql.= "LIMIT 0,20 ";
      $q = $this->db->query($sql);
      foreach($q->result() as $r) {
        $rr = new stdClass();
        $rr->id = $r->id;
        $rr->value = $r->id;
        $rr->label = $r->nombre." (ID: ".$r->id.")";
        $rr->path = $r->path;
        $resultado[] = $rr;
      }
    }
    echo json_encode($resultado);
  }  

  function asignar_vendedores() {
    $id_adrian = 32;
    $id_matias = 1636;
    $id_marcelo = 920;
    $id_proyecto = 14;
    $sql = "SELECT E.* FROM empresas E LEFT JOIN empresas_vendedores EV ON (EV.id_empresa = E.id) ";
    $sql.= "WHERE EV.id_usuario IS NULL AND E.id_proyecto = $id_proyecto ";
    $q = $this->db->query($sql);
    foreach($q->result() as $r) {
      $mitad = $r->costo / 2;
      $this->db->query("INSERT INTO empresas_vendedores (id_empresa,id_usuario,monto) VALUES ($r->id,$id_marcelo,$mitad)");
      $this->db->query("INSERT INTO empresas_vendedores (id_empresa,id_usuario,monto) VALUES ($r->id,$id_matias,$mitad)");
      echo "ACTUALIZO $r->nombre <br/>";
    }
    echo "TERMINO";
  }

  // Pasa todas las empresas como clientes de la cuenta 99. MATIAS BASILE
  function pasar_empresas_clientes() {
    $this->load->helper("file_helper");
    $q = $this->db->query("SELECT * FROM empresas");
    foreach($q->result() as $r) {
      $sql = "INSERT INTO clientes (";
      $sql.= " id, id_empresa, tipo, nombre, email, codigo, activo, fecha_inicial, id_tipo_iva, cuit, direccion, id_localidad, telefono ";
      $sql.= ") VALUES (";
      $sql.= " '$r->id', 99, 0, '$r->razon_social', '$r->email', '$r->id', 1, NOW(), '$r->id_tipo_contribuyente', '$r->cuit', '$r->direccion', '$r->id_localidad', '$r->telefono' ";
      $sql.= ")";
      $this->db->query($sql);
    }
    echo "TERMINO";
  }

  function acomodar_links() {
    $this->load->helper("file_helper");
    $q = $this->db->query("SELECT * FROM empresas");
    foreach($q->result() as $r) {
      $r->link = filename($r->nombre,"-",0);
      $this->db->query("UPDATE empresas SET link = '$r->link' WHERE id = $r->id ");
    }
    echo "TERMINO";
  }

  function get_datos_cuenta() {

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    $this->load->model("Empresa_Model");

    $id_empresa = parent::get_empresa();
    $id_usuario = parent::get_post("id_usuario",0);

    // Obtenemos las ultimas facturas de ese usuario
    $this->load->model("Venta_Model");
    $ventas = $this->Venta_Model->listado(array(
      "id_empresa"=>$id_empresa,
      "id_usuario"=>$id_usuario,
      "limit"=>0,
      "offset"=>10,
      "estado"=>1,
    ));

    $stripe_public_key = "pk_test_51HH7OLGB6qOnjLuYHr7IpqDoKgaGZzTsBWFcUPuSnYIMgHK3p2uqlybgMKOSp88ttVt2B0SOD9Ogfj1Todygf8tS00l17hyKpz";
    $stripe_secret_key = "sk_test_51HH7OLGB6qOnjLuYvXnqTuXoWcBpIW9wMQ4Xt0tf9QDxqplbOXEcEoeEbOp1YPCj3HRa8WdNTYyG0FDGgADyOpK300MARUshkp";

    $comprobantes = array();
    foreach($ventas["results"] as $r) {
      $r->preference = "";
      /*
      if ($r->total > 0) {

        // Obtenemos el primer articulo que marca el plan
        $r->articulo = "";
        $sql = "SELECT * FROM facturas_items WHERE id_empresa = $r->id_empresa AND id_factura = $r->id AND id_punto_venta = $r->id_punto_venta ORDER BY orden ASC LIMIT 0,1 ";
        $qq = $this->db->query($sql);
        if ($qq->num_rows() > 0) {
          $rr = $qq->row();
          $r->articulo = $rr->nombre;
        }

        // Si la factura no esta paga, anexamos MP para pagarla
        if ($r->pagada == 0) {
          // TODO: Stripe          
        }
      }*/
      $comprobantes[] = $r;
    }

    // El saldo siempre es de la ultima factura
    $saldo = 0;
    if (sizeof($comprobantes)>0) {
      $ultima = $comprobantes[0];
      $saldo = $ultima->total;
    }

    echo json_encode(array(
      "saldo"=>$saldo,
      "comprobantes"=>$comprobantes,
    ));
  }

  function dominio_varcreative() {
    $this->load->helper("file_helper");
    $q = $this->db->query("SELECT * FROM empresas");
    foreach($q->result() as $r) {
      //$dominio_varcreative = "www.varcreative.com/sandbox/".filename($r->razon_social,"-",0)."/";
      $dominio_varcreative = "www.varcreative.com/sandbox/".$r->id."/";
      $this->db->query("UPDATE empresas SET dominio_varcreative = '$dominio_varcreative' WHERE id = $r->id ");
    }
    echo "TERMINO";
  }

  function registro_turnoclick() {

    header('Access-Control-Allow-Origin: *');
    $nombre = ($this->input->post("nombre") !== FALSE ? $this->input->post("nombre") : "");
    $nombre_empresa = ($this->input->post("nombre_empresa") !== FALSE ? $this->input->post("nombre_empresa") : "");
    $telefono = ($this->input->post("telefono") !== FALSE ? $this->input->post("telefono") : "");
    $email = $this->input->post("email");
    $password = ($this->input->post("password") !== FALSE ? $this->input->post("password") : "");
    $id_plan = ($this->input->post("id_plan") !== FALSE ? $this->input->post("id_plan") : 10); // 10 = SMALL
    $proyecto = 17;
    $id_web = 1000;

    // Guardamos en un log los datos enviados
    file_put_contents("registro_turnoclick.txt", date("Y-m-d H:i:s")."\n".print_r($_POST,true)."\n\n", FILE_APPEND);

    // Primero de todo controlamos que el email no exista en otra empresa
    $e = $this->modelo->get_empresa_by_email($email);
    if ($e !== FALSE) {
      echo json_encode(array(
        "mensaje"=>"El email $email ya esta usado por otra cuenta. Por favor ingrese uno distinto.",
        "error"=>1,
      ));
      exit();
    }

    if (!($id_plan == 10 || $id_plan == 11 || $id_plan == 12)) $id_plan = 10;
    $sql = "SELECT * FROM planes WHERE id = $id_plan AND id_proyecto = $proyecto ";
    $q_plan = $this->db->query($sql);
    $plan = $q_plan->row();

    // Enviamos el email al usuario
    $sql = "SELECT * FROM crm_emails_templates WHERE id_empresa = $id_web AND clave = 'registro' ";
    $q = $this->db->query($sql);
    if ($q->num_rows()>0) {
      $template = $q->row();

      $bcc_array = array("basile.matias99@gmail.com","misticastudio@gmail.com");
      require APPPATH.'libraries/Mandrill/Mandrill.php';
      $body = (html_entity_decode($template->texto,ENT_QUOTES));
      $body = str_replace("{{email}}",$email,$body);
      $body = str_replace("{{nombre}}",htmlentities($nombre,ENT_QUOTES),$body);
      $body = str_replace("{{password}}",$password,$body);
      mandrill_send(array(
        "to"=>$email,
        "from"=>MAIL_FROM_ADDRESS,
        "from_name"=>"TurnoClick",
        "subject"=>$template->nombre,
        "body"=>$body,
        "reply_to"=>MAIL_REPLY_TO_ADDRESS,
        "bcc"=>$bcc_array,
      ));
    }

    $obj = new stdClass();
    $obj->nombre = $nombre;
    $obj->razon_social = (!empty($nombre_empresa) ? $nombre_empresa : $nombre);
    $obj->id_proyecto = 17;
    $obj->id_tipo_contribuyente = 4;
    $obj->email = $email;
    $obj->configuracion_menu = 1;
    $obj->configuracion_menu_iconos = 1;
    $obj->telefono_empresa = $telefono;
    $obj->password = $password;
    $obj->dominios = $sitio;
    $obj->estado_empresa = 1; // A contactar
    $obj->administrar_pagos = 1;
    $obj->periodo_fact = "+1 month";
    $obj->fecha_prox_venc = date("Y-m-d",strtotime("+15 days"));
    $obj->fecha_suspension = date("Y-m-d",strtotime("+30 days"));
    $obj->costo = (isset($plan->precio_anual)) ? $plan->precio_anual : 0;
    $obj->limite = (isset($plan->limite_articulos) ? $plan->limite_articulos : 0);
    $obj->id_plan = $id_plan;

    $res = $this->modelo->insert($obj);

    echo json_encode(array(
      "error"=>0,
    ));
  }


  function registro_shopvar() {

    header('Access-Control-Allow-Origin: *');
    $nombre = ($this->input->post("nombre") !== FALSE ? $this->input->post("nombre") : "");
    $empresa = parent::get_post("empresa","");
    $sitio = ($this->input->post("sitio") !== FALSE ? $this->input->post("sitio") : "");
    $email = $this->input->post("email");
    $password = ($this->input->post("password") !== FALSE ? $this->input->post("password") : "");
    $id_plan = ($this->input->post("id_plan") !== FALSE ? $this->input->post("id_plan") : 8); // 8 = BASICO
    $laplataconstruye = parent::get_post("laplataconstruye",0);
    $casaclick = parent::get_post("casaclick",0);
    $telefono = parent::get_post("telefono","");
    $id_proyecto = 2; // ID del PROYECTO
    $id_web = (($laplataconstruye == 1) ? 708 : 119); // ID de la empresa SHOPVAR
    $nombre_proyecto = (($laplataconstruye == 1) ? "La Plata Construye" : "Shopvar");
    $reply_to = (($laplataconstruye == 1) ? "info@laplataconstruye.com" : "info@varcreative.com");

    file_put_contents("registro_shopvar.txt", date("Y-m-d H:i:s")."\n".print_r($_POST,true)."\n\n", FILE_APPEND);

    // Primero de todo controlamos que el email no exista en otra empresa
    $e = $this->modelo->get_empresa_by_email($email);
    if ($e !== FALSE) {
      echo json_encode(array(
        "mensaje"=>"El email $email ya esta usado por otra cuenta. Por favor ingrese uno distinto.",
        "error"=>1,
      ));
      exit();
    }

    if (!empty($sitio)) {
      $sitio = strtolower($sitio);
      $sitio = str_replace("http://", "", $sitio);
      $sitio = str_replace("https://", "", $sitio);
      $sitio = str_replace("www.", "", $sitio);
      $sitio = ((strpos($sitio, "/") !== FALSE) ? substr($sitio, 0, strpos($sitio, "/")-1) : $sitio);
      $sitio = $sitio.";;;www.".$sitio; // Lo agregamos con y sin www
    }

    //if (!($id_plan == 8 || $id_plan == 11 || $id_plan == 12)) $id_plan = 8;
    if (!($id_plan == 8)) $id_plan = 8;
    $sql = "SELECT * FROM planes WHERE id = $id_plan AND id_proyecto = $id_proyecto ";
    $q_plan = $this->db->query($sql);
    $plan = $q_plan->row();

    // Enviamos el email al usuario
    $this->load->model("Email_Template_Model");
    $template = $this->Email_Template_Model->get_by_key('registro',$id_web);
    if (!empty($template)) {
      $bcc_array = array("basile.matias99@gmail.com","misticastudio@gmail.com");
      if ($laplataconstruye == 1) $bcc_array[] = "leonardo.rica@gmail.com";
      require APPPATH.'libraries/Mandrill/Mandrill.php';
      $body = $template->texto;
      $body = str_replace("{{email}}",$email,$body);
      $body = str_replace("{{nombre}}",htmlentities($nombre,ENT_QUOTES),$body);
      $body = str_replace("{{password}}",htmlentities($password,ENT_QUOTES),$body);
      $body = str_replace("{{telefono}}",htmlentities($telefono,ENT_QUOTES),$body);
      mandrill_send(array(
        "to"=>$email,
        "from"=>MAIL_FROM_ADDRESS,
        "from_name"=>$nombre_proyecto,
        "subject"=>$template->nombre,
        "body"=>$body,
        "reply_to"=>$reply_to,
        "bcc"=>$bcc_array,
      ));
    }

    $obj = new stdClass();
    $obj->nombre = $nombre;
    $obj->razon_social = (!empty($empresa) ? $empresa : $nombre);
    $obj->id_proyecto = $id_proyecto;
    $obj->id_tipo_contribuyente = 4;
    $obj->email = $email;
    $obj->configuracion_menu = 1;
    $obj->configuracion_menu_iconos = 1;
    $obj->telefono_empresa = $telefono;
    $obj->password = $password;
    $obj->dominios = $sitio;
    $obj->administrar_pagos = 1;
    $obj->estado_empresa = 1; // A contactar
    $obj->periodo_fact = "+1 month";
    $obj->fecha_prox_venc = date("Y-m-d",strtotime("+15 days"));
    $obj->fecha_suspension = date("Y-m-d",strtotime("+30 days"));
    $obj->costo = (isset($plan->precio_anual)) ? $plan->precio_anual : 0;
    $obj->limite = (isset($plan->limite_articulos) ? $plan->limite_articulos : 0);
    $obj->id_plan = $id_plan;
    $obj->estado_inicial = 1;
    $obj->laplataconstruye = $laplataconstruye;

    if ($casaclick == 1) {
      $marcelo = new stdClass();
      $marcelo->id_usuario = 920;
      $marcelo->monto = 0;
      $cc = new stdClass();
      $cc->id_usuario = 2138;
      $cc->monto = 0;
      $obj->vendedores = array($cc,$marcelo);
    }

    $res = $this->modelo->insert($obj);

    echo json_encode(array(
      "error"=>0,
    ));
  }


  function registro_inmovar() {

    header('Access-Control-Allow-Origin: *');
    $nombre = ($this->input->post("nombre") !== FALSE ? $this->input->post("nombre") : "");
    $nombre_inmobiliaria = ($this->input->post("nombre_inmobiliaria") !== FALSE ? $this->input->post("nombre_inmobiliaria") : "");
    $telefono = ($this->input->post("telefono") !== FALSE ? $this->input->post("telefono") : "");
    $email = $this->input->post("email");
    $password = ($this->input->post("password") !== FALSE ? $this->input->post("password") : "");
    $id_plan = ($this->input->post("id_plan") !== FALSE ? $this->input->post("id_plan") : 9); // 9 = INICIAL
    $casaclick = parent::get_post("casaclick",0);
    $proyecto = 3;
    $id_web = 270;

    // Si estamos usando reCAPTCHA
    $captcha = $this->input->post("g-recaptcha-response");
    if ($captcha !== FALSE) {
      require APPPATH.'libraries/recaptchalib.php';
      $site_key = "6LeHSTQUAAAAAA5FV121v-M7rnhqdkXZIGmP9N8E";
      $secret = "6LeHSTQUAAAAACG9dCyy6hv24tlRYL8TKtxe4O54";
      $reCaptcha = new ReCaptcha($secret);
      $resp = $reCaptcha->verifyResponse(
        $_SERVER["REMOTE_ADDR"],
        $captcha
      );
      if ($resp == null || !isset($resp->success) || $resp->success === FALSE) {
        $salida = array(
          "mensaje"=>"El codigo de validacion es incorrecto.",
          "error"=>1,
        );
        echo json_encode($salida);
        exit();
      }
    }

    file_put_contents("registro_inmovar.txt", date("Y-m-d H:i:s")."\n".print_r($_POST,true)."\n\n", FILE_APPEND);

    // Primero de todo controlamos que el email no exista en otra empresa
    $e = $this->modelo->get_empresa_by_email($email);
    if ($e !== FALSE) {
      echo json_encode(array(
        "mensaje"=>"El email $email ya esta usado por otra cuenta. Por favor ingrese uno distinto.",
        "error"=>1,
      ));
      exit();
    }

    /*
    if (empty($_POST) || empty($nombre)) {
      $salida = array(
        "mensaje"=>"ERROR: datos de registro insuficientes.",
        "error"=>1,
      );
      echo json_encode($salida);
      exit();
    }
    */

    if (!($id_plan == 9 || $id_plan == 13 || $id_plan == 14)) $id_plan = 9;
    $sql = "SELECT * FROM planes WHERE id = $id_plan AND id_proyecto = $proyecto ";
    $q_plan = $this->db->query($sql);
    $plan = $q_plan->row();

    // Enviamos el email al usuario
    $this->load->model("Email_Template_Model");
    $template = $this->Email_Template_Model->get_by_key('registro',118);
    if (!empty($template)) {
      $bcc_array = array("basile.matias99@gmail.com","misticastudio@gmail.com");
      require APPPATH.'libraries/Mandrill/Mandrill.php';
      $body = $template->texto;
      $body = str_replace("{{email}}",$email,$body);
      $body = str_replace("{{nombre}}",htmlentities($nombre,ENT_QUOTES),$body);
      $body = str_replace("{{password}}",htmlentities($password,ENT_QUOTES),$body);
      $body = str_replace("{{telefono}}",htmlentities($telefono,ENT_QUOTES),$body);
      mandrill_send(array(
        "to"=>$email,
        "from"=>MAIL_FROM_ADDRESS,
        "from_name"=>"Inmovar",
        "subject"=>$template->nombre,
        "body"=>$body,
        "reply_to"=>MAIL_REPLY_TO_ADDRESS,
        "bcc"=>$bcc_array,
      ));
    }

    $obj = new stdClass();
    $obj->nombre = $nombre;
    $obj->razon_social = (!empty($nombre_inmobiliaria)) ? $nombre_inmobiliaria : $nombre;
    $obj->id_proyecto = 3;
    $obj->id_tipo_contribuyente = 4;
    $obj->email = $email;
    $obj->configuracion_menu = 1;
    $obj->configuracion_menu_iconos = 1;
    $obj->telefono_empresa = $telefono;
    $obj->password = $password;
    $obj->administrar_pagos = 1;
    $obj->estado_empresa = 1; // A contactar
    $obj->periodo_fact = "+1 month";
    $obj->fecha_prox_venc = date("Y-m-d",strtotime("+15 days"));
    $obj->fecha_suspension = date("Y-m-d",strtotime("+15 days"));
    $obj->costo = (isset($plan->precio_anual)) ? $plan->precio_anual : 0;
    $obj->limite = (isset($plan->limite_articulos) ? $plan->limite_articulos : 0);
    $obj->id_plan = $id_plan;

    if ($casaclick == 1) {
      $marcelo = new stdClass();
      $marcelo->id_usuario = 920;
      $marcelo->monto = 0;
      $cc = new stdClass();
      $cc->id_usuario = 2138;
      $cc->monto = 0;
      $obj->vendedores = array($cc,$marcelo);
    }

    $res = $this->modelo->insert($obj);

    // Creamos una nueva tarea en background para que pueda ejecutarse
    $this->load->model("Tarea_Background_Model");
    $this->Tarea_Background_Model->guardar(array(
      "url"=>"https://www.varcreative.com/sistema/permisos_red/function/notificar/".$res["id"]."/",
    ));

    echo json_encode(array(
      "error"=>0,
    ));
  }


  function registro_clienapp() {

    header('Access-Control-Allow-Origin: *');
    $nombre = ($this->input->post("nombre") !== FALSE ? $this->input->post("nombre") : "");
    $sitio = ($this->input->post("sitio") !== FALSE ? $this->input->post("sitio") : "");
    $email = $this->input->post("email");
    $password = ($this->input->post("password") !== FALSE ? $this->input->post("password") : "");
    $id_plan = ($this->input->post("id_plan") !== FALSE ? $this->input->post("id_plan") : 10); // 10 = SMALL
    $proyecto = 14;
    $id_web = 290;

    $sitio = strtolower($sitio);
    $sitio = str_replace("http://", "", $sitio);
    $sitio = str_replace("https://", "", $sitio);
    $sitio = str_replace("www.", "", $sitio);
    $sitio = ((strpos($sitio, "/") !== FALSE) ? substr($sitio, 0, strpos($sitio, "/")-1) : $sitio);
    $sitio = $sitio.";;;www.".$sitio; // Lo agregamos con y sin www

    file_put_contents("registro_clienapp.txt", date("Y-m-d H:i:s")."\n".print_r($_POST,true)."\n\n", FILE_APPEND);

    // Primero de todo controlamos que el email no exista en otra empresa
    $e = $this->modelo->get_empresa_by_email($email);
    if ($e !== FALSE) {
      echo json_encode(array(
        "mensaje"=>"El email $email ya esta usado por otra cuenta. Por favor ingrese uno distinto.",
        "error"=>1,
      ));
      exit();
    }

    if (!($id_plan == 10 || $id_plan == 11 || $id_plan == 12)) $id_plan = 10;
    $sql = "SELECT * FROM planes WHERE id = $id_plan AND id_proyecto = $proyecto ";
    $q_plan = $this->db->query($sql);
    $plan = $q_plan->row();

    // Enviamos el email al usuario
    $sql = "SELECT * FROM crm_emails_templates WHERE id_empresa = $id_web AND clave = 'registro' ";
    $q = $this->db->query($sql);
    if ($q->num_rows()>0) {
      $template = $q->row();

      $bcc_array = array("basile.matias99@gmail.com","misticastudio@gmail.com");
      require APPPATH.'libraries/Mandrill/Mandrill.php';
      $body = (html_entity_decode($template->texto,ENT_QUOTES));
      $body = str_replace("{{email}}",$email,$body);
      $body = str_replace("{{nombre}}",htmlentities($nombre,ENT_QUOTES),$body);
      $body = str_replace("{{password}}",$password,$body);
      mandrill_send(array(
        "to"=>$email,
        "from"=>MAIL_FROM_ADDRESS,
        "from_name"=>"ClienApp",
        "subject"=>$template->nombre,
        "body"=>$body,
        "reply_to"=>MAIL_REPLY_TO_ADDRESS,
        "bcc"=>$bcc_array,
      ));
    }

    $obj = new stdClass();
    $obj->nombre = $nombre;
    $obj->razon_social = $nombre;
    $obj->id_proyecto = 14;
    $obj->id_tipo_contribuyente = 4;
    $obj->email = $email;
    $obj->configuracion_menu = 1;
    $obj->configuracion_menu_iconos = 1;
    $obj->telefono_empresa = "";
    $obj->password = $password;
    $obj->dominios = $sitio;
    $obj->estado_empresa = 1; // A contactar
    $obj->administrar_pagos = 1;
    $obj->periodo_fact = "+1 month";
    $obj->fecha_prox_venc = date("Y-m-d",strtotime("+15 days"));
    $obj->fecha_suspension = date("Y-m-d",strtotime("+30 days"));
    $obj->costo = (isset($plan->precio_anual)) ? $plan->precio_anual : 0;
    $obj->limite = (isset($plan->limite_articulos) ? $plan->limite_articulos : 0);
    $obj->id_plan = $id_plan;

    $res = $this->modelo->insert($obj);

    echo json_encode(array(
      "error"=>0,
    ));
  }

  function guardar_dominio() {
    $id_empresa = parent::get_empresa();
    $dominio = $this->input->post("dominio");
    if (empty($dominio)) {
      echo json_encode(array(
        "error"=>1,
      ));
      exit();
    }
    $dominio = str_replace("http://", "", $dominio);
    $dominio = str_replace("https://", "", $dominio);
    $dominio = str_replace("www.", "", $dominio);
    $dominio = ((strpos($dominio, "/") !== FALSE) ? substr($dominio, 0, strpos($dominio, "/")-1) : $dominio);
    // Primero buscamos si ya existe el dominio en otra cuenta
    $sql = "SELECT * FROM empresas_dominios WHERE dominio = '$dominio' AND id_empresa != $id_empresa";
    $q = $this->db->query($sql);
    if ($q->num_rows()>0) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"El dominio $dominio ya esta registrado en otra cuenta.",
      ));
      exit();
    }
    $this->db->query("DELETE FROM empresas_dominios WHERE id_empresa = $id_empresa ");
    $this->db->query("INSERT INTO empresas_dominios (id_empresa,dominio) VALUES ('$id_empresa','$dominio')");  
    $dominio = "www.".$dominio; // Lo agregamos con y sin www
    $this->db->query("INSERT INTO empresas_dominios (id_empresa,dominio) VALUES ('$id_empresa','$dominio')");  
    echo json_encode(array(
      "error"=>0,
    ));
  }

  function registro() {

    $nombre = ($this->input->post("nombre") !== FALSE ? $this->input->post("nombre") : "");
    $empresa = ($this->input->post("empresa") !== FALSE ? $this->input->post("empresa") : "");
    $email = $this->input->post("email");
    $telefono = ($this->input->post("telefono") !== FALSE ? $this->input->post("telefono") : "");
    $password = ($this->input->post("password") !== FALSE ? $this->input->post("password") : "");
    $proyecto = $this->input->post("proyecto");
    $id_web = $this->input->post("id_web");
    $id_empresa = 0; // Para identificar que todavia no hizo click en el email de registro

    // En el apellido guardamos la empresa
    // En el id_perfiles guardamos el proyecto
    $fecha = date("Y-m-d H:i:s");
    $sql = "INSERT INTO com_usuarios (";
    $sql.= " nombre_usuario, nombre, apellido, fecha_alta, telefono, email, activo, password, id_perfiles ";
    $sql.= ") VALUES (";
    $sql.= " '$nombre', '$nombre', '$empresa', '$fecha', '$telefono', '$email', 0, '$password', '$proyecto' ";
    $sql.= ")";
    $this->db->query($sql);

    $id = $this->db->insert_id();

    // Enviamos el email al usuario
    $sql = "SELECT * FROM crm_emails_templates WHERE id_empresa = $id_web AND clave = 'registro' ";
    $q = $this->db->query($sql);
    if ($q->num_rows()>0) {
      $template = $q->row();

      $headers = "MIME-Version: 1.0\r\n";
      $headers.= "From: ".MAIL_FROM_ADDRESS."\r\n";
      $headers.= "Content-Type: text/html; charset=ISO-8859-1\r\n";
      $headers.= "BCC: basile.matias99@gmail.com,misticastudio@gmail.com\r\n";
      $body = (html_entity_decode($template->texto,ENT_QUOTES));
      $body = str_replace("{{email}}",$email,$body);
      $body = str_replace("{{nombre}}",$nombre,$body);
      $body = str_replace("{{id}}",$id,$body);
      @mail($email,$template->nombre,$body,$headers);
    }

    echo json_encode(array(
      "error"=>0,
    ));
  }

  // Con esta funcion creamos la empresa
  function fin_registro($id_usuario = 0) {

    if ($id_usuario == 0) return;
    $sql = "SELECT * FROM com_usuarios WHERE id_empresa = 0 AND id = $id_usuario AND activo = 0 ";
    $q = $this->db->query($sql);
    if ($q->num_rows()<=0) {
      // No hay un usuario, redireccionamos
      header("Location: http://www.varcreative.com/sistema/");
    }
    $usuario = $q->row();
    
    $obj = new stdClass();
    $obj->crear_usuario = FALSE;
    $obj->nombre = $usuario->apellido;
    $obj->razon_social = $usuario->apellido;
    $obj->id_proyecto = $usuario->id_perfiles;
    $obj->id_tipo_contribuyente = 1;
    $obj->email = $usuario->email;
    $obj->configuracion_menu = 1;
    $obj->configuracion_menu_iconos = 1;
    $obj->telefono_empresa = $usuario->telefono;

    if ($obj->id_proyecto == 2) $obj->id_web_template = 15; // SHOPVAR
    else if ($obj->id_proyecto == 3) $obj->id_web_template = 33; // INMOVAR

    // Dominio de prueba
    $this->load->helper("file_helper");
    $obj->dominios = "www.varcreative.com/sandbox/".filename($obj->razon_social,"-",0)."/";

    $res = $this->modelo->insert($obj);

    // Finalmente redireccionamos para que entre con su usuario
    header("Location: https://www.varcreative.com/sistema/");
  }

  public function get_modulos($id_empresa) {
    $this->load->model("Modulo_Model");
    $arr = $this->Modulo_Model->get_by_empresa($id_empresa);
    echo json_encode($arr);
  }

  function save_image($dir="",$filename="") {
    $registro = $this->input->post("registro");
    if ($registro === FALSE) {
      $id_empresa = $this->get_empresa();
      $dir = "uploads/$id_empresa/images";  
    } else $dir = "uploads/images";
    $filename = $this->input->post("file");
    echo parent::save_image($dir,$filename);
  }  

  function exportar($id_empresa,$solo_datos=1) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    set_time_limit(0);
    $perfil = $_SESSION["perfil"];
    if ($perfil != -1) {
      echo "No tiene permisos para ejecutar esta accion.";
      exit();
    }
    $this->load->dbutil();
    $tablas = $this->db->list_tables();
    $salida = "";
    // Recorremos las tablas
    foreach($tablas as $t) {
      // Si la tabla tiene el campo id_empresa
      $dump = "mysqldump -u ".USER_DB." -p".PASSWORD_DB." ";
      if ($solo_datos == 1) $dump.= "--no-create-info --skip-triggers ";
      $dump.= "--no-create-db ";
      $dump.= "--insert-ignore ";
      if ($this->db->field_exists("id_empresa",$t)) $dump.= "--where=\"id_empresa=$id_empresa OR id_empresa = 0\" ";
      $dump.= DATABASE." $t";
      $salida .= shell_exec($dump)."\n\n";
    }
    if (!empty($salida)) {
      $filename = "backup-" . $id_empresa."-".date("d-m-Y") . ".sql";
      header("Content-Type: text/plain");
      header('Content-Disposition: attachment; filename="' . $filename . '"');
      echo $salida;
      exit(0);        
    } else {
      echo "No se encontraron resultados";
    }
  }

    
  // SOLAMENTE EL SUPERADMIN PUEDE HACER ESTA OPERACION
  function delete($id = null,$exec = 1)  {
    $perfil = $_SESSION["perfil"];
    if ($perfil == -1) {
      $tablas = $this->db->list_tables();
      // Recorremos las tablas
      foreach($tablas as $t) {
      // Si la tabla tiene el campo id_empresa
        if ($this->db->field_exists("id_empresa",$t)) {
        // Ejecutamos la consulta
          $sql = "DELETE FROM $t WHERE id_empresa = $id";
          if ($exec) $this->db->query($sql);
          else echo $sql.";<br/>";
        }
      }
      // Finalmente eliminamos el registro de la tabla empresas
      $sql = "DELETE FROM empresas WHERE id = $id";
      if ($exec) $this->db->query($sql);
      else echo $sql.";<br/>";

      if ($exec == 1) {
        // Eliminamos la carpeta y todos sus archivos
        $dir = "uploads/$id";
        if (file_exists($dir)) {
          $it = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
          $files = new RecursiveIteratorIterator($it,RecursiveIteratorIterator::CHILD_FIRST);
          foreach($files as $file) {
            if ($file->isDir()){
              @rmdir($file->getRealPath());
            } else {
              @unlink($file->getRealPath());
            }
          }
          @rmdir($dir);                
        }

        // Eliminamos el cliente dentro de la cuenta de VARCREATIVE
        $this->db->query("DELETE FROM clientes WHERE id_empresa = 936 AND id = $id ");
      }
    }
    echo json_encode(array("error"=>0));
  }

  // FUNCION QUE BORRA TODAS LAS EMPRESAS MENOS LA INDICADA
  function borrar_empresas($id) {
    $perfil = $_SESSION["perfil"];
    if ($perfil == -1) {
      $tablas = $this->db->list_tables();
      // Recorremos las tablas
      foreach($tablas as $t) {
        // Si la tabla tiene el campo id_empresa
        if ($this->db->field_exists("id_empresa",$t)) {
          $sql = "DELETE FROM $t WHERE id_empresa != $id";
          echo $sql.";<br/>";
        }
      }
      // Finalmente eliminamos el registro de la tabla empresas
      $sql = "DELETE FROM empresas WHERE id != $id";
      echo $sql.";<br/>";
    }
  }

  // SOLAMENTE EL SUPERADMIN PUEDE HACER ESTA OPERACION
  function update_id($id_anterior = 0, $id_nuevo = 0)  {
    $perfil = $_SESSION["perfil"];
    if ($perfil == -1) {
      $tablas = $this->db->list_tables();
      // Recorremos las tablas
      foreach($tablas as $t) {
        // Si la tabla tiene el campo id_empresa
        if ($this->db->field_exists("id_empresa",$t)) {
          // Ejecutamos la consulta
          $this->db->query("UPDATE $t SET id_empresa = $id_nuevo WHERE id_empresa = $id_anterior");
        }
      }
      // Finalmente eliminamos el registro de la tabla empresas
      $this->db->query("UPDATE empresas SET id = $id_nuevo WHERE id = $id_anterior");
    }
    echo json_encode(array("error"=>0));
  }

  // SOLAMENTE EL SUPERADMIN PUEDE HACER ESTA OPERACION
  function export_sql($id_empresa)  {
    $perfil = $_SESSION["perfil"];
    if ($perfil == -1) {
      $tablas = $this->db->list_tables();
      $t2 = array();
      // Recorremos las tablas
      foreach($tablas as $t) {
        // Si la tabla tiene el campo id_empresa
        if ($this->db->field_exists("id_empresa",$t)) {
          // Si la tabla tiene registros de esa empresa
          $sql = "SELECT * FROM $t WHERE id_empresa = $id_empresa ";
          $q = $this->db->query($sql);
          if ($q->num_rows()>0) $t2[] = $t;
        }
      }

      // Recorremos solo las tablas que tienen informacion
      foreach($t2 as $t) {
        $sql = "DELETE FROM $t WHERE id_empresa = $id_empresa;";
        echo $sql."<br/>";
      }
      echo "<br/><br/><br/><br/>";
      echo "rfNu8Wj4f5f9<br/><br/>";

      // Recorremos solo las tablas que tienen informacion
      $tables_s = implode(" ",$t2);
      echo "mysqldump -u servidor -p92voNUsiru --databases servidor --tables $tables_s --where=\"id_empresa = $id_empresa\" --skip-add-drop-table --no-create-info > /home/vw000181/public_html/sistema/uploads/$id_empresa/backup.sql ";
    }
  }

  // SOLAMENTE EL SUPERADMIN PUEDE HACER ESTA OPERACION
  function exportar_configuracion($id_empresa)  {
    $perfil = $_SESSION["perfil"];
    if ($perfil == -1) {
      $solo_datos = 1;
      $limpiar = 1;
      $tablas = array(
        "empresas",
        "fact_configuracion",
        "puntos_venta",
        "numeros_comprobantes",
        "com_usuarios",
        "com_perfiles",
        "com_permisos_modulos",
        "com_modulos_empresas",
        "web_configuracion",
        "env_configuracion",
        "almacenes",
        "almacenes_puntos_venta",
      );
      // Recorremos las tablas
      $salida = "";

      foreach($tablas as $t) {
        $salida.= "DELETE FROM $t;\n\n";
      }
      foreach($tablas as $t) {
        // Si la tabla tiene el campo id_empresa
        $dump = "mysqldump -u ".USER_DB." -p".PASSWORD_DB." ";
        if ($solo_datos == 1) $dump.= "--no-create-info --skip-triggers ";
        $dump.= "--no-create-db --skip-add-locks --skip-comments ";
        if ($t == "empresas") $dump.= "--where=\"id=$id_empresa\" ";
        else if ($this->db->field_exists("id_empresa",$t)) $dump.= "--where=\"id_empresa=$id_empresa OR id_empresa = 0\" ";
        $dump.= DATABASE." $t";
        $salida .= shell_exec($dump)."\n\n";
      }
      if (!empty($salida)) {
        $filename = $id_empresa.".sql";
        header("Content-Type: text/plain");
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        echo $salida;
        exit(0);        
      } else {
        echo "No se encontraron resultados";
      }      
    }
  }  

  function get($id) {
    $id_empresa = parent::get_empresa();
    if ($id == "index") {

      $limit = $this->input->get("limit");
      $offset = $this->input->get("offset");
      $filter = $this->input->get("filter");
      $id_proyecto = $this->input->get("id_proyecto");
      $order_by = $this->input->get("order_by");
      $order = $this->input->get("order");
      $id_usuario = parent::get_get("id_usuario",0);
      $estado_empresa = (($this->input->get("estado_empresa") !== FALSE) ? $this->input->get("estado_empresa") : -1);

      $lista = $this->modelo->buscar(array(
        "filter"=>$filter,
        "id_proyecto"=>$id_proyecto,
        "limit"=>$limit,
        "offset"=>$offset,
        "id_usuario"=>$id_usuario,
        "estado_empresa"=>$estado_empresa,
      ));
      $total = $this->modelo->count_all();

      $salida = array(
        "total"=> $total,
        "results"=>$lista
        );
      echo json_encode($salida);
    } 
    else echo json_encode($this->modelo->get($id));
  }

}