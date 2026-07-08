<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Asunto_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("crm_asuntos","id","orden ASC, nombre ASC");
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

  function get($id) {
    $id_empresa = $this->get_empresa();
    $sql = "SELECT * FROM crm_asuntos WHERE id = $id AND (id_empresa = $id_empresa OR id_empresa = 0)";
    $query = $this->db->query($sql);
    $row = $query->row(); 
    $this->db->close();
    return $row;
  }  

  function get_all($limit = null, $offset = null,$order_by = '',$order = '') {
    
    if (!empty($order_by)) $this->db->order_by($order_by." ".$order);
    else $this->db->order_by($this->order_by);

    $id_empresa = $this->get_empresa();
    $this->db->where("id_empresa",$id_empresa);      
    $this->db->or_where("id_empresa",0);

    // Si no son NULL y tienen algun valor
    // Nota: No use empty($var) porque si $limit puede ser 0,
    // entonces empty("0") = TRUE y esta mal eso, porque tiene que paginar
    if (!is_null($limit) && (strlen($limit)>0) && !is_null($offset) && (strlen($offset)>0)) {
      $query = $this->db->get($this->tabla,$offset,$limit);  
    } else {
      $query = $this->db->get($this->tabla);
    }
    $result = $query->result();
    $this->db->close();
    return $result;
  }

}