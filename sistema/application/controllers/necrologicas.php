<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Necrologicas extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Necrologica_Model', 'modelo',"nombre ASC",1);
  }

  function enviar_participacion() {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    $this->load->helper("fecha_helper");
    $id_empresa = 70;
    $nombre = utf8_encode(parent::get_post("nombre",""));
    $email = parent::get_post("email","");
    $id_participacion = parent::get_post("id_participacion",0);

    $sql = "SELECT *, DATE_FORMAT(fecha_traslado,'%d/%m/%Y') AS fecha_traslado,  ";
    $sql.= " DATE_FORMAT(fecha_fallecimiento,'%d/%m/%Y') AS fecha_fallecimiento ";
    $sql.= "FROM inf_necrologicas WHERE id_empresa = $id_empresa AND id = $id_participacion";
    $q = $this->db->query($sql);
    if ($q->num_rows() <= 0) {
      echo json_encode(array("error"=>0)); exit();
    }
    $necro = $q->row();
    $texto = "Falleci&oacute; a la edad de $necro->edad a&ntilde;os. <b>$nombre</b> participa/n su fallecimiento e invita/n a acompa&ntilde;ar sus restos al $necro->cementerio el $necro->fecha_traslado a las $necro->hora_traslado hs.";
    $sql = "INSERT INTO inf_necrologicas ";
    $sql.= " (id_empresa, edad, nombre, participacion, participante, participante_email, texto, fecha, activo, fecha_fallecimiento) VALUES (";
    $sql.= " $id_empresa, '$necro->edad', '$necro->nombre', 1, '$nombre', '$email', '$texto', NOW(), 0, '".fecha_mysql($necro->fecha_fallecimiento)."') ";
    $this->db->query($sql);

    require APPPATH.'libraries/Mandrill/Mandrill.php';
    $body = $texto;
    mandrill_send(array(
      "to"=>"quepensaschacabuco@gmail.com",
      "from"=>MAIL_FROM_ADDRESS,
      "subject"=>"Nueva participacion funebre",
      "body"=>$body,
    ));

    echo json_encode(array("error"=>0));
  }
  
}