<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Sector_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("mant_sectores","id","nombre ASC");
	}

}