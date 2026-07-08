<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Sorteos extends REST_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('Sorteo_Model', 'modelo');
    }
	
    function save_file() {
        $this->load->helper("file_helper");
        $id_empresa = $this->get_empresa();
        if (!isset($_FILES['path']) || empty($_FILES['path'])) {
            echo json_encode(array(
                "error"=>1,
                "mensaje"=>"No se ha enviado ningun archivo."
            ));
            return;
        }
        $filename = filename($_FILES["path"]["name"],"-");
        $path = "uploads/$id_empresa/publicidades/$filename";
        @move_uploaded_file($_FILES["path"]["tmp_name"],$path);
        echo json_encode(array(
            "path"=>$path,
            "error"=>0,
        ));
    }
	
	
    function duplicar($id) {
        
        $this->load->helper("fecha_helper");
        $this->load->helper("file_helper");
        
        $encuesta = $this->modelo->get($id);
        if ($encuesta === FALSE) {
            echo json_encode(array(
                "error"=>1,
                "mensaje"=>"No se encuentra la encuesta con ID: $id",
            ));
            return;
        }
        
        $this->remove_properties($encuesta);
        $encuesta->id = 0;
		
        $insert_id = $this->modelo->insert($encuesta);

        // Actualizamos el link
        $array->link = "sorteo/".filename($array->titulo,"-",0)."-".$insert_id."/";
        $this->db->query("UPDATE sorteos SET link = '$array->link' WHERE id = $insert_id");
        
        // Actualizamos las relaciones
        echo json_encode(array(
            "id"=>$insert_id
        ));
    }
    
    private function remove_properties($array) {
        unset($array->usuarios);
    }
    
    function update($id) {
        
        if ($id == 0) { $this->insert(); return; }
        $this->load->helper("file_helper");
        $array = $this->parse_put();
        
        $id_empresa = parent::get_empresa();
        $array->id_empresa = $id_empresa;
		
        // Acomodamos las fechas
        $this->load->helper("fecha_helper");
    	//$array->valida_desde = fecha_mysql($array->valida_desde);
		//$array->valida_hasta = fecha_mysql($array->valida_hasta);
        $this->remove_properties($array);
        $array->link = "sorteo/".filename($array->titulo,"-",0)."-".$id."/";
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
		
        // Acomodamos las fechas
        $this->load->helper("fecha_helper");
    	//$array->valida_desde = fecha_mysql($array->valida_desde);
		//$array->valida_hasta = fecha_mysql($array->valida_hasta);
		
        $this->remove_properties($array);
        
        // Insertamos el encuesta
        $insert_id = $this->modelo->save($array);

        // Actualizamos el link
        $array->link = "sorteo/".filename($array->titulo,"-",0)."-".$insert_id."/";
        $this->db->query("UPDATE sorteos SET link = '$array->link' WHERE id = $insert_id");        
        
        $salida = array(
            "id"=>$insert_id,
            "error"=>0,
        );
        echo json_encode($salida);        
    }
    
    /**
     *  Obtenemos los datos de un encuesta en particular
     */
    function get($id) {
        $id_empresa = parent::get_empresa();
        // Obtenemos el listado
        if ($id == "index") {
            $sql = "SELECT A.* ";
            $sql.= "FROM sorteos A ";
            $sql.= "WHERE A.activo = 1 AND id_empresa = '$id_empresa' ";
            $sql.= "ORDER BY A.titulo ASC ";
            $q = $this->db->query($sql);
            $result = $q->result();
            echo json_encode(array(
                "results"=>$result,
                "total"=>sizeof($result)
            ));
        } else {
            $encuesta = $this->modelo->get($id);
            echo json_encode($encuesta);
        }
        
    }
    
    
    /**
     *  Muestra todos los sorteos filtrando segun distintos parametros
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
	
	
	
	/**
	 * Esta funcion recibe un voto del frontend
	 */
	function participar() {
		
		$id_empresa = $this->input->post("id_empresa");
		if ($id_empresa === FALSE) $this->return_message(1,"ERROR: No se selecciono ninguna empresa.");
		$id_sorteo = $this->input->post("id_sorteo");
		if ($id_sorteo === FALSE) $this->return_message(1,"ERROR: No se selecciono ningun sorteo.");
		$email = $this->input->post("email");
		if ($email === FALSE) $email = "";
		$nombre = $this->input->post("nombre");
		if ($nombre === FALSE) $nombre = "Anonimo";
		$codigo = $this->input->post("codigo");
		if ($codigo === FALSE) $codigo = "";
		
		$encuesta = $this->modelo->get($id_sorteo,$id_empresa);
		if ($encuesta === FALSE) $this->return_message(1,"ERROR: No se selecciono ninguna encuesta.");
		
		// El sistema de comentarios utilizado necesita usuarios registrados, por lo que hay que relacionarlos
		$this->load->model("Web_User_Model");
		$usuario = $this->Web_User_Model->get_by_email($email,$id_empresa);
		
		// El usuario no existe, debemos crearlo
		if ($usuario === FALSE) {
			$id_usuario = $this->Web_User_Model->insert(array(
				"id_empresa"=>$id_empresa,
				"email"=>$email,
				"codigo"=>$codigo,
				"nombre"=>$nombre,
				"activo"=>1,
				"path"=>((!empty($codigo)) ? "http://graph.facebook.com/".$codigo."/picture" : ""),
				"fecha_inicial"=>date("Y-m-d"),
			));
		} else {
			$id_usuario = $usuario->id;

            if ($usuario->activo == 0) {
                $this->return_message(1,"Su usuario se encuentra inhabilitado para participar. Ante cualquier inquietud comuniquese con el administrador del sitio. Muchas gracias.");
                return;                    
            }
			
			// Controlamos si el usuario ya voto en la misma encuesta
			if ($this->modelo->participo($id_usuario,$id_sorteo,$id_empresa) === TRUE) {
				$this->return_message(1,"Ya estas participando del sorteo. Muchas gracias!");
			}
			
		}
		
		// Guardamos
    $f_tar = date("Y-m-d H:i:s");
		$sql = "INSERT INTO sorteos_usuarios (";
		$sql.= " id_empresa,id_sorteo,id_usuario,fecha ";
		$sql.= ") VALUES(";
		$sql.= " '$id_empresa','$id_sorteo','$id_usuario','$f_tar' ";
		$sql.= ")";
		$this->db->query($sql);
		
		$this->return_message(0,"Muchas gracias por su participacion!");
	}
	
	private function return_message($error = 1,$mensaje = "") {
		echo json_encode(array(
			"error"=>$error,
			"mensaje"=>$mensaje
		));
		exit();
	}
}
