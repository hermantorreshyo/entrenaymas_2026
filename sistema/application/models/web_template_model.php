<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Web_Template_Model extends Abstract_Model {
	
	private $config;
	
	function __construct() {
		parent::__construct("web_templates","id","nombre ASC",0);
	}
    
	function find($filter) {
		$this->db->like("nombre",$filter);
		$query = $this->db->get($this->tabla);
		$result = $query->result();
		$this->db->close();
		return $result;
	}
	
	function save($data) {
		$this->config = $data->config;
		unset($data->config);
		parent::save($data);
	}
	
	function post_save($id) {
		
		$this->db->query("DELETE FROM web_templates_config WHERE id_template = $id");
		$lineas = explode(";",$this->config);
		foreach($lineas as $l) {
			$l = trim($l);
			if (empty($l)) continue;
			if (strpos($l,"=")>0) {
				$campos = explode("=",$l);
				$clave = trim($campos[0]);
				$valor = trim($campos[1]);
				if (empty($clave)) continue;
				$this->db->query("INSERT INTO web_templates_config (id_template, clave, valor) VALUES ($id,'$clave','$valor')");
			}
		}
	}
	
	function get($id) {
		$q = $this->db->query("SELECT * FROM web_templates WHERE id = $id");
		$row = $q->row();
		$qq = $this->db->query("SELECT * FROM web_templates_config WHERE id_template = $id");
		$config = "";
		foreach($qq->result() as $r) {
			$config.= "$r->clave = $r->valor ;\n";
		}
		$row->config = $config;
		return $row;
	}
}