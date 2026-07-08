<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Roturas_Mercaderias_Items extends REST_Controller {

  function __construct() {
    parent::__construct();
	}

  function insert() { echo json_encode(array()); }
  function delete() { echo json_encode(array()); }
  function update() { echo json_encode(array()); }
  function get() { echo json_encode(array()); }

}