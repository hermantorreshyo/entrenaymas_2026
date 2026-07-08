<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Chat_Configuracion_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("chat_configuracion","id_empresa","id_empresa ASC",0);
	}
	
	function get($id) {
    $sql = "SELECT * FROM chat_configuracion WHERE id_empresa = $id ";
		$query = $this->db->query($sql);
		return $query->row();
	}
	
	function save($data) {
		unset($data->id);
		$data->id_empresa = $this->get_empresa();
		parent::save($data);
	}	
    
}