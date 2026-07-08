<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Clasificados extends REST_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('Clasificado_Model', 'modelo');
    }
    
    function save_image($dir="",$filename="") {
		$id_empresa = $this->get_empresa();
		$dir = "uploads/$id_empresa/clasificados/";
		$filename = $this->input->post("file");
		echo parent::save_image($dir,$filename);
    }        
    
    function duplicar($id) {
        
        $this->load->helper("fecha_helper");
        $this->load->helper("file_helper");
        
        $clasificado = $this->modelo->get($id);
        if ($clasificado === FALSE) {
            echo json_encode(array(
                "error"=>1,
                "mensaje"=>"No se encuentra el clasificado con ID: $id",
            ));
            return;
        }
        
        $clasificado->id = 0;
        $clasificado->link = ""; // Como el link tiene el ID, se tiene que generar de vuelta
        
        $images = $clasificado->images;
        
        // Acomodamos los datos especificos
        $clasificado->fecha = fecha_mysql($clasificado->fecha);
        
        $this->remove_properties($clasificado);
        $insert_id = $this->modelo->insert($clasificado);
        
        // Actualizamos el link
        $clasificado->link = "clasificado/".filename($clasificado->titulo,"-",0)."-".$insert_id."/";
        $this->db->query("UPDATE clasificados SET link = '$clasificado->link' WHERE id = $insert_id");
        
        // Actualizamos las relaciones
        echo json_encode(array(
            "id"=>$insert_id
        ));
    }
    
    private function remove_properties($array) {
        unset($array->images);
        unset($array->categoria);
        unset($array->usuario);
        unset($array->nuevo);
        unset($array->etiquetas);
        unset($array->atributos);
        unset($array->publicidad_path);
    }    
    
    function update($id) {
        
        if ($id == 0) { $this->insert(); return; }
        $this->load->helper("file_helper");
        $array = $this->parse_put();
        
        $this->load->model("Log_Model");
        
        $id_empresa = parent::get_empresa();
        $array->id_empresa = $id_empresa;        
        
        // Acomodamos las fechas
        $this->load->helper("fecha_helper");
        $array->fecha = fecha_mysql($array->fecha);
		$array->activo_desde = fecha_mysql($array->activo_desde);
		$array->activo_hasta = fecha_mysql($array->activo_hasta);		
        
        // Eliminamos todo lo que no se persiste
        $images = $array->images;
        $atributos = $array->atributos;
        $this->remove_properties($array);
        
        // Actualizamos el link
        $array->link = "clasificado/".filename($array->titulo,"-",0)."-".$id."/";
        
        // Actualizamos los datos del clasificado
        $this->modelo->save($array);
        
        // Actualizamos los valores de los atributos
        $this->db->query("DELETE FROM clasificados_atributos_valores WHERE id_clasificado = $id AND id_empresa = $id_empresa");
        foreach($atributos as $a) {
            $this->db->insert("clasificados_atributos_valores",array(
                "id_empresa"=>$array->id_empresa,
                "id_clasificado"=>$id,
                "id_atributo"=>$a->id_atributo,
                "valor"=>$a->valor,
            ));
        }        
        
        // Guardamos las imagenes
        $this->db->query("DELETE FROM clasificados_images WHERE id_clasificado = $id AND id_empresa = $id_empresa");
        $k=0;
        foreach($images as $im) {
            $this->db->query("INSERT INTO clasificados_images (id_empresa,id_clasificado,path,orden) VALUES($id_empresa,$id,'$im',$k)");
            $k++;
        }
        
        $this->Log_Model->log("ha modificado la clasificado $array->titulo","app/#clasificado/".$id);
        
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
        
        $this->load->model("Log_Model");
        
        // Acomodamos las fechas
        $this->load->helper("fecha_helper");
    	$array->fecha = date("Y-m-d H:i:s");
		$array->activo_desde = fecha_mysql($array->activo_desde);
		$array->activo_hasta = fecha_mysql($array->activo_hasta);
        
        // Eliminamos todo lo que no se persiste
        $images = $array->images;
        $atributos = $array->atributos;
        $this->remove_properties($array);

        // Insertamos el clasificado
        $insert_id = $this->modelo->save($array);
        
        // Actualizamos el link
        $array->link = "clasificado/".filename($array->titulo,"-",0)."-".$insert_id."/";
        $this->db->query("UPDATE clasificados SET link = '$array->link' WHERE id = $insert_id");
        
        // Actualizamos los valores de los atributos
        foreach($atributos as $a) {
            $this->db->insert("clasificados_atributos_valores",array(
                "id_empresa"=>$array->id_empresa,
                "id_clasificado"=>$insert_id,
                "id_atributo"=>$a->id_atributo,
                "valor"=>$a->valor,
            ));
        }        
        
        // Guardamos las imagenes
        $k=0;
        foreach($images as $im) {
            $this->db->query("INSERT INTO clasificados_images (id_empresa,id_clasificado,path,orden) VALUES($id_empresa,$insert_id,'$im',$k)");
            $k++;
        }
        
        $this->Log_Model->log("ha creado una nueva clasificado $array->titulo","app/#clasificado/".$insert_id,"I");
        
        $salida = array(
            "id"=>$insert_id,
            "error"=>0,
        );
        echo json_encode($salida);        
    }
    
    /**
     *  Obtenemos los datos de un clasificado en particular
     */
    function get($id,$id_empresa=0) {
        $id_empresa = ($id_empresa == 0) ? parent::get_empresa() : $id_empresa;
        // Obtenemos el listado
        if ($id == "index") {
            $sql = "SELECT A.*, ";
			$sql.= " DATE_FORMAT(A.activo_desde,'%d/%m/%Y %H:%i') AS activo_desde, ";
			$sql.= " DATE_FORMAT(A.activo_hasta,'%d/%m/%Y %H:%i') AS activo_hasta, ";
            $sql.= " DATE_FORMAT(A.fecha,'%d/%m/%Y %H:%i') AS fecha ";
            $sql.= "FROM clasificados A ";
            $sql.= "WHERE A.activo = 1 AND id_empresa = $id_empresa ";
            $sql.= "ORDER BY A.activo DESC, A.fecha DESC ";
            $q = $this->db->query($sql);
            $result = $q->result();
            echo json_encode(array(
                "results"=>$result,
                "total"=>sizeof($result)
            ));
        } else {
            $clasificado = $this->modelo->get($id,$id_empresa);
            echo json_encode($clasificado);
        }
        
    }
    
    
    /**
     *  Muestra todos los clasificados filtrando segun distintos parametros
     *  El resultado esta paginado
     */
    function ver() {
        
        $filter = $this->input->get("filter");
        $id_categoria = $this->input->get("id_categoria");
        $limit = $this->input->get("limit");
        $offset = $this->input->get("offset");
        $id_empresa = ($this->input->get("id_empresa") !== FALSE) ? $this->input->get("id_empresa") : 0;
        $activo = ($this->input->get("activo") !== FALSE) ? $this->input->get("activo") : -1;
        $order_by = $this->input->get("order_by");
        $order = $this->input->get("order");
        if (!empty($order_by) && !empty($order)) $order = $order_by." ".$order;
        else $order = "";
        
        $conf = array(
            "filter"=>$filter,
            "order"=>$order,
            "limit"=>$limit,
            "activo"=>$activo,
            "offset"=>$offset,
            "id_categoria"=>$id_categoria,
            "id_empresa"=>$id_empresa,
        );
        
        $r = $this->modelo->buscar($conf);
        echo json_encode($r);
    }
    
    /**
     * Esta funcion recibe un comentario del frontend
     */
    function contactar() {
            
      $id_empresa = $this->input->post("id_empresa");
      $id_clasificado = $this->input->post("id_clasificado");
      $tipo = $this->input->post("tipo");
      $email = $this->input->post("email");
      if ($email === FALSE) $email = "";
      $nombre = $this->input->post("nombre");
      if ($nombre === FALSE) $nombre = "Anonimo";
      $codigo = $this->input->post("codigo");
      if ($codigo === FALSE) $codigo = "";
      $es_usuario = $this->input->post("es_usuario");
      if ($es_usuario === FALSE) {
        echo json_encode(array(
          "error"=>1,
          "mensaje"=>"Error en la configuracion. Indique si es un usuario registrado o no."
        ));
        return;
      }
      
      if ($es_usuario == 1) {
        // El sistema de comentarios utilizado necesita usuarios registrados, por lo que hay que relacionarlos
        $this->load->model("Web_User_Model");
        $cliente = $this->Web_User_Model->get_by_email($email,$id_empresa);
        
        // El cliente no existe, debemos crearlo
        if ($cliente === FALSE) {
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
          $id_usuario = $cliente->id;
        }			
      } else {
        $id_usuario = 0; // El sistema de comentarios no necesita usuarios registrados
      }

      if ($tipo == "propiedad") {
        $sql = "UPDATE inm_propiedades SET cantidad_consultas = cantidad_consultas + 1 WHERE id = $id_clasificado ";
        $this->db->query($sql);
      } else if ($tipo == "autos") {
        $sql = "UPDATE veh_autos SET cantidad_consultas = cantidad_consultas + 1 WHERE id = $id_clasificado ";
        $this->db->query($sql);
      } else if ($tipo == "varios") {

      }
      
      echo json_encode(array(
        "mensaje"=>"Muchas gracias por su participacion",
        "error"=>0,
      ));
    }    
    
}