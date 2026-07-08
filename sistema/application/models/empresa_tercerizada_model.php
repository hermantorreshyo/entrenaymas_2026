<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Empresa_Tercerizada_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("mant_empresas_tercerizadas","id","nombre ASC");
	}

}