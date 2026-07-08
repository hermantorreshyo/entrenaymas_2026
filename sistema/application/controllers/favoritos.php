<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Favoritos extends CI_Controller {

    function __construct() {
        parent::__construct();
    }
	
	function agregar() {
		$id = $this->input->get("id");
		$url = $this->input->get("url");
		if (!isset($_SESSION["favoritos"])) $_SESSION["favoritos"] = "";
		$_SESSION["favoritos"] = $_SESSION["favoritos"].$id.",";
		if (!empty($url)) {
			header("Location: ".urldecode($url));    
		} else {
			header("Location: ".$_SERVER["HTTP_REFERER"]);    
		}		
	}
	
	function eliminar() {
		$id = $this->input->get("id");

		// Eliminamos todos
		if ($id == "E") {
			$_SESSION["favoritos"] = "";
			header("Location: /");
			exit();
			
		// Eliminamos uno en particular
		} else if ($id !== FALSE) {
			$fav = array();
			$favoritos = explode(",",$_SESSION["favoritos"]);
			foreach($favoritos as $f) {
				if ($f !== $id) $fav[] = $f;
			}
			$_SESSION["favoritos"] = implode(",",$fav);
			header("Location: ".$_SERVER["HTTP_REFERER"]);
		}		
	}
	
}