<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Consultas extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Consulta_Model', 'modelo');
  }

  function mostrar_siempre() {
    $id_cliente = parent::get_post("id_cliente", 0);
    $id_empresa = parent::get_post("id_empresa", parent::get_empresa()); 

    $sql = "UPDATE crm_consultas ";
    $sql.= "SET ";
    $sql.= "custom_1 = 'mostrar_siempre' ";
    $sql.= "WHERE id_empresa = '$id_empresa' ";
    $sql.= "AND id_contacto = '$id_cliente' ";
    $q = $this->db->query($sql);

    echo json_encode(array(
      "error"=>0,
    ));
  }

  // Este proceso se ejecuta cada tantos minutos:
  // - Analiza el correo de "respuestas.varcreative@gmail.com"
  // - Procesa todos los correos no leidos y los transforma a consultas
  function procesar() {

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    date_default_timezone_set("America/Argentina/Buenos_Aires");
    set_time_limit(0);    

    // A traves de un archivo, controlamos que no se ejecuten dos veces el mismo proceso
    /*
    $filename = "consultas_email.txt";
    if (file_exists($filename) === FALSE) file_put_contents($filename, "");
    $file = fopen($filename, "r+");
    if (flock($file, LOCK_EX | LOCK_NB) === FALSE) {  // Intenta adquirir un bloqueo exclusivo
      // Si falla es porque el proceso sigue activo
      exit();
    }
    */

    $connection = imap_open('{c1040339.ferozo.com:993/imap/ssl}INBOX', 'info@grupo-urbano.com.ar', 'Leonel235') or die('Cannot connect to Gmail: ' . imap_last_error());
    $emailData = imap_search($connection, 'ALL');
    //$emailData = imap_search($connection, 'ALL'); //Toma emails no leidos !!!!CAMBIAR ALL POR UNSEEN
    if (empty($emailData)) return;

    foreach ($emailData as $emailIdent) { //Leer emails
      $i=0;
      $overview = imap_fetch_overview($connection, $emailIdent, 0);
      $message = imap_fetchbody($connection, $emailIdent, '1');
      //$messageExcerpt = substr($message, 0, 300); Por si se quiere mostrar X caracteres

      // Datos de los usuarios
      $text = trim(quoted_printable_decode($message)); 
      $fecha = date("Y-m-d H:i:s", strtotime($overview[$i]->date));
      $titulo = $overview[$i]->subject;
      $from = $overview[$i]->from;
      if (strstr($from, "<")) {
        //Si el mail no tiene nombre de usuario quito las <, si no lo muestro tal como es
        $from = strstr($from, "<");
        $from=str_replace("<", "", $from);
        $from=str_replace(">", "", $from);
      }
      $to = $overview[$i]->to;

      echo "FROM: $from \n";
      echo "FECHA: $fecha \n";
      echo "TO: $to \n";
      echo "TITULO: $titulo \n";
      //echo "TEXT: $text \n";
      echo "--------\n";
    }
  }


  // USADO EN CRM/CONSULTAS
  function editar_usuario_asignado() {

    $id_empresa = parent::get_empresa();
    $ids = parent::get_post("ids");
    $ids = explode(",", $ids);
    $id_contacto = parent::get_post("id_contacto");
    // Usuario que realizo la operacion
    $id_usuario = parent::get_post("id_usuario");
    // Nuevo usuario asignado al cliente
    $id_usuario_asignado = parent::get_post("id_usuario_asignado");

    $bcc_array = array("basile.matias99@gmail.com");
    require APPPATH.'libraries/Mandrill/Mandrill.php';

    $this->load->model("Empresa_Model");
    $this->load->model("Email_Template_Model");
    $this->load->model("Usuario_Model");
    $this->load->model("Consulta_Model");
    $template = $this->Email_Template_Model->get_by_key("asignacion-usuario",936);
    $empresa = $this->Empresa_Model->get_min($id_empresa);

    // Primero obtenemos el usuario nuevo
    $usuario_asignado = $this->Usuario_Model->get($id_usuario_asignado,array(
      "id_empresa"=>$id_empresa,
    ));
    if ($usuario_asignado === FALSE) {
      echo json_encode(array("error"=>1,"mensaje"=>"No existe el usuario solicitado"));
      exit();
    }

    $usuario = $this->Usuario_Model->get($id_usuario,array(
      "id_empresa"=>$id_empresa,
    ));

    foreach($ids as $id) {

      // Movemos todas las consultas del contacto hacia el nuevo usuario
      $sql = "UPDATE crm_consultas SET id_usuario = '$id_usuario_asignado' WHERE id_empresa = $id_empresa AND id_contacto = $id_contacto ";
      $q = $this->db->query($sql);

      // Creamos un nuevo movimiento en el historial de ese cliente
      if ($id_usuario_asignado != $id_usuario) {
        // Otro asigno
        $texto = $usuario->nombre." ha asignado a ".$usuario_asignado->nombre." para atender la consulta.";  
      } else {
        // Me asigne yo mismo
        $texto = $usuario->nombre." se asigno la consulta.";
      }
      
      $sql = "INSERT INTO crm_consultas (id_contacto,id_empresa,fecha,asunto,texto,id_usuario,tipo,id_origen) VALUES (";
      $sql.= " $id_contacto,$id_empresa,NOW(),'Asignacion de usuario','$texto',$id_usuario,0,32) ";
      $this->db->query($sql);

      $web_conf = $this->Empresa_Model->get_web_conf($id_empresa);
      if ($web_conf->crm_notificar_asignaciones_usuarios == 1 && $id_usuario != $id_usuario_asignado)  {

        $consulta = $this->Consulta_Model->get($id);

        $asunto = ($usuario->nombre)." te asigno un nuevo contacto!";
        $texto = $template->texto;
        $texto = str_replace("{{nombre}}", ($usuario_asignado->nombre), $texto);
        $cuerpo = "<b>Cliente: </b> ".$consulta->nombre." <br/>";
        $cuerpo.= "<b>Email: </b> ".$consulta->email." <br/>";
        $cuerpo.= "<b>Telefono: </b> ".$consulta->telefono." <br/>";
        //$cuerpo.= "<b>ID Consulta: </b> #".$consulta->id." <br/>";
        $texto = str_replace("{{cuerpo}}", $cuerpo, $texto);

        mandrill_send(array(
          "to"=>$usuario_asignado->email,
          "from"=>MAIL_FROM_ADDRESS,
          "from_name"=>$empresa->nombre,
          "subject"=>$asunto,
          "body"=>$texto,
          "bcc"=>$bcc_array,
        ));
      }
    }

    echo json_encode(array("error"=>0));
  }  

  // Registra cuando el usuario hace click en el email de carrito abandonado
  // Se le envia un email al administrador avisando
  function aviso_carrito_abandonado($id_empresa,$id_cliente) {
    header('Access-Control-Allow-Origin: *');

    $this->load->model("Empresa_Model");
    $this->load->model("Cliente_Model");

    $empresa = $this->Empresa_Model->get($id_empresa);
    $cliente = $this->Cliente_Model->get($id_cliente,$id_empresa);

    $fecha = date("Y-m-d H:i:s");
    $asunto = "Interesado en productos";
    $texto = "$cliente->nombre ha vuelto a abrir el carrito luego de hacer click en el email.";
    $consulta = array(
      "id_empresa"=>$id_empresa,
      "fecha"=>$fecha,
      "asunto"=>$asunto,
      "texto"=>$texto,
      "id_contacto"=>$id_cliente,
      "id_origen"=>21, // EMAIL AUTOMATICO
    );
    $this->modelo->insert($consulta);

    $bcc_array = array("basile.matias99@gmail.com");
    require APPPATH.'libraries/Mandrill/Mandrill.php';
    mandrill_send(array(
      "to"=>$empresa->email,
      "from"=>MAIL_FROM_ADDRESS,
      "from_name"=>$empresa->nombre,
      "subject"=>$asunto,
      "body"=>$texto,
      "reply_to"=>$email,
      "bcc"=>$bcc_array,
    ));
    header("Location: http://www.grupoanacleto.com.ar/carrito/");
  }

  // Utilizado en ARGENCASH / crm / consultas.js / guardar_tarea()
  function guardar_tarea() {
    $id_empresa = parent::get_empresa();
    $id_contacto = parent::get_post("id_contacto",0);
    $fecha = parent::get_post("fecha",date("Y-m-d"));
    $fecha_visto = parent::get_post("fecha_visto",date("Y-m-d H:i:s"));
    $hora = parent::get_post("hora",date("H:i:s"));
    $asunto = parent::get_post("asunto","");
    $texto = parent::get_post("texto","");
    $id_origen = parent::get_post("id_origen",0);
    $id_usuario = parent::get_post("id_usuario",0);
    $tipo = parent::get_post("tipo",0);
    $id_asunto = parent::get_post("id_asunto",0);
    $estado = parent::get_post("estado",0);
    $sql = "INSERT INTO crm_consultas ( ";
    $sql.= " id_contacto,id_empresa,fecha,asunto, ";
    $sql.= " texto,id_origen,id_usuario,tipo,id_asunto,estado,fecha_visto ";
    $sql.= ") VALUES (";
    $sql.= " '$id_contacto','$id_empresa','$fecha $hora','$asunto', ";
    $sql.= " '$texto','$id_origen','$id_usuario','$tipo','$id_asunto','$estado','$fecha_visto' ";
    $sql.= ")";
    file_put_contents("consulta_insertar.txt", date("Y-m-d H:i:s")." - ".$sql."\n", FILE_APPEND);
    $this->db->query($sql);
    echo json_encode(array(
      "error"=>0,
    ));
  }
  
  function ver() {
    $limit = $this->input->get("limit");
    $offset = $this->input->get("offset");
    $filter = $this->input->get("filter");
    $order_by = $this->input->get("order_by");
    $order = $this->input->get("order");
    $id_origen = $this->input->get("id_origen");
    $id_usuario = ($this->input->get("id_usuario") === FALSE) ? 0 : $this->input->get("id_usuario");
    $id_origenes = $this->input->get("id_origenes");
    if (!empty($order_by) && !empty($order)) $order = $order_by." ".$order;
    else $order = "";
    
    $conf = array(
      "filter"=>$filter,
      "limit"=>$limit,
      "offset"=>$offset,
      "order"=>$order,
      "id_origen"=>$id_origen,
      "id_usuario"=>$id_usuario,
      "id_origenes"=>$id_origenes,
      );
    $r = $this->modelo->buscar($conf);
    echo json_encode($r);    
  }
  
  function enviar() {

    header('Access-Control-Allow-Origin: *');

    // Honeypot: campo trampa que solo un bot completaria.
    // Respondemos como si hubiera salido bien, sin guardar nada ni enviar mail.
    $hp = $this->input->post("hp");
    if (!empty($hp)) {
      echo json_encode(array(
        "error"=>0,
      ));
      exit();
    }

    // Verificacion de reCAPTCHA contra la API de Google
    require_once APPPATH.'libraries/recaptchalib.php';
    $captcha = $this->input->post("g-recaptcha-response");
    $reCaptcha = new ReCaptcha(RECAPTCHA_SECRET_KEY);
    $resp = $reCaptcha->verifyResponse(
      $_SERVER["REMOTE_ADDR"],
      $captcha
    );
    if ($resp == null || !isset($resp->success) || $resp->success === FALSE) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"Por favor verifique el captcha e intente nuevamente.",
      ));
      exit();
    }

    $this->load->model("Cliente_Model");
    $this->load->model("Email_Template_Model");

    $nombre = ($this->input->post("nombre") === FALSE) ? "" : htmlentities($this->input->post("nombre"),ENT_QUOTES);
    $apellido = ($this->input->post("apellido") === FALSE) ? "" : htmlentities($this->input->post("apellido"),ENT_QUOTES);
    if ($apellido !== FALSE) $nombre = $nombre." ".$apellido;
    $nombre = trim(ucwords(strtolower($nombre)));
    $email = ($this->input->post("email") === FALSE) ? "" : htmlentities($this->input->post("email"),ENT_QUOTES);
    $mensaje = ($this->input->post("mensaje") === FALSE) ? "" : htmlentities($this->input->post("mensaje"),ENT_QUOTES);
    $asunto = ($this->input->post("asunto") === FALSE) ? "" : htmlentities($this->input->post("asunto"),ENT_QUOTES);
    $subtitulo = ($this->input->post("subtitulo") === FALSE) ? "" : htmlentities($this->input->post("subtitulo"),ENT_QUOTES);

    $telefono = ($this->input->post("telefono") === FALSE) ? "" : htmlentities($this->input->post("telefono"),ENT_QUOTES);
    $celular = ($this->input->post("celular") === FALSE) ? "" : htmlentities($this->input->post("celular"),ENT_QUOTES);
    $prefijo = ($this->input->post("prefijo") === FALSE) ? "549" : htmlentities($this->input->post("prefijo"),ENT_QUOTES);

    $direccion = $this->input->post("direccion");
    if ($direccion === FALSE) $direccion = "";
    $ciudad = $this->input->post("ciudad");
    if ($ciudad === FALSE) $ciudad = "";
    $id_localidad = $this->input->post("id_localidad");
    if ($id_localidad === FALSE) $id_localidad = 0;
    $id_propiedad = $this->input->post("id_propiedad");
    if ($id_propiedad === FALSE) $id_propiedad = 0;
    $id_viaje = $this->input->post("id_viaje");
    if ($id_viaje === FALSE) $id_viaje = 0;
    $id_auto = $this->input->post("id_auto");
    if ($id_auto === FALSE) $id_auto = 0;
    $id_articulo = $this->input->post("id_articulo");
    if ($id_articulo === FALSE) $id_articulo = 0;
    $id_entrada = $this->input->post("id_entrada");
    if ($id_entrada === FALSE) $id_entrada = 0;
    $id_origen = $this->input->post("id_origen");
    if ($id_origen === FALSE) $id_origen = 9;
    $id_usuario = $this->input->post("id_usuario");
    if ($id_usuario === FALSE) $id_usuario = 0;
    $no_actualizar_fecha = $this->input->post("no_actualizar_fecha");
    if ($no_actualizar_fecha === FALSE) $no_actualizar_fecha = 0;
    $para = $this->input->post("para");
    if ($para === FALSE) $para = "";
    $bcc = $this->input->post("bcc");
    if ($bcc === FALSE) $bcc = "";
    $custom = $this->input->post("custom");
    if ($custom === FALSE) $custom = "";
    $testing = $this->input->post("testing");
    if ($testing === FALSE) $testing = 0;
    // Template enviado al mismo cliente
    $template = $this->input->post("template");
    if ($template === FALSE) $template = "";

    $canal = $this->input->post("canal");
    if ($canal === FALSE) $canal = "";

    $nombre_profesional = "";
    if ($id_usuario != 0) {
      $this->load->model("Usuario_Model");
      $row_usuario = $this->Usuario_Model->get($id_usuario);
      $nombre_profesional = $row_usuario->nombre;
    }

    // Este es un parametro especial que se usa para el CHAT
    // Como en el registro antes de enviar un whatsapp no se pide email,
    // mandamos este parametro para que busque y enlace el contacto por el telefono
    // ya que el telefono es obligatorio
    $buscar_telefono = parent::get_post("buscar_telefono",0);

    // En caso de tener que crear una nueva entrada 
    $crear_entrada = parent::get_post("crear_entrada",0);
    $entrada_titulo = parent::get_post("entrada_titulo","");
    $entrada_subtitulo = parent::get_post("entrada_subtitulo","");
    $entrada_id_categoria = parent::get_post("entrada_id_categoria",0);
    $entrada_ids_etiquetas = parent::get_post("entrada_ids_etiquetas",0);
    $entrada_id_pais = parent::get_post("entrada_id_pais",0);
    $entrada_activo = parent::get_post("entrada_activo",0);
    $entrada_base_link = parent::get_post("entrada_base_link","");
    $entrada_direccion = parent::get_post("entrada_direccion","");
    $entrada_localidad = parent::get_post("entrada_localidad","");
    $entrada_custom_1 = parent::get_post("entrada_custom_1","");
    $entrada_custom_2 = parent::get_post("entrada_custom_2","");
    $entrada_custom_3 = parent::get_post("entrada_custom_3","");
    $entrada_custom_4 = parent::get_post("entrada_custom_4","");
    $entrada_custom_5 = parent::get_post("entrada_custom_5","");
    $entrada_custom_6 = parent::get_post("entrada_custom_6","");
    $entrada_custom_7 = parent::get_post("entrada_custom_7","");
    $entrada_custom_8 = parent::get_post("entrada_custom_8","");
    $entrada_custom_9 = parent::get_post("entrada_custom_9","");
    $entrada_custom_10 = parent::get_post("entrada_custom_10","");
    $entrada_fecha = parent::get_post("entrada_fecha",date("Y-m-d H:i:s"));

    $link_ficha_propiedad = parent::get_post("link_ficha_propiedad","");

    // 1 = Contacto por defecto
    $tipo = ($this->input->post("tipo") !== FALSE) ? ((int) $this->input->post("tipo")) : 1;
    // 1 = Manda solo el campo mensaje
    $solo_mensaje = ($this->input->post("solo_mensaje") !== FALSE) ? ((int) $this->input->post("solo_mensaje")) : 0;

    // Nota: la verificacion de reCAPTCHA para este endpoint ya se hace
    // al principio de enviar(), antes de llegar hasta aca.

    // Si no esta definida la empresa, tiene que estar si o si el parametro DOMINIO
    $id_empresa = $this->input->post("id_empresa");
    if ($id_empresa === FALSE) {

      $dominio = $this->input->post("dominio");
      if ($dominio === FALSE) {
        echo json_encode(array(
          "error"=>1,
          "mensaje"=>"Falta el parametro DOMINIO",
        ));
        return;
      }
      // Buscamos la empresa por el dominio que fue enviado por parametro
      $this->load->model("Empresa_Model");
      $empresa = $this->Empresa_Model->get_empresa_by_dominio($dominio);
      if ($empresa === FALSE) {
        echo json_encode(array(
          "error"=>1,
          "mensaje"=>"No existe la cuenta de empresa con dominio $dominio.",
        ));
        return;
      }
      $empresa->nombre = ucwords(strtolower($empresa->nombre));

      // TODO: Controlar que la empresa esta al dia con los pagos
      // TODO: Controlar que la empresa tiene habilitado el CHATBOT

      // Parametros especificos del CHAT
      $id_empresa = $empresa->id;
      $asunto = "Contacto desde chat";
      $testing = 1; // Por ahora el chat esta a prueba
      $para = $empresa->email;
      $bcc = $empresa->bcc_email;

    } else {

      // Si es una web nuestra, los emails son obligatorios
      /*
      if (empty($email)) {
        echo json_encode(array(
          "error"=>1,
          "mensaje"=>"Por favor ingrese un email."
        ));
        return;
      } 
      */ 
      $asunto = (empty($asunto)) ? "Contacto" : $asunto;
    }
   
    if (!isset($empresa)) {
      $this->load->model("Empresa_Model");
      $empresa = $this->Empresa_Model->get($id_empresa);        
    }
    if (empty($bcc) && isset($empresa->bcc_email)) $bcc = $empresa->bcc_email;

    // Si se paso un email, buscamos el contacto para saber si existe
    if ($buscar_telefono == 0) {
      $contacto = (!empty($email)) ? $this->Cliente_Model->get_by_email($email,$id_empresa,array("id_usuario"=>$id_usuario)) : FALSE;
    } else {
      $contacto = $this->Cliente_Model->get_by_telefono($telefono,array(
        "id_empresa"=>$id_empresa
      ));
    }
    
    if ($contacto === FALSE) {
      // Debemos crearlo
      $contacto = new stdClass();
      $contacto->id_empresa = $id_empresa;
      $contacto->email = $email;
      $contacto->nombre = $nombre;
      $contacto->telefono = $telefono;
      $contacto->fax = $prefijo;
      $contacto->celular = $celular;
      $contacto->direccion = $direccion;
      $contacto->localidad = $ciudad;
      $contacto->id_localidad = $id_localidad;
      $contacto->fecha_inicial = date("Y-m-d");
      $contacto->fecha_ult_operacion = date("Y-m-d H:i:s");
      // Por defecto le ponemos contreña 1 a los que consultan para no tener problemas al momento de comprar
      $contacto->password = "c4ca4238a0b923820dcc509a6f75849b";
      $contacto->tipo = $tipo; // 1 = Contacto
      $contacto->activo = 1; // El cliente esta activo por defecto
      $contacto->id_sucursal = 0; // Para que en algunas BD no tire error de default value
      $contacto->codigo = $this->Cliente_Model->next(array("id_empresa"=>$id_empresa));
      $contacto->id_usuario = $id_usuario;
      $contacto->custom_3 = (($empresa->id_proyecto == 3) ? "1" : "");

      // Resto de los campos NOT NULL sin default en la tabla clientes
      $contacto->id_tipo_documento = 96;
      $contacto->id_tipo_iva = 4;
      $contacto->forma_pago = "E";
      $contacto->enviar_email = 1;
      $contacto->cuit = "";
      $contacto->percibe_ib = 0;
      $contacto->descuento = 0;
      $contacto->percepcion_ib = 0;
      $contacto->id_provincia = 0;
      $contacto->lista = 0;
      $contacto->id_vendedor = 0;
      $contacto->observaciones = "";
      $contacto->contacto_nombre = "";
      $contacto->contacto_telefono = "";
      $contacto->contacto_celular = "";
      $contacto->contacto_email = "";
      $contacto->saldo_inicial = 0;
      $contacto->saldo_inicial_2 = 0;
      $contacto->path = "";
      $contacto->id_plan = 0;
      $contacto->codigo_postal = "";
      $contacto->fecha_nac = date("Y-m-d");
      $contacto->uploaded = 0;
      $contacto->fecha_vencimiento = date("Y-m-d H:i:s");
      $contacto->no_leido = 1;
      $contacto->custom_1 = "";
      $contacto->custom_2 = "";
      $contacto->custom_4 = "";
      $contacto->custom_5 = "";
      $contacto->facebook = "";
      $contacto->twitter = "";
      $contacto->instagram = "";
      $contacto->youtube = "";
      $contacto->linkedin = "";
      $contacto->latitud = 0;
      $contacto->longitud = 0;
      $contacto->zoom = 0;
      $contacto->path_2 = "";
      $contacto->horario = "";
      $contacto->nombre_fantasia = "";

      $id = $this->Cliente_Model->insert($contacto);
      $contacto->id = $id;

      // REGISTRAMOS COMO UN EVENTO LA CREACION DEL NUEVO USUARIO
      $this->load->model("Consulta_Model");
      $this->Consulta_Model->registro_creacion_usuario(array(
        "id_contacto"=>$id,
        "id_empresa"=>$id_empresa,
      ));
    } else {
      // Si hay algun dato distinto, debemos actualizarlo
      $updates = array();
      if (!empty($nombre) && $nombre != $contacto->nombre) $updates[] = array("key"=>"nombre","value"=>$nombre);
      if (!empty($telefono) && $telefono != $contacto->telefono) $updates[] = array("key"=>"telefono","value"=>$telefono);
      if (!empty($prefijo) && $prefijo != $contacto->fax) $updates[] = array("key"=>"fax","value"=>$prefijo);
      if (!empty($direccion) && $direccion != $contacto->direccion) $updates[] = array("key"=>"direccion","value"=>$direccion);
      if (!empty($localidad) && $localidad != $contacto->localidad) $updates[] = array("key"=>"localidad","value"=>$localidad);
      if (!empty($celular) && $celular != $contacto->celular) $updates[] = array("key"=>"celular","value"=>$celular);
      if (!empty($id_localidad) && $id_localidad != $contacto->id_localidad) $updates[] = array("key"=>"id_localidad","value"=>$id_localidad);
      if (sizeof($updates)>0) {
        $sql = "UPDATE clientes SET ";
        for ($it=0; $it < sizeof($updates); $it++) { 
          $up = $updates[$it];
          $sql.= $up["key"]." = '".$up["value"]."' ".(($it<sizeof($updates)-1)?",":"");
        }
        $sql.= "WHERE id = $contacto->id AND id_empresa = $id_empresa ";
        $this->db->query($sql);
      }
    }

    // Dependiendo de cual atributo se envio
    $id_referencia = 0;
    if ($id_propiedad != 0) $id_referencia = $id_propiedad;
    else if ($id_articulo != 0) $id_referencia = $id_articulo;
    else if ($id_viaje != 0) $id_referencia = $id_viaje;
    else if ($id_auto != 0) $id_referencia = $id_auto;

    // En el caso de estar consultando por una propiedad de la red
    $id_empresa_relacion = parent::get_post("id_empresa_relacion",$id_empresa);

    if (!empty($id_propiedad)) {
      $this->load->model("Propiedad_Model");
      $propiedad = $this->Propiedad_Model->get($id_propiedad,array(
        "id_empresa"=>$id_empresa
      ));
      // Si no estamos definiendo un usuario desde la web, tenemos que poner el asignado en la propiedad
      if (empty($id_usuario)) $id_usuario = $propiedad->id_usuario;
    }      
    
    $fecha = date("Y-m-d H:i:s");
    $consulta = new stdClass();
    $consulta->id_empresa = $id_empresa;
    $consulta->id_empresa_relacion = $id_empresa_relacion;
    $consulta->id_entrada = $id_entrada;
    $consulta->fecha = $fecha;
    $consulta->hora = date("H:i:s");
    $consulta->asunto = $asunto;
    $consulta->subtitulo = $subtitulo;
    $consulta->texto = $mensaje;
    $consulta->canal = $canal;
    $consulta->id_contacto = $contacto->id;
    $consulta->id_origen = $id_origen;
    $consulta->id_usuario = $id_usuario;
    $consulta->id_referencia = $id_referencia;
    $this->modelo->insert($consulta);

    // Actualizamos el contacto con la ultima fecha de operacion
    $sql = "UPDATE clientes SET ";
    if ($no_actualizar_fecha == 0) $sql.= "fecha_ult_operacion = '$fecha', ";
    if ($tipo != 1) $sql.= "tipo = '$tipo', ";
    $sql.= "no_leido = 1 ";
    $sql.= "WHERE id = $contacto->id AND id_empresa = $id_empresa ";
    $this->db->query($sql);

    $body = "";
    if ($solo_mensaje == 1) {
      $body = nl2br($mensaje);
    } else {
      // TODO: Configurar esto segun el idioma de la empresa
      if ($id_empresa == 256 || $id_empresa == 257) {
        if (!empty($nombre)) $body.= "Name: $nombre <br/>";
        if (!empty($asunto)) $body.= "Subject: $asunto <br/>";
        if (!empty($email)) $body.= "Email: $email <br/>";
        if (!empty($telefono)) $body.= "Phone: $telefono <br/>";
        if (!empty($ciudad)) $body.= "City: $ciudad <br/>";
        if (isset($para) && !empty($para)) $body.= "To: $para <br/>";
        if (!empty($mensaje)) $body.= "Message: $mensaje <br/>";

      } else if ($id_origen == 30 || $id_origen == 31) {
        
        // Contacto de Clienapp (sea directo o fuera de linea)
        $clave_template = (($id_origen == 31) ? "contacto-clienapp-fuera-linea" : "contacto-clienapp");
        $temp = $this->Email_Template_Model->get_by_key($clave_template,290);
        $asunto = $temp->nombre;
        $body = $temp->texto;
        $link_panel = "https://www.varcreative.com/sistema/app/#cliente_acciones/".$contacto->id;
        $link_whatsapp = "https://wa.me/".$prefijo.$telefono."?text=".urlencode("Hola $nombre muchas gracias por contactarte con nosotros");
        $body = str_replace("{{empresa}}", htmlentities($empresa->nombre,ENT_QUOTES), $body);
        $body = str_replace("{{nombre}}", $nombre, $body);
        $body = str_replace("{{telefono}}", $prefijo.$telefono, $body);
        $body = str_replace("{{email}}", $email, $body);
        $body = str_replace("{{subtitulo}}", $subtitulo, $body);
        $body = str_replace("{{mensaje}}", nl2br($mensaje), $body);
        $body = str_replace("{{link_whatsapp}}", $link_whatsapp, $body);
        $body = str_replace("{{link_panel}}", $link_panel, $body);

      } else {

        if (!empty($nombre)) $body.= "<b>Nombre:</b> $nombre <br/>";
        if (isset($para) && !empty($para)) {
          if (is_array($para)) $para = implode(", ", $para);
          $body.= "<b>Para:</b> $para <br/>";
        }
        if (!empty($email)) $body.= "<b>Email:</b> $email <br/>";
        if (!empty($telefono)) $body.= "<b>Telefono:</b> ".$prefijo.$telefono."<br/>";
        if (!empty($ciudad)) $body.= "<b>Ciudad:</b> $ciudad <br/>";
        if (!empty($asunto)) $body.= "<b>Interesado en:</b> $asunto <br/>";
        if (!empty($mensaje)) $body.= "<b>Comentarios:</b><br/> ".nl2br($mensaje)." <br/>";

        // Si existe el template asignado

        if ($id_usuario == 0) {
          $temp = $this->Email_Template_Model->get_by_key("consulta",1319);
        } else if ($row_usuario->id_perfiles == 1357) {
          $temp = $this->Email_Template_Model->get_by_key("consulta-perfil-basico",1319);
        } else {
          $temp = $this->Email_Template_Model->get_by_key("consulta",1319);
        }
        if ($temp !== FALSE) {
          $body_ant = $body;
          $body = $temp->texto;
          $body = str_replace("{{cuerpo}}", $body_ant, $body);
          $body = str_replace("{{nombre}}", $nombre_profesional, $body);
        }
      }        
    }
    /*
    echo $body;
    exit();
    */

    if ($crear_entrada == 1) {
      $sql = "INSERT INTO not_entradas (fecha,id_empresa,titulo,subtitulo,id_categoria,activo,direccion,localidad,id_pais,custom_1,custom_2,custom_3,custom_4,custom_5,custom_6,custom_7,custom_8,custom_9,custom_10,eliminada) VALUES (";
      $sql.= "'$entrada_fecha','$id_empresa','$entrada_titulo','$entrada_subtitulo','$entrada_id_categoria','$entrada_activo','$entrada_direccion','$entrada_localidad','$entrada_id_pais','$entrada_custom_1','$entrada_custom_2','$entrada_custom_3','$entrada_custom_4','$entrada_custom_5','$entrada_custom_6','$entrada_custom_7','$entrada_custom_8','$entrada_custom_9','$entrada_custom_10',0)";
      file_put_contents("log_crear_entrada.txt", $sql."\n", FILE_APPEND);
      $this->db->query($sql);
      $id_entrada = $this->db->insert_id();
      $this->load->helper("file_helper");
      $link = $entrada_base_link."entrada/".filename($titulo,"-",0)."-".$id_entrada."/";
      $this->db->query("UPDATE not_entradas SET link = '$link' WHERE id = $id_entrada AND id_empresa = $id_empresa");
      if (!empty($entrada_ids_etiquetas)) {
        $ids_etiquetas = explode(",", $entrada_ids_etiquetas);
        foreach($ids_etiquetas as $id_etiqueta) {
          $this->db->query("INSERT INTO not_entradas_etiquetas (id_entrada,id_etiqueta,id_empresa) VALUES ($id_entrada,$id_etiqueta,$id_empresa) ");
        }
      }
    }

    $bcc_array = array();
    if ($testing == 1) $bcc_array[] = "basile.matias99@gmail.com";
    if (!empty($bcc)) {
      $arr = explode(",", $bcc);
      $bcc_array = array_merge($bcc_array,$arr);
      $bcc_array = array_unique($bcc_array);
    }

    if (isset($para) && empty($para)) $para = $empresa->email;
    if (!is_array($para)) $para = explode(",", $para);

    // Dependiendo de la configuracion 
    if (!empty($id_propiedad) && isset($propiedad->usuario_email) && !empty($propiedad->usuario_email)) {
      $this->load->model("Web_Configuracion_Model");
      $web = $this->Web_Configuracion_Model->get($id_empresa);
      if (isset($web->crm_enviar_emails_usuarios) && $web->crm_enviar_emails_usuarios == 1) {
        // Se tiene que enviar solo al usuario de la propiedad asignada
        $para = array($propiedad->usuario_email);
      } else if (isset($web->crm_enviar_emails_usuarios) && $web->crm_enviar_emails_usuarios == 2) {
        // Se tiene que mandar tanto al email de la empresa como el usuario asignado a la propiedad
        $para = array(
          $propiedad->usuario_email,
          $empresa->email
        );
      }
    }

    // Por las dudas que haya quedado algun repetido, lo eliminamos y listo
    $para = array_unique($para);

    // Copia de seguimiento para verificar que el envio de mails funciona
    $bcc_array[] = "tecnologiaentrenaymas@gmail.com";
    $bcc_array = array_unique($bcc_array);

    require APPPATH.'libraries/Mandrill/Mandrill.php';
    mandrill_send(array(
      "to"=>$para,
      "from"=>MAIL_FROM_ADDRESS,
      "from_name"=>$empresa->nombre,
      "subject"=>html_entity_decode($asunto,ENT_QUOTES),
      "body"=>$body,
      "reply_to"=>$email,
      "bcc"=>$bcc_array,
    ));

    // Si tenemos que enviarle un template al mismo cliente
    if (!empty($template)) {
      
      $temp = $this->Email_Template_Model->get_by_key($template,$id_empresa);
      if ($temp !== FALSE) {
        $bcc_array = array_merge($bcc_array,$para);
        $body = $temp->texto;
        $body = str_replace("{{nombre}}", $nombre, $body);
        $body = str_replace("{{name}}", $nombre, $body);
        $body = str_replace("{{link_ficha_propiedad}}", $link_ficha_propiedad, $body);
        mandrill_send(array(
          "to"=>$email,
          "from"=>MAIL_FROM_ADDRESS,
          "from_name"=>$empresa->nombre,
          "subject"=>$temp->nombre,
          "body"=>$body,
          "reply_to"=>$para,
          "bcc"=>$bcc_array,
        ));
      }
    }

    // Registramos la consulta en la factura abierta del usuario
    $this->load->model("Factura_Model");
    $this->Factura_Model->registrar_consulta(array(
      "origen"=>"formulario",
      "id_empresa"=>$id_empresa,
      "id_usuario"=>$id_usuario,
      "id_localidad"=>$id_localidad,
    ));
    
    echo json_encode(array(
     "error"=>0,
    ));
  }

  function exportar($id_origen = 0) {
    header('Content-Type: application/csv');
    header('Content-Disposition: attachment; filename=consultas.csv');
    header('Pragma: no-cache');
    $id_empresa = parent::get_empresa();
    $sql = "SELECT CTO.email, CTO.nombre, CTO.telefono, CTA.* ";
    $sql.= "FROM crm_consultas CTA INNER JOIN clientes CTO ON (CTA.id_contacto = CTO.id) ";
    $sql.= "WHERE CTA.id_empresa = $id_empresa ";
    if ($id_origen != 0) $sql.= "AND CTA.id_origen = $id_origen ";
    $q = $this->db->query($sql);
    foreach($q->result() as $r) {
     echo $r->nombre.";";
     echo $r->email.";";
     echo $r->telefono.";";
     echo "\n";
   }
 }
 
}
