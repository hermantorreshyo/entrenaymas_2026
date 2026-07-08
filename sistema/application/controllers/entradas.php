<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Entradas extends REST_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('Entrada_Model', 'modelo');
  }

  function acomodar_link() {
    $this->load->helper("file_helper");
    $q = $this->db->query("SELECT * FROM not_entradas WHERE id_empresa = 1129");
    foreach($q->result() as $row) {
      $link = filename($row->titulo,"-",0)."-".$row->id;
      $this->db->query("UPDATE not_entradas SET link = '$link' WHERE id = $row->id AND id_empresa = $row->id_empresa ");
    }
    echo "TERMINO";
  }  

  function me_gusta() {
    $id_entrada = (int)(parent::get_get("id_entrada",0));
    $id_empresa = (int)(parent::get_get("id_empresa",0));
    if (!is_numeric($id_entrada) || !is_numeric($id_empresa)) parent::send_error("Error en los parametros.");
    $this->db->query("UPDATE not_entradas SET me_gusta = me_gusta + 1 WHERE id_empresa = $id_empresa AND id = $id_entrada");
    echo json_encode(array(
      "error"=>0,
    ));
  }

  function upload_images($id_empresa = 0) {
    $id_empresa = (empty($id_empresa)) ? $this->get_empresa() : $id_empresa;
    return parent::upload_images(array(
      "id_empresa"=>$id_empresa,
      "clave_width"=>"entrada_galeria_image_width",
      "clave_height"=>"entrada_galeria_image_height",
      "upload_dir"=>"uploads/$id_empresa/entradas/",
    ));
  }


  function notificar($id) {
    header('Access-Control-Allow-Origin: *');
    $id_empresa = parent::get_empresa();
    $entrada = $this->modelo->get($id);
    if ($entrada === FALSE) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"No existe la entrada con ID: $id",
      ));
      exit();
    }
    $dominio = ($id_empresa == 70) ? "https://www.quepensaschacabuco.com": "https://www.varcreative.com";
    $params = array(
      "id_empresa"=>$id_empresa,
      "texto"=>urlencode($entrada->titulo),
      "image"=>$dominio."/sistema/".urlencode($entrada->path),
      "link"=>$dominio."/".urlencode($entrada->link),
    );
    $url = "https://www.varcreative.com/sistema/application/cronjobs/push_notification.php?".http_build_query($params);
    $c = curl_init($url);
    curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($c,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
    curl_exec($c);
    echo json_encode(array(
      "error"=>0,
      "mensaje"=>"Las notificaciones se han enviado correctamente.",
      "url"=>$url,
    ));
  }

  function notificar_email($id) {

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

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
    $temp = $this->Email_Template_Model->get_by_key("email-noticias",$id_empresa);
    if (empty($temp)) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"No se puede encontrar el template con ID: 'email-noticias'.",
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
    $link = str_replace("//", "/", $dominio."/".$entrada->link);
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
      $this->db->query("UPDATE not_entradas SET custom_1 = '1' WHERE id_empresa = $id_empresa AND id = $entrada->id ");
    }

    echo json_encode(array(
      "error"=>0,
      "mensaje"=>"The emails has been sent successfully",
    ));
  }

  function set_nivel_importancia() {
    $id_empresa = $this->get_empresa();
    $id = $this->input->post("id");
    $value = $this->input->post("value");
    $destacado = $this->input->post("destacado");
    $table = $this->input->post("table");
    $sql = "UPDATE not_entradas SET ";
    $sql.= "nivel_importancia = '$value', destacado = '$destacado' ";
    $sql.= "WHERE id = '$id' AND id_empresa = '$id_empresa' ";
    $this->db->query($sql);
    echo json_encode(array(
      "error"=>0,
    ));
  }

  function exportar_csv() {
    $id_empresa = parent::get_empresa();
    $this->load->dbutil();
    $this->load->helper('download');
    $query = $this->db->query("SELECT * FROM not_entradas WHERE id_empresa = $id_empresa AND eliminada = 0 ORDER BY id DESC LIMIT 0,100");
    $salida = $this->dbutil->csv_from_result($query, ";", "\r\n");
    force_download('entradas.csv', $salida);
  }

  function importar() {
    $tabla = "not_entradas";
    parent::import($tabla,1);
    header("Location: /sistema/app/#$tabla");
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
    
  function duplicar($id) {
      
    $this->load->helper("fecha_helper");
    $this->load->helper("file_helper");
    
    $entrada = $this->modelo->get($id);
    if ($entrada === FALSE) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"No se encuentra el entrada con ID: $id",
      ));
      return;
    }
    
    $entrada->id = 0;
    $entrada->link = ""; // Como el link tiene el ID, se tiene que generar de vuelta
    
    $relacionados = $entrada->relacionados;
    $categorias_relacionados = $entrada->categorias_relacionados; 
    $preguntas = $entrada->preguntas; 
    $horarios = $entrada->horarios;
    $images = $entrada->images;
    
    // Acomodamos los datos especificos
    $entrada->fecha = fecha_mysql($entrada->fecha);
    
    $this->remove_properties($entrada);
    $insert_id = $this->modelo->insert($entrada);
    
    // Actualizamos el link
    $base_link = ($entrada->id_empresa == 225) ? (substr($entrada->fecha, 0, 10)."/") : "entrada/";
    if ($id_empresa == 256) $base_link = "";
    $entrada->link = $base_link.filename($entrada->titulo,"-",0)."-".$insert_id;
    $this->db->query("UPDATE not_entradas SET link = '$entrada->link' WHERE id = $insert_id");
    
    // Actualizamos los productos relacionados
    $i=1;
    foreach($relacionados as $p) {
      $this->db->insert("not_entradas_relacionadas",array(
        "id_entrada"=>$insert_id,
        "id_empresa"=>$entrada->id_empresa,
        "id_relacion"=>$p->id,
        "id_categoria"=>0,
        "destacado"=>$p->destacado,
        "orden"=>$i,
      ));
      $i++;
    }
    
    // Actualizamos las categorias relacionadas
    $i=1;
    foreach($categorias_relacionados as $p) {
      $this->db->insert("not_entradas_relacionadas",array(
        "id_entrada"=>$insert_id,
        "id_empresa"=>$entrada->id_empresa,
        "id_relacion"=>0,
        "id_categoria"=>$p->id,
        "orden"=>$i,
      ));
      $i++;
    }

    $i=1;
    foreach($preguntas as $p) {
      $this->db->insert("not_entradas_preguntas",array(
        "id_entrada"=>$insert_id,
        "id_empresa"=>$entrada->id_empresa,
        "pregunta"=>$p->pregunta,
        "respuesta"=>$p->respuesta,
        "segundos"=>$p->segundos,
        "orden"=>$i,
      ));
      $i++;
    }
    
    $i=1;
    foreach($horarios as $p) {
      $this->db->insert("not_entradas_horarios",array(
        "id_entrada"=>$insert_id,
        "id_empresa"=>$entrada->id_empresa,
        "fecha"=>fecha_mysql($p->fecha),
        "hora"=>$p->hora,
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
    unset($array->images);
    unset($array->categoria);
    unset($array->usuario);
    unset($array->nuevo);
    unset($array->etiquetas);
    unset($array->horarios);
    unset($array->relacionados);
    unset($array->categorias_relacionados);
  }    
    
  function update($id) {
      
    if ($id == 0) { $this->insert(); return; }
    $this->load->helper("file_helper");
    $array = $this->parse_put();

    // Obtenemos la entrada actual antes de guardarla
    $anterior = $this->modelo->get($id);
    
    $id_empresa = parent::get_empresa();
    $array->id_empresa = $id_empresa;        
    
    // Acomodamos las fechas
    $this->load->helper("fecha_helper");
    $array->fecha = fecha_mysql($array->fecha);
    
    // Eliminamos todo lo que no se persiste
    $images = $array->images;
    $relacionados = $array->relacionados;
    $etiquetas = $array->etiquetas;
    $preguntas = $array->preguntas;
    $horarios = $array->horarios;
    $categorias_relacionados = $array->categorias_relacionados;
    $this->remove_properties($array);
    
    // Actualizamos el link en caso de que estemos publicando
    if ($anterior->activo == 0 && $array->activo == 1) {
      $base_link = ($id_empresa == 225) ? (substr($array->fecha, 0, 10)."/") : "entrada/";
      if ($id_empresa == 256) $base_link = "";
      $array->link = $base_link.filename($array->titulo,"-",0)."-".$id;
    }

    // LaboralGym, solo guardamos el ID del video de Youtube
    if ($id_empresa == 341) {
      $array->video = str_replace("https://www.youtube.com/watch?v=", "", $array->video);
    }

    if ($array->nivel_importancia > 0) $array->destacado = 1;
    
    // Actualizamos los datos del entrada
    $this->modelo->save($array);
    
    // Eliminamos las relaciones entre entradas
    $this->db->query("DELETE FROM not_entradas_relacionadas WHERE id_entrada = $id ");
    
    // Actualizamos los productos relacionados
    $i=1;
    foreach($relacionados as $p) {
      $this->db->insert("not_entradas_relacionadas",array(
        "id_entrada"=>$id,
        "id_relacion"=>$p->id,
        "id_empresa"=>$array->id_empresa,
        "id_categoria"=>0,
        "destacado"=>$p->destacado,
        "orden"=>$i,
      ));
      $i++;
    }
    
    // Actualizamos las categorias relacionadas
    $i=1;
    foreach($categorias_relacionados as $p) {
      $this->db->insert("not_entradas_relacionadas",array(
        "id_entrada"=>$id,
        "id_relacion"=>0,
        "id_empresa"=>$array->id_empresa,
        "id_categoria"=>$p->id,
        "orden"=>$i,
      ));
      $i++;
    }
    
    // Guardamos las relaciones con las etiquetas (Y se crean en caso de que no exitan)
    $i=1;
    $this->db->query("DELETE FROM not_entradas_etiquetas WHERE id_entrada = $id AND id_empresa = $array->id_empresa");
    foreach($etiquetas as $e) {
      $tag = new stdClass();
      $tag->id_empresa = $array->id_empresa;
      $tag->id_entrada = $id;
      $tag->nombre = $e;
      $this->modelo->save_tag($tag);
    }    

    $i=1;
    $this->db->query("DELETE FROM not_entradas_preguntas WHERE id_entrada = $id AND id_empresa = $array->id_empresa");
    foreach($preguntas as $p) {
      $this->db->insert("not_entradas_preguntas",array(
        "id_entrada"=>$id,
        "id_empresa"=>$array->id_empresa,
        "pregunta"=>$p->pregunta,
        "respuesta"=>$p->respuesta,
        "segundos"=>$p->segundos,
        "orden"=>$i,
      ));
      $i++;
    }

    $i=1;
    $this->db->query("DELETE FROM not_entradas_horarios WHERE id_entrada = $id AND id_empresa = $array->id_empresa");
    foreach($horarios as $p) {
      $this->db->insert("not_entradas_horarios",array(
        "id_entrada"=>$id,
        "id_empresa"=>$array->id_empresa,
        "fecha"=>fecha_mysql($p->fecha),
        "hora"=>$p->hora,
        "orden"=>$i,
      ));
      $i++;
    }
    
    // Guardamos las imagenes
    $this->db->query("DELETE FROM not_entradas_images WHERE id_entrada = $id AND id_empresa = $id_empresa");
    $k=0;
    foreach($images as $im) {
      $this->db->query("INSERT INTO not_entradas_images (id_empresa,id_entrada,path,orden) VALUES($id_empresa,$id,'$im',$k)");
      $k++;
    }

    // Llamamos al cacheador
    if ($id_empresa == 70) {
      $ch = curl_init();
      curl_setopt($ch,CURLOPT_URL, "https://www.quepensaschacabuco.com/sistema/application/cronjobs/cachear_quepensas.php?id=$id");
      curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
      curl_exec($ch);
    }    
    
    $salida = array(
      "id"=>$id,
      "error"=>0,
    );
    echo json_encode($salida);        
  }
    
  function insert() {

    set_time_limit(0);
        
    $this->load->helper("file_helper");
    $array = $this->parse_put();
        
    $id_empresa = parent::get_empresa();
    $array->id_empresa = $id_empresa;
    
    // Acomodamos las fechas
    $this->load->helper("fecha_helper");
    $array->fecha = fecha_mysql($array->fecha);

    if ($array->nivel_importancia > 0) $array->destacado = 1;
        
    // Eliminamos todo lo que no se persiste
    $images = $array->images;
    $relacionados = $array->relacionados;
    $etiquetas = $array->etiquetas;
    $preguntas = $array->preguntas;
    $horarios = $array->horarios;
    $categorias_relacionados = $array->categorias_relacionados;
    $this->remove_properties($array);

    // LaboralGym, solo guardamos el ID del video de Youtube
    if ($id_empresa == 341) {
      $array->video = str_replace("https://www.youtube.com/watch?v=", "", $array->video);
    }

    // Insertamos el entrada
    $insert_id = $this->modelo->save($array);
    
    // Actualizamos el link
    $base_link = ($id_empresa == 225) ? (substr($array->fecha, 0, 10)."/") : "entrada/";
    if ($id_empresa == 256) $base_link = "";
    $array->link = $base_link.filename($array->titulo,"-",0)."-".$insert_id;
    $this->db->query("UPDATE not_entradas SET link = '$array->link' WHERE id = $insert_id");
    
    // Actualizamos los productos relacionados
    $i=1;
    foreach($relacionados as $p) {
      $this->db->insert("not_entradas_relacionadas",array(
        "id_entrada"=>$insert_id,
        "id_relacion"=>$p->id,
        "id_empresa"=>$array->id_empresa,
        "id_categoria"=>0,
        "destacado"=>$p->destacado,
        "orden"=>$i,
      ));
      $i++;
    }
    
    // Actualizamos las categorias relacionadas
    $i=1;
    foreach($categorias_relacionados as $p) {
      $this->db->insert("not_entradas_relacionadas",array(
        "id_entrada"=>$insert_id,
        "id_relacion"=>0,
        "id_empresa"=>$array->id_empresa,
        "id_categoria"=>$p->id,
        "orden"=>$i,
      ));
      $i++;
    }
    
    // Guardamos las relaciones con las etiquetas (Y se crean en caso de que no exitan)
    $i=1;
    $this->db->query("DELETE FROM not_entradas_etiquetas WHERE id_entrada = $insert_id AND id_empresa = $array->id_empresa");
    foreach($etiquetas as $e) {
      $tag = new stdClass();
      $tag->id_empresa = $array->id_empresa;
      $tag->id_entrada = $insert_id;
      $tag->nombre = $e;
      $this->modelo->save_tag($tag);
    }   

    $i=1;
    $this->db->query("DELETE FROM not_entradas_preguntas WHERE id_entrada = $insert_id AND id_empresa = $array->id_empresa");
    foreach($preguntas as $p) {
      $this->db->insert("not_entradas_preguntas",array(
        "id_entrada"=>$insert_id,
        "id_empresa"=>$array->id_empresa,
        "pregunta"=>$p->pregunta,
        "respuesta"=>$p->respuesta,
        "segundos"=>$p->segundos,
        "orden"=>$i,
      ));
      $i++;
    }
    
    $i=1;
    $this->db->query("DELETE FROM not_entradas_horarios WHERE id_entrada = $insert_id AND id_empresa = $array->id_empresa");
    foreach($horarios as $p) {
      $this->db->insert("not_entradas_horarios",array(
        "id_entrada"=>$insert_id,
        "id_empresa"=>$array->id_empresa,
        "fecha"=>fecha_mysql($p->fecha),
        "hora"=>$p->hora,
        "orden"=>$i,
      ));
      $i++;
    }

    // Guardamos las imagenes
    $k=0;
    foreach($images as $im) {
      $this->db->query("INSERT INTO not_entradas_images (id_empresa,id_entrada,path,orden) VALUES($id_empresa,$insert_id,'$im',$k)");
      $k++;
    }

    // Consultamos si ese editor tiene seguidores
    /*
    $sql = "SELECT SEG.* ";
    $sql.= "FROM not_editores_seguidores SEG ";
    $sql.= "INNER JOIN clientes C ON (SEG.id_empresa = C.id_empresa AND SEG.id_usuario = C.id) ";
    $sql.= "WHERE SEG.id_empresa = $id_empresa ";
    $sql.= "AND SEG.id_editor = $array->id_editor ";
    $q = $this->db->query($sql);
    if ($q->num_rows()>0) {

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
      $temp = $this->Email_Template_Model->get_by_key("email-noticias",$id_empresa);
      if (empty($temp)) {
        echo json_encode(array(
          "error"=>1,
          "mensaje"=>"No se puede encontrar el template con ID: 'email-noticias'.",
        ));
        exit();      
      }
      $body = $temp->texto;
      $body = str_replace("{{nombre}}", $cliente->nombre, $body);
      $body = str_replace("{{titulo}}", $entrada->titulo, $body);
      $body = str_replace("{{id_cliente}}", $cliente->id, $body);
    }
    */

    // Llamamos al cacheador
    if ($id_empresa == 70) {
      $ch = curl_init();
      curl_setopt($ch,CURLOPT_URL, "https://www.quepensaschacabuco.com/sistema/application/cronjobs/cachear_quepensas.php");
      curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
      curl_exec($ch);
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
    header('Access-Control-Allow-Origin: *');
    $id_empresa = parent::get_post("id_empresa",0);
    if ($id_empresa == 0) $id_empresa = parent::get_empresa();
    // Obtenemos el listado
    if ($id == "index") {
      $sql = "SELECT A.*, ";
      $sql.= " DATE_FORMAT(A.fecha,'%d/%m/%Y %H:%i') AS fecha ";
      $sql.= "FROM not_entradas A ";
      $sql.= "WHERE A.activo = 1 AND id_empresa = $id_empresa ";
      $sql.= "ORDER BY A.titulo ASC ";
      $q = $this->db->query($sql);
      $result = $q->result();
      echo json_encode(array(
        "results"=>$result,
        "total"=>sizeof($result)
      ));
    } else {
      $entrada = $this->modelo->get($id,array(
        "id_empresa"=>$id_empresa
      ));
      echo json_encode($entrada);
    }
  }

  function get_video($id,$id_empresa) {
    $sql = "SELECT A.video ";
    $sql.= "FROM not_entradas A ";
    $sql.= "WHERE A.activo = 1 AND id_empresa = $id_empresa AND id = $id ";
    $q = $this->db->query($sql);
    $row = $q->row();
    
    // Anexamos una publicidad
    $this->load->model("Pieza_Model");
    $publicidades = $this->Pieza_Model->get_list(array(
      "buscar_por_fecha"=>1, // Filtra solo las piezas disponibles a la fecha
      "id_empresa"=>$id_empresa,
      "categoria"=>"Video",
      "offset"=>1,
    ));
    if (sizeof($publicidades)>0) {
      $publi = $publicidades[0];
      if (!empty($publi->video)) {
        $id_video = "";
        if (strpos($publi->video, "youtu.be")>0) {
          $id_video = str_replace("https://youtu.be/", "", $publi->video);
        } else if (strpos($publi->video, "/watch?")>0) {
          $id_video = str_replace("https://www.youtube.com/watch?v=", "", $publi->video);
        }
        if (!empty($id_video)) {
          $row->publicidad = $id_video;
          $row->tiempo = $publi->cerrar_despues * 1000;
        }
      }
    }
    echo json_encode($row);
  }
  
    
  /**
   *  Muestra todos los entradas filtrando segun distintos parametros
   *  El resultado esta paginado
   */
  function ver() {
    
    header('Access-Control-Allow-Origin: *');
    $limit = $this->input->get("limit");
    $filter = $this->input->get("filter");
    $offset = $this->input->get("offset");
    $order_by = $this->input->get("order_by");
    $order = $this->input->get("order");
    $id_categoria = $this->input->get("id_categoria");
    $id_empresa = ($this->input->get("id_empresa") !== FALSE) ? $this->input->get("id_empresa") : parent::get_empresa();
    $id_usuario = ($this->input->get("id_usuario") !== FALSE) ? $this->input->get("id_usuario") : 0;
    $id_cliente = ($this->input->get("id_cliente") !== FALSE) ? $this->input->get("id_cliente") : 0;
    $eliminada = ($this->input->get("eliminada") !== FALSE) ? $this->input->get("eliminada") : 0;
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
      "eliminada"=>$eliminada,
      "id_usuario"=>$id_usuario,
      "id_cliente"=>$id_cliente,
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
  
  /**
   * Esta funcion recibe un comentario del frontend
   */
  function comentar() {

    header('Access-Control-Allow-Origin: *');
    $this->load->model("Cliente_Model");
    
    $id_empresa = $this->input->post("id_empresa");
    $this->load->model("Empresa_Model");
    $empresa = $this->Empresa_Model->get($id_empresa);
    if ($empresa === FALSE) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"Error en la configuracion. Indique la empresa."
      ));
      return;            
    }
    $id_entrada = $this->input->post("id_entrada");
    $entrada = $this->modelo->get($id_entrada,array(
      "id_empresa"=>$id_empresa
    ));

    $id_padre = parent::get_post("id_padre",0);
    $email = parent::get_post("email","");
    $nombre = parent::get_post("nombre","Anonimo");
    $id_usuario = parent::get_post("id_usuario","");
    $codigo = parent::get_post("codigo","");
    $texto = parent::get_post("texto","");
    $para = parent::get_post("para","");
    $estado_comentario = parent::get_post("estado_comentario",1);

    $id_origen = 12;
    
    // Si se paso un email, buscamos el contacto para saber si existe
    $contacto = (!empty($email)) ? $this->Cliente_Model->get_by_email($email,$id_empresa) : FALSE;
    $this->load->model("Consulta_Model");

    if ($contacto === FALSE) {
      // Debemos crearlo
      $contacto = new stdClass();
      $contacto->id_empresa = $id_empresa;
      $contacto->email = $email;
      $contacto->nombre = $nombre;
      $contacto->fecha_inicial = date("Y-m-d");
      $contacto->fecha_ult_operacion = date("Y-m-d H:i:s");
      $contacto->tipo = 1; // Contacto
      $contacto->activo = 1; // El cliente esta activo por defecto
      $id = $this->Cliente_Model->insert($contacto);
      $contacto->id = $id;

      // REGISTRAMOS COMO UN EVENTO LA CREACION DEL NUEVO USUARIO
      $this->Consulta_Model->registro_creacion_usuario(array(
        "id_contacto"=>$id,
        "id_empresa"=>$id_empresa,
      ));
    }

    $fecha = date("Y-m-d H:i:s");
    $consulta = new stdClass();
    $consulta->id_empresa = $id_empresa;
    $consulta->id_entrada = $id_entrada;
    $consulta->fecha = $fecha;
    $consulta->asunto = $entrada->titulo;
    $consulta->texto = $texto;
    $consulta->id_contacto = $contacto->id;
    $consulta->id_origen = $id_origen;
    $consulta->id_usuario = $id_usuario;
    $consulta->fecha = date("Y-m-d");
    $consulta->hora = date("H:i:s");
    $id_consulta = $this->Consulta_Model->insert($consulta);

    // TODO: A futuro habria que eliminar los web_users y utilizar unicamente los clientes

    // El sistema de comentarios utilizado necesita usuarios registrados, por lo que hay que relacionarlos
    $this->load->model("Web_User_Model");
    $cliente = $this->Web_User_Model->get_by_email($email,$id_empresa);

    // Si el cliente esta bloqueado
    if ($cliente !== FALSE) {
      $id_usuario = $cliente->id;
      if ($cliente->activo == 0) {
        echo json_encode(array(
          "error"=>1,
          "mensaje"=>"Su usuario se encuentra inhabilitado para hacer comentarios. Ante cualquier inquietud comuniquese con el administrador del sitio. Muchas gracias."
        ));
        return;                    
      }
    } else {
      // El cliente no existe, debemos crearlo
      $id_usuario = $this->Web_User_Model->insert(array(
        "id_empresa"=>$id_empresa,
        "email"=>$email,
        "codigo"=>$codigo,
        "nombre"=>$nombre,
        "activo"=>1,
        "path"=>((!empty($codigo)) ? "https://graph.facebook.com/".$codigo."/picture" : ""),
        "fecha_inicial"=>date("Y-m-d"),
      ));
    }      
    
    // Insertamos el comentario
    $fecha = date("Y-m-d H:i:s");
    $sql = "INSERT INTO not_entradas_comentarios (";
    $sql.= " id_empresa,id_entrada,id_usuario,fecha,texto,estado,nombre,email ";
    $sql.= ") VALUES(";
    $sql.= " '$id_empresa','$id_entrada','$id_usuario','$fecha','$texto','$estado_comentario','$nombre','$email' ";
    $sql.= ")";
    $this->db->query($sql);
    $id_comentario = $this->db->insert_id();

    // Guardamos la referencia con la consulta
    $sql = "UPDATE crm_consultas SET id_referencia = $id_comentario ";
    $sql.= "WHERE id_empresa = $id_empresa AND id = $id_consulta ";
    $this->db->query($sql);

    $mensaje_salida = "Muchas gracias por su comentario!";

    if ($id_empresa != 70) {
      require APPPATH.'libraries/Mandrill/Mandrill.php';
      mandrill_send(array(
        "to"=>$empresa->email,
        "from"=>"no-reply@varcreative.com",
        "from_name"=>$nombre,
        "subject"=>"Comentario",
        "body"=>$texto,
        "reply_to"=>$email,
        "bcc"=>"basile.matias99@gmail.com",
      ));      
    }

    echo json_encode(array(
      "mensaje"=>$mensaje_salida,
      "error"=>0,
    ));
  }
  
  function activar_comentario() {
    $id_empresa = parent::get_empresa();
    $id = $this->input->post("id");
    $estado = $this->input->post("estado");
    $sql = "UPDATE not_entradas_comentarios SET estado = '$estado' WHERE id = $id AND id_empresa = $id_empresa";
    $this->db->query($sql);
    echo json_encode(array(
      "error"=>0,
    ));
  }
    
  function eliminar_comentario() {
    $id_empresa = parent::get_empresa();
    $id = $this->input->post("id");
    $sql = "DELETE FROM crm_consultas WHERE id_empresa = $id_empresa AND id_origen = 12 AND id_referencia = $id";
    $this->db->query($sql);
    $sql = "DELETE FROM not_entradas_comentarios WHERE id = $id AND id_empresa = $id_empresa";
    $this->db->query($sql);
    echo json_encode(array(
      "error"=>0,
    ));
  }
  
  function get_calendar($id_empresa,$id_categoria = 0) {
    header('Access-Control-Allow-Origin: *');
    $this->load->helper("fecha_helper");
    $conf = array();
    $conf["id_empresa"] = $id_empresa;
    $conf["id_categoria"] = $id_categoria;
    $desde = $this->input->get("start");
    if ($desde !== FALSE) $conf["desde"] = $desde;
    $hasta = $this->input->get("end");
    if ($hasta !== FALSE) $conf["hasta"] = $hasta;
    $salida = $this->modelo->buscar($conf);
    $resultado = array();
    // Acomodamos los datos
    foreach($salida["results"] as $r) {
      $o = new stdClass();
      $o->id = $r->id;
      $o->title = html_entity_decode($r->titulo,ENT_QUOTES);
      $o->start = fecha_mysql($r->fecha);
      $o->end = fecha_mysql($r->fecha);
      $o->link = $r->link;
      $o->allDay = true;
      $o->color = "#5196de";
      $o->editable = true;
      $resultado[] = $o;
    }
    echo json_encode($resultado);
  }


  function votar_emocion() {
    $id_empresa = $this->input->post("id_empresa");
    if ($id_empresa === FALSE) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"No esta definida la empresa",
      ));
      exit();
    }
    $id_entrada = $this->input->post("id_entrada");
    if ($id_entrada === FALSE) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"No esta definida la entrada",
      ));
      exit();
    }
    $opcion = $this->input->post("opcion");
    if ($opcion === FALSE || $opcion <= 0 || $opcion > 4) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"ERROR: Opcion incorrecta",
      ));
      exit();
    }
    $sql = "UPDATE not_entradas ";
    $sql.= "SET emocion_".$opcion."_cant = emocion_".$opcion."_cant + 1 ";
    $sql.= "WHERE id_empresa = '$id_empresa' AND id = '$id_entrada' ";
    $this->db->query($sql);
    echo json_encode(array(
      "error"=>0,
    ));
  }

  function ver_porcentajes_emociones() {
    $id_empresa = $this->input->post("id_empresa");
    if ($id_empresa === FALSE) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"No esta definida la empresa",
      ));
      exit();
    }
    $id_entrada = $this->input->post("id_entrada");
    if ($id_entrada === FALSE) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"No esta definida la entrada",
      ));
      exit();
    }
    $opcion = $this->input->post("opcion");
    if ($opcion === FALSE || $opcion <= 0 || $opcion > 4) {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"ERROR: Opcion incorrecta",
      ));
      exit();
    }

    $sql = "SELECT emocion_1_cant, emocion_2_cant, emocion_3_cant, emocion_4_cant ";
    $sql.= "WHERE id_empresa = '$id_empresa' AND id = '$id_entrada' ";
    $q = $this->db->query($sql);
    $r = $q->row();
    echo json_encode($r);
  }

  function delete($id = null) {
    $id_empresa = parent::get_empresa();
    $this->modelo->delete($id);

    // Llamamos al cacheador
    if ($id_empresa == 70) {
      $ch = curl_init();
      curl_setopt($ch,CURLOPT_URL, "https://www.quepensaschacabuco.com/sistema/application/cronjobs/cachear_quepensas.php");
      curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
      curl_exec($ch);
    } 

    echo json_encode(array());
  }

  function borrar($id) {
    $id_empresa = parent::get_empresa();
    $this->modelo->borrar($id);

    // Llamamos al cacheador
    if ($id_empresa == 70) {
      $ch = curl_init();
      curl_setopt($ch,CURLOPT_URL, "https://www.quepensaschacabuco.com/sistema/application/cronjobs/cachear_quepensas.php");
      curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
      curl_exec($ch);
    } 

    echo json_encode(array("error"=>0));
  }

}