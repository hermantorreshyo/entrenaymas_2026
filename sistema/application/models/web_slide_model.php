<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Web_Slide_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("web_slider","id","orden ASC",1);
	}
    
	function find($filter,$clave) {
		$this->db->like("linea_2",$filter);
		$this->db->where("clave",$clave);
		$query = $this->db->get($this->tabla);
		$result = $query->result();
		$this->db->close();
		return $result;
	}

	function get_all($limit = null, $offset = null,$order_by = '',$order = '', $clave = "") 
	{
		if (!empty($order_by)) $this->db->order_by($order_by." ".$order);
		else $this->db->order_by($this->order_by);
        
		if ($this->usa_id_empresa == 1) {
			$id_empresa = $this->get_empresa();
			$this->db->where("id_empresa",$id_empresa);			
		}

		$this->db->where("clave",$clave);
		
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

	function insert($data) {
		// Ponemos el orden del elemento en ultimo lugar
		$sql = "SELECT IF(MAX(orden) IS NULL,0,MAX(orden)+1) AS maximo FROM web_slider WHERE id_empresa = $data->id_empresa AND clave = '$data->clave' ORDER BY orden ASC ";
		$q = $this->db->query($sql);
		$row = $q->row();
		$data->orden = $row->maximo;
		return parent::insert($data);
	}	
	
	function save($data) {
		$data->id_empresa = $this->get_empresa();
		return parent::save($data);
	}	

}