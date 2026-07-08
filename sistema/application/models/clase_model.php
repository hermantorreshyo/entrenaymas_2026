<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Clase_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("aca_clases","id","fecha DESC",1);
	}

  function get($id = 0,$config=array()) {
    $id_empresa = parent::get_empresa();
    $id_comision = isset($config["id_comision"]) ? $config["id_comision"] : 0;
    $id_materia = isset($config["id_materia"]) ? $config["id_materia"] : 0;
    $fecha = isset($config["fecha"]) ? $config["fecha"] : "";
    $sql = "SELECT * FROM aca_clases ";
    $sql.= "WHERE id_empresa = $id_empresa ";
    if ($id != 0) $sql.= "AND id = $id ";
    if ($id_comision) $sql.= "AND id_comision = $id_comision ";
    if ($id_materia) $sql.= "AND id_materia = $id_materia ";
    if ($fecha) $sql.= "AND fecha = '$fecha' ";
    $q = $this->db->query($sql);
    return ($q->num_rows() > 0) ? $q->row() : FALSE;
  }

  function get_list($config=array()) {
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $id_comision = isset($config["id_comision"]) ? $config["id_comision"] : 0;
    $id_materia = isset($config["id_materia"]) ? $config["id_materia"] : 0;
    $fecha = isset($config["fecha"]) ? $config["fecha"] : "";
    $sql = "SELECT * FROM aca_clases ";
    $sql.= "WHERE id_empresa = $id_empresa ";
    if ($id_comision) $sql.= "AND id_comision = $id_comision ";
    if ($id_materia) $sql.= "AND id_materia = $id_materia ";
    if ($fecha) $sql.= "AND fecha = '$fecha' ";
    $q = $this->db->query($sql);
    return $q->result();
  }

  function save($data) {
    $this->load->helper("fecha_helper");
    $repetir = (isset($data->repetir)) ? $data->repetir : 0;
    $fecha_hasta = (isset($data->fecha_hasta)) ? fecha_mysql($data->fecha_hasta) : "";
    $data->fecha = fecha_mysql($data->fecha);
    $id = parent::save($data);
    if ($repetir > 0 && $fecha_hasta != "") {
      if ($repetir == 1) $in = "P1W";
      else if ($repetir == 2) $in = "P2W";
      else if ($repetir == 4) $in = "P1M";
      else return $id;
      $interval = new DateInterval($in);
      $desde = new DateTime($data->fecha);
      $hasta = new DateTime(fecha_mysql($fecha_hasta));
      $period = new DatePeriod($desde, $interval, $hasta);
      $i=0;
      foreach($period as $dt) {
        if ($i!=0) {
          $data->id_clase_padre = $id;
          $data->fecha = $dt->format("Y-m-d");
          parent::save($data);
        }
        $i++;
      }
    }
    return $id;
  }

  function delete($id) {
    $id_empresa = parent::get_empresa();
    $this->db->query("DELETE FROM aca_clases WHERE id_empresa = $id_empresa AND id_clase_padre = $id ");
    $this->db->query("DELETE FROM aca_clases WHERE id_empresa = $id_empresa AND id = $id ");
  }
  
	function calendario($conf = array()) {
		$id_empresa = isset($conf["id_empresa"])?$conf["id_empresa"]:parent::get_empresa();
		$fecha_desde = isset($conf["desde"])?$conf["desde"]:"";
		$fecha_hasta = isset($conf["hasta"])?$conf["hasta"]:"";
		$id_comision = isset($conf["id_comision"])?$conf["id_comision"]:0;
		$id_docente = isset($conf["id_docente"])?$conf["id_docente"]:0;
		$sql = "SELECT C.*, ";
		if (!empty($id_comision)) {
			// Si estamos filtrando por comision, mostramos las materias
			$sql.= " M.nombre AS title, CONCAT('Prof: ',P.apellido,' ',P.nombre) AS description, M.color AS backgroundColor, M.color AS borderColor, ";
		} else if (!empty($id_docente)) {
			// Si estamos filtrando por docente, mostramos las comisiones
			$sql.= " CO.nombre AS title, CONCAT('Materia: ',M.nombre) AS description, ";
		} else {
			$sql.= " M.nombre AS title, ";
		}
		$sql.= " CONCAT(C.fecha,' ',C.hora) AS start, ";
		$sql.= " (IF(C.duracion_tipo = 'H',DATE_ADD((CONCAT(C.fecha,' ',C.hora)),INTERVAL C.duracion_cantidad*60 MINUTE), DATE_ADD((CONCAT(C.fecha,' ',C.hora)),INTERVAL C.duracion_cantidad MINUTE))) AS end ";
		$sql.= "FROM aca_clases C ";
		$sql.= "INNER JOIN aca_materias M ON (C.id_materia = M.id AND C.id_empresa = M.id_empresa) ";
		$sql.= "INNER JOIN aca_comisiones CO ON (C.id_comision = CO.id AND CO.id_empresa = C.id_empresa) ";
		$sql.= "INNER JOIN aca_docentes P ON (C.id_docente = P.id_cliente AND C.id_empresa = P.id_empresa) ";
		$sql.= "WHERE 1=1 ";
		if (!empty($id_empresa)) $sql.= "AND C.id_empresa = $id_empresa ";
		if (!empty($id_comision)) $sql.= "AND C.id_comision = $id_comision ";
		if (!empty($id_docente)) $sql.= "AND C.id_docente = $id_docente ";
		if (!empty($fecha_desde)) $sql.= "AND '$fecha_desde' <= C.fecha ";
		if (!empty($fecha_hasta)) $sql.= "AND C.fecha <= '$fecha_hasta' ";
		$q = $this->db->query($sql);
		return $q->result();
	}
}