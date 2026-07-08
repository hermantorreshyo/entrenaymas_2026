<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Ambientaciones extends REST_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('Ambientacion_Model', 'modelo');
    }

    function save_image($dir="",$filename="") {
		$id_empresa = $this->get_empresa();
		$dir = "uploads/$id_empresa/articulos/";
		$filename = $this->input->post("file");
		echo parent::save_image($dir,$filename);
    }    

    function duplicar($id) {
        
        $this->load->helper("fecha_helper");
        $this->load->helper("file_helper");
        
        $articulo = $this->modelo->get($id);
        if ($articulo === FALSE) {
            echo json_encode(array(
                "error"=>1,
                "mensaje"=>"No se encuentra el articulo con ID: $id",
            ));
            return;
        }
        
        $articulo->id = 0;
        $articulos = $articulo->articulos;
        $images = $articulo->images;
        
        $this->remove_properties($articulo);
        $insert_id = $this->modelo->insert($articulo);

        // Actualizamos el link
        $articulo->link = "ambientacion/".filename($articulo->nombre,"-",0)."-".$insert_id."/";
        $this->db->query("UPDATE ambientaciones SET link = '$articulo->link' WHERE id = $insert_id");
        
        // Actualizamos los productos articulos
        $i=1;
        foreach($articulos as $p) {
            $this->db->insert("ambientaciones_articulos",array(
                "id_ambientacion"=>$insert_id,
                "id_articulo"=>$p->id,
                "id_empresa"=>$articulo->id_empresa,
                "orden"=>$i,
            ));
            $i++;
        }
        
        // Actualizamos las relaciones
        echo json_encode(array(
            "id"=>$insert_id
        ));
    }
    
    private function remove_properties($array) {
        unset($array->variantes);
        unset($array->images);
        unset($array->proveedores);
        unset($array->marca);
        unset($array->promocion);
        unset($array->promocion_path);
        unset($array->rubro);
        unset($array->subrubro);
        unset($array->etiquetas);
        unset($array->codigo_proveedor);        
        unset($array->articulos);
        unset($array->rubros_articulos);
    }    
    
    function update($id) {
        
        if ($id == 0) { $this->insert(); return; }
        $this->load->helper("file_helper");
        $array = $this->parse_put();
        
        $id_empresa = parent::get_empresa();
        $array->id_empresa = $id_empresa;        
        
        // Eliminamos todo lo que no se persiste
        $images = $array->images;
        $articulos = $array->articulos;
        $this->remove_properties($array);

        // Actualizamos el link
        $array->link = "ambientacion/".filename($array->nombre,"-",0)."-".$id."/";
        
        // Actualizamos los datos del articulo
        $this->modelo->save($array);

        // Eliminamos las relaciones entre ambientaciones
        $this->db->query("DELETE FROM ambientaciones_articulos WHERE id_ambientacion = $id ");
        
        // Actualizamos los productos articulos
        $i=1;
        foreach($articulos as $p) {
            $this->db->insert("ambientaciones_articulos",array(
                "id_ambientacion"=>$id,
                "id_articulo"=>$p->id,
                "orden"=>$i,
                "id_empresa"=>$array->id_empresa,
            ));
            $i++;
        }
        
        // Guardamos las imagenes
        $this->db->query("DELETE FROM ambientaciones_images WHERE id_ambientacion = $id AND id_empresa = $id_empresa");
        $k=0;
        foreach($images as $im) {
            $this->db->query("INSERT INTO ambientaciones_images (id_empresa,id_ambientacion,path,orden) VALUES($id_empresa,$id,'$im',$k)");
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
        
        // Eliminamos todo lo que no se persiste
        $images = $array->images;
        $articulos = $array->articulos;
        $this->remove_properties($array);

        // Insertamos el articulo
        $insert_id = $this->modelo->save($array);

        // Actualizamos el link
        $array->link = "ambientacion/".filename($array->nombre,"-",0)."-".$insert_id."/";
        $this->db->query("UPDATE ambientaciones SET link = '$array->link' WHERE id = $insert_id");
        
        // Actualizamos los productos articulos
        $i=1;
        foreach($articulos as $p) {
            $this->db->insert("ambientaciones_articulos",array(
                "id_ambientacion"=>$insert_id,
                "id_articulo"=>$p->id,
                "orden"=>$i,
                "id_empresa"=>$array->id_empresa,
            ));
            $i++;
        }
        
        // Guardamos las imagenes
        $k=0;
        foreach($images as $im) {
            $this->db->query("INSERT INTO ambientaciones_images (id_empresa,id_ambientacion,path,orden) VALUES($id_empresa,$insert_id,'$im',$k)");
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
            $sql = "SELECT A.* ";
            $sql.= "FROM ambientaciones A ";
            $sql.= "WHERE A.activo = 1 AND id_empresa = $id_empresa ";
            $sql.= "ORDER BY A.nombre ASC ";
            $q = $this->db->query($sql);
            $result = $q->result();
            echo json_encode(array(
                "results"=>$result,
                "total"=>sizeof($result)
            ));
        } else {
            $articulo = $this->modelo->get($id);
            echo json_encode($articulo);
        }
        
    }
    
    
    /**
     *  Muestra todos los ambientaciones filtrando segun distintos parametros
     *  El resultado esta paginado
     */
    function ver() {
        
        $filter = ($this->input->get("texto") === FALSE) ? "" : $this->input->get("texto");
        $limit = $this->input->get("limit");
        $offset = $this->input->get("offset");
        $order_by = $this->input->get("order_by");
        $order = $this->input->get("order");
        if (!empty($order_by) && !empty($order)) $order = $order_by." ".$order;
        else $order = "";
        
        $r = $this->modelo->buscar(array(
            "filter"=>$filter,
            "limit"=>$limit,
            "offset"=>$offset,
            "order"=>$order,
        ));
        echo json_encode($r);
    }
    
}