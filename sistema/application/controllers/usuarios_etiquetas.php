<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Usuarios_Etiquetas extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Usuario_Etiqueta_Model', 'modelo');
  }

  function buscar() {
    $grupo = parent::get_get("grupo",0);
    $salida = $this->modelo->buscar(array(
      "grupo"=>$grupo,
    ));
    echo json_encode($salida);
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
    $path = "uploads/$id_empresa/marcas/";
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
		$dir = "uploads/$id_empresa/marcas/";
		$filename = $this->input->post("file");
		echo parent::save_image($dir,$filename);
  }	
	
  function get_by_nombre() {
		$id_empresa = parent::get_empresa();
    $nombre = $this->input->get("term");
    $sql = "SELECT * ";
    $sql.= "FROM com_usuarios_etiquetas ";
    $sql.= "WHERE nombre LIKE '%$nombre%' ";
		$sql.= "AND id_empresa = $id_empresa ";
    $q = $this->db->query($sql);
    $resultado = array();
    foreach($q->result() as $r) {
      $rr = new stdClass();
      $rr->id = $r->id;
      $rr->value = $r->nombre;
      $rr->label = $r->nombre;
      $resultado[] = $rr;
    }
    echo json_encode($resultado);
  }
    
}