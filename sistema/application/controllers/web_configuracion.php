<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Web_Configuracion extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Web_Configuracion_Model', 'modelo');
  }

  function set_menu_configuracion() {
    $id_empresa = parent::get_empresa();
    $conf = parent::get_post("menu_configuracion","");
    if (empty($conf)) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"La configuracion no puede ser vacia."
      ));
      exit();
    }
    $conf = addslashes($conf);
    $sql = "UPDATE web_configuracion SET menu_configuracion = '$conf' WHERE id_empresa = $id_empresa ";
    $this->db->query($sql);
    echo json_encode(array(
      "error"=>0,
    ));
  }  

  function save_attribute() {
    $id_empresa = $this->get_empresa();
    $attribute = $this->input->post("attribute");
    $value = $this->input->post("value");
    if (!empty($attribute)) {
      $this->db->query("UPDATE web_configuracion SET $attribute = '$value' WHERE id_empresa = $id_empresa ");    
    }
    echo json_encode(array(
      "error"=>0
    ));
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
    $path = "uploads/$id_empresa/$filename";
    @move_uploaded_file($_FILES["path"]["tmp_name"],$path);
    echo json_encode(array(
      "path"=>$path,
      "error"=>0,
    ));
  }

  function save_image($dir="",$filename="") {
    $id_empresa = $this->get_empresa();
    $dir = "uploads/$id_empresa/";
    $filename = $this->input->post("file");
    echo parent::save_image($dir,$filename);
  }    


}