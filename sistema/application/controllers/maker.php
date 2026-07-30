<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Maker extends REST_Controller {

  function __construct() {
    parent::__construct();
  }

  function test() {
    error_reporting(0); // Evita que warnings/deprecations de PHP contaminen la respuesta
    $this->load->library("maker/MakerService");
    $this->makerservice->obtenerEmpresas();
  }

}