<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Tipo_Habitacion_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("hot_tipos_habitaciones","id","nombre ASC");
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

	function get($id,$id_empresa=0) {
		if ($id_empresa == 0) $id_empresa = parent::get_empresa();
		$this->load->helper("fecha_helper");
		parent::set_empresa($id_empresa);
		$row = parent::get($id);
		if ($row !== FALSE) {
			$row->precios = array();
			$row->promociones = array();
			$row->images = array();

			// Obtenemos las imagenes
			$sql = "SELECT AI.* FROM hot_tipos_habitaciones_images AI WHERE AI.id_tipo_habitacion = $id AND AI.id_empresa = $row->id_empresa ORDER BY AI.orden ASC";
			$q = $this->db->query($sql);
			foreach($q->result() as $r) {
				$row->images[] = $r->path;
			}

			// Obtenemos los precios
			$sql = "SELECT AI.* FROM hot_precios AI WHERE AI.id_tipo_habitacion = $id AND AI.id_empresa = $row->id_empresa ORDER BY AI.id ASC";
			$q = $this->db->query($sql);
			foreach($q->result() as $r) {
				$r->fecha_desde = fecha_es($r->fecha_desde);
				$r->fecha_hasta = fecha_es($r->fecha_hasta);
				if ($r->promocion == 0) {
					$row->precios[] = $r;
				} else if ($r->promocion == 1) {
					$row->promociones[] = $r;
				}
			}
		}
		return $row;
	}

	function save($data) {
		$this->load->helper("fecha_helper");
    $this->load->helper("file_helper");
		$precios = $data->precios;
		$promociones = $data->promociones;
		$images = $data->images;
		unset($data->precios);
		unset($data->promociones);
		unset($data->images);
		$id = parent::save($data);

    $data->link = "habitacion/".filename($data->nombre,"-",0)."-".$id."/";
    $this->db->query("UPDATE hot_tipos_habitaciones SET link = '$data->link' WHERE id = $id AND id_empresa = $data->id_empresa ");

    // Guardamos las imagenes
    $this->db->query("DELETE FROM hot_tipos_habitaciones_images WHERE id_tipo_habitacion = $id AND id_empresa = $data->id_empresa");
    $k=0;
    foreach($images as $im) {
      $this->db->query("INSERT INTO hot_tipos_habitaciones_images (id_empresa,id_tipo_habitacion,path,orden) VALUES($data->id_empresa,$id,'$im',$k)");
      $k++;
    }

    // Guardamos los precios
    $this->db->query("DELETE FROM hot_precios WHERE id_tipo_habitacion = $id AND id_empresa = $data->id_empresa AND promocion < 2");
    foreach($precios as $im) {
    	$desde = fecha_mysql($im->fecha_desde);
    	$hasta = fecha_mysql($im->fecha_hasta);
      $this->db->query("INSERT INTO hot_precios (id_empresa,id_tipo_habitacion,promocion,fecha_desde,fecha_hasta,personas,precio) VALUES($data->id_empresa,$id,0,'$desde','$hasta',$im->cantidad,$im->monto)");
    }
    foreach($promociones as $im) {
    	$desde = fecha_mysql($im->fecha_desde);
    	$hasta = fecha_mysql($im->fecha_hasta);
      $this->db->query("INSERT INTO hot_precios (id_empresa,id_tipo_habitacion,promocion,fecha_desde,fecha_hasta,personas,precio) VALUES($data->id_empresa,$id,1,'$desde','$hasta',$im->cantidad,$im->monto)");
    }
		return $id;
	}

	// Obtiene el precio por noche de una habitacion
	function precio_por_dia($config = array()) {
		$id_empresa = (isset($config["id_empresa"])) ? $config["id_empresa"] : parent::get_empresa();
		$fecha = (isset($config["fecha"])) ? $config["fecha"] : date("Y-m-d");
		$id_tipo_habitacion = (isset($config["id_tipo_habitacion"])) ? $config["id_tipo_habitacion"] : 0;
		$personas = (isset($config["personas"])) ? $config["personas"] : 0;
		$sql = "SELECT precio FROM hot_precios ";
		$sql.= "WHERE id_empresa = $id_empresa ";
		$sql.= "AND fecha_desde <= '$fecha' AND '$fecha' <= fecha_hasta ";
		$sql.= "AND id_tipo_habitacion = $id_tipo_habitacion ";
		if (!empty($personas)) $sql.= "AND personas = $personas ";
		// Promocion = 2 -> Precio puesto a mano para esa fecha
		// Promocion = 1 -> Rango de precios en promocion
		// Promocion = 1 -> Rango de precios de temporada
		$sql.= "ORDER BY promocion DESC ";
		$sql.= "LIMIT 0,1 "; // Tomamos el primero
		$q = $this->db->query($sql);

		// Encontramos un registro
		if ($q->num_rows()>0) {
			$row = $q->row();
			return $row->precio;

		// No encontramos un registro, tomamos la tarifa base
		} else {
			$sql = "SELECT precio FROM hot_tipos_habitaciones ";
			$sql.= "WHERE id_empresa = $id_empresa ";
			$sql.= "AND id = $id_tipo_habitacion ";
			$q = $this->db->query($sql);
			if ($q->num_rows()>0) {
				$row = $q->row();
				return $row->precio;
			} else {
				return 0;
			}
		}
	}


}