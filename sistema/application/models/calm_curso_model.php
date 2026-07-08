<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Calm_Curso_Model extends Abstract_Model {
  
  function __construct() {
    parent::__construct("calm_cursos","id","nombre ASC");
  }

  function get($id) {
    $data = parent::get($id);
    $data->audios = array();
    $sql = "SELECT * ";
    $sql.= "FROM calm_cursos_audios ";
    $sql.= "WHERE id_curso = $id AND id_empresa = $data->id_empresa ";
    $sql.= "ORDER BY orden ASC ";
    $q = $this->db->query($sql);
    foreach($q->result() as $r) {
      $data->audios[] = $r;
    }
    return $data;
  }

  function delete($id) {
    $this->db->query("DELETE FROM calm_cursos_audios WHERE id_curso = $id ");
    parent::delete($id);
  }

  function save($data) {
    $audios = $data->audios;
    $id = parent::save($data);
    $i=0;
    $sql = "DELETE FROM calm_cursos_audios WHERE id_empresa = $data->id_empresa AND id_curso = $id ";
    $this->db->query($sql);
    foreach($audios as $p) {
      $this->db->insert("calm_cursos_audios",array(
        "id_empresa"=>$p->id_empresa,
        "id_curso"=>$id,
        "path_audio"=>$p->path_audio,
        "nombre"=>$p->nombre,
        "duracion"=>$p->duracion,
        "orden"=>$i,
      ));
      $i++;
    }
    return $id;
  }

  function buscar($conf = array()) {
    $id_empresa = isset($conf["id_empresa"]) ? $conf["id_empresa"] : parent::get_empresa();
    $order = isset($conf["order"]) ? $conf["order"] : "ASC";
    if (empty($order)) $order = "ASC";
    $order_by = isset($conf["order_by"]) ? $conf["order_by"] : "A.nombre";
    if (empty($order_by)) $order_by = "A.nombre";
    $filter = isset($conf["filter"]) ? $conf["filter"] : "";
    $limit = isset($conf["limit"]) ? $conf["limit"] : 0;
    $offset = isset($conf["offset"]) ? $conf["offset"] : 10;
    $sql = "SELECT SQL_CALC_FOUND_ROWS A.*, ";
    $sql.= " IF(C.nombre IS NULL,'Todas',C.nombre) AS categoria ";
    $sql.= "FROM calm_cursos A ";
    $sql.= "LEFT JOIN calm_categorias C ON (A.id_empresa = C.id_empresa AND A.id_categoria = C.id) ";
    $sql.= "WHERE A.id_empresa = $id_empresa ";
    if (!empty($filter)) $sql.= "AND A.nombre LIKE '%$filter%' ";
    $sql.= "ORDER BY $order_by $order ";
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