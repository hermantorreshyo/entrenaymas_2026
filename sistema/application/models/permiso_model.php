<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Permiso_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("com_permisos_modulos","id_permisos_modulos");
	}

	public function get_permisos($config = array()) {
    
    $id_perfil = isset($config["id_perfil"]) ? $config["id_perfil"] : 0;
    $id_proyecto = isset($config["id_proyecto"]) ? $config["id_proyecto"] : 0;
    $lang = isset($config["lang"]) ? $config["lang"] : "";
    $id_empresa = 0;
		
		// MODULOS DE SUPERADMIN
		if ($id_perfil == -1) {

      $sql = "SELECT MP.*, M.id, M.dir, MP.nombre_es AS title, M.nombre, 1 AS tiene_pantalla, 1 AS visible ";
      $sql.= "FROM com_modulos_proyectos MP ";
      $sql.= " INNER JOIN com_modulos M ON (M.id = MP.id_modulo) ";
      $sql.= "AND MP.id_proyecto = $id_proyecto ";
      $sql.= "ORDER BY MP.orden_1 ASC, MP.orden_2 ASC ";

		} else {

      $id_empresa = $this->get_empresa();
      $sql = "SELECT MP.*, MP.nombre_es AS title, MP.nombre_en, ";
      $sql.= " IF(M.id IS NULL,0,M.id) AS id, ";
      $sql.= " IF(M.dir IS NULL,0,M.dir) AS dir, ";
      $sql.= " IF(M.tiene_pantalla IS NULL,1,M.tiene_pantalla) AS tiene_pantalla, ";
      $sql.= " IF(M.nombre IS NULL,'',M.nombre) AS nombre ";
      $sql.= "FROM com_modulos_proyectos MP ";
      $sql.= " LEFT JOIN com_modulos M ON (M.id = MP.id_modulo) ";
      $sql.= "WHERE MP.id_proyecto = $id_proyecto ";
      $sql.= "ORDER BY MP.orden_1 ASC, MP.orden_2 ASC ";
		}
		$query = $this->db->query($sql);
		$elementos = array();
		foreach($query->result() as $row) {
      $e = $row;
			$e->permiso = 0;
			
			if ($id_perfil != -1 && $e->id_modulo != 0) {

        // Controlams si la empresa tiene ese modulo habilitado
        $sql = "SELECT * FROM com_modulos_empresas ";
        $sql.= "WHERE id_modulo = $e->id_modulo AND id_empresa = $id_empresa ";
        $qq = $this->db->query($sql);
        if ($qq->num_rows()<=0) continue;
        $mod_empresa = $qq->row();
        if ($lang == "en") {
          if (!empty($mod_empresa->nombre_en)) {
            $e->title = $mod_empresa->nombre_en;
          } else {
            if (!empty($row->nombre_en)) {
              $e->title = $row->nombre_en;  
            } else {
              $e->title = $mod_empresa->nombre_es;  
            }
          }
        } else {
          if (!empty($mod_empresa->nombre_es)) {
            $e->title = $mod_empresa->nombre_es;
          }
        }
        if (isset($mod_empresa->visible)) {
          $e->visible = $mod_empresa->visible;
        } else {
          $e->visible = 1;
        }

				// Controlamos si el perfil tiene ese modulo marcado
				$this->db->where("id_perfiles = $id_perfil");
				$this->db->where("id_modulos = $row->id");
        if ($id_empresa != 0) $this->db->where("id_empresa = $id_empresa");
				$q = $this->db->get("com_permisos_modulos");
				if ($q->num_rows()>0) {
					$r = $q->row();
					$e->permiso = $r->permiso;
				} else {
          $e->permiso = 0;
        }

			} else {
				$e->permiso = 3;
			}
      $elementos[] = $e;
		}
		return $elementos;		
	}

}