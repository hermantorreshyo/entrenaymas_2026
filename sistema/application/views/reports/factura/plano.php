<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$codigo_comprobante = str_pad($factura->id_tipo_comprobante,2,"0",STR_PAD_LEFT);

$letra = "R";
$discrimina_iva = 1;
switch ($factura->id_tipo_comprobante) {
  case 1:
    $comprobante = ("FACTURA"); $letra = "A"; $discrimina_iva = 1; break;
  case 2:
    $comprobante = ("NOTA DE D&Eacute;BITO"); $letra = "A"; $discrimina_iva = 1; break;
  case 3:
    $comprobante = ("NOTA DE CR&Eacute;DITO"); $letra = "A"; $discrimina_iva = 1; break;
  case 4:
    $comprobante = ("RECIBO"); $letra = "A"; $discrimina_iva = 1; break;
  case 6:
    $comprobante = ("FACTURA"); $letra = "B"; $discrimina_iva = 0; break;
  case 7:
    $comprobante = ("NOTA DE D&Eacute;BITO"); $letra = "B"; $discrimina_iva = 0; break;
  case 8:
    $comprobante = ("NOTA DE CR&Eacute;DITO"); $letra = "B"; $discrimina_iva = 0; break;
  case 9:
    $comprobante = ("RECIBO"); $letra = "B"; $discrimina_iva = 0; break;
  case 11:
    $comprobante = ("FACTURA"); $letra = "C"; $discrimina_iva = 0; break;
  case 12:
    $comprobante = ("NOTA DE D&Eacute;BITO"); $letra = "C"; $discrimina_iva = 0; break;
  case 13:
    $comprobante = ("NOTA DE CR&Eacute;DITO"); $letra = "C"; $discrimina_iva = 0; break;
  case 15:
    $comprobante = ("RECIBO"); $letra = "C"; $discrimina_iva = 0; break;
  case 998:
    $comprobante = ("PRESUPUESTO"); $letra = "X"; $discrimina_iva = 0; break;
  case 999:
    $comprobante = ("REMITO"); $letra = "X"; $discrimina_iva = 0; break;
}

// Si el cliente es de IMPORT SHOW, y tiene la etiqueta SINDICATO, que aparezca eso
if ($factura->id_empresa == 356 && isset($factura->cliente->etiquetas) && isset($factura->cliente->etiquetas[0]) && strtolower($factura->cliente->etiquetas[0]) == "sindicato" ) {
  $comprobante = "SINDICATO"; $letra = "X";
}

function mostrar_iva($id) {
  switch($id) {
    case 3: return "EXENTO";
    case 4: return "10.50%";
    case 5: return "21.00%";
    case 6: return "27.00%";
    case 8: return "5.00%";
    case 9: return "2.50%";
  }
  return "";
}
?>
<!DOCTYPE>
<html>
<head>
<title><?php echo $comprobante." ".$factura->comprobante ?></title>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
</head>
<body>
<?php echo $header; ?>
<!-- FACTURA/PLANO.PHP -->
Fecha: <?php echo $factura->fecha; ?><br/>
Cliente: <?php echo utf8_decode($factura->cliente->nombre); ?> <br/>
<?php echo $factura->comprobante; ?><br/>
----------------------<br/>
<?php foreach($factura->items as $i) { ?>
  <?php if ($i->id_articulo != 0 && $i->cantidad == 0 && ($i->tipo_cantidad == "" || $i->tipo_cantidad == "X")) continue; ?>
  Cant. <?php echo $i->cantidad ?> | P Unit. $ <?php echo number_format((($discrimina_iva==1)?$i->neto:$i->precio),2); ?>
  | Subtotal: $ <?php echo number_format((($discrimina_iva==1)?$i->total_sin_iva:$i->total_con_iva),2); ?><br/>
  <?php echo $i->nombre; ?><br/>
<?php } ?>
----------------------<br/>
Total: <?php echo $factura->total; ?>
</body>
</html>