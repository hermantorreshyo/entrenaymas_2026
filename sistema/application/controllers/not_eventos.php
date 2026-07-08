<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Not_Eventos extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Not_Evento_Model', 'modelo');
  }

  function acomodar_link() {
    $this->load->helper("file_helper");
    $q = $this->db->query("SELECT * FROM not_eventos");
    foreach($q->result() as $row) {
      $row->titulo = trim($row->titulo);
      $row->titulo = str_replace("/", "-", $row->titulo);
      $link = "events/".filename($row->titulo,"-",0)."/";
      $this->db->query("UPDATE not_eventos SET link = '$link' WHERE id = $row->id AND id_empresa = $row->id_empresa ");
    }
    echo "TERMINO";
  }  

  function upload_images($id_empresa = 0) {
    $id_empresa = (empty($id_empresa)) ? $this->get_empresa() : $id_empresa;
    return parent::upload_images(array(
      "id_empresa"=>$id_empresa,
      "clave_width"=>"not_evento_galeria_image_width",
      "clave_height"=>"not_evento_galeria_image_height",
      "upload_dir"=>"uploads/$id_empresa/entradas/",
      "upload_dir_thumbnail"=>"uploads/$id_empresa/propiedades/",
    ));
  }

  function save_file() {
    $this->load->helper("imagen_helper");
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
    $path = "uploads/$id_empresa/entradas/";
    @move_uploaded_file($_FILES["path"]["tmp_name"],$path.$filename);
    // Si es una imagen, lo redimensionamos
    if (is_image($filename)) {
      resize(array(
        "dir"=>$path,
        "filename"=>$filename,
      ));
    }    
    echo json_encode(array(
      "path"=>$path.$filename,
      "error"=>0,
    ));
  }   

  function save_image($dir="",$filename="") {
    $id_empresa = $this->get_empresa();
    $dir = "uploads/$id_empresa/entradas/";
    $filename = $this->input->post("file");
    $res = parent::save_image($dir,$filename);

    $thumbnail_width = $this->input->post("thumbnail_width");
    if (!empty($thumbnail_width)) {
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
    
    $video = $this->modelo->get($id);
    if ($video === FALSE) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"No se encuentra el entrada con ID: $id",
      ));
      return;
    }

    $images = $video->images;
    
    $video->id = 0;
    $video->link = ""; // Como el link tiene el ID, se tiene que generar de vuelta
    $video->fecha_desde = fecha_mysql($video->fecha_desde);
    $video->fecha_hasta = fecha_mysql($video->fecha_hasta);
    $insert_id = $this->modelo->insert($video);
    
    // Actualizamos el link
    $base_link = "events/";
    $video->link = $base_link.filename($video->titulo,"-",0)."/";
    $this->db->query("UPDATE not_eventos SET link = '$video->link' WHERE id = $insert_id AND id_empresa = $video->id_empresa ");
    
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

    $images = $array->images;
    
    // Acomodamos las fechas
    $this->load->helper("fecha_helper");
    $array->fecha_desde = fecha_mysql($array->fecha_desde);
    $array->fecha_hasta = fecha_mysql($array->fecha_hasta);
    
    // Actualizamos el link en caso de que estemos publicando
    if ($anterior->activo == 0 && $array->activo == 1) {
      $base_link = "events/";
      $array->link = $base_link.filename($array->titulo,"-",0)."/";
    }

    // Actualizamos los datos del entrada
    $this->modelo->save($array);

    // Guardamos las imagenes
    $this->db->query("DELETE FROM not_eventos_images WHERE id_evento = $id AND id_empresa = $id_empresa");
    $k=0;
    foreach($images as $im) {
      $this->db->query("INSERT INTO not_eventos_images (id_empresa,id_evento,path,orden) VALUES($id_empresa,$id,'$im',$k)");
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
    $array->fecha_desde = fecha_mysql($array->fecha_desde);
    $array->fecha_hasta = fecha_mysql($array->fecha_hasta);
    $images = $array->images;

    // Insertamos el entrada
    $insert_id = $this->modelo->save($array);
    
    // Actualizamos el link
    $base_link = "events/";
    $array->link = $base_link.filename($array->titulo,"-",0)."/";
    $this->db->query("UPDATE not_eventos SET link = '$array->link' WHERE id = $insert_id AND id_empresa = $id_empresa");

    // Guardamos las imagenes
    $k=0;
    foreach($images as $im) {
      $this->db->query("INSERT INTO not_eventos_images (id_empresa,id_evento,path,orden) VALUES($id_empresa,$insert_id,'$im',$k)");
      $k++;
    }
    
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
      $sql.= " DATE_FORMAT(A.fecha_desde,'%d/%m/%Y %H:%i') AS fecha_desde, ";
      $sql.= " DATE_FORMAT(A.fecha_hasta,'%d/%m/%Y %H:%i') AS fecha_hasta ";
      $sql.= "FROM not_eventos A ";
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
    $id_empresa = ($this->input->get("id_empresa") !== FALSE) ? $this->input->get("id_empresa") : parent::get_empresa();
    $id_usuario = ($this->input->get("id_usuario") !== FALSE) ? $this->input->get("id_usuario") : 0;
    $categoria = ($this->input->get("categoria") !== FALSE) ? $this->input->get("categoria") : -1;
    $proximos = ($this->input->get("proximos") !== FALSE) ? $this->input->get("proximos") : -1;
    if (!empty($order_by) && !empty($order)) $order = $order_by." ".$order;
    else $order = "A.fecha_desde DESC";

    // Si term esta definido, es porque estamos buscando de la barra
    $term = $this->input->get("term");
    if ($term !== FALSE) $filter = $term;        
        
    $conf = array(
      "filter"=>$filter,
      "order"=>$order,
      "limit"=>$limit,
      "offset"=>$offset,
      "categoria"=>$categoria,
      "proximos"=>$proximos,
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