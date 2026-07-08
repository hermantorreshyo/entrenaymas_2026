<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Proyecto_Model extends Abstract_Model {
	
	private $modulos = array();
	
	function __construct() {
		parent::__construct("com_proyectos","id","id ASC",0);
	}

  function get($id) {
    $row = parent::get($id);
    if ($row !== FALSE) {
      $sql = "SELECT MP.*, M.nombre AS modulo ";
      $sql.= "FROM com_modulos_proyectos MP INNER JOIN com_modulos M ON (MP.id_modulo = M.id) ";
      $sql.= "WHERE MP.id_proyecto = $id ";
      $sql.= "ORDER BY MP.orden_1 ASC, MP.orden_2 ASC ";
      $q = $this->db->query($sql);
      $row->modulos = $q->result();
    }
    return $row;
  }

  function activos() {
    $sql = "SELECT * FROM com_proyectos WHERE 1=1 ";
    if ($this->db->field_exists("inactivo","com_proyectos")) $sql.= "AND inactivo = 0 ";
    $sql.= "ORDER BY id ASC ";
    $q = $this->db->query($sql);
    return $q->result();
  }
	
	function get_modulos($id_proyecto = 0) {
		if ($id_proyecto == 0) return array(); // SUPERADMIN
		$sql = "SELECT * FROM com_modulos_proyectos MP INNER JOIN com_modulos M ON (MP.id_modulo = M.id) ";
		$sql.= "WHERE M.tiene_pantalla = 1 ";
		if ($id_proyecto != 0) $sql.= "AND MP.id_proyecto = $id_proyecto ";
		$q = $this->db->query($sql);
		return $q->result();
	}

	// Reordena los elementos del arbol
	function reorder($id_proyecto,$elements,$orden = 0, $id_padre = 0) {
		if (isset($elements["id"])) {
			$id = $elements["id"];
			if (!empty($id)) {
				$sql = "UPDATE com_modulos_proyectos SET orden = $orden ";
				$sql.= "WHERE id_proyecto = $id_proyecto AND id_modulo = $id ";
				$this->db->query($sql);				
			}
		}
		if (isset($elements["children"]) && is_array($elements["children"])){
			for($i=0;$i<sizeof($elements["children"]);$i++) {
				$e = $elements["children"][$i];
				$this->reorder($id_proyecto,$e,$i,$id);
			}
		}
	}
	
	function save($data) {
		$this->modulos = $data->modulos;
		unset($data->modulos);
		return parent::save($data);
	}
	
	function post_save($id) {
		// Obtenemos las empresas de ese proyecto (si las tiene)
		$empresas = array();
		$q = $this->db->query("SELECT * FROM empresas WHERE id_proyecto = $id");
		if ($q->num_rows()>0) $empresas = $q->result();
		
		$this->db->query("DELETE FROM com_modulos_proyectos WHERE id_proyecto = $id");
		// Guardamos los modulos que estan habilitados
		foreach($this->modulos as $m) {
      // Insertamo el modulo
      $m->id_proyecto = $id;
      $this->db->insert("com_modulos_proyectos",$m);

      // Si el modulo existe y esta habilitado
			if ($m->estado > 0 && $m->id_modulo != 0) {

				// Actualizamos los modulos de las empresas para saber si hay que agregar alguno
				if (sizeof($empresas)>0) {
					foreach($empresas as $e) {

						$qq = $this->db->query("SELECT * FROM com_modulos_empresas WHERE id_modulo = $m->id_modulo");
            // Si no tiene el modulo, debemos agregarlo
						if ($qq->num_rows()<=0) {
              $f_tar = date("Y-m-d H:i:s");
							$this->db->query("INSERT INTO com_modulos_empresas (id_modulo,id_empresa,fecha_alta,nombre_es) VALUES ($m->id_modulo,$e->id,'$f_tar','$m->nombre_es')");
						}

            // Si el modulo es POR DEFECTO
            if ($m->estado == 2) {
              $q_perfiles = $this->db->query("SELECT * FROM com_perfiles WHERE id_empresa = $e->id ");
              $perfiles = $q_perfiles->result();
              foreach($perfiles as $perfil) {
                $qq = $this->db->query("SELECT * FROM com_permisos_modulos WHERE id_empresa = $e->id AND id_modulos = $m->id_modulo AND id_perfiles = $perfil->id");
                if ($qq->num_rows()<=0) {
                  $this->db->query("INSERT INTO com_permisos_modulos (id_modulos,id_perfiles,permiso,id_empresa) VALUES ($m->id_modulo,$perfil->id,3,$e->id)");
                }
              }
            }

					}
				}
				
			}
		}
		
		
	}
    
}