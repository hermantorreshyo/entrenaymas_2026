<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Cursos extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Curso_Model', 'modelo');
  }

  function duplicar($id) {
      
    $this->load->helper("fecha_helper");
    $this->load->helper("file_helper");

    $id_empresa = parent::get_empresa();
    /*
    $control_plan = $this->modelo->controlar_plan($id_empresa);
    if ($control_plan !== TRUE) {
      echo json_encode($control_plan);
      exit();
    }
    */

    $curso = $this->modelo->get($id);
    if ($curso === FALSE) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"No se encuentra el curso con ID: $id",
      ));
      return;
    }

    $clases = $curso->clases;
    $images = $curso->images;
    $etiquetas = $curso->etiquetas;
    unset($curso->clases);
    unset($curso->images);
    unset($curso->etiquetas);

    $curso->id = 0;
    $curso->link = ""; // Como el link tiene el ID, se tiene que generar de vuelta
    
    $insert_id = $this->modelo->insert($curso);
      
    // Actualizar el link
    $this->modelo->crear_link(array(
      "id"=>$insert_id,
      "id_empresa"=>$curso->id_empresa,
      "id_categoria"=>$curso->id_categoria,
      "nombre"=>$curso->nombre,
    ));

    $i=1;
    foreach($clases as $p) {
      $this->db->insert("cursos_clases",array(
        "id_curso"=>$insert_id,
        "nombre"=>$p->nombre,
        "video"=>$p->video,
        "path_clase"=>$p->path_clase,
        "id_empresa"=>$p->id_empresa,
        "orden"=>$p->orden,
        "texto"=>$p->texto,
        "audio"=>$p->audio,
        "custom_1"=>$p->custom_1,
        "custom_2"=>$p->custom_2,
        "custom_3"=>$p->custom_3,
        "custom_4"=>$p->custom_4,
        "custom_5"=>$p->custom_5,
        "custom_6"=>$p->custom_6,
        "custom_7"=>$p->custom_7,
        "custom_8"=>$p->custom_8,
        "custom_9"=>$p->custom_9,
        "custom_10"=>$p->custom_10,
      ));
      $i++;
    }

    $i=1;
    foreach($images as $p) {
      $this->db->insert("cursos_images",array(
        "id_curso"=>$insert_id,
        "path"=>$p,
        "id_empresa"=>$curso->id_empresa,
        "orden"=>$i,
      ));
      $i++;
    }

    foreach($etiquetas as $p) {
      $tag = new stdClass();
      $tag->id_curso = $insert_id;
      $tag->nombre = $p;
      $tag->id_empresa = $curso->id_empresa;
      $this->modelo->save_tag($tag);
    }

    echo json_encode(array(
      "id"=>$insert_id
    ));
  }  

  function get($id) {
    if ($id == "index") {
      $limit = parent::get_get("limit",0);
      $offset = parent::get_get("offset",10);
      $filter = parent::get_get("filter","");
      $order_by = parent::get_get("order_by","A.nombre");
      $order = parent::get_get("order","ASC");
      $salida = $this->modelo->buscar(array(
        "limit"=>$limit,
        "offset"=>$offset,
        "filter"=>$filter,
        "order"=>$order,
        "order_by"=>$order_by,
      ));
      echo json_encode($salida);
    } else {
      // Estamos obteniendo un elemento en particular
      echo json_encode($this->modelo->get($id));
    }
  }  

  function save_file() {
    $this->load->helper("file_helper");
    $this->load->helper("imagen_helper");
    $id_empresa = $this->get_empresa();
    if (!isset($_FILES['path']) || empty($_FILES['path'])) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"No se ha enviado ningun archivo."
      ));
      return;
    }
    // Primero copiamos el archivo
    $filename = filename($_FILES["path"]["name"],"-");
    $path = "uploads/$id_empresa/entradas/";
    @move_uploaded_file($_FILES["path"]["tmp_name"],$path.$filename);
    // Si es una imagen, lo redimensionamos
    if (is_image($filename)) {
      resize(array(
        "dir"=>$path,
        "filename"=>$filename,
      ));
    }
    echo json_encode(array(
      "path"=>$path.$filename,
      "error"=>0,
    ));
  }
	
  function save_image($dir="",$filename="") {
		$id_empresa = $this->get_empresa();
		$dir = "uploads/$id_empresa/entradas/";
		$filename = $this->input->post("file");
		echo parent::save_image($dir,$filename);
  }	
	
}