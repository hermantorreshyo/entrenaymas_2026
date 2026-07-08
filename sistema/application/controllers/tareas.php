<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Tareas extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Consulta_Model', 'modelo');
  }

  function enviar_recordatorio() {

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    set_time_limit(0);
    date_default_timezone_set("America/Argentina/Buenos_Aires");

    // A traves de un archivo, controlamos que no se ejecuten dos veces el mismo proceso
    $filename = "sem_tareas.txt";
    if (file_exists($filename) === FALSE) file_put_contents($filename, "");
    $file = fopen($filename, "r+");
    // Intenta adquirir un bloqueo exclusivo
    if((flock($file, LOCK_EX | LOCK_NB) === FALSE)) exit();

    $bcc_array = array("basile.matias99@gmail.com");
    require APPPATH.'libraries/Mandrill/Mandrill.php';
    $this->load->model("Email_Template_Model");
    $this->load->model("Empresa_Model");

    $template = $this->Email_Template_Model->get_by_key("tarea",936);

    // Seleccionamos las tareas que vencen dentro de un tiempo
    $sql = "SELECT * FROM crm_consultas WHERE id_origen = 17 ";
    $q = $this->db->query($sql);
    foreach($q->result() as $tarea) {

      $empresa = $this->Empresa_Model->get_min($tarea->id_empresa);
      $asunto = $template->nombre;
      $texto = $template->texto;
      $texto = str_replace("{{nombre}}", $usuario->nombre, $texto);

      mandrill_send(array(
        "to"=>$usuario->email,
        "from"=>"no-reply@varcreative.com",
        "from_name"=>$empresa->nombre,
        "subject"=>$asunto,
        "body"=>$texto,
        "bcc"=>$bcc_array,
      ));
    }
  }


  function delete($id) {
    $id_empresa = parent::get_empresa();
    $sql = "DELETE FROM crm_consultas WHERE id = $id AND id_empresa = $id_empresa";
    $this->db->query($sql);
    echo json_encode(array("error"=>0));
  }


  // Utilizado en ARGENCASH
  function estadisticas_prestamos_tareas() {
    $this->load->helper("fecha_helper");
    $id_empresa = parent::get_empresa();
    $desde = parent::get_get("desde",date("d/m/Y"));
    $hasta = parent::get_get("hasta",date("d/m/Y"));
    $desde = fecha_mysql($desde);
    $hasta = fecha_mysql($hasta);
    $sql = "SELECT U.*, ALM.nombre AS sucursal ";
    $sql.= "FROM com_usuarios U INNER JOIN almacenes ALM ON (U.id_empresa = ALM.id_empresa AND U.id_sucursal = ALM.id) ";
    $sql.= "WHERE U.id_empresa = $id_empresa ";
    $q = $this->db->query($sql);
    $salida = array();
    foreach($q->result() as $usuario) {

      // Contamos la cantidad de tareas
      $sql = "SELECT IF(COUNT(*) IS NULL,0,COUNT(*)) AS cantidad ";
      $sql.= "FROM crm_consultas C ";
      $sql.= "WHERE C.id_empresa = $id_empresa ";
      if (!empty($desde)) $sql.= "AND C.fecha >= '$desde' ";
      if (!empty($hasta)) $sql.= "AND C.fecha <= '$hasta' ";
      $sql.= "AND C.id_usuario = $usuario->id ";
      $qq = $this->db->query($sql);
      $tareas = $qq->row();

      // Contamos la cantidad de tareas cumplidas
      $sql = "SELECT IF(COUNT(*) IS NULL,0,COUNT(*)) AS cantidad ";
      $sql.= "FROM crm_consultas C ";
      $sql.= "WHERE C.id_empresa = $id_empresa ";
      $sql.= "AND C.custom_1 = '1' ";
      if (!empty($desde)) $sql.= "AND C.fecha >= '$desde' ";
      if (!empty($hasta)) $sql.= "AND C.fecha <= '$hasta' ";
      $sql.= "AND C.id_usuario = $usuario->id ";
      $qq = $this->db->query($sql);
      $tareas_cumplidas = $qq->row();

      // Creamos el objeto nuevo
      $r = new stdClass();
      $r->usuario = $usuario->nombre;
      $r->sucursal = $usuario->sucursal;
      $r->total_tareas = $tareas->cantidad;
      $r->total_tareas_cumplidas = $tareas_cumplidas->cantidad;
      $r->porcentaje = ($tareas->cantidad > 0) ? ($tareas_cumplidas->cantidad / $tareas->cantidad * 100) : 0;
      $salida[] = $r;
    }
    echo json_encode(array("datos"=>$salida));
  }


  function buscar() {

    $this->load->helper("fecha_helper");
    $id_empresa = parent::get_empresa();
    $filter = parent::get_get("filter","");
    $custom_1 = parent::get_get("custom_1","");
    $filtro_fecha = parent::get_get("filtro_fecha",0);
    $desde = parent::get_get("desde","");
    if (!empty($desde)) $desde = fecha_mysql($desde);
    $hasta = parent::get_get("hasta","");
    if (!empty($hasta)) $hasta = fecha_mysql($hasta);
    $hasta = date('Y-m-d', strtotime($hasta. '+1 days'));

    $id_sucursal = parent::get_get("id_sucursal",0);
    $id_usuario = parent::get_get("id_usuario",0);
    $limit = $this->input->get("limit");
    $offset = $this->input->get("offset");
    $order_by = $this->input->get("order_by");
    $order = $this->input->get("order");
    if (!empty($order_by) && !empty($order)) $order = $order_by." ".$order;
    else $order = "";    

    $sql = "SELECT SQL_CALC_FOUND_ROWS C.*, CONCAT(CLI.nombre,' ',CLI.apellido) AS nombre, ";
    // La fecha es la fecha de la promesa (puede ser a futuro)
    $sql.= " DATE_FORMAT(C.fecha,'%d/%m/%Y %H:%i:%s') AS fecha, ";
    // La fecha visto es la fecha de la tarea
    $sql.= " DATE_FORMAT(C.fecha_visto,'%d/%m/%Y %H:%i:%s') AS fecha_visto ";

    $sql.= "FROM crm_consultas C ";
    $sql.= "INNER JOIN pres_clientes CLI ON (C.id_contacto = CLI.id AND C.id_empresa = CLI.id_empresa) ";
    $sql.= "WHERE C.id_empresa = $id_empresa ";

    if (is_numeric($filter)) {
      // Si es numero, buscamos por DNI
      $sql.= "AND CLI.documento = '$filter' ";
    } else {
      // Sino buscamos por nombre
      $sql.= "AND CONCAT(nombre,' ',apellido) LIKE '%$filter%' ";
    }
    if (!empty($custom_1)) $sql.= "AND C.custom_1 = $custom_1 ";
    if (!empty($id_sucursal)) $sql.= "AND CLI.id_sucursal = $id_sucursal ";
    if (!empty($id_usuario)) $sql.= "AND C.id_usuario = $id_usuario ";
    if ($filtro_fecha == 0) {
      if (!empty($desde)) $sql.= "AND C.fecha >= '$desde' ";
      if (!empty($hasta)) $sql.= "AND C.fecha <= '$hasta' ";
      $sql.= "ORDER BY C.fecha DESC ";
    } else if ($filtro_fecha == 1) {
      if (!empty($desde)) $sql.= "AND C.fecha_visto >= '$desde' ";
      if (!empty($hasta)) $sql.= "AND C.fecha_visto <= '$hasta' ";      
      $sql.= "ORDER BY C.fecha_visto DESC ";
    }
    $sql.= "LIMIT $limit, $offset ";
    $q = $this->db->query($sql);
    $salida = $q->result();

    $q_total = $this->db->query("SELECT FOUND_ROWS() AS total");
    $total = $q_total->row();

    echo json_encode(array(
      "results"=>$salida,
      "total"=>$total->total,
    ));
  }

  function vencidas() {
    $this->load->model("Consulta_Model");
    $id_sucursal = parent::get_get("id_sucursal",0);
    $id_usuario = parent::get_get("id_usuario",0);
    $id_contacto = parent::get_get("id_contacto",0);
    $tareas = $this->Consulta_Model->buscar(array(
      "id_origenes"=>17, // ES UNA TAREA
      "id_usuario"=>$id_usuario,
      "id_contacto"=>$id_contacto,
      "estado"=>0,
      "hasta"=>date("Y-m-d H:i:s"),
      "order_by"=>"C.fecha ASC, C.id ASC", // El orden es al reves, de las mas atrasadas a las mas nuevas
    ));
    echo json_encode($tareas);
  }

  function get_by_date() {
    $this->load->model("Consulta_Model");
    $this->load->helper("fecha_helper");
    $fecha_desde = $this->input->get("start");
    $fecha_hasta = $this->input->get("end");
    $id_sucursal = parent::get_get("id_sucursal",0);
    $id_usuario = parent::get_get("id_usuario",0);
    $id_contacto = parent::get_get("id_contacto",0);
    $estado = parent::get_get("estado",-1);
    $id_empresa = parent::get_empresa();
    $salida = array();
    $tareas = $this->Consulta_Model->buscar(array(
      "id_origenes"=>17, // ES UNA TAREA
      "id_usuario"=>$id_usuario,
      "id_contacto"=>$id_contacto,
      "estado"=>$estado,
      "desde"=>$fecha_desde,
      "hasta"=>$fecha_hasta,
    ));
    foreach($tareas["results"] as $m) {
      $m->title = $m->nombre." - ".$m->asunto;
      $m->descripcion = $m->texto;
      $m->backgroundColor = $m->color_asunto;
      $m->borderColor = $m->color_asunto;
      $m->start = fecha_mysql($m->fecha)." ".$m->hora;
      $m->desde = fecha_mysql($m->fecha)." ".$m->hora;
      $salida[] = $m;
    }
    echo json_encode($salida);
  }  

  // Utilizado en eventDrop de calendario
  function cambiar_fecha() {
    $data = new stdClass();
    $data->id_empresa = parent::get_empresa();
    $data->id = ($this->input->post("id") !== FALSE) ? $this->input->post("id") : 0;
    $data->fecha = ($this->input->post("fecha") !== FALSE) ? $this->input->post("fecha") : "";
    $data->hora = ($this->input->post("hora") !== FALSE) ? $this->input->post("hora") : "00:00:00";
    if (!empty($data->fecha)) {
      $sql = "UPDATE crm_consultas SET fecha = '$data->fecha $data->hora' ";
      $sql.= "WHERE id_empresa = $data->id_empresa AND id = $data->id ";
      //echo $sql; exit();
      $this->db->query($sql);      
    }
    echo json_encode(array());
  }

}