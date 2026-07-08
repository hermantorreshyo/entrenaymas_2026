<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Video_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("com_videos","id","clave ASC",0);
	}
    
	function find($filter) {
		return $this->get_all(null,null,$filter);
	}    

}