<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Categoria_Entrada_Model extends Abstract_Model {
	
	private $categorias_relacionadas = array();
	
	function __construct() {
		parent::__construct("not_categorias","id","nombre ASC");
	}

  function get_by_link($link,$config = array()) {
    $id_empresa = (isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa());
    $sql = "SELECT R.* ";
    $sql.= "FROM not_categorias R ";
    $sql.= "WHERE R.link = '$link' ";
    $sql.= "AND R.id_empresa = $id_empresa ";
    $q = $this->db->query($sql);
    if ($q->num_rows() == 0) return array();
    $row = $q->row();
    return $row;
  }
	
	function get($id) {
		$id_empresa = parent::get_empresa();
		$id = (int)$id;
		$sql = "SELECT R.* ";
		$sql.= "FROM not_categorias R ";
		$sql.= "WHERE R.id = $id ";
		$sql.= "AND R.id_empresa = $id_empresa ";
		$q = $this->db->query($sql);
		if ($q->num_rows() == 0) return array();
		$row = $q->row();
		
		// Obtenemos las categorias relacionados con ese producto
		$sql = "SELECT R.id, R.nombre ";
		$sql.= "FROM not_categorias R INNER JOIN not_categorias_relacionadas RR ON (R.id = RR.id_relacion AND R.id_empresa = RR.id_empresa) ";
		$sql.= "WHERE RR.id_categoria = $id ";
		$sql.= "ORDER BY RR.orden ASC ";
		$q = $this->db->query($sql);
		$row->categorias_relacionadas = array();
		foreach($q->result() as $r) {
			$obj = new stdClass();
			$obj->id = $r->id;
			$obj->nombre = $r->nombre;
			$row->categorias_relacionadas[] = $obj;
		}		
		
		return $row;
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
	
	// Reordena los elementos del arbol
	function reorder($elements,$orden = 0, $id_padre = 0) {
		$id_empresa = parent::get_empresa();

		if (isset($elements["id"])) {
			$id = $elements["id"];
			if (!empty($id)) {
				$sql = "UPDATE not_categorias SET orden = $orden, id_padre = $id_padre ";
				$sql.= "WHERE id = $id AND id_empresa = $id_empresa ";
				$this->db->query($sql);				
			}
		}
		if (isset($elements["children"]) && is_array($elements["children"])){
			for($i=0;$i<sizeof($elements["children"]);$i++) {
				$e = $elements["children"][$i];
				$this->reorder($e,$i,$id);
			}
		}
	}
	
	
  function get_arbol($config = array()) {
		$id_empresa = (isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa());
    $separador = (isset($config["separador"]) ? $config["separador"] : "");
    $not_ids = (isset($config["not_ids"]) ? $config["not_ids"] : "");
    $id_padre = (isset($config["id_padre"]) ? $config["id_padre"] : 0);
    $result = array();
    $sql = "SELECT * FROM not_categorias WHERE id_empresa = $id_empresa AND id_padre = $id_padre ";
    if (!empty($not_ids)) $sql.= "AND id NOT IN ($not_ids) ";
    $sql.= "ORDER BY orden ASC";
    $q = $this->db->query($sql);
    foreach($q->result() as $row) {
			$e = new stdClass();
			$e->id = $row->id;
      $e->activo = $row->activo;
			$e->fija = $row->fija;
			$e->id_padre = $id_padre;
			$e->title = $row->nombre;
			$e->nombre_es = $e->title;
			$e->key = $row->id;
			$e->children = $this->get_arbol(array(
        "id_padre"=>$row->id,
        "separador"=>$separador."&nbsp;&nbsp;&nbsp;",
        "id_empresa"=>$id_empresa,
      ));
			$result[] = $e;            
    }
    return $result;
  }
	
  function get_select($id_padre = 0,$separador = "") {
		$id_empresa = parent::get_empresa();
    $result = array();
    $q = $this->db->query("SELECT * FROM not_categorias WHERE id_empresa = $id_empresa AND id_padre = $id_padre ORDER BY nombre ASC");
    foreach($q->result() as $row) {
			$e = new stdClass();
			$e->id = $row->id;
			$e->id_padre = $id_padre;
			$e->nombre = $separador.$row->nombre;
			$result[] = $e;
			$hijos = $this->get_select($row->id,$separador."&nbsp;&nbsp;&nbsp;");
			$result = array_merge($result,$hijos);
    }
    return $result;
  }
	
	function save($data) {
    $id_empresa = parent::get_empresa();
		$this->load->helper("file_helper");
		$this->categorias_relacionadas = $data->categorias_relacionadas;
		unset($data->categorias_relacionadas);
		$data->link = filename($data->nombre,"-",0);
    $data->id_empresa = $id_empresa;
    $id = parent::save($data);

    // Actualizamos las categorias relacionadas
    $i=1;
    $this->db->query("DELETE FROM not_categorias_relacionadas WHERE id_categoria = $id AND id_empresa = $id_empresa");
    foreach($this->categorias_relacionadas as $p) {
      $this->db->insert("not_categorias_relacionadas",array(
        "id_categoria"=>$id,
        "id_empresa"=>$id_empresa,
        "id_relacion"=>$p->id,
        "orden"=>$i,
      ));
      $i++;
    }

    // Calculamos el full link
    $full_link_array = $this->full_link($id);
    $full_link = $full_link_array["full_link"];
    $profundidad = $full_link_array["depth"];
    $this->db->query("UPDATE not_categorias SET full_link = '$full_link', profundidad = '$profundidad' WHERE id = $id AND id_empresa = $id_empresa ");

		return $id;
	}

  function get_id_root($id_categoria,$conf = array()) {
    $id_empresa = isset($conf["id_empresa"]) ? $conf["id_empresa"] : parent::get_empresa();
    $id = 0;
    while(TRUE) {
      $sql = "SELECT * FROM not_categorias WHERE id = $id_categoria AND id_empresa = $id_empresa ";
      $q = $this->db->query($sql);
      if ($q->num_rows() == 0) break;
      $cat = $q->row();
      $id = $cat->id;
      if ($cat->id_padre == 0) break; // Llegamos al final
      $id_categoria = $cat->id_padre;
    }
    return $id;
  }

  function full_link($id_categoria,$conf = array()) {
    $id_empresa = isset($conf["id_empresa"]) ? $conf["id_empresa"] : parent::get_empresa();
    $categorias = array();
    while(TRUE) {
      $sql = "SELECT * FROM not_categorias WHERE id = $id_categoria AND id_empresa = $id_empresa ";
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

	function delete($id) {
		$id_empresa = parent::get_empresa();
		if ($id_empresa === FALSE) return;
		$q = $this->db->query("SELECT * FROM not_categorias WHERE id = $id AND id_empresa = $id_empresa ");
		if ($q->num_rows()>0) {
			$this->db->query("DELETE FROM not_categorias_relacionadas WHERE id_categoria = $id AND id_empresa = $id_empresa");
			$this->db->query("DELETE FROM not_categorias WHERE id = $id AND id_empresa = $id_empresa");
		}
	}
}