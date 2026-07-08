<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Not_Evento_Model extends Abstract_Model {
  
  function __construct() {
    parent::__construct("not_eventos","id","fecha_desde DESC");
  }

  function update($id,$data) {
    $data->texto = str_replace("&ldquo;", "&quot;", $data->texto);
    $data->texto = str_replace("&rdquo;", "&quot;", $data->texto);
    $data->texto = str_replace("&lsquo;", "&quot;", $data->texto);
    $data->texto = str_replace("&rsquo;", "&quot;", $data->texto);
    $data->titulo = preg_replace("/[”“]/u","&quot;",$data->titulo);
    return parent::update($id,$data);
  }
  
  function insert($data) {
    $id_empresa = parent::get_empresa();
    $data->texto = str_replace("&ldquo;", "&quot;", $data->texto);
    $data->texto = str_replace("&rdquo;", "&quot;", $data->texto);
    $data->texto = str_replace("&lsquo;", "&quot;", $data->texto);
    $data->texto = str_replace("&rsquo;", "&quot;", $data->texto);
    $data->titulo = preg_replace("/[”“]/u","&quot;",$data->titulo);
    $res = parent::insert($data);    
    return $res;
  }  
  
  function find($filter) {
    $id_empresa = parent::get_empresa();
    $this->db->where("id_empresa",$id_empresa);
    $this->db->like("titulo",$filter);
    $query = $this->db->get($this->tabla);
    $result = $query->result();
    $this->db->close();
    return $result;
  }
  
  /**
   * Obtiene los eventos a partir de diferentes parametros
   */
  function buscar($conf = array()) {
    
    $id_empresa = isset($conf["id_empresa"]) ? $conf["id_empresa"] : parent::get_empresa();
    $filter = isset($conf["filter"]) ? $conf["filter"] : "";
    $limit = (isset($conf["limit"]) && !empty($conf["limit"])) ? $conf["limit"] : 0;
    $offset = (isset($conf["offset"]) && !empty($conf["offset"])) ? $conf["offset"] : 10;
    $id_usuario = isset($conf["id_usuario"]) ? $conf["id_usuario"] : 0;
    $order = isset($conf["order"]) ? $conf["order"] : "A.fecha_desde DESC";
    $categoria = isset($conf["categoria"]) ? $conf["categoria"] : -1;
    $proximos = isset($conf["proximos"]) ? $conf["proximos"] : -1;
    
    $sql = "SELECT SQL_CALC_FOUND_ROWS DISTINCT A.*, ";
    $sql.= "  IF(fecha_desde='0000-00-00','',DATE_FORMAT(fecha_desde,'%d/%m/%Y')) AS fecha_desde, ";
    $sql.= "  IF(fecha_hasta='0000-00-00','',DATE_FORMAT(fecha_hasta,'%d/%m/%Y')) AS fecha_hasta, ";
    $sql.= "  IF(U.nombre IS NULL,'',U.nombre) AS usuario ";
    $sql.= "FROM not_eventos A ";
    $sql.= "LEFT JOIN com_usuarios U ON (A.id_usuario = U.id AND A.id_empresa = U.id_empresa) ";
    $sql.= "WHERE 1=1 ";
    $sql.= "AND A.id_empresa = $id_empresa ";
    if (!empty($filter)) $sql.= "AND A.titulo LIKE '%$filter%' ";
    if (!empty($id_usuario)) $sql.= "AND A.id_usuario = '$id_usuario' ";
    if ($categoria != -1) $sql.= "AND A.categoria = $categoria ";
    if ($proximos == 1) $sql.= "AND A.fecha_desde >= NOW() ";
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
  
  function get($id,$config = array()) {

    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    
    // Obtenemos los datos del entrada
    $id = (int)$id;
    $sql = "SELECT A.*, ";
    $sql.= "  IF(U.nombre IS NULL,'',U.nombre) AS usuario, ";
    $sql.= "  IF(A.fecha_desde='0000-00-00','',DATE_FORMAT(A.fecha_desde,'%d/%m/%Y')) AS fecha_desde, ";
    $sql.= "  IF(A.fecha_hasta='0000-00-00','',DATE_FORMAT(A.fecha_hasta,'%d/%m/%Y')) AS fecha_hasta ";
    $sql.= "FROM not_eventos A ";
    $sql.= "LEFT JOIN com_usuarios U ON (A.id_usuario = U.id AND A.id_empresa = U.id_empresa) ";
    $sql.= "WHERE A.id = $id ";
    $sql.= "AND A.id_empresa = $id_empresa ";
    $q = $this->db->query($sql);
    if ($q->num_rows() == 0) return array();
    $entrada = $q->row();

    // Obtenemos las imagenes de ese entrada
    $sql = "SELECT AI.* FROM not_eventos_images AI WHERE AI.id_evento = $id AND AI.id_empresa = $id_empresa ORDER BY AI.orden ASC";
    $q = $this->db->query($sql);
    $entrada->images = array();
    foreach($q->result() as $r) {
      $entrada->images[] = $r->path;
    }

    return $entrada;
  }
  
  function delete($id) {
    $id_empresa = parent::get_empresa();
    if ($id_empresa === FALSE) return;
    $this->db->query("DELETE FROM not_eventos WHERE id = $id AND id_empresa = $id_empresa");
  }

}