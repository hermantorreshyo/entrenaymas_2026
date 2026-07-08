<?php 
// ARRAY DE LINKS
array(
  "url"=>"https://www.catycan.com/alimentos-perros/royal-canin-maxi-adulto-x-15-kg"
  "pagina"=>"catycan"
)
// FOR DE LINKS

// 1) CARGAR EL HTML
$c = curl_init($link_original);
curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($c,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
$html = curl_exec($c);
if (curl_error($c)) die(curl_error($c));
$status = curl_getinfo($c, CURLINFO_HTTP_CODE);
curl_close($c);

// 2) LO CARGAS DENTRO DE UN DOM

// Parse de HTML
$dom = new DOMDocument();
libxml_use_internal_errors(true);
$dom->loadHTML($html);

// 3) 
if (pagina == catycan) {

  foreach ($dom->getElementsById('our_price_display') as $node) {
    $precio = $node->text

    // Guardar en la base de datos
    
  }

}


