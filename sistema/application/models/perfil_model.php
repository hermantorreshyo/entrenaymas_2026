<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Perfil_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("com_perfiles","id");
	}

	function find($filter) 
	{
		$this->db->like("nombre",$filter);
		$query = $this->db->get($this->tabla);
		$result = $query->result();
		$this->db->close();
		return $result;
	}

  // Obtiene un perfil a partir de un nombre
  function get_by_nombre($nombre) {
    $id_empresa = parent::get_empresa();
    $sql = "SELECT * FROM com_perfiles WHERE nombre = '$nombre' AND id_empresa = $id_empresa ";
    $q = $this->db->query($sql);
    if ($q->num_rows() <= 0) return FALSE;
    else return $q->row();
  } 
	
	function get_by_empresa($id_empresa) {
		$this->db->where("id_empresa",$id_empresa);
		$query = $this->db->get($this->tabla);
		$result = $query->result();
		return $result;
	}	

	function save($data)
	{
		// Tomamos el atributo identificador
		$id = isset($data->id) ? $data->id : null;
		$data->id_empresa = $this->get_empresa();

		// Si es nulo o cero
		if ( (is_null($id)) || ($id == 0)) {

			// ESTAMOS INSERTANDO

			// Removemos el ID
			if (isset($data->id)) unset($data->id);

			// Guardamos el array de permisos en una variable auxiliar
			$permisos = $data->permisos;
			unset($data->permisos);
      $data->last_update = time();

			$id_perfil = $this->insert($data);

		} else {
			// Si tiene algun valor, debemos actualizarlo
			if (isset($data->id)) unset($data->id);
			$permisos = $data->permisos;
			unset($data->permisos);
      $data->last_update = time();
			$this->update($id,$data);
			$id_perfil = $id;
		}

		// Eliminamos todo lo configurado hasta el momento
		$this->db->delete("com_permisos_modulos",array("id_perfiles"=>$id_perfil,"id_empresa"=>$data->id_empresa));	

		// Guardamos todos los permisos de los modulos

		// Recorremos todos los modulos
		$query = $this->db->get("com_modulos");
		foreach ($query->result() as $modulo) {

			$permiso = 0; // Por defecto, no se muestra

			// Si el ID se encuentra dentro de los que selecciono el usuario
			foreach($permisos as $p) {
				if ($modulo->id == $p->id) {
					$permiso = $p->permiso;
					
					// Lo guardamos en la base de datos
					$a = array(
						"id_modulos"=>$modulo->id,
						"id_perfiles"=>$id_perfil,
            "id_empresa"=>$data->id_empresa,
						"permiso"=>$permiso,
            "last_update"=>time(),
					);
					$this->db->insert("com_permisos_modulos",$a);
				}
			}
			

		}

		return $id_perfil;
	}



	function delete($id) 
	{	
		$this->db->delete($this->tabla,array($this->ident=>$id));
		$this->db->delete("com_permisos_modulos",array("id_perfiles"=>$id));	
		$this->db->close();
	}	

}