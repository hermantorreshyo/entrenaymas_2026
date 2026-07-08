<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Web_Users extends REST_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('Web_User_Model', 'modelo');
    }
    
    function save_image($dir="",$filename="") {
		$id_empresa = $this->get_empresa();
		$dir = "uploads/$id_empresa/";
		$filename = $this->input->post("file");
		echo parent::save_image($dir,$filename);
    }
	
    private function remove_properties($array) {
        unset($array->comentarios);
    }    
    
    function update($id) {
        
        if ($id == 0) { $this->insert(); return; }
        $this->load->helper("file_helper");
        $array = $this->parse_put();
        
        $id_empresa = parent::get_empresa();
        $array->id_empresa = $id_empresa;        
        $this->remove_properties($array);
        
        // Actualizamos los datos del entrada
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
        $this->remove_properties($array);
        $insert_id = $this->modelo->save($array);
        $salida = array(
            "id"=>$insert_id,
            "error"=>0,
        );
        echo json_encode($salida);        
    }
    
    /**
     *  Obtenemos los datos de un entrada en particular
     */
    function get($id) {
        $id_empresa = parent::get_empresa();
        // Obtenemos el listado
        if ($id == "index") {
            $sql = "SELECT A.*, ";
            $sql.= " DATE_FORMAT(A.fecha,'%d/%m/%Y %H:%i') AS fecha ";
            $sql.= "FROM not_entradas A ";
            $sql.= "WHERE A.activo = 1 AND id_empresa = $id_empresa ";
            $sql.= "ORDER BY A.titulo ASC ";
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
}