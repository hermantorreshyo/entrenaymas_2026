<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Fot_Trabajos extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Fot_Trabajo_Model', 'modelo');
  }

  function upload_images($id_empresa = 0) {
    $id_empresa = (empty($id_empresa)) ? $this->get_empresa() : $id_empresa;
    return parent::upload_images(array(
      "id_empresa"=>$id_empresa,
      "clave_width"=>"galeria_galeria_image_width",
      "clave_height"=>"galeria_galeria_image_height",
      "upload_dir"=>"uploads/$id_empresa/entradas/",
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
    $video->fecha = fecha_mysql($video->fecha);
    $insert_id = $this->modelo->insert($video);
    
    // Actualizamos el link
    $base_link = "galeria/";
    $video->link = $base_link.filename($video->titulo,"-",0)."-".$insert_id."/";
    $this->db->query("UPDATE fot_trabajos SET link = '$video->link' WHERE id = $insert_id AND id_empresa = $video->id_empresa ");
    
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
    $array->fecha = fecha_mysql($array->fecha);
    
    // Actualizamos el link en caso de que estemos publicando
    if ($anterior->activo == 0 && $array->activo == 1) {
      $base_link = "galeria/";
      $array->link = $base_link.filename($array->titulo,"-",0)."-".$id."/";
    }

    // Actualizamos los datos del entrada
    $this->modelo->save($array);

    // Guardamos las imagenes
    $this->db->query("DELETE FROM fot_trabajos_images WHERE id_trabajo = $id AND id_empresa = $id_empresa");
    $k=0;
    foreach($images as $im) {
      $this->db->query("INSERT INTO fot_trabajos_images (id_empresa,id_trabajo,path,orden) VALUES($id_empresa,$id,'$im',$k)");
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
    $array->fecha = fecha_mysql($array->fecha);
    $images = $array->images;

    // Insertamos el entrada
    $insert_id = $this->modelo->save($array);
    
    // Actualizamos el link
    $base_link = "galeria/";
    $array->link = $base_link.filename($array->titulo,"-",0)."-".$insert_id."/";
    $this->db->query("UPDATE fot_trabajos SET link = '$array->link' WHERE id = $insert_id AND id_empresa = $id_empresa");

    // Guardamos las imagenes
    $k=0;
    foreach($images as $im) {
      $this->db->query("INSERT INTO fot_trabajos_images (id_empresa,id_trabajo,path,orden) VALUES($id_empresa,$insert_id,'$im',$k)");
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
      $sql.= " DATE_FORMAT(A.fecha,'%d/%m/%Y %H:%i') AS fecha ";
      $sql.= "FROM fot_trabajos A ";
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
    else $order = "A.fecha DESC";

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