<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Reservas extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Reserva_Model', 'modelo',"fecha ASC",1);
  }

  function buscar() {
    $this->load->helper("fecha_helper");
    $conf = array(
      "id_empresa"=>parent::get_empresa(),
      "desde"=>fecha_mysql(parent::get_get("desde",date("d-m-Y"))),
      "hasta"=>fecha_mysql(parent::get_get("hasta",date("d-m-Y"))),
      "filter"=>parent::get_get("filter",""),
      "tipo_estado"=>parent::get_get("tipo_estado",-1),
    );
    $salida = $this->modelo->buscar($conf);
    echo json_encode($salida);
  }

  function enviar() {

    error_reporting(0); // Evita que warnings/deprecations de PHP contaminen la respuesta

    $this->load->model("Cliente_Model");
    $this->load->helper("fecha_helper");

    $nombre = $this->input->post("nombre");
    if ($nombre == FALSE) $nombre = "";
    $nombre_solo = $nombre;
    $apellido = $this->input->post("apellido");
    if ($apellido !== FALSE) $nombre = $nombre." ".$apellido;
    $email = $this->input->post("email");
    $id_empresa = $this->input->post("id_empresa");
    $mensaje = $this->input->post("mensaje");
    if ($mensaje === FALSE) $mensaje = "";
    $telefono = $this->input->post("telefono");
    if ($telefono === FALSE) $telefono = "";
    $para = $this->input->post("para");
    if ($para === FALSE) $para = "";
    $personas = $this->input->post("personas");
    if ($personas === FALSE) $personas = 1;
    $precio = $this->input->post("precio");
    if ($precio === FALSE) $precio = 1;
    $desde = ($this->input->post("desde") !== FALSE) ? $this->input->post("desde") : date("Y-m-d");
    $hasta = ($this->input->post("hasta") !== FALSE) ? $this->input->post("hasta") : date("Y-m-d");
    $precio_por_noche = parent::get_post("precio_por_noche",0);
    $precio_sin_descuento = parent::get_post("precio_sin_descuento",0);
    $cantidad_noches = parent::get_post("cantidad_noches",0);
    $id_habitacion = parent::get_post("id_habitacion",0);
    $id_tipo_habitacion = parent::get_post("id_tipo_habitacion",0);
    $descuento = parent::get_post("descuento",0);

    if (empty($email)) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"Por favor ingrese un email."
        ));
      return;
    }

    $this->load->model("Empresa_Model");
    $empresa = $this->Empresa_Model->get_empresa_min($id_empresa);

    $contacto = $this->Cliente_Model->get_by_email($email,$id_empresa);
    if ($contacto === FALSE) {
      // Debemos crearlo
      $contacto = new stdClass();
      $contacto->id_empresa = $id_empresa;
      $contacto->email = $email;
      $contacto->nombre = $nombre;
      $contacto->telefono = $telefono;
      $contacto->fecha_inicial = date("Y-m-d");
      $contacto->fecha_ult_operacion = date("Y-m-d H:i:s");
      $contacto->id_tipo_iva = 4; // CF por defecto
      $contacto->forma_pago = "E"; // Efectivo
      $contacto->enviar_email = 1;
      $contacto->no_leido = 1;
      $contacto->activo = 1;
      $contacto->tipo = 1;
      $id = $this->Cliente_Model->insert($contacto);
      $contacto->id = $id;
    }

    // Obtenemos el proximo numero de reserva
    $sql = "SELECT IF(MAX(numero) IS NULL,0,MAX(numero)) AS numero ";
    $sql.= "FROM hot_reservas ";
    $sql.= "WHERE id_empresa = $id_empresa ";
    $q_num = $this->db->query($sql);
    $r_num = $q_num->row();
    $numero = $r_num->numero + 1;

    $this->load->helper("fecha_helper");
    $desde = fecha_mysql($desde);
    $hasta = fecha_mysql($hasta);

    $reserva = array(
      "id_empresa"=>$id_empresa,
      "id_cliente"=>$contacto->id,
      "fecha_desde"=>$desde,
      "fecha_hasta"=>$hasta,
      "personas"=>$personas,
      "precio"=>$precio,
      "descuento"=>$descuento,
      "precio_por_noche"=>$precio_por_noche,
      "precio_sin_descuento"=>$precio_sin_descuento,
      "cantidad_noches"=>$cantidad_noches,
      "id_habitacion"=>$id_habitacion,
      "id_tipo_habitacion"=>$id_tipo_habitacion,
      "id_estado"=>0, // Pendiente
      "fecha_reserva"=>date("Y-m-d"),
      "hora_reserva"=>date("H:i:s"),
      "numero"=>$numero,
      "comentario"=>$mensaje,
    );
    $this->db->insert("hot_reservas",$reserva);
    $id_reserva = $this->db->insert_id();

    $this->load->model("Habitacion_Model");
    $habitacion = $this->Habitacion_Model->get($id_habitacion,$id_empresa);
    $this->load->model("Tipo_Habitacion_Model");
    $tipo_habitacion = $this->Tipo_Habitacion_Model->get($habitacion->id_tipo_habitacion,$id_empresa);

    if ($tipo_habitacion->compartida == 0) {
      // La habitacion no es compartida, sacamos de la disponibilidad el total maximo de la habitacion, para que quede inhabilitada
      $cant_personas = $tipo_habitacion->capacidad_maxima;
    } else {
      // La habitacion es compartida, sacamos de la disponibilidad solo la cantidad que estan reservando
      $cant_personas = $personas;
    }

    // Bajamos la disponibilidad para esas fechas
    $d = new DateTime($desde);
    $h = new DateTime($hasta);
    $interval = new DateInterval('P1D');
    $range = new DatePeriod($d,$interval,$h);
    foreach($range as $fecha) {
      $f = $fecha->format("Y-m-d");
      // Disminuimos la disponibilidad de la habitacion
      $this->modelo->mover_disponibilidad(array(
        "id_habitacion"=>$id_habitacion,
        "id_reserva"=>$id_reserva,
        "fecha"=>$f,
        "id_empresa"=>$id_empresa,
        "cantidad"=>$cant_personas,
        "operacion"=>"-",
      ));
    }

    require APPPATH.'libraries/Mandrill/Mandrill.php';

    // MANDAMOS EL EMAIL AL USUARIO
    $this->load->model("Email_Template_Model");
    $template = $this->Email_Template_Model->get_by_key("reserva",$id_empresa);
    if (!empty($template)) {
      $body = $template->texto;
      // Si no tiene descuento, ocultamos el id
      if ($descuento == 0) {
        $doc = new DOMDocument();
        $doc->loadHTML($body);
        $x = new DOMXPath($doc);
        $element = $x->query("//*[@id='bloque_descuento']")->item(0);
        if (!is_null($element)) {
          $element->parentNode->removeChild($element);
          $body = $doc->saveHTML();
        }
      }
      $desde_full = fecha_completa($desde);
      $hasta_full = fecha_completa($hasta);
      $body = str_replace("{{desde}}",$desde_full,$body);
      $body = str_replace("{{hasta}}",$hasta_full,$body);
      $body = str_replace("{{nombre}}",$nombre_solo,$body);
      $body = str_replace("{{tipo_habitacion_imagen}}","https://www.varcreative.com/sistema/".$tipo_habitacion->path,$body);
      $body = str_replace("{{tipo_habitacion}}",$tipo_habitacion->nombre,$body);
      $body = str_replace("{{tipo_habitacion_capacidad}}",$tipo_habitacion->capacidad_maxima,$body);
      $body = str_replace("{{cantidad_personas}}",(($personas==1)?"1 persona":($personas." personas")),$body);
      $body = str_replace("{{precio_sin_descuento}}",number_format($precio_sin_descuento,2),$body);
      $body = str_replace("{{precio_por_noche}}",number_format($precio_por_noche,2),$body);
      $body = str_replace("{{cantidad_noches}}",number_format($cantidad_noches,2),$body);
      $body = str_replace("{{descuento}}",number_format($descuento,2),$body);
      $body = str_replace("{{total}}",number_format($precio,2),$body);
      $bcc_array = array("basile.matias99@gmail.com");
      mandrill_send(array(
        "to"=>$email,
        "from"=>MAIL_FROM_ADDRESS,
        "from_name"=>$empresa->nombre,
        "subject"=>$template->nombre,
        "body"=>$body,
        "reply_to"=>$empresa->email,
        "bcc"=>$bcc_array,
      ));
    }

    // MANDAMOS EL EMAIL AL ADMINISTRADOR, CON LA NUEVA RESERVA
    $template = $this->Email_Template_Model->get_by_key("reserva-admin",$id_empresa);
    if (!empty($template)) {
      $body = $template->texto;
      $body = str_replace("{{desde}}",$desde_full,$body);
      $body = str_replace("{{hasta}}",$hasta_full,$body);
      $body = str_replace("{{tipo_habitacion}}",$tipo_habitacion->nombre,$body);
      $body = str_replace("{{tipo_habitacion_imagen}}","https://www.varcreative.com/sistema/".$tipo_habitacion->path,$body);
      $body = str_replace("{{personas}}",$personas,$body);
      $body = str_replace("{{total}}",number_format($precio,2),$body);
      $body = str_replace("{{precio_sin_descuento}}",number_format($precio_sin_descuento,2),$body);
      $body = str_replace("{{precio_por_noche}}",number_format($precio_por_noche,2),$body);
      $body = str_replace("{{cantidad_noches}}",number_format($cantidad_noches,2),$body);
      $body = str_replace("{{nombre}}",$nombre,$body);
      $body = str_replace("{{email}}",$email,$body);
      $body = str_replace("{{telefono}}",$telefono,$body);
      $body = str_replace("{{mensaje}}",$mensaje,$body);
      mandrill_send(array(
        "to"=>$empresa->email,
        "from"=>MAIL_FROM_ADDRESS,
        "from_name"=>$nombre,
        "subject"=>$template->nombre,
        "body"=>$body,
        "reply_to"=>$email,
        "bcc"=>$bcc_array,
      ));
    }

    /*
    $api_key = "XVBBXTfJPpSD6Lw5sz14No5ODnf29kZg"; // see https://telerivet.com/dashboard/api
    $project_id = "PJdacc669044067d73";
    require_once '/sistema/application/libraries/telerivet/telerivet.php';
    $api = new Telerivet_API($api_key);
    $project = $api->initProjectById($project_id);
    $mensaje = "";
    try {
      $contact = $project->sendMessage(array(
        'to_number' => $empresa->telefono,
        'content' => "Tiene un nuevo pedido"
      ));
    } catch (Telerivet_Exception $ex) {
      $mensaje = htmlentities($ex->getMessage());
    }
    */

    echo json_encode(array(
      "id_reserva"=>$id_reserva,
      "error"=>0,
      "mensaje"=>$mensaje,
    ));
  }


  function calendario() {
    $conf = array();
    $conf["id_empresa"] = parent::get_empresa();
    $conf["desde"] = $this->input->get("start");
    $conf["hasta"] = $this->input->get("end");
    $salida = $this->modelo->buscar_calendario($conf);
    echo json_encode($salida);
  }

  function ver_disponibilidad() {

    $id_empresa = parent::get_empresa();
    $this->load->model("Tipo_Habitacion_Model");
    $id_tipo_habitacion = 3;
    $d = new DateTime();
    $h = new DateTime();
    $h->modify("+7 days");
    $interval = new DateInterval('P1D');
    $range = new DatePeriod($d,$interval,$h);
    $salida = array();
    foreach($range as $fecha) {
      $precio = $this->Tipo_Habitacion_Model->precio_por_dia(array(
        "fecha"=>$fecha->format("Y-m-d"),
        "id_tipo_habitacion"=>$id_tipo_habitacion,
        ));
      $salida[] = array(
        "precio"=>$precio,
        "fecha"=>$fecha->format("d/m/Y"),
        );
    }
    print_r($salida);
  }   

  function delete($id) {

    $id_empresa = parent::get_empresa();
    $reserva = $this->modelo->get($id);
    if ($reserva === FALSE) { return; }

    $this->load->helper("fecha_helper");
    $desde = fecha_mysql($reserva->fecha_desde);
    $hasta = fecha_mysql($reserva->fecha_hasta);

    if ($reserva->tipo_habitacion->compartida == 0) {
      $this->db->query("DELETE FROM hot_disponibilidad WHERE id_empresa = $id_empresa AND id_reserva = $id ");
    } else {
      // Subimos la disponibilidad para esas fechas
      $d = new DateTime($desde);
      $h = new DateTime($hasta);
      $interval = new DateInterval('P1D');
      $range = new DatePeriod($d,$interval,$h);
      foreach($range as $fecha) {
        $f = $fecha->format("Y-m-d");
        // Aumentamos la disponibilidad de la habitacion
        $this->modelo->mover_disponibilidad(array(
          "id_habitacion"=>$reserva->id_habitacion,
          "id_reserva"=>$id,
          "fecha"=>$f,
          "id_empresa"=>$id_empresa,
          "cantidad"=>$reserva->personas,
          "operacion"=>"+",
        ));
      }
    }
    // Ahora si eliminamos la reserva
    $this->db->query("DELETE FROM hot_reservas WHERE id = $reserva->id AND id_empresa = $id_empresa");
  }


  function imprimir($id_reserva) {
    $this->load->helper("fecha_helper");
    $id_empresa = parent::get_empresa();
    $this->load->model("Reserva_Model");
    $reserva = $this->Reserva_Model->get($id_reserva);
    $this->load->model("Empresa_Model");
    $empresa = $this->Empresa_Model->get($reserva->id_empresa);
    $header = $this->load->view("reports/reserva/header",null,true);
    $this->load->view("reports/reserva/reserva",array(
      "reserva"=>$reserva,
      "empresa"=>$empresa,
      "header"=>$header,
    ));
  }

}