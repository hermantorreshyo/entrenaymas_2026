<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Tipo_Orden_Trabajo_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("mant_tipos_ordenes_trabajo","id","nombre ASC");
	}

}