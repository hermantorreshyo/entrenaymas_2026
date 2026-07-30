<?php
date_default_timezone_set("America/Argentina/Buenos_Aires");
error_reporting(0); // Evita que warnings/deprecations de PHP contaminen la respuesta
header('Access-Control-Allow-Origin: *');

// Tomamos el archivo de configuracion del punto de venta 
$impresora = "EPSON TM-T20II Receipt";
$impresora_cocina = "";
$ini = file_get_contents("punto_venta.txt");
$configs = explode("\n", $ini);
foreach($configs as $c) {
  $c1 = explode("=", $c);
  $p = trim($c1[0]);
  $v = trim($c1[1]);
  if ($p == "impresora_cocina") $impresora_cocina = $v;
  if ($p == "impresora") $impresora = $v;
}

// Si esta definido el parametro "impresora_cocina", tomamos ese
if (!empty($impresora_cocina)) $impresora = $impresora_cocina;

// A traves de un archivo, controlamos que no se ejecuten dos veces el mismo proceso
$filename = "$impresora.txt";
if (file_exists($filename) === FALSE) file_put_contents($filename, "");
$file = fopen($filename, "r+");
// Intenta adquirir un bloqueo exclusivo
while((flock($file, LOCK_EX | LOCK_NB) === FALSE)) usleep(1000000);

require 'sistema/application/libraries/escpos/autoload.php';
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;

$pedido = json_decode($_POST["pedido"]);
$cliente = $pedido->cliente;
$connector = new WindowsPrintConnector($impresora);
$printer = new Printer($connector);

if ($pedido->id_empresa == 171) {
  // JOHN BREAD
  $lineas = 24;
  $sep = str_pad("",$lineas,"-",STR_PAD_LEFT)."\n";
  $printer->setJustification(Printer::JUSTIFY_CENTER);
  $printer -> text($pedido->empresa_nombre."®\n");
  $printer -> text("25 de Mayo 291\n");
  $printer -> text("Tel: 426556\n");
  $printer->setJustification(Printer::JUSTIFY_LEFT);
  $printer -> text(date("H:i")." - Nro: ".$pedido->numero."\n");
  if (isset($pedido->titulo)) $printer -> text($pedido->titulo."\n");  
  $printer -> setEmphasis(true);
  if (isset($pedido->cliente)) $printer -> text("Cliente: ".$pedido->cliente->nombre."\n");  
  $printer -> setEmphasis(false);
  if (isset($pedido->cliente) && !empty($pedido->cliente->direccion)) $printer -> text("Dir: ".$pedido->cliente->direccion."\n");
  if (isset($pedido->cliente) && !empty($pedido->cliente->telefono)) $printer -> text("Tel: ".$pedido->cliente->telefono."\n");
  $printer -> text($sep);
  foreach($pedido->items as $item) {
    $cantidad = number_format($item->cantidad,1);
    $bonificacion = (isset($item->bonificacion)) ? number_format($item->bonificacion,1) : 0;
    $subtotal = number_format($item->total_con_iva,2);
    if ($item->id_rubro == 3604069 || $item->id_rubro == 3604066) {
  		$printer->setReverseColors(true);  	
    }
    $printer -> text("$item->nombre\n");
    $printer->setReverseColors(false);
    if (!empty($item->descripcion)) $printer -> text("  $item->descripcion\n");
    $printer -> text("Cantidad: $cantidad\n");
    if ($bonificacion > 0) $printer -> text("Dto: $bonificacion %\n");
    $printer -> text("Subtotal: $ $subtotal\n");
    $printer -> text("---\n\n");
  }
  $printer -> text($sep);
  $printer -> setEmphasis(true);
  if (isset($pedido->observaciones)) $printer -> text($pedido->observaciones."\n");  
  $printer->setReverseColors(true);
  if (isset($pedido->esta_pagado) && $pedido->esta_pagado == 1) $printer -> text("El pedido ya fue pagado.\n");  
  $printer->setReverseColors(false);
  if (isset($pedido->porc_descuento) && $pedido->porc_descuento > 0) $printer -> text("Dto Gral: $pedido->porc_descuento %\n");
  $printer -> text("Total: $".$pedido->total."\n");
  $printer -> setEmphasis(false);
  $printer -> text($sep);
  $printer -> text(" \n \n \n \n"); 

} else {
  $lineas = 24;
  $sep = str_pad("",$lineas,"-",STR_PAD_LEFT)."\n";
  $printer -> setTextSize(2,2);
  $printer -> text($pedido->empresa_nombre."\n");
  $printer -> text("Fecha: ".date("d/m/Y")." Hora: ".date("H:i")."\n");
  if (isset($pedido->titulo)) $printer -> text("Origen: ".$pedido->titulo."\n");  
  $printer -> text("Usuario: ".$pedido->usuario."\n");  
  $printer -> text($sep);
  foreach($pedido->items as $item) {
    $cantidad = str_pad(number_format($item->cantidad,1),6," ",STR_PAD_RIGHT);
    $dif = $lineas-6-2;
      $nombre = str_pad($item->nombre,$dif," ",STR_PAD_RIGHT);
      $printer -> text("$cantidad $nombre\n");
    if (!empty($item->descripcion)) $printer -> text("       $item->descripcion\n");
  }
  $printer -> text($sep);
  $printer -> text(" \n \n \n \n"); 
  $printer -> cut();
}
$printer -> close();
echo json_encode(array(
  "error"=>0,
));
?>