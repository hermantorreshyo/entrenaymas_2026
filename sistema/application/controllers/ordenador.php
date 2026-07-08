<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Ordenador extends CI_Controller {    
    
	function ordenar() {

		$data = $this->input->post("datos");
		$data = json_decode($data);
		$array = isset($data->array) ? $data->array : array();
		$tabla = isset($data->table) ? $data->table : "";
		$campo_orden = isset($data->order_column) ? $data->order_column : "orden";
		$campo_id = isset($data->id_column) ? $data->id_column : "id";
		$where = isset($data->where) ? $data->where : "";
		// Recorremos el array y vamos actualizando sus elementos, en el orden que lo vamos recorriendo
		$i=0;
		foreach($array as $id) {
			$sql = "UPDATE $tabla SET $campo_orden = $i WHERE $campo_id = $id";
			if (!empty($where)) $sql.= " AND $where";
			$this->db->query($sql);
			$i++;
		}
		echo json_encode(array("error"=>false));
	}
    
}