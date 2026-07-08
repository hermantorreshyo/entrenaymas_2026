<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Calm extends REST_Controller {

  function __construct() {
    parent::__construct();
  }

  function get_categorias() {
    header('Access-Control-Allow-Origin: *');
    $id_empresa = 997;
    $sql = "SELECT * FROM calm_categorias WHERE id_empresa = $id_empresa ORDER BY nombre ASC";
    $q = $this->db->query($sql);
    $salida = array();

    foreach($q->result() as $r) {    
      $sql = "SELECT * from calm_cursos ";
      $sql.= "WHERE activo = 1 AND id_empresa = $id_empresa ";
      $sql.= "AND id_categoria = $r->id ";
      $qq = $this->db->query($sql);
      $r->cursos = array();
      foreach($qq->result() as $curso) {
        $curso->path = (!empty($curso->path)) ? "https://www.varcreative.com/sistema/".$curso->path : "";
        $r->cursos[] = $curso;
      }
      $salida[] = $r;
    }
    echo json_encode($salida);
  }

  function get_libro() {
    header('Access-Control-Allow-Origin: *');
    $id_empresa = 997;
    $q = $this->db->query("SELECT * FROM cursos WHERE id_empresa = $id_empresa LIMIT 0,1 ");
    if ($q->num_rows() == 0) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"No se encuentra el libro",
      ));
      exit();
    }
    $r = $q->row();
    $this->load->model("Curso_Model");
    $curso = $this->Curso_Model->get($r->id,array(
      "id_empresa"=>$id_empresa,
    ));
    $salida = array(
      "nombre"=>$curso->nombre,
      "subtitulo"=>$curso->subtitulo,
      "path" => (!empty($curso->path)) ? "https://www.varcreative.com/sistema/".$curso->path : "",
      "texto"=>strip_tags($curso->texto),
      "capitulos"=>array(),
    );
    foreach($curso->clases as $clase) {
      $salida["capitulos"][] = array(
        "id"=>$clase->id,
        "nombre"=>$clase->nombre,
        "texto"=>$clase->texto,
        "path_clase" => (!empty($clase->path_clase)) ? "https://www.varcreative.com/sistema/".$clase->path_clase : "",
      );
    }
    echo json_encode($salida);
  }

  function get_curso($id) {
    header('Access-Control-Allow-Origin: *');
    $q = $this->db->query("SELECT * FROM calm_cursos WHERE activo = 1 AND id = $id ");
    if ($q->num_rows() == 0) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"No se encuentra el curso con ID: $id",
      ));
      exit();
    }
    $r = $q->row();
    unset($r->path_audio);
    $sql = "SELECT * FROM calm_cursos_audios WHERE id_curso = $r->id ORDER BY orden ASC";
    $qq = $this->db->query($sql);
    $r->audios = array();
    foreach($qq->result() as $rr) {
      $audio = new stdClass();
      $audio->id = $rr->id;
      $audio->nombre = $rr->nombre;
      $audio->duracion = $rr->duracion;
      $audio->url = (!empty($rr->path_audio)) ? "https://www.varcreative.com/sistema/".$rr->path_audio : "";
      $r->audios[] = $audio;
    }
    $r->path = (!empty($r->path)) ? "https://www.varcreative.com/sistema/".$r->path : "";
    echo json_encode($r);
  }

  function get_cursos() {
    header('Access-Control-Allow-Origin: *');
    $id_categoria = parent::get_get("id_categoria",0);
    $id_empresa = 997;
    $sql = "SELECT * from calm_cursos where activo = 1 AND id_empresa = $id_empresa ";
    if (!empty($id_categoria)) $sql.= "AND id_categoria = $id_categoria ";
    $q = $this->db->query($sql);
    $salida = array();
    foreach($q->result() as $r) {
      unset($r->path_audio);
      $r->path = (!empty($r->path)) ? "https://www.varcreative.com/sistema/".$r->path : "";
      $salida[] = $r;
    }
    echo json_encode($salida);
  }

  function get_escenas() {
    header('Access-Control-Allow-Origin: *');
    $q = $this->db->query('Select * from calm_escenas where activo = 1');
    $salida = array();
    foreach($q->result() as $r) {
      $r->path_audio = (!empty($r->path_audio)) ? "https://www.varcreative.com/sistema/".$r->path_audio : "";
      $r->path = (!empty($r->path)) ? "https://www.varcreative.com/sistema/".$r->path : "";
      $salida[] = $r;
    }
    echo json_encode($salida);
  }

  function get_escena($id) {
    header('Access-Control-Allow-Origin: *');
    $q = $this->db->query("SELECT * FROM calm_escenas WHERE activo = 1 AND id = $id ");
    if ($q->num_rows() == 0) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"No se encuentra el curso con ID: $id",
      ));
      exit();
    }
    $r = $q->row();
    $r->path_audio = (!empty($r->path_audio)) ? "https://www.varcreative.com/sistema/".$r->path_audio : "";
    $r->path = (!empty($r->path)) ? "https://www.varcreative.com/sistema/".$r->path : "";
    echo json_encode($r);
  }

}