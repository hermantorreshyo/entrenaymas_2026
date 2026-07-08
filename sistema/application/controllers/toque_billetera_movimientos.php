<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Toque_Billetera_Movimientos extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Toque_Billetera_Movimiento_Model', 'modelo');
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
  
  function delete($id = null) {
    $this->modelo->borrar(array(
      "id"=>$id,
    ));
    echo json_encode(array());
  }  

  function listado() {

    $this->load->helper("fecha_helper");
    $id_cliente = $this->get_post("id_cliente",0);
    $tipo = $this->get_post("tipo",0);
    $id_concepto = $this->get_post("id_concepto",0);
    $id_usuario = $this->get_post("id_usuario",0);
    $id_factura = $this->get_post("id_factura",0);
    $filter = $this->get_post("filter","");
    
    $desde = $this->get_post("desde");
    if (!empty($desde)) $desde = fecha_mysql($desde);
    $hasta = $this->get_post("hasta");
    if (!empty($hasta)) $hasta = date("Y-m-d",strtotime(fecha_mysql($hasta)." +1 day"));

    $config = array(
      "filter"=>$filter,
      "id_cliente"=>$id_cliente,
      "id_concepto"=>$id_concepto,
      "id_factura"=>$id_factura,
      "id_usuario"=>$id_usuario,
      "desde"=>$desde,
      "hasta"=>$hasta,
    );
    $resultado = $this->modelo->buscar($config);
    echo json_encode($resultado);
  }

}