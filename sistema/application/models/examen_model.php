<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Examen_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("aca_examenes","id","id DESC");
	}

  function save($data) {
    $id_empresa = parent::get_empresa();
    $notas = $data->notas;
    unset($data->notas);
    $this->load->helper("fecha_helper");
    $data->fecha = fecha_mysql($data->fecha);
    $id_examen = parent::save($data);

    foreach($notas as $asis) {
      $asis->id_examen = $id_examen;
      $asis->id_empresa = $id_empresa;
      $sql = "SELECT * FROM aca_notas_alumnos ";
      $sql.= "WHERE id_empresa = $id_empresa ";
      $sql.= "AND id_examen = $id_examen ";
      $sql.= "AND id_alumno = $asis->id_alumno ";
      $sql.= "LIMIT 0,1 ";
      $q = $this->db->query($sql);
      if ($q->num_rows()>0) {
        $r = $q->row();
        $this->db->where(array(
          "id"=>$r->id,
          "id_empresa"=>$r->id_empresa,
        ));
        $this->db->update("aca_notas_alumnos",$asis);
      } else {
        $this->db->insert("aca_notas_alumnos",$asis);  
      }
    }
    return $id_examen;
  }
	
	function find($filter) {
		$id_empresa = parent::get_empresa();
		$this->db->where("id_empresa",$id_empresa);
		$this->db->like("nombre",$filter);
		$query = $this->db->get($this->tabla);
		$result = $query->result();
		$this->db->close();
		return $result;
	} 

  function buscar_fechas($conf = array()) {
    
    $id_empresa = parent::get_empresa();
    $id_comision = isset($conf["id_comision"]) ? $conf["id_comision"] : 0;
    $id_materia = isset($conf["id_materia"]) ? $conf["id_materia"] : 0;
    $fecha_desde = isset($conf["fecha_desde"]) ? $conf["fecha_desde"] : "";
    $fecha_hasta = isset($conf["fecha_hasta"]) ? $conf["fecha_hasta"] : "";

    // Obtenemos todos los examenes entre esas fechas
    $begin = new DateTime($fecha_desde);
    $end = new DateTime($fecha_hasta);
    $end->add(new DateInterval('P1D'));
    $interval = DateInterval::createFromDateString('1 day');
    $period = new DatePeriod($begin, $interval, $end);
    $examenes = array();
    foreach ( $period as $dt ) {
      $fecha = $dt->format("Y-m-d");
      $sql = "SELECT * FROM aca_examenes ";
      $sql.= "WHERE id_empresa = $id_empresa ";
      $sql.= "AND id_materia = $id_materia ";
      $sql.= "AND id_comision = $id_comision ";
      $sql.= "AND fecha = '$fecha' ";
      $q = $this->db->query($sql);
      if ($q->num_rows()>0) {
        foreach($q->result() as $r) {
          $r->fecha = $dt->format("d/m/Y");
          $examenes[] = $r;
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

      $al->examenes = array();

      // Recorremos todos los examenes
      foreach($examenes as $examen) {

        // Buscamos la nota de ese alumno en ese examen
        $sql = "SELECT valor, observaciones FROM aca_notas_alumnos ";
        $sql.= "WHERE id_empresa = $id_empresa ";
        $sql.= "AND id_alumno = $al->id ";
        $sql.= "AND id_examen = ".$examen->id;
        $q = $this->db->query($sql);
        if ($q->num_rows()>0) {
          $a = $q->row();
        } else {
          $a = new stdClass();
          $a->valor = "-"; // No definida
          $a->observaciones = "";
        }
        $a->id_examen = $examen->id;
        $al->examenes[] = $a;
      }
      $salida[] = array(
        "id_alumno"=>$al->id,
        "path"=>$al->path,
        "nombre"=>$al->nombre,
        "examenes"=>$al->examenes,
      );
    }
    return array(
      "error"=>0,
      "results"=>$salida,
      "examenes"=>$examenes,
    );
  }


  function get($id = 0,$conf = array()) {

    $id_comision = isset($conf["id_comision"]) ? $conf["id_comision"] : 0;
    $id_materia = isset($conf["id_materia"]) ? $conf["id_materia"] : 0;
    $id_empresa = parent::get_empresa();

    // Debemos crear uno nuevo
    if ($id == 0) {
      $row = new stdClass();
      $row->id = 0;
      $row->id_comision = $id_comision;
      $row->id_materia = $id_materia;
      $row->id_empresa = $id_empresa;
      $row->nombre = "";
      $row->id_docente = 0;
      $row->numerico = 1;
      $row->aprueba_con = 7;
      $row->utilizada_en_promedio = 1;
      $row->cerrada = 0;
      $row->fecha = date("Y-m-d");

    } else {
      $sql = "SELECT E.* ";
      $sql.= "FROM aca_examenes E ";
      $sql.= "INNER JOIN aca_comisiones C ON (E.id_comision = C.id AND E.id_empresa = C.id_empresa) ";
      $sql.= "INNER JOIN aca_materias M ON (E.id_materia = M.id AND E.id_empresa = M.id_empresa) ";
      $sql.= "WHERE E.id_empresa = $id_empresa ";
      $sql.= "AND E.id = $id ";
      $q = $this->db->query($sql);
      if ($q->num_rows() == 0) return FALSE;
      $row = $q->row();
    }

    $this->load->model("Alumno_Model");
    $alumnos = $this->Alumno_Model->buscar(array(
      "id_comision"=>$row->id_comision,
      "offset"=>99999,
    ));
    $row->notas = array();
    foreach($alumnos["results"] as $al) {

      $sql = "SELECT valor, observaciones FROM aca_notas_alumnos ";
      $sql.= "WHERE id_empresa = $id_empresa ";
      $sql.= "AND id_alumno = $al->id ";
      $sql.= "AND id_examen = ".$id;
      $q = $this->db->query($sql);
      if ($q->num_rows()>0) {
        $a = $q->row();
      } else {
        $a = new stdClass();
        $a->valor = "-"; // No definida
        $a->observaciones = "";
      }
      $row->notas[] = array(
        "id_examen"=>$id,
        "id_alumno"=>$al->id,
        "path"=>$al->path,
        "nombre"=>$al->nombre,
        "valor"=>$a->valor,
        "observaciones"=>$a->observaciones,
      );
    }
    return $row;
  }



  function boletin($conf = array()) {
    
    $id_empresa = parent::get_empresa();
    $id_comision = isset($conf["id_comision"]) ? $conf["id_comision"] : 0;
    $fecha_desde = isset($conf["fecha_desde"]) ? $conf["fecha_desde"] : "";
    $fecha_hasta = isset($conf["fecha_hasta"]) ? $conf["fecha_hasta"] : "";
    $materias = array();

    // Obtenemos el listado de alumnos
    $this->load->model("Alumno_Model");
    $alumnos = $this->Alumno_Model->buscar(array(
      "id_comision"=>$id_comision,
      "offset"=>99999,
    ));

    // Obtenemos las materias que hay examenes
    // Entre las fechas seleccionadas
    $sql = "SELECT DISTINCT id_materia ";
    $sql.= "FROM aca_examenes C ";
    $sql.= "WHERE C.id_empresa = $id_empresa ";
    $sql.= "AND C.id_comision = '$id_comision' ";
    $sql.= "AND '$fecha_desde' <= C.fecha ";
    $sql.= "AND C.fecha <= '$fecha_hasta' ";
    $q = $this->db->query($sql);
    $this->load->model("Materia_Model");
    foreach($q->result() as $r) {
      $materia = $this->Materia_Model->get($r->id_materia);
      $materias[] = $materia;
    }

    $salida = array(
      "alumnos"=>array(),
      "materias"=>$materias,
      "error"=>0,
    );

    // Recorremos los alumnos, y vamos calculando su promedio en cada materia
    foreach($alumnos["results"] as $al) {

      $al->notas = array();
      foreach($materias as $materia) {

        // Calculamos el promedio de los examenes para esa materia
        $sql = "SELECT N.valor ";
        $sql.= "FROM aca_examenes E ";
        $sql.= "INNER JOIN aca_notas_alumnos N ON (E.id = N.id_examen AND E.id_empresa = N.id_empresa) ";
        $sql.= "WHERE E.id_empresa = $id_empresa ";
        $sql.= "AND E.id_comision = $id_comision ";
        $sql.= "AND E.id_materia = $materia->id ";
        $sql.= "AND E.utilizada_en_promedio = 1 ";
        $sql.= "AND '$fecha_desde' <= E.fecha ";
        $sql.= "AND E.fecha <= '$fecha_hasta' ";
        $sql.= "AND N.id_alumno = $al->id ";
        $qq = $this->db->query($sql);
        $total = 0;
        foreach($qq->result() as $rr) {
          $total += (is_numeric($rr->valor)) ? $rr->valor : 0;
        }
        $promedio = ($qq->num_rows()>0) ? round($total / $qq->num_rows(),2) : 0;
        $al->notas[] = array(
          "id_materia"=>$materia->id,
          "promedio"=>$promedio,
        );
      }
      $salida["alumnos"][] = $al;
    }
    return $salida;
  }

}