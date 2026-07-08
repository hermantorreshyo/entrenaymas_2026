<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Rubro_Lpc_Model extends Abstract_Model {
	
	private $rubros_lpc_relacionados = array();
	
	function __construct() {
		parent::__construct("rubros_lpc","id","nombre ASC");
	}

  function full_link($id_categoria,$conf = array()) {
    $categorias = array();
    while(TRUE) {
      $sql = "SELECT * FROM rubros_lpc WHERE id = $id_categoria ";
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

  function get_arbol($id_padre = 0,$separador = "",$config = array()) {
    $id_usuario = isset($config["id_usuario"]) ? $config["id_usuario"] : 0;
    $result = array();
    $sql = "SELECT * FROM rubros_lpc WHERE id_padre = $id_padre ";
    if ($id_usuario != 0) $sql.= "AND (id_usuario = $id_usuario OR id_usuario = 0) ";
    $sql.= "ORDER BY orden ASC";
    $q = $this->db->query($sql);
    foreach($q->result() as $row) {
      $e = new stdClass();
      $e->id = $row->id;
      $e->id_padre = $id_padre;
      $e->title = $row->nombre;
      $e->nombre_es = $e->title;
      $e->key = $row->id;
      $e->children = $this->get_arbol($row->id,$separador."&nbsp;&nbsp;&nbsp;",$config);
      $result[] = $e;            
    }
    return $result;
  }

  function get_select($id_padre = 0,$separador = "",$config = array()) {
    $id_usuario = isset($config["id_usuario"]) ? $config["id_usuario"] : 0;
    $result = array();
    $sql = "SELECT * FROM rubros_lpc WHERE id_padre = $id_padre ";
    if ($id_usuario != 0) $sql.= "AND (id_usuario = $id_usuario OR id_usuario = 0) ";
    $sql.= "ORDER BY orden ASC, nombre ASC";
    $q = $this->db->query($sql);
    foreach($q->result() as $row) {
      $e = new stdClass();
      $e->id = $row->id;
      $e->id_padre = $id_padre;
      $e->nombre = $separador.$row->nombre;
      $result[] = $e;
      $hijos = $this->get_select($row->id,$separador."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;",$config);
      $result = array_merge($result,$hijos);
    }
    return $result;
  }


  // TODO: Tiene un limite de nivel
  function get_ids_rubros_lpc($id_categoria_padre) {
    $salida = array();
    $s = $this->get_arbol($id_categoria_padre);
    foreach($s as $r) {
      $salida[] = $r->id;
      if (isset($r->children) && sizeof($r->children)>0) {
        foreach($r->children as $rr) {
          $salida[] = $rr->id;
          if (isset($rr->children) && sizeof($rr->children)>0) {
            foreach($rr->children as $rrr) {
              $salida[] = $rrr->id;
              if (isset($rrr->children) && sizeof($rrr->children)>0) {
                foreach($rrr->children as $rrrr) {
                  $salida[] = $rrrr->id;
                }
              }
            }
          }
        }
      }
    }
    $salida[] = $id_categoria_padre; // Incluimos el padre
    return $salida;
  }

}