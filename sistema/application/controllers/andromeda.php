<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Andromeda extends REST_Controller {

  function __construct() {
    parent::__construct();
  }

  function registrar() {

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    header('Access-Control-Allow-Origin: *');
    header('Content-Type:application/json; charset=UTF-8');

    $email = parent::get_post("email","");
    if (empty($email)) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"El email es obligatorio",
      ));
      exit();
    }
    $id_empresa = parent::get_post("id_empresa","");
    $nombre = parent::get_post("nombre","");
    $apellido = parent::get_post("apellido","");
    $telefono = parent::get_post("telefono","");
    $codigo_descuento = parent::get_post("codigo_descuento","");

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

    // Controlamos que el codigo de descuento no exista
    $sql = "SELECT 1 FROM andromeda_prescriptores WHERE codigo_descuento = '$codigo_descuento' ";
    $q = $this->db->query($sql);
    if ($q->num_rows()>0) {
      $salida = array(
        "mensaje"=>"Usted ya esta dado de alta en el sistema de promotores, puede utilizar el mismo codigo de promotor, no hace falta darse de alta nuevamente.",
        "error"=>1,
      );
      echo json_encode($salida);
      exit();
    }

    // Controlamos que el email no exista
    $sql = "SELECT 1 FROM andromeda_prescriptores WHERE email = '$email' ";
    $q = $this->db->query($sql);
    if ($q->num_rows()>0) {
      $salida = array(
        "mensaje"=>"Usted ya esta dado de alta en el sistema de promotores, puede utilizar el mismo codigo de promotor, no hace falta darse de alta nuevamente.",
        "error"=>1,
      );
      echo json_encode($salida);
      exit();
    }

    $empresas = array(680,681);
    // Insertamos el mismo registro en todas las empresas
    foreach($empresas as $id_empresa) {

      $codigo_andromeda = "";
      if ($id_empresa == 680) {
        $codigo_andromeda = "10X10V"; // VENNTUR
      } else if ($id_empresa == 681) {
        $codigo_andromeda = "10X10D"; // DRIVEANDO
      }

      $sql = "INSERT INTO andromeda_prescriptores (";
      $sql.= " nombre,apellido,email,telefono,codigo_descuento,id_empresa,descuento,codigo_andromeda";
      $sql.= ") VALUES(";
      $sql.= " '$nombre','$apellido','$email','$telefono','$codigo_descuento','$id_empresa',10,'$codigo_andromeda')";
      $this->db->query($sql);
    }

    echo json_encode(array(
      "error"=>0,
      "mensaje"=>"Su registro se ha enviado correctamente!",
    ));
  }

}