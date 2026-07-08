<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Tipo_Habitacion_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("tablas_importacion","id","nombre ASC");
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

	function get($id) {
		$id_empresa = parent::get_empresa();
		$row = parent::get($id);
		if ($row !== FALSE) {
			$row->campos = array();

			// Obtenemos los campos
			$sql = "SELECT AI.* FROM tablas_importacion_campos AI WHERE AI.id_tabla_importacion = $id AND AI.id_empresa = $row->id_empresa ORDER BY AI.id ASC";
			$q = $this->db->query($sql);
			foreach($q->result() as $r) {
				$row->campos[] = $r;
			}
		}
		return $row;
	}

	function save($data) {
		$this->load->helper("fecha_helper");
    $this->load->helper("file_helper");
		$campos = $data->campos;
		unset($data->campos);
		$id = parent::save($data);

    // Guardamos los campos
    $this->db->query("DELETE FROM tablas_importacion_campos WHERE id_tabla_importacion = $id AND id_empresa = $data->id_empresa");
    foreach($campos as $im) {
      $this->db->query("INSERT INTO tablas_importacion_campos (id_empresa,id_tabla_importacion,promocion,fecha_desde,fecha_hasta,personas,precio) VALUES($data->id_empresa,$id,0,'$desde','$hasta',$im->cantidad,$im->monto)");
    }
		return $id;
	}

}