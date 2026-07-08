<?php defined('BASEPATH') OR exit('No direct script access allowed');
require_once("abstract_model.php");

class Sindi_Limite_Condicion_Especial_Model extends Abstract_Model {

  function __construct() {
    parent::__construct("sindi_limites_condiciones_especiales","id","id_tipo_practica ASC");
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

  function buscar($config = array()) {

    $id_empresa = (isset($config["id_empresa"])) ? $config["id_empresa"] : parent::get_empresa();
    $filter = (isset($config["filter"])) ? $config["filter"] : "";
    $limit = (isset($config["limit"])) ? $config["limit"] : 0;
    $offset = (isset($config["offset"])) ? $config["offset"] : 10;

    $sql = "SELECT SQL_CALC_FOUND_ROWS SLCE.*, IF(SCE.nombre IS NULL,'', SCE.nombre) AS condicionespecial, IF(STP.nombre IS NULL,'', STP.nombre) AS tipodepractica ";
    $sql.= "FROM sindi_limites_condiciones_especiales SLCE ";
    $sql.= "LEFT JOIN sindi_condiciones_especiales SCE ON (SLCE.id_empresa = SCE.id_empresa AND SLCE.id_condicion_especial = SCE.id) ";
    $sql.= "LEFT JOIN sindi_tipos_practicas STP ON (SLCE.id_empresa = STP.id_empresa AND SLCE.id_tipo_practica = STP.id) ";
    $sql.= "WHERE SLCE.id_empresa = '$id_empresa' ";
    if (!empty($filter)) $sql.= "AND STP.nombre LIKE '%$filter%' OR SCE.nombre LIKE '%$filter%' ";
    $sql.= "ORDER BY SCE.nombre ASC ";
    $sql.= "LIMIT $limit, $offset ";

    $q = $this->db->query($sql);
    $salida = array();

    foreach($q->result() as $r) {
      $salida[] = $r;
    }

    $q_total = $this->db->query("SELECT FOUND_ROWS() AS total");
    $total = $q_total->row();
    return array(
      "results"=>$salida,
      "total"=>$total->total,
    );
  }

  function save($data) {

    $this->load->model("Sindi_Tipo_Practica_Model");   
    $practica = $this->Sindi_Tipo_Practica_Model->get("$data->id_tipo_practica");

    $this->load->model("Sindi_Condicion_Especial_Model");    
    $condicion = $this->Sindi_Condicion_Especial_Model->get("$elemento->id_condicion_especial");  

    // si data->id es 0 es nuevo, sino modifica!!!
    if ($data->id == 0) {
        $this->load->model("Sindi_Historial_Model");    
        $this->Sindi_Historial_Model->registrar(array(
          "evento"=>"Agregado de Limite de Practica",
          "motivo"=>"Practica: ".$practica->nombre." Condicion Especial: ".$condicion->nombre." Cantidad: ".$data->cantidad." Meses: ".$data->meses,
        ));
    } else {
        $anterior = $this->get("$data->id");
        $practica2 = $this->Sindi_Tipo_Practica_Model->get("$data->id_tipo_practica");
        $condicion2 = $this->Sindi_Condicion_Especial_Model->get("$data->id_condicion_especial");
        $this->load->model("Sindi_Historial_Model");    
        $this->Sindi_Historial_Model->registrar(array(
          "evento"=>"Modificación de Limite de Practica",
          "motivo"=>"Antes (Practica: ".$practica2->nombre." Condicion Especial: ".$condicion2->nombre." Cantidad: ".$anterior->cantidad." Meses: ".$anterior->meses. "- Después (Practica: ".$practica->nombre." Condicion Especial: ".$condicion->nombre." Cantidad: ".$data->cantidad." Meses: ".$data->meses.")",
        ));
    }

    return parent::save($data);
  }
}