<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Conferencista_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("conferencistas","id","nombre ASC");
	}

  function save($data) {
    $this->load->helper("fecha_helper");
    $data->fecha = fecha_mysql($data->fecha);
    return parent::save($data);
  }

  function get($id,$config = array()) {

    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    
    // Obtenemos los datos del entrada
    $id = (int)$id;
    $sql = "SELECT A.*, ";
    $sql.= "  IF(A.fecha='0000-00-00 00:00:00','',DATE_FORMAT(A.fecha,'%d/%m/%Y %H:%i')) AS fecha ";
    $sql.= "FROM conferencistas A ";
    $sql.= "WHERE A.id = $id ";
    $sql.= "AND A.id_empresa = $id_empresa ";
    $q = $this->db->query($sql);
    if ($q->num_rows() == 0) return array();
    $entrada = $q->row();
    return $entrada;
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

}