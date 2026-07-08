<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Cursos_Evaluaciones extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Cursos_Evaluaciones_Model', 'modelo');
  }

  function buscar() {
    $limit = parent::get_get("limit",0);
    $id_usuario = parent::get_get("id_usuario",0);
    $id_clase = parent::get_get("id_clase",0);
    $id_curso = parent::get_get("id_curso",0);
    $id_etiqueta = parent::get_get("id_etiqueta",0);
    $estado = parent::get_get("estado",-1);
    $offset = parent::get_get("offset",10);
    $salida = $this->modelo->buscar(array(
      "limit"=>$limit,
      "offset"=>$offset,
      "id_usuario"=>$id_usuario,
      "id_clase"=>$id_clase,
      "id_etiqueta"=>$id_etiqueta,
      "id_curso"=>$id_curso,
      "estado"=>$estado,
    ));
    echo json_encode($salida);
  }

  function enviar() {

    header('Access-Control-Allow-Origin: *');
    header('Content-Type:application/json; charset=UTF-8');
    $id_empresa = $this->input->post("id_empresa");
    $id_curso = $this->input->post("id_curso");
    $id_clase = $this->input->post("id_clase");
    $id_usuario = $this->input->post("id_usuario");
    $respuestas = json_decode($this->input->post("respuestas"));
    $correctas = 0;
    foreach($respuestas as $resp) {
      $sql = "SELECT * FROM cursos_clases_respuestas ";
      $sql.= "WHERE id_empresa = $id_empresa ";
      $sql.= "AND id_pregunta = $resp->id_pregunta ";
      $sql.= "AND id = $resp->id_respuesta ";
      $q = $this->db->query($sql);
      if ($q->num_rows() == 0) continue;
      $respuesta = $q->row();
      if ($respuesta->correcta == 1) $correctas++;
    }

    // Obtenemos la clase
    $sql = "SELECT * FROM cursos_clases WHERE id_empresa = $id_empresa AND id = $id_clase ";
    $q = $this->db->query($sql);
    $clase = $q->row();

    // 1 = APROBADO
    // 0 = NO APROBADO
    $estado = ($correctas >= $clase->respuestas_correctas) ? 1 : 0;

    // Guardamos la evaluacion
    $fecha = date("Y-m-d H:i:s");
    $sql = "INSERT INTO cursos_evaluaciones (id_empresa, id_curso, id_usuario, estado, id_clase, fecha) VALUES (";
    $sql.= " '$id_empresa', '$id_curso', '$id_usuario', '$estado', '$id_clase', '$fecha' ) ";
    $this->db->query($sql);

    // Recorremos las respuestas
    foreach($respuestas as $resp) {
      $sql = "INSERT INTO cursos_evaluaciones_respuestas (";
      $sql.= " id_empresa, id_curso, id_clase, id_usuario, id_pregunta, id_respuesta, fecha ";
      $sql.= " ) VALUES ( ";
      $sql.= " '$id_empresa', '$id_curso', '$id_clase', '$id_usuario', '$resp->id_pregunta', '$resp->id_respuesta', '$fecha' ";
      $sql.= " ) ";
      $this->db->query($sql);
    }

    // Obtenemos la proxima clase
    $sql = "SELECT * FROM cursos_clases ";
    $sql.= "WHERE id_empresa = $id_empresa ";
    $sql.= "AND id_curso = $id_curso ";
    $sql.= "AND orden > $clase->orden ";
    $sql.= "AND id != $id_clase ";
    $q = $this->db->query($sql);
    if ($q->num_rows() > 0) {
      $proxima_clase = $q->row();
      $id_proxima_clase = $proxima_clase->id;
    } else {
      $id_proxima_clase = 0;
    }
    echo json_encode(array(
      "error"=>0,
      "estado"=>$estado,
      "id_proxima_clase"=>$id_proxima_clase,
    ));

  }
	
}