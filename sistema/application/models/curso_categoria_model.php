<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Curso_Categoria_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("cursos_categorias","id","nombre ASC");
	}

  function full_link($id_categoria,$conf = array()) {
    $id_empresa = isset($conf["id_empresa"]) ? $conf["id_empresa"] : parent::get_empresa();
    $categorias = array();
    while(TRUE) {
      $sql = "SELECT * FROM cursos_categorias WHERE id = $id_categoria AND id_empresa = $id_empresa ";
      $q = $this->db->query($sql);
      if ($q->num_rows() == 0) break;
      $cat = $q->row();
      $categorias[] = $cat;
      if ($cat->id_padre == 0) break; // Llegamos al final
      $id_categoria = $cat->id_padre;
    }
    $categorias = array_reverse($categorias);
    $link_1 = "";
    $i=1;
    foreach($categorias as $cat) {
      $link_1 .= $cat->link.(($i<sizeof($categorias)) ? "/" : "");
      $i++;
    }
    return array(
      "full_link"=>$link_1,
      "depth"=>sizeof($categorias),
    );
  } 	

  function save($data) {
    $this->load->helper("file_helper");
    $data->link = filename($data->nombre,"-",0);
    $id = parent::save($data);

    // Calculamos el full link
    $full_link_array = $this->full_link($id);
    $full_link = $full_link_array["full_link"];
    $this->db->query("UPDATE cursos_categorias SET full_link = '$full_link' WHERE id = $id AND id_empresa = $data->id_empresa ");

    return $id;
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