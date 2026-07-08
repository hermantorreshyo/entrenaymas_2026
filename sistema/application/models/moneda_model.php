<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Moneda_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("com_monedas","id","nombre ASC",0);
	}

	function get_id_moneda_by_signo($signo) {
		if ($signo == "$" || $signo == "ARS") return 1;
		else if ($signo == 'USD' || $signo == 'U$D' || $signo == 'U$S' || strtoupper($signo) == "DOLAR") return 2;
		else if ($signo == 'R$' || strtoupper($signo) == "REAL") return 3;
		else if ($signo == '€' || strtoupper($signo) == "EURO") return 4;
		else return 0;
	}
    
	function find($filter) {
		$this->db->like("nombre",$filter);
		$query = $this->db->get($this->tabla);
		$result = $query->result();
		$this->db->close();
		return $result;
	}        

}