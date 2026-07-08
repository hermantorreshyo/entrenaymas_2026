<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Clasificado_Categoria_Model extends Abstract_Model {
	
	private $categorias_relacionadas = array();
	private $atributos = array();
	
	function __construct() {
		parent::__construct("clasificados_categorias","id","nombre ASC");
	}
	
	function get($id) {
		$id_empresa = parent::get_empresa();
		$id = (int)$id;
		$sql = "SELECT R.* ";
		$sql.= "FROM clasificados_categorias R ";
		$sql.= "WHERE R.id = $id ";
		$sql.= "AND R.id_empresa = $id_empresa ";
		$q = $this->db->query($sql);
		if ($q->num_rows() == 0) return array();
		$row = $q->row();
		
		// Obtenemos las categorias relacionados con ese producto
		/*
		$sql = "SELECT R.id, R.nombre ";
		$sql.= "FROM clasificados_categorias R INNER JOIN clasificados_categorias_relacionadas RR ON (R.id = RR.id_relacion) ";
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
		*/
		
		// Obtenemos las etiquetas de ese producto
		/*
		$sql = "SELECT E.nombre ";
		$sql.= "FROM clasificados_atributos E INNER JOIN clasificados_categorias_atributos ER ON (E.id = ER.id_atributo) ";
		$sql.= "WHERE ER.id_clasificado = $id ";
		$sql.= "ORDER BY ER.orden ASC ";
		$q = $this->db->query($sql);
		$row->atributos = array();
		foreach($q->result() as $r) {
			$row->atributos[] = $r->nombre;
		}
		*/
		
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
	function reorder($elements,$filter_by = "",$filter_value = "") {
		$id_empresa = parent::get_empresa();
		$i=0;
		foreach($elements as $id) {
			$sql = "UPDATE clasificados_categorias SET orden = $i WHERE id = $id AND id_empresa = $id_empresa ";
			$sql.= "AND id_padre = '$filter_value' ";
			$this->db->query($sql);
			$i++;
		}
		return TRUE;
	}
	
	
    function get_arbol($id_padre = 0,$separador = "") {
		$id_empresa = parent::get_empresa();
        $result = array();
        $q = $this->db->query("SELECT * FROM clasificados_categorias WHERE id_empresa = $id_empresa AND id_padre = $id_padre ORDER BY nombre ASC");
        foreach($q->result() as $row) {
			$e = new stdClass();
			$e->id = $row->id;
			$e->id_padre = $id_padre;
			$e->title = $row->nombre;
			$e->nombre_es = $e->title;
			$e->key = $row->id;
			$e->children = $this->get_arbol($row->id,$separador."&nbsp;&nbsp;&nbsp;");
			$result[] = $e;            
        }
        return $result;
    }
	
    function get_select($id_padre = 0,$separador = "") {
		$id_empresa = parent::get_empresa();
        $result = array();
        $q = $this->db->query("SELECT * FROM clasificados_categorias WHERE id_empresa = $id_empresa AND id_padre = $id_padre ORDER BY nombre ASC");
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
		$this->load->helper("file_helper");
		//$this->categorias_relacionadas = $data->categorias_relacionadas;
		//$this->atributos = $data->atributos;
		//unset($data->categorias_relacionadas);
		//unset($data->atributos);
		$data->link = filename($data->nombre,"-",0);
		parent::save($data);
	}
	
	/*
	function post_save($id) {
		$id_empresa = parent::get_empresa();
        // Actualizamos las categorias relacionadas
        $i=1;
		$this->db->query("DELETE FROM clasificados_categorias_relacionadas WHERE id_categoria = $id ");
        foreach($this->categorias_relacionadas as $p) {
            $this->db->insert("clasificados_categorias_relacionadas",array(
                "id_categoria"=>$id,
                "id_relacion"=>$p->id,
                "orden"=>$i,
            ));
            $i++;
        }
		
        // Guardamos las relaciones con los atributos (Y se crean en caso de que no exitan)
        $i=1;
        $this->db->query("DELETE FROM clasificados_categorias_atributos WHERE id_clasificado = $id AND id_empresa = $id_empresa");
        foreach($this->atributos as $e) {
            $tag = new stdClass();
            $tag->id_empresa = $id_empresa;
            $tag->id_clasificado = $id;
            $tag->nombre = $e;
            $this->modelo->save_atributo($tag);
        }		
	}
	*/
	
	function save_atributo($tag) {
		$this->load->helper("file_helper");
		// Primero controlamos si existe la etiqueta
		$q = $this->db->query("SELECT * FROM clasificados_atributos WHERE nombre = '$tag->nombre' AND id_empresa = $tag->id_empresa LIMIT 0,1");
		if ($q->num_rows()<=0) {
			// Si no existe, la guardamos
			$link = filename($tag->nombre,"-",0);
			$this->db->query("INSERT INTO clasificados_atributos (nombre,link,id_empresa) VALUES ('$tag->nombre','$link',$tag->id_empresa)");
			$id_atributo = $this->db->insert_id();
		} else {
			$row = $q->row();
			$id_atributo = $row->id;
		}
		$this->db->query("INSERT INTO clasificados_categorias_atributos (id_empresa,id_clasificado,id_atributo) VALUES ($tag->id_empresa,$tag->id_clasificado,$id_atributo) ");
	}	
	
	function delete($id) {
		$id_empresa = parent::get_empresa();
		if ($id_empresa === FALSE) return;
		$q = $this->db->query("SELECT * FROM clasificados_categorias WHERE id = $id AND id_empresa = $id_empresa ");
		if ($q->num_rows()>0) {
			$this->db->query("DELETE FROM clasificados_categorias_relacionadas WHERE id_categoria = $id ");
			$this->db->query("DELETE FROM clasificados_categorias WHERE id = $id");
		}
	}
}