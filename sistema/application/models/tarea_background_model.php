<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Tarea_Background_Model extends Abstract_Model {
	
  function guardar($config) {
    $url = (isset($config["url"]) ? $config["url"] : "");
    $fecha = (isset($config["fecha"]) ? $config["fecha"] : date("Y-m-d H:i:s"));
    $this->db->insert("com_tareas_background",array(
      "url"=>$url,
      "fecha"=>$fecha,
      "realizada"=>0,
    ));
  }
}