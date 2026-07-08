<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Turno_Model extends Abstract_Model {
  
  function __construct() {
    parent::__construct("turnos","id","fecha DESC",1);
  }

  function save($data) {
    $this->load->helper("fecha_helper");
    $data->fecha = fecha_mysql($data->fecha);

    // Ponemos los stamps desde y hasta
    $data->desde = $data->fecha." ".$data->hora;
    $dt = new DateTime($data->desde);
    $dt->add(new DateInterval("PT".$data->duracion_cantidad.$data->duracion_tipo));
    $data->hasta = $dt->format("Y-m-d H:i");

    // Controlamos si el turno esta libre
    $libre = $this->is_free(array(
      "fecha"=>$data->fecha,
      "hora"=>$data->hora,
      "not_id"=>((isset($data->id) && !empty($data->id)) ? $data->id : 0),
      "id_servicio"=>$data->id_servicio,
    ));
    if (!$libre) $this->send_error("El turno esta ocupado, por favor seleccione otro horario.");

    return parent::save($data);
  }

  function disponibles($config=array()) {

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    $salida = array();
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $id_servicio = isset($config["id_servicio"]) ? $config["id_servicio"] : 0;
    $fecha = isset($config["fecha"]) ? $config["fecha"] : "";

    $this->load->model("Turno_Servicio_Model");
    $servicio = $this->Turno_Servicio_Model->get($id_servicio,$id_empresa);
    if ($servicio === FALSE) return $salida;

    // Obtenemos todos los horarios de ese servicio ese dia en particular
    $dayofweek = date('w', strtotime($fecha));
    foreach($servicio->horarios as $horario) {
      if ($horario->dia == $dayofweek) {

        $desde = new DateTime($fecha." ".$horario->desde);
        $hasta = new DateTime($fecha." ".$horario->hasta);

        // La duracion del turno no puede ser cero, en caso de que este mal configurado le ponemos una hora
        if ($servicio->duracion_turno <= 0) $servicio->duracion_turno = 60;
        
        $interval = new DateInterval("PT".$servicio->duracion_turno."M");

        // Le restamos un intervalo para que no pueda sacar turnos que se excedan del horario
        $hasta->sub($interval);
        $hasta->add(new DateInterval("PT1M")); // Le sumamos 1 minuto por si justo entra el intervalo

        $period = new DatePeriod($desde,$interval,$hasta);
        foreach ($period as $dt) {

          $esta_libre = $this->is_free(array(
            "id_empresa"=>$id_empresa,
            "id_servicio"=>$servicio->id,
            "fecha"=>$fecha,
            "hora"=>$dt->format("H:i:s"),
          ));
          if ($esta_libre) {
            $salida[] = array(
              "hora"=>$dt->format("H:i"),
              "fecha"=>$fecha,
            );
          }
        }
      }
    }
    return $salida;
  }

  function is_free($config=array()) {
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $not_id = isset($config["not_id"]) ? $config["not_id"] : 0;
    $id_servicio = isset($config["id_servicio"]) ? $config["id_servicio"] : 0;
    $id_cliente = isset($config["id_cliente"]) ? $config["id_cliente"] : 0;
    $fecha = isset($config["fecha"]) ? $config["fecha"] : "";
    $hora = isset($config["hora"]) ? $config["hora"] : "";
    $sql = "SELECT * FROM turnos ";
    $sql.= "WHERE id_empresa = $id_empresa ";
    if (!empty($not_id)) $sql.= "AND id != $not_id ";
    if (!empty($id_servicio)) $sql.= "AND id_servicio = $id_servicio ";
    if (!empty($id_cliente)) $sql.= "AND id_cliente = $id_cliente ";
    if (!empty($fecha) && !empty($hora)) {
      $fechahora = $fecha." ".$hora;
      $sql.= "AND desde <= '$fechahora' AND '$fechahora' < hasta ";
    }
    $q = $this->db->query($sql);
    return ($q->num_rows()>0)?FALSE:TRUE;
  }

  function insert($data) {
    
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    $servicio = $data->servicio;
    $data->fecha_realizacion = date("Y-m-d H:i:s");
    $id = parent::insert($data);

    // Debemos crear la consulta
    $this->load->model("Consulta_Model");
    $consulta = new stdClass();
    $consulta->id_origen = 23; // TURNO EN GENERAL
    $consulta->tipo = 1;
    $consulta->id_empresa = $data->id_empresa;
    $consulta->id_contacto = $data->id_cliente;
    $consulta->fecha = $data->fecha;
    $consulta->id_usuario = $data->id_usuario;
    $consulta->hora = $data->hora;
    $consulta->texto = $data->observaciones;
    $consulta->id_referencia = $id;
    $consulta->asunto = $servicio;
    $this->Consulta_Model->insert($consulta);

    $this->load->helper("fecha_helper");
    $this->load->model("Cliente_Model");
    $cliente = $this->Cliente_Model->get($data->id_cliente,$data->id_empresa,array(
      "buscar_consultas"=>0,
      "buscar_etiquetas"=>0,
    ));
    if (empty($cliente)) return $id;
    if (!empty($cliente->email)) {

      $this->load->model("Empresa_Model");
      $empresa = $this->Empresa_Model->get($data->id_empresa);

      require APPPATH.'libraries/Mandrill/Mandrill.php';

      // Le mandamos un propio email al usuario con su reserva
      $this->load->model("Email_Template_Model");
      $template = $this->Email_Template_Model->get_by_key("turno-ok",$data->id_empresa);
      if (!isset($template->texto)) {
        $template = new stdClass();
        $template->nombre = "Turno web";
        $template->texto = "Recuerde que tiene turno para {{servicio}} el dia {{fecha}} a las {{hora}}.";
      }
      $body = $template->texto;
      $body = str_replace("{{cliente}}",$cliente->nombre,$body);
      $body = str_replace("{{fecha}}",fecha_es($data->fecha),$body);
      $body = str_replace("{{hora}}",$data->hora,$body);
      $body = str_replace("{{servicio}}",utf8_decode($servicio),$body);
      $body = str_replace("{{empresa}}",($empresa->nombre),$body);
      if (!empty($empresa->dominio_ppal)) $body = str_replace("{{link_web}}",$empresa->dominio_ppal,$body);
      $body = str_replace("{{link_ver_pedido}}","https://www.varcreative.com/sistema/turnos/function/ver_pdf/".$id."/".$data->id_empresa,$body);    
      $body = str_replace("{{id_empresa}}",$empresa->id,$body);
      $body = str_replace("'", "\"", $body);

      mandrill_send(array(
        "from_name"=>$empresa->nombre,
        "to"=>$cliente->email,
        "to_name"=>$cliente->nombre,
        "reply_to"=>$empresa->email,
        "subject"=>$template->nombre,
        "body"=>$body,
      ));
    }
    return $id;
  }

  function delete($id) {
    $id_empresa = parent::get_empresa();
    $this->db->query("DELETE FROM crm_consultas WHERE id_empresa = $id_empresa AND id_referencia = $id AND id_origen = 23");
    return parent::delete($id);
  }

  function update($id,$data) {
    unset($data->fecha_realizacion);
    $salida = parent::update($id,$data);

    // Actualizamos el turno por si se movio de fecha
    $sql = "UPDATE crm_consultas SET ";
    $sql.= " fecha = '$data->fecha $data->hora' ";
    $sql.= "WHERE id_empresa = $data->id_empresa ";
    $sql.= "AND id_referencia = $id ";
    $sql.= "AND id_origen = 23 ";
    $sql.= "AND id_contacto = $data->id_cliente ";
    $this->db->query($sql);

    return $salida;
  }

  function get($id, $id_empresa = 0) {
    $id_empresa = ($id_empresa == 0) ? parent::get_empresa() : $id_empresa;
    $sql = "SELECT T.*, ";
    $sql.= " IF(T.fecha_realizacion = '0000-00-00 00:00:00','',DATE_FORMAT(T.fecha_realizacion,'%d/%m/%Y %H:%i')) AS fecha_realizacion, ";
    $sql.= " IF(CLI.nombre IS NULL,'',CLI.nombre) AS cliente, ";
    $sql.= " IF(PRO.nombre IS NULL,'',PRO.nombre) AS servicio ";
    $sql.= "FROM turnos T ";
    $sql.= " INNER JOIN clientes CLI ON (T.id_cliente = CLI.id AND T.id_empresa = CLI.id_empresa) ";
    $sql.= " INNER JOIN turnos_servicios PRO ON (T.id_servicio = PRO.id AND T.id_empresa = PRO.id_empresa) ";
    $sql.= "WHERE T.id = $id AND T.id_empresa = $id_empresa ";
    $q = $this->db->query($sql);
    return $q->row();
  }
  
  function calendario($conf = array()) {
    $id_empresa = isset($conf["id_empresa"])?$conf["id_empresa"]:parent::get_empresa();
    $fecha_desde = isset($conf["desde"])?$conf["desde"]:"";
    $fecha_hasta = isset($conf["hasta"])?$conf["hasta"]:"";
    $id_servicio = isset($conf["id_servicio"])?$conf["id_servicio"]:0;
    $id_cliente = isset($conf["id_cliente"])?$conf["id_cliente"]:0;
    $id_usuario = isset($conf["id_usuario"])?$conf["id_usuario"]:0;
    $sql = "SELECT C.*, ";
    $sql.= " IF(C.estado=-1,'background','') AS rendering, "; // Si el horario fue deshabilitado
    $sql.= " IF(C.estado=0,IF(CO.color != '',CO.color,'#28b492'),'#a94442') AS backgroundColor, ";
    $sql.= " IF(C.estado=0,IF(CO.color != '',CO.color,'#28b492'),'#a94442') AS borderColor, ";
    $sql.= " IF(CLI.nombre IS NULL,'',CLI.nombre) AS title, ";
    $sql.= " CONCAT(C.fecha,' ',C.hora) AS start, ";
    $sql.= " (IF(C.duracion_tipo = 'H',DATE_ADD((CONCAT(C.fecha,' ',C.hora)),INTERVAL C.duracion_cantidad*60 MINUTE), DATE_ADD((CONCAT(C.fecha,' ',C.hora)),INTERVAL C.duracion_cantidad MINUTE))) AS end ";
    $sql.= "FROM turnos C ";
    $sql.= "INNER JOIN turnos_servicios CO ON (C.id_servicio = CO.id AND C.id_empresa = CO.id_empresa) ";
    $sql.= "LEFT JOIN clientes CLI ON (C.id_cliente = CLI.id AND C.id_empresa = CLI.id_empresa) ";
    $sql.= "WHERE 1=1 ";
    if (!empty($id_empresa)) $sql.= "AND C.id_empresa = $id_empresa ";
    if (!empty($id_servicio)) $sql.= "AND C.id_servicio = $id_servicio ";
    if (!empty($id_cliente)) $sql.= "AND C.id_cliente = $id_cliente ";
    if (!empty($id_usuario)) $sql.= "AND C.id_usuario = $id_usuario ";
    if (!empty($fecha_desde)) $sql.= "AND '$fecha_desde' <= C.fecha ";
    if (!empty($fecha_hasta)) $sql.= "AND C.fecha <= '$fecha_hasta' ";
    $q = $this->db->query($sql);
    return $q->result();
  }
}