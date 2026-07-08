<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once("../../params.php");
include_once("../helpers/rssparser.php");
include_once("../helpers/file_helper.php");

// Obtenemos el RSS
$url = "https://www.ambito.com/rss/finanzas.xml";
$id_categoria = 836;
$fuente = "ambito.com";
$id_empresa = 857;
$rss_parser = new RSSParser($url);
$feeddata = $rss_parser->getRawOutput();
if (empty($feeddata)) exit();
extract($feeddata['RSS']['CHANNEL'][0], EXTR_PREFIX_ALL, 'rss');
// Recorremos las noticias
$j=0;
foreach($rss_ITEM as $itemdata) {

  if ($j==3) exit();

  // Primero controlamos que el link original a la nota ya no exista, asi no duplicamos la noticia
  $link_original = $itemdata['LINK'];
  echo $link_original."<br/>";

  $sql = "SELECT * FROM not_entradas WHERE id_empresa = $id_empresa AND link_original = '$link_original' ";
  $q_entrada = mysqli_query($conx,$sql);
  if (mysqli_num_rows($q_entrada)>0) continue;

  // Acomodamos los datos
  $titulo = isset($itemdata['TITLE']) ? ($itemdata['TITLE']) : "";

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

  $finder = new DomXPath($dom);
  // Sacamos un modulo de la persona que lo escribe
  $nodes = $finder->query("//*[contains(@class, 'person')]");
  foreach ($nodes as $node) {
    $node->parentNode->removeChild($node);
  }

  $imagen = "";
  $nodes = $finder->query("//*[contains(@id, 'content-gallery')]/div/div/div/img");
  foreach ($nodes as $node) {
    $imagen = $node->getAttribute("longdesc");
  }

  $contenido = "";
  $nodes = $finder->query("//*[contains(@class, 'note-body')]");
  foreach ($nodes as $node) {
    $contenido.= $node->textContent."<br/>";
  }

  $contenido = str_replace('“', "&quot;", $contenido);
  $contenido = str_replace('”', "&quot;", $contenido);
  $contenido = str_replace('"', "&quot;", $contenido);
  $contenido = str_replace('„', "&quot;", $contenido);
  $contenido = str_replace("'", "&quot;", $contenido);
  $contenido = stripcslashes($contenido);
  $titulo = htmlentities($titulo);
  $contenido = htmlentities($contenido);

  // Guardamos la noticia
  $f_tar = date("Y-m-d H:i:s");
  $sql = "INSERT INTO not_entradas (id_empresa,titulo,texto,id_categoria,fecha,activo,destacado,path,link_original,descripcion,fuente,comentarios_activo";
  $sql.= ") VALUES (";
  $sql.= "$id_empresa,'$titulo','$contenido','$id_categoria','$f_tar',1,0,'$imagen','$link_original','','$fuente',0 ";
  $sql.= ")";
  mysqli_query($conx,$sql);
  $id_entrada = mysqli_insert_id($conx);

  // Actualizamos el link propio de la entrada
  $link = "entrada/".filename($titulo)."-$id_entrada/";
  $sql = "UPDATE not_entradas SET link = '$link' WHERE id = $id_entrada ";
  mysqli_query($conx,$sql);

  $j++;
}
?>