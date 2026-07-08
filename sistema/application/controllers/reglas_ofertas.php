<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Reglas_Ofertas extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Regla_Oferta_Model', 'modelo');
  }

  function export($id_empresa = 0, $tabla = 0) {
    if ($id_empresa == 0) { echo gzdeflate("0"); exit(); }
    if ($tabla == 0) $tabla = "reglas_ofertas";
    else if ($tabla == 1) $tabla = "reglas_ofertas_articulos";
    else if ($tabla == 2) $tabla = "reglas_ofertas_sucursales";
    else { echo gzdeflate("0"); exit(); }
    $sql = "SELECT A.* ";
    $sql.= "FROM $tabla A ";
    $sql.= "WHERE A.id_empresa = $id_empresa ";

    $q = $this->db->query($sql);
    if ($q->num_rows() == 0) { echo gzdeflate("0"); exit(); }

    $this->load->helper("import_helper");
    $salida = create_string_to_export($q);
    
    // Enviamos la cadena comprimida para ahorrar ancho de banda
    echo gzdeflate($salida);
  }
	
  function ver() {
    $limit = $this->input->get("limit");
    $offset = $this->input->get("offset");
    $filter = $this->input->get("filter");
    $order_by = $this->input->get("order_by");
    $order = $this->input->get("order");
    if (!empty($order_by) && !empty($order)) $order = $order_by." ".$order;
    else $order = "A.desde DESC";
    $conf = array(
      "filter"=>$filter,
      "limit"=>$limit,
      "offset"=>$offset,
      "order"=>$order,
    );
    $r = $this->modelo->buscar($conf);
    echo json_encode($r);
  }

}