<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Turno_Medico_Model extends Abstract_Model {
  
  function __construct() {
    parent::__construct("med_turnos_medicos","id","fecha DESC",1);
  }

  function save($data) {
    $this->load->helper("fecha_helper");
    $data->fecha = fecha_mysql($data->fecha);

    // Ponemos los stamps desde y hasta
    $data->desde = $data->fecha." ".$data->hora;
    $dt = new DateTime($data->desde);
    $dt->add(new DateInterval("PT".$data->duracion_cantidad.$data->duracion_tipo));
    $data->hasta = $dt->format("Y-m-d H:i");

    // Controlamos si el turno esta libre
    $libre = $this->is_free(array(
      "fecha"=>$data->fecha,
      "hora"=>$data->hora,
      "not_id"=>((isset($data->id) && !empty($data->id)) ? $data->id : 0),
      "id_profesional"=>$data->id_profesional,
    ));
    if (!$libre) $this->send_error("El turno esta ocupado, por favor seleccione otro horario.");

    return parent::save($data);
  }

  function is_free($config=array()) {
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : parent::get_empresa();
    $not_id = isset($config["not_id"]) ? $config["not_id"] : 0;
    $id_profesional = isset($config["id_profesional"]) ? $config["id_profesional"] : 0;
    $id_paciente = isset($config["id_paciente"]) ? $config["id_paciente"] : 0;
    $fecha = isset($config["fecha"]) ? $config["fecha"] : "";
    $hora = isset($config["hora"]) ? $config["hora"] : "";
    $sql = "SELECT * FROM med_turnos_medicos ";
    $sql.= "WHERE id_empresa = $id_empresa ";
    if (!empty($not_id)) $sql.= "AND id != $not_id ";
    if (!empty($id_profesional)) $sql.= "AND id_profesional = $id_profesional ";
    if (!empty($id_paciente)) $sql.= "AND id_paciente = $id_paciente ";
    if (!empty($fecha) && !empty($hora)) {
      $fechahora = $fecha." ".$hora;
      $sql.= "AND desde <= '$fechahora' AND '$fechahora' < hasta ";
    }
    $q = $this->db->query($sql);
    return ($q->num_rows()>0)?FALSE:TRUE;
  }

  function insert($data) {
    $profesional = $data->profesional;
    $id = parent::insert($data);

    // Debemos crear la consulta
    $this->load->model("Consulta_Model");
    $consulta = new stdClass();
    $consulta->id_origen = 16; // TURNO MEDICO
    $consulta->tipo = 1;
    $consulta->id_empresa = $data->id_empresa;
    $consulta->id_contacto = $data->id_paciente;
    $consulta->fecha = $data->fecha;
    $consulta->id_usuario = $data->id_usuario;
    $consulta->hora = $data->hora;
    $consulta->texto = $data->observaciones;
    $consulta->id_referencia = $id;
    $consulta->asunto = "Turno con $profesional";
    $this->Consulta_Model->insert($consulta);

    return $id;
  }

  function delete($id) {
    $id_empresa = parent::get_empresa();
    $this->db->query("DELETE FROM crm_consultas WHERE id_empresa = $id_empresa AND id_referencia = $id AND id_origen = 16");
    return parent::delete($id);
  }

  function update($id,$data) {
    $salida = parent::update($id,$data);

    // Actualizamos el turno por si se movio de fecha
    $sql = "UPDATE crm_consultas SET ";
    $sql.= " fecha = '$data->fecha $data->hora' ";
    $sql.= "WHERE id_empresa = $data->id_empresa ";
    $sql.= "AND id_referencia = $id ";
    $sql.= "AND id_origen = 16 ";
    $sql.= "AND id_contacto = $data->id_paciente ";
    $this->db->query($sql);

    return $salida;
  }

  function get($id) {
    $id_empresa = parent::get_empresa();
    $sql = "SELECT T.*, ";
    $sql.= " IF(CLI.nombre IS NULL,'',CLI.nombre) AS paciente, ";
    $sql.= " IF(PRO.nombre IS NULL,'',CONCAT(PRO.apellido,' ',PRO.nombre)) AS profesional ";
    $sql.= "FROM med_turnos_medicos T ";
    $sql.= " INNER JOIN med_pacientes P ON (T.id_paciente = P.id_cliente AND T.id_empresa = P.id_empresa) ";
    $sql.= " INNER JOIN clientes CLI ON (P.id_cliente = CLI.id AND P.id_empresa = CLI.id_empresa) ";
    $sql.= " INNER JOIN med_profesionales PRO ON (T.id_profesional = PRO.id AND T.id_empresa = PRO.id_empresa) ";
    $sql.= "WHERE T.id = $id AND T.id_empresa = $id_empresa ";
    $q = $this->db->query($sql);
    return $q->row();
  }
  
  function calendario($conf = array()) {
    $id_empresa = isset($conf["id_empresa"])?$conf["id_empresa"]:parent::get_empresa();
    $fecha_desde = isset($conf["desde"])?$conf["desde"]:"";
    $fecha_hasta = isset($conf["hasta"])?$conf["hasta"]:"";
    $id_profesional = isset($conf["id_profesional"])?$conf["id_profesional"]:0;
    $id_paciente = isset($conf["id_paciente"])?$conf["id_paciente"]:0;
    $sql = "SELECT C.*, ";
    $sql.= " IF(C.estado=-1,'background','') AS rendering, "; // Si el horario fue deshabilitado
    $sql.= " IF(C.estado=0,'#28b492','#a94442') AS backgroundColor, ";
    $sql.= " IF(C.estado=0,'#28b492','#a94442') AS borderColor, ";
    $sql.= " IF(CLI.nombre IS NULL,'',CLI.nombre) AS title, ";
    $sql.= " CONCAT(C.fecha,' ',C.hora) AS start, ";
    $sql.= " (IF(C.duracion_tipo = 'H',DATE_ADD((CONCAT(C.fecha,' ',C.hora)),INTERVAL C.duracion_cantidad*60 MINUTE), DATE_ADD((CONCAT(C.fecha,' ',C.hora)),INTERVAL C.duracion_cantidad MINUTE))) AS end ";
    $sql.= "FROM med_turnos_medicos C ";
    $sql.= "INNER JOIN med_profesionales CO ON (C.id_profesional = CO.id AND C.id_empresa = CO.id_empresa) ";
    $sql.= "LEFT JOIN med_pacientes P ON (C.id_paciente = P.id_cliente AND C.id_empresa = P.id_empresa) ";
    $sql.= "LEFT JOIN clientes CLI ON (P.id_cliente = CLI.id AND P.id_empresa = CLI.id_empresa) ";
    $sql.= "WHERE 1=1 ";
    if (!empty($id_empresa)) $sql.= "AND C.id_empresa = $id_empresa ";
    if (!empty($id_profesional)) $sql.= "AND C.id_profesional = $id_profesional ";
    if (!empty($id_paciente)) $sql.= "AND C.id_paciente = $id_paciente ";
    if (!empty($fecha_desde)) $sql.= "AND '$fecha_desde' <= C.fecha ";
    if (!empty($fecha_hasta)) $sql.= "AND C.fecha <= '$fecha_hasta' ";
    $q = $this->db->query($sql);
    return $q->result();
  }
}