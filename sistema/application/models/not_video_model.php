<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Not_Video_Model extends Abstract_Model {
  
  function __construct() {
    parent::__construct("not_videos","id","fecha DESC");
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
   * Obtiene toda la lista de IDS de subcategorias de un determinado padre
   */
  private function get_list_ids($id_empresa,$id_padre) {
    $sql = "SELECT id FROM not_categorias WHERE id_padre = $id_padre AND id_empresa = $id_empresa ";
    $q = $this->db->query($sql);
    $array = array();
    foreach($q->result() as $r) {
      $array[] = $r->id;
      $array = array_merge($array,$this->get_list_ids($id_empresa,$r->id));
    }
    return $array;
  }
    
  /**
   * Obtiene los entradas a partir de diferentes parametros
   */
  function buscar($conf = array()) {
    
    $id_empresa = isset($conf["id_empresa"]) ? $conf["id_empresa"] : parent::get_empresa();
    $filter = isset($conf["filter"]) ? $conf["filter"] : "";
    $limit = (isset($conf["limit"]) && !empty($conf["limit"])) ? $conf["limit"] : 0;
    $offset = (isset($conf["offset"]) && !empty($conf["offset"])) ? $conf["offset"] : 10;
    $fecha = isset($conf["fecha"]) ? $conf["fecha"] : "";
    $desde = isset($conf["desde"]) ? $conf["desde"] : "";
    $hasta = isset($conf["hasta"]) ? $conf["hasta"] : "";
    $id_usuario = isset($conf["id_usuario"]) ? $conf["id_usuario"] : 0;
    $order = isset($conf["order"]) ? $conf["order"] : "A.fecha DESC";
    
    if (isset($conf["id_categoria"]) && !empty($conf["id_categoria"])) {
      $id_categoria = $conf["id_categoria"];
      $array = array($id_categoria);
      $in_ids_categoria = implode(",",array_merge($array,$this->get_list_ids($id_empresa,$id_categoria)));
    } else {
      $id_categoria = "";
      $in_ids_categoria = "";
    }
    
    $sql = "SELECT SQL_CALC_FOUND_ROWS DISTINCT A.*, ";
    $sql.= "  IF(A.link_youtube != '',CONCAT('https://www.youtube.com/watch?v=',A.link_youtube),'') AS link_youtube, ";
    $sql.= "  IF(fecha='0000-00-00 00:00:00','',DATE_FORMAT(fecha,'%d/%m/%Y %H:%i')) AS fecha, ";
    $sql.= "  IF(U.nombre IS NULL,'',U.nombre) AS usuario, ";
    $sql.= "  IF(R.nombre IS NULL,'Sin definir',R.nombre) AS categoria ";
    $sql.= "FROM not_videos A ";
    $sql.= "LEFT JOIN not_categorias R ON (A.id_categoria = R.id AND A.id_empresa = R.id_empresa) ";
    $sql.= "LEFT JOIN com_usuarios U ON (A.id_usuario = U.id AND A.id_empresa = U.id_empresa) ";
    $sql.= "WHERE 1=1 ";
    $sql.= "AND A.id_empresa = $id_empresa ";
    if (!empty($filter)) $sql.= "AND A.titulo LIKE '%$filter%' ";
    if (!empty($in_ids_categoria)) $sql.= "AND A.id_categoria IN ($in_ids_categoria) ";
    if (!empty($fecha)) $sql.= "AND DATE_FORMAT(A.fecha,'%d/%m/%Y') = '$fecha' ";
    if (!empty($desde)) $sql.= "AND '$desde' <= A.fecha ";
    if (!empty($hasta)) $sql.= "AND A.fecha <= '$hasta' ";
    if (!empty($id_usuario)) $sql.= "AND A.id_usuario = '$id_usuario' ";
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
    $id_cliente = isset($config["id_cliente"]) ? $config["id_cliente"] : 0;
    
    // Obtenemos los datos del entrada
    $id = (int)$id;
    $sql = "SELECT A.*, ";
    $sql.= "  IF(A.link_youtube != '',CONCAT('https://www.youtube.com/watch?v=',A.link_youtube),'') AS link_youtube, ";
    $sql.= "  IF(U.nombre IS NULL,'',U.nombre) AS usuario, ";
    $sql.= "  IF(R.nombre IS NULL,'Sin definir',R.nombre) AS categoria, ";
    $sql.= "  IF(A.fecha='0000-00-00 00:00:00','',DATE_FORMAT(A.fecha,'%d/%m/%Y %H:%i')) AS fecha ";
    $sql.= "FROM not_videos A ";
    $sql.= "LEFT JOIN not_categorias R ON (A.id_categoria = R.id AND A.id_empresa = R.id_empresa) ";
    $sql.= "LEFT JOIN com_usuarios U ON (A.id_usuario = U.id AND A.id_empresa = U.id_empresa) ";
    $sql.= "WHERE A.id = $id ";
    $sql.= "AND A.id_empresa = $id_empresa ";
    if (!empty($id_cliente)) $sql.= "AND A.id_cliente = $id_cliente ";
    $q = $this->db->query($sql);
    if ($q->num_rows() == 0) return array();
    $entrada = $q->row();
    return $entrada;
  }
  
  function delete($id) {
    $id_empresa = parent::get_empresa();
    if ($id_empresa === FALSE) return;
    $this->db->query("DELETE FROM not_videos WHERE id = $id AND id_empresa = $id_empresa");
  }

}