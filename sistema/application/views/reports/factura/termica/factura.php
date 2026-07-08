<?php
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
  case 51:
    $comprobante = ("FACTURA"); $letra = "M"; $discrimina_iva = 0; break;
  case 52:
    $comprobante = ("NOTA DE D&Eacute;BITO"); $letra = "M"; $discrimina_iva = 0; break;
  case 53:
    $comprobante = ("NOTA DE CR&Eacute;DITO"); $letra = "M"; $discrimina_iva = 0; break;
  case 201:
    $comprobante = ("FACTURA MiPyME"); $letra = "A"; $discrimina_iva = 1; break;
  case 202:
    $comprobante = ("NOTA DE D&Eacute;BITO"); $letra = "A"; $discrimina_iva = 1; break;
  case 203:
    $comprobante = ("NOTA DE CR&Eacute;DITO"); $letra = "A"; $discrimina_iva = 1; break;
  case 206:
    $comprobante = ("FACTURA MiPyME"); $letra = "B"; $discrimina_iva = 0; break;
  case 207:
    $comprobante = ("NOTA DE D&Eacute;BITO"); $letra = "B"; $discrimina_iva = 0; break;
  case 208:
    $comprobante = ("NOTA DE CR&Eacute;DITO"); $letra = "B"; $discrimina_iva = 0; break;
  case 998:
    $comprobante = ("PRESUPUESTO"); $letra = "R"; $discrimina_iva = 0; break;
  case 999:
    $comprobante = ("REMITO"); $letra = "R"; $discrimina_iva = 0; break;
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

<style type="text/css">
#barra {}
<?php $cborde = "#a1a1a1"; ?>
.inner { padding: 5px; }
body { font-family: Arial; font-size: 14px; background-color: #EEE; }
h1 { font-size: 20px; }
.borde { border: solid 1px <?php echo $cborde; ?>; overflow: hidden; }
.tac { text-align: center; }
.tar { text-align: right; }
.tal { text-align: left; }
.fl { float: left; }
.fr { float: right; }
p { margin-top: 3px; margin-bottom: 5px; }
.w60p { width: 60%; }
.w50p { width: 50%; }
.w40p { width: 40%; }
.w100p { width: 100%; }
.oh { overflow: hidden; }
.bold { font-weight: bold; }
.p20 { padding: 20px; }
.ml30 { margin-left: 30px; }
.tabla { padding: 10px 0px; margin: 10px 0px; border-top: solid 1px <?php echo $cborde; ?>; border-bottom: solid 1px <?php echo $cborde; ?>; }
#total { font-weight: bold; font-size: 16px; margin-top: 15px; border-top: solid 1px <?php echo $cborde; ?>; padding-top: 15px; }
.barcode { margin-top: 20px; font-size: 8px; text-align: center; }
.barcode > div { margin-bottom: 3px; }
</style>
</head>

<?php
// Dependiendo la cantidad de productos que tiene la factura
$piezas_items = (sizeof($factura->items) > 0) ? array_chunk($factura->items, 27) : array($factura->items);
?>
<body style="background-color: white;">
  <?php echo $header; ?>
  <!-- FACTURA/BASICO/FACTURA.PHP -->
  <div id="printable">
    <?php
    $copias = $empresa->config["facturacion_cantidad_copias"];
    for($copia=0;$copia < $copias; $copia++) { 

      $nro_pieza = 1;
      foreach($piezas_items as $items) { ?>
        <div class="">
          <div class="inner">
            <div class="">
              <div>
                <h2 style="margin: 5px 0px; padding: 0px; font-size: 14px;"><?php echo $factura->comprobante; ?></h2>
                <?php if ($factura->id_tipo_comprobante < 900) { ?>
                  <div style="font-size: 12px" class="codigo_comprobante">COD. <?php echo $codigo_comprobante; ?></div>
                <?php } ?>
                <p><?php echo $factura->fecha; ?></p>
              </div>
              <div class="">
                <p class="bold"><?php echo $empresa->razon_social?></p>
                <?php if (!empty($empresa->direccion)) { ?>
                  <p><?php echo $empresa->direccion ?></p>
                <?php } ?>
                <p><?php echo $empresa->cuit; ?></p>
                <?php if (!empty($empresa->config["numero_ib"])) { ?>
                  <p><b>Ingresos Brutos: </b><?php echo $empresa->config["numero_ib"]; ?></p>
                <?php } ?>
                <?php
                $fecha_inicio = $empresa->config["fecha_inicio"];
                if ($fecha_inicio != "0000-00-00" && $fecha_inicio != "00/00/0000") { ?>
                  <p><b>Inicio de Actividades: </b> <?php echo $fecha_inicio ?></p>
                <?php } ?>
                <p><b>
                <?php
                switch($empresa->id_tipo_contribuyente) {
                  case 1: echo "IVA RESPONSABLE INSCRIPTO"; break;
                  case 2: echo "MONOTRIBUTO"; break;
                  case 3: echo "IVA EXENTO"; break;
                }
                ?>
                </b></p>            
              </div>
              
            </div>
            <div style="padding-top: 10px; border-top: solid 1px black; margin: 5px 0px;">
              <p>
                <b>Cliente: </b>
                <br/><span><?php echo utf8_decode($factura->cliente->nombre); ?></span>
              </p>
              <?php if(!empty($factura->cliente->cuit)) { ?>
                <p>CUIT: </b><span><?php echo $factura->cliente->cuit; ?></span></p>
              <?php } ?>
              <?php if (!empty($factura->cliente->direccion)) { ?>
                <p>
                  <b>Domicilio: </b> <span><?php echo utf8_decode($factura->cliente->direccion); ?>
                  <?php if (!empty($factura->cliente->localidad)) { ?>
                    - <?php echo utf8_decode($factura->cliente->localidad); ?>
                  <?php } ?>
                  </span>
                </p>
              <?php } ?>
              <p>
                <b>Condici&oacute;n IVA: </b> <span><?php echo $factura->cliente->tipo_iva; ?></span>
              </p>
            </div>
            <div class="tabla">
              <?php 
              $total_neto = 0; $total_con_iva = 0;
              foreach($items as $i) { ?>
                <?php if ($i->anulado == 0) { ?>
                  <?php if ($i->id_articulo != 0 && $i->cantidad == 0 && ($i->tipo_cantidad == "" || $i->tipo_cantidad == "X")) continue; ?>
                  <p>
                    <?php echo ($i->tipo_cantidad == "B")?"<b>Bonificado: </b>":"" ?>
                    <?php echo $i->nombre; ?>
                    <?php echo ((isset($i->variante) && !empty($i->variante))?"<br/><span>".($i->variante)."</span>":""); ?>
                    <?php if (!empty($i->descripcion)) { ?>
                      <br/><span><?php echo $i->descripcion; ?></span>
                    <?php } ?>
                  </p>
                  <p>
                    Cant: <?php echo number_format($i->cantidad,2); ?> | Unit: $ <?php echo number_format((($discrimina_iva==1)?$i->neto:$i->precio),2); ?>
                    | Subtotal: $ <?php echo number_format((($discrimina_iva==1)?$i->total_sin_iva:$i->total_con_iva),2); ?>
                  </p>
                  <?php $total_neto += $i->total_sin_iva; ?>
                  <?php $total_con_iva += $i->total_con_iva; ?>
                <?php } ?>
              <?php } ?>
            </div>

            <?php if ($nro_pieza == sizeof($piezas_items)) { ?>
              <div>
                <?php if (!empty($factura->cae)) { ?>
                  <p>
                    <span>C.A.E.:</span>
                    <span><?php echo $factura->cae ?></span>
                  </p>
                  <p>
                    <span>Fecha Vto. de CAE:</span>
                    <span><?php echo ($factura->fecha_vto); ?></span>
                  </p>
                <?php } ?>
                <?php if ($factura->efectivo > 0) { ?>
                  <?php if ($factura->tipo_pago == "O") { ?>
                    <p><span>Forma Pago: Otro</span> <span><?php echo number_format($factura->efectivo - $factura->vuelto,2) ?></span></p>
                  <?php } else { ?>
                    <p><span>Efectivo: </span> <span>$ <?php echo number_format($factura->efectivo,2) ?></span></p>
                    <p><span>Vuelto: </span> <span>$ <?php echo number_format($factura->vuelto,2) ?></span></p>
                  <?php } ?>
                <?php } ?>
                <?php if ($factura->tarjeta > 0) { ?>
                  <?php if ($factura->tipo_pago == "B") { ?>
                    <p><span>Banco:</span> <span><?php echo number_format($factura->tarjeta,2) ?></span></p>
                  <?php } else { ?>
                    <p><span>Tarjeta:</span> <span><?php echo number_format($factura->tarjeta,2) ?></span></p>
                  <?php } ?>
                <?php } ?>
                <?php if ($factura->cta_cte > 0) { ?>
                  <p><span>Cta. Cte.:</span> <span><?php echo number_format($factura->cta_cte,2) ?></span></p>
                <?php } ?>
                <?php if ($factura->cheque > 0) { ?>
                  <p><span>Cheque:</span> <span><?php echo number_format($factura->cheque,2) ?></span></p>
                <?php } ?>

                <?php if (!empty($factura->observaciones)) {
                  // Analizamos las observaciones
                  $obs = (nl2br($factura->observaciones));
                  $obs = str_replace("{{COTIZACION_DOLAR}}",$factura->cotizacion_dolar,$obs);
                  $obs = str_replace("{{TOTAL_EN_LETRAS}}",strtoupper($letras->ValorEnLetras(round($factura->total,2),"PESOS")),$obs);
                  if ($factura->cotizacion_dolar == 0) {
                    $obs = str_replace("{{TOTAL_EN_DOLARES}}","",$obs);
                    $obs = str_replace("{{TOTAL_EN_DOLARES_EN_LETRAS}}","",$obs);
                  } else {
                    $total_dolares = round($factura->total / $factura->cotizacion_dolar,2);
                    $obs = str_replace("{{TOTAL_EN_DOLARES}}",$total_dolares,$obs);
                    $obs = str_replace("{{TOTAL_EN_DOLARES_EN_LETRAS}}",strtoupper($letras->ValorEnLetras($total_dolares,"DOLARES")),$obs);
                  }
                  ?>
                  <p><?php echo $obs ?></p>
                <?php } ?>
                <div class="totales">
                  <?php if ($discrimina_iva) { ?>
                    <p id="subtotal">
                      <span>SUBTOTAL NETO:</span>
                      <span>$ <?php echo number_format($total_neto,2); ?></span>
                    </p>
                  <?php } ?>
                  <?php if ($factura->porc_descuento > 0) { ?>
                    <p id="descuento">
                      <span>DTO. <?php echo number_format($factura->porc_descuento,2) ?> %:</span>
                      <span>$ <?php echo number_format($factura->descuento,2) ?></span>
                    </p>
                  <?php } else if ($factura->descuento > 0) { ?>
                    <p id="descuento">
                      <span>DESCUENTO:</span>
                      <span>$ <?php echo number_format($factura->descuento,2) ?></span>
                    </p>
                  <?php } ?>
                  <?php if ($discrimina_iva == 1) { ?>
                    <?php foreach($factura->ivas as $i) { ?>
                      <p id="iva">
                        <span>IVA <?php echo mostrar_iva($i->id_alicuota_iva); ?>:</span>
                        <span>$ <?php echo number_format($i->iva,2); ?></span>
                      </p>
                    <?php } ?>
                  <?php } ?>
                  <?php if ($factura->interes > 0) { ?>
                    <p id="descuento">
                      <span>RECARGO TARJETA:</span>
                      <span>$ <?php echo number_format($factura->interes,2) ?></span>
                    </p>
                  <?php } ?>
                  <p id="total">
                    <span>TOTAL:</span>
                    <span>$ <?php echo number_format($factura->total,2); ?></span>
                  </p>
                </div>
              </div>

              <?php if ($empresa->id == 1354) { ?>
                <p>
                  <span>Retiro:</span>
                  <span><?php echo $factura->fecha_vto ?></span>
                </p>
                <p>
                  <span>Entrega:</span>
                  <span><?php echo $factura->fecha_reparto ?></span>
                </p>
              <?php } else { ?>
                <div class="barcode">
                  <div><img src="/sistema/application/helpers/barcode.php?text=<?php echo $barcode; ?>" /></div>
                  <div><?php echo $barcode ?></div>
                </div>
              <?php } ?>
            <?php } ?>

          </div>
        </div>
      <?php $nro_pieza++; } ?>
    <?php } ?>
  </div>
</body>
</html>