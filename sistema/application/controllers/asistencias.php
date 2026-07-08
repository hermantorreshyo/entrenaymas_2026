<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Asistencias extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Asistencia_Model', 'modelo');
  }
  
  function buscar() {
    $this->load->helper("fecha_helper");
    $id_comision = $this->input->get("id_comision");
    $id_materia = ($this->input->get("id_materia") !== FALSE) ? $this->input->get("id_materia") : 0;
    $fecha = ($this->input->get("fecha") !== FALSE) ? fecha_mysql(str_replace("/", "-",$this->input->get("fecha"))) : date("Y-m-d");
    $conf = array(
      "id_comision"=>$id_comision,
      "id_materia"=>$id_materia,
      "fecha"=>$fecha,
    );
    $r = $this->modelo->buscar($conf);
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
    $r = $this->modelo->buscar_fechas($conf);
    echo json_encode($r);
  }


  function imprimir() {

    $id_empresa = parent::get_empresa();
    $this->load->model("Empresa_Model");
    $empresa = $this->Empresa_Model->get($id_empresa);
    $this->load->model("Web_Configuracion_Model");
    $web_conf = $this->Web_Configuracion_Model->get($empresa->id);
    $empresa = (object) array_merge((array) $empresa, (array) $web_conf);

    $this->load->helper("fecha_helper");
    $id_comision = $this->input->get("id_comision");
    $id_materia = ($this->input->get("id_materia") !== FALSE) ? $this->input->get("id_materia") : 0;
    $fecha = ($this->input->get("fecha") !== FALSE) ? fecha_mysql(str_replace("/", "-",$this->input->get("fecha"))) : date("Y-m-d");
    $conf = array(
      "id_comision"=>$id_comision,
      "id_materia"=>$id_materia,
      "fecha"=>$fecha,
    );
    $r = $this->modelo->buscar($conf);

    $r["id_materia"] = $id_materia;
    if ($id_materia != 0) {
      $this->load->model("Materia_Model");
      $r["materia"] = $this->Materia_Model->get($id_materia);
    }
    $this->load->model("Comision_Model");
    $r["comision"] = $this->Comision_Model->get($id_comision);

    $r["header"] = $this->load->view("reports/academico/header",null,true);
    $r["empresa"] = $empresa;
    $this->load->view("reports/academico/asistencia",$r);
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

    $fechas2 = array();
    if ($this->input->get("fechas") !== FALSE) {
      $fechas = $this->input->get("fechas");
      $fechas = explode("--", $fechas);
      foreach($fechas as $f) {
        $f = fecha_mysql(str_replace("-", "/", $f)."/".date("Y"));
        $fechas2[] = $f;
      }
    }
    //$fecha_desde = ($this->input->get("fecha_desde") !== FALSE) ? fecha_mysql(str_replace("/", "-",$this->input->get("fecha_desde"))) : date("Y-m-d");
    //$fecha_hasta = ($this->input->get("fecha_hasta") !== FALSE) ? fecha_mysql(str_replace("/", "-",$this->input->get("fecha_hasta"))) : date("Y-m-d");
    $conf = array(
      "id_comision"=>$id_comision,
      "id_materia"=>$id_materia,
      "fechas"=>$fechas2,
      //"fecha_desde"=>$fecha_desde,
      //"fecha_hasta"=>$fecha_hasta,
    );
    $r = $this->modelo->buscar_fechas($conf);

    $r["id_materia"] = $id_materia;
    if ($id_materia != 0) {
      $this->load->model("Materia_Model");
      $r["materia"] = $this->Materia_Model->get($id_materia);
    }

    $this->load->model("Comision_Model");
    $r["comision"] = $this->Comision_Model->get($id_comision);

    $r["header"] = $this->load->view("reports/academico/header",null,true);
    $r["empresa"] = $empresa;
    $this->load->view("reports/academico/asistencias",$r);
  }

  function buscar_docentes() {
    $this->load->helper("fecha_helper");
    $id_comision = ($this->input->get("id_comision") !== FALSE) ? $this->input->get("id_comision") : 0;
    $id_materia = ($this->input->get("id_materia") !== FALSE) ? $this->input->get("id_materia") : 0;
    $fecha = ($this->input->get("fecha") !== FALSE) ? fecha_mysql(str_replace("/", "-",$this->input->get("fecha"))) : date("Y-m-d");
    $conf = array(
      "id_comision"=>$id_comision,
      "id_materia"=>$id_materia,
      "fecha"=>$fecha,
    );
    $r = $this->modelo->buscar_docentes($conf);
    echo json_encode($r);
  }

  function buscar_docentes_fechas() {
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
    $r = $this->modelo->buscar_docentes_fechas($conf);
    echo json_encode($r);
  }

  function imprimir_docentes_entre_fechas() {

    $id_empresa = parent::get_empresa();
    $this->load->model("Empresa_Model");
    $empresa = $this->Empresa_Model->get($id_empresa);
    $this->load->model("Web_Configuracion_Model");
    $web_conf = $this->Web_Configuracion_Model->get($empresa->id);
    $empresa = (object) array_merge((array) $empresa, (array) $web_conf);

    $this->load->helper("fecha_helper");
    $fechas2 = array();
    if ($this->input->get("fechas") !== FALSE) {
      $fechas = $this->input->get("fechas");
      $fechas = explode("--", $fechas);
      foreach($fechas as $f) {
        $f = fecha_mysql(str_replace("-", "/", $f)."/".date("Y"));
        $fechas2[] = $f;
      }
    }
    $conf = array(
      "fechas"=>$fechas2,
    );
    $r = $this->modelo->buscar_docentes_fechas($conf);
    $r["header"] = $this->load->view("reports/academico/header",null,true);
    $r["empresa"] = $empresa;
    $r["es_docente"] = 1;
    $this->load->view("reports/academico/asistencias",$r);
  }

  function guardar() {
    $id_empresa = parent::get_empresa();
    $asistencias = json_decode($this->input->post("asistencias"));
    $id_clase = ($this->input->post("id_clase"));

    foreach($asistencias as $asis) {
      $asis->id_clase = $id_clase;
      $asis->id_empresa = $id_empresa;

      $sql = "SELECT * FROM aca_asistencias ";
      $sql.= "WHERE id_empresa = $id_empresa ";
      $sql.= "AND id_clase = $id_clase ";
      if (isset($asis->id_alumno)) $sql.= "AND id_alumno = $asis->id_alumno ";
      if (isset($asis->id_docente)) $sql.= "AND id_docente = $asis->id_docente ";
      if (isset($asis->fecha)) $sql.= "AND fecha = '$asis->fecha' ";
      $sql.= "LIMIT 0,1 ";
      $q = $this->db->query($sql);
      if ($q->num_rows()>0) {
        $r = $q->row();
        $this->db->where(array(
          "id"=>$r->id,
          "id_empresa"=>$r->id_empresa,
        ));
        $this->db->update("aca_asistencias",$asis);
      } else {
        $this->db->insert("aca_asistencias",$asis);  
      }
    }
    echo json_encode(array("error"=>0));
  }
  
}
