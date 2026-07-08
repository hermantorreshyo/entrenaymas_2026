<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Subrubro_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("subrubros","id","nombre");
	}
	
	
	function find($filter = "",$id_rubro = 0) {
		$id_rubro = $this->input->get("id_rubro");
		$this->db->like("nombre",$filter);
		if ($id_rubro != 0) {
			$this->db->where("id_rubro",$id_rubro);
		}
		$query = $this->db->get($this->tabla);
		$result = $query->result();
		$this->db->close();
		return $result;
	}	
	
    
	function get_all($limit = null, $offset = null, $id_rubro = -1) {
		$this->db->order_by($this->order_by);
		if ($id_rubro != -1) {
			$this->db->where("id_rubro",$id_rubro);	
		}
		if (!is_null($limit) && (strlen($limit)>0) && !is_null($offset) && (strlen($offset)>0)) {
			$query = $this->db->get($this->tabla,$offset,$limit);	
		} else {
			$query = $this->db->get($this->tabla);
		}
		$result = $query->result();
		$this->db->close();
		return $result;
	}
	
	
    function update($id,$data) {
        $this->db->where($this->ident,$id);        
        $this->db->update($this->tabla,$data);
        $aff = $this->db->affected_rows();
        $this->db->close();
        return $aff;
    }
    
    function insert($data) {
        $this->db->insert($this->tabla,$data);
        $id = $this->db->insert_id();
        $this->db->close();
        if (!isset($id)) return -1;
        else return $id;
    }
	

}