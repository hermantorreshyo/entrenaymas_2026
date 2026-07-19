<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Emails_Templates extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Email_Template_Model', 'modelo',"nombre ASC");
  }

  function enviar_plantilla() {

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    set_time_limit(0);
    $id_empresa = parent::get_post("id_empresa",parent::get_empresa());
    $id_plantilla = parent::get_post("id_email_template",0);
    $this->load->model("Empresa_Model");
    $empresa = $this->Empresa_Model->get($id_empresa);
    $this->load->model("Cliente_Model");
    require APPPATH.'libraries/Mandrill/Mandrill.php';

    // Obtenemos los contactos que se suscribieron a digital
    $contactos = $this->Cliente_Model->buscar(array(
      "id_empresa"=>$id_empresa,
      "tipo"=>8,
      "offset"=>9999999,
    ));
    $template = $this->modelo->get($id_plantilla,$id_empresa);
    if ($template === FALSE) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"No existe un template para la notificacion",
      ));
      exit();
    }
    $cantidad = 0;

    /*
    $contactos["results"] = array();
    $pablo = new stdClass();
    $pablo->nombre = "Pablo Porcel";
    $pablo->email = "porcelp@gmail.com";
    $contactos["results"][] = $pablo;
    $matias = new stdClass();
    $matias->nombre = "Matías Basile";
    $matias->email = "basile.matias99@gmail.com";
    $contactos["results"][] = $matias;
    */

    foreach($contactos["results"] as $c) {

      $body = $template->texto;
      $body = str_replace("{{nombre}}",htmlentities($c->nombre),$body);
      $body = str_replace("'", "\"", $body);

      $array = array(
        "to"=>$c->email,
        "from"=>MAIL_FROM_ADDRESS,
        "from_name"=>$empresa->nombre,
        "subject"=>$template->nombre,
        "body"=>$body,
      );

      // Si es la plantilla de certificado
      if ($id_plantilla == 135) {
        // Generamos el PDF
        $cache_dir = '/home/ubuntu/data/cache';
        require_once('/home/ubuntu/data/vendor/autoload.php');
        $mpdf = new \Mpdf\Mpdf([
          'tempDir' => $cache_dir,
          'orientation' => 'L'
        ]);
        $data_query = array(
          "nombre"=>$c->nombre,
        );
        $url = "https://en.allextruded.com/web/certificado/?".http_build_query($data_query);
        $html = file_get_contents($url);
        $mpdf->CSSselectMedia='mpdf';
        $mpdf->setBasePath($url);
        $mpdf->WriteHTML($html);
        $c->nombre = mb_strtolower($c->nombre);
        $c->nombre = str_replace("á", "a", $c->nombre);
        $c->nombre = str_replace("é", "e", $c->nombre);
        $c->nombre = str_replace("í", "i", $c->nombre);
        $c->nombre = str_replace("ó", "o", $c->nombre);
        $c->nombre = str_replace("ú", "u", $c->nombre);
        $c->nombre = str_replace("ñ", "n", $c->nombre);
        $filename = "Certificate Webinar ".ucwords($c->nombre).".pdf";
        $mpdf->Output($cache_dir."/".$filename, \Mpdf\Output\Destination::FILE);
        $array["attachments"] = array($cache_dir."/".$filename);
      }

      mandrill_send($array);
      $cantidad++;        
    }
    echo json_encode(array(
      "error"=>0,
      "cantidad"=>$cantidad,
    ));
  }  

  function notificar_milling() {

    $id_empresa = parent::get_empresa();
    $this->load->model("Empresa_Model");
    $empresa = $this->Empresa_Model->get($id_empresa);
    $this->load->model("Cliente_Model");
    require APPPATH.'libraries/Mandrill/Mandrill.php';

    // Obtenemos los contactos que se suscribieron a digital
    $contactos = $this->Cliente_Model->buscar(array(
      "id_empresa"=>$id_empresa,
      "tipo"=>2,
    ));
    $template = $this->modelo->get_by_key("notificacion",$id_empresa);
    if ($template === FALSE) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"No existe un template para la notificacion",
      ));
      exit();
    }
    $cantidad = 0;
    foreach($contactos["results"] as $c) {

      $body = $template->texto;
      $body = str_replace("{{nombre}}",$c->nombre,$body);
      $body = str_replace("'", "\"", $body);
      
      mandrill_send(array(
        "to"=>$c->email,
        "from"=>MAIL_FROM_ADDRESS,
        "from_name"=>$empresa->nombre,
        "subject"=>$template->nombre,
        "body"=>$body,
      ));
      $cantidad++;
    }
    echo json_encode(array(
      "error"=>0,
      "cantidad"=>$cantidad,
    ));
  }
    
}