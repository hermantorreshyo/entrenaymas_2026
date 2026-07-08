<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once("../../params.php");
include_once("../helpers/rssparser.php");
include_once("../helpers/file_helper.php");

$id_rss = isset($_GET["id_rss"]) ? filter_var($_GET["id_rss"],FILTER_SANITIZE_STRING) : 0;
$id_empresa = isset($_GET["id_empresa"]) ? filter_var($_GET["id_empresa"],FILTER_SANITIZE_STRING) : 0;
$sql = "SELECT * FROM rss_sources ";
$sql.= "WHERE id_empresa = $id_empresa ";
if (!empty($id_rss)) $sql.= "AND id = $id_rss ";
$q_rss = mysqli_query($conx,$sql);
while(($rss=mysqli_fetch_object($q_rss))!==NULL) {

  if ($rss->noticias_cantidad == 0) $rss->noticias_cantidad = 10;

  // Obtenemos el RSS
  $rss_parser = new RSSParser($rss->url);
  $feeddata = $rss_parser->getRawOutput();
  if (empty($feeddata)) continue;
  extract($feeddata['RSS']['CHANNEL'][0], EXTR_PREFIX_ALL, 'rss');
  // Recorremos las noticias
  $j=0;
  foreach($rss_ITEM as $itemdata) {

    // Controlamos si llegamos al limite que queriamos
    if ($j>=$rss->noticias_cantidad) break;

    // Primero controlamos que el link original a la nota ya no exista, asi no duplicamos la noticia
    $link_original = $itemdata['LINK'];
    if ($rss->id == 9) {
      $link_original = "http://www.telam.com.ar/".$link_original;
    }

    echo $link_original."<br/>";

    // Hack para solucionar problema de Clarin
    $link_original = str_replace("politica/politica/","politica/",$link_original);
    $link_original = str_replace("deportes/futbol/deportes/futbol/","deportes/futbol/",$link_original);
    $link_original = str_replace("extrashow/fama/extrashow/fama/","extrashow/fama/",$link_original);
    $link_original = str_replace("extrashow/extrashow/","extrashow/",$link_original);

    $sql = "SELECT * FROM not_entradas WHERE id_empresa = $id_empresa AND link_original = '$link_original' ";
    $q_entrada = mysqli_query($conx,$sql);
    if (mysqli_num_rows($q_entrada)>0) continue;

    // Acomodamos los datos
    $titulo = isset($itemdata['TITLE']) ? ($itemdata['TITLE']) : "";
    $imagen = isset($itemdata['ENCLOSURE']["URL"]) ? $itemdata['ENCLOSURE']["URL"] : "";
    if ($id_empresa == 70 && empty($imagen)) continue; // Si no tiene foto, seguimos con otra...
    $descripcion = isset($itemdata['DESCRIPTION']) ? ($itemdata['DESCRIPTION']) : "";

    // Debemos seguir el link que ir a buscar el contenido de la noticia
    $contenido = "";
    if ($rss->noticias_incluir_contenido == 1) {

      if (isset($itemdata['CONTENT']['ENCODED'])) {
        // Tomamos el HTML
        $contenido = $itemdata['CONTENT']['ENCODED'];

      } else if(!empty($rss->noticias_path_contenido)) {
        // Descargamos el HTML
        $c = curl_init($link_original);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($c,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
        $html = curl_exec($c);
        if (curl_error($c)) die(curl_error($c));
        $status = curl_getinfo($c, CURLINFO_HTTP_CODE);
        curl_close($c);

        // Parse de HTML
        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($html);

        // Removemos los scripts que pueda llegar a tener la pagina
        foreach ($dom->getElementsByTagName('script') as $node) {
          $node->parentNode->removeChild($node);
        }
        // Removemos los style que pueda llegar a tener la pagina
        foreach ($dom->getElementsByTagName('style') as $node) {
          $node->parentNode->removeChild($node);
        }
        // Removemos los iframe que pueda llegar a tener la pagina
        foreach ($dom->getElementsByTagName('iframe') as $node) {
          $node->parentNode->removeChild($node);
        }

        // ID IMAGE de TELAM
        $node = $dom->getElementById('image');
        if (!is_null($node)) $node->parentNode->removeChild($node);

        // Sacamos todos los href de los links
        foreach ($dom->getElementsByTagName('a') as $node) {
          $node->removeAttribute('href');
        }

        $finder = new DomXPath($dom);

        // Eliminamos si tiene plugin social
        $nodes = $finder->query("//*[contains(@class, 'social-fixed-nav')]");
        foreach ($nodes as $node) {
          $node->parentNode->removeChild($node);
        }

        // EL PATH del contendo debe estar definido, para no cargar toda la pagina
        $nodes = $finder->query($rss->noticias_path_contenido);
        foreach($nodes as $n) {
          $contenido.= $dom->saveXML($n);
        }
      }

      /*
      $contenido = str_replace('“', "&quot;", $contenido);
      $contenido = str_replace('”', "&quot;", $contenido);
      $contenido = str_replace('"', "&quot;", $contenido);
      $contenido = str_replace('„', "&quot;", $contenido);
      $contenido = str_replace("'", "&quot;", $contenido);
      
      $contenido = utf8_decode($contenido);
      */
      $contenido = stripcslashes($contenido);
      $titulo = htmlentities($titulo);
      $descripcion = htmlentities($descripcion);
      $contenido = htmlentities($contenido);
    }

    // Ejecutamos los reemplazos
    if (!empty($rss->reemplazos)) {
      $reemplazos = explode(";",$rss->reemplazos);
      foreach($reemplazos as $r) {
        $p = explode("=>",$r);
        $contenido = str_replace(trim($p[0]),trim($p[1]),$contenido);
        $imagen = str_replace(trim($p[0]),trim($p[1]),$imagen);
      }      
    }

    // Guardamos la noticia
    $f_tar = date("Y-m-d H:i:s");
    $sql = "INSERT INTO not_entradas (id_empresa,titulo,texto,id_categoria,fecha,activo,destacado,path,link_original,descripcion,fuente,comentarios_activo";
    $sql.= ") VALUES (";
    $sql.= "$id_empresa,'$titulo','$contenido','$rss->noticias_id_categoria','$f_tar',$rss->noticias_activo,$rss->noticias_destacado,'$imagen','$link_original','$descripcion','$rss->fuente',1 ";
    $sql.= ")";
    mysqli_query($conx,$sql);
    $id_entrada = mysqli_insert_id($conx);

    // Relacionamos con las etiquetas
    $etiquetas = explode(",",$rss->noticias_etiquetas);
    $i=0;
    foreach($etiquetas as $e) {
      $sql = "INSERT INTO not_entradas_etiquetas (id_entrada,id_etiqueta,id_empresa,orden) VALUES (";
      $sql.= "$id_entrada, $e, $id_empresa, $i)";
      mysqli_query($conx,$sql);
      $i++;
    }

    // Actualizamos el link propio de la entrada
    $link = "entrada/".filename($titulo)."-$id_entrada/";
    $sql = "UPDATE not_entradas SET link = '$link' WHERE id = $id_entrada ";
    mysqli_query($conx,$sql);

    $j++;

  } // Fin for

}
?>
