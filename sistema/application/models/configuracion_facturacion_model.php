<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Configuracion_Facturacion_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("fact_configuracion","id_empresa","id_empresa ASC",1);
	}
	
	function get($id) {
		$id_empresa = $this->get_empresa();
    $sql = "SELECT *, ";
    $sql.= " IF(fecha_inicio != '0000-00-00',DATE_FORMAT(fecha_inicio,'%d/%m/%Y'),'') AS fecha_inicio ";
    $sql.= "FROM fact_configuracion ";
    $sql.= "WHERE id_empresa = $id_empresa ";
    $query = $this->db->query($sql);
    $row = $query->row();
    $this->db->close();
    return $row;
	}
	
	function save($data) {
		unset($data->id);
    $this->load->helper("fecha_helper");
    $data->fecha_inicio = fecha_mysql($data->fecha_inicio);
		parent::save($data);
	}
    
}