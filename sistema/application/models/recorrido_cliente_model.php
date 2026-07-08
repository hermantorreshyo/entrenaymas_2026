<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Recorrido_Cliente_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("recorridos","id","nombre ASC");
	}
	
	function save($data) {
    $clientes = $data->clientes;
    unset($data->clientes);
    $id_recorrido = parent::save($data);

    // Actualizamos la recorrido que pertenecen los clientes
    $this->db->query("DELETE FROM recorridos_clientes WHERE id_recorrido = $id_recorrido AND id_empresa = $data->id_empresa ");
    $i=0;
    foreach($clientes as $al) {
      $this->db->query("INSERT INTO recorridos_clientes (id_empresa,id_recorrido,id_cliente,orden) VALUES ($data->id_empresa,$id_recorrido,$al->id,$i)");
      $i++;
    }
    return $id_recorrido;
  }

  function delete($id) {
    // Controlamos que se este borrando un carrera que pertenece a la empresa de la session
    $id_empresa = parent::get_empresa();
    if ($id_empresa === FALSE) return;
    $this->db->query("DELETE FROM recorridos WHERE id = $id AND id_empresa = $id_empresa ");
    $this->db->query("DELETE FROM recorridos_clientes WHERE id_recorrido = $id AND id_empresa = $id_empresa ");
  }

  function buscar($conf=array()) {
    $id_empresa = isset($conf["id_empresa"]) ? $conf["id_empresa"] : parent::get_empresa();
    $limit = (isset($conf["limit"]) && $conf["limit"] != FALSE) ? $conf["limit"] : 0;
    $offset = (isset($conf["offset"]) && $conf["offset"] != FALSE) ? $conf["offset"] : 10;
    $filter = (isset($conf["filter"]) && $conf["filter"] != FALSE) ? $conf["filter"] : "";
    $order_by = (isset($conf["order_by"]) && $conf["order_by"] != FALSE) ? $conf["order_by"] : "dia";
    $order = (isset($conf["order"]) && $conf["order"] != FALSE) ? $conf["order"] : "ASC";
    $sql = "SELECT SQL_CALC_FOUND_ROWS R.* ";
    $sql.= "FROM recorridos R ";
    $sql.= "WHERE R.id_empresa = $id_empresa ";
    if (!empty($filter)) $sql.= "AND V.nombre LIKE '%$filter%' ";
    if ($order_by != "cantidad_clientes") $sql.= "ORDER BY $order_by $order ";
    $sql.= "LIMIT $limit, $offset ";
    $q = $this->db->query($sql);

    $q_total = $this->db->query("SELECT FOUND_ROWS() AS total");
    $total = $q_total->row();

    $salida = array();
    foreach($q->result() as $row) {

      // Cantidad de clientes de esa recorrido
      $q_cant = $this->db->query("SELECT IF(COUNT(*) IS NULL,0,COUNT(*)) AS cantidad FROM recorridos_clientes WHERE id_empresa = $id_empresa AND id_recorrido = $row->id ");
      $cant = $q_cant->row();
      $row->cantidad_clientes = $cant->cantidad;

      $salida[] = $row;
    }

    if ($order_by == "cantidad_clientes") {
      if ($order == "asc") usort($salida,array('Recorrido_Cliente_Model','ordenar_clientes_asc'));
      elseif ($order == "desc") usort($salida,array('Recorrido_Cliente_Model','ordenar_clientes_desc'));
    }
    return array(
      "results"=>$salida,
      "total"=>$total->total,
    );
  }

  private static function ordenar_clientes_asc($a,$b) {
    return ($a->cantidad_clientes <= $b->cantidad_clientes) ? -1 : 1;
  }
  private static function ordenar_clientes_desc($a,$b) {
    return ($a->cantidad_clientes <= $b->cantidad_clientes) ? 1 : -1;
  }

  function get($id) {
    $id_empresa = parent::get_empresa();
    
    $id = (int)$id;
    $sql = "SELECT A.* ";
    $sql.= "FROM recorridos A ";
    $sql.= "WHERE A.id = $id ";
    $sql.= "AND A.id_empresa = $id_empresa ";
    $q = $this->db->query($sql);
    if ($q->num_rows() == 0) return FALSE;
    $recorrido = $q->row();

    // Obtenemos las clientes de esa recorrido
    $sql = "SELECT ";
    $sql.= " C.nombre, C.direccion, C.id, C.codigo, A.id_recorrido ";
    $sql.= "FROM recorridos_clientes A INNER JOIN clientes C ON (A.id_cliente = C.id AND A.id_empresa = C.id_empresa) ";
    $sql.= "WHERE A.id_empresa = $id_empresa ";
    $sql.= "AND A.id_recorrido = $id ";
    $sql.= "ORDER BY A.orden ASC ";
    $qq = $this->db->query($sql);
    $recorrido->clientes = $qq->result();

    return $recorrido;
  }
    
	function find($filter) {
    exit();
	}    

}