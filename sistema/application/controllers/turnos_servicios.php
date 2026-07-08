<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Turnos_Servicios extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Turno_Servicio_Model', 'modelo');
  }

  function get_consultas() {
    $this->load->model("Consulta_Model");
    $this->load->helper("fecha_helper");
    $id_servicio = $this->input->post("id_servicio");
    $estado_turno = $this->input->post("estado_turno");
    $fecha = $this->input->post("fecha");
    $fecha = ($fecha !== FALSE) ? fecha_mysql($fecha) : "";
    $res = $this->Consulta_Model->buscar(array(
      "id_servicio"=>$id_servicio,
      "fecha"=>$fecha,
      "estado_turno"=>$estado_turno,
      "offset"=>999999,
      "tipo"=>1,
    ));
    echo json_encode($res["results"]);
  }

  function ver() {
    $limit = $this->input->get("limit");
    $offset = $this->input->get("offset");
    $order_by = $this->input->get("order_by");
    $id_usuario = $this->input->get("id_usuario",0);
    $order = $this->input->get("order");
    $filter = $this->input->get("filter");
    if (!empty($order_by) && !empty($order)) $order = $order_by." ".$order;
    else $order = "";
    $conf = array(
      "id_usuario"=>$id_usuario,
      "filter"=>$filter,
      "limit"=>$limit,
      "offset"=>$offset,
      "order"=>$order,
    );
    $r = $this->modelo->buscar($conf);
    echo json_encode($r);
  }

  function save_image($dir="",$filename="") {
    $id_empresa = $this->get_empresa();
    $dir = "uploads/$id_empresa/entradas/";
    $filename = $this->input->post("file");
    $res = parent::save_image($dir,$filename);

    if ($this->input->post("thumbnail_width") !== FALSE) {
      $resp = json_decode($res);
      $filename = str_replace($dir, "", $resp->path);
      $thumbnail_width = $this->input->post("thumbnail_width");
      $thumbnail_height = $this->input->post("thumbnail_height");
      if ($thumbnail_width != 0 && $thumbnail_height != 0) {
        parent::thumbnails(array(
          "dir"=>$dir,
          "preffix"=>"thumb_",
          "filename"=>$filename,
          "thumbnail_width"=>$thumbnail_width,
          "thumbnail_height"=>$thumbnail_height,                
          "tipo_redimension"=>2,
        ));                
      }
    }        
    echo $res;
  }

}