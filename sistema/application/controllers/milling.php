<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Milling extends REST_Controller {

  function __construct() {
    parent::__construct();
  }

  function guardar() {
    $id_empresa = parent::get_post("id_empresa");
    $id_cliente = parent::get_post("id_cliente");
    $nombre = parent::get_post("nombre");
    $email = parent::get_post("email");
    $contacto_nombre = parent::get_post("contacto_nombre");
    $contacto_telefono = parent::get_post("contacto_telefono");
    $custom_1 = parent::get_post("custom_1");
    $custom_2 = parent::get_post("custom_2");
    $custom_3 = parent::get_post("custom_3");
    $custom_4 = parent::get_post("custom_4");
    $custom_5 = parent::get_post("custom_5");
    $sql = "UPDATE clientes SET ";
    $sql.= " nombre = '$nombre', ";
    $sql.= " contacto_nombre = '$contacto_nombre', ";
    $sql.= " contacto_telefono = '$contacto_telefono', ";
    $sql.= " custom_1 = '$custom_1', ";
    $sql.= " custom_2 = '$custom_2', ";
    $sql.= " custom_3 = '$custom_3', ";
    $sql.= " custom_4 = '$custom_4', ";
    $sql.= " custom_5 = '$custom_5' ";
    $sql.= "WHERE id_empresa = $id_empresa AND id = $id_cliente ";
    $this->db->query($sql);

    $listas = array();
    $listas[] = "059fe5c6-0f6d-11ea-9c10-d4ae5275b1a5"; // MAG
    //if (strpos($custom_5, "IAF") !== FALSE) $listas[] = "cc638bd2-3a0f-11ea-82d9-d4ae529a8639";
    //if (strpos($custom_5, "IMD") !== FALSE) $listas[] = "d27e5baa-3a0f-11ea-994e-d4ae529a8639";

    if (!empty($email)) {
      include_once("/home/ubuntu/data/sistema/application/libraries/ConstantContact.php");
      $cc = new ConstantContact();
      $cc->enviar_contacto(array(
        "nombre"=>$nombre,
        "email"=>$email,
        "listas"=>$listas,
      ));
    }
    echo json_encode(array(
      "error"=>0,
      "mensaje"=>"",
    ));
  }

}