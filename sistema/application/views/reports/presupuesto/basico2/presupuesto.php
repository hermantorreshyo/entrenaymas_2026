<?php
$codigo_comprobante = "00";
$comprobante = ("PRESUPUESTO");
$letra = "P";
$discrimina_iva = 0;
$moneda = "$";
if ($presupuesto->moneda == 2) $moneda = "USD";
else if ($presupuesto->moneda == 3) $moneda = "BRL";
else if ($presupuesto->moneda == 4) $moneda = "EUR";
else if ($presupuesto->moneda == 5) $moneda = "CLP";

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
  <div id="printable">
    <div class="a4">
      <div class="a4inner">
        <div class="inner">
          <div class="borde" style="padding: 10px">
            <table style="width: 100%">
              <tr>
                <td>
                  <div class="p10 pt0">
                    <h2 style="margin-top: 0px; padding-top: 0px;"><?php echo $comprobante; ?></h2>
                    <?php if(!empty($empresa->logo)) { ?>
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
                    <p><b>Numero:</b> <?php echo $presupuesto->numero; ?></p>
                    <p><b>Generado el: <?php echo $presupuesto->fecha; ?></b></p>
                    <?php if ($empresa->id != 224) { ?>
                      <p><b>V&aacute;lido hasta: <?php echo $presupuesto->fecha_hasta; ?></b></p>
                    <?php } ?>
                  </div>                
                </td>
              </tr>
            </table>
            <div style="margin-left: 4px;">
              <p>
                <b>Cliente: </b><span><?php echo utf8_decode($presupuesto->cliente->nombre); ?></span>
              </p>
              <p>
                <?php if(!empty($presupuesto->direccion)) { ?>
                  <b>Direccion: </b>
                  <span>
                    <?php echo utf8_decode($presupuesto->direccion); ?>
                    <?php if (!empty($presupuesto->localidad)) { ?>
                      - <?php echo utf8_decode($presupuesto->localidad); ?>
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
                  <th style="width: 10%;">Unitario</th>
                  <th style="width: 10%;">Bonif.</th>
                  <th style="width: 10%;">Subtotal</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach($presupuesto->items as $i) { ?>
                  <?php if ($i->anulado == 0) { ?>
                    <tr>
                      <td><?php echo number_format($i->cantidad,2); ?></td>
                      <td><?php echo $i->codigo; ?></td>
                      <td>
                        <?php echo $i->nombre; ?>
                        <?php if (!empty($i->descripcion)) { ?>
                          <br/><span><?php echo $i->descripcion; ?></span>
                        <?php } ?>
                      </td>
                      <td><?php echo $moneda." ".number_format($i->precio,2); ?></td>
                      <td><?php echo ($i->bonificacion != 0) ? str_replace(".00", "", $i->bonificacion)."%" : "" ?></td>
                      <td><?php echo $moneda." ".number_format($i->total,2); ?></td>
                    </tr>
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
                    <?php if (!empty($presupuesto->observaciones)) {
                      // Analizamos las observaciones
                      $obs = (nl2br($presupuesto->observaciones));
                      $obs = str_replace("{{COTIZACION_DOLAR}}",$presupuesto->cotizacion_dolar,$obs);
                      $obs = str_replace("{{TOTAL_EN_LETRAS}}",strtoupper($letras->ValorEnLetras(round($presupuesto->total,2),"PESOS")),$obs);
                      if ($presupuesto->cotizacion_dolar == 0) {
                        $obs = str_replace("{{TOTAL_EN_DOLARES}}","",$obs);
                        $obs = str_replace("{{TOTAL_EN_DOLARES_EN_LETRAS}}","",$obs);
                      } else {
                        $total_dolares = round($presupuesto->total / $presupuesto->cotizacion_dolar,2);
                        $obs = str_replace("{{TOTAL_EN_DOLARES}}",$total_dolares,$obs);
                        $obs = str_replace("{{TOTAL_EN_DOLARES_EN_LETRAS}}",strtoupper($letras->ValorEnLetras($total_dolares,"DOLARES")),$obs);
                      }
                      ?>
                      <div style="padding: 15px 20px; margin-bottom: 15px;">
                        <?php echo $obs ?>
                      </div>
                    <?php } ?>
                  </td>
                  <td style="padding: 0px; vertical-align: bottom; border-left: solid 1px <?php echo $cborde; ?>; width: 30%">
                    <div class="totales">
                      <?php if ($presupuesto->subtotal != $presupuesto->total) { ?>
                        <p id="subtotal">
                          <span>SUBTOTAL:</span>
                          <span><?php echo $moneda." ".number_format($presupuesto->subtotal,2); ?></span>
                        </p>
                      <?php } ?>
                      <?php if ($presupuesto->porc_descuento > 0) { ?>
                        <p id="descuento">
                          <span>DTO. <?php echo number_format($presupuesto->porc_descuento,2) ?> %:</span>
                          <span><?php echo $moneda." ".number_format($presupuesto->descuento,2) ?></span>
                        </p>
                      <?php } else if ($presupuesto->descuento > 0) { ?>
                        <p id="descuento">
                          <span>DESCUENTO:</span>
                          <span><?php echo $moneda." ".number_format($presupuesto->descuento,2) ?></span>
                        </p>
                      <?php } ?>

                      <?php if ($presupuesto->recargo > 0 && $presupuesto->cuotas > 0) { ?>
                        <p id="descuento">
                          <span>SUBTOTAL:</span>
                          <span><?php echo $moneda." ".number_format($presupuesto->total,2) ?></span>
                        </p>
                        <p id="descuento">
                          <span>RECARGO <?php echo $presupuesto->cuotas ?> CUOTAS:</span>
                          <span><?php echo $moneda." ".number_format($presupuesto->recargo,2) ?></span>
                        </p>
                      <?php } ?>
                      
                      <p id="total">
                        <span>TOTAL:</span>
                        <span><?php echo $moneda." ".number_format(($presupuesto->total + $presupuesto->recargo),2); ?></span>
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
  </div>
</body>
</html>