<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Entrada_Model extends Abstract_Model {
  
  function __construct() {
    parent::__construct("not_entradas","id","fecha DESC");
  }

  function update($id,$data) {
    $data->texto = str_replace("&ldquo;", "&apos;", $data->texto);
    $data->texto = str_replace("&rdquo;", "&apos;", $data->texto);
    $data->texto = str_replace("&lsquo;", "&apos;", $data->texto);
    $data->texto = str_replace("&rsquo;", "&apos;", $data->texto);
    $data->titulo = preg_replace("/[”“]/u","&apos;",$data->titulo);
    $data->fecha = substr($data->fecha, 0, 19);
    if (empty($data->eliminada_fecha)) $data->eliminada_fecha = "0000-00-00 00:00:00";
    return parent::update($id,$data);
  }
  
  function insert($data) {
    $id_empresa = parent::get_empresa();
    $data->texto = str_replace("&ldquo;", "&apos;", $data->texto);
    $data->texto = str_replace("&rdquo;", "&apos;", $data->texto);
    $data->texto = str_replace("&lsquo;", "&apos;", $data->texto);
    $data->texto = str_replace("&rsquo;", "&apos;", $data->texto);
    $data->titulo = preg_replace("/[”“]/u","&apos;",$data->titulo);
    $data->fecha = substr($data->fecha, 0, 19);
    if (empty($data->eliminada_fecha)) $data->eliminada_fecha = "0000-00-00 00:00:00";
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
  
  function save_tag($tag) {
    $this->load->helper("file_helper");
    // Primero controlamos si existe la etiqueta
    $q = $this->db->query("SELECT * FROM not_etiquetas WHERE nombre = '$tag->nombre' AND id_empresa = $tag->id_empresa LIMIT 0,1");
    if ($q->num_rows()<=0) {
      // Si no existe, la guardamos
      $link = filename($tag->nombre,"-",0);
      $this->db->query("INSERT INTO not_etiquetas (nombre,link,id_empresa) VALUES ('$tag->nombre','$link',$tag->id_empresa)");
      $id_etiqueta = $this->db->insert_id();
    } else {
      $row = $q->row();
      $id_etiqueta = $row->id;
    }
    $this->db->query("INSERT INTO not_entradas_etiquetas (id_empresa,id_entrada,id_etiqueta) VALUES ($tag->id_empresa,$tag->id_entrada,$id_etiqueta) ");
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
    $eliminada = isset($conf["eliminada"]) ? $conf["eliminada"] : 0;
    $id_usuario = isset($conf["id_usuario"]) ? $conf["id_usuario"] : 0;
    $id_cliente = isset($conf["id_cliente"]) ? $conf["id_cliente"] : 0;
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
    $sql.= "  IF(fecha='0000-00-00 00:00:00','',DATE_FORMAT(fecha,'%d/%m/%Y %H:%i')) AS fecha, ";
    $sql.= "  IF(U.nombre IS NULL,'',U.nombre) AS usuario, ";
    $sql.= "  IF(R.nombre IS NULL,'Sin definir',R.nombre) AS categoria ";
    $sql.= "FROM not_entradas A ";
    $sql.= "LEFT JOIN not_categorias R ON (A.id_categoria = R.id AND A.id_empresa = R.id_empresa) ";
    $sql.= "LEFT JOIN com_usuarios U ON (A.id_usuario = U.id AND A.id_empresa = U.id_empresa) ";
    $sql.= "WHERE 1=1 ";
    $sql.= "AND A.id_empresa = $id_empresa ";
    if ($eliminada != -1) $sql.= "AND A.eliminada = $eliminada ";
    if (!empty($filter)) $sql.= "AND A.titulo LIKE '%$filter%' ";
    if (!empty($in_ids_categoria)) $sql.= "AND A.id_categoria IN ($in_ids_categoria) ";
    if (!empty($fecha)) $sql.= "AND DATE_FORMAT(A.fecha,'%d/%m/%Y') = '$fecha' ";
    if (!empty($desde)) $sql.= "AND '$desde' <= A.fecha ";
    if (!empty($hasta)) $sql.= "AND A.fecha <= '$hasta' ";
    if (!empty($id_usuario)) $sql.= "AND A.id_usuario = '$id_usuario' ";
    if (!empty($id_cliente)) $sql.= "AND A.id_cliente = '$id_cliente' ";
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
    $min = isset($config["min"]) ? $config["min"] : 0;
    $id_cliente = isset($config["id_cliente"]) ? $config["id_cliente"] : 0;
    
    // Obtenemos los datos del entrada
    $id = (int)$id;
    $sql = "SELECT A.*, ";
    $sql.= "  IF(U.nombre IS NULL,'',U.nombre) AS usuario, ";
    $sql.= "  IF(R.nombre IS NULL,'Sin definir',R.nombre) AS categoria, ";
    $sql.= "  IF(A.fecha='0000-00-00 00:00:00','',DATE_FORMAT(A.fecha,'%d/%m/%Y %H:%i')) AS fecha ";
    $sql.= "FROM not_entradas A ";
    $sql.= "LEFT JOIN not_categorias R ON (A.id_categoria = R.id AND A.id_empresa = R.id_empresa) ";
    $sql.= "LEFT JOIN com_usuarios U ON (A.id_usuario = U.id AND A.id_empresa = U.id_empresa) ";
    $sql.= "WHERE A.id = $id ";
    $sql.= "AND A.id_empresa = $id_empresa ";
    if (!empty($id_cliente)) $sql.= "AND A.id_cliente = $id_cliente ";
    $q = $this->db->query($sql);
    if ($q->num_rows() == 0) return array();
    $entrada = $q->row();
    if ($min == 1) return $entrada;
    
    // Obtenemos los entradas relacionados con ese producto
    $sql = "SELECT A.id, A.titulo, A.path, AR.destacado ";
    $sql.= "FROM not_entradas A INNER JOIN not_entradas_relacionadas AR ON (A.id = AR.id_relacion AND A.id_empresa = AR.id_empresa) ";
    $sql.= "WHERE AR.id_entrada = $id AND AR.id_empresa = $entrada->id_empresa ";
    $sql.= "ORDER BY AR.orden ASC ";
    $q = $this->db->query($sql);
    $entrada->relacionados = array();
    foreach($q->result() as $r) {
      $obj = new stdClass();
      $obj->id = $r->id;
      $obj->titulo = (html_entity_decode($r->titulo));
      $obj->path = $r->path;
      $obj->destacado = $r->destacado;
      $entrada->relacionados[] = $obj;
    }
    
    // Obtenemos las categorias relacionados con ese producto
    $sql = "SELECT R.id, R.nombre ";
    $sql.= "FROM not_categorias R INNER JOIN not_entradas_relacionadas AR ON (R.id = AR.id_categoria AND R.id_empresa = AR.id_empresa) ";
    $sql.= "WHERE AR.id_entrada = $id AND AR.id_empresa = $entrada->id_empresa ";
    $sql.= "ORDER BY AR.orden ASC ";
    $q = $this->db->query($sql);
    $entrada->categorias_relacionados = array();
    foreach($q->result() as $r) {
      $obj = new stdClass();
      $obj->id = $r->id;
      $obj->nombre = $r->nombre;
      $entrada->categorias_relacionados[] = $obj;
    }    
    
    // Obtenemos las imagenes de ese entrada
    $sql = "SELECT AI.* FROM not_entradas_images AI WHERE AI.id_entrada = $id AND AI.id_empresa = $id_empresa ORDER BY AI.orden ASC";
    $q = $this->db->query($sql);
    $entrada->images = array();
    foreach($q->result() as $r) {
      $entrada->images[] = $r->path;
    }

    // Obtenemos las preguntas
    $sql = "SELECT AI.* FROM not_entradas_preguntas AI WHERE AI.id_entrada = $id AND AI.id_empresa = $id_empresa ORDER BY AI.orden ASC";
    $q = $this->db->query($sql);
    $entrada->preguntas = array();
    foreach($q->result() as $r) {
      $entrada->preguntas[] = $r;
    }

    // Obtenemos los horarios
    $sql = "SELECT AI.*, DATE_FORMAT(AI.fecha,'%d/%m/%Y') AS fecha, DATE_FORMAT(AI.hora,'%H:%i') AS hora ";
    $sql.= "FROM not_entradas_horarios AI WHERE AI.id_entrada = $id AND AI.id_empresa = $id_empresa ORDER BY AI.orden ASC";
    $q = $this->db->query($sql);
    $entrada->horarios = array();
    foreach($q->result() as $r) {
      $entrada->horarios[] = $r;
    }
    
    // Obtenemos las etiquetas de esa entrada
    $sql = "SELECT E.nombre ";
    $sql.= " FROM not_entradas_etiquetas EE INNER JOIN not_etiquetas E ON (EE.id_etiqueta = E.id AND EE.id_empresa = E.id_empresa) ";
    $sql.= "WHERE EE.id_entrada = $id AND EE.id_empresa = $id_empresa ORDER BY EE.orden ASC";
    $q = $this->db->query($sql);
    $entrada->etiquetas = array();
    foreach($q->result() as $r) {
      $entrada->etiquetas[] = (html_entity_decode($r->nombre));
    }
    
    // Obtenemos los comentarios
    $sql = "SELECT EC.*, ";
    $sql.= " DATE_FORMAT(EC.fecha,'%d/%m/%Y') AS fecha, ";
    $sql.= " DATE_FORMAT(EC.fecha,'%H:%i') AS hora, ";
    $sql.= " IF(C.path IS NULL,'',C.path) AS path ";
    $sql.= "FROM not_entradas_comentarios EC ";
    $sql.= " LEFT JOIN web_users C ON (C.id = EC.id_usuario AND EC.id_empresa = C.id_empresa) ";
    $sql.= "WHERE EC.id_entrada = $id AND EC.id_empresa = $id_empresa ";
    $sql.= "ORDER BY EC.orden ASC";
    $q = $this->db->query($sql);
    $entrada->comentarios = array();
    foreach($q->result() as $r) {
      if (!empty($r->path)) $r->path = ((strpos($r->path,"http://")===FALSE)) ? "/sistema/".$r->path : $r->path;
      $entrada->comentarios[] = $r;
    }
    
    return $entrada;
  }
  
  function delete($id) {
    $id_empresa = parent::get_empresa();
    if ($id_empresa === FALSE) return;
    // Las entradas no se borran, solo se marcan como eliminadas
    $fecha = date("Y-m-d H:i:s");
    $this->db->query("UPDATE not_entradas SET activo = 0, eliminada = 1, eliminada_fecha = '$fecha' WHERE id = $id AND id_empresa = $id_empresa ");
  }

  function borrar($id) {
    $id_empresa = parent::get_empresa();
    if ($id_empresa === FALSE) return;
    $q = $this->db->query("SELECT * FROM not_entradas WHERE id = $id AND id_empresa = $id_empresa ");
    if ($q->num_rows()>0) {
      $this->db->query("DELETE FROM not_entradas_comentarios WHERE id_entrada = $id AND id_empresa = $id_empresa ");
      $this->db->query("DELETE FROM not_entradas_etiquetas WHERE id_entrada = $id AND id_empresa = $id_empresa ");
      $this->db->query("DELETE FROM not_entradas_relacionadas WHERE id_entrada = $id AND id_empresa = $id_empresa ");
      $this->db->query("DELETE FROM not_entradas_images WHERE id_entrada = $id AND id_empresa = $id_empresa");
      $this->db->query("DELETE FROM not_entradas_horarios WHERE id_entrada = $id AND id_empresa = $id_empresa");
      $this->db->query("DELETE FROM not_entradas WHERE id = $id AND id_empresa = $id_empresa");
    }
  }

}