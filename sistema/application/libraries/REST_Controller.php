<?php defined('BASEPATH') OR exit('No direct script access allowed');

class REST_Controller extends CI_Controller {

  private $id_empresa = null;

  function _get_method() {
    return strtolower($this->input->server('REQUEST_METHOD'));
  }
  
  function send_error($mensaje) {
    header($_SERVER['SERVER_PROTOCOL'] . ' 500 '.$mensaje, true, 500);
    exit();    
  }

  function change_property() {
    $id_empresa = ($this->input->post("id_empresa") !== FALSE) ? $this->input->post("id_empresa") : $this->get_empresa();
    $id = $this->input->post("id");
    $attribute = $this->input->post("attribute");
    $value = $this->input->post("value");
    $table = $this->input->post("table");
    $id_field = $this->get_post("id_field","id");
    $sql = "UPDATE $table SET $attribute = '$value' WHERE $id_field = '$id' AND id_empresa = '$id_empresa' ";
    $this->db->query($sql);
    echo json_encode(array(
      "error"=>0,
    ));
  }

  function mklink($url) {
    $id_empresa = $this->get_empresa();
    $sql = "SELECT * FROM empresas_dominios WHERE id_empresa = $id_empresa LIMIT 0,1 ";
    $q = $this->db->query($sql);
    if ($q->num_rows($q)>0) {
      $dom = $q->row();
      $d = $dom->dominio;
    } else $d = "varcreative.com/sandbox/";
    if (substr($d,-1) !== "/") $d.="/"; // Si no termina con /, se la agregamos
    $d = (strpos($d, "http://") !== FALSE) ? $d : "http://".$d; // Siempre le agregamos el http://
    return $d.(($url !== "/")?$url:"");
  }

  function get_post($clave,$default = "") {
    $value = $this->input->post($clave);
    if ($value === FALSE) return $default;
    else return ($value);
  }
  function get_get($clave,$default = "") {
    $value = $this->input->get($clave);
    if ($value === FALSE) return $default;
    else return ($value);
  }

  private function prevent_upload() {
    // En todos los uploads se llama a esta funcion primero
    // Tenemos que ver si nos llega un FILE, y siempre corroborar que no sea un archivo .php
    if (isset($_FILES) && !empty($_FILES) && sizeof($_FILES)>0) {
      $this->load->helper("file_helper");
      foreach($_FILES as $key => $file) {
        $filename = $file["name"];
        $extension = get_extension($filename);
        if ($extension == "php" || $extension == "sh") {
          $this->send_error("Error en el tipo de archivo.");
          exit();
        }
      }
    }    
  }
  
  // EL ID DE LA EMPRESA ES SETEADO CUANDO SE LOGUEA EN EL ADMINISTRADOR
  function get_empresa() {
    return 1319;
    /*
    $this->prevent_upload();
    if ($this->id_empresa != null) {
      return $this->id_empresa;
    } else if (!isset($_SESSION["id_empresa"])) {
      // TODO: Se vencio la session, tenemos que enviarle el error
      $this->output->set_status_header('400');
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"La sesion ha expirado. Por favor ingrese nuevamente."
      ));
      exit();
    } else return $_SESSION["id_empresa"];
    */
  }
  
  // Funcion utilizada para reordenar los elementos en el arbol
  // Si el modelo tiene el metodo reorder(), se ejecuta
  function reorder() {
    if (method_exists($this->modelo,"reorder")) {
      
      $elements = $this->input->post("elements");
      if (empty($elements)) {
        echo json_encode(array(
          "error"=>1,
          "mensaje"=>"ERROR: No existen elementos para reordenar."
        ));
        return;
      }
      $elements = explode("-",$elements); // Lo convertimos en un array
      
      // Campo por el cual se filtra
      $filter_by = $this->input->post("filter_by");
      // Valor del campo de filtro
      $filter_value = $this->input->post("filter_value");
      
      $res = $this->modelo->reorder($elements,$filter_by,$filter_value);
      if ($res) {
        echo json_encode(array(
          "error"=>0,
        ));
      } else {
        echo json_encode(array(
          "error"=>1,
          "mensaje"=>"Ocurrio un error al reordenar los elementos."
        ));        
      }
      return;
    } else {
      echo json_encode(array(
        "error"=>1,
        "mensaje"=>"ERROR: Metodo no soportado para esta tabla."
      ));
      return;
    }
  }

  function start_import_excel($config = array()) {
    
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : $this->get_empresa();
    $tabla = isset($config["tabla"]) ? $config["tabla"] : "";

    $file = "uploads/".$_FILES["file"]["name"];
    $r = move_uploaded_file($_FILES["file"]["tmp_name"],$file);
    if ($r === FALSE) throw new Exception("Error al subir el archivo");

    $this->load->library("ExcelImporter");
    $importador = new ExcelImporter();
    $id = $importador->save_preview(array(
      "db"=>$this->db,
      "tabla"=>$tabla,
      "id_empresa"=>$id_empresa,
      "pathfile"=>$file,
    ));
    return $id;
    header("Location: /sistema/app/#importacion/articulos/$id");
  }
  
  function import($tabla,$borrar_tabla = 0) {
    $id_empresa = $this->get_empresa();
    $file = "uploads/".$_FILES["file"]["name"];
    if (move_uploaded_file($_FILES["file"]["tmp_name"],$file)) {
      $this->load->helper("import_helper");
      $db = $this->db;
      importar_tabla_csv(array(
        "id_empresa"=>$id_empresa,
        "tabla"=>$tabla,
        "archivo"=>$file,
        "borrar_tabla"=>$borrar_tabla,
        "db"=>$db,
      ));
    }
  }

  /** 
   *  Como esta funcion esta definida, todo lo que llega a este controlador pasa por aca.
   *  Nos fijamos de que tipo es el paquete, y enviamos a la funcion que corresponde
   *  @param $method: es el nombre del metodo, pero como estamo usando REST con Backbone,
   *  correspondera al ID
   *  @param $params: array que tiene el resto de los parametros, lo dejamos por si es necesario
   *  implementar algun metodo especial aparte de CRUD
   */
  public function _remap($method, $params = array()) 
  {
    // Llamamos a una funcion definida por nosotros
    if ($method == "function") 
    {
      // El primer parametro del array es el nombre de la funcion
      $metodo = $params[0];
      unset($params[0]);
      call_user_func_array(array($this,$metodo),$params);
    } else {

      // Estamos usando el REST segun Backbone
      switch($this->_get_method()) {
        case "get":   $this->get($method); break;
        case "post":   $this->insert(); break;
        case "put":   $this->update($method); break;
        case "delete":   $this->delete($method); break;
      }      
    }
  }

  /**
   * Obtiene un valor determinado que viene en PUT
   * @param string $key Clave del atributo del objeto que se quiere obtener
   */
  function put($key) {
    $arr = json_decode(file_get_contents('php://input'));
    if (isset($arr->{$key})) return $arr->{$key};
    else return null;
  }

  /**
   * Obtiene un array con todos los datos que vienen por PUT
   * @return Objeto JSON;
   */ 
  function parse_put() {
    $r = json_decode(nl2br(file_get_contents('php://input')));
    foreach ($r as $key => $value) {
      if (is_string($value)) $r->{$key} = ($value);
    }
    unset($r->undefined);
    unset($r->error);
    unset($r->mensaje);
    return $r;
    //return json_decode(str_replace('\n',"",file_get_contents('php://input')));
  }


  function get($id) {

    // Obtenemos todos los registros
    if ($id == "index") {
            
      $limit = $this->input->get("limit");
      $offset = $this->input->get("offset");
      $filter = $this->input->get("filter");
      $order_by = $this->input->get("order_by");
      $order = $this->input->get("order");
            
      if (!empty($filter)) {
        $lista = $this->modelo->find($filter);  
      } else {
        $lista = $this->modelo->get_all($limit,$offset,$order_by,$order);  
      }
      if (!$lista) $lista = array();

      // Total de lista
      $total = $this->modelo->count_all();

      // Armamos la salida
      $salida = array(
        "total"=> $total,
        "results"=>$lista
      );
      echo json_encode($salida);

    } else {
      // Estamos obteniendo un elemento en particular
      echo json_encode($this->modelo->get($id));
    }
  }

  function insert() {
    $array = $this->parse_put();
    $insert_id = $this->modelo->save($array);
    $salida = array("id"=>$insert_id);
    echo json_encode($salida);
  }

  function update($id) {
    // Si es 0, entonces lo insertamos
    if ($id == 0) { $this->insert($id); return; }
    $array = $this->parse_put();
    $this->modelo->save($array);
    $salida = array("id"=>$id);
    echo json_encode($salida);
  }

  function delete($id = null) {
    $this->modelo->delete($id);
    echo json_encode(array());
  }


  // Processes the encoded image data and returns the decoded image
  function process_image($photo) {
    $type = null;
    if (preg_match('/^data:image\/(jpg|jpeg|png)/i', $photo, $matches)) {
      $type = $matches[1];
    } else {
      return false;
    }
    // Remove the mime-type header
    $data = reset(array_reverse(explode('base64,', $photo)));
    // Use strict mode to prevent characters from outside the base64 range
    $image = base64_decode($data, true);
    if (!$image) { return false; }
    return array(
      'data' => $image,
      'type' => $type
    );
  }

  function upload_images($param = array()) {

    $id_empresa = (isset($param["id_empresa"]) ? $param["id_empresa"] : $this->get_empresa());
    $this->id_empresa = $id_empresa;
    $this->load->model("Empresa_Model");
    $empresa = $this->Empresa_Model->get($id_empresa);
    
    if (isset($param["clave_width"])) {
      $width = isset($empresa->config[$param["clave_width"]]) ? $empresa->config[$param["clave_width"]] : 400;
    } else $width = 400;
    if (isset($param["clave_height"])) {
      $height = isset($empresa->config[$param["clave_height"]]) ? $empresa->config[$param["clave_height"]] : 400;
    } else $height = 400;
    // TODO: Agregar los otros parametros como quality

    $upload_dir = isset($param["upload_dir"]) ? $param["upload_dir"] : "uploads/";
    $upload_url = $this->mklink("sistema/".$upload_dir);

    include_once("application/libraries/UploadHandler.php");
    $crop = (isset($empresa->config["habilitar_crop_multiple"])) ? true : false;
    /*if ($crop) {
      // Tenemos que cortar y redimensionar las imagenes
      $image_versions = array(
        ""=>array(
          "max_width"=>$width,
          "max_height"=>$height,
          "min_width"=>$width,
          "min_height"=>$height,
          "crop"=>true,
        )
      );
    } else {*/
      // No se cortan las imagenes
      // TEST: probamos de cortarla por las dudas de alguna forma
      $image_versions = array(
        ""=>array(
          "crop"=>false,
          "max_width"=>1920,
          "max_height"=>1080,
        ),
      );
      /*
      if (isset($param["upload_dir_thumbnail"])) {
        $thumb_dir = dirname($_SERVER['SCRIPT_FILENAME'])."/".$param["upload_dir_thumbnail"];
        $thumb_url = $this->mklink("sistema/".$thumb_dir);
        $upload_handler = new UploadHandler(array(
          "upload_dir"=>$thumb_dir,
          "upload_url"=>$thumb_url,
          "image_versions"=>array(
            ""=>array(
              "crop"=>false,
              "max_width"=>320,
              "max_height"=>180,
            )
          ),
        ));
      }*/
    //}

    $upload_handler = new UploadHandler(array(
      "upload_dir"=>dirname($_SERVER['SCRIPT_FILENAME'])."/".$upload_dir,
      "upload_url"=>$upload_url,
      "image_versions"=>$image_versions,
    ));
  }

  function upload_files($param = array()) {

    $id_empresa = (isset($param["id_empresa"]) ? $param["id_empresa"] : $this->get_empresa());
    $this->id_empresa = $id_empresa;
    $upload_dir = isset($param["upload_dir"]) ? $param["upload_dir"] : "uploads/";
    $base = $this->config->item("base_url");
    $upload_url = $base.$upload_dir;

    include_once("application/libraries/UploadHandler.php");
    $upload_handler = new UploadHandler(array(
      "upload_dir"=>dirname($_SERVER['SCRIPT_FILENAME'])."/".$upload_dir,
      "upload_url"=>$upload_url,
    ));
  }
  
  function save_image($dir="",$filename="") {
    
    header("Access-Control-Allow-Origin: *");
    
    if (!isset($_FILES["imagen"])) {
      $response = array(
        'error'=>1,
        'message'=>"ERROR: No se envio ninguna imagen.",
      );
      return json_encode($response);
    }
    
    if (empty($filename)) {
      // Si no tiene nombre, es porque es una imagen nueva
      $this->load->helper("file_helper");
      $filename = uniqid().'.'.get_extension($filename);
    } else {
      // Si el nombre ya tiene una barra, nos quedamos con la ultima parte del string
      if (strpos($filename,"/")>=0) $filename = substr($filename,strrpos($filename,"/")+1);
    }
    
    if (is_writable($dir)) {
      @move_uploaded_file($_FILES["imagen"]["tmp_name"],$dir.$filename);
      $response = array(
        'error'=>0,
        'path'=>$dir.$filename
      );
      return json_encode($response);
    } else {
      $response = array(
        'error'=>1,
        'message'=>"ERROR: No tiene permisos para escribir en el directorio $dir.",
      );
      return json_encode($response);
    }
  }

  function thumbnails($config = array()) {

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);    

    $updir = isset($config["dir"]) ? $config["dir"] : "uploads";
    $tipo_redimension = isset($config["tipo_redimension"]) ? $config["tipo_redimension"] : 1;
    $img = isset($config["filename"]) ? $config["filename"] : "";
    $thumbnail_width = isset($config["thumbnail_width"]) ? $config["thumbnail_width"] : 134;
    $thumbnail_height = isset($config["thumbnail_height"]) ? $config["thumbnail_height"] : 189;
    $thumb_beforeword = isset($config["preffix"]) ? $config["preffix"] : "thumb_";
    $arr_image_details = getimagesize("$updir"."$img");
    $original_width = $arr_image_details[0];
    $original_height = $arr_image_details[1];

    if ($tipo_redimension == 1) {
      // Ajusta dentro del tamaño
      if ($original_width > $original_height) {
        $new_width = $thumbnail_width;
        $new_height = floor($original_height * $new_width / $original_width);
      } else {
        $new_height = $thumbnail_height;
        $new_width = floor($original_width * $new_height / $original_height);
      }        
      $dest_x = intval(($thumbnail_width - $new_width) / 2);
      $dest_y = intval(($thumbnail_height - $new_height) / 2);
    } else if ($tipo_redimension == 2) {
      // Corta y no ajusta
      $new_width = $thumbnail_width;
      $new_height = floor($original_height * $new_width / $original_width);
      $dest_x = 0;
      $dest_y = 0;
    }
    if ($arr_image_details[2] == 1) {
      $imgt = "imagegif";
      $imgcreatefrom = "imagecreatefromgif";
    }
    if ($arr_image_details[2] == 2) {
      $imgt = "imagejpeg";
      $imgcreatefrom = "imagecreatefromjpeg";
    }
    if ($arr_image_details[2] == 3) {
      $imgt = "imagepng";
      $imgcreatefrom = "imagecreatefrompng";
    }
    if ($imgt) {
      $old_image = $imgcreatefrom("$updir"."$img");
      $new_image = imagecreatetruecolor($thumbnail_width, $thumbnail_height);
      $blanco = imagecolorallocate($new_image, 255, 255, 255);
      imagefill($new_image, 0, 0, $blanco);
      imagecopyresized($new_image, $old_image, $dest_x, $dest_y, 0, 0, $new_width, $new_height, $original_width, $original_height);
      $imgt($new_image, "$updir"."$thumb_beforeword"."$img");
    }
  }  
}