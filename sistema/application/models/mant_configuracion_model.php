<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Mant_Configuracion_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("mant_configuracion","id_empresa","id_empresa ASC",1);
	}
	
	function get($id) {
		$id_empresa = $this->get_empresa();
    $sql = "SELECT * ";
    $sql.= "FROM mant_configuracion ";
    $sql.= "WHERE id_empresa = $id_empresa ";
    $query = $this->db->query($sql);
    $row = $query->row();
    $this->db->close();
    return $row;
	}
	
	function save($data) {
		unset($data->id);
		parent::save($data);
	}
    
}