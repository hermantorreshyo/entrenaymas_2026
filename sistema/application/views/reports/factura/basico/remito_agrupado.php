<!DOCTYPE>
<html>
<head>
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
  <?php $comprobante = ("REMITO"); $letra = "R"; $discrimina_iva = 0;
  $piezas_items = array_chunk($items, 40);
  $nro_pieza = 1;
  foreach($piezas_items as $items2) { ?>
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
                    <p><b>Fecha: <?php echo date("d/m/Y"); ?></b></p>
                  </div>                
                </td>
              </tr>
            </table>
            <div style="margin-left: 4px;">
              <p>
                <b>Cliente: </b><span><?php echo utf8_decode($cliente->nombre); ?></span>
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
                foreach($items2 as $i) { ?>
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
                  </td>
                  <td style="padding: 0px; vertical-align: bottom; border-left: solid 1px <?php echo $cborde; ?>; width: 30%">
                    <div class="totales">
                      <p id="total">
                        <span>TOTAL:</span>
                        <span>$ <?php echo number_format($total,2); ?></span>
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
</div>
</body>
</html>