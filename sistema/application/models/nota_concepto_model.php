<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Nota_Concepto_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("aca_notas_conceptos","id","id DESC",1);
	}
  
}