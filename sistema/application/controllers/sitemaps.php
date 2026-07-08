<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Sitemaps extends REST_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('Sitemap_Model', 'modelo');
    }

    function duplicar($id) {
        
        $this->load->helper("file_helper");
        
        $libro = $this->modelo->get($id);
        if ($libro === FALSE) {
            echo json_encode(array(
                "error"=>1,
                "mensaje"=>"No se encuentra el libro con ID: $id",
            ));
            return;
        }
        
        $libro->id = 0;
        
        $this->remove_properties($libro);
        $insert_id = $this->modelo->insert($libro);
        
        // Actualizamos las relaciones
        echo json_encode(array(
            "id"=>$insert_id
        ));
    }
    
    private function remove_properties($array) {
    }    
    
    function update($id) {
        
        if ($id == 0) { $this->insert(); return; }
        $this->load->helper("file_helper");
        $this->load->helper("fecha_helper");
        $array = $this->parse_put();
        
        $id_empresa = parent::get_empresa();
        $array->id_empresa = $id_empresa;        
        $array->lastmod = fecha_mysql($array->lastmod);
        
        // Eliminamos todo lo que no se persiste
        $this->remove_properties($array);
        
        // Actualizamos los datos del libro
        $this->modelo->save($array);
        
        $salida = array(
            "id"=>$id,
            "error"=>0,
        );
        echo json_encode($salida);        
    }
    
    function insert() {
        
        $this->load->helper("file_helper");
        $this->load->helper("fecha_helper");
    	$array = $this->parse_put();
        
        $id_empresa = parent::get_empresa();
        $array->id_empresa = $id_empresa;
        $array->lastmod = fecha_mysql($array->lastmod);
        
        // Eliminamos todo lo que no se persiste
        $this->remove_properties($array);

        // Insertamos el libro
        $insert_id = $this->modelo->save($array);
        
        $salida = array(
            "id"=>$insert_id,
            "error"=>0,
        );
        echo json_encode($salida);        
    }
    
    function get($id) {
        $id_empresa = parent::get_empresa();
        // Obtenemos el listado
        if ($id == "index") {
            $sql = "SELECT A.* ";
            $sql.= "FROM web_sitemap A ";
            $sql.= "WHERE A.activo = 1 AND id_empresa = $id_empresa ";
            $sql.= "ORDER BY A.id ASC ";
            $q = $this->db->query($sql);
            $result = $q->result();
            echo json_encode(array(
                "results"=>$result,
                "total"=>sizeof($result)
            ));
        } else {
            $libro = $this->modelo->get($id);
            echo json_encode($libro);
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

}