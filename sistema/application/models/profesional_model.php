<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Profesional_Model extends Abstract_Model {

  function __construct() {
    parent::__construct("med_profesionales","id","nombre ASC");
  }
  
  function save($data) {
    $data->id_empresa = parent::get_empresa();
    unset($data->especialidad);
    return parent::save($data);
  }  

  function post_save($id) {
    $this->load->helper("file_helper");
    $data = $this->get($id);
    $data->link = "profesional/".filename($data->apellido." ".$data->nombre,"-",0)."-".$data->id."/";
    $this->update($id,$data);
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

  function buscar($conf = array()) {

    $id_empresa = isset($conf["id_empresa"]) ? $conf["id_empresa"] : parent::get_empresa();
    $filter = isset($conf["filter"]) ? $conf["filter"] : "";
    $limit = isset($conf["limit"]) ? $conf["limit"] : 0;
    $offset = isset($conf["offset"]) ? $conf["offset"] : 10;
    $order = isset($conf["order"]) ? $conf["order"] : "A.apellido ASC";
    $id_especialidad = isset($conf["id_especialidad"]) ? $conf["id_especialidad"] : 0;
    
    $sql = "SELECT SQL_CALC_FOUND_ROWS DISTINCT A.*, ";
    $sql.= "  IF(E.nombre IS NULL,'',E.nombre) AS especialidad ";
    $sql.= "FROM med_profesionales A ";
    $sql.= "LEFT JOIN med_especialidades E ON (A.id_especialidad = E.id) ";
    $sql.= "WHERE 1=1 ";
    $sql.= "AND A.id_empresa = $id_empresa ";
    if (!empty($filter)) $sql.= "AND CONCAT(A.apellido,' ',A.nombre) LIKE '%$filter%' ";
    if (!empty($id_especialidad)) $sql.= "AND A.id_especialidad = $id_especialidad ";
    $sql.= "ORDER BY $order ";
    $sql.= "LIMIT $limit, $offset ";
    $q = $this->db->query($sql);

    $q_total = $this->db->query("SELECT FOUND_ROWS() AS total");
    $total = $q_total->row();
    
    return array(
      "results"=>$q->result(),
      "total"=>$total->total,
    );
  }

}