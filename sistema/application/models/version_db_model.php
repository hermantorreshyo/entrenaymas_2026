<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Version_Db_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("com_versiones_db","id","id DESC",0);
	}
    
	function find($filter) {
		$this->db->where("id",$filter);
		$query = $this->db->get($this->tabla);
		$result = $query->result();
		$this->db->close();
		return $result;
	}
	
	function insert($data) {
		$this->db->insert($this->tabla,$data);
		$id = $this->db->insert_id();
		if (!isset($id)) return -1;
		else {
			
			// Cuando insertamos un nuevo script SQL
			
			// Como ultimo comando, le agregamos la actualizacion de numero de version
			$sql = "UPDATE com_configuracion SET version_db = $id WHERE id = 1";
			$data->texto.= "\r\n$sql;";
			$this->db->query("UPDATE com_versiones_db SET texto = '$data->texto' WHERE id = $id ");
			
			// Cambiamos el numero de version de la base de datos
			$this->db->query($sql);
			
			return $id;
		}
	}	

}