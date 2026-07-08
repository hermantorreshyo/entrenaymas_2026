<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Khios extends REST_Controller {

	function __construct() {
		parent::__construct();
	}

	function get($id) {
		echo json_encode(array());
	}

	function insert($id) {
		echo json_encode(array());
	}

	function update($id) {
		echo json_encode(array());
	}

	function delete($id) {
		echo json_encode(array());
	}
	
}