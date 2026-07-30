<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Chat_Turnos extends CI_Controller {

  function __construct() {
    parent::__construct();
  }

  /*
  function count() {
    header('Access-Control-Allow-Origin: *');
    $id_empresa = $this->input->get("id_empresa");
    if (!is_numeric($id_empresa)) { echo json_encode(array()); return; }
    $id_usuario = $this->input->get("id_usuario");
    if (!is_numeric($id_usuario)) { echo json_encode(array()); return; }
    
    $pagina = ($this->input->get("pagina") !== FALSE) ? $this->input->get("pagina") : "";
    $pagina = str_replace("www.", "", $pagina);
    $pagina = str_replace("http://", "", $pagina);
    $pagina = str_replace("https://", "", $pagina);

    $stamp = time();
    $sql = "INSERT INTO whatsapp_clicks (id_empresa,id_usuario,stamp,pagina) VALUES ('$id_empresa','$id_usuario','$stamp','$pagina') ";
    $this->db->query($sql);
    echo json_encode(array());
  }
  */

  /*
  // INSTALACION
<script type="text/javascript" src="https://www.varcreative.com/sistema/resources/js/loader.js"></script>
<script type="text/javascript">loadScript("https://www.varcreative.com/sistema/chat_turnos/get/"+window.location.hostname+"/");</script>
  */
  function get($dominio = 0) {
    date_default_timezone_set("America/Argentina/Buenos_Aires");
    error_reporting(0); // Evita que warnings/deprecations de PHP contaminen la respuesta
    header('Access-Control-Allow-Origin: *');

    $this->load->model("Empresa_Model");
    /*
    $dominio = str_replace("www.", "", $dominio);
    $id_empresa = $this->Empresa_Model->get_id_empresa_by_dominio($dominio);
    if ($id_empresa == 0) {
      // No existe la empresa seleccionada, no devolvemos nada
      exit();
    }
    */
    $id_empresa = 389;

    $this->load->model("Turno_Servicio_Model");
    $usuarios = $this->Turno_Servicio_Model->buscar(array(
      "id_empresa"=>$id_empresa,
    ));
    foreach($usuarios["results"] as $u) {
      $u->cargo = "";
      $u->disponible = 1;
      $u->path = (empty($u->path)) ? "https://www.varcreative.com/sistema/resources/images/a0.jpg" : "https://www.varcreative.com/sistema/".$u->path;
      $u->dias = implode("-",$u->dias);
    }

    $empresa = $this->Empresa_Model->get($id_empresa);
    $conf = array();
    $conf["usuarios"] = $usuarios["results"];
    $conf["empresa"] = $empresa;
    $conf["abierto"] = 1; //$empresa->config["chat_turnos_abierto"];
    $conf["posicion"] = "D"; //$empresa->config["chat_turnos_posicion"];
    $conf["sonido"] = 1; //$empresa->config["chat_turnos_sonido"];
    $tpl_base = $this->load->view("turnos/chat_base",null,true);
    $tpl_user = $this->load->view("turnos/chat_user",null,true);
    $this->load->view("turnos/chat",array(
      "id_empresa"=>$id_empresa,
      "config"=>$conf,
      "tpl_base"=>$tpl_base,
      "tpl_user"=>$tpl_user,
    ));
  }
}