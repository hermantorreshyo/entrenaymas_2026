<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Modulo_Model extends Abstract_Model {

	function __construct() {
		parent::__construct("com_modulos","id_modulos");
	}

  function get_all($limit = null, $offset = null,$order_by = '',$order = '')  {
    $sql = "SELECT M.* ";
    $sql.= "FROM com_modulos M ";
    $q = $this->db->query($sql);    
    return $q->result();
  }
	
	// Devuelve la lista de modulos habilitados para ese proyecto
	function get_by_proyecto($id_proyecto = 0,$id_padre = 0) {

		// Si id_proyecto = 0, devuelve la lista completa de modulos
		if ($id_proyecto == 0) {
			$sql = "SELECT M.*, M.etiqueta AS title, ";
			$sql.= " 0 AS estado ";
			$sql.= "FROM com_modulos M ";
			$sql.= "WHERE M.modulo_padre = $id_padre ";
			$sql.= "ORDER BY M.orden ASC ";
		} else {
			$sql = "SELECT M.*, ";
			$sql.= " IF(MP.nombre_es IS NULL OR MP.nombre_es = '',M.etiqueta,MP.nombre_es) AS title, ";
			$sql.= " IF(MP.estado IS NULL,0,MP.estado) AS estado ";
			$sql.= "FROM com_modulos M ";
			$sql.= "LEFT JOIN com_modulos_proyectos MP ON (M.id = MP.id_modulo AND MP.id_proyecto = $id_proyecto) ";
			$sql.= "WHERE M.modulo_padre = $id_padre ";
			$sql.= "ORDER BY IF(MP.orden IS NULL,2147483647,MP.orden) ASC, M.orden ASC ";
		}
		$query = $this->db->query($sql);
		$elementos = array();
		foreach($query->result() as $row) {
			$e = new stdClass();
			$e->children = $this->get_by_proyecto($id_proyecto,$row->id);
			$e->id = $row->id;	
			$e->estado = $row->estado;
			$e->title = $row->title;		
			$elementos[] = $e;
		}
		return $elementos;
	}
	
	
	
	// Devuelve la lista de modulos habilitados para esa empresa y los disponibles para ese proyecto
	function get_by_empresa($id_empresa = 0,$id_padre = 0) {
		
		$id_proyecto = 0;
		if ($id_empresa != 0) {
			$this->load->model("Empresa_Model");
			$empresa = $this->Empresa_Model->get($id_empresa);
			$id_proyecto = $empresa->id_proyecto;			
		}

    $sql = "SELECT MP.*, M.nombre AS modulo FROM com_modulos_proyectos MP ";
    $sql.= " INNER JOIN com_modulos M ON (MP.id_modulo = M.id) ";
    $sql.= "WHERE MP.id_proyecto = $id_proyecto ";
    $sql.= "ORDER BY MP.orden_1 ASC, MP.orden_2 ASC ";
		$query = $this->db->query($sql);
		$elementos = array();
		foreach($query->result() as $row) {
			$e = $row;
			$e->habilitado = 0;
			$e->title = $row->nombre_es;
			$e->modulo = $row->modulo;
			if ($id_empresa != 0 && $row->id_modulo != 0) {
				// Sino se busca si fue asignado para esa empresa
				$q = $this->db->query("SELECT * FROM com_modulos_empresas WHERE id_modulo = $row->id_modulo AND id_empresa = $id_empresa ");
				if ($q->num_rows()>0) {
					$me = $q->row();
					$e->habilitado = 1;
					$e->visible = (isset($me->visible)) ? $me->visible : 1;
					if (!empty($me->nombre_es)) $e->title = $me->nombre_es;
				} else {
					$e->habilitado = 0;
					$e->visible = 1;
				}
			}
			$elementos[] = $e;
		}
		return $elementos;
	}	
	
	
	
	

	// Los modulos no se pueden modificar, borrar o insertar
	// por eso simplemente sobreescribimos estos metodos
	// y no le ponemos ningun comportamiento

	function update($id,$data) {}

	function insert($data) {}

	function save($data) {}

	function delete($id) {}

}