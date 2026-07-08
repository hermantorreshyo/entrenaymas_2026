<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Galerias_Imagenes extends REST_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('Galeria_Imagen_Model', 'modelo');
    }

    function save_file() {
        $this->load->helper("file_helper");
        if (!isset($_FILES['path']) || empty($_FILES['path'])) {
            echo json_encode(array(
                "error"=>1,
                "mensaje"=>"No se ha enviado ningun archivo."
            ));
            return;
        }
        $filename = filename($_FILES["path"]["name"],"-");
        $path = "uploads/images/$filename";
        @move_uploaded_file($_FILES["path"]["tmp_name"],$path);
        echo json_encode(array(
            "path"=>$path,
            "error"=>0,
        ));
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
        unset($array->etiquetas);
    }    
    
    function update($id) {
        
        if ($id == 0) { $this->insert(); return; }
        $this->load->helper("file_helper");
        $array = $this->parse_put();
        
        // Eliminamos todo lo que no se persiste
        $etiquetas = $array->etiquetas;
        $this->remove_properties($array);
        
        // Actualizamos los datos del libro
        $this->modelo->save($array);
        
        // Guardamos las relaciones con las etiquetas (Y se crean en caso de que no exitan)
        $i=1;
        $this->db->query("DELETE FROM galerias_imagenes_etiquetas WHERE id_galeria = $id ");
        foreach($etiquetas as $e) {
            $tag = new stdClass();
            $tag->id_galeria = $id;
            $tag->nombre = $e;
            $this->modelo->save_tag($tag);
        }        
        
        $salida = array(
            "id"=>$id,
            "error"=>0,
        );
        echo json_encode($salida);        
    }
    
    function insert() {
        
        $this->load->helper("file_helper");
    	$array = $this->parse_put();
        
        // Eliminamos todo lo que no se persiste
        $etiquetas = $array->etiquetas;
        $this->remove_properties($array);

        // Insertamos el libro
        $insert_id = $this->modelo->save($array);
        
        // Guardamos las relaciones con las etiquetas (Y se crean en caso de que no exitan)
        $i=1;
        $this->db->query("DELETE FROM galerias_imagenes_etiquetas WHERE id_galeria = $insert_id ");
        foreach($etiquetas as $e) {
            $tag = new stdClass();
            $tag->id_galeria = $insert_id;
            $tag->nombre = $e;
            $this->modelo->save_tag($tag);
        }        
        
        $salida = array(
            "id"=>$insert_id,
            "error"=>0,
        );
        echo json_encode($salida);        
    }
    
    function get($id) {
        // Obtenemos el listado
        if ($id == "index") {
            $sql = "SELECT A.* ";
            $sql.= "FROM galeria_imagenes A ";
            $sql.= "WHERE A.activo = 1 ";
            $sql.= "ORDER BY A.nombre ASC ";
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
        $id_etiqueta = $this->input->get("id_etiqueta");
        if (!empty($order_by) && !empty($order)) $order = $order_by." ".$order;
        else $order = "";
        
        $conf = array(
            "filter"=>$filter,
            "order"=>$order,
            "limit"=>$limit,
            "offset"=>$offset,
            "id_etiqueta"=>$id_etiqueta,
        );
        
        $r = $this->modelo->buscar($conf);
        echo json_encode($r);
    }


    function get_by_nombre() {
        $nombre = $this->input->get("term");
        $sql = "SELECT * ";
        $sql.= "FROM galeria_imagenes ";
        $sql.= "WHERE nombre LIKE '%$nombre%' ";
        $q = $this->db->query($sql);
        $resultado = array();
        foreach($q->result() as $r) {
            $rr = new stdClass();
            $rr->id = $r->id;
            $rr->value = $r->id;
            $rr->label = $r->nombre;
            $rr->info = "";
            $rr->disponibles = $r->disponibles;
            $resultado[] = $rr;
        }
        echo json_encode($resultado);
    }

	
}