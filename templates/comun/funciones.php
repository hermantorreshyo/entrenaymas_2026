<?php
// SI ESTAMOS EDITANDO EL TEMPLATE
if (isset($_GET["edit"]) && $_GET["edit"] == 1) {
  $edicion = TRUE;
}

if (!function_exists("format")) {
  function format($n,$consultar = true,$moneda = "$") {
    if ($n==0 && $consultar) return "Consultar";
    $n = number_format($n,2,',','.');
    $p = explode(",",$n);
    // Si la moneda viene como un numero, entonces es un ID_MONEDA
    if (is_numeric($moneda)) {
      if ($moneda == 1) $moneda = '$'; // Peso Argentino
      else if ($moneda == 2) $moneda = 'U$S'; // Dolar
      else if ($moneda == 3) $moneda = 'R$'; // Real
      else if ($moneda == 4) $moneda = '€'; // Euro
      else if ($moneda == 5) $moneda = '$'; // Peso Chileno
    }
    return $moneda." ".$p[0]." <sup>".$p[1]."</sup>";
  }
}

if (!function_exists("format")) {
  function estaEnFavoritos($id) {
    if (!isset($_SESSION["favoritos"])) return false;
    $favoritos = explode(",",$_SESSION["favoritos"]);
    foreach($favoritos as $f) {
      if ($f == $id) return true;
    }
    return false;
  }
}
?>