<!DOCTYPE>
<html>
<head>
<title>Recibo</title>
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

.tabla { min-height: 350px; border: solid 1px <?php echo $cborde; ?> }
.tabla table { width: 100%; border-collapse: collapse; font-size: 13px; }
.tabla table thead th { background-color: #e1e1e1; padding: 8px; }
.tabla table td { padding: 5px 8px; vertical-align: top; }
table td { font-size: 14px; }

.tabla2 { margin-top: 30px; width: 100%; border-collapse: collapse; font-size: 14px; }
.tabla2 thead th { background-color: #e1e1e1; padding: 8px; }
.tabla2 td { padding: 5px 8px; vertical-align: top; }
.tabla2 td { font-size: 14px; }

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

.tabla_borde { border-collapse: collapse; width: 100%; }
.tabla_borde td { border: solid 2px black; padding: 5px; }
.tabla_borde.b1 td { border: solid 1px black; padding: 5px; }

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
          <table class="tabla_borde" style="width: 100%">
            <tr>
              <td rowspan="2" style="width: 50%; text-align: center">
                <?php if(!empty($empresa->logo)) { ?>
                  <img style="width: 100%" src="/sistema/<?php echo $empresa->logo ?>"/>
                <?php } else { ?>
                  <h1><?php echo $empresa->razon_social ?></h1>
                <?php } ?>
              </td>
              <td><div style="font-size: 36px; font-weight: bold; text-align: center;">X</div></td>
              <td><div style="font-size: 28px; font-weight: bold; text-align: center;">Recibo</div></td>
              <td>
                <div style="font-size: 10px">
                  <div>
                    <?php if (!empty($empresa->direccion)) { ?>
                      <div>
                        Direccion: <span class="fr"><?php echo $empresa->direccion ?></span>    
                      </div>
                    <?php } ?>
                    <?php if (!empty($empresa->cuit)) { ?>
                      <div>
                        CUIT: <span class="fr"><?php echo $empresa->cuit ?></span>
                      </div>
                    <?php } ?>
                  </div>
                </div>
              </td>
            </tr>
            <tr>
              <td>
                <div style="text-align: center; font-size: 10px;">
                Doc. no v&aacute;lido<br/>
                como factura
                </div>
              </td>
              <td>
                <div style="text-align: center; font-size: 16px; font-weight: bold">
                  Nro. <?php echo (!empty($recibo->punto_venta) ? $recibo->punto_venta."-" : "").$recibo->numero ?><br/>
                </div>
              </td>
              <td>
                <div style="text-align: center; font-size: 16px; font-weight: bold">
                  Fecha<br/>
                  <?php echo $recibo->fecha ?>
                </div>
              </td>
            </tr>
          </table>
          <div style="padding: 20px 40px; border-bottom: solid 2px black; overflow: hidden; margin: 30px 0px;">
            <div style="font-size: 18px; line-height: 24px; margin-bottom: 50px; ">
              Recib&iacute; de <b><?php echo $cliente->nombre ?></b>,
              <?php if (!empty($cliente->cuit)) { ?>
                con <?php 
                if ($cliente->id_tipo_documento == 80) echo "CUIT ".substr($cliente->cuit, 0, 2)."-".substr($cliente->cuit, 2, 8)."-".substr($cliente->cuit, -1, 1);
                else if ($cliente->id_tipo_documento == 96) echo "DNI ".$cliente->cuit;
                else echo "CUIT/DNI ".$cliente->cuit; ?>,
              <?php } ?>

              <?php 
              $total = $recibo->total_cheques + $recibo->total_tarjetas + $recibo->efectivo + $recibo->retencion_iibb + $recibo->retencion_ganancias + $recibo->total_depositos;
              ?>
              la suma total de <b>$<?php echo number_format($total,2) ?></b>, 

              <?php if (sizeof($recibo->comprobantes) == 0) { ?>
                en concepto de pago de cuenta corriente.
              <?php } else { ?>
                en concepto de pago de los siguientes comprobantes:
                <table class="tabla2">
                  <thead>
                    <tr>
                      <th>Fecha</th>
                      <th>Comprobante</th>
                      <th>Total</th>
                      <th>Entrega</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php 
                    $total_comprobantes_haber = 0;
                    $total_comprobantes = 0;
                    foreach($recibo->comprobantes as $fact) { ?>
                      <tr>
                        <td><?php echo $fact->fecha ?></td>
                        <td><?php echo $fact->comprobante ?></td>
                        <td>$ <?php echo $fact->total ?></td>
                        <td>$ <?php echo $fact->haber ?></td>
                      </tr>
                    <?php $total_comprobantes += $fact->total; $total_comprobantes_haber += $fact->haber;
                    } ?>
                    <tr>
                      <td><b>Subtotal:</b></td>
                      <td></td>
                      <td><b>$ <?php echo number_format($total_comprobantes,2) ?></b></td>
                      <td><b>$ <?php echo number_format($total_comprobantes_haber,2) ?></b></td>
                    </tr>
                  </tbody>
                </table>
              <?php } ?>

              <?php if ($recibo->efectivo !=  0) { ?>
                <br/>Efectivo: $ <?php echo number_format($recibo->efectivo,2) ?>.
              <?php } ?>

              <?php if ($recibo->descuento !=  0) { ?>
                <br/>Descuento: $ <?php echo number_format($recibo->descuento,2) ?>.
              <?php } ?>

              <?php if (sizeof($recibo->cheques)>0) { ?>
                <br/>Cheques: $ <?php echo number_format($recibo->total_cheques,2) ?>
                <table class="tabla2">
                  <thead>
                    <tr>
                      <th>Banco</th>
                      <th>N&uacute;mero</th>
                      <th>Fecha emisi&oacute;n</th>
                      <th>Fecha cobro</th>
                      <th>Monto</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach($recibo->cheques as $cheque) { ?>
                      <tr>
                        <td><?php echo $cheque->banco ?></td>
                        <td><?php echo $cheque->numero ?></td>
                        <td><?php echo $cheque->fecha_emision ?></td>
                        <td><?php echo $cheque->fecha_cobro ?></td>
                        <td>$ <?php echo number_format($cheque->monto,2) ?></td>
                      </tr>
                    <?php } ?>
                  </tbody>
                </table>
              <?php } ?>

              <?php if (sizeof($recibo->depositos)) { ?>
                <br/>Depositos: $ <?php echo number_format($recibo->total_depositos,2) ?>
                <table class="tabla2">
                  <thead>
                    <tr>
                      <th>Cuenta</th>
                      <th>Monto</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach($recibo->depositos as $depo) { ?>
                      <tr>
                        <td><?php echo $depo->caja ?></td>
                        <td>$ <?php echo number_format($depo->monto,2) ?></td>
                      </tr>
                    <?php } ?>
                  </tbody>
                </table>
              <?php } ?>

              <?php if ($recibo->retencion_iibb !=  0) { ?>
                <br/>Retencion de IIBB: $ <?php echo number_format($recibo->retencion_iibb,2) ?>.
              <?php } ?>
              <?php if ($recibo->retencion_ganancias !=  0) { ?>
                <br/>Retencion de Ganancias: $ <?php echo number_format($recibo->retencion_ganancias,2) ?>.
              <?php } ?>
              <?php if ($recibo->retencion_suss !=  0) { ?>
                <br/>Retencion SUSS: $ <?php echo number_format($recibo->retencion_suss,2) ?>.
              <?php } ?>
              <?php if ($recibo->retencion_iva !=  0) { ?>
                <br/>Retencion IVA: $ <?php echo number_format($recibo->retencion_iva,2) ?>.
              <?php } ?>
              <?php if ($recibo->retencion_otras !=  0) { ?>
                <br/>Otras Retenciones: $ <?php echo number_format($recibo->retencion_otras,2) ?>.
              <?php } ?>

            </div>
          </div>
          <?php /*
          <div style="overflow:hidden; padding: 20px 40px; border-bottom: solid 2px black; ">
            <div style="font-size: 24px; font-weight: bold; float: left;">
            </div>
            <div style="font-size: 18px; font-weight: bold; float: right; width: 50%;">
              FIRMA: <br/><br/>
              ACLARACI&Oacute;N:
            </div>
          </div>
          */ ?>
        </div>
      </div>
    </div>
  </div>
</body>
</html>