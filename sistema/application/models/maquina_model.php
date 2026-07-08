<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Maquina_Model extends Abstract_Model {
  
  function __construct() {
    parent::__construct("mant_maquinas","id");
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
    
  // Controlamos si existe el codigo
  function existe_codigo($codigo,$id = 0) {
    $id_empresa = parent::get_empresa();
    if (empty($codigo)) return FALSE;
    $sql = "SELECT * FROM mant_maquinas WHERE codigo = '$codigo' AND id_empresa = '$id_empresa' ";
    if ($id != 0) $sql.= "AND id != $id ";
    $q = $this->db->query($sql);
    return ($q->num_rows()>0);
  }
    
  /**
   * Obtiene los maquinas a partir de diferentes parametros
   */
  function buscar($conf = array()) {
    
    $id_empresa = (isset($conf["id_empresa"])) ? $conf["id_empresa"] : parent::get_empresa();
    $id_sector = (isset($conf["id_sector"])) ? $conf["id_sector"] : 0;
    $filter = (isset($conf["filter"])) ? $conf["filter"] : "";
    $limit = (isset($conf["limit"])) ? $conf["limit"] : 0;
    $offset = (isset($conf["offset"])) ? $conf["offset"] : 0;
    $order = (isset($conf["order"])) ? $conf["order"] : "";
    
    $sql = "SELECT SQL_CALC_FOUND_ROWS A.*, ";
    $sql.= "IF(TE.nombre IS NULL,'',TE.nombre) AS sector ";
    $sql.= "FROM mant_maquinas A ";
    $sql.= "LEFT JOIN mant_sectores TE ON (A.id_sector = TE.id AND A.id_empresa = TE.id_empresa) ";
    $sql.= "WHERE 1=1 ";
    $sql.= "AND A.id_empresa = $id_empresa ";
    if (!empty($filter)) $sql.= "AND (A.codigo LIKE '%$filter%' OR A.nombre LIKE '%$filter%') ";
    if (!empty($id_sector)) $sql.= "AND A.id_sector = $id_sector ";
    if (!empty($order)) $sql.= "ORDER BY $order ";
    if ($offset != 0) $sql.= "LIMIT $limit, $offset ";
    $q = $this->db->query($sql);
    $q_total = $this->db->query("SELECT FOUND_ROWS() AS total");
    $total = $q_total->row();
    
    return array(
      "results"=>$q->result(),
      "total"=>$total->total
    );
  }
  
  function buscar_partes($conf = array()) {
    
    $id_empresa = (isset($conf["id_empresa"])) ? $conf["id_empresa"] : parent::get_empresa();
    $id_maquina = (isset($conf["id_maquina"])) ? $conf["id_maquina"] : 0;
    $filter = (isset($conf["filter"])) ? $conf["filter"] : "";
    $limit = (isset($conf["limit"])) ? $conf["limit"] : 0;
    $offset = (isset($conf["offset"])) ? $conf["offset"] : 0;
    $order = (isset($conf["order"])) ? $conf["order"] : "";
    
    $sql = "SELECT SQL_CALC_FOUND_ROWS A.* ";
    $sql.= "FROM mant_partes A ";
    $sql.= "WHERE 1=1 ";
    $sql.= "AND A.id_empresa = $id_empresa ";
    if (!empty($filter)) $sql.= "AND (A.codigo LIKE '%$filter%' OR A.nombre LIKE '%$filter%') ";
    if (!empty($id_maquina)) $sql.= "AND A.id_maquina = $id_maquina ";
    if (!empty($order)) $sql.= "ORDER BY $order ";
    if ($offset != 0) $sql.= "LIMIT $limit, $offset ";
    $q = $this->db->query($sql);
    $q_total = $this->db->query("SELECT FOUND_ROWS() AS total");
    $total = $q_total->row();
    
    return array(
      "results"=>$q->result(),
      "total"=>$total->total
    );
  }

  function next() {
    $id_empresa = parent::get_empresa();
    $q = $this->db->query("SELECT IF(MAX(codigo) IS NULL,0,MAX(codigo)) AS codigo FROM mant_maquinas WHERE id_empresa = $id_empresa");
    $r = $q->row();
    return ((int)$r->codigo + 1);
  }
  
  
  function get($id,$config=array()) {
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    // Obtenemos los datos del maquina
    $id = (int)$id;
    $sql = "SELECT A.*, ";
    $sql.= "IF(TE.nombre IS NULL,'',TE.nombre) AS sector ";
    $sql.= "FROM mant_maquinas A ";
    $sql.= "LEFT JOIN mant_sectores TE ON (A.id_sector = TE.id AND A.id_empresa = TE.id_empresa) ";
    $sql.= "WHERE A.id = $id ";
    $sql.= "AND A.id_empresa = $id_empresa ";
    $q = $this->db->query($sql);
    if ($q->num_rows() == 0) return array();
    $maquina = $q->row();
    
    $sql = "SELECT * ";
    $sql.= "FROM mant_partes ";
    $sql.= "WHERE id_maquina = $id AND id_empresa = $maquina->id_empresa ";
    $sql.= "ORDER BY orden ASC ";
    $q = $this->db->query($sql);
    $maquina->partes = array();
    foreach($q->result() as $r) {
      $maquina->partes[] = $r;
    }
    return $maquina;
  }
  
  function delete($id) {
    // Controlamos que se este borrando un maquina que pertenece a la empresa de la session
    $id_empresa = parent::get_empresa();
    if ($id_empresa === FALSE) return;
    $q = $this->db->query("SELECT * FROM mant_maquinas WHERE id = $id AND id_empresa = $id_empresa ");
    if ($q->num_rows()>0) {
      $this->db->query("DELETE FROM mant_partes WHERE id_maquina = $id AND id_empresa = $id_empresa");
      $this->db->query("DELETE FROM mant_maquinas WHERE id = $id AND id_empresa = $id_empresa");
    }
  }

}