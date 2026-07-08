<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Comision_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("aca_comisiones","id","nombre ASC");
	}
	
	function save($data) {
    $alumnos = $data->alumnos;
    unset($data->alumnos);
    $id_comision = parent::save($data);

    // Actualizamos la comision que pertenecen los alumnos
    $this->db->query("UPDATE aca_alumnos SET id_comision = 0 WHERE id_empresa = $data->id_empresa AND id_comision = $id_comision ");
    foreach($alumnos as $al) {
      $this->db->query("UPDATE aca_alumnos SET id_comision = $id_comision WHERE id_empresa = $data->id_empresa AND id_cliente = '$al->id' ");
    }

    return $id_comision;
  }

  function delete($id) {
    // Controlamos que se este borrando un carrera que pertenece a la empresa de la session
    $id_empresa = parent::get_empresa();
    if ($id_empresa === FALSE) return;
    $this->db->query("UPDATE aca_alumnos SET id_comision = 0 WHERE id_comision = $id AND id_empresa = $id_empresa ");
    $this->db->query("DELETE FROM aca_comisiones WHERE id = $id AND id_empresa = $id_empresa ");
  }

  function buscar($conf=array()) {
    $id_empresa = isset($conf["id_empresa"]) ? $conf["id_empresa"] : parent::get_empresa();
    $limit = (isset($conf["limit"]) && $conf["limit"] != FALSE) ? $conf["limit"] : 0;
    $offset = (isset($conf["offset"]) && $conf["offset"] != FALSE) ? $conf["offset"] : 10;
    $filter = (isset($conf["filter"]) && $conf["filter"] != FALSE) ? $conf["filter"] : "";
    $order_by = (isset($conf["order_by"]) && $conf["order_by"] != FALSE) ? $conf["order_by"] : "nombre";
    $order = (isset($conf["order"]) && $conf["order"] != FALSE) ? $conf["order"] : "ASC";
    $sql = "SELECT SQL_CALC_FOUND_ROWS * ";
    $sql.= "FROM aca_comisiones ";
    $sql.= "WHERE id_empresa = $id_empresa ";
    if (!empty($filter)) $sql.= "AND nombre LIKE '%$filter%' ";
    if ($order_by != "cantidad_alumnos") $sql.= "ORDER BY $order_by $order ";
    $sql.= "LIMIT $limit, $offset ";
    $q = $this->db->query($sql);

    $q_total = $this->db->query("SELECT FOUND_ROWS() AS total");
    $total = $q_total->row();

    $salida = array();
    foreach($q->result() as $row) {

      // Cantidad de alumnos de esa comision
      $q_cant = $this->db->query("SELECT IF(COUNT(*) IS NULL,0,COUNT(*)) AS cantidad FROM aca_alumnos WHERE id_empresa = $id_empresa AND id_comision = $row->id ");
      $cant = $q_cant->row();
      $row->cantidad_alumnos = $cant->cantidad;

      $salida[] = $row;
    }

    if ($order_by == "cantidad_alumnos") {
      if ($order == "asc") usort($salida,array('Comision_Model','ordenar_alumnos_asc'));
      elseif ($order == "desc") usort($salida,array('Comision_Model','ordenar_alumnos_desc'));
    }
    return array(
      "results"=>$salida,
      "total"=>$total->total,
    );
  }

  private static function ordenar_alumnos_asc($a,$b) {
    return ($a->cantidad_alumnos <= $b->cantidad_alumnos) ? -1 : 1;
  }
  private static function ordenar_alumnos_desc($a,$b) {
    return ($a->cantidad_alumnos <= $b->cantidad_alumnos) ? 1 : -1;
  }

  function get($id) {
    $id_empresa = parent::get_empresa();
    
    $id = (int)$id;
    $sql = "SELECT A.* ";
    $sql.= "FROM aca_comisiones A ";
    $sql.= "WHERE A.id = $id ";
    $sql.= "AND A.id_empresa = $id_empresa ";
    $q = $this->db->query($sql);
    if ($q->num_rows() == 0) return FALSE;
    $comision = $q->row();

    // Obtenemos las alumnos de esa comision
    $sql = "SELECT ";
    $sql.= " C.nombre, C.id, C.email, C.celular, C.cuit, C.activo, C.path, A.numero_legajo, A.id_comision ";
    $sql.= "FROM aca_alumnos A INNER JOIN clientes C ON (A.id_cliente = C.id AND A.id_empresa = C.id_empresa) ";
    $sql.= "WHERE A.id_empresa = $id_empresa ";
    $sql.= "AND A.id_comision = $id ";
    $sql.= "ORDER BY C.nombre ASC ";
    $qq = $this->db->query($sql);
    $comision->alumnos = $qq->result();

    return $comision;
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

}