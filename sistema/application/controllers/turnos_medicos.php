<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Turnos_Medicos extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Turno_Medico_Model', 'modelo',"fecha ASC",1);
  }

  function calendario() {
    $conf = array();
    $conf["id_empresa"] = parent::get_empresa();
    $conf["desde"] = $this->input->get("start");
    $conf["hasta"] = $this->input->get("end");
    $conf["id_profesional"] = ($this->input->get("id_profesional") !== FALSE) ? $this->input->get("id_profesional") : 0;
    $conf["id_paciente"] = ($this->input->get("id_paciente") !== FALSE) ? $this->input->get("id_paciente") : 0;
    $salida = $this->modelo->calendario($conf);
    echo json_encode($salida);
  }    

  function realizar_turno() {
    $id_empresa = parent::get_empresa();
    $id = ($this->input->post("id") !== FALSE) ? $this->input->post("id") : 0;
    $this->db->query("UPDATE med_turnos_medicos SET estado = 1 WHERE id = $id AND id_empresa = $id_empresa");
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
    $data->id_paciente = ($this->input->post("id_paciente") !== FALSE) ? $this->input->post("id_paciente") : 0;
    $data->id_profesional = ($this->input->post("id_profesional") !== FALSE) ? $this->input->post("id_profesional") : 0;
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