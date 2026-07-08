<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Tipo_Tarea_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("mant_tipos_tareas","id","nombre ASC");
	}

}