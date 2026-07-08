<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Carrera_Model extends Abstract_Model {
  
  function __construct() {
    parent::__construct("aca_carreras","id","nombre ASC");
  }

  function save($data) {
    $this->load->helper("file_helper");
    $materias = $data->materias;
    unset($data->materias);
    $id_carrera = parent::save($data);

    foreach($materias as $m) {
      $m->link = filename($m->nombre,"-",0);
      $m->id_carrera = $id_carrera;
      if ($m->id == 0) {
        $this->db->insert("aca_materias",$m);
      } else {
        $this->db->where(array(
          "id_empresa"=>$data->id_empresa,
          "id"=>$m->id,
        ));
        $this->db->update("aca_materias",$m);
      }
    }
    
    return $id_carrera;
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
  
  /**
   * Obtiene los carreras a partir de diferentes parametros
   */
  function buscar($conf = array()) {
    
    $id_empresa = isset($conf["id_empresa"]) ? $conf["id_empresa"] : parent::get_empresa();
    $filter = isset($conf["filter"]) ? $conf["filter"] : "";
    $limit = isset($conf["limit"]) ? $conf["limit"] : 0;
    $offset = isset($conf["offset"]) ? $conf["offset"] : 10;
    $order = isset($conf["order"]) ? $conf["order"] : "A.nombre ASC";
    
    $sql = "SELECT SQL_CALC_FOUND_ROWS DISTINCT A.* ";
    $sql.= "FROM aca_carreras A ";
    $sql.= "WHERE 1=1 ";
    $sql.= "AND A.id_empresa = $id_empresa ";
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
  
  function get($id) {
    $id_empresa = parent::get_empresa();
    // Obtenemos los datos del carrera
    $id = (int)$id;
    $sql = "SELECT A.* ";
    $sql.= "FROM aca_carreras A ";
    $sql.= "WHERE A.id = $id ";
    $sql.= "AND A.id_empresa = $id_empresa ";
    $q = $this->db->query($sql);
    if ($q->num_rows() == 0) return FALSE;
    $carrera = $q->row();

    // Obtenemos las materias de esa carrera
    $sql = "SELECT * FROM aca_materias WHERE id_empresa = $id_empresa AND id_carrera = $id ";
    $sql.= "ORDER BY anio ASC, cuatrimestre ASC ";
    $qq = $this->db->query($sql);
    $carrera->materias = $qq->result();

    return $carrera;
  }
  
  function delete($id) {
    // Controlamos que se este borrando un carrera que pertenece a la empresa de la session
    $id_empresa = parent::get_empresa();
    if ($id_empresa === FALSE) return;
    $this->db->query("DELETE FROM aca_materias WHERE id_carrera = $id AND id_empresa = $id_empresa ");
    $this->db->query("DELETE FROM aca_carreras WHERE id = $id AND id_empresa = $id_empresa ");
  }

}