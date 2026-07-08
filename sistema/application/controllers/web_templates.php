<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Web_Templates extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Web_Template_Model', 'modelo',"nombre ASC",0);
  }
  
  function save_image($dir="",$filename="") {
    $dir = "uploads/images/";
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
    $path = "uploads/images/$filename";
    @move_uploaded_file($_FILES["path"]["tmp_name"],$path);
    echo json_encode(array(
      "path"=>$path,
      "error"=>0,
    ));
  }  

  function lista() {
    $id_empresa = $this->input->post("id_empresa");
    $id_proyecto = $this->input->post("id_proyecto");

    // Templates publicos
    $sql = "SELECT * FROM web_templates ";
    $sql.= "WHERE id_proyecto = $id_proyecto AND publico = 1 ";
    $q = $this->db->query($sql);
    $opciones = $q->result();

    // Templates que pertenecen a la empresa
    $sql = "SELECT * FROM web_templates ";
    $sql.= "WHERE id_proyecto = $id_proyecto AND publico = 0 AND id_empresa = $id_empresa ";
    $q = $this->db->query($sql);   
    if ($q->num_rows()>0) {
      $opciones = array_merge($opciones,$q->result());
    }

    $sql = "SELECT T.* FROM empresas E INNER JOIN web_templates T ON (E.id_web_template = T.id) ";
    $sql.= "WHERE E.id = $id_empresa ";
    $q_empresa = $this->db->query($sql);
    $seleccionado = $q_empresa->row();

    echo json_encode(array(
      "opciones"=>$opciones,
      "seleccionado"=>$seleccionado,
    ));
  }

  function elegir_template() {
    $id_empresa = $this->input->post("id_empresa");
    $id_web_template = $this->input->post("id_template");
    $sql = "UPDATE empresas SET id_web_template = $id_web_template WHERE id = $id_empresa ";
    $this->db->query($sql);
    echo json_encode(array(
      "error"=>0,
    ));
  }
}