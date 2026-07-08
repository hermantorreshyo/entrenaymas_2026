<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Tdf_Sorteo_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("custom_tdf_sorteos","id");
	}

  function get_numero($id_sorteo,$maximo=0) {
    $id_empresa = parent::get_empresa();
    $rand = 0;
    if ($maximo > 0) {
      while(TRUE) {
        $rand = rand(0,$maximo);
        $sql = "SELECT * FROM custom_tdf_sorteos_clientes ";
        $sql.= "WHERE id_empresa = $id_empresa ";
        $sql.= "AND id_sorteo = $id_sorteo ";
        $sql.= "AND numero = $rand ";
        $q = $this->db->query($sql);
        if ($q->num_rows()<=0) break;
      }
    }
    return $rand;
  }
	
	function find($filter) {
		$id_empresa = parent::get_empresa();
		$this->db->where("id_empresa",$id_empresa);
		$this->db->like("marca",$filter);
		$this->db->or_like("modelo",$filter);
		$query = $this->db->get($this->tabla);
		$result = $query->result();
		$this->db->close();
		return $result;
	}
    
	function buscar($conf = array()) {
		
		$id_empresa = parent::get_empresa();
    $limit = isset($conf["limit"]) ? $conf["limit"] : 0;
    $offset = isset($conf["offset"]) ? $conf["offset"] : 20;
    $order = isset($conf["order"]) ? $conf["order"] : "";
		
		$sql = "SELECT SQL_CALC_FOUND_ROWS A.*, ";
    $sql.= " IF(A.fecha_desde = '0000-00-00 00:00:00','',DATE_FORMAT(A.fecha_desde,'%d/%m/%Y %H:%i')) AS fecha_desde, ";
    $sql.= " IF(A.fecha_hasta = '0000-00-00 00:00:00','',DATE_FORMAT(A.fecha_hasta,'%d/%m/%Y %H:%i')) AS fecha_hasta, ";
		$sql.= " IF(TI.nombre IS NULL,'',TI.nombre) AS tipo ";
		$sql.= "FROM custom_tdf_sorteos A ";
		$sql.= "LEFT JOIN veh_tipos TI ON (A.id_tipo = TI.id) ";
		$sql.= "WHERE 1=1 ";
		$sql.= "AND A.id_empresa = $id_empresa ";
		if (isset($conf["filter"]) && !empty($conf["filter"])) $sql.= "AND A.marca LIKE '%".$conf["filter"]."%' ";
		if (empty($order)) $sql.= "ORDER BY A.fecha_creacion DESC ";
		else $sql.= "ORDER BY $order ";
		if ($offset != 0) $sql.= "LIMIT $limit, $offset ";

    $q = $this->db->query($sql);
    $q_total = $this->db->query("SELECT FOUND_ROWS() AS total");
    $total = $q_total->row();
		
		return array(
      "results"=>$q->result(),
      "total"=>$total->total
		);
	}
	
	function get($id) {
		$id_empresa = parent::get_empresa();
		// Obtenemos los datos del propiedad
		$id = (int)$id;
		$sql = "SELECT A.*, ";
    $sql.= " IF(A.fecha_desde = '0000-00-00 00:00:00','',DATE_FORMAT(A.fecha_desde,'%d/%m/%Y %H:%i')) AS fecha_desde, ";
    $sql.= " IF(A.fecha_hasta = '0000-00-00 00:00:00','',DATE_FORMAT(A.fecha_hasta,'%d/%m/%Y %H:%i')) AS fecha_hasta, ";
		$sql.= " IF(TI.nombre IS NULL,'',TI.nombre) AS tipo ";
		$sql.= "FROM custom_tdf_sorteos A ";
		$sql.= "LEFT JOIN veh_tipos TI ON (A.id_tipo = TI.id) ";
		$sql.= "WHERE A.id = $id ";
		$sql.= "AND A.id_empresa = $id_empresa ";
		$q = $this->db->query($sql);
		if ($q->num_rows() == 0) return array();
		$auto = $q->row();
		
		// Obtenemos las imagenes de ese propiedad
		$sql = "SELECT AI.* FROM custom_tdf_sorteos_images AI WHERE AI.id_sorteo = $id AND AI.id_empresa = $id_empresa ORDER BY AI.orden ASC";
		$q = $this->db->query($sql);
		$auto->images = array();
		foreach($q->result() as $r) {
			$auto->images[] = $r->path;
		}

    // Obtenemos las imagenes de ese propiedad
    $sql = "SELECT SC.*, C.nombre, C.email, ";
    $sql.= " IF(SC.fecha = '0000-00-00 00:00:00','',DATE_FORMAT(SC.fecha,'%d/%m/%Y %H:%i')) AS fecha ";
    $sql.= "FROM custom_tdf_sorteos_clientes SC ";
    $sql.= "INNER JOIN clientes C ON (SC.id_cliente = C.id AND SC.id_empresa = C.id_empresa)";
    $sql.= "WHERE SC.id_sorteo = $id AND SC.id_empresa = $id_empresa ";
    $sql.= "ORDER BY SC.fecha DESC ";
    $q = $this->db->query($sql);
    $auto->clientes = array();
    foreach($q->result() as $r) {
      $auto->clientes[] = $r;
    }

		return $auto;
	}
	
	function delete($id) {
		// Controlamos que se este borrando un propiedad que pertenece a la empresa de la session
		$id_empresa = parent::get_empresa();
		if ($id_empresa === FALSE) return;
		$q = $this->db->query("SELECT * FROM custom_tdf_sorteos WHERE id = $id AND id_empresa = $id_empresa ");
		if ($q->num_rows()>0) {
			$this->db->query("DELETE FROM custom_tdf_sorteos_images WHERE id_sorteo = $id AND id_empresa = $id_empresa");
      $this->db->query("DELETE FROM custom_tdf_sorteos_clientes WHERE id_sorteo = $id AND id_empresa = $id_empresa");
			$this->db->query("DELETE FROM custom_tdf_sorteos WHERE id = $id AND id_empresa = $id_empresa");
		}
	}

}