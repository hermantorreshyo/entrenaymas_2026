<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Tripulante_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("via_tripulantes","id","nombre ASC");
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

	function save($data) {
		$id = parent::save($data);

		// Si la empresa es NOROESTE, debemos mantener actualizado el usuario
		if ($data->id_empresa == 501) {
			$sql = "SELECT * FROM com_usuarios WHERE id_empresa = 501 AND id = $id ";
			$q = $this->db->query($sql);
			if ($q->num_rows()>0) {
				$sql = "UPDATE com_usuarios SET ";
				$sql.= " nombre = '$data->nombre', ";
				$sql.= " dni = '$data->dni', ";
				$sql.= " telefono = '$data->telefono', ";
				$sql.= " email = '$data->email', ";
				$sql.= " password = '$data->password' ";
				$sql.= "WHERE id = $id AND id_empresa = 501 ";
				$this->db->query($sql);
			} else {
				$sql = "INSERT INTO com_usuarios (id, id_empresa, nombre, dni, telefono, email, password, id_perfiles, activo, admin, estado_inicial) VALUES ( ";
				$sql.= " $id,501,'$data->nombre','$data->dni','$data->telefono','$data->email','$data->password',589,1,0,0) ";
				$this->db->query($sql);
			}
		}

	}

}