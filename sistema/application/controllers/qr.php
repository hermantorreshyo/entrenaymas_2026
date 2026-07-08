<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Qr extends CI_Controller {

  function __construct() {
    parent::__construct();
  }
  
  function registrar() {
    $id_empresa = $this->input->get("e");
    $id_cliente = $this->input->get("c");
    $link = $this->input->get("l");
    $link = urldecode($link);
    $sql = "INSERT INTO qr_click_links (id_empresa,fecha,link,id_cliente) VALUES(";
    $sql.= "'$id_empresa',NOW(),'$link','$id_cliente') ";
    $this->db->query($sql);
    header("Location: $link");
  }

}