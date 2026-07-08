<?php
include_once("../../params.php");
include_once("../helpers/file_helper.php"); 
$id_empresa = 70;
set_time_limit(0);
$desde = $_GET["desde"];
$hasta = $_GET["hasta"];
for($i=$desde;$i<$hasta;$i++) {
	
// Listado de categorias
$url = "http://www.estoeschacabuco.com.ar/2015-2016/detalle.php?IDComercio=$i";

$c = curl_init($url);
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
$rubro = "";
$nombre = "";
$telefono = "";
$direccion = "";
$email = "";

// Obtenemos el nombre
$nodes = $finder->query("//*[@id='nombreDetalle']");
foreach($nodes as $n) {
	$nombre = utf8_decode($n->nodeValue);
}

// Si el nombre es VACIO, seguimos
if (empty($nombre)) {
	continue;
}
$nombre = ucfirst(strtolower($nombre));

// Obtenemos el rubro
$nodes = $finder->query("//*[@class='rubro']");
foreach($nodes as $n) {
	$rubro = utf8_decode($n->nodeValue);
	$rubro = ucfirst(strtolower($rubro));
	$rubro_link = filename($rubro,"-",0);
}

// Obtenemos la direccion
$nodes = $finder->query("//*[contains(@class,'direccion')]");
foreach($nodes as $n) {
	$direccion = utf8_decode($n->nodeValue);
}

// Obtenemos la email
$nodes = $finder->query("//*[contains(@class,'email')]");
foreach($nodes as $n) {
	$email = utf8_decode($n->nodeValue);
}

// Obtenemos la telefono
$nodes = $finder->query("//*[contains(@class,'telefono')]");
foreach($nodes as $n) {
	$telefono = utf8_decode($n->nodeValue);
}

$sql = "SELECT * FROM clasificados_categorias WHERE id_empresa = $id_empresa and nombre = '$rubro' ";
$q = mysqli_query($conx,$sql);
if (mysqli_num_rows($q)<=0) {
	$sql = "INSERT INTO clasificados_categorias (id_empresa,nombre,link,activo) VALUES ($id_empresa,'$rubro','$rubro_link',1)";
	$qq = mysqli_query($conx,$sql);
	$id_categoria = mysqli_insert_id($qq);
} else {
	$categoria = mysqli_fetch_object($q);
	$id_categoria = $categoria->id;
}

// Si no existe el clasificado, lo guardamos
$sql = "SELECT * FROM clasificados WHERE id_empresa = $id_empresa AND titulo = '$nombre' ";
$q = mysqli_query($conx,$sql);
if (mysqli_num_rows($q)<=0) {
	$sql = "INSERT INTO clasificados (id_empresa,titulo,activo,fecha,id_categoria,direccion,telefono,email) VALUES ($id_empresa,'$nombre',1,NOW(),$id_categoria,'$direccion','$telefono','$email') ";
	$qq = mysqli_query($conx,$sql);
}

sleep(1);

echo "Nombre: [".$nombre."]<br/>Rubro: [".$rubro."]<br/>Email: [".$email."]<br/>Direccion [".$direccion."]<br/>Telefono [".$telefono."]";

}
echo "TERMINO 2";
?>