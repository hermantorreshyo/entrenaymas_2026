<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once("abstract_model.php");

class Chat_Pregunta_Model extends Abstract_Model {
	
	function __construct() {
		parent::__construct("chat_preguntas","id","orden ASC",0);
	}
    
	function find($filter) {
		return $this->get_all(null,null,$filter);
	}   

  // Obtiene las preguntas en un array por tipo
  function get_by_type() {
    $salida = array();
    $q = $this->db->query("SELECT DISTINCT tipo FROM chat_preguntas");
    foreach($q->result() as $t) {
      $sql = "SELECT ";
      $sql.= " pregunta AS text, success_text, fail_text ";
      $sql.= "FROM chat_preguntas WHERE tipo = '$t->tipo' ";
      $sql.= "ORDER BY orden ASC ";
      $qq = $this->db->query($sql);
      foreach($qq->result() as $rr) {
        $rr->text = explode("\n", $rr->text);
        $rr->success_text = explode("\n", $rr->success_text);
        $rr->fail_text = explode("\n", $rr->fail_text);
        $salida[$t->tipo][] = $rr;
      }
      
    }
    return $salida;
  } 

}