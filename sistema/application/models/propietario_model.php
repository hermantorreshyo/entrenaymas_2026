<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Propietario_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("clientes","id","nombre ASC");
	}

  function get_all($limit = null, $offset = null,$order_by = '',$order = '') {
    
    if (!empty($order_by)) $this->db->order_by($order_by." ".$order);
    else $this->db->order_by($this->order_by);
    $id_empresa = $this->get_empresa();
    $this->db->where("custom_5","1");
    $this->db->where("id_empresa",$id_empresa);      
    if (!is_null($limit) && (strlen($limit)>0) && !is_null($offset) && (strlen($offset)>0)) {
      $query = $this->db->get($this->tabla,$offset,$limit);  
    } else {
      $query = $this->db->get($this->tabla);
    }
    $result = $query->result();
    $this->db->close();
    return $result;
  }
    
	function find($filter) {
		$id_empresa = parent::get_empresa();
		$this->db->where("id_empresa",$id_empresa);
		$this->db->where("custom_5","1");
		$this->db->like("nombre",$filter);
		$query = $this->db->get($this->tabla);
		$result = $query->result();
		$this->db->close();
		return $result;
	}    

}