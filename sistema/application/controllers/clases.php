<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Clases extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Clase_Model', 'modelo',"fecha ASC",1);
  }
  
  function calendario() {
    $conf = array();
    $conf["id_empresa"] = parent::get_empresa();
    $conf["desde"] = $this->input->get("start");
    $conf["hasta"] = $this->input->get("end");
    $conf["id_docente"] = ($this->input->get("id_docente") !== FALSE) ? $this->input->get("id_docente") : 0;
    $conf["id_comision"] = ($this->input->get("id_comision") !== FALSE) ? $this->input->get("id_comision") : 0;
    $salida = $this->modelo->calendario($conf);
    echo json_encode($salida);
  }    

}