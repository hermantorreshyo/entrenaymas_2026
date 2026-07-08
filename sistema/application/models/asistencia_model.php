<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Asistencia_Model extends Abstract_Model {
  
  function __construct() {
    parent::__construct("aca_asistencias","id");
  }

  function buscar($conf = array()) {
    
    $asistencias = array();
    $id_empresa = parent::get_empresa();
    $id_comision = isset($conf["id_comision"]) ? $conf["id_comision"] : 0;
    $id_materia = isset($conf["id_materia"]) ? $conf["id_materia"] : 0;
    $fecha = isset($conf["fecha"]) ? $conf["fecha"] : "";
    $this->load->model("Clase_Model");

    $id_clase = 0;
    if ($id_materia != 0) {
      // Buscamos la clase de la materia en particular
      $clase = $this->Clase_Model->get(0,array(
        "id_comision"=>$id_comision,
        "id_materia"=>$id_materia,
        "fecha"=>$fecha,
      ));
      if ($clase === FALSE) {
        return array(
          "error"=>1,
        );
      }      
      $id_clase = $clase->id;
    } else {
      // Controlamos que haya clases de esa comision esa fecha
      $clases = $this->Clase_Model->get_list(array(
        "id_comision"=>$id_comision,
        "fecha"=>$fecha,
      ));
      if (sizeof($clases)==0) {
        return array(
          "error"=>1,
        );        
      }
    }

    $this->load->model("Alumno_Model");
    $alumnos = $this->Alumno_Model->buscar(array(
      "id_comision"=>$id_comision,
      "offset"=>99999,
    ));
    foreach($alumnos["results"] as $al) {

      $sql = "SELECT * FROM aca_asistencias ";
      $sql.= "WHERE id_empresa = $id_empresa ";
      $sql.= "AND id_alumno = $al->id ";
      $sql.= "AND fecha = '$fecha' ";
      if ($id_clase != 0) $sql.= "AND id_clase = $id_clase ";
      $q = $this->db->query($sql);
      if ($q->num_rows()>0) {
        $a = $q->row();
      } else {
        $a = new stdClass();
        $a->condicion = "U"; // No definida
        $a->observaciones = "";
      }
      $asistencias[] = array(
        "id_clase"=>$id_clase,
        "fecha"=>$fecha,
        "id_alumno"=>$al->id,
        "path"=>$al->path,
        "nombre"=>$al->nombre,
        "condicion"=>$a->condicion,
        "observaciones"=>$a->observaciones,
      );
    }
    return array(
      "error"=>0,
      "results"=>$asistencias,
      "id_clase"=>$id_clase,
    );
  }



  function buscar_fechas($conf = array()) {
    
    $asistencias = array();
    $id_empresa = parent::get_empresa();
    $id_comision = isset($conf["id_comision"]) ? $conf["id_comision"] : 0;
    $id_materia = isset($conf["id_materia"]) ? $conf["id_materia"] : 0;
    $fecha_desde = isset($conf["fecha_desde"]) ? $conf["fecha_desde"] : "";
    $fecha_hasta = isset($conf["fecha_hasta"]) ? $conf["fecha_hasta"] : "";
    $fechas = isset($conf["fechas"]) ? $conf["fechas"] : "";
    $this->load->model("Clase_Model");

    // Obtenemos todas las clases entre esas dos fechas
    if (!empty($fecha_desde) && !empty($fecha_hasta)) {
      $begin = new DateTime($fecha_desde);
      $end = new DateTime($fecha_hasta);
      $end->add(new DateInterval('P1D'));
      $interval = DateInterval::createFromDateString('1 day');
      $period = new DatePeriod($begin, $interval, $end);
      $clases = array();
      foreach ( $period as $dt ) {
        $fecha = $dt->format("Y-m-d");
        if ($id_materia != 0) {
          // Buscamos la clase de la materia en particular
          $clase = $this->Clase_Model->get(0,array(
            "id_comision"=>$id_comision,
            "id_materia"=>$id_materia,
            "fecha"=>$fecha,
          ));
          if ($clase === FALSE) continue;
          $clases[] = array(
            "id"=>$clase->id,
            "fecha"=>$dt->format("Y-m-d"),
          );
        } else {
          // Controlamos que haya clases de esa comision esa fecha
          $c = $this->Clase_Model->get_list(array(
            "id_comision"=>$id_comision,
            "fecha"=>$fecha,
          ));
          if (sizeof($c)==0) continue;
          // Agregamos la fecha para buscar
          $clases[] = array(
            "id"=>0,
            "fecha"=>$dt->format("Y-m-d"),
          );
        }
      }      
    } else if (!empty($fechas)) {

      foreach($fechas as $fecha) {
        if ($id_materia != 0) {
          // Buscamos la clase de la materia en particular
          $clase = $this->Clase_Model->get(0,array(
            "id_comision"=>$id_comision,
            "id_materia"=>$id_materia,
            "fecha"=>$fecha,
          ));
          if ($clase === FALSE) continue;
          $clases[] = array(
            "id"=>$clase->id,
            "fecha"=>$fecha,
          );
        } else {
          // Controlamos que haya clases de esa comision esa fecha
          $c = $this->Clase_Model->get_list(array(
            "id_comision"=>$id_comision,
            "fecha"=>$fecha,
          ));
          if (sizeof($c)==0) continue;
          // Agregamos la fecha para buscar
          $clases[] = array(
            "id"=>0,
            "fecha"=>$fecha,
          );
        }
      }

    }

    $this->load->model("Alumno_Model");
    $alumnos = $this->Alumno_Model->buscar(array(
      "id_comision"=>$id_comision,
      "offset"=>99999,
    ));
    $salida = array();
    foreach($alumnos["results"] as $al) {

      $al->clases = array();

      // Recorremos todas las clases
      foreach($clases as $clase) {

        // Buscamos la asistencia en esa clase de ese alumno
        $sql = "SELECT condicion, observaciones FROM aca_asistencias ";
        $sql.= "WHERE id_empresa = $id_empresa ";
        $sql.= "AND id_alumno = $al->id ";
        if ($id_materia == 0) $sql.= "AND fecha = '".$clase["fecha"]."' ";
        else $sql.= "AND id_clase = ".$clase["id"];
        $q = $this->db->query($sql);
        if ($q->num_rows()>0) {
          $a = $q->row();
        } else {
          $a = new stdClass();
          $a->condicion = "-"; // No definida
          $a->observaciones = "";
        }
        $al->clases[] = $a;
      }
      $salida[] = array(
        "id_alumno"=>$al->id,
        "path"=>$al->path,
        "nombre"=>$al->nombre,
        "clases"=>$al->clases,
      );
    }
    return array(
      "error"=>0,
      "results"=>$salida,
      "clases"=>$clases,
    );
  }





  function buscar_docentes($conf = array()) {
    
    $asistencias = array();
    $id_empresa = parent::get_empresa();
    $id_comision = isset($conf["id_comision"]) ? $conf["id_comision"] : 0;
    $id_materia = isset($conf["id_materia"]) ? $conf["id_materia"] : 0;
    $fecha = isset($conf["fecha"]) ? $conf["fecha"] : "";
    $this->load->model("Clase_Model");

    $id_clase = 0;
    if ($id_materia != 0) {
      // Buscamos la clase de la materia en particular
      $clase = $this->Clase_Model->get(0,array(
        "id_comision"=>$id_comision,
        "id_materia"=>$id_materia,
        "fecha"=>$fecha,
      ));
      if ($clase === FALSE) {
        return array(
          "error"=>1,
        );
      }      
      $id_clase = $clase->id;
    } else {
      // Controlamos que haya clases de esa comision esa fecha
      $clases = $this->Clase_Model->get_list(array(
        "id_comision"=>$id_comision,
        "fecha"=>$fecha,
      ));
      if (sizeof($clases)==0) {
        return array(
          "error"=>1,
        );        
      }
    }

    $this->load->model("Docente_Model");
    $docentes = $this->Docente_Model->buscar(array(
      "id_comision"=>$id_comision,
      "offset"=>99999,
    ));
    foreach($docentes["results"] as $al) {

      $sql = "SELECT * FROM aca_asistencias ";
      $sql.= "WHERE id_empresa = $id_empresa ";
      $sql.= "AND id_docente = $al->id ";
      $sql.= "AND fecha = '$fecha' ";
      if ($id_clase != 0) $sql.= "AND id_clase = $id_clase ";
      $q = $this->db->query($sql);
      if ($q->num_rows()>0) {
        $a = $q->row();
      } else {
        $a = new stdClass();
        $a->condicion = "U"; // No definida
        $a->observaciones = "";
      }
      $asistencias[] = array(
        "id_clase"=>$id_clase,
        "fecha"=>$fecha,
        "id_docente"=>$al->id,
        "path"=>$al->path,
        "nombre"=>$al->nombre,
        "condicion"=>$a->condicion,
        "observaciones"=>$a->observaciones,
      );
    }
    return array(
      "error"=>0,
      "results"=>$asistencias,
      "id_clase"=>$id_clase,
    );
  }

  function buscar_docentes_fechas($conf = array()) {
    
    $asistencias = array();
    $id_empresa = parent::get_empresa();
    $id_comision = isset($conf["id_comision"]) ? $conf["id_comision"] : 0;
    $id_materia = isset($conf["id_materia"]) ? $conf["id_materia"] : 0;
    $fecha_desde = isset($conf["fecha_desde"]) ? $conf["fecha_desde"] : "";
    $fecha_hasta = isset($conf["fecha_hasta"]) ? $conf["fecha_hasta"] : "";
    $fechas = isset($conf["fechas"]) ? $conf["fechas"] : "";
    $this->load->model("Clase_Model");

    // Obtenemos todas las clases entre esas dos fechas
    if (!empty($fecha_desde) && !empty($fecha_hasta)) {
      $begin = new DateTime($fecha_desde);
      $end = new DateTime($fecha_hasta);
      $end->add(new DateInterval('P1D'));
      $interval = DateInterval::createFromDateString('1 day');
      $period = new DatePeriod($begin, $interval, $end);
      $clases = array();
      foreach ( $period as $dt ) {
        $fecha = $dt->format("Y-m-d");
        if ($id_materia != 0) {
          // Buscamos la clase de la materia en particular
          $clase = $this->Clase_Model->get(0,array(
            "id_comision"=>$id_comision,
            "id_materia"=>$id_materia,
            "fecha"=>$fecha,
          ));
          if ($clase === FALSE) continue;
          $clases[] = array(
            "id"=>$clase->id,
            "fecha"=>$dt->format("Y-m-d"),
          );
        } else {
          // Controlamos que haya clases de esa comision esa fecha
          $c = $this->Clase_Model->get_list(array(
            "id_comision"=>$id_comision,
            "fecha"=>$fecha,
          ));
          if (sizeof($c)==0) continue;
          // Agregamos la fecha para buscar
          $clases[] = array(
            "id"=>0,
            "fecha"=>$dt->format("Y-m-d"),
          );
        }
      }
    } else if (!empty($fechas)) {
      foreach ($fechas as $fecha) {
        if ($id_materia != 0) {
          // Buscamos la clase de la materia en particular
          $clase = $this->Clase_Model->get(0,array(
            "id_comision"=>$id_comision,
            "id_materia"=>$id_materia,
            "fecha"=>$fecha,
          ));
          if ($clase === FALSE) continue;
          $clases[] = array(
            "id"=>$clase->id,
            "fecha"=>$fecha,
          );
        } else {
          // Controlamos que haya clases de esa comision esa fecha
          $c = $this->Clase_Model->get_list(array(
            "id_comision"=>$id_comision,
            "fecha"=>$fecha,
          ));
          if (sizeof($c)==0) continue;
          // Agregamos la fecha para buscar
          $clases[] = array(
            "id"=>0,
            "fecha"=>$fecha,
          );
        }
      }      
    }

    $this->load->model("Docente_Model");
    $alumnos = $this->Docente_Model->buscar(array(
      "id_comision"=>$id_comision,
      "offset"=>99999,
    ));
    $salida = array();
    foreach($alumnos["results"] as $al) {

      $al->clases = array();

      // Recorremos todas las clases
      foreach($clases as $clase) {

        // Buscamos la asistencia en esa clase de ese alumno
        $sql = "SELECT condicion, observaciones FROM aca_asistencias ";
        $sql.= "WHERE id_empresa = $id_empresa ";
        $sql.= "AND id_docente = $al->id ";
        if ($id_materia == 0) $sql.= "AND fecha = '".$clase["fecha"]."' ";
        else $sql.= "AND id_clase = ".$clase["id"];
        $q = $this->db->query($sql);
        if ($q->num_rows()>0) {
          $a = $q->row();
        } else {
          $a = new stdClass();
          $a->condicion = "-"; // No definida
          $a->observaciones = "";
        }
        $al->clases[] = $a;
      }
      $salida[] = array(
        "id_alumno"=>$al->id,
        "path"=>$al->path,
        "nombre"=>$al->nombre,
        "clases"=>$al->clases,
      );
    }
    return array(
      "error"=>0,
      "results"=>$salida,
      "clases"=>$clases,
    );
  }
  
}