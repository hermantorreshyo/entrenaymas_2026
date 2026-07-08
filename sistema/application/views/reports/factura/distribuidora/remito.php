<?php
$comprobante = ("REMITO");
$letra = "R";
$discrimina_iva = 0;

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
<title>Impresion de Remitos</title>
<style type="text/css">
#barra {}
<?php $cborde = "#a1a1a1"; ?>
.a4 {
  width: 210mm;
  height: 291mm;
  overflow: hidden;
  margin: 0 auto;
  background-color: white;
}
.a4inner { padding: 20px; }
.inner { padding: 0px; }
.inner.second { margin-top: 20px; }
body { font-family: Arial; font-size: 14px; background-color: #EEE; }
h1 { font-size: 20px; }
.borde { border: solid 1px <?php echo $cborde; ?>; overflow: hidden; }
.tac { text-align: center; }
.tar { text-align: right; }
.tal { text-align: left; }
.fl { float: left; }
.fr { float: right; }
p { margin-top: 0px; margin-bottom: 3px; }
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

.tabla { min-height: 320px; border: solid 1px <?php echo $cborde; ?> }
.tabla table { width: 100%; border-collapse: collapse; font-size: 13px; }
.tabla table .thead td { background-color: #e1e1e1 !important; padding: 4px !important; font-weight: bold !important; font-size: 9px; }
.tabla table td { padding: 3px 6px; vertical-align: top; }
table td { font-size: 11px; }

.totales { }
.totales > p { margin-bottom: 3px; margin-top: 3px;}
.totales > p > span { font-weight: bold; display: inline-block; text-align: right; width: 48%; }
.totales > p > span:first-child { font-weight: normal; text-align: right;  }
#total { font-weight: bold; font-size: 16px; border-top: solid 1px <?php echo $cborde; ?>; padding-top: 5px; padding-bottom: 0px }

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
  <!-- DISTRIBUIDORA / REMITO -->
  <div id="printable">

    <?php foreach($facturas as $factura) { ?>

      <div class="a4">
        <div class="a4inner">

        <?php
        $copias = $empresa->config["facturacion_cantidad_copias"];
        for($copia=0;$copia < $copias; $copia++) { 
          $class = "";
          if ($copia == 0) $class = "first";
          else if ($copia == 1) $class = "second";
          else if ($copia == 2) $class = "third";
          else if ($copia == 3) $class = "fourth";
          ?>

          <div class="inner <?php echo $class ?>">
            <table style="width: 100%">
              <tr><td style="width: 80%">
                <div class="borde" style="padding: 5px 10px">
                  <table style="width: 100%">
                    <tr>
                      <td style="vertical-align: top">
                        <div class="p10 pt0">
                          <!--
                          <?php if(!empty($empresa->logo)) { ?>
                            <div style="position: relative; top: -10px; left: -10px;">
                              <img src="/sistema/<?php echo $empresa->logo ?>"/>
                            </div>
                          <?php } ?>
                          -->
                          <div>
                            <?php if ($factura->id_punto_venta == 1647) { ?>
                              <b style="font-size: 16px">Sergio Di Piero</b><br/>
                              <p>Fortunato Diaz 1546. Lobos</p>
                              <p><?php echo $empresa->tipo_contribuyente; ?> - CUIT: <?php echo $empresa->cuit; ?></p>
                            <?php } else if (!(($empresa->id == 229 || $empresa->id == 230 || $empresa->id == 1355) && $letra == "R")) { ?>
                              <b style="font-size: 16px"><?php echo $empresa->razon_social?></b><br/>
                              <?php if (!empty($empresa->direccion)) { ?>
                                <p><?php echo $empresa->direccion ?> - <?php echo $empresa->localidad ?></p>
                              <?php } ?>
                              <p><?php echo $empresa->tipo_contribuyente; ?> - CUIT: <?php echo $empresa->cuit; ?></p>
                            <?php } ?>
                          </div>
                        </div>
                        <p>
                          <b>Cliente: </b><span><?php echo utf8_decode($factura->cliente->nombre); ?></span>
                          <b class="ml30">Venta: </b>
                          <span><?php echo ($factura->tipo_pago == "C") ? "Cta. Cte.":"Efectivo"; ?></span>
                        </p>
                        <?php if(!empty($factura->cliente->direccion)) { ?>
                          <p>
                            <b>Direccion: </b>
                            <span>
                              <?php echo ($factura->cliente->direccion); ?>
                              <?php /*if (!empty($factura->localidad)) { ?>
                                - <?php echo ucfirst(strtolower(($factura->localidad))); ?>
                                <?php echo (!empty($factura->codigo_postal)) ? " - CP: ".$factura->codigo_postal : ""; ?>
                              <?php }*/ ?>
                            </span>
                          </p>
                        <?php } ?>
                        <?php if (!empty($factura->cliente->cuit)) { ?>
                          <p>
                            <b>CUIT: </b><span><?php echo ($factura->cliente->cuit); ?></span>
                          </p>
                        <?php } ?>
                      </td>
                      <td style="vertical-align: top">
                        <div class="p10" style="margin-bottom: 0px; padding-bottom: 0px; text-align: right">
                          <h2 style="margin: 0px; padding: 0px;"><?php echo $comprobante; ?></h2>
                          <p><b>Numero: </b><?php 
                          $arr = explode(" ",$factura->comprobante);
                          echo (is_array($arr)) ? end($arr) : $arr ?></p>
                          <p><b>Fecha: <?php echo $factura->fecha; ?></b></p>
                          <h3 style="margin: 0px; padding: 0px;">
                            <?php if ($copia == 0) echo "ORIGINAL";
                            else if ($copia == 1) echo "DUPLICADO";
                            else if ($copia == 2) echo "TRIPLICADO";
                            else if ($copia == 3) echo "CUADRUPLICADO"; ?>
                          </h3>
                        </div>                
                      </td>
                    </tr>
                  </table>
                </div>
                <div class="tabla">
                  <table>
                    <tbody>
                      <tr class="thead">
                        <td style="width: 8%;">Cod.</td>
                        <td style="width: 8%;">Cant.</td>
                        <td style="width: 50%;">Descripcion</td>
                        <td style="width: 12%; text-align: right;">P. Unit</td>
                        <td style="width: 10%; text-align: right;">Dto.</td>
                        <td style="width: 12%; text-align: right;">Subtotal</td>
                      </tr>
                      <?php $total_neto = 0; $total_con_iva = 0; ?>
                      <?php foreach($factura->items as $i) { ?>
                        <tr>
                          <td class="tar"><?php echo $i->codigo; ?></td>
                          <td class="tar"><?php echo number_format($i->cantidad,2); ?></td>
                          <td>
                            <?php if (($empresa->id == 230 || $empresa->id == 229) && $i->cantidad < 0) { ?>
                              <?php echo "<b>DEVOLUCION: </b>"; ?>
                            <?php } ?>
                            <?php if ($i->tipo_cantidad == "C") { ?>
                              <?php echo "<b>CAMBIO: </b>"; ?>
                            <?php } ?>                            
                            <?php echo $i->nombre; ?>
                            <?php if (!empty($i->descripcion)) { ?>
                            <br/><span><?php echo $i->descripcion; ?></span>
                            <?php } ?>
                          </td>
                          <td class="tar">$ <?php echo number_format((($discrimina_iva==1)?$i->neto:$i->precio),2); ?></td>
                          <td class="tar"><?php echo ($i->bonificacion != 0) ? number_format($i->bonificacion,2)."%" : "" ?></td>
                          <td class="tar">$ <?php echo number_format((($discrimina_iva==1)?$i->total_sin_iva:$i->total_con_iva),2); ?></td>
                        </tr>
                        <?php $total_neto += $i->total_sin_iva; ?>
                        <?php $total_con_iva += $i->total_con_iva; ?>
                      <?php } ?>        
                    </tbody>
                  </table>
                </div>
            
                <div class="tabla" style="min-height: auto;">
                  <table>
                    <tbody>
                      <tr>
                        <td class="" style="width: 60%">
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
                            <div style="padding: 15px 20px; margin-bottom: 15px;">
                              <?php echo $obs ?>
                            </div>
                          <?php } ?>
                        </td>
                        <td style="padding: 0px; vertical-align: bottom; border-left: solid 1px <?php echo $cborde; ?>; width: 40%">
                          <div class="totales">
                            <p id="subtotal">
                              <span>SUBTOTAL:</span>
                              <span>$ <?php echo number_format($total_con_iva,2); ?></span>
                            </p>
                            <?php if ($factura->porc_descuento > 0) { ?>
                              <p id="descuento">
                                <span>DTO. <?php echo number_format($factura->porc_descuento,2) ?> %:</span>
                                <span>$ <?php echo number_format($factura->descuento,2) ?></span>
                              </p>
                              <p id="subtotal_descuento">
                                <span>SUBTOTAL:</span>
                                <span>$ <?php echo number_format($factura->subtotal,2); ?></span>
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
              </td>
                <td style="width: 20%">
                  <div class="borde" style="height: 102px; padding: 0px">
                    <table style="width: 100%">
                      <tr>
                        <td>
                          <div style="margin-bottom: 0px; padding: 5px 5px 0px 5px; text-align: right">
                            <?php if (!(($empresa->id == 229 || $empresa->id == 230 || $empresa->id == 1355) && $letra == "R")) { ?>
                              <b style="font-size: 10px"><?php echo $empresa->razon_social?></b><br/>
                            <?php } ?>
                            <h2 style="margin: 0px; padding: 0px;"><?php echo $comprobante; ?></h2>
                            <p><?php 
                            $arr = explode(" ",$factura->comprobante);
                            echo (is_array($arr)) ? end($arr) : $arr ?></p>
                            <p><b><?php echo $factura->fecha; ?></b></p>
                            <p><b><?php echo $factura->cliente->nombre; ?></b></p>
                            <?php if (!empty($factura->cliente->direccion)) { ?>
                              <p><span><?php echo ($factura->cliente->direccion); ?></span></p>
                            <?php } ?>
                          </div>
                        </td>
                      </tr>
                    </table>
                  </div>
                  <div class="tabla">
                    <table>
                      <tbody>
                        <tr class="thead">
                          <td>Cod.</td>
                          <td>Cant.</td>
                          <td style="text-align: right;">Subtotal</td>
                        </tr>
                        <?php foreach($factura->items as $i) { ?>
                          <tr>
                            <td class="tar"><?php echo $i->codigo; ?></td>
                            <td class="tar"><?php echo number_format($i->cantidad,2); ?></td>
                            <td class="tar">$ <?php echo number_format((($discrimina_iva==1)?$i->total_sin_iva:$i->total_con_iva),2); ?></td>
                          </tr>
                        <?php } ?>        
                      </tbody>
                    </table>
                  </div>
                  <div class="tabla" style="min-height: auto;">
                    <table>
                      <tbody>
                        <tr>
                          <td style="padding: 0px; vertical-align: bottom; border-left: solid 1px <?php echo $cborde; ?>; width: 30%">
                            <div class="totales">
                              <p id="subtotal">
                                <span>SUBTOTAL:</span>
                                <span class="tar">$ <?php echo number_format($factura->subtotal + $factura->descuento,2); ?></span>
                              </p>
                              <?php if ($factura->porc_descuento > 0) { ?>
                                <p id="descuento">
                                  <span><?php echo number_format($factura->porc_descuento,2) ?> %:</span>
                                  <span>$ <?php echo number_format($factura->descuento,2) ?></span>
                                </p>
                                <p id="subtotal_descuento">
                                  <span>SUBTOTAL:</span>
                                  <span class="tar">$ <?php echo number_format($factura->subtotal,2); ?></span>
                                </p>
                              <?php } ?>
                              <?php if ($discrimina_iva == 1) { ?>
                                <?php foreach($factura->ivas as $i) { ?>
                                  <?php if ($i->id_alicuota_iva == 3) continue; ?>
                                  <p id="iva">
                                    <span>IVA <?php echo mostrar_iva($i->id_alicuota_iva); ?>:</span>
                                    <span class="tar">$ <?php echo number_format($i->iva,2); ?></span>
                                  </p>
                                <?php } ?>
                              <?php } ?>
                              <p id="total">
                                <span class="tar" style="font-weight: bold; width: 100% !important">$ <?php echo number_format($factura->total,2); ?></span>
                              </p>
                            </div>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </td>
              </tr>
            </table>
          </div>
        <?php } ?>
        </div>
      </div>
    <?php } ?>
  </div>

<script type="text/javascript">
<?php //if ($factura->id_empresa == 229 || $factura->id_empresa == 230) { ?>
  window.print();
<?php //} ?>
</script>
</body>
</html>