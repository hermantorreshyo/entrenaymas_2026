<?php defined('BASEPATH') OR exit('No direct script access allowed');
require_once("abstract_model.php");

class Sindi_Condicion_Especial_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("sindi_condiciones_especiales","id","id ASC");
    $this->load->model("Sindi_Historial_Model"); 		
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
		$data->nombre = ucwords(strtolower($data->nombre));
		$data->domicilio = ucwords(strtolower($data->domicilio));
		$data->titular1 = ucwords(strtolower($data->titular1));
		$data->titular2 = ucwords(strtolower($data->titular2));
		$data->titular3 = ucwords(strtolower($data->titular3));
    $fecha_alta = $data->fecha_alta;
    $es_nuevo = (!isset($data->id) || $data->id == 0);
    $id_empresa = parent::get_empresa();    

    if ($data->id == 0) {
      $sql = "SELECT * FROM sindi_empresas WHERE subzona = '$data->subzona' AND codigo = '$data->codigo' AND identificador = '$data->identificador' ";
      $quest = $this->db->query($sql);
      if ($quest->num_rows()>0) {
        $this->send_error("La subzona ".$data->subzona." con el codigo ".$data->codigo." y el identificador ".$data->identificador." ya se encuentra en uso.");
      }
    }
  	$id = parent::save($data);
    if ($es_nuevo) {
      $this->Sindi_Historial_Model->registrar(array(
        "id_sindi_empresa"=>$id,
        "evento"=>"Alta de la Empresa en Sistema",
        "motivo"=>"Carga Inicial",
      ));  
    }
    return $id;
  }
}