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

$pedido = json_decode($_POST["pedido"]);
$cliente = $pedido->cliente;
$connector = new WindowsPrintConnector($impresora);
$printer = new Printer($connector);

// PANCHUQUE
if ($pedido->id_empresa == 718) {

	$lineas = 64;
	$sep = str_pad("",$lineas,"-",STR_PAD_LEFT)."\n";

	$printer -> setEmphasis(true);
	$printer -> text($pedido->empresa_nombre."\n");
	$printer -> setEmphasis(false);
	$printer -> text("Fecha: ".date("d/m/Y")." Hora: ".date("H:i")."\n");
	$printer -> setTextSize(2,2);
	$printer -> text($pedido->comprobante."\n");
	$printer -> setTextSize(1,1);
	$printer -> text("Cliente: $cliente->nombre\n");
	$printer -> text($sep);
	foreach($pedido->items as $item) {
		$cantidad = str_pad(number_format($item->cantidad,1),6," ",STR_PAD_RIGHT);
		$precio = str_pad("$".number_format($item->total_con_iva),8," ",STR_PAD_LEFT);
		$dif = $lineas-6-8-2;
		if (strlen($item->nombre)>$dif) {
			$cant_lineas = ceil(strlen($item->nombre)/$dif);
			for($i=0;$i<$cant_lineas;$i++) {
				$nombre = substr($item->nombre,($i*$cant_lineas),$dif);
				$nombre = str_pad($nombre,$dif," ",STR_PAD_RIGHT);
				if ($i==0) $printer -> text("$cantidad $nombre $precio\n");
				else $printer -> text("       $nombre         \n");
			}
		} else {
			$nombre = str_pad($item->nombre,$dif," ",STR_PAD_RIGHT);
			$printer -> text("$cantidad $nombre $precio\n");
		}
	}
	$printer -> text($sep);
	$printer -> text("TOTAL: $ $pedido->total \n");
	$printer -> text($sep);
	if ($pedido->pagada == 1) {
		$printer -> text("PEDIDO PAGADO\n");
	}


} else {
	
	$lineas = 48;
	$sep = str_pad("",$lineas,"-",STR_PAD_LEFT)."\n";

	$printer -> setEmphasis(true);
	$printer -> text($pedido->empresa_nombre."\n");
	$printer -> setEmphasis(false);
	$printer -> text("Fecha: ".date("d/m/Y")." Hora: ".date("H:i")."\n");
	$printer -> text($pedido->comprobante."\n");
	if (isset($pedido->titulo)) $printer -> text($pedido->titulo."\n");
	$printer -> text("Cliente: $cliente->nombre\n");
	if (!empty($cliente->direccion)) $printer -> text("Direccion: $cliente->direccion\n");
	if (!empty($cliente->telefono)) $printer -> text("Telefono: $cliente->telefono\n");
	if (isset($pedido->usuario)) $printer -> text("Atendido por:".$pedido->usuario."\n");
	$printer -> text($sep);
	foreach($pedido->items as $item) {
		$cantidad = str_pad(number_format($item->cantidad,1),6," ",STR_PAD_RIGHT);
		$precio = str_pad("$".number_format($item->total_con_iva),8," ",STR_PAD_LEFT);
		$dif = $lineas-6-8-2;
		if (strlen($item->nombre)>$dif) {
			$cant_lineas = ceil(strlen($item->nombre)/$dif);
			for($i=0;$i<$cant_lineas;$i++) {
				$nombre = substr($item->nombre,($i*$cant_lineas),$dif);
				$nombre = str_pad($nombre,$dif," ",STR_PAD_RIGHT);
				if ($i==0) $printer -> text("$cantidad $nombre $precio\n");
				else $printer -> text("       $nombre         \n");
			}
		} else {
			$nombre = str_pad($item->nombre,$dif," ",STR_PAD_RIGHT);
			$printer -> text("$cantidad $nombre $precio\n");
		}
	}
	$printer -> text($sep);
	$printer -> text("TOTAL: $ $pedido->total \n");
	$printer -> text($sep);
	if ($pedido->pagada == 1) {
		$printer -> text("PEDIDO PAGADO\n");
	}
	$printer -> text("\n\nDesarrollado por varcreative.com\n");
}
$printer -> text(" \n \n \n \n");
$printer -> cut();
$printer -> close();
echo json_encode(array(
	"error"=>0,
));
?>