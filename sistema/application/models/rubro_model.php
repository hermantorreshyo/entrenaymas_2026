<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Rubro_Model extends Abstract_Model {
	
	private $rubros_relacionados = array();
	
	function __construct() {
		parent::__construct("rubros","id","nombre ASC");
	}

  function recalcular_full_link($conf = array()) {
    $id_empresa = isset($conf["id_empresa"]) ? $conf["id_empresa"] : parent::get_empresa();
    $id = isset($conf["id"]) ? $conf["id"] : 0;
    $sql = "SELECT * FROM rubros WHERE id_empresa = $id_empresa ";
    if ($id != 0) $sql.= "AND id = $id ";
    $q = $this->db->query($sql);
    foreach($q->result() as $r) {
      $f = $this->full_link($r->id,array(
        "id_empresa"=>$r->id_empresa,
      ));
      $this->db->query("UPDATE rubros SET full_link = '".$f["full_link"]."', profundidad = '".$f["profundidad"]."' WHERE id = $r->id AND id_empresa = $r->id_empresa ");
    }    
  }

  function full_link($id_categoria,$conf = array()) {
    $id_empresa = isset($conf["id_empresa"]) ? $conf["id_empresa"] : parent::get_empresa();
    $categorias = array();
    while(TRUE) {
      $sql = "SELECT * FROM rubros WHERE id = $id_categoria AND id_empresa = $id_empresa ";
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
	
	function get($id) {
		$id_empresa = parent::get_empresa();
		$id = (int)$id;
		$sql = "SELECT R.* ";
		$sql.= "FROM rubros R ";
		$sql.= "WHERE R.id = $id ";
		$sql.= "AND R.id_empresa = $id_empresa ";
		$q = $this->db->query($sql);
		if ($q->num_rows() == 0) return array();
		$row = $q->row();
		
		// Obtenemos las categorias relacionados con ese producto
		$sql = "SELECT R.id, R.nombre ";
		$sql.= "FROM rubros R INNER JOIN rubros_relacionados RR ON (R.id = RR.id_relacion AND R.id_empresa = RR.id_empresa) ";
		$sql.= "WHERE RR.id_rubro = $id AND R.id_empresa = $id_empresa ";
		$sql.= "ORDER BY RR.orden ASC ";
		$q = $this->db->query($sql);
		$row->rubros_relacionados = array();
		foreach($q->result() as $r) {
			$obj = new stdClass();
			$obj->id = $r->id;
			$obj->nombre = $r->nombre;
			$row->rubros_relacionados[] = $obj;
		}		

    $sql = "SELECT AI.* FROM rubros_images AI WHERE AI.id_rubro = $id AND AI.id_empresa = $id_empresa ORDER BY AI.orden ASC";
    $q = $this->db->query($sql);
    $row->images = array();
    foreach($q->result() as $r) {
      $row->images[] = $r->path;
    }

    $sql = "SELECT * FROM rubros_web WHERE id_empresa = $id_empresa AND id_rubro = $id ";
    $q = $this->db->query($sql);
    if ($q->num_rows() > 0) {
      $r = $q->row();
      $row->seo_title = $r->seo_title;
      $row->seo_description = $r->seo_description;
      $row->texto = $r->texto;
      $row->texto_en = $r->texto_en;
      $row->texto_pt = $r->texto_pt;
      $row->h1 = $r->h1;
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
				$sql = "UPDATE rubros SET orden = $orden, id_padre = $id_padre ";
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

  // Ordena los rubros de manera alfabetica
  function reordenar_todos($config = array()) {
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $order_by = isset($config["order_by"]) ? $config["order_by"] : "nombre ASC ";
    $sql = "SELECT * FROM rubros WHERE id_empresa = $id_empresa ORDER BY $order_by ";
    $q = $this->db->query($sql);
    $i=0;
    foreach($q->result() as $r) {
      $sql = "UPDATE rubros SET orden = $i WHERE id_empresa = $id_empresa AND id = $r->id ";
      $this->db->query($sql);
      $i++;
    }
  }

  function recalcular_links($config = array()) {
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $this->load->helper("file_helper");
    $q = $this->db->query("SELECT * FROM rubros WHERE id_empresa = $id_empresa");
    foreach($q->result() as $r) {
      $link = filename($r->nombre,"-",0);
      $this->db->query("UPDATE rubros SET link = '$link' WHERE id_empresa = $id_empresa AND id = '$r->id' ");
    }    
  }
	
	
  function get_arbol($id_padre = 0,$separador = "",$config = array()) {
    $id_usuario = isset($config["id_usuario"]) ? $config["id_usuario"] : 0;
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $order_by = isset($config["order_by"]) ? $config["order_by"] : "orden ASC ";
    $result = array();
    $sql = "SELECT * FROM rubros WHERE id_empresa = $id_empresa AND id_padre = $id_padre ";
    if ($id_usuario != 0) $sql.= "AND (id_usuario = $id_usuario OR id_usuario = 0) ";
    $sql.= "ORDER BY $order_by ";
    $q = $this->db->query($sql);
    foreach($q->result() as $row) {
      $e = new stdClass();
      $e->id = $row->id;
      $e->id_padre = $id_padre;
      $e->title = $row->nombre;
      $e->nombre_es = $e->title;
      $e->key = $row->id;
      $e->id_usuario = $row->id_usuario;
      $e->children = $this->get_arbol($row->id,$separador."&nbsp;&nbsp;&nbsp;",$config);
      $result[] = $e;            
    }
    return $result;
  }

  function get_select($id_padre = 0,$separador = "",$config = array()) {
    $id_usuario = isset($config["id_usuario"]) ? $config["id_usuario"] : 0;
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $result = array();
    $sql = "SELECT * FROM rubros WHERE id_empresa = $id_empresa AND id_padre = $id_padre ";
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
  function get_ids_rubros($id_categoria_padre) {
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

  function save($data) {
    $this->load->helper("file_helper");
    $this->rubros_relacionados = $data->rubros_relacionados;
    $images = $data->images;
    unset($data->rubros_relacionados);
    unset($data->images);
    $data->link = filename($data->nombre,"-",0);

    // Guardamos los datos que corresponden a la otra tabla
    $seo_title = isset($data->seo_title) ? $data->seo_title : "";
    $seo_description = isset($data->seo_description) ? $data->seo_description : "";
    $texto = isset($data->texto) ? $data->texto : "";
    $texto_en = isset($data->texto_en) ? $data->texto_en : "";
    $texto_pt = isset($data->texto_pt) ? $data->texto_pt : "";
    $h1 = isset($data->h1) ? $data->h1 : "";

    $id = parent::save($data);

    // Guardamos las imagenes
    $this->db->query("DELETE FROM rubros_images WHERE id_rubro = $id AND id_empresa = $data->id_empresa");
    $k=0;
    foreach($images as $im) {
      $this->db->query("INSERT INTO rubros_images (id_empresa,id_rubro,path,orden) VALUES($data->id_empresa,$id,'$im',$k)");
      $k++;
    }

    // Calculamos el full link
    $full_link_array = $this->full_link($id);
    $full_link = $full_link_array["full_link"];
    $profundidad = $full_link_array["depth"];
    $this->db->query("UPDATE rubros SET full_link = '$full_link', profundidad = '$profundidad' WHERE id = $id AND id_empresa = $data->id_empresa ");

    // Guardamos la tabla rubros_web
    $sql = "SELECT * FROM rubros_web WHERE id_empresa = $data->id_empresa AND id_rubro = $id ";
    $q = $this->db->query($sql);
    if ($q->num_rows() > 0) {
      $sql = "UPDATE rubros_web SET ";
      $sql.= " seo_title = '$seo_title', ";
      $sql.= " seo_description = '$seo_description', ";
      $sql.= " texto = '$texto', ";
      $sql.= " texto_en = '$texto_en', ";
      $sql.= " h1 = '$h1', ";
      $sql.= " texto_pt = '$texto_pt' ";
      $sql.= "WHERE id_rubro = $id AND id_empresa = $data->id_empresa ";
    } else {
      $sql = "INSERT INTO rubros_web ( id_empresa, id_rubro, seo_title, seo_description, texto, texto_en, texto_pt, h1) VALUES (";
      $sql.= " '$data->id_empresa', '$id', '$seo_title', '$seo_description', '$texto', '$texto_en', '$texto_pt', '$h1' )";
    }
    $this->db->query($sql);

    // Recalculamos el link de las categorias hijas, nietas, etc
    $this->recorrer_arbol_recalcular_full_link($this->get_arbol($id),array(
      "id_empresa"=>$data->id_empresa,
    ));

    return $id;
  }

  function recorrer_arbol_recalcular_full_link($array,$config = array()) {
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    foreach($array as $item) {
      $this->recalcular_full_link(array(
        "id_empresa"=>$id_empresa,
        "id"=>$item->id,
      ));
      if (isset($item->children)) {
        $this->recorrer_arbol_recalcular_full_link($item->children,array(
          "id_empresa"=>$id_empresa
        ));
      }
    }
  }

  function post_save($id) {
    $id_empresa = parent::get_empresa();
    // Actualizamos las categorias relacionadas
    $i=1;
    $this->db->query("DELETE FROM rubros_relacionados WHERE id_rubro = $id AND id_empresa = $id_empresa ");
    foreach($this->rubros_relacionados as $p) {
      $this->db->insert("rubros_relacionados",array(
        "id_rubro"=>$id,
        "id_empresa"=>$id_empresa,
        "id_relacion"=>$p->id,
        "orden"=>$i,
      ));
      $i++;
    }
  }

  function delete($id) {
    $id_empresa = parent::get_empresa();
    if ($id_empresa === FALSE) return;
    $q = $this->db->query("SELECT * FROM rubros WHERE id = $id AND id_empresa = $id_empresa ");
    if ($q->num_rows()>0) {
      $this->db->query("DELETE FROM rubros_web WHERE id_rubro = $id AND id_empresa = $id_empresa");
      $this->db->query("DELETE FROM rubros_images WHERE id_rubro = $id AND id_empresa = $id_empresa");
      $this->db->query("DELETE FROM rubros_relacionados WHERE id_rubro = $id AND id_empresa = $id_empresa");
      $this->db->query("DELETE FROM rubros WHERE id = $id AND id_empresa = $id_empresa");
    }
  }

  // Crea una nueva categoria a partir de un nombre, se usa en las importaciones
  // Si ya existe devuelve el ID
  function create($config = array()) {

    $this->load->helper("file_helper");
    $id_empresa = (isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa());
    $id_padre = (isset($config["id_padre"]) ? $config["id_padre"] : 0);
    $nombre = (isset($config["nombre"]) ? $config["nombre"] : "");
    if (empty($nombre)) return -1;

    // Consultamos si el rubro ya existe
    $sql = "SELECT * FROM rubros WHERE UPPER(nombre) = '".mb_strtoupper($nombre)."' ";
    $sql.= "AND id_empresa = $id_empresa ";
    if (!empty($id_padre)) $sql.= "AND id_padre = $id_padre ";
    $q = $this->db->query($sql);
    if ($q->num_rows()>0) {
      // Obtenemos el ID del rubro
      $row = $q->row();
      $id_rubro = $row->id;

    } else {
      // Debemos insertar el nuevo rubro
      $sql = "SELECT IF(MAX(orden) IS NULL,0,MAX(orden)) AS maximo FROM rubros WHERE id_empresa = $id_empresa ";
      $q_max = $this->db->query($sql);
      $maximo = $q_max->row();
      $orden = $maximo->maximo + 1;
      $link = filename($nombre,"-",0);
      $sql = "INSERT INTO rubros (id_empresa, nombre, id_padre, link, orden, activo) VALUES (";
      $sql.= "$id_empresa, '$nombre', '$id_padre', '$link', $orden, 1)";
      $this->db->query($sql);
      $id_rubro = $this->db->insert_id();
    }

    $s = $this->full_link($id_rubro,array(
      "id_empresa"=>$id_empresa,
    ));
    $sql = "UPDATE rubros SET full_link = '".$s["full_link"]."' WHERE id_empresa = $id_empresa AND id = $id_rubro ";
    $this->db->query($sql);

    return $id_rubro;
  }


    /*
    function get_arbol() {
		$id_empresa = parent::get_empresa();
        $result = array();
        $q = $this->db->query("SELECT * FROM rubros WHERE id_empresa = $id_empresa ORDER BY nombre ASC");
        foreach($q->result() as $row) {
			$e = new stdClass();
			$e->id = $row->id;
			$e->id_padre = 0;
			$e->title = $row->nombre;
			$e->key = $row->id;
            $children = array();
            $qq = $this->db->query("SELECT * FROM subrubros WHERE id_rubro = $row->id AND id_empresa = $id_empresa ORDER BY nombre ASC");
            foreach($qq->result() as $rrow) {
                $ee = new stdClass();
                $ee->id = $rrow->id;
                $ee->id_padre = $row->id;
                $ee->title = $rrow->nombre;
                $ee->key = $rrow->id;
                $children[] = $ee;
            }
			$e->children = $children;
			$result[] = $e;            
        }
        return $result;
    }
    */
  }