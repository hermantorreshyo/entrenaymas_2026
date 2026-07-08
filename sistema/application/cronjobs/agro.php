<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once("../../params.php");
include_once("../helpers/rssparser.php");
include_once("../helpers/file_helper.php");
 
$link = "http://news.agrofy.com.ar/granos/pizarra-cereales";

// Descargamos el HTML
$c = curl_init($link);
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
$finder = new DomXPath($dom);
$nodes = $finder->query("//*[@id='tabla-precios-pizarra']");
$contenido = "";
foreach($nodes as $n) {
  $contenido.= $dom->saveXML($n);
}
$contenido = str_replace('“', "&quot;", $contenido);
$contenido = str_replace('”', "&quot;", $contenido);
$contenido = str_replace('"', "&quot;", $contenido);
$contenido = str_replace('„', "&quot;", $contenido);
$contenido = str_replace("'", "&quot;", $contenido);
$contenido = stripcslashes($contenido);
$contenido = utf8_decode($contenido);
echo $contenido;

// Guardamos la noticia
/*
$sql = "INSERT INTO not_entradas (id_empresa,titulo,texto,id_categoria,fecha,activo,destacado,path,link_original,descripcion,fuente,comentarios_activo";
$sql.= ") VALUES (";
$sql.= "$id_empresa,'$titulo','$contenido','$rss->noticias_id_categoria',NOW(),$rss->noticias_activo,$rss->noticias_destacado,'$imagen','$link_original','$descripcion','$rss->fuente',1 ";
$sql.= ")";
mysqli_query($conx,$sql);
$id_entrada = mysqli_insert_id($conx);
*/
?>
