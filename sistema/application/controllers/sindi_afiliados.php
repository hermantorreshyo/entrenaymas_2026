<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Sindi_Afiliados extends REST_Controller {

  function __construct() {
    error_reporting(0); // Evita que warnings/deprecations de PHP contaminen la respuesta
    parent::__construct();
    $this->load->model('Sindi_Afiliado_Model', 'modelo');
    $this->load->model("Sindi_Historial_Model");
  }

  function insert() {
    $array = $this->parse_put();

    // Control para saber si ya existe el codigo y el identificador
    $sql = "SELECT * FROM sindi_afiliados WHERE codigo = '$array->codigo' AND identificador = '$array->identificador' ";
    $quest = $this->db->query($sql);
    if ($quest->num_rows()>0) {
      parent::send_error("El codigo ".$array->codigo." con el identificador ".$array->identificador." ya se encuentra en uso.");
    }
    $insert_id = $this->modelo->save($array);
    $salida = array("id"=>$insert_id);
    echo json_encode($salida);
  }

  function buscar_empresas() {
    $id_empresa = parent::get_empresa();
    $id_afiliado = parent::get_get("id_afiliado",0);
    $limit = parent::get_get("limit",0);
    $offset = parent::get_get("offset",10);
    $empresas = $this->modelo->get_empresas(array(
      "id_afiliado"=>$id_afiliado,
      "limit"=>$limit,
      "offset"=>$offset,
    ));
    echo json_encode($empresas);
  }

  function alta_baja_empresa() {

    $id_empresa = parent::get_empresa();
    $id_sindi_empresa = parent::get_post("id_sindi_empresa",0);
    $id_afiliado = parent::get_post("id_afiliado",0);
    $afil = $this->modelo->get("$id_afiliado");
    $this->load->model("Sindi_Empresa_Model");
    $empr = $this->Sindi_Empresa_Model->get($id_sindi_empresa);
    $fecha = parent::get_post("fecha","");
    $tipo = parent::get_post("tipo","");
    $motivo = parent::get_post("motivo","");
    // Obtengo el titular
    $id_titular_query = $this->db->query("SELECT id_titular FROM sindi_afiliados WHERE id_empresa = $id_empresa AND id = $id_afiliado ");
    $tit = $id_titular_query->row();
    $id_titular = $tit->id_titular;

    if ($tipo == "alta") {
      // Actualizo el afiliado
      $sql = "UPDATE sindi_afiliados SET ";
      $sql.= " fecha_ingreso_empresa = '$fecha', ";
      $sql.= " id_empresa_transporte = $id_sindi_empresa ";
      $sql.= "WHERE id_empresa = $id_empresa AND id = $id_afiliado ";
      $this->db->query($sql);
      // Actualizo el historial
      $this->Sindi_Historial_Model->registrar(array(
        "id_afiliado"=>$id_afiliado,
        "id_titular"=>$id_titular,
        "id_sindi_empresa"=>$id_sindi_empresa,
        "evento"=>"Alta en la Empresa",
        "motivo"=>"Nombre: ".$afil->nombre." Codigo: ".$afil->codigo."-".$afil->identificador." Empresa: ".$empr->nombre." Motivo: ".$motivo,
      ));
    } else if ($tipo == "baja") {
      // Actualizo el afiliado
      $sql = "UPDATE sindi_afiliados SET ";
      $sql.= " fecha_ingreso_empresa = '0000-00-00', ";
      $sql.= " id_empresa_transporte = 1 ";
      $sql.= "WHERE id_empresa = $id_empresa AND id = $id_afiliado ";
      $this->db->query($sql);
      // Actualizo el historial
      $this->Sindi_Historial_Model->registrar(array(
        "id_afiliado"=>$id_afiliado,
        "id_titular"=>$id_titular,
        "id_sindi_empresa"=>$id_sindi_empresa,
        "evento"=>"Baja de la Empresa",
        "motivo"=>"Nombre: ".$afil->nombre." Codigo: ".$afil->codigo."-".$afil->identificador." Empresa: ".$empr->nombre."  Motivo: ".$motivo,
      ));
    }

    echo json_encode(array(
      "error"=>0,
    ));
  }

  function buscar_consumos() {
    $id_empresa = parent::get_empresa();
    $id_afiliado = parent::get_get("id_afiliado",0);
    $id_paciente = parent::get_get("id_paciente",0);
    $desde = parent::get_get("desde","");
    $hasta = parent::get_get("hasta","");
    $offset = parent::get_get("offset",10);
    $limit = parent::get_get("limit",0);
    $tipo = parent::get_get("tipo","");

    $salida = $this->modelo->buscar_consumos(array(
      "id_empresa"=>$id_empresa,
      "id_afiliado"=>$id_afiliado,
      "id_paciente"=>$id_paciente,
      "desde"=>$desde,
      "hasta"=>$hasta,
      "offset"=>$offset,
      "limit"=>$limit,
      "tipo"=>$tipo,
    ));
    echo json_encode($salida);
  }

  function imprimir_consumos() {

  }

  function buscar_por_codigo() {
    $id_empresa = parent::get_empresa();
    $codigo = parent::get_post("codigo","");
    $res = $this->modelo->buscar_por_codigo(array(
      "codigo"=>$codigo,
    ));
    echo json_encode($res);
  }

  function buscar() {
    $id_empresa = parent::get_empresa();
    $codigo = parent::get_get("codigo","");
    $filter = parent::get_get("filter","");
    $estado_obra_social = parent::get_get("estado_obra_social","");
    $offset = parent::get_get("offset",10);
    $limit = parent::get_get("limit",0);
    $order_by = parent::get_get("order_by","");
    $order = parent::get_get("order","");
    $s = $this->modelo->buscar(array(
      "id_empresa"=>$id_empresa,
      "codigo"=>$codigo,
      "filter"=>$filter,
      "estado_obra_social"=>$estado_obra_social,
      "offset"=>$offset,
      "limit"=>$limit,
      "order_by"=>$order_by,
      "order"=>$order,
    ));
    echo json_encode($s);
  }

  function buscar_sindicatos() {
    $id_empresa = parent::get_empresa();
    $id_afiliado = parent::get_get("id_afiliado",0);
    $limit = parent::get_get("limit",0);
    $offset = parent::get_get("offset",10);
    $empresas = $this->modelo->get_sindicatos(array(
      "id_afiliado"=>$id_afiliado,
      "limit"=>$limit,
      "offset"=>$offset,
    ));
    echo json_encode($empresas);
  }

    function buscar_historial() {
    $id_empresa = parent::get_empresa();
    $id_afiliado = parent::get_get("id_afiliado",0);
    $limit = parent::get_get("limit",0);
    $offset = parent::get_get("offset",10);
    $empresas = $this->modelo->get_historial(array(
      "id_afiliado"=>$id_afiliado,
      "limit"=>$limit,
      "offset"=>$offset,
    ));
    echo json_encode($empresas);
  }

  function alta_baja_sindicato() {
    $id_empresa = parent::get_empresa();
    $id_empresa_transporte = parent::get_post("id_empresa_transporte",0);
    $id_afiliado = parent::get_post("id_afiliado",0);
    $fecha = parent::get_post("fecha","");
    $motivo = parent::get_post("motivo","");
    $tipo = parent::get_post("tipo","");
    $fecha_alta = ($tipo == "alta") ? $fecha : "";
    $fecha_baja = ($tipo == "baja") ? $fecha : "";
    // Obtengo el titular
    $id_titular_query = $this->db->query("SELECT id_titular FROM sindi_afiliados WHERE id_empresa = $id_empresa AND id = $id_afiliado ");
    $tit = $id_titular_query->row();
    $id_titular = $tit->id_titular;
    // Obtengo el estado del titular
    $id_estados = $this->db->query("SELECT estado_sindicato FROM sindi_afiliados WHERE id_empresa = $id_empresa AND id = $id_titular ");
    $est = $id_estados->row();
    $estado_sindicato = $est->estado_sindicato;
    // Actualizamos el registro del afiliado y el historial
    if ($tipo == "alta") {
      // Controlamos que el titular este activo, sino no puede darse de alta
      if ($estado_sindicato == 1 || $id_titular == $id_afiliado) {
        // Actualizamos el registro en Afiliados
        $sql = "UPDATE sindi_afiliados SET ";
        $sql.= " estado_sindicato = '1' ";
        $sql.= "WHERE id_empresa = $id_empresa AND id = $id_afiliado ";
        $this->db->query($sql);
        // Añadimos al log del historial
        $this->Sindi_Historial_Model->registrar(array(
          "id_afiliado"=>$id_afiliado,
          "id_titular"=>$id_titular,
          "evento"=>"Alta en Sindicato",
          "motivo"=>$motivo,
        ));
      } else {
        echo json_encode(array(
          "error"=>1,
          "mensaje"=>"No se puede dar de alta porque el titular esta dado de baja."
        ));
        return;
      }
    } elseif ($tipo == "baja") {
      // Controlamos si es titular para saber si debemos dar de baja a todo el grupo familiar
      if ($id_titular == $id_afiliado) {
        // Buscamos el grupo familiar activo
        $sql = "SELECT * FROM sindi_afiliados WHERE id_titular = $id_titular AND id_empresa = $id_empresa AND estado_sindicato = 1 ORDER BY id ASC ";
        $grupo = $this->db->query($sql);
        // Damos de baja a todo el grupo familiar en la tabla afiliados
        $sql = "UPDATE sindi_afiliados SET ";
        $sql.= "estado_sindicato = '0' ";
        $sql.= "WHERE id_empresa = $id_empresa AND id_titular = $id_titular" ;
        $this->db->query($sql);
        // Añadimos al log del historial
        foreach ($grupo->result() as $afi) {
          // Añadimos al log del historial
          $this->Sindi_Historial_Model->registrar(array(
            "id_afiliado"=>$afi->id,
            "id_titular"=>$id_titular,
            "evento"=>"Baja en Sindicato",
          ));
        }
      } else {
        // Actualizamos solo el registro de ese afiliado en Afiliados
        $sql = "UPDATE sindi_afiliados SET ";
        $sql.= " estado_sindicato = '0' ";
        $sql.= "WHERE id_empresa = $id_empresa AND id = $id_afiliado ";
        $this->db->query($sql);
        // Añadimos al log del historial
        $this->Sindi_Historial_Model->registrar(array(
          "id_afiliado"=>$id_afiliado,
          "id_titular"=>$id_titular,
          "evento"=>"Baja en Sindicato",
        ));
      }
    }
    echo json_encode(array(
      "error"=>0,
    ));
  }

  function alta_baja_os() {
    $id_empresa = parent::get_empresa();
    $id_empresa_transporte = parent::get_post("id_empresa_transporte",0);
    $id_afiliado = parent::get_post("id_afiliado",0);
    $fecha = parent::get_post("fecha","");
    $motivo = parent::get_post("motivo","");
    $tipo = parent::get_post("tipo","");
    $fecha_alta = ($tipo == "alta") ? $fecha : "";
    $fecha_baja = ($tipo == "baja") ? $fecha : "";
    // Obtengo el titular
    $id_titular_query = $this->db->query("SELECT id_titular FROM sindi_afiliados WHERE id_empresa = $id_empresa AND id = $id_afiliado ");
    $tit = $id_titular_query->row();
    $id_titular = $tit->id_titular;
    // Obtengo el estado del titular
    $id_estados = $this->db->query("SELECT estado_obra_social FROM sindi_afiliados WHERE id_empresa = $id_empresa AND id = $id_titular ");
    $est = $id_estados->row();
    $estado_obra_social = $est->estado_obra_social;
    // Actualizamos el registro del afiliado y el historial
    if ($tipo == "alta") {
      // Controlamos que el titular este activo, sino no puede darse de alta
      if ($estado_obra_social == 1 || $id_titular == $id_afiliado) {
        // Actualizamos el registro en Afiliados
        $sql = "UPDATE sindi_afiliados SET ";
        $sql.= " estado_obra_social = '1' ";
        $sql.= "WHERE id_empresa = $id_empresa AND id = $id_afiliado ";
        $this->db->query($sql);
        // Añadimos al log del historial
        $this->Sindi_Historial_Model->registrar(array(
          "id_afiliado"=>$id_afiliado,
          "id_titular"=>$id_titular,
          "evento"=>"Alta en Obra Social",
        ));
      } else {
        echo json_encode(array(
          "error"=>1,
          "mensaje"=>"No se puede dar de alta porque el titular esta dado de baja."
        ));
        return;
      }
    } elseif ($tipo == "baja") {
      // Controlamos si es titular para saber si debemos dar de baja a todo el grupo familiar
      if ($id_titular == $id_afiliado) {
        // Buscamos el grupo familiar activo
        $sql = "SELECT * FROM sindi_afiliados WHERE id_titular = $id_titular AND id_empresa = $id_empresa AND estado_obra_social = 1 ORDER BY id ASC ";
        $grupo = $this->db->query($sql);
        // Damos de baja a todo el grupo familiar en la tabla afiliados
        $sql = "UPDATE sindi_afiliados SET ";
        $sql.= "estado_obra_social = '0' ";
        $sql.= "WHERE id_empresa = $id_empresa AND id_titular = $id_titular" ;
        $this->db->query($sql);
        // Insertamos en el historial
        foreach ($grupo->result() as $afi) {
          // Añadimos al log del historial
          $this->Sindi_Historial_Model->registrar(array(
            "id_afiliado"=>$afi->id,
            "id_titular"=>$id_titular,
            "evento"=>"Baja en Obra Social",
          ));
        }
      } else {
        // Actualizamos solo el registro de ese afiliado en Afiliados
        $sql = "UPDATE sindi_afiliados SET ";
        $sql.= " estado_obra_social = '0' ";
        $sql.= "WHERE id_empresa = $id_empresa AND id = $id_afiliado ";
        $this->db->query($sql);
        // Añadimos al log del historial
        $this->Sindi_Historial_Model->registrar(array(
          "id_afiliado"=>$id_afiliado,
          "id_titular"=>$id_titular,
          "evento"=>"Baja en Obra Social",
        ));
      }
    }
    echo json_encode(array(
      "error"=>0,
    ));
  }

  function buscar_historial_afiliado() {
    $this->load->model('Sindi_Historial_Model', 'modelohistorial');
    $id_empresa = parent::get_empresa();
    $id_afiliado = $this->input->get("id_afiliado");
    $limit = parent::get_get("limit",0);
    $offset = parent::get_get("offset",10);
    $movimientos = $this->modelohistorial->buscar_historial_afiliado(array(
      "id_afiliado"=>$id_afiliado,
      "limit"=>$limit,
      "offset"=>$offset,
    ));
    echo json_encode($movimientos);
  }

}