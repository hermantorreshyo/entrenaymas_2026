<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include("../../params.php");

//$url = "https://free.currencyconverterapi.com/api/v5/convert?q=USD_ARS";
$url = "https://api-contenidos.lanacion.com.ar/json/v2/economia/cotizacion";
$c = curl_init($url);
curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($c,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
$html = curl_exec($c);
$resultado = json_decode($html);
foreach($resultado as $r) {
  if ($r->papel == "DLRBILL") {
    $valor = str_replace(",", ".", $r->venta);
    $compra = str_replace(",", ".", $r->compra);
    $sql = "UPDATE cotizaciones SET ";
    $sql.= " valor = $valor, ";
    $sql.= " valor_compra = $compra, ";
    $sql.= " fecha = NOW() WHERE id = 1";
    mysqli_query($conx,$sql);
    //echo "Venta: ".$valor." Compra: ".$compra;
  }
}

// TOMAMOS OTRAS MONEDAS DEL BANCO NACION
$url = "https://www.bna.com.ar/Personas";
$c = curl_init($url);
curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($c,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
$html = curl_exec($c);
$dom = new DOMDocument();
libxml_use_internal_errors(true);
$dom->loadHTML($html);
$finder = new DomXPath($dom);
$nodes = $finder->query("//*[@id='billetes']/table/tbody/tr");
foreach ($nodes as $node) {
    $moneda = "";
    $compra = "";
    $venta = "";
    $i = 0;
    foreach($node->childNodes as $n) {
        $t = strtolower($n->textContent);
        $t = str_replace(",", ".", $t);
        if ($i==0) $moneda = $t;
        if ($i==2) $compra = $t;
        if ($i==4) $venta = $t;
        $i++;
    }
  //echo "MONEDA: ".$moneda." ".$compra." ".$venta."<br/>";
  if (strpos($moneda, "euro")!==FALSE) {
    // ACTUALIZAMOS EL VALOR DEL EURO
    $sql = "UPDATE cotizaciones SET ";
    $sql.= " valor = $venta, ";
    $sql.= " valor_compra = $compra, ";
    $sql.= " fecha = NOW() WHERE id = 4 ";
    mysqli_query($conx,$sql);
  } else if (strpos($moneda, "real")!==FALSE) {
    // ACTUALIZAMOS EL VALOR DEL REAL
    $venta = ((float)$venta) / 100;
    $compra = ((float)$compra) / 100;
    $sql = "UPDATE cotizaciones SET ";
    $sql.= " valor = $venta, ";
    $sql.= " valor_compra = $compra, ";
    $sql.= " fecha = NOW() WHERE id = 3 ";
    mysqli_query($conx,$sql);
  }
}


// BOLSA DE VALORES
$url = "http://www.maxintavalores.com/wp-content/plugins/premium-stock-market-widgets/ajax.php";
$par = "params[source]=live&params[symbols][]=%5EMERV&params[symbols][]=%5EDJI&params[symbols][]=%5EGSPC&params[symbols][]=%5EBVSP&params[fields][]=virtual.name&params[fields][]=virtual.symbol&params[fields][]=quote.regularMarketPrice&params[fields][]=quote.regularMarketChange&params[fields][]=quote.regularMarketChangePercent";
$headers = array(
  'Content-Type: application/x-www-form-urlencoded'
);
$c = curl_init($url);
curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($c, CURLOPT_POST, true);
curl_setopt($c, CURLOPT_HTTPHEADER, $headers);
curl_setopt($c, CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
curl_setopt($c, CURLOPT_POSTFIELDS, $par);
$result = curl_exec($c);
curl_close($c);
$json = json_decode($result);

// MERVAL
$venta = $json->data->{"^MERV"}->{"quote.regularMarketPrice"};
$compra = $json->data->{"^MERV"}->{"quote.regularMarketChangePercent"};
$venta = str_replace(",", "", $venta);
$compra = str_replace(",", "", $compra);
$compra = str_replace("%", "", $compra);
$sql = "UPDATE cotizaciones SET ";
$sql.= " valor = '$venta', ";
$sql.= " valor_compra = '$compra', ";
$sql.= " fecha = NOW() WHERE id = 5 ";
mysqli_query($conx,$sql);

// Dow Jones Industrial Average
$venta = $json->data->{"^DJI"}->{"quote.regularMarketPrice"};
$compra = $json->data->{"^DJI"}->{"quote.regularMarketChangePercent"};
$venta = str_replace(",", "", $venta);
$compra = str_replace(",", "", $compra);
$compra = str_replace("%", "", $compra);
$sql = "UPDATE cotizaciones SET ";
$sql.= " valor = '$venta', ";
$sql.= " valor_compra = '$compra', ";
$sql.= " fecha = NOW() WHERE id = 6 ";
mysqli_query($conx,$sql);

// S&P 500
$venta = $json->data->{"^GSPC"}->{"quote.regularMarketPrice"};
$compra = $json->data->{"^GSPC"}->{"quote.regularMarketChangePercent"};
$venta = str_replace(",", "", $venta);
$compra = str_replace(",", "", $compra);
$compra = str_replace("%", "", $compra);
$sql = "UPDATE cotizaciones SET ";
$sql.= " valor = '$venta', ";
$sql.= " valor_compra = '$compra', ";
$sql.= " fecha = NOW() WHERE id = 7 ";
mysqli_query($conx,$sql);

// IBOVESPA
$venta = $json->data->{"^BVSP"}->{"quote.regularMarketPrice"};
$compra = $json->data->{"^BVSP"}->{"quote.regularMarketChangePercent"};
$venta = str_replace(",", "", $venta);
$compra = str_replace(",", "", $compra);
$compra = str_replace("%", "", $compra);
$sql = "UPDATE cotizaciones SET ";
$sql.= " valor = '$venta', ";
$sql.= " valor_compra = '$compra', ";
$sql.= " fecha = NOW() WHERE id = 8 ";
echo $sql;
mysqli_query($conx,$sql);
?>