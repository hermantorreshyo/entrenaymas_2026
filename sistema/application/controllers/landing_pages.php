<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Landing_Pages extends REST_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('Landing_Page_Model', 'modelo');
    }
		
	function go_to() {
		$id_landing_page = $this->input->get("id");
		$landing = $this->modelo->get($id_landing_page);
		if ($landing === FALSE) { header("Location: /"); exit(); }
		// Guardamos el evento del click
    $fecha = date("Y-m-d H:i:s");
		$this->db->query("INSERT INTO landing_pages_clicks (id_empresa,id_landing_page,stamp) VALUES ($landing->id_empresa,$landing->id,'$fecha')");
		// Redireccionamos a donde corresponde
		if (!empty($landing->link)) header("Location: ".$landing->link);
		else header("Location: /");
	}
	
    function save_image($dir="",$filename="") {
		$id_empresa = $this->get_empresa();
		$dir = "uploads/$id_empresa/paginas/";
		$filename = $this->input->post("file");
		echo parent::save_image($dir,$filename);
    }    
    
    function duplicar($id) {
        
        $this->load->helper("fecha_helper");
        $this->load->helper("file_helper");
        
        $publicidad = $this->modelo->get($id);
        if ($publicidad === FALSE) {
            echo json_encode(array(
                "error"=>1,
                "mensaje"=>"No se encuentra la publicidad con ID: $id",
            ));
            return;
        }
        
        $images = $publicidad->images;
        $this->remove_properties($publicidad);
        $publicidad->id = 0;
		
        $insert_id = $this->modelo->insert($publicidad);
				
        // Actualizamos el link
        $publicidad->link_landing = "landing/".filename($publicidad->nombre,"-",0)."-".$insert_id."/";
        $this->db->query("UPDATE landing_pages SET link_landing = '$publicidad->link_landing' WHERE id = $insert_id");
        
        // Actualizamos las relaciones
        echo json_encode(array(
            "id"=>$insert_id
        ));
    }
    
    private function remove_properties($array) {
        unset($array->tipo_publicidad);
        unset($array->categoria);
        unset($array->images);        
    }
    
    function update($id) {
        
        if ($id == 0) { $this->insert(); return; }
        $this->load->helper("file_helper");
        $array = $this->parse_put();
        
        $id_empresa = parent::get_empresa();
        $array->id_empresa = $id_empresa;        
        $images = $array->images;
        $this->remove_properties($array);
		$array->link_landing = "landing/".filename($array->nombre,"-",0)."-".$id."/";
        $this->modelo->save($array);
        
        // Guardamos las imagenes
        $this->db->query("DELETE FROM landing_pages_images WHERE id_landing_page = $id AND id_empresa = $id_empresa");
        $k=0;
        foreach($images as $im) {
            $this->db->query("INSERT INTO landing_pages_images (id_empresa,id_landing_page,path,orden) VALUES($id_empresa,$id,'$im',$k)");
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
        $images = $array->images;
        $this->remove_properties($array);
        
        // Insertamos el publicidad
        $insert_id = $this->modelo->save($array);
				
        // Actualizamos el link
        $array->link_landing = "landing/".filename($array->nombre,"-",0)."-".$insert_id."/";
        $this->db->query("UPDATE landing_pages SET link_landing = '$array->link_landing' WHERE id = $insert_id");				
        
        // Guardamos las imagenes
        $k=0;
        foreach($images as $im) {
            $this->db->query("INSERT INTO landing_pages_images (id_empresa,id_landing_page,path,orden) VALUES($id_empresa,$insert_id,'$im',$k)");
            $k++;
        }
        
        $salida = array(
            "id"=>$insert_id,
            "error"=>0,
        );
        echo json_encode($salida);        
    }
    
    /**
     *  Obtenemos los datos de un publicidad en particular
     */
    function get($id) {
        $id_empresa = parent::get_empresa();
        // Obtenemos el listado
        if ($id == "index") {
            $sql = "SELECT A.* ";
            $sql.= "FROM landing_pages A ";
            $sql.= "WHERE A.activo = 1 AND id_empresa = '$id_empresa' ";
            $sql.= "ORDER BY A.nombre ASC ";
            $q = $this->db->query($sql);
            $result = $q->result();
            echo json_encode(array(
                "results"=>$result,
                "total"=>sizeof($result)
            ));
        } else {
            $publicidad = $this->modelo->get($id);
            echo json_encode($publicidad);
        }
        
    }
    
    
    /**
     *  Muestra todos los publicidades filtrando segun distintos parametros
     *  El resultado esta paginado
     */
    function ver() {
				$filter = $this->input->get("filter");
        $limit = $this->input->get("limit");
        $offset = $this->input->get("offset");
        $order_by = $this->input->get("order_by");
        $order = $this->input->get("order");
        if (!empty($order_by) && !empty($order)) $order = $order_by." ".$order;
        else $order = "";
        
        $conf = array(
            "filter"=>$filter,
        );
        $r = $this->modelo->buscar($conf);
        echo json_encode($r);
    }
		
		function impresiones() {
				$filter = $this->input->get("filter");
				$desde = $this->input->get("desde");
				$hasta = $this->input->get("hasta");
				$id_categoria = $this->input->get("id_categoria");
        $limit = $this->input->get("limit");
        $offset = $this->input->get("offset");
        $order_by = $this->input->get("order_by");
        $order = $this->input->get("order");
        if (!empty($order_by) && !empty($order)) $order = $order_by." ".$order;
        else $order = "";
        
        $conf = array(
            "filter"=>$filter,
						"desde"=>$desde,
						"hasta"=>$hasta,
						"id_categoria"=>$id_categoria,
        );
        $r = $this->modelo->impresiones($conf);
        echo json_encode($r);				
		}
    
}
