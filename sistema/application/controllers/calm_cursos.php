<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Calm_Cursos extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Calm_Curso_Model', 'modelo');
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