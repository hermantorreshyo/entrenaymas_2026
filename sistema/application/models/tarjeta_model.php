<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Tarjeta_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("tarjetas","id",'numero_comercio ASC, nombre ASC');
	}

	function find($filter) {
    $this->db->where("id_empresa",parent::get_empresa());
		$this->db->like("nombre",$filter);
		$query = $this->db->get($this->tabla);
		$result = $query->result();
		$this->db->close();
		return $result;
	}

  function save($data) {
    $this->load->helper("fecha_helper");
    $this->load->helper("file_helper");
    $id_empresa = $data->id_empresa;
    $cuotas = $data->cuotas;
    unset($data->undefined);
    unset($data->cuotas);
    $data->last_update = time();
    $id = parent::save($data);

    // Guardamos los cuotas
    $this->db->query("DELETE FROM tarjetas_intereses WHERE id_tarjeta = $id AND id_empresa = $data->id_empresa ");
    foreach($cuotas as $item) {
      $item->fecha_desde = (isset($item->fecha_desde) && !empty($item->fecha_desde)) ? fecha_mysql($item->fecha_desde) : '';
      $item->fecha_hasta = (isset($item->fecha_hasta) && !empty($item->fecha_hasta)) ? fecha_mysql($item->fecha_hasta) : '';
      $sql = "INSERT INTO tarjetas_intereses (id_empresa,id_tarjeta,cuota_desde,cuota_hasta,interes,interes_especial,fecha_desde,fecha_hasta,last_update";
      $sql.= ") VALUES ($data->id_empresa,$id,'$item->cuota_desde','$item->cuota_hasta','$item->interes','$item->interes_especial','$item->fecha_desde','$item->fecha_hasta','$data->last_update') ";
      $this->db->query($sql);
    }
    return $id;
  }

  function get($id,$config=array()) {

    $row = parent::get($id);
    if ($row === FALSE) return $row;

    // Obtenemos los cuotas
    $sql = "SELECT AI.*, ";
    $sql.= " IF(AI.fecha_desde = '0000-00-00','',DATE_FORMAT(AI.fecha_desde,'%d/%m/%Y')) AS fecha_desde, ";
    $sql.= " IF(AI.fecha_hasta = '0000-00-00','',DATE_FORMAT(AI.fecha_hasta,'%d/%m/%Y')) AS fecha_hasta ";
    $sql.= "FROM tarjetas_intereses AI ";
    $sql.= "WHERE AI.id_tarjeta = $id AND AI.id_empresa = $row->id_empresa ORDER BY AI.cuota_desde ASC";
    $q = $this->db->query($sql);
    foreach($q->result() as $r) {
      $row->cuotas[] = $r;
    }
    return $row;
  }


  function delete($id) {
    $id_empresa = parent::get_empresa();
    $this->db->query("DELETE FROM tarjetas_intereses WHERE id_empresa = $id_empresa AND id_tarjeta = $id");
    $this->db->query("DELETE FROM tarjetas WHERE id_empresa = $id_empresa AND id = $id");
  }


}