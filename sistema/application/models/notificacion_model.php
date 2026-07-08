<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Notificacion_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("com_log","id");
	}
	
	function get_all($limit = null, $offset = null,$order_by = '',$order = '')  {
		$id_empresa = $this->get_empresa();
		$sql = "SELECT * FROM com_log WHERE id_empresa = $id_empresa AND importancia = 'N' ORDER BY fecha DESC";
		$query = $this->db->query($sql);
		$result = $query->result();
		$this->db->close();
		return $result;
	}	
	
}