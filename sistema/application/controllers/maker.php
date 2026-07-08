<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Maker extends REST_Controller {

  function __construct() {
    parent::__construct();
  }

  function test() {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    $this->load->library("maker/MakerService");
    $this->makerservice->obtenerEmpresas();
  }

}