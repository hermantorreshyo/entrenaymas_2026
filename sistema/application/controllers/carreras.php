<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Carreras extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Carrera_Model', 'modelo');
  }
    
  /*
  function update($id) {
    if ($id == 0) { $this->insert(); return; }
    $this->load->helper("file_helper");
    $array = $this->parse_put();
    $id_empresa = parent::get_empresa();
    $array->id_empresa = $id_empresa;
    $this->modelo->save($array);
    $salida = array(
      "id"=>$id,
      "error"=>0,
    );
    echo json_encode($salida);        
  }
    
    function insert() {
        
        $this->load->helper("file_helper");
    	$array = $this->parse_put();
        $id_empresa = parent::get_empresa();
        $array->id_empresa = $id_empresa;
        $repetir = isset($array->repetir) ? $array->repetir : "0";
        $insert_id = $this->modelo->save($array);

        // Si debemos repetir la clase
        if ($repetir == "1") {
            // Una vez por semana
            $array->id_clase_padre = $insert_id;

        } else if ($repetir == "2") {
            // Cada quince dias
            $array->id_clase_padre = $insert_id;

        } else if ($repetir == "3") {
            // Cada tres semanas
            $array->id_clase_padre = $insert_id;

        } else if ($repetir == "4") {
            // Cada un mes
            $array->id_clase_padre = $insert_id;

        }

        $salida = array(
            "id"=>$insert_id,
            "error"=>0,
        );
        echo json_encode($salida);        
    }
  */
    
    /**
     *  Obtenemos los datos de un carrera en particular
     */
    function get($id) {
        $id_empresa = parent::get_empresa();
        // Obtenemos el listado
        if ($id == "index") {
            $sql = "SELECT A.* ";
            $sql.= "FROM aca_carreras A ";
            $sql.= "WHERE id_empresa = $id_empresa ";
            $sql.= "ORDER BY A.nombre ASC ";
            $q = $this->db->query($sql);
            $result = $q->result();
            echo json_encode(array(
                "results"=>$result,
                "total"=>sizeof($result)
            ));
        } else {
            $carrera = $this->modelo->get($id);
            echo json_encode($carrera);
        }
        
    }
    
    
  function ver() {
    $limit = $this->input->get("limit");
    $filter = $this->input->get("filter");
    $offset = $this->input->get("offset");
    $order_by = $this->input->get("order_by");
    $order = $this->input->get("order");
    if (!empty($order_by) && !empty($order)) $order = $order_by." ".$order;
    else $order = "";
    $conf = array(
      "filter"=>$filter,
      "order"=>$order,
      "limit"=>$limit,
      "offset"=>$offset,
    );
    $r = $this->modelo->buscar($conf);
    echo json_encode($r);
  }


    function get_by_nombre() {
        $id_empresa = parent::get_empresa();
        $nombre = $this->input->get("term");
        $sql = "SELECT * ";
        $sql.= "FROM aca_carreras ";
        $sql.= "WHERE nombre LIKE '%$nombre%' ";
        $sql.= "AND id_empresa = $id_empresa ";
        $q = $this->db->query($sql);
        $resultado = array();
        foreach($q->result() as $r) {
            $rr = new stdClass();
            $rr->id = $r->id;
            $rr->value = $r->id;
            $rr->label = $r->nombre;
            $rr->info = "";
            $resultado[] = $rr;
        }
        echo json_encode($resultado);
    }

	
}