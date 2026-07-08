<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Sindi_Consultas extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Sindi_Consulta_Model', 'modelo');
  }

  function imprimir($id) {
    $id_empresa = parent::get_empresa();
    $datos = $this->modelo->get($id);
    $this->load->helper("fecha_helper");
    $datos->fecha_completa = fecha_es($datos->fecha);
    $fecha = explode(" ",$datos->fecha);
    $datos->fecha = fecha_es($fecha[0]);
    $time = strtotime($fecha[0]);
    $datos->vencimiento = fecha_es(date("Y-m-d", strtotime("+1 month", $time)));
    $this->load->model("Sindi_Afiliado_Model");
    $titular = $this->Sindi_Afiliado_Model->get_titular($datos->codigoafiliado);

    $this->load->view("reports/sindi/bonoconsulta", array(
      "consulta"=>$datos,
      "titular"=>$titular,
    ));
    $this->db->query("UPDATE sindi_consultas SET impresa = 1 WHERE id = $id AND id_empresa = $id_empresa ");

    $this->load->model("Sindi_Historial_Model");
    $this->Sindi_Historial_Model->registrar(array(
      "id_afiliado"=>$titular->id,
      "id_titular"=>$titular->id_titular,
      "evento"=>"Bono Consulta",
      "motivo"=>"Impresion Bono Consulta Nro $datos->numero",
    ));
  }

  function buscar() {
    $id_empresa = parent::get_empresa();
    $codigo = parent::get_get("codigo","");
    $id_afiliado = parent::get_get("id_afiliado",0);
    $offset = parent::get_get("offset",10);
    $limit = parent::get_get("limit",0);
    $s = $this->modelo->buscar(array(
      "id_empresa"=>$id_empresa,
      "codigo"=>$codigo,
      "id_afiliado"=>$id_afiliado,
      "offset"=>$offset,
      "limit"=>$limit,
    ));
    echo json_encode($s);
  }
  
	
}