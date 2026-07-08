<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Petips_Productos extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Petips_Producto_Model', 'modelo');
  }

  function upload_images($id_empresa = 0) {
    $id_empresa = (empty($id_empresa)) ? $this->get_empresa() : $id_empresa;
    return parent::upload_images(array(
      "id_empresa"=>$id_empresa,
      "clave_width"=>"producto_galeria_image_width",
      "clave_height"=>"producto_galeria_image_height",
      "upload_dir"=>"uploads/$id_empresa/articulos/",
    ));
  }
	
}