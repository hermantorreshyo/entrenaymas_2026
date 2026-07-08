<?php defined('BASEPATH') OR exit('No direct script access allowed');
require_once("abstract_model.php");

class Sindi_Afiliado_Model extends Abstract_Model {

	function __construct() {
		parent::__construct("sindi_afiliados","id","nombre ASC");
    $this->load->model("Sindi_Historial_Model");
	}

  function limpiar_limites($config = array()) {

    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $hoy = date("Y-m-d");

    $sql = "SELECT * FROM sindi_configuracion WHERE id_empresa = $id_empresa";
    $q = $this->db->query($sql);
    $configuracion = $q->row();
    if ($configuracion->limpieza_limite >= $hoy) return;

    $this->load->model("Sindi_Historial_Model");
    $sql = "SELECT * FROM sindi_limites_afiliados ";
    $sql.= "WHERE vencimiento < '$hoy' AND vencimiento != '0000-00-00' ";
    $sql.= "AND id_empresa = '$id_empresa' ";
    $q = $this->db->query($sql);
    foreach($q->result() as $row) {
      if ($row->tipo == 1) {
        // Es una consulta
        $this->Sindi_Historial_Model->registrar(array(
          "id_afiliado"=>$row->id_afiliado,
          "evento"=>"Vencimiento Limite Consulta",
          "motivo"=>"Valor anterior: $row->cantidad - Fecha: $row->vencimiento",
        ));
        $sql = "UPDATE sindi_limites_afiliados ";
        $sql.= "SET cantidad = 2, meses = 1, motivo = '', vencimiento = '0000-00-00'  ";
        $sql.= "WHERE id = $row->id ";
        $sql.= "AND id_empresa = $row->id_empresa ";
        $this->db->query($sql);

      } else if ($row->tipo == 2) {
        // Recetario normal
        $this->Sindi_Historial_Model->registrar(array(
          "id_afiliado"=>$row->id_afiliado,
          "evento"=>"Vencimiento Limite Recetario",
          "motivo"=>"Valor anterior: $row->cantidad - Fecha: $row->vencimiento",
        ));
        $sql = "UPDATE sindi_limites_afiliados ";
        $sql.= "SET cantidad = 2, meses = 1, motivo = '', vencimiento = '0000-00-00'  ";
        $sql.= "WHERE id = $row->id ";
        $sql.= "AND id_empresa = $row->id_empresa ";
        $this->db->query($sql);

      } else if ($row->tipo == 3) {
        // Recetario 70%
        $this->Sindi_Historial_Model->registrar(array(
          "id_afiliado"=>$row->id_afiliado,
          "evento"=>"Vencimiento Limite Recetario 70%",
          "motivo"=>"Valor anterior: $row->cantidad - Fecha: $row->vencimiento",
        ));
        $sql = "UPDATE sindi_limites_afiliados ";
        $sql.= "SET cantidad = 0, meses = 1, motivo = '', vencimiento = '0000-00-00'  ";
        $sql.= "WHERE id = $row->id ";
        $sql.= "AND id_empresa = $row->id_empresa ";
        $this->db->query($sql);

      } else if ($row->tipo == 4) {
        // Recetario 100%
        $this->Sindi_Historial_Model->registrar(array(
          "id_afiliado"=>$row->id_afiliado,
          "evento"=>"Vencimiento Limite Recetario 100%",
          "motivo"=>"Valor anterior: $row->cantidad - Fecha: $row->vencimiento",
        ));
        $sql = "UPDATE sindi_limites_afiliados ";
        $sql.= "SET cantidad = 0, meses = 1, motivo = '', vencimiento = '0000-00-00'  ";
        $sql.= "WHERE id = $row->id ";
        $sql.= "AND id_empresa = $row->id_empresa ";
        $this->db->query($sql);

      }
    }
    // Actualizamos la fecha de ultima ejecucion
    $this->db->query("UPDATE sindi_configuracion SET limpieza_limite = '$hoy' WHERE id_empresa = $id_empresa ");
  }


  // Devuelve el historial de  empresas del afiliado
  function get_empresas($config = array()) {

    $limit = isset($config["limit"]) ? $config["limit"] : 0;
    $offset = isset($config["offset"]) ? $config["offset"] : 10;
    $id_afiliado = isset($config["id_afiliado"]) ? $config["id_afiliado"] : 0;
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();

    $afil = $this->get("$id_afiliado");

    $sql = "SELECT SQL_CALC_FOUND_ROWS AE.*, E.nombre ";
    $sql.= "FROM sindi_afiliados_empresas AE ";
    $sql.= "INNER JOIN sindi_empresas E ON (AE.id_empresa = E.id_empresa AND AE.id_sindi_empresa = E.id) ";
    $sql.= "WHERE AE.id_afiliado = $id_afiliado ";
    $sql.= "AND AE.id_empresa = $afil->id_empresa_transporte ";
    $sql.= "ORDER BY AE.id DESC ";
    if (!empty($offset)) $sql.= "LIMIT $limit, $offset ";
    $q = $this->db->query($sql);

    $q_total = $this->db->query("SELECT FOUND_ROWS() AS total");
    $total = $q_total->row();

    return array(
      "results"=>$q->result(),
      "total"=>$total->total,
    );
  }

  function get_sindicatos($config = array()) {

    $limit = isset($config["limit"]) ? $config["limit"] : 0;
    $offset = isset($config["offset"]) ? $config["offset"] : 10;
    $id_afiliado = isset($config["id_afiliado"]) ? $config["id_afiliado"] : 0;
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $sql = "SELECT SQL_CALC_FOUND_ROWS SS.*, E.nombre AS nombreempresa, SA.nombre AS nombreafiliado ";
    $sql.= "FROM sindi_sindicato SS ";
    $sql.= "INNER JOIN sindi_empresas E ON (SS.id_empresa = E.id_empresa AND SS.id_empresa_transporte = E.id) ";
    $sql.= "INNER JOIN sindi_afiliados SA ON (SS.id_empresa = SA.id_empresa AND SS.id_afiliado = SA.id) ";
    $sql.= "WHERE SS.id_afiliado = $id_afiliado ";
    $sql.= "AND SS.id_empresa = $id_empresa ";
    $sql.= "ORDER BY SS.id DESC ";
    if (!empty($offset)) $sql.= "LIMIT $limit, $offset ";
    $q = $this->db->query($sql);

    $q_total = $this->db->query("SELECT FOUND_ROWS() AS total");
    $total = $q_total->row();

    return array(
      "results"=>$q->result(),
      "total"=>$total->total,
    );
  }

  function get_os($config = array()) {

    $limit = isset($config["limit"]) ? $config["limit"] : 0;
    $offset = isset($config["offset"]) ? $config["offset"] : 10;
    $id_afiliado = isset($config["id_afiliado"]) ? $config["id_afiliado"] : 0;
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $sql = "SELECT SQL_CALC_FOUND_ROWS SOS.*, E.nombre AS nombreempresa, SA.nombre AS nombreafiliado ";
    $sql.= "FROM sindi_obra_social SOS ";
    $sql.= "INNER JOIN sindi_empresas E ON (SOS.id_empresa = E.id_empresa AND SOS.id_empresa_transporte = E.id) ";
    $sql.= "INNER JOIN sindi_afiliados SA ON (SOS.id_empresa = SA.id_empresa AND SOS.id_afiliado = SA.id) ";
    $sql.= "WHERE SOS.id_afiliado = $id_afiliado ";
    $sql.= "AND SOS.id_empresa = $id_empresa ";
    $sql.= "ORDER BY SOS.id DESC ";
    if (!empty($offset)) $sql.= "LIMIT $limit, $offset ";
    $q = $this->db->query($sql);

    $q_total = $this->db->query("SELECT FOUND_ROWS() AS total");
    $total = $q_total->row();

    return array(
      "results"=>$q->result(),
      "total"=>$total->total,
    );
  }

  function buscar_consumos($config = array()) {

    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $id_afiliado = isset($config["id_afiliado"]) ? $config["id_afiliado"] : 0;
    $id_paciente = isset($config["id_paciente"]) ? $config["id_paciente"] : 0;
    $desde = isset($config["desde"]) ? $config["desde"] : 0;
    $hasta = isset($config["hasta"]) ? $config["hasta"] : 0;
    $offset = isset($config["offset"]) ? $config["offset"] : 0;
    $limit = isset($config["limit"]) ? $config["limit"] : 10;
    $tipo = isset($config["tipo"]) ? $config["tipo"] : '';

    $sql_consultas = "SC.id, DATE_FORMAT(SC.fecha,'%d/%m/%Y %H:%i') AS fecha, SC.numero, SC.id_afiliado, SC.id_paciente, 'C' as tipo, IF(SC.hospital = 1,'Hospital: Si','Hospital: No') AS observaciones, SC.importe AS importe ";
    $sql_consultas.= "FROM sindi_consultas SC ";
    $sql_consultas.= "WHERE SC.id_empresa = $id_empresa ";
    if (!empty($id_afiliado)) $sql_consultas.= "AND SC.id_afiliado = $id_afiliado ";
    if (!empty($id_paciente)) $sql_consultas.= "AND SC.id_paciente = $id_paciente ";
    if (!empty($desde)) $sql_consultas.= "AND SC.fecha >= '$desde' ";
    if (!empty($hasta)) $sql_consultas.= "AND SC.fecha <= '$hasta' ";

    $sql_practicas = "SP.id, DATE_FORMAT(SP.fecha,'%d/%m/%Y %H:%i') AS fecha, SP.numero, SP.id_afiliado, SP.id_paciente, 'P' as tipo, STP.nombre AS observaciones, SP.importe AS importe ";
    $sql_practicas.= "FROM sindi_practicas SP ";
    $sql_practicas.= "INNER JOIN sindi_tipos_practicas STP ON (SP.id_empresa = STP.id_empresa AND STP.id = SP.id_tipo_practica) ";
    $sql_practicas.= "WHERE SP.id_empresa = $id_empresa ";
    if (!empty($id_afiliado)) $sql_practicas.= "AND SP.id_afiliado = $id_afiliado ";
    if (!empty($id_paciente)) $sql_practicas.= "AND SP.id_paciente = $id_paciente ";
    if (!empty($desde)) $sql_practicas.= "AND SP.fecha >= '$desde' ";
    if (!empty($hasta)) $sql_practicas.= "AND SP.fecha <= '$hasta' ";

    $sql_reintegros = "SR.id, DATE_FORMAT(SR.fecha,'%d/%m/%Y %H:%i') AS fecha, SR.numero, SR.id_afiliado, SR.id_paciente, 'R' as tipo, STR.nombre AS observaciones, SR.importe_reintegro AS importe ";
    $sql_reintegros.= "FROM sindi_reintegros SR ";
    $sql_reintegros.= "INNER JOIN sindi_tipos_reintegros STR ON (SR.id_empresa = STR.id_empresa AND STR.id = SR.id_tipo_reintegro) ";
    $sql_reintegros.= "WHERE SR.id_empresa = $id_empresa ";
    if (!empty($id_afiliado)) $sql_reintegros.= "AND SR.id_afiliado = $id_afiliado ";
    if (!empty($id_paciente)) $sql_reintegros.= "AND SR.id_paciente = $id_paciente ";
    if (!empty($desde)) $sql_reintegros.= "AND SR.fecha >= '$desde' ";
    if (!empty($hasta)) $sql_reintegros.= "AND SR.fecha <= '$hasta' ";

    $sql_recetarios = "ST.id, DATE_FORMAT(ST.fecha,'%d/%m/%Y %H:%i') AS fecha, ST.numero, ST.id_afiliado, ST.id_paciente, 'T' as tipo, CONCAT(ST.cantidad,' recetario/s de ',ST.porcentaje,'%') AS observaciones, 0 AS importe ";
    $sql_recetarios.= "FROM sindi_recetarios ST ";
    $sql_recetarios.= "WHERE ST.id_empresa = $id_empresa ";
    if (!empty($id_afiliado)) $sql_recetarios.= "AND ST.id_afiliado = $id_afiliado ";
    if (!empty($id_paciente)) $sql_recetarios.= "AND ST.id_paciente = $id_paciente ";
    if (!empty($desde)) $sql_recetarios.= "AND ST.fecha >= '$desde' ";
    if (!empty($hasta)) $sql_recetarios.= "AND ST.fecha <= '$hasta' ";

    $sql = "SELECT SQL_CALC_FOUND_ROWS ";
    if (empty($tipo)) {
      $sql.= $sql_consultas;
      $sql.= "UNION ";
      $sql.= "SELECT ".$sql_practicas;
      $sql.= "UNION ";
      $sql.= "SELECT ".$sql_reintegros;
      $sql.= "UNION ";
      $sql.= "SELECT ".$sql_recetarios;
    } else if ($tipo == "C") {
      $sql.= $sql_consultas;
    } else if ($tipo == "P") {
      $sql.= $sql_practicas;
    } else if ($tipo == "R") {
      $sql.= $sql_reintegros;
    } else if ($tipo == "T") {
      $sql.= $sql_recetarios;
    }
    $sql.= "ORDER BY fecha DESC ";
    if (!empty($offset)) $sql.= "LIMIT $limit, $offset ";
    $q = $this->db->query($sql);

    $q_total = $this->db->query("SELECT FOUND_ROWS() AS total");
    $total = $q_total->row();

    $salida = array();
    foreach($q->result() as $row) {

      $sql = "SELECT * FROM sindi_afiliados WHERE id_empresa = $id_empresa ";
      $sql.= "AND id = $row->id_paciente ";
      $qq = $this->db->query($sql);
      $rr = $qq->row();
      $row->nombre = $rr->nombre;

      $salida[] = $row;
    }
    return array(
      "results"=>$salida,
      "total"=>$total->total,
    );
  }

  function buscar_por_codigo($config = array()) {

    $id_empresa = (isset($config["id_empresa"])) ? $config["id_empresa"] : parent::get_empresa();
    $codigo = (isset($config["codigo"])) ? $config["codigo"] : "";
    $identificador = (isset($config["identificador"])) ? $config["identificador"] : "";

    $partes = explode("-", $codigo);
    if (sizeof($partes)>1) {
      $codigo = $partes[0];
      $identificador = $partes[1];
    }

    $sql = "SELECT * FROM sindi_afiliados ";
    $sql.= "WHERE id_empresa = $id_empresa ";
    $sql.= "AND codigo = '$codigo' ";
    if ($identificador != "") $sql.= "AND identificador = '$identificador' ";
    $q = $this->db->query($sql);
    if ($q->num_rows() == 0) {
      return array(
        "error"=>1,
        "mensaje"=>"El codigo no existe un afiliado con ese codigo.",
      );
    } else {
      $r = $q->row();
      $salida = $this->get($r->id);
      $salida->error = 0;
      $salida->mensaje = "";
      return $salida;
    }
  }

  function get_titular($codigo) {
     $id_empresa = parent::get_empresa();
     $sql = "SELECT * FROM sindi_afiliados WHERE codigo = '$codigo' AND identificador = '0' AND id_empresa = '$id_empresa'";
     $q = $this->db->query($sql);

     return $q->row();
  }

	function buscar($config = array()) {

		$id_empresa = (isset($config["id_empresa"])) ? $config["id_empresa"] : parent::get_empresa();
		$codigo = (isset($config["codigo"])) ? $config["codigo"] : "";
    $filter = (isset($config["filter"])) ? $config["filter"] : "";
    $order_by = (isset($config["order_by"])) ? $config["order_by"] : "";
    $order = (isset($config["order"])) ? $config["order"] : "ASC";
    $estado_obra_social = (isset($config["estado_obra_social"])) ? $config["estado_obra_social"] : "";
		$limit = (isset($config["limit"])) ? $config["limit"] : 0;
		$offset = (isset($config["offset"])) ? $config["offset"] : 10;

    $sql = "SELECT SQL_CALC_FOUND_ROWS SA.*, IF(L.nombre IS NULL,'', L.nombre) AS localidad, ";
    $sql.= " DATE_FORMAT(fecha_nacimiento,'%d/%m/%Y') AS fecha_nacimiento ";
    $sql.= "FROM sindi_afiliados SA ";
    $sql.= "LEFT JOIN sindi_localidades L ON (SA.id_empresa = L.id_empresa AND SA.id_localidad = L.id) ";
    $sql.= "WHERE SA.id_empresa = '$id_empresa' ";
    if (!empty($filter)) $sql.= "AND SA.nombre LIKE '%$filter%' ";
    if (!empty($codigo)) $sql.= "AND SA.codigo = '$codigo' ";
    if ($estado_obra_social == 1) $sql.= "AND SA.estado_obra_social = 1 ";
    if (!empty($order_by)) {
      $sql.= "ORDER BY $order_by $order ";
    } else {
      $sql.= "ORDER BY SA.nombre ";
    }
    $sql.= "LIMIT $limit, $offset ";
    $q = $this->db->query($sql);
    $salida = array();

    foreach($q->result() as $r) {
      $afiliado = $this->get($r->id);
      $salida[] = $afiliado;
    }

    $q_total = $this->db->query("SELECT FOUND_ROWS() AS total");
    $total = $q_total->row();

    return array(
      "results"=>$salida,
      "total"=>$total->total,
    );
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


  function get($id) {

    $id_empresa = parent::get_empresa();
    $mes_actual = date("m");
    $anio_actual = date("Y");

    $sql = "SELECT AF.*, IF(L.nombre IS NULL,'', L.nombre) AS localidad, IF(SE.nombre IS NULL,'', SE.nombre) AS nombreempresa ";
    $sql.= "FROM sindi_afiliados AF ";
    $sql.= "LEFT JOIN sindi_localidades L ON (AF.id_empresa = L.id_empresa AND AF.id_localidad = L.id) ";
    $sql.= "LEFT JOIN sindi_empresas SE ON (AF.id_empresa = SE.id_empresa AND AF.id_empresa_transporte = SE.id) ";
    $sql.= "WHERE AF.id_empresa = $id_empresa AND AF.id = $id";
    $q = $this->db->query($sql);
    $afiliado = $q->row();

    // Asignamos todo lo del historial
    $afiliado->historial = array();
    $sql = "SELECT SH.*, IF(SA.nombre IS NULL,'', SA.nombre) AS nombretitular, IF(SE.nombre IS NULL,'', SE.nombre) AS nombreempresa, ";
    $sql.= "IF(SA2.nombre IS NULL,'', SA2.nombre) AS nombreafiliado ";
    $sql.= "FROM sindi_historial SH ";
    $sql.= "LEFT JOIN sindi_afiliados SA ON (SA.id_empresa = SH.id_empresa AND SH.id_titular = SA.id) ";
    $sql.= "LEFT JOIN sindi_empresas SE ON (SE.id_empresa = SH.id_empresa AND SH.id_sindi_empresa = SE.id) ";
    $sql.= "LEFT JOIN sindi_afiliados SA2 ON (SA2.id_empresa = SH.id_empresa AND SH.id_afiliado = SA2.id) ";
    $sql.= "WHERE SH.id_empresa = $id_empresa AND SH.id_titular = $id ";
    $qhis = $this->db->query($sql);
    $resultadoshis = $qhis->result();
    foreach($resultadoshis as $r) {
      $afiliado->historial[] = $r;
    }

    // Si el afiliado esta dado de baja en la obra social, buscamos la fecha
    $afiliado->fecha_baja_obra_social = "";
    if ($afiliado->estado_obra_social == 0) {
      $sql = "SELECT * FROM sindi_historial WHERE id_empresa = $afiliado->id_empresa AND id_afiliado = $id AND evento = 'Baja en Obra Social' ORDER BY fecha DESC LIMIT 0,1 ";
      $q = $this->db->query($sql);
      if ($q->num_rows()>0) {
        $r = $q->row();  
        $afiliado->fecha_baja_obra_social = $r->fecha;
      }
    }

    // Crear la coleccion de condiciones especiales
    $afiliado->condiciones_especiales = array();
    $sql = "SELECT CEP.*, CE.nombre FROM sindi_condiciones_especiales_pacientes CEP ";
    $sql.= "INNER JOIN sindi_condiciones_especiales CE ON (CEP.id_empresa = CE.id_empresa AND CEP.id_condicion_especial = CE.id) ";
    $sql.= "WHERE CEP.id_afiliado = $id ";
    $sql.= "AND CEP.id_empresa = $afiliado->id_empresa ";
    $q = $this->db->query($sql);
    $resultados = $q->result();
    foreach($resultados as $r) {
      // Aca se pueden hacer cosas
      $afiliado->condiciones_especiales[] = $r;
    }

    $afiliado->limites = array();
    $sql = "SELECT LA.*, IF(TP.nombre IS NULL,'',TP.nombre) AS tipopractica ";
    $sql.= "FROM sindi_limites_afiliados LA ";
    $sql.= "LEFT JOIN sindi_tipos_practicas TP ON (LA.id_empresa = TP.id_empresa AND LA.id_tipo_practica = TP.id) ";
    $sql.= "WHERE LA.id_empresa = $afiliado->id_empresa ";
    $sql.= "AND LA.id_afiliado = $id ";
    $q = $this->db->query($sql);
    if ($q->num_rows() == 0) {
      $r = new stdClass();
      $r->consultas_realizadas = 0;
      $afiliado->limites[] = $r;
      $r->recetarios_realizados = 0;
      $afiliado->limites[] = $r;
      $r->recetarios_70_realizados = 0;
      $afiliado->limites[] = $r;
      $r->recetarios_100_realizados = 0;
      $afiliado->limites[] = $r;

    } else {
      foreach($q->result() as $r) {

        // Los limites de consultas y recetarios son mensuales, entonces buscamos cuantos sacamos el mes corriente
        if ($r->tipo == 1) {
          // Es consulta
          $sql = "SELECT COUNT(*) AS consultas_realizadas ";
          $sql.= "FROM sindi_consultas ";
          $sql.= "WHERE id_empresa = $r->id_empresa ";
          $sql.= "AND id_paciente = $id ";
          $sql.= "AND MONTH(fecha) = '$mes_actual' ";
          $sql.= "AND YEAR(fecha) = '$anio_actual' ";
          $sql.= "AND anulada = 0 ";
          $q_consulta = $this->db->query($sql);
          $r_consulta = $q_consulta->row();
          $r->consultas_realizadas = (is_null($r_consulta->consultas_realizadas) ? 0 : $r_consulta->consultas_realizadas);

        } else if ($r->tipo == 2) {
          // Es Recetario Normal
          $sql = "SELECT COUNT(*) AS recetarios_realizados ";
          $sql.= "FROM sindi_recetarios ";
          $sql.= "WHERE id_empresa = $r->id_empresa ";
          $sql.= "AND id_paciente = $id ";
          $sql.= "AND MONTH(fecha) = '$mes_actual' ";
          $sql.= "AND YEAR(fecha) = '$anio_actual' ";
          $sql.= "AND anulada = 0 ";
          $sql.= "AND porcentaje < 70 "; // 40 o 50
          $q_recetario = $this->db->query($sql);
          $r_recetario = $q_recetario->row();
          $r->recetarios_realizados = (is_null($r_recetario->recetarios_realizados) ? 0 : $r_recetario->recetarios_realizados);

        } else if ($r->tipo == 3) {
          // Es Recetario 70
          $sql = "SELECT COUNT(*) AS recetarios_70_realizados ";
          $sql.= "FROM sindi_recetarios ";
          $sql.= "WHERE id_empresa = $r->id_empresa ";
          $sql.= "AND id_paciente = $id ";
          $sql.= "AND MONTH(fecha) = '$mes_actual' ";
          $sql.= "AND YEAR(fecha) = '$anio_actual' ";
          $sql.= "AND anulada = 0 ";
          $sql.= "AND porcentaje = 70 ";
          $q_recetario = $this->db->query($sql);
          $r_recetario = $q_recetario->row();
          $r->recetarios_70_realizados = (is_null($r_recetario->recetarios_70_realizados) ? 0 : $r_recetario->recetarios_70_realizados);

        } else if ($r->tipo == 4) {
          // Es Recetario 100
          $sql = "SELECT COUNT(*) AS recetarios_100_realizados ";
          $sql.= "FROM sindi_recetarios ";
          $sql.= "WHERE id_empresa = $r->id_empresa ";
          $sql.= "AND id_paciente = $id ";
          $sql.= "AND MONTH(fecha) = '$mes_actual' ";
          $sql.= "AND YEAR(fecha) = '$anio_actual' ";
          $sql.= "AND anulada = 0 ";
          $sql.= "AND porcentaje = 100 ";
          $q_recetario = $this->db->query($sql);
          $r_recetario = $q_recetario->row();
          $r->recetarios_100_realizados = (is_null($r_recetario->recetarios_100_realizados) ? 0 : $r_recetario->recetarios_100_realizados);
        }

        $afiliado->limites[] = $r;
      }
    }
    return $afiliado;
  }


	function save($data) {
    $codigo = $data->codigo;
    $identificador = $data->identificador;
    $id_empresa = parent::get_empresa();
    $condiciones_especiales = $data->condiciones_especiales;
    $limites = $data->limites;
		$data->nombre = ucwords(strtolower($data->nombre));
		$data->domicilio = ucwords(strtolower($data->domicilio));
    $fecha = date('Y-m-d');
    // Updateamos si cambiaron el codigo del titular!
    $sql = "SELECT codigo FROM sindi_afiliados ";
    $sql.= "WHERE id_empresa = $data->id_empresa ";
    $sql.= "AND id = $data->id ";
    $sql.= "ORDER BY identificador ASC ";
    $sql.= "LIMIT 0,1 ";
    $q = $this->db->query($sql);
    if ($q->num_rows()>0) {
      $r = $q->row();
      if ($r->codigo != $data->codigo) {
        //Actualizamos los afiliados
        $this->db->query("UPDATE sindi_afiliados SET codigo = $data->codigo WHERE id_empresa = $data->id_empresa AND codigo = $r->codigo ");
        //Cargamos en el historial
        $this->Sindi_Historial_Model->registrar(array(
          "id_afiliado"=>$data->id,
          "id_titular"=>$data->id_titular,
          "evento"=>"Cambio de Codigo",
          "motivo"=>"Se cambiaron los afiliados con el codigo ".$r->codigo." a el codigo ".$data->codigo,
          "nivel"=>0
        ));
      }
    }

    $id = parent::save($data);

		// Obtenemos el primer afiliado (codigo 0)
		$sql = "SELECT id FROM sindi_afiliados ";
		$sql.= "WHERE id_empresa = $data->id_empresa ";
		$sql.= "AND codigo = '$data->codigo' ";
		$sql.= "ORDER BY identificador ASC ";
		$sql.= "LIMIT 0,1 ";
		$q = $this->db->query($sql);
    $id_titular = 0;
		if ($q->num_rows()>0) {
			$r = $q->row();
			$id_titular = $r->id;
			$sql = "UPDATE sindi_afiliados SET id_titular = $id_titular ";
			$sql.= "WHERE id_empresa = $data->id_empresa ";
			$sql.= "AND id = $id ";
			$this->db->query($sql);
    }

    // Historial del ingreso al sistema
    if ($id == 0) {
      if ($data->identificador == 0) {
        $text = "titular";
      } else {
        $text = "familiar";
      }
      $this->Sindi_Historial_Model->registrar(array(
        "id_afiliado"=>$id,
        "id_titular"=>$id_titular,
        "evento"=>"Ingreso al Sistema",
        "motivo"=>"Se carga el afiliado ".$text." con el codigo ".$data->codigo."-".$data->identificador,
        "nivel"=>0
      ));
    }

    // Escribimos las condiciones especiales
    $ids_condiciones = array();
    foreach($condiciones_especiales as $condicion) {
      $ids_condiciones[] = $condicion->id;
      $sql = "SELECT * FROM sindi_condiciones_especiales_pacientes ";
      $sql.= "WHERE id_empresa = $data->id_empresa ";
      $sql.= "AND id_afiliado = $id ";
      $sql.= "AND id_condicion_especial = $condicion->id ";
      $qq = $this->db->query($sql);
      if ($qq->num_rows()==0) {
        $sql = "INSERT INTO sindi_condiciones_especiales_pacientes (";
        $sql.= " id_empresa, id_afiliado, id_condicion_especial, resuelta, descripcion, vence, estado ";
        $sql.= ") VALUES (";
        $sql.= " $data->id_empresa, $id, $condicion->id, 0, '', '$condicion->vence', $condicion->estado ";
        $sql.= ")";
        $this->db->query($sql);
        // Insertamos en el historial
        if ($condicion->estado == 0) {
          $this->Sindi_Historial_Model->registrar(array(
            "id_afiliado"=>$id,
            "id_sindi_empresa"=>$data->id_empresa_transporte,
            "evento"=>"Solicitud de Condición Especial",
            "motivo"=>$condicion->nombre,
            "nivel"=>2,
          ));
        } else {
          $this->Sindi_Historial_Model->registrar(array(
            "id_afiliado"=>$id,
            "id_sindi_empresa"=>$data->id_empresa_transporte,
            "evento"=>"Asignación de Condición Especial",
            "motivo"=>$condicion->nombre,
            "nivel"=>2,
          ));
        }
      }
    }

    // Condiciones especiales que se eliminaron
    // Es decir, ya no estan mas en el array de condiciones_especiales
    // pero siguen estando guardados en la base de datos
    $ids_condiciones = implode(",", $ids_condiciones);
    $sql = "SELECT CEP.*, CE.nombre FROM sindi_condiciones_especiales_pacientes CEP ";
    $sql.= "INNER JOIN sindi_condiciones_especiales CE ON (CEP.id_empresa = CE.id_empresa AND CEP.id_condicion_especial = CE.id) ";
    $sql.= "WHERE CEP.id_empresa = $data->id_empresa ";
    $sql.= "AND CEP.id_afiliado = $id ";
    if (!empty($ids_condiciones)) $sql.= "AND CEP.id_condicion_especial NOT IN ($ids_condiciones) ";
    $qq = $this->db->query($sql);
    foreach($qq->result() as $rr) {
      // Actualizamos el historial
      $this->Sindi_Historial_Model->registrar(array(
        "id_afiliado"=>$id,
        "id_sindi_empresa"=>$data->id_empresa_transporte,
        "evento"=>"Remueve la Condición Especial",
        "motivo"=>$rr->nombre,
        "nivel"=>2,
      ));
      // Eliminamos el registro
      $sql = "DELETE FROM sindi_condiciones_especiales_pacientes ";
      $sql.= "WHERE id_empresa = $data->id_empresa ";
      $sql.= "AND id_afiliado = $id ";
      $sql.= "AND id_condicion_especial = $rr->id_condicion_especial ";
      $sql.= "AND id = $rr->id ";
      $this->db->query($sql);
    }

    $ids_limites = array();
    foreach($limites as $limite) {
      if ($limite->id == 0) {
        $this->Sindi_Historial_Model->registrar(array(
          "id_afiliado"=>$id,
          "evento"=>"Asignación de Limite Practica",
          "motivo"=>"$limite->vencimiento Cantidad: $limite->cantidad Meses: $limite->meses ".(($limite->tipo==5)?"Tipo Practica: $limite->tipo_practica ":""),
          "nivel"=>2,
        ));
      } else {
        // Tomamos los valores de los registros y nos fijamos si cambiaron los limites
        $sql = "SELECT LA.*, IF(TP.nombre IS NULL,'',TP.nombre) AS tipo_practica FROM sindi_limites_afiliados LA ";
        $sql.= "LEFT JOIN sindi_tipos_practicas TP ON (LA.id_empresa = TP.id_empresa AND LA.id_tipo_practica = TP.id) ";
        $sql.= "WHERE LA.id_empresa = $data->id_empresa AND LA.id_afiliado = $id AND LA.id = $limite->id ";
        $qq = $this->db->query($sql);
        if ($qq->num_rows()>0) {
          // Controlamos si cambio
          $registro = $qq->row();
          if ($limite->meses != $registro->meses || $limite->cantidad != $registro->cantidad || $limite->id_tipo_practica != $registro->id_tipo_practica || ($limite->vencimiento != $registro->vencimiento && $limite->vencimiento != "")) {
            $motivo = "Anterior: $registro->vencimiento Cantidad: $registro->cantidad Meses: $registro->meses ".(($registro->tipo==5)?"Tipo Practica: $registro->tipo_practica ":"");
            $motivo.= "Nuevo: $limite->vencimiento Cantidad: $limite->cantidad Meses: $limite->meses ".(($limite->tipo==5)?"Tipo Practica: $limite->tipo_practica ":"");
            $motivo.= "Motivo: ".$limite->motivo;
            if ($limite->tipo == 1) {
              $tipovar = "Consulta";
            } else if ($limite->tipo == 2) {
              $tipovar = "Recetario";
            } else if ($limite->tipo == 3) {
              $tipovar = "Recetario 70%";
            } else if ($limite->tipo == 4) {
              $tipovar = "Recetario 100%";
            } else {
              $tipovar = "Error de tipo: ".$limite->tipo;
            };
            $this->Sindi_Historial_Model->registrar(array(
              "id_afiliado"=>$id,
              "evento"=>"Cambio de Limite ".$tipovar,
              "motivo"=>$motivo,
              "nivel"=>2,
            ));
          }
          $ids_limites[] = $limite->id;
        }
      }
    }

    // Los limites que se borraron de la base de datos
    $ids_limites = implode(",", $ids_limites);
    $sql = "SELECT LA.*, IF(TP.nombre IS NULL,'',TP.nombre) AS tipo_practica FROM sindi_limites_afiliados LA ";
    $sql.= "LEFT JOIN sindi_tipos_practicas TP ON (LA.id_empresa = TP.id_empresa AND LA.id_tipo_practica = TP.id) ";
    $sql.= "WHERE LA.id_empresa = $data->id_empresa AND LA.id_afiliado = $id ";
    if (!empty($ids_limites)) $sql.= "AND LA.id NOT IN ($ids_limites) ";
    $qq = $this->db->query($sql);
    foreach($qq->result() as $rr) {
      $this->Sindi_Historial_Model->registrar(array(
        "id_afiliado"=>$id,
        "evento"=>"Eliminacion de Limite Practica",
        "motivo"=>"$rr->vencimiento Cantidad: $rr->cantidad Meses: $rr->meses ".(($rr->tipo==5)?"Tipo Practica: $rr->tipo_practica ":""),
        "nivel"=>2,
      ));
    }

    // Realizamos las modificaciones sobre la tabla de limites afiliados
    $sql = "DELETE FROM sindi_limites_afiliados WHERE id_empresa = $data->id_empresa AND id_afiliado = $id ";
    $this->db->query($sql);
    $ids_limites = array();
    foreach($limites as $limite) {
      $sql = "INSERT INTO sindi_limites_afiliados (";
      $sql.= " id_empresa, meses, cantidad, id_afiliado, id_tipo_practica, motivo, tipo, vencimiento ";
      $sql.= ") VALUES (";
      $sql.= " '$data->id_empresa', '$limite->meses', '$limite->cantidad', '$id', '$limite->id_tipo_practica', '$limite->motivo', '$limite->tipo', '$limite->vencimiento' ";
      $sql.= ")";
      $this->db->query($sql);
    }
		return $id;

	}

}