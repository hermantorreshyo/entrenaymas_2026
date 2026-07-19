<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Permiso_Red_Model extends Abstract_Model {

  // Devuelve todas las inmobiliarias que estan compartiendo en la red
  function get_inmobiliarias_red($config = array()) {
    $id_empresa = (isset($config["id_empresa"])) ? $config["id_empresa"] : parent::get_empresa();
    $sql = "SELECT E.id, E.nombre, E.razon_social, E.path AS logo, WC.email, ";
    $sql.= " WC.telefono_web, WC.direccion_web, ";
    $sql.= " IF(L.nombre IS NULL,'',L.nombre) AS localidad ";
    $sql.= "FROM empresas E ";
    $sql.= "INNER JOIN web_configuracion WC ON (E.id = WC.id_empresa) ";
    $sql.= "LEFT JOIN com_localidades L ON (E.id_localidad = L.id) ";
    $sql.= "WHERE E.id_proyecto = 3 "; // Solamente los de INMOVAR
    $sql.= "AND WC.red_inmovar = 1 "; // Que pertenezcan a la RED
    $sql.= "AND E.id != $id_empresa "; // Que no sea la misma empresa
    $sql.= "ORDER BY E.id DESC ";
    $salida = array();
    $q = $this->db->query($sql);
    foreach($q->result() as $row) {
      $row->nombre = ucwords(strtolower($row->nombre));
      $row->razon_social = ucwords(strtolower($row->razon_social));
      $row->localidad = ucwords(strtolower($row->localidad));
      $row->telefono_web = preg_replace("/[^0-9]/", "", $row->telefono_web);
      $salida[] = $row;
    }
    return $salida;
  }
  
  function notificar($config = array()) {
    $id_empresa = (isset($config["id_empresa"])) ? $config["id_empresa"] : parent::get_empresa();

    // Obtenemos los datos de la nueva inmobiliaria
    $this->load->model("Empresa_Model");
    $empresa = $this->Empresa_Model->get_min($id_empresa);
    $empresa->nombre = ucwords(strtolower($empresa->nombre));

    // Obtenemos los datos del email que vamos a armar
    $this->load->model("Email_Template_Model");
    $template = $this->Email_Template_Model->get_by_key("nueva-inmobiliaria",118);
    $bcc_array = array(); //array("basile.matias99@gmail.com","misticastudio@gmail.com");
    include_once APPPATH.'libraries/Mandrill/Mandrill.php';
    $empresas = $this->get_inmobiliarias_red(array(
      "id_empresa"=>$id_empresa
    ));
    foreach($empresas as $emp) {
      $emp->email = "basile.matias99@gmail.com";
      // Reemplazamos los textos
      $asunto = $template->nombre;
      $texto = $template->texto;
      $asunto = str_replace("{{inmobiliaria}}", $empresa->nombre, $asunto);
      $asunto = str_replace("{{nombre}}", $emp->nombre, $asunto);
      $texto = str_replace("{{inmobiliaria}}", $empresa->nombre, $texto);
      $texto = str_replace("{{nombre}}", $emp->nombre, $texto);
      // Enviamos el email a la inmobiliaria con la invitacion de la nueva
      mandrill_send(array(
        "to"=>$emp->email,
        "from"=>MAIL_FROM_ADDRESS,
        "from_name"=>"Inmovar",
        "subject"=>$asunto,
        "body"=>$texto,
        "bcc"=>$bcc_array,
      ));
      // Guardamos una nueva notificacion en el panel de la propia inmobiliaria
      $this->db->insert("com_log",array(
        "id_empresa"=>$emp->id,
        "importancia"=>'N',
        "fecha"=>date('Y-m-d H:i:s'),
        "id_usuario"=>0,
        "texto"=>$asunto,
        "leida"=>0,
      ));
    }
  }

}