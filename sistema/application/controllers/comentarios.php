<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Comentarios extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Comentario_Model', 'modelo');
  }

  function votar() {
    $id_empresa = parent::get_get("id_empresa",0);
    $id_comentario = parent::get_get("id_comentario",0);
    $positivo = parent::get_get("positivo",-1);
    if (!is_numeric($id_empresa) || !is_numeric($id_comentario) || !is_numeric($positivo)) {
      echo json_encode(array("error"=>1));
      return;
    }
    if ($positivo == 1) $this->db->query("UPDATE not_entradas_comentarios SET votos_positivos = votos_positivos + 1 WHERE id = $id_comentario AND id_empresa = $id_empresa");
    else if ($positivo == 0)$this->db->query("UPDATE not_entradas_comentarios SET votos_negativos = votos_negativos + 1 WHERE id = $id_comentario AND id_empresa = $id_empresa");
    echo json_encode(array("error"=>0));
  }
  
  function get($id) {
    $id_empresa = parent::get_empresa();
    // Obtenemos el listado
    if ($id == "index") {
      $sql = "SELECT A.*, ";
      $sql.= " DATE_FORMAT(A.fecha,'%d/%m/%Y %H:%i') AS fecha ";
      $sql.= "FROM not_entradas_comentarios A ";
      $sql.= "WHERE id_empresa = $id_empresa ";
      $sql.= "ORDER BY A.fecha DESC ";
      $q = $this->db->query($sql);
      $result = $q->result();
      echo json_encode(array(
        "results"=>$result,
        "total"=>sizeof($result)
      ));
    } else {
      $entrada = $this->modelo->get($id);
      echo json_encode($entrada);
    }
    
  }
  
  
  /**
   *  Muestra todos los entradas filtrando segun distintos parametros
   *  El resultado esta paginado
   */
  function ver() {
    
    $limit = $this->input->get("limit");
		$filter = $this->input->get("filter");
    $offset = $this->input->get("offset");
    $order_by = $this->input->get("order_by");
    $order = $this->input->get("order");
		$id_usuario = $this->input->get("id_usuario");
		$id_entrada = $this->input->get("id_entrada");
    if (!empty($order_by) && !empty($order)) $order = $order_by." ".$order;
    else $order = "";
    
    $conf = array(
      "filter"=>$filter,
      "order"=>$order,
      "limit"=>$limit,
      "offset"=>$offset,
			"id_usuario"=>$id_usuario,
			"id_entrada"=>$id_entrada,
    );
    
    $r = $this->modelo->buscar($conf);
    echo json_encode($r);
  }
	
}