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
    <?php foreach($facturas as $factura) { ?>
    <div class="a4">
      <div class="a4inner">
        <div class="inner">
          <table class="tabla_borde" style="width: 100%">
            <tr>
              <td rowspan="2" style="width: 50%">
                <?php if(!empty($empresa->logo)) { ?>
                  <img style="width: 100%" src="/sistema/<?php echo $empresa->logo ?>"/>
                <?php } ?>
              </td>
              <td><div style="font-size: 36px; font-weight: bold; text-align: center;">X</div></td>
              <td><div style="font-size: 28px; font-weight: bold; text-align: center;">Recibo</div></td>
              <td>
                <div style="font-size: 10px">
                  <div>
                    CUIT: <span class="fr">20-16415694-0</span>
                  </div>
                  <div>
                    II. BB.: <span class="fr">20-16415694-0</span>
                  </div>
                  <div>
                    INICIO ACT.: <span class="fr">01/06/2015</span>
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
                  Nro.<br/>
                  <?php echo $factura->comprobante ?>
                </div>
              </td>
              <td>
                <div style="text-align: center; font-size: 16px; font-weight: bold">
                  Fecha<br/>
                  <?php echo date("d/m/Y") ?>
                </div>
              </td>
            </tr>
          </table>
          <div style="padding: 20px 40px; border-bottom: solid 2px black; overflow: hidden;">
            <div style="font-size: 16px; line-height: 24px; ">
              Recib&iacute; de <?php echo $factura->cliente ?>,
              con DNI/CUIT <?php echo $factura->cuit ?>,
              la cantidad de pesos <?php echo $letras->ValorEnLetras($factura->total) ?>
              por el alquiler de <?php echo $factura->propiedad ?>
              ubicado en <?php echo $factura->direccion ?>
              correspondiente al mes de <?php echo $factura->corresponde_a ?>
              que vence el <?php echo $factura->vencimiento ?>.<br/>
              <?php if (!empty($factura->propietario)) { ?>
                Importe para entregar a: <?php echo $factura->propietario ?>
              <?php } ?>
            </div>
            <?php 
            $total_items = 0;
            if (sizeof($factura->items)>0) { ?>
              <div style="float: left; width: 60%; margin-top: 20px;">
                <table class="tabla_borde b1">
                  <tr>
                    <td><b>Tasas / Servicios / Expensas</b></td>
                    <td><b>Monto</b></td>
                  </tr>
                  <?php foreach($factura->items as $i) { ?>
                    <?php $total_items += $i->monto; ?>
                    <tr>
                      <td><?php echo $i->nombre; ?></td>
                      <td>$ <?php echo number_format($i->monto,2); ?></td>
                    </tr>
                  <?php } ?>        
                </table>
              </div>
            <?php } ?>
          </div>
          
          <div style="overflow:hidden; padding: 20px 40px; border-bottom: solid 2px black; ">
            <div style="font-size: 24px; font-weight: bold; float: left;">
              TOTAL: $ <?php echo ($factura->total + $total_items) ?>
            </div>
            <div style="font-size: 18px; font-weight: bold; float: right; width: 50%;">
              FIRMA: <br/><br/>
              ACLARACI&Oacute;N:
            </div>
          </div>
        </div>
      </div>
    </div>
    <?php } ?>
  </div>
</body>
</html>