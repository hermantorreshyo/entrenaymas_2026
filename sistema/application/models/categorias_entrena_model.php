<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Categorias_Entrena_Model extends Abstract_Model {
  
  function __construct() {
    parent::__construct("categorias_entrena","id","nombre ASC");
  }
    
  function find($filter) {
    $this->db->like("nombre",$filter);
    $query = $this->db->get($this->tabla);
    $result = $query->result();
    $this->db->close();
    return $result;
  }
  
  function get($id,$id_empresa=0) {
    if ($id_empresa == 0) $id_empresa = parent::get_empresa();
    $sql = "SELECT * FROM categorias_entrena ";
    $sql.= "WHERE id = '$id' AND id_empresa = $id_empresa ";
    $query = $this->db->query($sql);
    if ($query->num_rows() == 0) return FALSE;
    $row = $query->row(); 
    $this->db->close();
    return $row;
  }
}