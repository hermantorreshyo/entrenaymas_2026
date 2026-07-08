<?php
class Language_Model {
	
  private $id_empresa = 0;
  private $conx = null;	
	private $disponibles = array("es","en","pt");
	private $defecto = "es";

  function __construct($id_empresa,$conx) {
    $this->id_empresa = $id_empresa;
    $this->conx = $conx;
    // TODO: Consultamos por los idiomas disponibles para esa empresa
  }
	
	function get_language() {
		
    if (session_status() == PHP_SESSION_NONE) {
		  @session_start();
    }

		// Si el idioma ya esta fijado, lo devolvemos	
		if (isset($_SESSION["language"])) { 
			return $_SESSION["language"]; 
		}
		
		if (isset($_SERVER["HTTP_ACCEPT_LANGUAGE"]) && $_SERVER['HTTP_ACCEPT_LANGUAGE'] != ''){ 
			$idiomas = explode(",", $_SERVER['HTTP_ACCEPT_LANGUAGE']);
			for ($i=0; $i<count($idiomas); $i++){
				if (!isset($_SESSION["language"])){
					
					foreach($this->disponibles as $lang) {
						$idioma = substr($idiomas[$i], 0, 2);
						if ($lang == $idioma) {
							$this->set_language($lang);
              $this->set_currency_default_lang($lang);
							return $lang;
						}
					}
				}
			}
		}
		
		// Si aún no hemos encontrado ningún idioma que nos convenga, mostramos la web en el idioma por defecto
		if (!isset($_SESSION["language"])){
			$this->set_language($this->defecto);
		}
		return $_SESSION["language"];
	}

  function set_currency_default_lang($lang) {
    if ($lang == "en") $this->set_currency("USD");
    else if ($lang == "pt") $this->set_currency("USD");
    else $this->set_currency("ARS");
  }
	
	function set_language($lang) {
		$_SESSION["language"] = $lang;
	}	

  function get_currency() {
    
    if (session_status() == PHP_SESSION_NONE) {
      @session_start();
    }

    // Si el idioma ya esta fijado, lo devolvemos 
    if (isset($_SESSION["currency"])) { 
      return $_SESSION["currency"]; 
    } else {
      $this->set_currency("ARS");
    }
    return $_SESSION["currency"]; 
  }

  function set_currency($cur) {
    $_SESSION["currency"] = $cur;
  } 
	
}
?>