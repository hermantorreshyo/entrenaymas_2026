<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Examenes extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Examen_Model', 'modelo');
  }

  function nuevo() {
    $id_materia = ($this->input->post("id_materia") !== FALSE) ? $this->input->post("id_materia") : 0;
    $id_comision = ($this->input->post("id_comision") !== FALSE) ? $this->input->post("id_comision") : 0;
    $r = $this->modelo->get(0,array(
      "id_comision"=>$id_comision,
      "id_materia"=>$id_materia,
    ));
    echo json_encode($r);
  }

  function buscar_fechas() {
    $this->load->helper("fecha_helper");
    $id_comision = $this->input->get("id_comision");
    $id_materia = ($this->input->get("id_materia") !== FALSE) ? $this->input->get("id_materia") : 0;
    $fecha_desde = ($this->input->get("fecha_desde") !== FALSE) ? fecha_mysql(str_replace("/", "-",$this->input->get("fecha_desde"))) : date("Y-m-d");
    $fecha_hasta = ($this->input->get("fecha_hasta") !== FALSE) ? fecha_mysql(str_replace("/", "-",$this->input->get("fecha_hasta"))) : date("Y-m-d");
    $conf = array(
      "id_comision"=>$id_comision,
      "id_materia"=>$id_materia,
      "fecha_desde"=>$fecha_desde,
      "fecha_hasta"=>$fecha_hasta,
    );

    // Si la materia es CERO, estamos buscamos todas las materias
    // Entonces en vez de mostrar como columnas los examenes de una materia en particular
    // Debemos mostrar los promedios finales de cada materia
    // O sea, es el BOLETIN
    if ($id_materia == 0) {
      $r = $this->modelo->boletin($conf);  
    } else {
      $r = $this->modelo->buscar_fechas($conf);  
    }
    echo json_encode($r);
  }

  function imprimir($id) {

    $id_empresa = parent::get_empresa();
    $this->load->helper("fecha_helper");

    $this->load->model("Empresa_Model");
    $empresa = $this->Empresa_Model->get($id_empresa);
    $this->load->model("Web_Configuracion_Model");
    $web_conf = $this->Web_Configuracion_Model->get($empresa->id);
    $empresa = (object) array_merge((array) $empresa, (array) $web_conf);

    $this->load->helper("fecha_helper");
    $r = $this->modelo->get($id);
    $r->header = $this->load->view("reports/academico/header",null,true);
    $r->empresa = $empresa;

    $this->load->model("Materia_Model");
    $r->materia = $this->Materia_Model->get($r->id_materia);

    $this->load->model("Comision_Model");
    $r->comision = $this->Comision_Model->get($r->id_comision);
    $r->fecha = fecha_es($r->fecha);

    $this->load->view("reports/academico/examen",$r);
  }

  function imprimir_entre_fechas() {

    $id_empresa = parent::get_empresa();
    $this->load->model("Empresa_Model");
    $empresa = $this->Empresa_Model->get($id_empresa);
    $this->load->model("Web_Configuracion_Model");
    $web_conf = $this->Web_Configuracion_Model->get($empresa->id);
    $empresa = (object) array_merge((array) $empresa, (array) $web_conf);

    $this->load->helper("fecha_helper");
    $id_comision = $this->input->get("id_comision");
    $id_materia = ($this->input->get("id_materia") !== FALSE) ? $this->input->get("id_materia") : 0;
    $fecha_desde = ($this->input->get("fecha_desde") !== FALSE) ? fecha_mysql(str_replace("/", "-",$this->input->get("fecha_desde"))) : date("Y-m-d");
    $fecha_hasta = ($this->input->get("fecha_hasta") !== FALSE) ? fecha_mysql(str_replace("/", "-",$this->input->get("fecha_hasta"))) : date("Y-m-d");
    $conf = array(
      "id_comision"=>$id_comision,
      "id_materia"=>$id_materia,
      "fecha_desde"=>$fecha_desde,
      "fecha_hasta"=>$fecha_hasta,
    );
    $r = $this->modelo->buscar_fechas($conf);

    $this->load->model("Materia_Model");
    $r["materia"] = $this->Materia_Model->get($id_materia);

    $this->load->model("Comision_Model");
    $r["comision"] = $this->Comision_Model->get($id_comision);
    
    $r["header"] = $this->load->view("reports/academico/header",null,true);
    $r["empresa"] = $empresa;
    $this->load->view("reports/academico/examenes",$r);
  }
    
}