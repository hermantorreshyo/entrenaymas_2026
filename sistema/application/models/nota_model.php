<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Nota_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("aca_asistencias","id");
	}
	
	function buscar($conf = array()) {
		
		$id_empresa = parent::get_empresa();
		$id_comision = isset($conf["id_comision"]) ? $conf["id_comision"] : 0;
		$id_materia = isset($conf["id_materia"]) ? $conf["id_materia"] : 0;

		$salida = array(
			"content"=>array(),
			"header"=>array(),
		);

		// Obtenemos los alumnos de la comision
		$sql = "SELECT * FROM aca_alumnos WHERE id_comision = $id_comision ";
		$q = $this->db->query($sql);
		$alumnos = $q->result();

		// Obtenemos las notas definidas para esa comision/materia
		$sql = "SELECT id, nombre, numerico, aprueba_con, valores, cerrada, utilizada_en_promedio ";
		$sql.= "FROM aca_notas_conceptos WHERE 1=1 ";
		$sql.= "AND id_comision = $id_comision ";
		if (!empty($id_materia)) $sql.= "AND id_materia = $id_materia ";
		$q = $this->db->query($sql);
		$conceptos = array();
		foreach($q->result() as $concepto) {
			$salida["header"][] = array(
				"id"=>$concepto->id,
				"nombre"=>$concepto->nombre,
				"cerrada"=>$concepto->cerrada,
			);
			$conceptos[] = $concepto;
		}

		foreach($alumnos as $alumno) {
			$notas = array();
			foreach($conceptos as $concepto) {
				$sql = "SELECT * FROM aca_notas WHERE id_alumno = $alumno->id AND id_nota_concepto = $concepto->id ";
				$q = $this->db->query($sql);
				if ($q->num_rows()>0) {
					$a = $q->row();
				} else {
					$a = new stdClass();
					$a->valor = ""; // No definida
				}
				$notas[] = array(
					"id_nota_concepto"=>$concepto->id,
					"valor"=>$a->valor,
					"numerico"=>$concepto->numerico,
					"valores"=>$concepto->valores,
					"en_prom"=>$concepto->utilizada_en_promedio,
				);
			}
			$salida["content"][] = array(
				"id_alumno"=>$alumno->id,
				"alumno"=>$alumno->apellido." ".$alumno->nombre,
				"notas"=>$notas,
			);
		}
		return array(
            "results"=>$salida,
		);
	}

	function save_or_update($s) {
		$id_empresa = parent::get_empresa();
		$sql = "SELECT * FROM aca_notas WHERE 1=1 ";
		$sql.= "AND id_alumno = ".$s["id_alumno"]." ";
		$sql.= "AND id_nota_concepto = ".$s["id_nota_concepto"]." ";
		$q = $this->db->query($sql);
		if ($q->num_rows($sql)>0) {
			$r = $q->row();
			$sql = "UPDATE aca_notas SET valor = '".$s["valor"]."' ";
			$sql.= "WHERE id_alumno = ".$s["id_alumno"]." ";
			$sql.= "AND id_nota_concepto = ".$s["id_nota_concepto"]." ";
		} else {
			$sql = "INSERT INTO aca_notas (id_empresa,id_alumno,id_nota_concepto,valor) VALUES (";
			$sql.= "'$id_empresa','".$s["id_alumno"]."','".$s["id_nota_concepto"]."','".$s["valor"]."')";
		}
		$this->db->query($sql);
	}
	
}