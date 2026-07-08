<?php
$codigo_comprobante = "00"; //str_pad($factura->id_tipo_comprobante,2,"0",STR_PAD_LEFT);

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
<title><?php echo $titulo ?></title>
<style type="text/css">
#barra {}
<?php $cborde = "#a1a1a1"; ?>
.a4 {
  /*
  width: 210mm;
  height: 291mm;
  overflow: hidden;
  */
  margin: 0 auto;
  background-color: white;
}
@media only screen { .a4 { width: 100%; } }
.a4inner { padding: 20px; }
.inner { padding: 0px; }
.inner.second { margin-top: 20px; }
body { font-family: Arial; font-size: 15px; background-color: #EEE; }
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
.w55p { width: 55%; }
.w40p { width: 40%; }
.w30p { width: 30%; }
.w100p { width: 100%; }
.oh { overflow: hidden; }
.bold { font-weight: bold; }
.p20 { padding: 20px; }
.ml30 { margin-left: 30px; }
th { text-align: left; }

.tabla { min-height: 650px; border: solid 1px <?php echo $cborde; ?> }
.tabla table { width: 100%; border-collapse: collapse; font-size: 13px; }
.tabla table thead th { background-color: #e1e1e1; padding: 8px; }
.tabla table td { padding: 3px 8px; vertical-align: top; }
table td { font-size: 12px; }

.totales { }
.totales > p { margin-bottom: 3px; margin-top: 3px;}
.totales > p > span { font-weight: bold; display: inline-block; text-align: left; width: 48%; }
.totales > p > span:first-child { font-weight: normal; text-align: right;  }
#total { font-weight: bold; font-size: 16px; border-top: solid 1px <?php echo $cborde; ?>; padding-top: 5px; padding-bottom: 5px }

.cae_container { margin-top: 20px; }
.cae_container > p > span { text-align: left; margin-right: 10px; }
.cae_container > p > span:first-child { font-weight: bold;  }

.letra { position: relative; top: -21px; left: -56px; background-color: white; float: left; text-align: center; border: solid 1px <?php echo $cborde; ?>; }
.letra h1 { font-size: 42px; margin: 0px; padding: 10px 18px; border-bottom: solid 1px <?php echo $cborde; ?>; }
.letra .codigo_comprobante { font-size: 9px; margin-top: 3px; margin-bottom: 3px; }

.barcode { margin-top: 20px; font-size: 8px; text-align: center; }
.barcode > div { margin-bottom: 3px; }

@media print {
  body {-webkit-print-color-adjust: exact; }
  .inner.second { margin-top: 45px; }
  .inner { padding: 0px 0px 0px 0px; }
  .a4inner { padding: 0px; }
  .a4 { page-break-after: always; padding: 20px; }
  .a4:last-child { page-break-after: avoid; }
}
@page {
  size: auto;
  margin: 0px;
}
</style>
</head>
<body>
<?php echo $header; ?>
<!-- FACTURA/BASICO/REMITO.PHP -->
<div id="printable">
  <?php foreach($facturas as $factura) { 

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
        $comprobante = ("PRESUPUESTO"); $letra = "R"; $discrimina_iva = 0; break;
      case 999:
        $comprobante = ("REMITO"); $letra = "R"; $discrimina_iva = 0; break;
    }
    $discrimina_iva = 0;

    // Si el cliente es de IMPORT SHOW, y tiene la etiqueta SINDICATO, que aparezca eso
    if ($factura->id_empresa == 356 && isset($factura->cliente->etiquetas) && isset($factura->cliente->etiquetas[0]) && strtolower($factura->cliente->etiquetas[0]) == "sindicato" ) {
      $comprobante = "SINDICATO";
      $letra = "X";
    }
    
    // Dependiendo la cantidad de productos que tiene la factura
    $piezas_items = array_chunk($factura->items, 27);

    $nro_pieza = 1;
    foreach($piezas_items as $items) { ?>
      <div class="a4">
        <div class="a4inner">
          <div class="inner">
            <div class="borde" style="padding: 10px">
              <table style="width: 100%">
                <tr>
                  <td>
                    <div class="p10 pt0">
                      <h2 style="margin-top: 0px; padding-top: 0px;"><?php echo $comprobante; ?></h2>
                      <?php if(!empty($empresa->logo) && $empresa->id != 249) { ?>
                        <div style="position: relative; top: -10px; left: -10px;">
                          <img src="/sistema/<?php echo $empresa->logo ?>"/>
                        </div>
                      <?php } ?>
                      <div>
                        <p><b><?php echo $empresa->razon_social?></b></p>
                        <?php if (!empty($empresa->direccion)) { ?>
                          <p><?php echo $empresa->direccion ?> - <?php echo $empresa->localidad ?></p>
                        <?php } ?>
                      </div>
                    </div>
                  </td>
                  <td>
                    <div class="p10" style="margin-bottom: 0px; padding-bottom: 0px; text-align: right">
                      
                      <p><b>Numero: </b>
                        <?php 
                        $exp = explode(" ",$factura->comprobante);
                        echo end($exp); ?>
                      </p>
                      <p><b>Fecha: <?php echo $factura->fecha; ?></b></p>
                    </div>                
                  </td>
                </tr>
              </table>
              <div style="margin-left: 4px;">
                <p>
                  <b>Cliente: </b><span><?php echo ($factura->cliente->nombre); ?></span>
                  <?php if ($empresa->id != 249 && $empresa->id != 224 && $empresa->id != 421 && $empresa->id != 1325) { ?>
                    <b class="ml30">Condicion de Venta: </b>
                    <span>
                      <?php echo ($factura->tipo_pago == "C") ? "Cuenta Corriente":""; ?>
                      <?php echo ($factura->tipo_pago == "E") ? "Efectivo":""; ?>
                      <?php echo ($factura->tipo_pago == "T") ? "Tarjeta":""; ?>
                      <?php echo ($factura->tipo_pago == "B") ? "Banco":""; ?>
                      <?php echo ($factura->tipo_pago == "H") ? "Cheque":""; ?>
                      <?php echo ($factura->tipo_pago == "O") ? "Otro":""; ?>
                    </span>
                  <?php } ?>
                </p>
                <p>
                  <?php if(!empty($factura->direccion)) { ?>
                    <b>Direccion: </b>
                    <span>
                      <?php echo ($factura->direccion); ?>
                      <?php if (!empty($factura->localidad)) { ?>
                        - <?php echo ($factura->localidad); ?>
                      <?php } ?>
                    </span>
                  <?php } ?>
                </p>
              </div>          
            </div>
            <div class="tabla">
              <table>
                <thead>
                  <tr>
                    <th style="width: 10%;">Cantidad</th>
                    <th style="width: 10%;">Cod.</th>
                    <th style="width: 50%;">Descripcion</th>
                    <th style="width: 15%;">Unitario</th>
                    <th style="width: 15%;">Subtotal</th>
                  </tr>
                </thead>
                <tbody>
                  <?php 
                  $total_neto = 0; $total_con_iva = 0;
                  foreach($items as $i) { ?>
                    <?php if ($i->anulado == 0) { ?>
                      <?php if ($i->id_articulo != 0 && $i->cantidad == 0 && ($i->tipo_cantidad == "" || $i->tipo_cantidad == "X")) continue; ?>
                      <tr>
                        <td><?php echo number_format($i->cantidad,2); ?></td>
                        <td><?php 
                        if (!empty($i->custom_1)) {
                          echo $i->custom_1;
                        } else if (isset($i->codigo_barra)) {
                          $codigos = explode("###", $i->codigo_barra);
                          echo $codigos[0];
                        }
                        ?></td>
                        <td>
                          <?php echo ($i->tipo_cantidad == "B")?"<b>Bonificado: </b>":"" ?>
                          <?php echo $i->nombre; ?>
                          <?php echo ((isset($i->variante) && !empty($i->variante))?"<br/><span>".($i->variante)."</span>":""); ?>
                          <?php if (!empty($i->descripcion)) { ?>
                            <br/><span><?php echo $i->descripcion; ?></span>
                          <?php } ?>
                        </td>
                        <td>$ <?php echo number_format((($discrimina_iva==1)?$i->neto:$i->precio),2); ?></td>
                        <td>$ <?php echo number_format((($discrimina_iva==1)?$i->total_sin_iva:$i->total_con_iva),2); ?></td>
                      </tr>
                      <?php $total_neto += $i->total_sin_iva; ?>
                      <?php $total_con_iva += $i->total_con_iva; ?>
                    <?php } ?>
                  <?php } ?>
                </tbody>
              </table>
            </div>
            
            <div class="tabla" style="min-height: auto;">
              <table>
                <tbody>
                  <tr>
                    <td class="" style="width: 70%">

                      <?php if ($factura->efectivo > 0) { ?>
                        <p><span>Efectivo:</span> <span><?php echo number_format($factura->efectivo - $factura->vuelto,2) ?></span></p>
                      <?php } ?>
                      <?php if ($factura->tarjeta > 0) { ?>
                        <p><span>Tarjeta:</span> <span><?php echo number_format($factura->tarjeta,2) ?></span></p>
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
                    </td>
                    <td style="padding: 0px; vertical-align: bottom; border-left: solid 1px <?php echo $cborde; ?>; width: 30%">
                      <div class="totales">
                        <?php if ($discrimina_iva) { ?>
                          <p id="subtotal">
                            <span>SUBTOTAL NETO:</span>
                            <span>$ <?php echo number_format($total_neto,2); ?></span>
                          </p>
                        <?php } else { ?>
                          <p id="subtotal">
                            <span>SUBTOTAL:</span>
                            <span>$ <?php echo number_format($total_con_iva,2); ?></span>
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
                        <?php if ($factura->interes > 0) { ?>
                          <p id="descuento">
                            <span>RECARGO:</span>
                            <span>$ <?php echo number_format($factura->interes,2) ?></span>
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
                        <p id="total">
                          <span>TOTAL:</span>
                          <span>$ <?php echo number_format($factura->total,2); ?></span>
                        </p>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>    
        </div>
      </div>
    <?php } ?>
  <?php } ?>
</div>
</body>
</html>