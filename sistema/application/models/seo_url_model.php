<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Seo_Url_Model extends Abstract_Model {
  
  function __construct() {
    parent::__construct("seo_urls","id","url ASC");
  }

  function buscar($params = array()) {

    $filter = isset($params["filter"]) ? $params["filter"] : "";
    $limit = isset($params["limit"]) ? $params["limit"] : 0;
    $activo = isset($params["activo"]) ? $params["activo"] : -1;
    $offset = isset($params["offset"]) ? $params["offset"] : 10;
    $order = isset($params["order"]) ? trim($params["order"]) : "A.url ASC ";
    if (empty($order)) $order = "A.url ASC ";
    $id_empresa = isset($params["id_empresa"]) ? $params["id_empresa"] : parent::get_empresa();

    $sql = "SELECT SQL_CALC_FOUND_ROWS A.* ";
    $sql.= "FROM seo_urls A ";
    $sql.= "WHERE 1=1 ";
    $sql.= "AND A.id_empresa = $id_empresa ";
    if (!empty($filter)) $sql.= "AND A.url LIKE '%$filter%' ";
    if (!empty($order)) $sql.= "ORDER BY $order ";
    $sql.= "LIMIT $limit, $offset ";
    $query = $this->db->query($sql);

    $q_total = $this->db->query("SELECT FOUND_ROWS() AS total");
    $total = $q_total->row();

    $result = $query->result();
    return array(
      "results"=>$result,
      "total"=>$total->total,
    );
  }
  
  function save($data) {
    
    $this->load->helper("fecha_helper");
    $this->load->helper("file_helper");

    $id_empresa = $data->id_empresa;
    $parametros = $data->parametros;
    unset($data->parametros);

    $id = parent::save($data);

    // Guardamos los parametros
    $this->db->query("DELETE FROM seo_urls_parametros WHERE id_seo = $id AND id_empresa = $id_empresa");
    foreach($parametros as $im) {
      $this->db->query("INSERT INTO seo_urls_parametros (id_empresa,id_seo,campo,orden,valor) VALUES($id_empresa,$id,'$im->campo','$im->orden','$im->valor')");
    }

    return $id;
  }

  function get($id,$config=array()) {

    $row = parent::get($id);
    if (empty($row)) return $row;

    $this->load->helper("fecha_helper");

    // Obtenemos los parametros
    $sql = "SELECT P.* ";
    $sql.= "FROM seo_urls_parametros P ";
    $sql.= "WHERE P.id_seo = $id AND P.id_empresa = $row->id_empresa ";
    $sql.= "ORDER BY P.orden ASC";
    $q = $this->db->query($sql);
    $row->parametros = array();
    foreach($q->result() as $r) {
      $row->parametros[] = $r;
    }

    return $row;
  }

  function delete($id) {
    $id_empresa = parent::get_empresa();
    $this->db->query("DELETE FROM seo_urls_parametros WHERE id_empresa = $id_empresa AND id_seo = $id");
    $this->db->query("DELETE FROM seo_urls WHERE id_empresa = $id_empresa AND id = $id");
  }

}