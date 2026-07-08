<?php defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH.'libraries/REST_Controller.php';

class Cursos_Autores extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Curso_Autor_Model', 'modelo');
  }

  function save_file() {
    $this->load->helper("imagen_helper");
    $this->load->helper("file_helper");
    $id_empresa = $this->get_empresa();
    if (!isset($_FILES['path']) || empty($_FILES['path'])) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"No se ha enviado ningun archivo."
      ));
      return;
    }
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
}