<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Chat extends CI_Controller {

  function __construct() {
    parent::__construct();
  }

  function get($id_empresa = 0) {
    header('Access-Control-Allow-Origin: *');
    $id_empresa = (int) $id_empresa;

    // Obtenemos la configuracion especifica del chat
    $sql = "SELECT * FROM chat_configuracion WHERE id_empresa = $id_empresa ";
    $query = $this->db->query($sql);
    $chat_conf = $query->row();
    if (empty($chat_conf)) {
      echo ""; exit(); // Devuelve vacio y no se hace nada
    }

    // Obtenemos las preguntas generales
    $this->load->model("Chat_Pregunta_Model");
    $conf = $this->Chat_Pregunta_Model->get_by_type();
    if (!empty($chat_conf->chat_pregunta)) {
      $conf["questions"][] = array(
        "text"=>$chat_conf->chat_pregunta
      );      
    }
    $conf["user"] = $chat_conf->chat_nombre;
    $conf["color"] = $chat_conf->chat_color;
    $conf["id_empresa"] = $id_empresa;

    $css = $this->load->view("chat/chat_css",array(
      "color"=>$conf["color"],
    ),true);
    $base = $this->load->view("chat/chat_base",null,true);
    $this->load->view("chat/chat",array(
      "chat_config"=>$conf,
      "chat_css"=>$css,
      "chat_base"=>$base,
    ));
  }

}