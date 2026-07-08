<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Web_Slider extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Web_Slide_Model', 'modelo',"orden ASC",1);
  }

  function eliminar($id) {
    $id_empresa = parent::get_empresa();
    $sql = "DELETE FROM web_slider WHERE id_empresa = $id_empresa AND id = $id";
    $this->db->query($sql);
    echo json_encode(array(
      "error"=>0,
    ));
  }

  function get($id) {

    // Obtenemos todos los registros
    if ($id == "index") {
      
      $limit = $this->input->get("limit");
      $offset = $this->input->get("offset");
      $filter = $this->input->get("filter");
      $order_by = $this->input->get("order_by");
      $order = $this->input->get("order");
      $clave = ($this->input->get("clave") === FALSE) ? "" : $this->input->get("clave");
      
      if (!empty($filter)) {
        $lista = $this->modelo->find($filter,$clave);  
      } else {
        $lista = $this->modelo->get_all($limit,$offset,$order_by,$order,$clave);
      }
      
      if (!$lista) $lista = array();

      // Total de lista
      $total = $this->modelo->count_all();

      // Armamos la salida
      $salida = array(
        "total"=> $total,
        "results"=>$lista
      );
      echo json_encode($salida);
    } 
    else 
    {
      // Estamos obteniendo un elemento en particular
      echo json_encode($this->modelo->get($id));
    }

  }

  
  function save_image($dir="",$filename="") {
		$id_empresa = $this->get_empresa();
		$dir = "uploads/$id_empresa/slider/";
		$filename = $this->input->post("file");
		echo parent::save_image($dir,$filename);
  }  

  function save_file() {
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
  $path = "uploads/$id_empresa/slider/$filename";
  @move_uploaded_file($_FILES["path"]["tmp_name"],$path);
  echo json_encode(array(
    "path"=>$path,
    "error"=>0,
  ));
  } 
    
  function ordenar() {
    $ids = $this->input->post("ids");
    $id_empresa = parent::get_empresa();
    if (!empty($ids)) {
      $ids = json_decode($ids);
      for($i=0;$i<sizeof($ids);$i++) {
        $id = $ids[$i];
        $this->db->query("UPDATE web_slider SET orden = $i WHERE id = $id AND id_empresa = $id_empresa");
      }
    }
    echo json_encode(array());
  }
  
}