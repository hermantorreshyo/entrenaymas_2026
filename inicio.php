<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
if ( extension_loaded( 'zlib' ) ) { ob_start( 'ob_gzhandler' ); }
ob_start();
if (session_status() == PHP_SESSION_NONE) {
  @session_start();
}
http_response_code(200);
include("sistema/params.php");
@include("sistema/error_handler.php");

// 1) ANALIZAMOS LA URL
$dominio = strtolower($_SERVER["HTTP_HOST"]);
//$dominio = str_replace("www.","",$dominio);
$url = strtolower($_SERVER['REQUEST_URI']);
$url = str_replace(".php","",$url);
$url = (strpos($url,"/")==0) ? substr($url,1) : $url;

// ANALIZAMOS LA URL
$params = explode("/",$url);
$get_params = array();
$ultimo = end($params);
if (empty($ultimo) || strpos($ultimo,"?") === 0 || strpos($ultimo,"#") === 0) {
  if (strpos($ultimo,"?") == 0) {
    $ultimo = substr($ultimo,1); // Sacamos el ?
    $gets = explode("&", $ultimo);
    foreach($gets as $g) {
      $f = explode("=",$g);
      if (sizeof($f)==2) $get_params[$f[0]] = urldecode($f[1]);
    }
    unset($params[sizeof($params)-1]);
  } else if (strpos($ultimo,"#") == 0) {
    unset($params[sizeof($params)-1]);
  }
  //if (substr($url, -1) == "/") unset($params[sizeof($params)-1]);
  $ultimo = end($params);
}

function go_404() {
  global $dir_template, $empresa, $conx;
  if (file_exists("$dir_template/404.php")) include("$dir_template/404.php");
  else include("404.php");
}

function get_empresa_by_dominio($dominio) {
  global $conx;
  if (empty($dominio)) return FALSE;
  $dominio_con_www = (strpos("www.", $dominio) === FALSE) ? "www.".$dominio : $dominio;
  $sql = "SELECT E.*, T.path AS template_path, WC.*, ";
  $sql.= " IF(L.nombre IS NULL,'',L.nombre) AS localidad ";
  $sql.= "FROM empresas E ";
  $sql.= " INNER JOIN empresas_dominios ED ON (E.id = ED.id_empresa) ";
  $sql.= " INNER JOIN web_configuracion WC ON (E.id = WC.id_empresa) ";
  $sql.= " INNER JOIN web_templates T ON (E.id_web_template = T.id) ";
  $sql.= " LEFT JOIN com_localidades L ON (E.id_localidad = L.id) ";
  $sql.= "WHERE ED.dominio = '$dominio' ";
  $dominio = "test.entrenaymas.com";
  if ($dominio_con_www != $dominio) $sql.= "OR ED.dominio = '$dominio_con_www' ";
  $q = mysqli_query($conx,$sql);
  if (mysqli_num_rows($q)>0) {
    $empresa = mysqli_fetch_object($q);
    $empresa->direccion = $empresa->direccion_web;
    $empresa->telefono = $empresa->telefono_web;    

    // Formateamos el telefono para que pueda ser usado directo como link tel:
    $empresa->telefono_f = preg_replace("/[^0-9]/", "", $empresa->telefono);

    if (isset($empresa->configuraciones_especiales) && !empty($empresa->configuraciones_especiales)) {
      $empresa->config = array();
      $lineas = explode(";",$empresa->configuraciones_especiales);
      foreach($lineas as $l) {
        $l = trim($l);
        if (empty($l)) continue;
        if (strpos($l,"=")>0) {
          $campos = explode("=",$l);
          $clave = trim($campos[0]);
          $valor = trim($campos[1]);
          if (empty($clave)) continue;
          $empresa->config[$clave] = $valor;
        }
      }      
    }
    return $empresa;
  } else {
    return FALSE;
  }
}

function get_empresa_by_dominio_varcreative($dominio) {
  global $conx;
  $sql = "SELECT E.*, T.path AS template_path, WC.*, ";
  $sql.= " IF(L.nombre IS NULL,'',L.nombre) AS localidad ";
  $sql.= "FROM empresas E ";
  $sql.= " INNER JOIN web_configuracion WC ON (E.id = WC.id_empresa) ";
  $sql.= " INNER JOIN web_templates T ON (E.id_web_template = T.id) ";
  $sql.= " LEFT JOIN com_localidades L ON (E.id_localidad = L.id) ";
  $sql.= "WHERE E.id = '$dominio' "; // Termina siendo el ID
  $q = mysqli_query($conx,$sql);
  if (mysqli_num_rows($q)>0) {
    $empresa = mysqli_fetch_object($q);
    $empresa->direccion = $empresa->direccion_web;
    $empresa->telefono = $empresa->telefono_web;
    $empresa->telefono_f = preg_replace("/[^0-9]/", "", $empresa->telefono);
    if (isset($empresa->configuraciones_especiales) && !empty($empresa->configuraciones_especiales)) {
      $empresa->config = array();
      $lineas = explode(";",$empresa->configuraciones_especiales);
      foreach($lineas as $l) {
        $l = trim($l);
        if (empty($l)) continue;
        if (strpos($l,"=")>0) {
          $campos = explode("=",$l);
          $clave = trim($campos[0]);
          $valor = trim($campos[1]);
          if (empty($clave)) continue;
          $empresa->config[$clave] = $valor;
        }
      }      
    }
    return $empresa;
  } else {
    return FALSE;
  } 
}

function mklink($url) {
  global $dominio, $_SERVER;
  $d = URL_BASE;
  if (substr($d,-1) !== "/") $d.="/"; // Si no termina con /, se la agregamos
  //$d = (strpos($d, "http") === 0) ? $d : "http".((isset($_SERVER["HTTP_X_FORWARDED_PROTO"]))?"s":"")."://".$d;
  //$d = str_replace("http://", "https://", $d);
  return $d.(($url !== "/")?$url:"");
}

echo "DOMINIO: ".dominio;
$empresa = get_empresa_by_dominio($dominio);
print_r($empresa);
$base = "/";

// Controlamos si tiene configurado un dominio principal
if (!empty($empresa->dominio_ppal) && $empresa->dominio_ppal != $dominio) {
  // Redireccionamos
  $actual = $_SERVER["HTTP_HOST"].$_SERVER['REQUEST_URI'];
  $nueva = str_replace($_SERVER["HTTP_HOST"], $empresa->dominio_ppal, $actual);
  if (strpos($nueva, "http") === 0) {
    $nueva = "http".((isset($_SERVER["HTTP_X_FORWARDED_PROTO"])) ? "s" : "")."://".$nueva;
  }
  header("HTTP/1.1 301 Moved Permanently"); 
  header("Location: $nueva");
  exit();
}

// Si no se encontro, redireccionamos
if ($empresa === FALSE) {
  header('Content-Type: text/html; charset=UTF-8');
  include("404.php");
  exit();
}

// Si es un SITEMAP
if (strpos($url,"sitemap.xml") !== FALSE) {
  header('Content-Type: text/xml; charset=UTF-8');
  include("sistema/sitemap.php");
  ob_end_flush();
  exit();
}

// Si es un CATALOGO DE FACEBOOK
if (strpos($url,"fb_catalog.xml") !== FALSE) {
  header('Content-Type: text/xml; charset=UTF-8');
  include("sistema/fb_catalog.php");
  ob_end_flush();
  exit();
}

// Si es ROBOTS.TXT
if (strpos($url,"robots.txt") !== FALSE) {
  header('Content-Type: text/plain; charset=UTF-8');
  echo 'User-agent: *'."\n";
  echo 'Allow: /*.css$'."\n";
  echo 'Allow: /*.js$'."\n";
  echo 'DisAllow: /*.php$'."\n";
  echo 'User-agent: BDCbot'."\n";
  echo 'Disallow: /'."\n";
  echo 'User-agent: MJ12bot'."\n";
  echo 'Disallow: /'."\n";
  echo 'User-agent: PetalBot'."\n";
  echo 'Disallow: /'."\n";
  echo 'User-agent: Spiderbot/Nutch-1.7'."\n";
  echo 'Disallow: /'."\n";
  echo 'User-agent: CompSpyBot'."\n";
  echo 'Disallow: /'."\n";
  echo 'User-agent: Curious George'."\n";
  echo 'Disallow: /'."\n";
  echo 'User-agent: CybEye.com'."\n";
  echo 'Disallow: /'."\n";
  echo 'User-agent: DoCoMo'."\n";
  echo 'Disallow: /'."\n";
  echo 'User-agent: ExB Language Crawler'."\n";
  echo 'Disallow: /'."\n";
  echo 'User-agent: Ezooms'."\n";
  echo 'Disallow: /'."\n";
  echo 'User-agent: Flamingo_SearchEngine'."\n";
  echo 'Disallow: /'."\n";
  echo 'User-agent: Genieo'."\n";
  echo 'Disallow: /'."\n";
  echo 'User-agent: Genio'."\n";
  echo 'Disallow: /'."\n";
  echo 'User-agent: LWNutch'."\n";
  echo 'Disallow: /'."\n";
  echo 'User-agent: LexxeBot'."\n";
  echo 'Disallow: /'."\n";
  echo 'User-agent: OpenWebIndex'."\n";
  echo 'Disallow: /'."\n";
  echo 'User-agent: RediffNewsBot'."\n";
  echo 'Disallow: /'."\n";
  echo 'User-agent: SEOENGWorldBot'."\n";
  echo 'Disallow: /'."\n";
  echo 'User-agent: Scanmine '."\n";
  echo 'Disallow: /'."\n";
  echo 'User-agent: ShopWiki'."\n";
  echo 'Disallow: /'."\n";
  echo 'User-agent: ShowyouBot'."\n";
  echo 'Disallow: /'."\n";
  echo 'User-agent: Sosospider'."\n";
  echo 'Disallow: /'."\n";
  echo 'User-agent: WocBot'."\n";
  echo 'Disallow: /'."\n";
  echo 'User-agent: Yeti'."\n";
  echo 'Disallow: /'."\n";
  echo 'User-agent: YoudaoBot'."\n";
  echo 'Disallow: /'."\n";
  echo 'User-agent: daumoa'."\n";
  echo 'Disallow: /'."\n";
  echo 'User-agent: gsa-crawler'."\n";
  echo 'Disallow: /'."\n";
  echo 'User-agent: libcrawl'."\n";
  echo 'Disallow: /'."\n";
  echo 'User-agent: linkdex'."\n";
  echo 'Disallow: /'."\n";
  echo 'User-agent: magpie-crawler'."\n";
  echo 'Disallow: /'."\n";
  echo 'User-agent: repparser'."\n";
  echo 'Disallow: /'."\n";
  echo 'User-agent: rogerbot'."\n";
  echo 'Disallow: /'."\n";
  echo 'User-agent: sindice-site-manager'."\n";
  echo 'Disallow: /'."\n";
  echo 'User-agent: sogou spider'."\n";
  echo 'Disallow: /'."\n";
  echo 'User-agent: sogou'."\n";
  echo 'Disallow: /'."\n";
  echo 'User-agent: woriobot'."\n";
  echo 'Disallow: /'."\n";
  echo 'User-agent: yacybot'."\n";
  echo 'Disallow: /'."\n";
  echo 'User-agent: yolinkBot'."\n";
  echo 'Disallow: /'."\n";
  echo 'Sitemap: https://entrenaymas.com/sitemap.xml'."\n";
  ob_end_flush();
  exit();
}

header('Content-Type: text/html; charset=UTF-8');

$fecha_suspension = new DateTime($empresa->fecha_suspension);
$fecha_suspension->modify("+1 month");
if ($empresa->administrar_pagos == 1 && $fecha_suspension->format("Y-m-d") < date("Y-m-d")) {
  ob_end_flush();
  exit();
}

$dominio = "http".((isset($_SERVER["HTTP_X_FORWARDED_PROTO"])) ? "s" : "")."://".$dominio;

$nombre_pagina = (sizeof($params)>0) ? $params[0] : "";
if (isset($empresa->template_path) && !empty($empresa->template_path)) { 

  $dir_template = "templates/$empresa->template_path/";

  // Controlamos a que pagina desea ir
  if ($nombre_pagina == "index" || $nombre_pagina == "/" || empty($nombre_pagina) || strpos($nombre_pagina,"?") === 0) {
    if (file_exists($dir_template.$empresa->template_home.".php")) include($dir_template.$empresa->template_home.".php");
    else go_404();

  } else if ($nombre_pagina == "contacto" || $nombre_pagina == "contact") {
    if (file_exists($dir_template.$empresa->template_contacto.".php")) include($dir_template.$empresa->template_contacto.".php");
    else go_404();

  } else if ($nombre_pagina == "presupuesto") {
    if (file_exists($dir_template."presupuesto.php")) include($dir_template."presupuesto.php");
    else go_404();

  } else if ($nombre_pagina == "pagina") {
    $id = substr($ultimo,strrpos($ultimo,"-")+1); // Obtenemos el ID del ultimo parametro
    if (file_exists($dir_template.$empresa->template_pagina.".php")) include($dir_template.$empresa->template_pagina.".php");
    else go_404();

  // Si la pagina empieza con WEB, vamos al archivo especificado por el segundo parametro
  // Esto permite dar libertad a cada template por si hay que mostrar archivos especificos del proyecto,
  // asi, todo lo que empiece por web/* es como ejecutar *.php
  } else if ($nombre_pagina == "web" && isset($params[1])) {
    if (file_exists($dir_template.$params[1].".php")) include($dir_template.$params[1].".php");
    else go_404();

  // Si la pagina comienza con "e" solamente, es un link para redirigir
  } else if ($nombre_pagina == "e") {
    if (isset($params[1])) $id_link = intval($params[1]);
    else {
      $id_link = substr($_SERVER["REQUEST_URI"], strrpos($_SERVER["REQUEST_URI"], "/")+1);
    }
    $id_link = (strpos($id_link, "?")>0) ? substr($id_link, 0, strpos($id_link, "?")) : $id_link;
    $sql = "SELECT link FROM qr_redirecciones WHERE id = $id_link ";
    $q_link = mysqli_query($conx,$sql);
    if (mysqli_num_rows($q_link)>0) {
      $o_link = mysqli_fetch_object($q_link);
      header("Location: ".$o_link->link);
    }

  } else if ($nombre_pagina == "cursos" || $nombre_pagina == "revistas" || $nombre_pagina == "magazine") {

    $url = "";

    // Buscamos directamente en el campo LINK
    foreach($params as $p) $url.= "$p/";
    $l = $url;
    $ll = explode("?", $l);
    $l = $ll[0];
    $sql = "SELECT id FROM cursos WHERE id_empresa = $empresa->id AND link = '$l' ";

    $q = mysqli_query($conx,$sql);
    if (mysqli_num_rows($q)>0) {
      $r = mysqli_fetch_object($q);
      $id = $r->id;
      if (file_exists($dir_template."cursos_detalle.php")) include($dir_template."cursos_detalle.php");
      else go_404();
    } else {
      // No existe la pagina buscada, vamos al listado
      if (file_exists($dir_template."cursos_listado.php")) include($dir_template."cursos_listado.php");
      else go_404();
    }    

  } else if ($nombre_pagina == "mapa") {
    if (file_exists($dir_template."mapa.php")) include($dir_template."mapa.php");
    else go_404();

  } else if ($nombre_pagina == "subi-tu-propiedad") {
    if (file_exists($dir_template."subi_propiedad.php")) include($dir_template."subi_propiedad.php");
    else go_404();

  } else if ($nombre_pagina == "carrito") {
    if (file_exists($dir_template."carrito.php")) include($dir_template."carrito.php");
    else go_404();

  } else if ($nombre_pagina == "checkout") {
    if (file_exists($dir_template."checkout.php")) include($dir_template."checkout.php");
    else go_404();

  } else if ($nombre_pagina == "ajax_profesionales") {
    if (file_exists($dir_template."ajax_profesionales.php")) include($dir_template."ajax_profesionales.php");
    else go_404();

  } else if ($nombre_pagina == "login") {
    if (file_exists($dir_template."login.php")) include($dir_template."login.php");
    else go_404();

  // IPN'S
  } else if ($nombre_pagina == "ipn_mp" || $nombre_pagina == "ipn-mp") {
    if (file_exists("ipn_mp.php")) include("ipn_mp.php");
    else go_404();

  // CUENTAS ESPECIALES DE VULCATIRES
  } else if ($nombre_pagina == "ipn_mp_vulca_1" || $nombre_pagina == "ipn-mp-vulca-1") {
    if (file_exists("ipn_mp.php")) { $cuenta_vulcatires = "SAN_MARINO"; include("ipn_mp.php"); }
    else go_404();
  } else if ($nombre_pagina == "ipn_mp_vulca_2" || $nombre_pagina == "ipn-mp-vulca-2") {
    if (file_exists("ipn_mp.php")) { $cuenta_vulcatires = "PATAGONIA"; include("ipn_mp.php"); }
    else go_404();
  } else if ($nombre_pagina == "ipn_mp_vulca_3" || $nombre_pagina == "ipn-mp-vulca-3") {
    if (file_exists("ipn_mp.php")) { $cuenta_vulcatires = "MAR_DEL_PLATA"; include("ipn_mp.php"); }
    else go_404();

  } else if ($nombre_pagina == "ipn_sorteo_mp" || $nombre_pagina == "ipn-sorteo-mp") {
    if (file_exists("ipn_sorteo_mp.php")) include("ipn_sorteo_mp.php");
    else go_404();
  } else if ($nombre_pagina == "ipn_ciudad" || $nombre_pagina == "ipn-ciudad") {
    if (file_exists("ipn_ciudad.php")) include("ipn_ciudad.php");
    else go_404();
  } else if ($nombre_pagina == "ipn_viaje_mp" || $nombre_pagina == "ipn-viaje-mp") {
    if (file_exists("ipn_viaje_mp.php")) include("ipn_viaje_mp.php");
    else go_404();
  } else if ($nombre_pagina == "ipn_viaje_paypal" || $nombre_pagina == "ipn-viaje-paypal") {
    if (file_exists("ipn_viaje_paypal.php")) include("ipn_viaje_paypal.php");
    else go_404();

  // Resultados de la compra
  } else if ($nombre_pagina == "compra-ok") {
    if (file_exists($dir_template."resultado_compra_ok.php")) include($dir_template."resultado_compra_ok.php");
    else go_404();
  } else if ($nombre_pagina == "compra-fail") {
    if (file_exists($dir_template."resultado_compra_fail.php")) include($dir_template."resultado_compra_fail.php");
    else go_404();
  } else if ($nombre_pagina == "compra-pending") {
    if (file_exists($dir_template."resultado_compra_pending.php")) include($dir_template."resultado_compra_pending.php");
    else go_404();

  } else if ($nombre_pagina == "logout") {
    include("logout.php");

  } else if ($nombre_pagina == "registro") {
    if (file_exists($dir_template."registro.php")) include($dir_template."registro.php");
    else go_404();

  } else if ($nombre_pagina == "perfil") {
    if (file_exists($dir_template."perfil.php")) include($dir_template."perfil.php");
    else go_404();

  } else if ($nombre_pagina == "staff") {
    if (file_exists($dir_template."staff.php")) include($dir_template."staff.php");
    else go_404();

  } else if ($nombre_pagina == "mis-propiedades") {
    if (file_exists($dir_template."mis_propiedades.php")) include($dir_template."mis_propiedades.php");
    else go_404();

  } else if ($nombre_pagina == "subi-tu-propiedad") {
    if (file_exists($dir_template."subi_propiedad.php")) include($dir_template."subi_propiedad.php");
    else go_404();

  } else if ($nombre_pagina == "favoritos") {
    if (file_exists($dir_template."favoritos.php")) include($dir_template."favoritos.php");
    else go_404();

  } else if ($nombre_pagina == "productos") {
    if (file_exists($dir_template.$empresa->template_productos_listado.".php")) include($dir_template.$empresa->template_productos_listado.".php");
    else go_404();

  } else if ($nombre_pagina == "promociones") {
    $link_promocion = $params[1];
    $params = array_splice($params, 1);
    if (file_exists($dir_template.$empresa->template_productos_listado.".php")) include($dir_template.$empresa->template_productos_listado.".php");
    else go_404();

  } else if ($nombre_pagina == "producto") {
    $id = substr($ultimo,strrpos($ultimo,"-")+1); // Obtenemos el ID del ultimo parametro
    if (file_exists($dir_template.$empresa->template_productos_detalle.".php")) include($dir_template.$empresa->template_productos_detalle.".php");
    else go_404();

  } else if ($nombre_pagina == "preview") {
    $id = substr($ultimo,strrpos($ultimo,"-")+1); // Obtenemos el ID del ultimo parametro
    if (file_exists($dir_template."includes/modal_preview.php")) include($dir_template."includes/modal_preview.php");
    else go_404();

  } else if ($nombre_pagina == "noticias" || $nombre_pagina == "entradas"
   || $nombre_pagina == "actualidad"
   || $nombre_pagina == "deportes"
   || $nombre_pagina == "espectaculos"
   || $nombre_pagina == "internet"
   || $nombre_pagina == "musica"
   || $nombre_pagina == "sociedad"
   || $nombre_pagina == "tecnologia"
   || $nombre_pagina == "tecno"
    ) {
    if (file_exists($dir_template.$empresa->template_noticias_listado.".php")) include($dir_template.$empresa->template_noticias_listado.".php");
    else go_404();

  } else if ($nombre_pagina == "blog") {
    $params[] = "blog";
    if (file_exists($dir_template.$empresa->template_noticias_listado.".php")) include($dir_template.$empresa->template_noticias_listado.".php");
    else go_404();

  } else if ($nombre_pagina == "landing") {
    $id = substr($ultimo,strrpos($ultimo,"-")+1); // Obtenemos el ID del ultimo parametro
    if (file_exists($dir_template."landing.php")) include($dir_template."landing.php");
    else go_404();

  } else if ($nombre_pagina == "propiedades") {
    if (file_exists($dir_template."propiedades_listado.php")) include($dir_template."propiedades_listado.php");
    else go_404();

  } else if ($nombre_pagina == "propiedad") {
    $id = substr($ultimo,strrpos($ultimo,"-")+1); // Obtenemos el ID del ultimo parametro
    if (file_exists($dir_template."propiedades_detalle.php")) include($dir_template."propiedades_detalle.php");
    else go_404();

  } else if ($nombre_pagina == "galerias") {
    if (file_exists($dir_template."galerias_listado.php")) include($dir_template."galerias_listado.php");
    else go_404();

  } else if ($nombre_pagina == "galeria") {
    $id = substr($ultimo,strrpos($ultimo,"-")+1); // Obtenemos el ID del ultimo parametro
    if (file_exists($dir_template."galerias_detalle.php")) include($dir_template."galerias_detalle.php");
    else go_404();

  } else if ($nombre_pagina == "comercios") {
    if (file_exists($dir_template."comercios_listado.php")) include($dir_template."comercios_listado.php");
    else go_404();

  } else if ($nombre_pagina == "comercio") {
    $id = substr($ultimo,strrpos($ultimo,"-")+1); // Obtenemos el ID del ultimo parametro
    if (file_exists($dir_template."comercios_detalle.php")) include($dir_template."comercios_detalle.php");
    else go_404();

  } else if ($nombre_pagina == "viajes") {
    if (file_exists($dir_template."viajes_listado.php")) include($dir_template."viajes_listado.php");
    else go_404();

  } else if ($nombre_pagina == "viaje") {
    $id = substr($ultimo,strrpos($ultimo,"-")+1); // Obtenemos el ID del ultimo parametro
    if (file_exists($dir_template."viajes_detalle.php")) include($dir_template."viajes_detalle.php");
    else go_404();

  } else if ($nombre_pagina == "profile") {
    if (file_exists($dir_template."profile.php")) include($dir_template."profile.php");
    else go_404();    

  } else if ($nombre_pagina == "profesionales") {
    if (file_exists($dir_template."profesionales_listado.php")) include($dir_template."profesionales_listado.php");
    else go_404();

  } else if ($nombre_pagina == "profesional") {
    $id = substr($ultimo,strrpos($ultimo,"-")+1); // Obtenemos el ID del ultimo parametro
    if (file_exists($dir_template."profesionales_detalle.php")) include($dir_template."profesionales_detalle.php");
    else go_404();

  } else if ($nombre_pagina == "habitaciones" || $nombre_pagina == "departamentos") {
    if (file_exists($dir_template."habitaciones_listado.php")) include($dir_template."habitaciones_listado.php");
    else go_404();

  } else if ($nombre_pagina == "habitacion" || $nombre_pagina == "departamento") {
    $id = substr($ultimo,strrpos($ultimo,"-")+1); // Obtenemos el ID del ultimo parametro
    if (file_exists($dir_template."habitaciones_detalle.php")) include($dir_template."habitaciones_detalle.php");
    else go_404();

  } else if ($nombre_pagina == "ambientaciones") {
    include($dir_template."ambientaciones_listado.php");

  } else if ($nombre_pagina == "ambientacion") {
    $id = substr($ultimo,strrpos($ultimo,"-")+1); // Obtenemos el ID del ultimo parametro
    include($dir_template."ambientaciones_detalle.php");

  } else if ($nombre_pagina == "cartelera") {
    $id = (strpos($ultimo, "-")>0) ? substr($ultimo,strrpos($ultimo,"-")+1) : 0; // Obtenemos el ID del ultimo parametro
    include($dir_template."cartelera.php");

  } else if ($nombre_pagina == "sorteo") {
    $id = substr($ultimo,strrpos($ultimo,"-")+1); // Obtenemos el ID del ultimo parametro
    include($dir_template."sorteo.php");

  } else if ($nombre_pagina == "clasificados") {
    include($dir_template."clasificados_listado.php");
  } else if ($nombre_pagina == "clasificado") {
    $id = substr($ultimo,strrpos($ultimo,"-")+1); // Obtenemos el ID del ultimo parametro
    include($dir_template."clasificados_detalle.php");

  // Operaciones sobre el carrito
  } else if ($nombre_pagina == "cart" && isset($params[1])) {

    include("models/Carrito_Model.php");
    $carrito_model = new Carrito_Model($empresa->id,$conx);
    
    if ($params[1] == "item") {
      $carrito_model->modificar_item();
    } else if ($params[1] == "items") {
      $carrito_model->modificar_items();
    } else if ($params[1] == "save") {
      $carrito_model->actualizar();
    } else if ($params[1] == "empty") {
      $carrito_model->vaciar_carrito();
    } else if ($params[1] == "delete") {
      $carrito_model->eliminar_carrito();
    } else if ($params[1] == "calculate_shipping_cost") {
      $carrito_model->calcular_costo_envio_personalizado();
    } else if ($params[1] == "calculate_shipping_cost_mercadoenvio") {
      $carrito_model->calcular_costo_envio_mercadoenvio();
    } else if ($params[1] == "calculate_shipping_cost_correo_argentino") {
      $carrito_model->calcular_costo_envio_correo_argentino();
    } else if ($params[1] == "set_promo_code") {
      $carrito_model->set_promo_code();
    } else if ($params[1] == "set_shipping") {
      $carrito_model->set_envio();
    } else if ($params[1] == "set_shipping_method") {
      $carrito_model->set_forma_envio();
    } else if ($params[1] == "set_payment") {
      $carrito_model->set_forma_pago();
    } else if ($params[1] == "delete_shipping") {
      $carrito_model->delete_envio();
    } else if ($params[1] == "complete") {
      $carrito_model->finalizar();
    } else if ($params[1] == "complete_clienapp") {
      $carrito_model->finalizar_clienapp();
    } else if ($params[1] == "show") {
      $carrito_model->mostrar();
    }

  } else {

    // EXCEPCIONES
    // ============================
    $request_url = urldecode($_SERVER['REQUEST_URI']);
    $request_url = substr($request_url, 1);
    $request_url = str_replace(";", "", $request_url);
    $sql = "SELECT * FROM seo_urls WHERE (url = '$request_url' OR url = '$request_url/') AND id_empresa = ".$empresa->id;
    $q = mysqli_query($conx,$sql);
    if (mysqli_num_rows($q)>0) {

      $seo_url = mysqli_fetch_object($q);
      $seo_url_id = $seo_url->id;
      $seo_title = utf8_decode($seo_url->title);
      $seo_description = utf8_decode($seo_url->description);
      $h1 = $seo_url->h1;
      $h2 = $seo_url->h2;
      $texto_comercial = $seo_url->texto_comercial;
      $texto_pie = $seo_url->texto;
      $canonical = mklink("/").$seo_url->url;

      $array = explode("/", $request_url);
      $orden = 1;
      foreach($array as $u) {
        if (empty($u)) continue;
        $sql = "SELECT * FROM seo_urls_parametros ";
        $sql.= "WHERE id_empresa = $empresa->id ";
        $sql.= "AND id_seo = $seo_url->id ";
        $sql.= "AND orden = $orden ";
        $qq = mysqli_query($conx,$sql);
        if (mysqli_num_rows($qq)>0) {
          $rr = mysqli_fetch_object($qq);
          if ($rr->campo == "Especialidad") {
            $get_params["cat"] = $rr->valor;

            $sql = "SELECT * FROM toque_categorias WHERE id_empresa = $empresa->id AND id = $rr->valor ";
            $qqq = mysqli_query($conx,$sql);
            if (mysqli_num_rows($qqq)>0) {
              $categoria_seo = mysqli_fetch_object($qqq);
            }

          } else if ($rr->campo == "Localidad") {
            $get_params["locs"] = $rr->valor;

            $sql = "SELECT * FROM com_localidades WHERE id = $rr->valor ";
            $qqq = mysqli_query($conx,$sql);
            if (mysqli_num_rows($qqq)>0) {
              $localidad_seo = mysqli_fetch_object($qqq);
            }

          } else if ($rr->campo == "Titulacion") {
            $get_params["tit"] = $rr->valor;

            $sql = "SELECT * FROM med_titulos WHERE id_empresa = $empresa->id AND id = $rr->valor ";
            $qqq = mysqli_query($conx,$sql);
            if (mysqli_num_rows($qqq)>0) {
              $titulacion_seo = mysqli_fetch_object($qqq);
            }

          } else if ($rr->campo == "Objetivo") {
            $get_params["fp"] = $rr->valor;
          }
        }
        $orden++;
      }

      include($dir_template."profesionales_listado.php");

    } else {
      // Primero buscamos si ya tiene el ID
      $id = substr($ultimo,strrpos($ultimo,"-")+1); // Obtenemos el ID del ultimo parametro
      if (is_numeric($id)) {
        if (file_exists($dir_template.$empresa->template_noticias_detalle.".php")) include($dir_template.$empresa->template_noticias_detalle.".php");
        else go_404();
      } else {
        // Sino buscamos directamente en el campo LINK
        $url = "";
        foreach($params as $p) $url.= "$p/";
        $l = $url;
        if (strpos($l, "?") !== FALSE) {
          $uu = explode("?", $l);
          $l = $uu[0];
        }
        $sql = "SELECT id FROM not_entradas WHERE id_empresa = $empresa->id AND link = '$l' ";
        $q = mysqli_query($conx,$sql);
        if (mysqli_num_rows($q)>0) {
          $r = mysqli_fetch_object($q);
          $id = $r->id;
          if (file_exists($dir_template.$empresa->template_noticias_detalle.".php")) include($dir_template.$empresa->template_noticias_detalle.".php");
          else go_404();
        } else {
          // No existe la pagina buscada, vamos a una pagina de ERROR 404 GENERAL
          go_404();
        }
      }
    }

  }

}

ob_end_flush();
?>
