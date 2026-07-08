<?php defined('BASEPATH') OR exit('No direct script access allowed');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require APPPATH.'libraries/REST_Controller.php';

class Not_Editores extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Not_Editor_Model', 'modelo');
  }

  function registrar() {

    $email = $this->input->post("email");
    $nombre = $this->input->post("nombre");
    $id_editor = $this->input->post("id_editor");
    $id_empresa = $this->input->post("id_empresa");

    $this->load->model("Empresa_Model");
    $empresa = $this->Empresa_Model->get_min($id_empresa);

    $editor = $this->modelo->get_editor_by_id($id_editor,$id_empresa);
    if (empty($editor)) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"Error al obtener el editor $id_editor",
      ));
      exit();
    }

    // Si estamos usando reCAPTCHA
    $captcha = $this->input->post("g-recaptcha-response");
    if ($captcha !== FALSE) {
      require APPPATH.'libraries/recaptchalib.php';
      $site_key = "6LeHSTQUAAAAAA5FV121v-M7rnhqdkXZIGmP9N8E";
      $secret = "6LeHSTQUAAAAACG9dCyy6hv24tlRYL8TKtxe4O54";
      $reCaptcha = new ReCaptcha($secret);
      $resp = $reCaptcha->verifyResponse(
        $_SERVER["REMOTE_ADDR"],
        $captcha
      );
      if ($resp == null || !isset($resp->success) || $resp->success === FALSE) {
        $salida = array(
          "mensaje"=>"El codigo de validacion es incorrecto.",
          "error"=>1,
        );
        echo json_encode($salida);
        exit();
      }
    }

    // Si se paso un email, buscamos el contacto para saber si existe
    $this->load->model("Cliente_Model");
    $contacto = (!empty($email)) ? $this->Cliente_Model->get_by_email($email,$id_empresa) : FALSE;
    
    if ($contacto === FALSE) {
      // Debemos crearlo
      $contacto = new stdClass();
      $contacto->id_empresa = $id_empresa;
      $contacto->email = $email;
      $contacto->nombre = $nombre;
      $contacto->fecha_inicial = date("Y-m-d");
      $contacto->fecha_ult_operacion = date("Y-m-d H:i:s");
      $contacto->tipo = 1; // Contacto
      $contacto->activo = 1; // El cliente esta activo por defecto
      $contacto->id_sucursal = 0; // Para que en algunas BD no tire error de default value
      $id = $this->Cliente_Model->insert($contacto);
      $contacto->id = $id;

      // REGISTRAMOS COMO UN EVENTO LA CREACION DEL NUEVO USUARIO
      $this->load->model("Consulta_Model");
      $this->Consulta_Model->registro_creacion_usuario(array(
        "id_contacto"=>$id,
        "id_empresa"=>$id_empresa,
      ));
    }

    // Agregamos a la relacion
    $sql = "SELECT * FROM not_editores_seguidores WHERE id_empresa = $id_empresa AND id_usuario = $contacto->id AND id_editor = $id_editor ";
    $q = $this->db->query($sql);
    if ($q->num_rows() == 0) {
      $sql = "INSERT INTO not_editores_seguidores (id_empresa, id_editor, id_usuario, creacion) VALUES (";
      $sql.= " '$id_empresa', '$id_editor', '$contacto->id', NOW() )";
      $this->db->query($sql);      
    }

    $bcc_array = array("basile.matias99@gmail.com","porcelp@gmail.com");
    $body = "Email: $email<br/>";
    $body.= "Nombre: $nombre";

    require APPPATH.'libraries/Mandrill/Mandrill.php';
    mandrill_send(array(
      "to"=>$bcc_array,
      "from"=>"no-reply@varcreative.com",
      "from_name"=>$empresa->nombre,
      "subject"=>"Nuevo seguidor de $editor->nombre",
      "body"=>$body,
    ));
    echo json_encode(array(
      "error"=>0,
    ));
  }
	
}