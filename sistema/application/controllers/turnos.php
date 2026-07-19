<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Turnos extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Turno_Model', 'modelo',"fecha ASC",1);
  }

  function listado() {
    $id_empresa = parent::get_empresa();
    $limit = parent::get_get("limit",0);
    $filter = parent::get_get("filter","");
    $offset = parent::get_get("offset",10);
    $id_usuario = parent::get_get("id_usuario",0);
    $sql = "SELECT SQL_CALC_FOUND_ROWS T.*, C.nombre, TS.nombre AS servicio, ";
    $sql.= " CONCAT(DATE_FORMAT(T.fecha,'%d/%m/%Y'),' ',T.hora) AS fecha ";
    $sql.= "FROM turnos T ";
    $sql.= "INNER JOIN clientes C ON (T.id_cliente = C.id AND T.id_empresa = C.id_empresa) ";
    $sql.= "INNER JOIN turnos_servicios TS ON (T.id_servicio = TS.id AND T.id_empresa = TS.id_empresa) ";
    $sql.= "WHERE T.id_empresa = $id_empresa ";
    if (!empty($filter)) $sql.= "AND C.nombre LIKE '%$filter%' ";
    if (!empty($id_usuario)) $sql.= "AND T.id_usuario = $id_usuario ";
    $sql.= "ORDER BY T.fecha DESC, T.hora DESC ";
    $sql.= "LIMIT $limit,$offset ";
    $q = $this->db->query($sql);

    $q_total = $this->db->query("SELECT FOUND_ROWS() AS total");
    $total = $q_total->row();

    echo json_encode(array(
      "results"=>$q->result(),
      "total"=>$total,
    ));
  }

  function ver_pdf($id_turno,$id_empresa) {
    
    $this->load->helper("fecha_helper");
    $this->load->helper("numero_letra_helper");
    $pedido = $this->modelo->get($id_turno,$id_empresa);
    if ($pedido === FALSE || empty($pedido)) {
      echo "Lo sentimos pero el turno ha sido eliminado.";
      exit();
    }
    
    $this->load->model("Empresa_Model");
    $empresa = $this->Empresa_Model->get($id_empresa);
    
    $header = $this->load->view("reports/turno/header",null,true);

    $this->load->model("Cliente_Model");
    $cliente = $this->Cliente_Model->get($pedido->id_cliente,$empresa->id);
    
    $tpl = "modelo1";
    $folder = "/sistema/application/views/reports/turno/$tpl/red";
    
    $datos = array(
      "pedido"=>$pedido,
      "cliente"=>$cliente,
      "empresa"=>$empresa,
      "header"=>$header,
      "folder"=>$folder,
    );
    $this->load->view("reports/turno/$tpl/pedido.php",$datos);
  }


  function cancelar_turno() {

    $id = $this->input->get("id");
    if (!is_numeric($id)) {
      echo "Parametros incorrectos"; exit();
    }
    $id_empresa = $this->input->get("id_empresa");
    if (!is_numeric($id_empresa)) {
      echo "Parametros incorrectos"; exit();
    }

    $this->load->model("Empresa_Model");
    $empresa = $this->Empresa_Model->get($id_empresa);

    $this->load->model("Turno_Model");
    $turno = $this->Turno_Model->get($id,$id_empresa);
    if (!empty($turno)) {
      $sql = "DELETE FROM turnos WHERE id_empresa = $id_empresa AND id = $id ";
      $this->db->query($sql);

      // Avisamos al administrador que el turno se cancelo
      $body = "El cliente ".$turno->cliente." ha cancelado su turno del servicio ".mb_convert_encoding($turno->servicio, 'UTF-8', 'ISO-8859-1')." para la fecha ".$turno->fecha." a las ".$turno->hora;
      require APPPATH.'libraries/Mandrill/Mandrill.php';
      mandrill_send(array(
        "to"=>$empresa->email,
        "from_name"=>$empresa->nombre,
        "subject"=>"Cancelacion de turno",
        "body"=>$body,
        "bcc"=>"basile.matias99@gmail.com",//(isset($empresa->config["bcc_email"]) ? $empresa->config["bcc_email"] : ""),
      ));

      echo "Su turno ha sido cancelado correctamente. Muchas gracias";
      exit();
    } else {
      echo "El turno ya ha sido cancelado. Muchas gracias.";
      exit();
    }

    /*
    // Redireccionamos a la web de la empresa
    if (!empty($empresa->dominio_ppal)) {
      $dominio = "http://".$empresa->dominio_ppal;
    } else if (sizeof($empresa->dominios)>0) {
      $dominio = "http://".$empresa->dominio[0];
    } else {
      $dominio = "http://".$empresa->dominio_varcreative;
    }
    header("Location: $dominio");
    */
  }


  // Metodo utilizado en las webs
  function enviar() {
    
    header('Access-Control-Allow-Origin: *');
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    $this->load->helper("fecha_helper");
    $this->load->model("Cliente_Model");
    $this->load->model("Consulta_Model");
    
    $id_origen = 23; // TURNOS EN GENERAL
    $nombre = parent::get_post("nombre","Cliente Nuevo");
    $apellido = parent::get_post("apellido","");
    if (!empty($apellido)) $nombre = $nombre." ".$apellido;
    $email = parent::get_post("email","");
    $fecha = parent::get_post("fecha","");
    $fecha = (empty($fecha) ? date("Y-m-d") : fecha_mysql($fecha));
    $hora = parent::get_post("hora","");
    $mensaje = parent::get_post("mensaje","");
    $asunto = parent::get_post("asunto","");
    $telefono = parent::get_post("telefono","");
    $prefijo = parent::get_post("prefijo","");
    $documento = parent::get_post("documento","");
    $celular = parent::get_post("celular","");
    $direccion = parent::get_post("direccion","");
    $ciudad = parent::get_post("ciudad","");
    $canal = parent::get_post("canal","");
    $id_localidad = parent::get_post("id_localidad",0);
    $id_servicio = parent::get_post("id_servicio",0);
    $id_usuario = parent::get_post("id_usuario",0);
    $no_actualizar_fecha = parent::get_post("no_actualizar_fecha",0);
    $para = parent::get_post("para","");
    $bcc = parent::get_post("bcc","");
    $custom = parent::get_post("custom","");
    $testing = parent::get_post("testing",1);

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

      // TODO: Controlar que la empresa esta al dia con los pagos
      // TODO: Controlar que la empresa tiene habilitado el SISTEMA DE TURNOS

      $id_empresa = $empresa->id;
      $asunto = "Turno desde web";
      $testing = 1; // Por ahora el chat esta a prueba
      $para = $empresa->email;
      $bcc = $empresa->bcc_email;

    } else {

      // Si es una web nuestra, los emails son obligatorios
      if (empty($email)) {
        echo json_encode(array(
          "error"=>1,
          "mensaje"=>"Por favor ingrese un email."
        ));
        return;
      }  
      $asunto = (empty($asunto)) ? "Turno desde web" : $asunto;    
    }

    if (!isset($empresa)) {
      $this->load->model("Empresa_Model");
      $empresa = $this->Empresa_Model->get($id_empresa);        
    }

    // Controlamos que exista el servicio
    $this->load->model("Turno_Servicio_Model");
    $servicio = $this->Turno_Servicio_Model->get($id_servicio,$id_empresa);
    if ($servicio === FALSE) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"No existe el servicio especificado.",
      ));
      return;      
    }

    // Controlamos si el turno esta disponible
    $disponible = $this->modelo->is_free(array(
      "id_empresa"=>$id_empresa,
      "id_servicio"=>$id_servicio,
      "fecha"=>$fecha,
      "hora"=>$hora,
    ));
    if (!$disponible) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"El turno se encuentra ocupado, por favor seleccione otro.",
      ));
      return;
    }   
    // Si se paso un email, buscamos el contacto para saber si existe
    $contacto = (!empty($email)) ? $this->Cliente_Model->get_by_email($email,$id_empresa,array("id_usuario"=>$id_usuario)) : FALSE;
    
    if ($contacto === FALSE) {
      // Debemos crearlo
      $contacto = new stdClass();
      $contacto->id_empresa = $id_empresa;
      $contacto->email = $email;
      $contacto->nombre = $nombre;
      $contacto->fax = $prefijo;
      $contacto->telefono = $telefono;
      $contacto->celular = $celular;
      $contacto->cuit = $documento;
      $contacto->direccion = $direccion;
      $contacto->localidad = $ciudad;
      $contacto->id_usuario = $id_usuario;
      $contacto->fecha_inicial = date("Y-m-d");
      $contacto->fecha_ult_operacion = date("Y-m-d H:i:s");
      $contacto->tipo = 1; // Contacto
      $contacto->activo = 1; // El cliente esta activo por defecto
      $id = $this->Cliente_Model->insert($contacto);
      $contacto->id = $id;

      // REGISTRAMOS COMO UN EVENTO LA CREACION DEL NUEVO USUARIO
      $this->Consulta_Model->registro_creacion_usuario(array(
        "id_contacto"=>$id,
        "id_empresa"=>$id_empresa,
      ));
    }

    $turno = new stdClass();
    $turno->id_empresa = $id_empresa;
    $turno->id_servicio = $id_servicio;
    $turno->id_cliente = $contacto->id;
    $turno->duracion_cantidad = $servicio->duracion_turno;
    $turno->duracion_tipo = "M";
    $turno->fecha = $fecha;
    $turno->fecha_realizacion = date("Y-m-d H:i:s");
    $turno->hora = $hora;
    $turno->id_usuario = $id_usuario;
    $turno->observaciones = $mensaje;
    $turno->sin_horario = 0;
    $turno->estado = 0;

    // Ponemos los stamps desde y hasta
    $turno->desde = $turno->fecha." ".$turno->hora;
    $dt = new DateTime($turno->desde);
    $dt->add(new DateInterval("PT".$turno->duracion_cantidad.$turno->duracion_tipo));
    $turno->hasta = $dt->format("Y-m-d H:i");

    $this->db->insert("turnos",$turno);
    $id_turno = $this->db->insert_id();
    if (!isset($id_turno)) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"Hubo un error al guardar el turno. Por favor intente nuevamente.",
      ));
      return;
    }

    $ahora = date("Y-m-d H:i:s");
    $consulta = new stdClass();
    $consulta->id_empresa = $id_empresa;
    $consulta->fecha = $ahora;
    $consulta->hora = date("H:i:s");
    $consulta->asunto = $asunto;
    $consulta->texto = $mensaje;
    $consulta->id_contacto = $contacto->id;
    $consulta->id_origen = $id_origen;
    $consulta->id_usuario = $id_usuario;
    $consulta->canal = $canal;
    $consulta->id_referencia = $id_turno;
    $this->Consulta_Model->insert($consulta);

    // Actualizamos el contacto con la ultima fecha de operacion
    $sql = "UPDATE clientes SET ";
    if ($no_actualizar_fecha == 0) $sql.= "fecha_ult_operacion = '$ahora', ";
    $sql.= "no_leido = 1 ";
    $sql.= "WHERE id = $contacto->id AND id_empresa = $id_empresa ";
    $this->db->query($sql);

    $this->load->model("Usuario_Model");
    $usuario = $this->Usuario_Model->get($id_usuario,array(
      "id_empresa"=>$id_empresa
    ));

    // Le mandamos un propio email al usuario con su reserva
    $this->load->model("Email_Template_Model");
    $template = $this->Email_Template_Model->get_by_key("turno-ok",$id_empresa);
    if (!isset($template->texto)) {
      $template = new stdClass();
      $template->nombre = "Turno web";
      $template->texto = "Recuerde que tiene turno para {{servicio}} el dia {{fecha}} a las {{hora}}.";
    }
    $body = $template->texto;
    $body = str_replace("{{cliente}}",$nombre,$body);
    $body = str_replace("{{profesional}}",$usuario->nombre,$body);
    $body = str_replace("{{fecha}}",fecha_es($fecha),$body);
    $body = str_replace("{{hora}}",$hora,$body);
    $body = str_replace("{{servicio}}",mb_convert_encoding($servicio->nombre, 'ISO-8859-1', 'UTF-8'),$body);
    $body = str_replace("{{empresa}}",($empresa->nombre),$body);
    if (!empty($empresa->dominio_ppal)) $body = str_replace("{{link_web}}",$empresa->dominio_ppal,$body);
    $body = str_replace("{{link_ver_pedido}}","https://www.entrenaymas.com/sistema/turnos/function/ver_pdf/".$id_turno."/".$id_empresa,$body);
    $body = str_replace("{{id_empresa}}",$empresa->id,$body);
    $body = str_replace("'", "\"", $body);

    $template->nombre = str_replace("{{profesional}}",$usuario->nombre,$template->nombre);

    require APPPATH.'libraries/Mandrill/Mandrill.php';
    mandrill_send(array(
      "from_name"=>$empresa->nombre,
      "to"=>$email, // Se envia al usuario
      "to_name"=>$nombre,
      "reply_to"=>$empresa->email,
      "subject"=>$template->nombre,
      "body"=>$body,
      "bcc"=>array($usuario->email), // Se manda con copia al mismo profesional
    ));

    // Registramos la consulta en la factura abierta del usuario
    $this->load->model("Factura_Model");
    $this->Factura_Model->registrar_consulta(array(
      "origen"=>"turno",
      "id_empresa"=>$id_empresa,
      "id_usuario"=>$id_usuario,
      "id_localidad"=>$id_localidad,
    ));    
    
    echo json_encode(array(
      "error"=>0,
      "id"=>$id_turno,
    ));
  }

  function calendario() {
    $conf = array();
    $conf["id_empresa"] = parent::get_empresa();
    $conf["desde"] = $this->input->get("start");
    $conf["hasta"] = $this->input->get("end");
    $conf["id_servicio"] = ($this->input->get("id_servicio") !== FALSE) ? $this->input->get("id_servicio") : 0;
    $conf["id_cliente"] = ($this->input->get("id_cliente") !== FALSE) ? $this->input->get("id_cliente") : 0;
    $conf["id_usuario"] = ($this->input->get("id_usuario") !== FALSE) ? $this->input->get("id_usuario") : 0;
    $salida = $this->modelo->calendario($conf);
    echo json_encode($salida);
  }    

  function realizar_turno() {
    $id_empresa = parent::get_empresa();
    $id = ($this->input->post("id") !== FALSE) ? $this->input->post("id") : 0;
    $this->db->query("UPDATE turnos SET estado = 1 WHERE id = $id AND id_empresa = $id_empresa");
    echo json_encode(array("error"=>0));
  }

  // Devuelve los horarios disponibles para el dia y el servicio especificado
  function disponibles() {
    header('Access-Control-Allow-Origin: *');
    $this->load->helper("fecha_helper");
    $id_empresa = ($this->input->post("id_empresa") !== FALSE) ? $this->input->post("id_empresa") : 0;
    $fecha = ($this->input->post("fecha") !== FALSE) ? fecha_mysql($this->input->post("fecha")) : "";
    $id_servicio = ($this->input->post("id_servicio") !== FALSE) ? $this->input->post("id_servicio") : 0;
    $disponibles = $this->modelo->disponibles(array(
      "id_empresa"=>$id_empresa,
      "fecha"=>$fecha,
      "id_servicio"=>$id_servicio,
    ));
    echo json_encode(array(
      "disponibles"=>$disponibles,
    ));
  }  

  // Utilizado en eventDrop de calendario
  function cambiar_fecha() {
    $data = new stdClass();
    $data->id_empresa = parent::get_empresa();
    $data->id = ($this->input->post("id") !== FALSE) ? $this->input->post("id") : 0;
    $data->fecha = ($this->input->post("fecha") !== FALSE) ? $this->input->post("fecha") : "";
    $data->hora = ($this->input->post("hora") !== FALSE) ? $this->input->post("hora") : 0;
    $data->duracion_cantidad = ($this->input->post("duracion_cantidad") !== FALSE) ? $this->input->post("duracion_cantidad") : 60;
    $data->duracion_tipo = ($this->input->post("duracion_tipo") !== FALSE) ? $this->input->post("duracion_tipo") : "M";
    $data->id_cliente = ($this->input->post("id_cliente") !== FALSE) ? $this->input->post("id_cliente") : 0;
    $data->id_servicio = ($this->input->post("id_servicio") !== FALSE) ? $this->input->post("id_servicio") : 0;
    $this->modelo->save($data);
    echo json_encode(array());
  }

  function update($id) {
    if ($id == 0) { $this->insert(); return; }
    $this->load->helper("fecha_helper");
    $array = $this->parse_put();
    $id_empresa = parent::get_empresa();
    $array->id_empresa = $id_empresa;
    $array->fecha = fecha_mysql($array->fecha);
    $this->modelo->save($array);
    $salida = array(
      "id"=>$id,
      "error"=>0,
    );
    echo json_encode($salida);        
  }

  function insert() {
    $this->load->helper("fecha_helper");
    $array = $this->parse_put();
    $id_empresa = parent::get_empresa();
    $array->id_empresa = $id_empresa;
    $array->fecha = fecha_mysql($array->fecha);
    $insert_id = $this->modelo->save($array);
    $salida = array(
      "id"=>$insert_id,
      "error"=>0,
      );
    echo json_encode($salida);        
  }    

}