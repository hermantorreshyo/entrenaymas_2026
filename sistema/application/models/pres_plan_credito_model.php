<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Pres_Plan_Credito_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("pres_planes_credito","id","nombre ASC");
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
    
    $documentacion = $data->documentacion;
    $cuotas = $data->cuotas;
    unset($data->documentacion);
    unset($data->cuotas);
    $id = parent::save($data);

    // Actualizamos los puntos de venta relacionados
    $this->db->query("DELETE FROM pres_planes_credito_documentacion WHERE id_plan = $id AND id_empresa = $data->id_empresa ");
    foreach($documentacion as $pv) {
      $sql = "INSERT INTO pres_planes_credito_documentacion (id_empresa,id_plan,id_documentacion) VALUES ($data->id_empresa,$id,$pv)";
      $this->db->query($sql);
    }

    // Guardamos los cuotas
    $this->db->query("DELETE FROM pres_planes_credito_cuotas_intereses WHERE id_plan_credito = $id AND id_empresa = $data->id_empresa ");
    foreach($cuotas as $item) {
      $sql = "INSERT INTO pres_planes_credito_cuotas_intereses (id_empresa,id_plan_credito,cuota,tasa_interes";
      $sql.= ") VALUES ($data->id_empresa,$id,'$item->cuota','$item->tasa_interes') ";
      $this->db->query($sql);
    }

    return $id;
  }

  function get($id) {
    $row = parent::get($id);
    if ($row !== FALSE) {
      $sql = "SELECT id_documentacion AS id FROM pres_planes_credito_documentacion WHERE id_plan = $row->id AND id_empresa = $row->id_empresa ";
      $query = $this->db->query($sql);
      $row->documentacion = $query->result();

      $sql = "SELECT * FROM pres_planes_credito_cuotas_intereses WHERE id_plan_credito = $row->id AND id_empresa = $row->id_empresa ORDER BY cuota ASC";
      $query = $this->db->query($sql);
      $row->cuotas = $query->result();
    }
    return $row;
  }

  function delete($id) {
    $id_empresa = parent::get_empresa();
    $this->db->query("DELETE FROM pres_planes_credito_cuotas_intereses WHERE id_empresa = $id_empresa AND id_plan_credito = $id");
    $this->db->query("DELETE FROM pres_planes_credito_documentacion WHERE id_empresa = $id_empresa AND id_plan = $id");
    $this->db->query("DELETE FROM pres_planes_credito WHERE id_empresa = $id_empresa AND id = $id");
    echo json_encode(array());
  }

}