<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Sindi_Limites_Tipos_Practicas extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Sindi_Limite_Tipo_Practica_Model', 'modelo');
  }

  function delete($id = null) {
    $elemento = $this->modelo->get("$id");

    $this->load->model("Sindi_Tipo_Practica_Model");   
    $practica = $this->Sindi_Tipo_Practica_Model->get("$elemento->id_tipo_practica");

    $this->load->model("Sindi_Historial_Model");    
    $this->Sindi_Historial_Model->registrar(array(
      "evento"=>"Eliminacion de Limite de Practica",
      "motivo"=>"Practica: ".$practica->nombre." Cantidad: ".$elemento->cantidad." Meses: ".$elemento->meses,
    ));
    $this->modelo->delete($id);
    echo json_encode(array());
  }

  function buscar() {
    $id_empresa = parent::get_empresa();
    $filter = parent::get_get("filter","");
    $offset = parent::get_get("offset",10);
    $limit = parent::get_get("limit",0);
    $s = $this->modelo->buscar(array(
      "id_empresa"=>$id_empresa,
      "filter"=>$filter,
      "offset"=>$offset,
      "limit"=>$limit,
    ));
    echo json_encode($s);
  }

  function insert() {
    $array = $this->parse_put();

    // Control para saber si ya existe el codigo y el identificador
    $sql = "SELECT * FROM sindi_limites_tipos_practicas WHERE id_tipo_practica = $array->id_tipo_practica ";
    $sql.= "AND meses = $array->meses AND cantidad = $array->cantidad";
    $quest = $this->db->query($sql);

    $this->load->model("Sindi_Tipo_Practica_Model");   
    $practica = $this->Sindi_Tipo_Practica_Model->get("$array->id_tipo_practica");

    if ($array->meses == 1) {
      $mes = "mes";
    } else {
      $mes = "meses";
    }

    if ($quest->num_rows()>0) {
      parent::send_error("Ya existe el limite de ".$practica->nombre." con ".$array->cantidad. " practica cada ".$array->meses. " ".$mes);
    }    

    $insert_id = $this->modelo->save($array);
    $salida = array("id"=>$insert_id);
    echo json_encode($salida);
  } 
}