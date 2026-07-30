<?php
date_default_timezone_set("America/Argentina/Buenos_Aires");
error_reporting(0); // Evita que warnings/deprecations de PHP contaminen la respuesta
header('Access-Control-Allow-Origin: *');

// Tomamos el archivo de configuracion del punto de venta 
$impresora = "EPSON TM-T20II Receipt";
$ini = file_get_contents("punto_venta.txt");
$configs = explode("\n", $ini);
foreach($configs as $c) {
  $c1 = explode("=", $c);
  $p = trim($c1[0]);
  $v = trim($c1[1]);
  if ($p == "impresora") $impresora = $v;
}

// A traves de un archivo, controlamos que no se ejecuten dos veces el mismo proceso
$filename = "$impresora.txt";
if (file_exists($filename) === FALSE) file_put_contents($filename, "");
$file = fopen($filename, "r+");
// Intenta adquirir un bloqueo exclusivo
while((flock($file, LOCK_EX | LOCK_NB) === FALSE)) usleep(1000000);

require 'sistema/application/libraries/escpos/autoload.php';
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;

$lineas = 48;
$sep = str_pad("",$lineas,"-",STR_PAD_LEFT)."\n";

$pedido = json_decode($_POST["pedido"]);
$cliente = $pedido->cliente;
$connector = new WindowsPrintConnector($impresora);
$printer = new Printer($connector);
$printer -> setTextSize(2,2);
$printer -> setEmphasis(true);
$printer -> text($pedido->empresa_nombre."\n");
$printer -> setEmphasis(false);
$printer -> text("Fecha: ".date("d/m/Y")." Hora: ".date("H:i")."\n");
if (isset($pedido->titulo)) $printer -> text("Origen: ".$pedido->titulo."\n");	
$printer -> text("Usuario: ".$pedido->usuario."\n");	
$printer -> text("Cliente: $cliente->nombre\n");
if (!empty($cliente->direccion)) $printer -> text("Direccion: $cliente->direccion\n");
if (!empty($cliente->telefono)) $printer -> text("Telefono: $cliente->telefono\n");
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
$printer -> close();
echo json_encode(array(
	"error"=>0,
));
?>