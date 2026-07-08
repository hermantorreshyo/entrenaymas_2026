<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Cheques extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Cheque_Model', 'modelo');
  } 

  function eliminar_deposito() {
    $id_empresa = parent::get_empresa();
    $id_cheque = parent::get_post("id_cheque",0);
    $this->load->model("Caja_Movimiento_Model");
    $this->Caja_Movimiento_Model->borrar(array(
      "id_empresa"=>$id_empresa,
      "id_cheque"=>$id_cheque,
    ));
    echo json_encode(array("error"=>0));
  }

  function depositar() {
    $id_empresa = parent::get_empresa();
    $id = parent::get_post("id",0);
    $id_caja_depositado = parent::get_post("id_caja_depositado",0);
    $tipo = $this->get_post("tipo","T");
    $this->load->helper("fecha_helper");
    $fecha_debitado = $this->get_post("fecha_debitado","");
    if (!empty($fecha_debitado)) $fecha_debitado = fecha_mysql($fecha_debitado);

    $cheque = $this->modelo->get($id,array(
      "tipo"=>$tipo,
    ));
    if (empty($cheque)) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"ERROR: No se encuentra el cheque",
      ));
      exit();
    }

    $this->load->model("Caja_Movimiento_Model");
    if ($tipo == "T") {
      $this->Caja_Movimiento_Model->ingreso(array(
        "fecha"=>$fecha_debitado." ".date("H:i:s"),
        "id_caja"=>$id_caja_depositado,
        "monto"=>$cheque->monto,
        "observaciones"=>"Deposito Cheque $cheque->numero",
        "id_cheque"=>$cheque->id,
        //"id_concepto"=>0, TODO: Deberia tener conceptos generales como COBRO DE CHEQUE
      ));
    } else if ($tipo == "P") {
      $this->Caja_Movimiento_Model->egreso(array(
        "fecha"=>$fecha_debitado." ".date("H:i:s"),
        "id_caja"=>$id_caja_depositado,
        "monto"=>$cheque->monto,
        "observaciones"=>"Debito Cheque $cheque->numero",
        "id_cheque"=>$cheque->id,
        //"id_concepto"=>0, TODO: Deberia tener conceptos generales como DEBITO DE CHEQUE
      ));      
    }

    // Actualizamos el cheque
    $sql = "UPDATE cheques SET id_caja_depositado = $id_caja_depositado, fecha_debitado = '$fecha_debitado' ";
    $sql.= "WHERE id = $id AND id_empresa = $id_empresa";
    $this->db->query($sql);

    echo json_encode(array(
      "error"=>0,
    ));
  }

  function exists() {
    $id_empresa = parent::get_empresa();
    $numero = $this->input->post("numero");
    $id_banco = $this->input->post("id_banco");
    $tipo = $this->input->post("tipo");
    /*
    $existe = $this->modelo->get(0,array(
      "id_empresa"=>$id_empresa,
      "numero"=>$numero,
      "id_banco"=>$id_banco,
      "tipo"=>$tipo,
    ));
    */
    echo json_encode(array(
      "existe"=>0,//(!empty($existe))?1:0,
      "mensaje"=>"",//(!empty($existe))?"ERROR: Ya existe un cheque cargado con el numero: $numero":"",
    ));
  }   

  function insert() {

    $array = $this->parse_put();

    // Debemos controlar si ya existe un cheque con el mismo numero
    $existe = $this->modelo->get(0,array(
      "id_empresa"=>$array->id_empresa,
      "numero"=>$array->numero,
      "id_banco"=>$array->id_banco,
      "tipo"=>$array->tipo,
    ));
    if (!empty($existe)) {
      /*
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"ERROR: Ya existe un cheque cargado con el numero: $array->numero",
      ));
      return;
      */
      echo json_encode(array(
        "id"=>$existe->id,
      ));
      return;
    }

    $insert_id = $this->modelo->save($array);

    // Si tiene una caja de origen
    if (isset($array->id_caja_origen) && $array->id_caja_origen != 0) {
      //$this->load->model("|");
    }


    $salida = array("id"=>$insert_id);
    echo json_encode($salida);
  }

  function buscar() {
    $conf = array();
    $this->load->helper("fecha_helper");
    $desde = $this->get_get("desde","");
    if (!empty($desde)) $conf["desde"] = fecha_mysql($desde);
    $hasta = $this->get_get("hasta","");
    if (!empty($hasta)) $conf["hasta"] = fecha_mysql($hasta);
    $conf["id_banco"] = $this->get_get("id_banco",0);
    $conf["mostrar_tipo"] = $this->get_get("mostrar_tipo",0);
    $conf["tipo"] = $this->get_get("tipo","P");
    $conf["titular"] = $this->get_get("titular","");
    $conf["fecha_comparacion"] = $this->get_get("fecha_comparacion","");
    $conf["filter"] = $this->get_get("filter","");
    $conf["limit"] = $this->get_get("limit",0);
    $conf["offset"] = $this->get_get("offset",20);
    $conf["id_sucursal"] = $this->get_get("id_sucursal",0);
    $conf["order_by"] = $this->get_get("order_by","C.fecha_emision");
    $conf["order"] = $this->get_get("order","DESC");
    $salida = $this->modelo->buscar($conf);
    $suma = $this->modelo->get_suma();
    $sql = $this->modelo->get_sql();
    $total = $this->modelo->get_total();
    echo json_encode(array(
      "results"=>$salida,
      "total"=>$total,
      "meta"=>array(
        "total"=>$suma,
        "sql"=>$sql,
      ),
    ));
  }

  function delete($id = NULL) {
    $id_empresa = parent::get_empresa();
    $sql = "DELETE FROM cheques WHERE id = $id AND id_empresa = $id_empresa ";
    $this->db->query($sql);
    echo json_encode(array());
  }
    
}