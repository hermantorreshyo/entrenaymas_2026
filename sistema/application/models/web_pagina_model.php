<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Web_Pagina_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("web_paginas","id","orden ASC",1);
	}
    
	function find($filter) {
		$this->db->like("titulo_es",$filter);
		$query = $this->db->get($this->tabla);
		$result = $query->result();
		$this->db->close();
		return $result;
	}
	
	function get_all($limit = null, $offset = null,$order_by = '',$order = '') {
		$id_empresa = $this->get_empresa();
		$sql = "SELECT P.*, ";
		$sql.= " IF(CP.id IS NULL,'',CP.nombre_es) AS categoria ";
		$sql.= "FROM web_paginas P LEFT JOIN web_categorias CP ON (P.id_categoria = CP.id) ";
		$sql.= "WHERE P.id_empresa = $id_empresa AND P.id_proyecto != 0 ";
		$query = $this->db->query($sql);
		$result = $query->result();
		$this->db->close();
		return $result;
	}	
	
	function save($data) {
		unset($data->categoria);
		$data->id_empresa = $this->get_empresa();
		parent::save($data);
	}
	
	function post_save($id) {
		$pagina = $this->get($id);
		$this->load->helper("file_helper");
		
		$this->load->model("Web_Categoria_Model");
		$categoria = $this->Web_Categoria_Model->get($pagina->id_categoria);
		
		$data = array(
			"link"=>"pagina".((!empty($categoria)) ? $categoria->link:"/").filename($pagina->titulo_es,"-",0)."-".$id."/"
		);
		$this->db->where(array("id"=>$id));
		$this->db->update("web_paginas",$data);
	}

}