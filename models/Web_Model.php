<?php
class Web_Model {

  private $id_empresa = 0;
  private $conx = null;
  private $total = 0;
  public $sql = "";

  function __construct($id_empresa,$conx) {
    $this->id_empresa = $id_empresa;
    $this->conx = $conx;
  }

  private function encod($r) {
    return ((mb_check_encoding($r) == "UTF-8") ? $r : utf8_encode($r));
  }

  function click_redes($conf = array()) {
    $red = isset($conf["red"]) ? $conf["red"] : "";
    $profesional_email = isset($conf["profesional_email"]) ? $conf["profesional_email"] : "";
    $id_usuario = isset($conf["id_usuario"]) ? $conf["id_usuario"] : 0;
    $id_empresa = isset($conf["id_empresa"]) ? $conf["id_empresa"] : $this->id_empresa;
    $ip_cliente = isset($conf["ip_cliente"]) ? $conf["ip_cliente"] : 0;
    $hoy = date("Y-m-d");

    $sql = "SELECT CR.* ";
    $sql.= "FROM click_redes CR ";
    $sql.= "WHERE CR.id_empresa = '$this->id_empresa' ";
    if (!empty($id_usuario)) $sql.= "AND CR.id_usuario = '$id_usuario' ";
    if (!empty($ip_cliente)) $sql.= "AND CR.ip_cliente = '$ip_cliente' ";
    if (!empty($red)) $sql.= "AND CR.red = '$red' ";
    $sql.= "AND CR.fecha = '$hoy' ";

    $q = mysqli_query($this->conx,$sql);
    
    if (mysqli_num_rows($q) == 0) {
      //Si no existe hay que crear y enviamos notificacion
      $sql = "INSERT INTO click_redes (id_empresa, id_usuario, red, fecha, ip_cliente) ";
      $sql.= "VALUES ";
      $sql.= "('$id_empresa', '$id_usuario', '$red', '$hoy', '$ip_cliente') ";
      mysqli_query($this->conx,$sql);

      include_once("sistema/application/libraries/Mandrill/Mandrill.php");

      $sql = "SELECT * FROM empresas WHERE id = $this->id_empresa";
      $q_emp = mysqli_query($this->conx,$sql);
      $empresa = mysqli_fetch_object($q_emp);

      $sql = "SELECT bcc_email FROM web_configuracion WHERE id_empresa = $this->id_empresa ";
      $q_conf_web = mysqli_query($this->conx,$sql);
      $web_conf = mysqli_fetch_object($q_conf_web);
      $empresa->bcc_email = $web_conf->bcc_email;

      $bcc_emails = array();
      if (!empty($empty->bcc_emails)) {
        $bcc_emails = explode(",", $empresa->bcc_email);
      }
      
      $bcc_emails[] = $empresa->email;

      $template = $this->get_email("nueva-visualizacion-red-social");

      mandrill_send(array(
        "from_name"=>$empresa->nombre,
        "to"=>$profesional_email,
        "subject"=>$template->nombre,
        "body"=>$template->texto,
        "bcc"=>$bcc_emails,
      ));

    } 
  }

  function get_anunciantes($conf = array()) {
    $sql = "SELECT * FROM anunciantes ";
    $sql.= "WHERE id_empresa = $this->id_empresa ";
    $sql.= "AND (mostrar = 'todos' OR mostrar = 'home') ";
    $q = mysqli_query($this->conx,$sql);
    $salida = array();
    while(($row=mysqli_fetch_object($q))!==NULL) {
      $row->nombre = $this->encod($row->nombre);
      $row->texto = $this->encod($row->texto);
      $row->path = ((strpos($row->path,"http")===FALSE)) ? "/sistema/".$row->path : $row->path;
      $salida[] = $row;
    }
    return $salida;    
  }

  function get_componentes($conf = array()) {
    $sql = "SELECT * FROM web_componentes ";
    $sql.= "WHERE id_empresa = $this->id_empresa ";
    $sql.= "AND activo = 1 ";
    $sql.= "ORDER BY orden ASC ";
    $q = mysqli_query($this->conx,$sql);
    $salida = array();
    while(($row=mysqli_fetch_object($q))!==NULL) {
      $row->nombre = $this->encod($row->nombre);
      $row->subtitulo = $this->encod($row->subtitulo);
      $row->path = ((strpos($row->path,"http")===FALSE)) ? "/sistema/".$row->path : $row->path;
      $salida[] = $row;
    }
    return $salida;
  }  

  function get_cotizacion_dolar() {
    return 19;
  }

  function create_og($config = array()) {
    $url = isset($config["url"]) ? $config["url"] : current_url();
    $tipo = isset($config["tipo"]) ? $config["tipo"] : "article";
    $titulo = isset($config["titulo"]) ? $config["titulo"] : "";
    $descripcion = isset($config["descripcion"]) ? $config["descripcion"] : "";
    $descripcion_largo = isset($config["descripcion_largo"]) ? $config["descripcion_largo"] : 180;
    $image = isset($config["image"]) ? $config["image"] : "";
    $image_width = isset($config["image_width"]) ? $config["image_width"] : 0;
    $image_height = isset($config["image_height"]) ? $config["image_height"] : 0;
    $salida = "";
    $salida.= '<meta property="og:url" content="'.$url.'" />';
    $salida.= '<meta property="og:type" content="'.$tipo.'" />';
    $salida.= '<meta property="og:title" content="'.$titulo.'" />';
    if (!empty($descripcion)) {
      $og_desc = strip_tags(html_entity_decode($descripcion,ENT_QUOTES));
      $og_desc = str_replace("\n","",$og_desc);
      $og_desc = str_replace("\"","",$og_desc);
      $og_desc = (strlen($og_desc)>$descripcion_largo) ? substr($og_desc, 0, $descripcion_largo)."..." : $og_desc;
      $salida.= '<meta property="og:description" content="'.$og_desc.'" />';
    }
    if (!empty($image)) {
      if (substr($image, 0, 4) != "http") $image =  current_url(TRUE).$image;
      $salida.= '<meta property="og:image" content="'.$image.'"/>';
      if (!empty($image_width)) $salida.= '<meta property="og:image:width" content="'.$image_width.'">';
      if (!empty($image_height)) $salida.= '<meta property="og:image:height" content="'.$image_height.'">';
    }
    return $salida;
  }

  function get_editor($id,$config=array()) {
    $id_empresa = isset($config["id_empresa"]) ? $config["id_empresa"] : $this->id_empresa;
    $sql = "SELECT * FROM not_editores ";
    $sql.= "WHERE id_empresa = $id_empresa ";
    $sql.= "AND id = $id ";
    $q = mysqli_query($this->conx,$sql);
    if (mysqli_num_rows($q)==0) return FALSE;
    $r = mysqli_fetch_object($q);
    if (!empty($r->path)) {
      $r->path = ((strpos($r->path,"http://")===FALSE)) ? "/sistema/".$r->path : $r->path;
    }
    return $r;
  }

  function get_editores($params = array()) {
    $limit = isset($params["limit"]) ? $params["limit"] : 0;
    $tipo = isset($params["tipo"]) ? $params["tipo"] : "";
    $filter = isset($params["filter"]) ? $params["filter"] : "";
    $offset = isset($params["offset"]) ? $params["offset"] : 9999;
    $id_empresa = isset($params["id_empresa"]) ? $params["id_empresa"] : $this->id_empresa;
    $sql = "SELECT * FROM not_editores ";
    $sql.= "WHERE id_empresa = $id_empresa ";
    if (!empty($filter)) $sql.= " AND LOWER(nombre) LIKE '%".strtolower($filter)."%' ";
    if (!empty($tipo)) $sql.= " AND tipo = '$tipo' ";
    $sql.= "LIMIT $limit, $offset ";
    $salida = array();
    $q = mysqli_query($this->conx,$sql);
    while(($r=mysqli_fetch_object($q))!==NULL) {
      if (!empty($r->path)) {
        $r->path = ((strpos($r->path,"http://")===FALSE)) ? "/sistema/".$r->path : $r->path;
      }
      $salida[] = $r;
    }
    return $salida;
  }

  function get_email_post_compra() {
    $sql = "SELECT ET.*, WC.id_email_post_compra, WC.email_post_compra_condicion, WC.email_post_compra_condicion_valor ";
    $sql.= "FROM web_configuracion WC INNER JOIN crm_emails_templates ET ON (WC.id_empresa = ET.id_empresa AND WC.id_email_post_compra = ET.id) ";
    $sql.= "WHERE WC.id_empresa = $this->id_empresa ";
    $q = mysqli_query($this->conx,$sql);
    if (mysqli_num_rows($q)<=0) return FALSE;
    $email = mysqli_fetch_object($q);
    return $email;
  }

  function get_metodo_envio() {
    $forma_envio = FALSE;
    $sql = "SELECT * FROM env_configuracion WHERE id_empresa = $this->id_empresa";
    $q = mysqli_query($this->conx,$sql);
    if (mysqli_num_rows($q)>0) {
      $forma_envio = mysqli_fetch_object($q);
    }

    // EN CASO DE QUE SEA MERCADOENVIOS, SE TOMAN LAS EXCEPCIONES
    $forma_envio->excepciones = array();
    $q = mysqli_query($this->conx,"SELECT * FROM env_excepciones WHERE id_empresa = $this->id_empresa AND tipo = 0 ORDER BY id ASC");
    while(($r=mysqli_fetch_object($q))!==NULL) {
      $forma_envio->excepciones[] = $r;
    }

    // EN CASO DE QUE SEA REPARTO, SE TOMA ESTE ARRAY
    $forma_envio->repartos = array();
    $q = mysqli_query($this->conx,"SELECT * FROM env_excepciones WHERE id_empresa = $this->id_empresa AND tipo = 1 ORDER BY id ASC");
    while(($r=mysqli_fetch_object($q))!==NULL) {
      $forma_envio->repartos[] = $r;
    }

    return $forma_envio;
  }

  // Metodo utilizado en ANACLETO para definir las sucursales que el usuario quiere ir a retirar su compra
  function get_sucursales_para_retiro() {
    $sql = "SELECT S.* ";
    $sql.= "FROM almacenes S ";
    $sql.= "WHERE S.id_empresa = $this->id_empresa ";
    $sql.= "AND S.para_retiro = 1 ";
    $salida = array();
    $q = mysqli_query($this->conx,$sql);
    while(($r=mysqli_fetch_object($q))!==NULL) {
      $r->nombre = $this->encod($r->nombre);
      $r->direccion = $this->encod($r->direccion);
      $salida[] = $r;
    }
    return $salida;
  }


  function get_sucursales() {
    $sql = "SELECT S.*, ";
    $sql.= " IF(L.nombre IS NULL,'',L.nombre) AS localidad, ";
    $sql.= " IF(L.latitud IS NULL,0,L.latitud) AS latitud, ";
    $sql.= " IF(L.longitud IS NULL,0,L.longitud) AS longitud ";
    $sql.= "FROM sucursales S ";
    $sql.= " LEFT JOIN com_localidades L ON (S.id_localidad = L.id) ";
    $sql.= "WHERE S.id_empresa = $this->id_empresa ";
    $sql.= "AND S.activo = 1 ";
    $salida = array();
    $q = mysqli_query($this->conx,$sql);
    while(($r=mysqli_fetch_object($q))!==NULL) {
      $salida[] = $r;
    }
    return $salida;
  }

  function get_usuarios($params = array()) {
    $limit = isset($params["limit"]) ? $params["limit"] : 0;
    $offset = isset($params["offset"]) ? $params["offset"] : 6;
    $id_empresa = isset($params["id_empresa"]) ? $params["id_empresa"] : $this->id_empresa;
    $aparece_web = isset($params["aparece_web"]) ? $params["aparece_web"] : 1;
    $sql = "SELECT S.* ";
    $sql.= "FROM com_usuarios S ";
    $sql.= "WHERE S.id_empresa = $id_empresa ";
    $sql.= "AND S.activo = 1 ";
    if ($aparece_web != -1) $sql.= "AND S.aparece_web = $aparece_web ";
    $sql.= "LIMIT $limit, $offset ";
    $salida = array();
    $q = mysqli_query($this->conx,$sql);
    while(($r=mysqli_fetch_object($q))!==NULL) {
      if (!empty($r->path)) {
        $r->path = ((strpos($r->path,"http://")===FALSE)) ? "/sistema/".$r->path : $r->path;
      }
      $salida[] = $r;
    }
    return $salida;
  }

  function get_slider($conf = array()) {
    $clave = (isset($conf["clave"])) ? $conf["clave"] : "";
    $sql = "SELECT * FROM web_slider WHERE id_empresa = $this->id_empresa AND activo = 1 ";
    if (!empty($clave)) $sql.= "AND clave = '$clave' ";
    $sql.= "ORDER BY orden ASC ";
    $this->sql = $sql;
    $salida = array();
    $q = mysqli_query($this->conx,$sql);
    while(($row=mysqli_fetch_object($q))!==NULL) {
      if (!empty($row->path)) {
        $row->path = ((strpos($row->path,"http://")===FALSE)) ? "/sistema/".$row->path : $row->path;
      }
      if (!empty($row->path_2)) {
        $row->path_2 = ((strpos($row->path_2,"http://")===FALSE)) ? "/sistema/".$row->path_2 : $row->path_2;
      }
      // Si ya fue guardado ANTES del cambio del panel
      if (!empty($row->linea_2) || !empty($row->linea_3) || !empty($row->linea_4) || !empty($row->linea_5)) {
        $row->linea_1 = $this->encod($row->linea_1);
        $row->linea_2 = $this->encod($row->linea_2);
        $row->linea_3 = $this->encod($row->linea_3);
        $row->linea_4 = $this->encod($row->linea_4);
        $row->linea_5 = $this->encod($row->linea_5);        
      } else {
        $linea = $this->encod($row->linea_1);
        $lineas= explode("\n", $linea);
        $row->linea_1 = $lineas[0];
        if (isset($lineas[1])) $row->linea_2 = $lineas[1];
        if (isset($lineas[2])) $row->linea_3 = $lineas[2];
        if (isset($lineas[3])) $row->linea_4 = $lineas[3];
        if (isset($lineas[4])) $row->linea_5 = $lineas[4];

        if (isset($row->linea_1_en)) {
          $linea = $this->encod($row->linea_1_en);
          $lineas= explode("\n", $linea);
          $row->linea_1_en = $lineas[0];
          if (isset($lineas[1])) $row->linea_2_en = $lineas[1];
          if (isset($lineas[2])) $row->linea_3_en = $lineas[2];
          if (isset($lineas[3])) $row->linea_4_en = $lineas[3];
          if (isset($lineas[4])) $row->linea_5_en = $lineas[4];
        }        
      }
      $row->texto_link_1 = $this->encod($row->texto_link_1);
      $row->texto_link_2 = $this->encod($row->texto_link_2);
      $salida[] = $row;
    }
    return $salida;
  }


  function get_categorias($id_padre) {

    $result = array();
    $sql = "SELECT * FROM not_categorias WHERE id_empresa = $this->id_empresa ";
    $sql.= "AND id_padre = $id_padre AND activo = 1 ORDER BY orden ASC ";
    $q = mysqli_query($this->conx,$sql);
    while(($row=mysqli_fetch_object($q))!==NULL) {
      $e = new stdClass();
      $e->id = $row->id;
      $e->id_padre = $id_padre;
      //echo "[".mb_detect_encoding($row->nombre)."]";
      $e->nombre = $this->encod($row->nombre);
      $e->link = $row->link;
      $e->external_link = $row->external_link;
      $e->thumbnail = (!empty($row->path) && strpos($row->path,"http://") === FALSE) ? (substr($row->path, 0, strrpos($row->path, "/"))."/thumb_".substr($row->path,strrpos($row->path,"/")+1)) : "";
      $e->children = $this->get_categorias($row->id);
      $result[] = $e;
    }
    return $result;
  }

  function get_categoria_by_nombre($nombre) {
    $result = array();
    $sql = "SELECT * FROM not_categorias WHERE id_empresa = $this->id_empresa AND link = '$nombre' ";
    $q = mysqli_query($this->conx,$sql);
    $row = mysqli_fetch_object($q);
    if ($row === NULL) return FALSE;
    $row->nombre = $this->encod($row->nombre);
    if (!empty($row->path)) {
      $row->path = ((strpos($row->path,"http://")===FALSE)) ? "/sistema/".$row->path : $row->path;
    }    
    return $row;
  }

  function get_categoria($id) {
    $result = array();
    $sql = "SELECT * FROM not_categorias WHERE id_empresa = $this->id_empresa AND id = $id ";
    $q = mysqli_query($this->conx,$sql);
    $row = mysqli_fetch_object($q);
    if ($row === NULL) return FALSE;
    $row->nombre = $this->encod($row->nombre);
    if (!empty($row->path)) {
      $row->path = ((strpos($row->path,"http://")===FALSE)) ? "/sistema/".$row->path : $row->path;
    }    
    return $row;
  }

  function get_category_by_name($link) {
    $result = array();
    $sql = "SELECT * FROM not_categorias WHERE id_empresa = $this->id_empresa AND link = '$link' ";
    $q = mysqli_query($this->conx,$sql);
    $row = mysqli_fetch_object($q);
    if ($row === NULL) return FALSE;
    $row->nombre = $this->encod($row->nombre);
    if (!empty($row->path)) {
      $row->path = ((strpos($row->path,"http://")===FALSE)) ? "/sistema/".$row->path : $row->path;
    }   
    return $row;
  }

  function get_main_categories() {
    $result = array();
    $sql = "SELECT * FROM not_categorias WHERE id_empresa = $this->id_empresa ";
    $sql.= "AND id_padre = 0 AND activo = 1 ORDER BY orden ASC ";
    $q = mysqli_query($this->conx,$sql);
    while(($row=mysqli_fetch_object($q))!==NULL) {
      $e = new stdClass();
      $e->id = $row->id;
      $e->id_padre = 0;
      $e->nombre = $this->encod($row->nombre);
      $e->link = $row->link;
      $e->external_link = $row->external_link;
      $result[] = $e;
    }
    return $result;
  }


  function get_text($clave,$default="Lorep ipsum",$config = array()) {
    if (session_status() == PHP_SESSION_NONE) {
      @session_start();
    }
    $no_lang = isset($config["no_lang"]) ? $config["no_lang"] : 0;
    $sql = "SELECT id, id_empresa, clave, link, texto_en, ";
    $language = (isset($_SESSION["language"])) ? $_SESSION["language"] : "";
    if ($no_lang == 1) {
      $sql.= "texto ";
    } else {
      // Debemos mostrar en un idioma en particular
      if ($language == "en") $sql.= "texto_en AS texto ";
      else if ($language == "pt") $sql.= "texto_pt AS texto ";
      else $sql.= "texto ";
    }
    $sql.= "FROM web_textos WHERE ";
    $sql.= "clave = '$clave' AND id_empresa = ".$this->id_empresa." ";
    $sql.= "LIMIT 0,1 ";
    $q = mysqli_query($this->conx,$sql);
    if (mysqli_num_rows($q)>0) {
      $row = mysqli_fetch_object($q);
      $texto = $row->texto;
      $row->texto = (html_entity_decode($texto,ENT_QUOTES));
      $row->plain_text = (strip_tags($texto,"<a><i><b><br>"));
      $row->plain_text = $this->encod($row->plain_text);
      $row->plain_text = ($row->plain_text == "/sistema/") ? $default : $row->plain_text;
      $row->plain_text = (empty($row->plain_text) ? $default : $row->plain_text);
      $texto = html_entity_decode($row->texto_en,ENT_QUOTES);
      $row->plain_text_en = (strip_tags($texto,"<a><i><b><br>"));
      $row->plain_text_en = $this->encod($row->plain_text_en);
      $row->plain_text_en = (empty($row->plain_text_en) ? $default : $row->plain_text_en);
      return $row;
    } else {
      // Devolvemos un objeto vacio
      $row = new stdClass();
      $row->texto = $default;
      $row->plain_text = $default;
      $row->plain_text_en = $default;
      $row->titulo = "";
      $row->clave = $clave;
      $row->link = "";
      $row->id = 0;
      return $row;
    }
  }

  function get_email($clave) {
    $sql = "SELECT * FROM crm_emails_templates WHERE clave = '$clave' AND id_empresa = ".$this->id_empresa." LIMIT 0,1 ";
    $q = mysqli_query($this->conx,$sql);
    if (mysqli_num_rows($q)>0) {
      return mysqli_fetch_object($q);
    } else {
      // Devolvemos un objeto vacio
      $row = new stdClass();
      $row->texto = "";
      $row->nombre = "";
      $row->clave = "";
      $row->id = 0;
      return $row;
    }
  }

  function get_videos($params = array()) {
    $limit = isset($params["limit"]) ? $params["limit"] : 0;
    $offset = isset($params["offset"]) ? $params["offset"] : 6;
    $filter = isset($params["filter"]) ? $params["filter"] : "";
    $not_id = isset($params["not_id"]) ? $params["not_id"] : "";
    $id_categoria = isset($params["id_categoria"]) ? $params["id_categoria"] : 0;
    $not_id_categoria = isset($params["not_id_categoria"]) ? $params["not_id_categoria"] : 0;
    $id_cliente = isset($params["id_cliente"]) ? $params["id_cliente"] : 0;
    $destacado = isset($params["destacado"]) ? $params["destacado"] : 0;
    $id_evento = isset($params["id_evento"]) ? $params["id_evento"] : 0;
    $sql = "SELECT SQL_CALC_FOUND_ROWS N.*, ";
    $sql.= " IF(R.nombre IS NULL,'',R.nombre) AS categoria, ";
    $sql.= " IF(R.link IS NULL,'',R.link) AS categoria_link, ";
    $sql.= " DATE_FORMAT(N.fecha,'%d/%m/%Y') AS fecha ";
    $sql.= "FROM not_videos N ";
    $sql.= "LEFT JOIN not_categorias R ON (N.id_categoria = R.id AND N.id_empresa = R.id_empresa) ";
    $sql.= "WHERE N.id_empresa = ".$this->id_empresa." ";
    if (!empty($filter)) $sql.= "AND N.titulo LIKE '%$filter%' ";
    if (!empty($not_id)) $sql.= "AND N.id NOT IN ($not_id) ";
    if (!empty($id_cliente)) $sql.= "AND N.id_cliente = $id_cliente ";
    if (!empty($id_categoria)) $sql.= "AND N.id_categoria = $id_categoria ";
    if (!empty($not_id_categoria)) $sql.= "AND N.id_categoria != $not_id_categoria ";
    if ($destacado == 1) $sql.= "AND N.destacado = 1 ";
    if (!empty($id_evento)) $sql.= "AND N.id_evento = $id_evento ";
    $sql.= "ORDER BY N.fecha DESC, N.id DESC ";
    $sql.= "LIMIT $limit, $offset ";
    $q = mysqli_query($this->conx,$sql);

    $q_total = mysqli_query($this->conx,"SELECT FOUND_ROWS() AS total");
    $t = mysqli_fetch_object($q_total);
    $this->total = $t->total;

    $salida = array();
    while(($r=mysqli_fetch_object($q))!==NULL) {
      $r->thumbnail = "https://img.youtube.com/vi/".$r->link_youtube."/0.jpg";
      $salida[] = $r;
    }
    return $salida;
  }

  function get_video($id,$params = array()) {
    $sql = "SELECT N.*, ";
    $sql.= " DATE_FORMAT(N.fecha,'%d/%m/%Y') AS fecha ";
    $sql.= "FROM not_videos N ";
    $sql.= "WHERE N.id_empresa = ".$this->id_empresa." ";
    $sql.= "AND N.id = $id ";
    $q = mysqli_query($this->conx,$sql);
    $row = mysqli_fetch_object($q);
    $row->thumbnail = "https://img.youtube.com/vi/".$row->link_youtube."/sddefault.jpg";
    return $row;
  }

  function get_eventos($params = array()) {
    $limit = isset($params["limit"]) ? $params["limit"] : 0;
    $offset = isset($params["offset"]) ? $params["offset"] : 6;
    $filter = isset($params["filter"]) ? $params["filter"] : "";
    $not_id = isset($params["not_id"]) ? $params["not_id"] : "";
    $tipo = isset($params["tipo"]) ? $params["tipo"] : "";
    $categoria = isset($params["categoria"]) ? $params["categoria"] : 0;
    $id_cliente = isset($params["id_cliente"]) ? $params["id_cliente"] : 0;
    $id_evento = isset($params["id_evento"]) ? $params["id_evento"] : 0;
    $filtro_fecha = isset($params["filtro_fecha"]) ? $params["filtro_fecha"] : 0;
    $sql = "SELECT SQL_CALC_FOUND_ROWS N.*, ";
    $sql.= " DATE_FORMAT(N.fecha_desde,'%d/%m/%Y') AS fecha_desde, ";
    $sql.= " DATE_FORMAT(N.fecha_hasta,'%d/%m/%Y') AS fecha_hasta ";
    $sql.= "FROM not_eventos N ";
    $sql.= "WHERE N.id_empresa = ".$this->id_empresa." ";
    $sql.= "AND N.categoria = '$categoria' ";
    if (!empty($filter)) $sql.= "AND N.titulo LIKE '%$filter%' ";
    if (!empty($not_id)) $sql.= "AND N.id NOT IN ($not_id) ";
    if (!empty($tipo)) $sql.= "AND (N.tipo = '$tipo' OR N.tipo = '') ";
    if (!empty($id_cliente)) $sql.= "AND N.id_cliente = '$id_cliente' ";
    if (!empty($id_evento)) $sql.= "AND N.id_evento = '$id_evento' ";
    if ($filtro_fecha == 1) {
      // Proximos
      $sql.= "AND N.fecha_desde > NOW() ";
      $sql.= "ORDER BY N.fecha_desde ASC, N.id ASC ";
    } else if ($filtro_fecha == 2) {
      // Realizados
      $sql.= "AND N.fecha_hasta < NOW() ";
      $sql.= "ORDER BY N.fecha_desde DESC, N.id DESC ";
    } else {
      $sql.= "ORDER BY N.fecha_desde DESC, N.id DESC ";  
    }
    $sql.= "LIMIT $limit, $offset ";
    $q = mysqli_query($this->conx,$sql);

    $q_total = mysqli_query($this->conx,"SELECT FOUND_ROWS() AS total");
    $t = mysqli_fetch_object($q_total);
    $this->total = $t->total;

    $salida = array();
    while(($r=mysqli_fetch_object($q))!==NULL) {
      if (!empty($r->path)) {
        $r->path = ((strpos($r->path,"http")===FALSE)) ? "/sistema/".$r->path : $r->path;
      }
      $salida[] = $r;
    }
    return $salida;
  }

  function get_evento($id,$params = array()) {
    $sql = "SELECT SQL_CALC_FOUND_ROWS N.*, ";
    $sql.= " DATE_FORMAT(N.fecha_desde,'%d/%m/%Y') AS fecha_desde, ";
    $sql.= " DATE_FORMAT(N.fecha_hasta,'%d/%m/%Y') AS fecha_hasta ";
    $sql.= "FROM not_eventos N ";
    $sql.= "WHERE N.id_empresa = ".$this->id_empresa." ";
    $sql.= "AND N.id = $id ";
    $q = mysqli_query($this->conx,$sql);
    $row = mysqli_fetch_object($q);
    if (!empty($row->path)) {
      $row->path = ((strpos($row->path,"http")===FALSE)) ? "/sistema/".$row->path : $row->path;
    }
    if (!empty($row->path_2)) {
      $row->path_2 = ((strpos($row->path_2,"http")===FALSE)) ? "/sistema/".$row->path_2 : $row->path_2;
    }
    // Obtenemos las imagenes de ese entrada
    $sql = "SELECT AI.* FROM not_eventos_images AI WHERE AI.id_evento = $id AND AI.id_empresa = $this->id_empresa ORDER BY AI.orden ASC";
    $q = mysqli_query($this->conx,$sql);
    while(($r=mysqli_fetch_object($q))!==NULL) {
      if (!empty($r->path)) {
        $r->path = ((strpos($r->path,"http")===FALSE)) ? "/sistema/".$r->path : $r->path;
      }
      $row->images[] = $r->path;
    }
    return $row;
  }

  function get_conferencistas($params = array()) {
    $limit = isset($params["limit"]) ? $params["limit"] : 0;
    $offset = isset($params["offset"]) ? $params["offset"] : 9999;
    $filter = isset($params["filter"]) ? $params["filter"] : "";
    $not_id = isset($params["not_id"]) ? $params["not_id"] : "";
    $id_evento = isset($params["id_evento"]) ? $params["id_evento"] : 0;
    $id_cliente = isset($params["id_cliente"]) ? $params["id_cliente"] : 0;
    $id_categoria = isset($params["id_categoria"]) ? $params["id_categoria"] : 0;
    $sql = "SELECT SQL_CALC_FOUND_ROWS N.*, ";
    $sql.= " DATE_FORMAT(N.fecha,'%d/%m/%Y') AS fecha, ";
    $sql.= " DATE_FORMAT(N.fecha,'%H:%i') AS hora ";
    $sql.= "FROM conferencistas N ";
    $sql.= "WHERE N.id_empresa = ".$this->id_empresa." ";
    if (!empty($filter)) $sql.= "AND (N.nombre LIKE '%$filter%' OR N.titulo LIKE '%$filter%') ";
    if (!empty($not_id)) $sql.= "AND N.id NOT IN ($not_id) ";
    if (!empty($id_evento)) $sql.= "AND N.id_evento = '$id_evento' ";
    if (!empty($id_cliente)) $sql.= "AND N.id_cliente = '$id_cliente' ";
    if (!empty($id_categoria)) $sql.= "AND N.id_categoria = '$id_categoria' ";
    $sql.= "ORDER BY N.fecha ASC, N.id DESC ";
    $sql.= "LIMIT $limit, $offset ";
    $q = mysqli_query($this->conx,$sql);

    $q_total = mysqli_query($this->conx,"SELECT FOUND_ROWS() AS total");
    $t = mysqli_fetch_object($q_total);
    $this->total = $t->total;

    $salida = array();
    while(($r=mysqli_fetch_object($q))!==NULL) {
      $r->titulo = $this->encod($r->titulo);
      $r->subtitulo = $this->encod($r->subtitulo);
      $r->nombre = $this->encod($r->nombre);
      if (!empty($r->path)) {
        $r->path = ((strpos($r->path,"http")===FALSE)) ? "/sistema/".$r->path : $r->path;
      }
      $salida[] = $r;
    }
    return $salida;
  }

  function get_total_results() {
    return $this->total;
  }

  function get_ultimas_necrologicas($params = array()) {
    $limit = isset($params["limit"]) ? $params["limit"] : 0;
    $offset = isset($params["offset"]) ? $params["offset"] : 4;
    $filter = isset($params["filter"]) ? $params["filter"] : "";
    $activo = isset($params["activo"]) ? $params["activo"] : 1;
    $participacion = isset($params["participacion"]) ? $params["participacion"] : -1;
    $sql = "SELECT *, ";
    $sql.= " DATE_FORMAT(N.fecha_traslado,'%d/%m/%Y') AS fecha_traslado, ";
    $sql.= " DATE_FORMAT(N.fecha_fallecimiento,'%d/%m/%Y') AS fecha_fallecimiento ";
    $sql.= "FROM inf_necrologicas N ";
    $sql.= "WHERE N.id_empresa = ".$this->id_empresa." ";
    $sql.= "AND N.activo = $activo ";
    if ($participacion != -1) $sql.= "AND N.participacion = $participacion ";
    if (!empty($filter)) $sql.= "AND N.nombre LIKE '%$filter%' ";
    $sql.= "ORDER BY N.fecha_fallecimiento DESC, N.id DESC ";
    $sql.= "LIMIT $limit, $offset ";
    $q = mysqli_query($this->conx,$sql);
    $salida = array();
    while(($r=mysqli_fetch_object($q))!==NULL) {
      $r->texto = str_replace("{{nombre}}",$this->encod($r->nombre),$r->texto);
      $r->texto = str_replace("{{edad}}",$this->encod($r->edad),$r->texto);
      $r->texto = str_replace("{{fecha_fallecimiento}}",$this->encod($r->fecha_fallecimiento),$r->texto);
      $r->texto = str_replace("{{fecha_traslado}}",$this->encod($r->fecha_traslado),$r->texto);
      $r->texto = str_replace("{{hora_traslado}}",$this->encod($r->hora_traslado),$r->texto);
      $r->texto = str_replace("{{casa_duelo}}",$this->encod($r->casa_duelo),$r->texto);
      $r->texto = str_replace("{{cementerio}}",$this->encod($r->cementerio),$r->texto);
      $r->texto = str_replace("{{servicio_velatorio}}",$this->encod($r->servicio_velatorio),$r->texto);
      $r->texto = html_entity_decode($r->texto,ENT_QUOTES);
      $salida[] = $r;
    }
    return $salida;
  }

  function farmacias_turno() {
    
    date_default_timezone_set("America/Argentina/Buenos_Aires");
    $ahora = new DateTime();
    
    $sql = "SELECT * FROM inf_farmacias F INNER JOIN inf_farmacias_turnos FT ON (F.id = FT.id_farmacia) ";
    $sql.= "WHERE F.id_empresa = $this->id_empresa ";
    
    // Si todavia no son las 8:30, tomamos la fecha de ayer
    if (strtotime(date("H:i:s")) < strtotime("08:30:00")) {
      $ahora->sub(new DateInterval("P1D"));
      $sql.= "AND FT.fecha = '".$ahora->format("Y-m-d")."' ";
      
    // Si son mas de las 8:30, tomamos la fecha de hoy
    } else {
      $sql.= "AND FT.fecha = '".$ahora->format("Y-m-d")."' ";
    }
    
    $q = mysqli_query($this->conx,$sql);
    $salida = array();
    while(($r=mysqli_fetch_object($q))!==NULL) {
      $salida[] = $r;
    }
    return $salida;
  }

  function get_encuestas_activas($conf = array()) {
    $limit = isset($conf["limit"]) ? $conf["limit"] : 0;
    $offset = isset($conf["offset"]) ? $conf["offset"] : 10;
    $sql = "SELECT * FROM encuestas ";
    $sql.= "WHERE id_empresa = $this->id_empresa ";
    $sql.= "AND activo = 1 ";
    $sql.= "AND valida_desde <= NOW() AND NOW() <= valida_hasta ";
    $sql.= "LIMIT $limit,$offset ";
    $q = mysqli_query($this->conx,$sql);
    $salida = array();
    while(($r=mysqli_fetch_object($q))!==NULL) {
      // Obtenemos las opciones
      $sql = "SELECT * FROM encuestas_opciones WHERE id_encuesta = $r->id AND id_empresa = $this->id_empresa ORDER BY orden ASC";
      $qq = mysqli_query($this->conx,$sql);
      $r->opciones = array();
      while(($rr=mysqli_fetch_object($qq))!==NULL) {
        $r->opciones[] = $rr;
      }
      $salida[] = $r;
    }
    return $salida;
  }

  function get_encuesta($id,$conf = array()) {
    $sql = "SELECT * FROM encuestas ";
    $sql.= "WHERE id_empresa = $this->id_empresa ";
    $sql.= "AND id = $id ";
    $sql.= "AND activo = 1 ";
    $sql.= "AND valida_desde <= NOW() AND NOW() <= valida_hasta ";
    $q = mysqli_query($this->conx,$sql);
    if (mysqli_num_rows($q)<=0) return FALSE;
    $r = mysqli_fetch_object($q);
    // Obtenemos las opciones
    $sql = "SELECT * FROM encuestas_opciones WHERE id_encuesta = $r->id AND id_empresa = $this->id_empresa ORDER BY orden ASC";
    $qq = mysqli_query($this->conx,$sql);
    $r->opciones = array();
    while(($rr=mysqli_fetch_object($qq))!==NULL) {
      $r->opciones[] = $rr;
    }
    return $r;
  }

  function get_testimonios($conf = array()) {
    $limit = isset($conf["limit"]) ? $conf["limit"] : 0;
    $offset = isset($conf["offset"]) ? $conf["offset"] : 10;
    $sql = "SELECT * FROM testimonios ";
    $sql.= "WHERE id_empresa = $this->id_empresa ";
    $sql.= "AND activo = 1 ";
    $sql.= "LIMIT $limit,$offset ";
    $q = mysqli_query($this->conx,$sql);
    $salida = array();
    while(($row=mysqli_fetch_object($q))!==NULL) {
      if (!empty($row->path)) {
        $row->path = ((strpos($row->path,"http://")===FALSE)) ? "/sistema/".$row->path : $row->path;
      }
      $salida[] = $row;
    }
    return $salida;
  }

  function get_peliculas_activas($conf = array()) {
    $limit = isset($conf["limit"]) ? $conf["limit"] : 0;
    $offset = isset($conf["offset"]) ? $conf["offset"] : 10;
    $sql = "SELECT * FROM inf_cartelera ";
    $sql.= "WHERE id_empresa = $this->id_empresa ";
    $sql.= "AND activo = 1 ";
    $sql.= "AND valido_desde <= NOW() AND NOW() <= valido_hasta ";
    $sql.= "LIMIT $limit,$offset ";
    $q = mysqli_query($this->conx,$sql);
    $salida = array();
    while(($r=mysqli_fetch_object($q))!==NULL) {
      $salida[] = $r;
    }
    return $salida;
  }


  function get_sorteos_activos($conf = array()) {
    $limit = isset($conf["limit"]) ? $conf["limit"] : 0;
    $offset = isset($conf["offset"]) ? $conf["offset"] : 10;
    $order_by = isset($conf["order_by"]) ? $conf["order_by"] : "id DESC";
    $sql = "SELECT * FROM sorteos ";
    $sql.= "WHERE id_empresa = $this->id_empresa ";
    $sql.= "AND activo = 1 ";
    $sql.= "AND valida_desde <= NOW() AND NOW() <= valida_hasta ";
    $sql.= "ORDER BY $order_by ";
    $sql.= "LIMIT $limit,$offset ";
    $q = mysqli_query($this->conx,$sql);
    $salida = array();
    while(($r=mysqli_fetch_object($q))!==NULL) {
      $salida[] = $r;
    }
    return $salida;
  }

  function get_sorteo($id,$conf = array()) {
    $sql = "SELECT * FROM sorteos ";
    $sql.= "WHERE id_empresa = $this->id_empresa ";
    $sql.= "AND id = $id ";
    $sql.= "AND activo = 1 ";
    $sql.= "AND valida_desde <= NOW() AND NOW() <= valida_hasta ";
    $q = mysqli_query($this->conx,$sql);
    $row = mysqli_fetch_object($q);
    if ($q !== NULL) {
      $row->titulo = $this->encod($row->titulo);
      $row->texto = $this->encod(html_entity_decode($row->texto,ENT_QUOTES));
      return $row;
    } else return FALSE;
  }

  function get_etiquetas($conf = array()) {
    $limit = isset($conf["limit"]) ? $conf["limit"] : 0;
    $offset = isset($conf["offset"]) ? $conf["offset"] : 10;
    $order_by = isset($conf["order_by"]) ? $conf["order_by"] : "nombre ASC";
    $sql = "SELECT * FROM not_etiquetas ";
    $sql.= "WHERE id_empresa = $this->id_empresa ";
    $sql.= "ORDER BY $order_by ";
    if (!empty($offset)) $sql.= "LIMIT $limit,$offset ";
    $q = mysqli_query($this->conx,$sql);
    $salida = array();
    while(($r=mysqli_fetch_object($q))!==NULL) {
      $salida[] = $r;
    }
    return $salida;
  }  

  function get_paises($conf = array()) {
    $limit = isset($conf["limit"]) ? $conf["limit"] : 0;
    $offset = isset($conf["offset"]) ? $conf["offset"] : 10;
    $order_by = isset($conf["order_by"]) ? $conf["order_by"] : "P.nombre ASC";
    $sql = "SELECT DISTINCT E.id_pais AS id, P.nombre ";
    $sql.= "FROM not_entradas E INNER JOIN com_paises P ON (E.id_pais = P.id) ";
    $sql.= "WHERE E.id_empresa = $this->id_empresa ";
    $sql.= "AND E.activo = 1 ";
    $sql.= "ORDER BY $order_by ";
    if (!empty($offset)) $sql.= "LIMIT $limit,$offset ";
    $q = mysqli_query($this->conx,$sql);
    $salida = array();
    while(($r=mysqli_fetch_object($q))!==NULL) {
      $salida[] = $r;
    }
    return $salida;
  }  

}
?>