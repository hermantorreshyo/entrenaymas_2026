<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Mantenimientos extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Mantenimiento_Model', 'modelo',"fecha ASC",1);
  }

  function next() {
    $row = $this->modelo->next();
    echo json_encode(array(
      "numero"=>$row->numero,
      "numero_orden_trabajo"=>$row->numero_orden_trabajo,
    ));
  }

  function calendario() {
    $conf = array();
    $conf["id_empresa"] = parent::get_empresa();
    $conf["desde"] = $this->input->get("start");
    $conf["hasta"] = $this->input->get("end");
    $salida = $this->modelo->calendario($conf);
    echo json_encode($salida);
  }    

  function realizar_turno() {
    $id_empresa = parent::get_empresa();
    $id = ($this->input->post("id") !== FALSE) ? $this->input->post("id") : 0;
    $this->db->query("UPDATE turnos SET estado = 1 WHERE id = $id AND id_empresa = $id_empresa");
    echo json_encode(array("error"=>0));
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

  function estadisticas($fecha_desde = "",$fecha_hasta = "") {

    error_reporting(0); // Evita que warnings/deprecations de PHP contaminen la respuesta
    
    @session_start();
    $id_empresa = $this->get_empresa();
    $this->load->helper("fecha_helper");
    $salida = array(
      "total_sesiones"=>0,
      "total_usuarios_nuevos"=> 0,
      "total_usuarios_recurrentes"=> 0,
      "paginas_vistas"=> 0,
      "porcentaje_rebote"=> 0,
      "desktop"=> 0,
      "mobile"=> 0,
      "tablet"=> 0,
      "error"=> "",
      "ciudades"=>array(),
      "fuentes"=>array(),
      "paginas_mas_vistas"=>array(),
      "usuarios"=>array(),
      "sesiones"=>array(),
      "usuarios_nuevos"=>array(),
      "usuarios_recurrentes"=>array(),
    );
    $desde = isset($fecha_desde) ? new DateTime(fecha_mysql($fecha_desde)) : new DateTime("-1 month");
    $hasta = isset($fecha_hasta) ? new DateTime(fecha_mysql($fecha_hasta)) : new DateTime();

    echo json_encode($salida);
  }

}