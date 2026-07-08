<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Not_Videos extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Not_Video_Model', 'modelo');
  }

  function acomodar_link() {
    $this->load->helper("file_helper");
    $q = $this->db->query("SELECT * FROM not_videos");
    foreach($q->result() as $row) {
      $row->titulo = trim($row->titulo);
      $row->titulo = str_replace("/", "-", $row->titulo);
      $link = "videos/".filename($row->titulo,"-",0)."/";
      $this->db->query("UPDATE not_videos SET link = '$link' WHERE id = $row->id AND id_empresa = $row->id_empresa ");
    }
    echo "TERMINO";
  }


  function notificar_email($id) {

    $id_empresa = parent::get_empresa();
    $entrada = $this->modelo->get($id);
    if ($entrada === FALSE) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"Entrada no valida.",
      ));
      exit();
    }

    $this->load->model("Cliente_Model");
    $cliente = $this->Cliente_Model->get($entrada->id_cliente);
    if ($cliente === FALSE) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"La entrada no tiene cliente asignado.",
      ));
      exit();      
    }

    $this->load->model("Empresa_Model");
    $empresa = $this->Empresa_Model->get($id_empresa);

    $this->load->model("Email_Template_Model");
    $temp = $this->Email_Template_Model->get_by_key("email-videos",$id_empresa);
    if (empty($temp)) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"No se puede encontrar el template con ID: 'email-videos'.",
      ));
      exit();      
    }
    $body = $temp->texto;
    $body = str_replace("{{nombre}}", $cliente->nombre, $body);
    $body = str_replace("{{titulo}}", $entrada->titulo, $body);
    $body = str_replace("{{id_cliente}}", $cliente->id, $body);

    $base_link = "";
    if (sizeof($empresa->dominios)>0) {
      $dominio = $empresa->dominios[0];
    } else {
      $dominio = $empresa->dominio_varcreative;
    }
    $link = str_replace("//", "/", $dominio."/"."web/company/?id=".$cliente->id."&tab=video&id_video=".$entrada->id);
    $body = str_replace("{{link}}", "http://".$link, $body);

    require APPPATH.'libraries/Mandrill/Mandrill.php';
    mandrill_send(array(
      "to"=>$cliente->email,
      "from"=>"no-reply@varcreative.com",
      "from_name"=>$empresa->nombre,
      "subject"=>$temp->nombre,
      "body"=>$body,
      "reply_to"=>$empresa->email,
      "bcc"=>(isset($empresa->config["bcc_email"]) ? $empresa->config["bcc_email"] : ""),
    ));

    // Marcamos que la notificacion fue exitosa
    if ($id_empresa == 256 || $id_empresa == 257) {
      $this->db->query("UPDATE not_videos SET custom_1 = '1' WHERE id_empresa = $id_empresa AND id = $entrada->id ");
    }

    echo json_encode(array(
      "error"=>0,
      "mensaje"=>"The emails has been sent successfully",
    ));
  }


  function duplicar($id) {
      
    $this->load->helper("fecha_helper");
    $this->load->helper("file_helper");
    
    $video = $this->modelo->get($id);
    if ($video === FALSE) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"No se encuentra el entrada con ID: $id",
      ));
      return;
    }
    
    $video->id = 0;
    $video->link = ""; // Como el link tiene el ID, se tiene que generar de vuelta
    $video->fecha = fecha_mysql($video->fecha);
    $video->link_youtube = str_replace("https://www.youtube.com/watch?v=", "", $video->link_youtube);
    $insert_id = $this->modelo->insert($video);
    
    // Actualizamos el link
    if ($video->id_categoria == 550) {
      $base_link = "rrl/";
      $video->link = $base_link.filename($video->titulo,"-",0)."/"; 
    } else {
      $base_link = "videos/";
      $video->link = $base_link.filename($video->titulo,"-",0)."-".$insert_id."/";      
    }
    $this->db->query("UPDATE not_videos SET link = '$video->link' WHERE id = $insert_id AND id_empresa = $video->id_empresa ");
    
    // Actualizamos las relaciones
    echo json_encode(array(
      "id"=>$insert_id
    ));
  }
    
  function update($id) {
      
    if ($id == 0) { $this->insert(); return; }
    $this->load->helper("file_helper");
    $array = $this->parse_put();

    // Obtenemos la entrada actual antes de guardarla
    $anterior = $this->modelo->get($id);
    
    $id_empresa = parent::get_empresa();
    $array->id_empresa = $id_empresa;        
    $array->link_youtube = str_replace("https://www.youtube.com/watch?v=", "", $array->link_youtube);
    
    // Acomodamos las fechas
    $this->load->helper("fecha_helper");
    $array->fecha = fecha_mysql($array->fecha);
    
    // Actualizamos el link en caso de que estemos publicando
    if ($anterior->activo == 0 && $array->activo == 1) {
      if ($array->id_categoria == 550) {
        $base_link = "rrl/";
        $array->link = $base_link.filename($array->titulo,"-",0)."/";
      } else {
        $base_link = "videos/";
        $array->link = $base_link.filename($array->titulo,"-",0)."-".$insert_id."/";      
      }
    }

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
    
    // Acomodamos las fechas
    $this->load->helper("fecha_helper");
    $array->fecha = fecha_mysql($array->fecha);
    $array->link_youtube = str_replace("https://www.youtube.com/watch?v=", "", $array->link_youtube);

    // Insertamos el entrada
    $insert_id = $this->modelo->save($array);
    
    // Actualizamos el link
    if ($array->id_categoria == 550) {
      $base_link = "rrl/";
      $array->link = $base_link.filename($array->titulo,"-",0)."/";
    } else {
      $base_link = "videos/";
      $array->link = $base_link.filename($array->titulo,"-",0)."-".$insert_id."/";      
    }
    $this->db->query("UPDATE not_videos SET link = '$array->link' WHERE id = $insert_id AND id_empresa = $id_empresa");
    
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
      $sql.= " IF(A.link_youtube != '',CONCAT('https://www.youtube.com/watch?v=',A.link_youtube),'') AS link_youtube, ";
      $sql.= " DATE_FORMAT(A.fecha,'%d/%m/%Y %H:%i') AS fecha ";
      $sql.= "FROM not_videos A ";
      $sql.= "WHERE A.activo = 1 AND id_empresa = $id_empresa ";
      $sql.= "ORDER BY A.titulo ASC ";
      $q = $this->db->query($sql);
      $result = $q->result();
      echo json_encode(array(
        "results"=>$result,
        "total"=>sizeof($result)
      ));
    } else {
      $video = $this->modelo->get($id);
      echo json_encode($video);
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
    $id_categoria = $this->input->get("id_categoria");
    $id_empresa = ($this->input->get("id_empresa") !== FALSE) ? $this->input->get("id_empresa") : parent::get_empresa();
    $id_usuario = ($this->input->get("id_usuario") !== FALSE) ? $this->input->get("id_usuario") : 0;
    if (!empty($order_by) && !empty($order)) $order = $order_by." ".$order;
    else $order = "A.fecha DESC";

    // Si term esta definido, es porque estamos buscando de la barra
    $term = $this->input->get("term");
    if ($term !== FALSE) $filter = $term;        
        
    $conf = array(
      "filter"=>$filter,
      "order"=>$order,
      "limit"=>$limit,
      "offset"=>$offset,
      "id_categoria"=>$id_categoria,
      "id_usuario"=>$id_usuario,
      "id_empresa"=>$id_empresa,
    );
    
    $r = $this->modelo->buscar($conf);

    // Dependiendo desde donde se hace la busqueda, devolvemos uno u otro formato
    if ($term === FALSE) {
      echo json_encode($r);
    } else {
      $salida = array();
      foreach($r["results"] as $row) {
        $rr = array();
        $rr["id"] = $row->id;
        $rr["value"] = $row->id;
        $rr["label"] = $row->titulo;
        $rr["subtitulo"] = $row->subtitulo;
        $rr["path"] = $row->path;
        $salida[] = $rr;
      }
      echo json_encode($salida);
    }
  }
  
  function borrar($id) {
    $this->modelo->delete($id);
    echo json_encode(array("error"=>0));
  }

}