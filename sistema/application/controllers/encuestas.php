<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Encuestas extends REST_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('Encuesta_Model', 'modelo');
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
        
        $opciones = $encuesta->opciones;
        $this->remove_properties($encuesta);
        $encuesta->id = 0;
		
        $insert_id = $this->modelo->insert($encuesta);
        
        // Actualizamos las relaciones
        echo json_encode(array(
            "id"=>$insert_id
        ));
    }
    
    private function remove_properties($array) {
        unset($array->opciones);        
    }
    
    function update($id) {
        
        if ($id == 0) { $this->insert(); return; }
        $this->load->helper("file_helper");
        $array = $this->parse_put();
        
        $id_empresa = parent::get_empresa();
        $array->id_empresa = $id_empresa;
		
        // Acomodamos las fechas
        $this->load->helper("fecha_helper");
    	$array->valida_desde = fecha_mysql($array->valida_desde);
		$array->valida_hasta = fecha_mysql($array->valida_hasta);
		
        $opciones = $array->opciones;
        $this->remove_properties($array);
        $this->modelo->save($array);
        
        // Guardamos las opciones
        $k=0;
		$ids = array();
        foreach($opciones as $im) {
			$q = $this->db->query("SELECT * FROM encuestas_opciones WHERE id = $im->id AND id_empresa = $id_empresa AND id_encuesta = $id");
			if ($q->num_rows()>0) {
				// Actualizamos
				$this->db->query("UPDATE encuestas_opciones SET nombre = '$im->nombre' WHERE id = $im->id AND id_empresa = $id_empresa AND id_encuesta = $id ");
			} else {
				// Insertamos
				$this->db->query("INSERT INTO encuestas_opciones (id_empresa,id_encuesta,nombre,orden) VALUES($id_empresa,$id,'$im->nombre',$k)");
				$im->id = $this->db->insert_id();
			}
			$ids[] = $im->id;
            $k++;
        }
		// Eliminamos aquellas que no estan en el array
		$in_ids = implode(",",$ids);
		$this->db->query("DELETE FROM encuestas_opciones WHERE id_encuesta = $id AND id_empresa = $id_empresa AND id NOT IN ($in_ids)");
        
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
    	$array->valida_desde = fecha_mysql($array->valida_desde);
		$array->valida_hasta = fecha_mysql($array->valida_hasta);
		
        $opciones = $array->opciones;
        $this->remove_properties($array);
        
        // Insertamos el encuesta
        $insert_id = $this->modelo->save($array);
        
        // Guardamos las opciones
        $k=0;
        foreach($opciones as $im) {
            $this->db->query("INSERT INTO encuestas_opciones (id_empresa,id_encuesta,nombre,orden) VALUES($id_empresa,$insert_id,'$im->nombre',$k)");
            $k++;
        }
        
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
            $sql.= "FROM encuestas A ";
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
     *  Muestra todos los encuestas filtrando segun distintos parametros
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
	function votar() {
		
		$id_empresa = $this->input->post("id_empresa");
		if ($id_empresa === FALSE) $this->return_message(1,"ERROR: No se selecciono ninguna empresa.");
		$id_encuesta = $this->input->post("id_encuesta");
		if ($id_encuesta === FALSE) $this->return_message(1,"ERROR: No se selecciono ninguna encuesta.");
		$id_opcion = $this->input->post("id_opcion");
		if ($id_opcion === FALSE) $this->return_message(1,"ERROR: No se selecciono ninguna opcion.");
		$email = $this->input->post("email");
		if ($email === FALSE) $email = "";
		$nombre = $this->input->post("nombre");
		if ($nombre === FALSE) $nombre = "Anonimo";
		$codigo = $this->input->post("codigo");
		if ($codigo === FALSE) $codigo = "";
		
		$encuesta = $this->modelo->get($id_encuesta,$id_empresa);
		if ($encuesta === FALSE) $this->return_message(1,"ERROR: No se selecciono ninguna encuesta.");
		
		if ($encuesta->forma_participacion == "facebook") {
			// El sistema de comentarios utilizado necesita usuarios registrados, por lo que hay que relacionarlos
			$this->load->model("Web_User_Model");
			$usuario = $this->Web_User_Model->get_by_codigo($codigo,$id_empresa);
			
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
				
				// Controlamos si el usuario ya voto en la misma encuesta
				if ($this->modelo->participo($id_usuario,$id_encuesta,$id_empresa) === TRUE) {
					$this->return_message(1,"Su voto ya ha sido registrado, muchas gracias!");
				}
				
			}
		} else {
			$id_usuario = 0; // El sistema de comentarios no necesita usuarios registrados
		}
		
		// Guardamos
    $fecha = date("Y-m-d H:i:s");
		$sql = "INSERT INTO encuestas_opciones_usuarios (";
		$sql.= " id_empresa,id_encuesta,id_opcion,id_usuario,fecha ";
		$sql.= ") VALUES(";
		$sql.= " '$id_empresa','$id_encuesta','$id_opcion','$id_usuario','$fecha' ";
		$sql.= ")";
		$this->db->query($sql);
		
		// Dependiendo de como este configurada la encuesta, mostramos o no los resultados
		$encuesta = $this->modelo->get($id_encuesta,$id_empresa);
    if ($encuesta === FALSE) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"No existe la encuesta",
      ));
    } else {
      $encuesta->error = 0;
      echo json_encode($encuesta);
    }
	}
	
	private function return_message($error = 1,$mensaje = "") {
		echo json_encode(array(
			"error"=>$error,
			"mensaje"=>$mensaje
		));
		exit();
	}
}
