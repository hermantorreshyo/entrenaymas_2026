<?php
error_reporting(0);
ini_set('display_errors', 0);

$copia = 0;
function ver_comprobante($id_tipo_comprobante) {
  $comprobante = "";
  switch ($id_tipo_comprobante) {
    case 1:
      $comprobante = ("FACTURA"); break;
    case 2:
      $comprobante = ("NOTA DE D&Eacute;BITO"); break;
    case 3:
      $comprobante = ("NOTA DE CR&Eacute;DITO"); break;
    case 4:
      $comprobante = ("RECIBO"); break;
    case 6:
      $comprobante = ("FACTURA"); break;
    case 7:
      $comprobante = ("NOTA DE D&Eacute;BITO"); break;
    case 8:
      $comprobante = ("NOTA DE CR&Eacute;DITO"); break;
    case 9:
      $comprobante = ("RECIBO"); break;
    case 11:
      $comprobante = ("FACTURA"); break;
    case 12:
      $comprobante = ("NOTA DE D&Eacute;BITO"); break;
    case 13:
      $comprobante = ("NOTA DE CR&Eacute;DITO"); break;
    case 15:
      $comprobante = ("RECIBO"); break;
    case 998:
      $comprobante = ("PRESUPUESTO"); break;
    case 999:
      $comprobante = ("REMITO"); break;
  }
  return $comprobante;
}

function discrimina_iva($id_tipo_comprobante) {
  return ($id_tipo_comprobante < 6) ? 1 : 0;
}

function ver_letra($id_tipo_comprobante) {
  if ($id_tipo_comprobante < 6) return "A";
  else if ($id_tipo_comprobante < 11) return "B";
  else if ($id_tipo_comprobante < 16) return "C";
  else return "R";
}

function mostrar_iva($id) {
  switch($id) {
    case 3: return "0%";
    case 4: return "10.5%";
    case 5: return "21%";
    case 6: return "27%";
    case 8: return "5%";
    case 9: return "2.5%";
  }
  return "";
}
?>
<!DOCTYPE>
<html>
<head>
<title>Impresion de comprobantes</title>
<script type="text/javascript" src="/sistema/resources/js/jquery.js"></script>
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
.tabla table thead th { background-color: #e1e1e1; padding: 4px; font-size: 9px; }
.tabla table td { padding: 3px 6px; vertical-align: top; }
table td { font-size: 11px; }

.totales { }
.totales > p { margin-bottom: 3px; margin-top: 3px;}
.totales > p > span { font-weight: bold; display: inline-block; text-align: right; width: 48%; }
.totales > p > span:first-child { font-weight: normal; text-align: right;  }
#total { font-weight: bold; font-size: 16px; border-top: solid 1px <?php echo $cborde; ?>; padding-top: 5px; padding-bottom: 0px }

.cae_container > p > span { text-align: left; margin-right: 10px; }
.cae_container > p > span:first-child { font-weight: bold;  }

.letra { position: relative; top: -21px; background-color: white; float: left; text-align: center; border: solid 1px <?php echo $cborde; ?>; }
.letra h1 { font-size: 42px; margin: 0px; padding: 10px 18px; border-bottom: solid 1px <?php echo $cborde; ?>; }
.letra .codigo_comprobante { font-size: 9px; margin-top: 3px; margin-bottom: 3px; }

.barcode { font-size: 8px; text-align: center; }
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
<!-- DISTRIBUIDORA / FACTURA -->
<div id="printable">
  <?php foreach($facturas as $factura) { 

    // Le sacamos el IVA para no imprimirlo
    $discrimina_iva = discrimina_iva($factura->id_tipo_comprobante);
    $letra = ver_letra($factura->id_tipo_comprobante);
    $comprobante = ver_comprobante($factura->id_tipo_comprobante);
    $factura->iva = ($discrimina_iva == 1) ? $factura->iva : 0;
    ?>
    <div class="a4">
      <div class="a4inner">
        <div class="inner first">
          <table style="width: 100%">
            <tr><td style="width: 80%">
              <div class="borde" style="padding: 5px 10px">
                <table style="width: 100%">
                  <tr>
                    <td style="width: 60%; vertical-align: top">
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
                            <b style="font-size: 16px"><?php echo $empresa->razon_social?></b><br/>
                            <p>Fortunato Diaz 1546. Lobos</p>
                            <p><?php echo $empresa->tipo_contribuyente; ?> - CUIT: <?php echo $empresa->cuit; ?></p>
                          <?php } else if (!(($empresa->id == 229 || $empresa->id == 230 || $empresa->id == 1355) && $letra == "X")) { ?>
                            <b style="font-size: 16px"><?php echo $empresa->razon_social?></b><br/>
                            <?php if (!empty($empresa->direccion)) { ?>
                              <p><?php echo $empresa->direccion ?> - <?php echo $empresa->localidad ?></p>
                            <?php } ?>
                            <p><?php echo $empresa->tipo_contribuyente; ?> - CUIT: <?php echo $empresa->cuit; ?></p>
                          <?php } ?>
                        </div>
                      </div>
                      <p>
                        <b>Cliente: </b><span><?php echo utf8_decode($factura->cliente->nombre).((!empty($factura->cliente->codigo)) ? " (".($factura->cliente->codigo).")" : ""); ?></span>
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
                      <?php if (!empty($factura->numero_remito)) { ?>
                        <p>
                          <b>Remito Nro: </b>
                          <span><?php echo $factura->numero_remito ?></span>
                        </p>
                      <?php } ?>
                    </td>
                    <td style="width: 40%; vertical-align: top">
                      <div class="p10" style="margin-bottom: 0px; padding-bottom: 0px; text-align: right">
                        <div class="letra">
                          <h1><?php echo $letra; ?></h1>
                          <div class="codigo_comprobante">COD. <?php echo str_pad($factura->id_tipo_comprobante,2,"0",STR_PAD_LEFT); ?></div>
                        </div>
                        <p><b><?php echo ver_comprobante($factura->comprobante); ?></b></p>
                        <p><b>Numero:</b><?php 
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
                  <thead>
                    <tr>
                      <th style="width: 8%;">Cod.</th>
                      <th style="width: 8%;">Cant.</th>
                      <th style="width: 50%;">Descripcion</th>
                      <th style="width: 12%; text-align: right;">P. Unit</th>
                      <?php if ($discrimina_iva == 1) { ?>
                        <th style="width: 5%; text-align: right;">IVA</th>
                      <?php } ?>
                      <th style="width: <?php echo ($discrimina_iva==1)?"5":"10" ?>%; text-align: right;">Dto.</th>
                      <th style="width: 12%; text-align: right;">Subtotal</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php 
                    $subtotal = 0;
                    foreach($factura->items as $i) { ?>
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
                          <?php if ($i->id_tipo_alicuota_iva == 3) { ?>
                            (IVA 0%)
                          <?php } ?>                          
                          <?php if (!empty($i->descripcion)) { ?>
                          <br/><span><?php echo $i->descripcion; ?></span>
                          <?php } ?>
                        </td>
                        <td class="tar">$ <?php echo number_format((($discrimina_iva==1)?$i->neto:$i->precio),2); ?></td>
                        <?php if ($discrimina_iva == 1) { ?>
                          <td class="tar"><?php echo $i->porc_iva ?></td>
                        <?php } ?>                        
                        <td class="tar"><?php echo ($i->bonificacion != 0) ? number_format($i->bonificacion,2)."%" : "" ?></td>
                        <td class="tar">$ <?php echo number_format((($discrimina_iva==1)?$i->total_sin_iva:$i->total_con_iva),2); ?></td>
                      </tr>
                      <?php $subtotal += ($discrimina_iva==1)?$i->total_sin_iva:$i->total_con_iva; ?>
                    <?php } ?>        
                  </tbody>
                </table>
              </div>
          
              <div id="footer_tabla_1" class="tabla" style="min-height: auto;">
                <table>
                  <tbody>
                    <tr>
                      <td class="" style="width: 60%">
                        <?php 
                        $obs = "";
                        if (!empty($factura->observaciones)) {
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
                        <?php } ?>
                        <div style="padding: 5px 10px; ">
                          <?php ?>
                          <?php echo $obs ?>
                          <?php if (!empty($factura->cae)) { ?>
                            <div class="cae_container">
                              <p>
                                <span>C.A.E.:</span>
                                <span><?php echo $factura->cae ?></span>
                              </p>
                              <p>
                                <span>Fecha Vto. de CAE:</span>
                                <span><?php echo ($factura->fecha_vto); ?></span>
                              </p>
                            </div>
                          <?php } ?>
                        </div>

                        <div class="barcode">
                          <div><img style="width: 100%" src="/sistema/application/helpers/barcode.php?text=<?php echo $factura->barcode; ?>" /></div>
                          <div><?php echo $factura->barcode ?></div>
                        </div>
                      </td>
                      <td style="padding: 0px; vertical-align: bottom; border-left: solid 1px <?php echo $cborde; ?>; width: 40%">
                        <div class="totales">
                          <p id="subtotal">
                            <span>SUBTOTAL:</span>
                            <span>$ <?php echo number_format($subtotal,2); ?></span>
                          </p>
                          <?php if ($factura->porc_descuento > 0) { ?>
                            <p id="descuento">
                              <span>DTO. <?php echo number_format($factura->porc_descuento,2) ?> %:</span>
                              <span>$ <?php echo number_format($factura->descuento,2) ?></span>
                            </p>
                            <p id="subtotal_descuento">
                              <span>SUBTOTAL:</span>
                              <span>$ <?php echo number_format($factura->total - $factura->iva,2); ?></span>
                            </p>
                          <?php } ?>
                          <?php if ($discrimina_iva == 1) { ?>
                            <?php foreach($factura->ivas as $i) { ?>
                              <p id="iva">
                                <span>$ <?php echo number_format($i->neto,2); ?> (<?php echo mostrar_iva($i->id_alicuota_iva); ?>):</span>
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
              <td style="width: 20%; vertical-align: top; ">
                <div class="borde" style="height: 102px; padding: 0px">
                  <table style="width: 100%">
                    <tr>
                      <td>
                        <div style="margin-bottom: 0px; padding: 5px 5px 0px 5px; text-align: right">
                          <?php if (!(($empresa->id == 229 || $empresa->id == 230 || $empresa->id == 1355) && $letra == "X")) { ?>
                            <b style="font-size: 10px"><?php echo $empresa->razon_social?></b><br/>
                          <?php } ?>
                          <h2 style="margin: 0px; padding: 0px;"><?php echo ver_comprobante($factura->comprobante); ?></h2>
                          <p><?php 
                          $arr = explode(" ",$factura->comprobante);
                          echo (is_array($arr)) ? end($arr) : $arr ?></p>
                          <p><b><?php echo $factura->fecha; ?></b></p>
                          <p><span><?php echo utf8_decode($factura->cliente->nombre); ?></span></p>
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
                    <thead>
                      <tr>
                        <th>Cod.</th>
                        <th>Cant.</th>
                        <th style="text-align: right;">Subtotal</th>
                      </tr>
                    </thead>
                    <tbody>
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
                        <td id="footer_tabla_2" style="padding: 0px; vertical-align: bottom; border-left: solid 1px <?php echo $cborde; ?>; width: 30%; ">
                          <div class="totales">
                            <p id="subtotal">
                              <span>SUBTOTAL:</span>
                              <span class="tar">$ <?php echo number_format($subtotal,2); ?></span>
                            </p>
                            <?php if ($factura->porc_descuento > 0) { ?>
                              <p id="descuento">
                                <span><?php echo number_format($factura->porc_descuento,2) ?> %:</span>
                                <span>$ <?php echo number_format($factura->descuento,2) ?></span>
                              </p>
                              <p id="subtotal_descuento">
                                <span>SUBTOTAL:</span>
                                <span class="tar">$ <?php echo number_format($factura->total - $factura->iva,2); ?></span>
                              </p>
                            <?php } ?>
                            <?php if ($discrimina_iva == 1) { ?>
                              <?php foreach($factura->ivas as $i) { ?>
                                <p id="iva">
                                  <span>$ <?php echo number_format($i->neto,2); ?> (<?php echo mostrar_iva($i->id_alicuota_iva); ?>):</span>
                                  <span>$ <?php echo number_format($i->iva,2); ?></span>
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
      </div>
    </div>
  <?php } ?>
</div>
<script type="text/javascript">
$(document).ready(function(){
  var altura = $("#footer_tabla_1").height();
  $("#footer_tabla_2").height(altura);

  <?php //if ($factura->id_empresa == 229 || $factura->id_empresa == 230) { ?>
    window.print();
  <?php //} ?>

});
</script>
</body>
</html>