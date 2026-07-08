<?php
set_time_limit(0);

// Listado de categorias
$url = "https://laplata.olx.com.ar/departamentos-casas-en-venta-cat-367-p-2";

// Detalle de un anuncio en particular
$url = "https://laplata.olx.com.ar/departamento-2-dormitorios-en-venta-la-plata-iid-875616674";

$c = curl_init($url);
curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($c,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
$html = curl_exec($c);
if (curl_error($c)) die(curl_error($c));
$status = curl_getinfo($c, CURLINFO_HTTP_CODE);
curl_close($c);
echo $html;

// TODOS:
// =================

// - Que un script se dedique a juntar links y otro a procesar los links recolectados
// de acuerdo a la pagina que corresponda (OLX, etc)

// - Tag automatico:
// Utilizar algun sistema de tags para marcar las propiedades de manera inteligente
// Por ej: si encuentra la palabra REMAX o RE/MAX, que lo marque y de esa manera el usuario
// podra realizar ciertas acciones



?>