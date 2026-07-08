<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Chequera_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("chequeras","id");
	}
	
	/**
	 * Devuelve todos los registros de la tabla
	 * @return Lista de registros
	 */
	function get_all($limit = null, $offset = null, $id_banco = -1) {
		$id_empresa = parent::get_empresa();
		$sql = "SELECT C.*, B.nombre AS banco ";
		$sql.= "FROM chequeras C INNER JOIN bancos B ON (C.id_banco = B.id) ";
		$sql.= "WHERE id_empresa = $id_empresa ";
		if ($id_banco != -1) {
		    $sql.= "AND id_banco = $id_banco ";
		}
		$sql.= "ORDER BY fecha DESC, id DESC ";
		if (!is_null($limit) && (strlen($limit)>0) && !is_null($offset) && (strlen($offset)>0)) {
		    $sql.= "LIMIT $limit, $offset";
		}
		$query = $this->db->query($sql);
		$result = $query->result();
		$this->db->close();
		return $result;
	}
	
	
	function insert($data) {

		// Insertamos la chequera
		$id_empresa = parent::get_empresa();
		unset($data->banco);
		$data->fecha = date("Y-m-d");
		
		// Controlamos si la chequera existe
		$sql = "SELECT * FROM chequeras WHERE ";
		$sql.= "id_banco = $data->id_banco ";
		$sql.= "AND numero_desde = $data->numero_desde ";
		$sql.= "AND numero_hasta = $data->numero_hasta ";
		$sql.= "AND numero = '$data->numero' ";
		$q = $this->db->query($sql);
		if ($q->num_rows()>0) {
			// La chequera ya esta cargada
			return -1;
		}
		
		$diferencia = $data->numero_hasta - $data->numero_desde;
		if ( ($diferencia > 100) || ($diferencia <= 0) ) {
			// La diferencia es enorme
			return -1;
		}
		
		// Cargamos la chequera
		$insert_id = $this->db->insert("chequeras",$data);
		
		// Al insertar una chequera, se insertan
		// todos los cheques que pertenecen a esos numeros
		for($i = $data->numero_desde ; $i <= $data->numero_hasta; $i++ ) {
			$sql = "INSERT INTO cheques ";
			$sql.= "(id_chequera,numero,titular,id_banco,tipo,id_empresa) ";
			$sql.= "VALUES (";
			$sql.= "$insert_id,$i,'',$data->id_banco,'P',$id_empresa) ";
			$this->db->query($sql);
		}
		$this->db->close();
		if (!isset($insert_id)) return -1;
		else return $insert_id;
	}

	function update($id,$data) {
		unset($data->banco);
		return parent::update($id,$data);
	}
	
}