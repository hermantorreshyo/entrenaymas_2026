<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Objetos extends REST_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('Objeto_Model', 'modelo');
    }
		
    function save_image($dir="",$filename="") {
		$id_empresa = $this->get_empresa();
		$dir = "uploads/$id_empresa/clasificados/";
		$filename = $this->input->post("file");
        $res = parent::save_image($dir,$filename);

        if ($this->input->post("thumbnail_width") !== FALSE) {
            $resp = json_decode($res);
            $filename = str_replace($dir, "", $resp->path);
            $thumbnail_width = $this->input->post("thumbnail_width");
            $thumbnail_height = $this->input->post("thumbnail_height");
            parent::thumbnails(array(
                "dir"=>$dir,
                "preffix"=>"thumb_",
                "filename"=>$filename,
                "thumbnail_width"=>$thumbnail_width,
                "thumbnail_height"=>$thumbnail_height,                
            ));
        }        
        echo $res;
    }
    
    function duplicar($id) {
        
        $this->load->helper("fecha_helper");
        $this->load->helper("file_helper");
        
        $objeto = $this->modelo->get($id);
        if ($objeto === FALSE) {
            echo json_encode(array(
                "error"=>1,
                "mensaje"=>"No se encuentra la propiedad con ID: $id",
            ));
            return;
        }
        
        $images = $objeto->images;
        //$rubros_relacionados = $objeto->rubros_relacionados;
        $this->remove_properties($objeto);
        
        $objeto->id = 0;
        $insert_id = $this->modelo->insert($objeto);
        
        // Actualizamos el link
        $objeto->link = "clasificado/objetos/".filename($objeto->nombre,"-",0)."-".$insert_id."/";
        $this->db->query("UPDATE clasif_objetos SET link = '$objeto->link' WHERE id = $insert_id");
        
        echo json_encode(array(
            "id"=>$insert_id
        ));
    }
    
    private function remove_properties($array) {
        unset($array->tipo);
        unset($array->localidad);
        unset($array->provincia);
        unset($array->cliente);
        unset($array->images);        
    }
    
    function update($id) {
        
        if ($id == 0) { $this->insert(); return; }
        $this->load->helper("file_helper");
        $array = $this->parse_put();
        
        $id_empresa = parent::get_empresa();
        $array->id_empresa = $id_empresa;        
        
        // Acomodamos las fechas
        $this->load->helper("fecha_helper");
        
        // Eliminamos todo lo que no se persiste
        $images = $array->images;
        $this->remove_properties($array);

        // Actualizamos el link
        $array->link = "clasificado/objetos/".filename($array->nombre,"-",0)."-".$id."/";

        // Actualizamos los datos del propiedad
        $this->modelo->save($array);
        
        // Guardamos las imagenes
        $this->db->query("DELETE FROM clasif_objetos_images WHERE id_objeto = $id AND id_empresa = $id_empresa");
        $k=0;
        foreach($images as $im) {
            $this->db->query("INSERT INTO clasif_objetos_images (id_empresa,id_objeto,path,orden) VALUES($id_empresa,$id,'$im',$k)");
            $k++;
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
        
        $id_empresa = parent::get_empresa();
        $array->id_empresa = $id_empresa;
        
        // Acomodamos las fechas
        $this->load->helper("fecha_helper");
        
        // Eliminamos todo lo que no se persiste
        $images = $array->images;
        $this->remove_properties($array);
        
        $array->fecha = date("Y-m-d H:i:s");
        
        // Insertamos el propiedad
        $insert_id = $this->modelo->save($array);
        
        // Actualizamos el link
        $array->link = "clasificado/objetos/".filename($array->nombre,"-",0)."-".$insert_id."/";
        $this->db->query("UPDATE clasif_objetos SET link = '$array->link' WHERE id = $insert_id");
        
        // Guardamos las imagenes
        $k=0;
        foreach($images as $im) {
            $this->db->query("INSERT INTO clasif_objetos_images (id_empresa,id_objeto,path,orden) VALUES($id_empresa,$insert_id,'$im',$k)");
            $k++;
        }
        
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
			$resultado = $this->modelo->buscar();
            echo json_encode($resultado);
        } else {
            $objeto = $this->modelo->get($id);
            echo json_encode($objeto);
        }
        
    }
    
    
    /**
     *  Muestra todos los propiedades filtrando segun distintos parametros
     *  El resultado esta paginado
     */
    function ver() {
        
        $limit = $this->input->get("limit");
        $offset = $this->input->get("offset");
        $filter = $this->input->get("filter");
        $order_by = $this->input->get("order_by");
        $order = $this->input->get("order");
        $id_usuario = $this->input->get("id_usuario");
        if (!empty($order_by) && !empty($order)) $order = $order_by." ".$order;
        else $order = "";
        
        $conf = array(
            "filter"=>$filter,
            "id_usuario"=>$id_usuario,
        );
        $r = $this->modelo->buscar($conf);
        echo json_encode($r);
    }
    
}
