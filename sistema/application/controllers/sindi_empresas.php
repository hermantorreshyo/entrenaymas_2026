<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Sindi_Empresas extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Sindi_Empresa_Model', 'modelo');
    $this->load->model("Sindi_Historial_Model");
  }

  function buscar() {
    $id_empresa = parent::get_empresa();
    $codigo = parent::get_get("codigo","");
    $filter = parent::get_get("filter","");
    $offset = parent::get_get("offset",10);
    $limit = parent::get_get("limit",0);
    $s = $this->modelo->buscar(array(
      "id_empresa"=>$id_empresa,
      "codigo"=>$codigo,
      "filter"=>$filter,
      "offset"=>$offset,
      "limit"=>$limit,
    ));
    echo json_encode($s);
  }

  function alta_baja_empresa() {

    $id_empresa = parent::get_empresa();
    $id_sindi_empresa = parent::get_post("id_sindi_empresa",0);
    $empresa = $this->modelo->get("$id_sindi_empresa");
    $fecha = parent::get_post("fecha","");
    $motivo = parent::get_post("motivo","");
    $tipo = parent::get_post("tipo","");


    if ($tipo == "alta") {
      // actualizamos el registro de la empresa
      $sql = "UPDATE sindi_empresas SET ";
      $sql.= " estado = 1, fecha_alta_baja = '$fecha' ";
      $sql.= "WHERE id_empresa = $id_empresa AND id = $id_sindi_empresa ";
      $this->db->query($sql);
      // Actualizamos el registro en el historial
      $this->Sindi_Historial_Model->registrar(array(
        "id_sindi_empresa"=>$id_sindi_empresa,
        "evento"=>"Empresa dada de Alta",
        "motivo"=>"Nombre: ".$empresa->nombre." Codigo: ".$empresa->subzona."-".$empresa->codigo."-".$empresa->identificador,
      ));
    } else if ($tipo == "baja") {
      // controlamos que no tenga empleados activos!!
      $sql = "SELECT * FROM sindi_afiliados WHERE id_empresa = $id_empresa AND id_empresa_transporte = $id_sindi_empresa ";
      $query = $this->db->query($sql);
      if($query->num_rows() > 0) {
       echo json_encode(array(
          "error"=>1,
          "mensaje"=>"No se puede dar de baja a la empresa porque tiene empleados activos."
        ));
        return;
      } else {
        // Si no tiene empleados activos la damos de baja
        $sql = "UPDATE sindi_empresas SET ";
        $sql.= " estado = 0, fecha_alta_baja = '$fecha' ";
        $sql.= "WHERE id_empresa = $id_empresa AND id = $id_sindi_empresa ";
        $this->db->query($sql);
        // Actualizamos el registro en el historial
        $this->Sindi_Historial_Model->registrar(array(
          "id_sindi_empresa"=>$id_sindi_empresa,
          "evento"=>"Empresa dada de Baja",
          "motivo"=>"Nombre: ".$empresa->nombre." Codigo: ".$empresa->subzona."-".$empresa->codigo."-".$empresa->identificador,
        ));
      }
    }


    echo json_encode(array(
      "error"=>0,
    ));
  }

  function buscar_historial_empresa() {
    $this->load->model('Sindi_Historial_Model', 'modelohistorial');
    $id_empresa = parent::get_empresa();
    $id_sindi_empresa = $this->input->get("id_empresa_transporte");
    $limit = parent::get_get("limit",0);
    $offset = parent::get_get("offset",10);
    $movimientos = $this->modelohistorial->buscar_historial_empresa(array(
      "id_empresa_transporte"=>$id_sindi_empresa,
      "limit"=>$limit,
      "offset"=>$offset,
    ));
    echo json_encode($movimientos);
  }

}