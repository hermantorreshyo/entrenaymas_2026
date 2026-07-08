<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$factura->monto = $factura->monto / 100;
?>
<!DOCTYPE>
<html>
<head>
<title>Comprobante</title>
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

.tabla { min-height: 360px; border: solid 1px <?php echo $cborde; ?> }
.tabla table { width: 100%; border-collapse: collapse; font-size: 13px; }
.tabla table thead th { background-color: #e1e1e1; padding: 8px; }
.tabla table td { padding: 3px 8px; vertical-align: top; font-size: 13px; }
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

.boton_imprimir{margin-top: 30px;padding: 5px 20px; color: white; background-color: #6ab56a; border-radius: 3px;border: none;}
.boton_imprimir:hover{cursor: pointer; background-color: #4c8b4c}

@media print {
  body {-webkit-print-color-adjust: exact; }
  .inner.second { margin-top: 60px; }
  .inner { padding: 0px 0px 0px 0px; }
  .a4inner { padding: 0px; }
  .a4 { page-break-after: always; padding: 20px; }
  .a4:last-child { page-break-after: avoid; }
  .boton_imprimir{display: none;}
}
@page {
  size: auto;
  margin: 0px;
}
</style>
</head>
<body>
  <div id="printable">
    <div class="a4">
      <div class="a4inner">
        <div class="inner">
          <div class="borde" style="padding: 10px">
            <table style="width: 100%">
              <tr>
                <td>
                  <div class="p10 pt0">
                    <h2>Entrenaymas</h2>
                    <p>
                      B01706290<br/>
                      C/ Duque de Sesto 11, 28009, Madrid. España<br/>
                      (+34) 641522483<br/>
                      info@entrenaymas.com
                    </p>
                  </div>
                </td>
                <td>
                  <div class="p10" style="margin-bottom: 0px; padding-bottom: 0px; text-align: right">
                    <p><b>Numero: </b><?php echo $factura->id ?>
                    <p><b>Fecha: </b><?php echo date("d/m/Y H:i", strtotime($factura->fecha)); ?></p>
                  </div>                
                </td>
              </tr>
            </table>
            <div style="margin-left: 4px;">
              <p>
                <b>Cliente: </b><span><?php echo $factura->nombre_usuario; ?></span>
              </p>
            </div>          
          </div>
          <div class="tabla">
            <table>
              <thead>
                <tr>
                  <th style="width: 10%;">Cantidad</th>
                  <th style="width: 65%;">Descripcion</th>
                  <th style="width: 25%;">Subtotal</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>1</td>
                  <td><?php echo $factura->tipo." ".(($factura->periodicidad == 30)?"Mensual":"Anual") ?></td>
                  <td><?php echo number_format((($factura->monto * 100) + $factura->bonificacion + $factura->cupon_descuento)/100,2); ?> €</td>
                </tr>
                <?php if ($factura->bonificacion > 0) { ?>
                  <tr>
                    <td>1</td>
                    <td><?php echo "Bonificacion Mes" ?></td>
                    <td>-<?php echo number_format($factura->bonificacion / 100,2); ?> €</td>
                  </tr>
                <?php } ?>
                <?php if ($factura->cupon_descuento > 0) { ?>
                  <tr>
                    <td>1</td>
                    <td><?php echo $factura->cupon_descuento_descripcion ?></td>
                    <td>-<?php echo number_format($factura->cupon_descuento / 100,2); ?> €</td>
                  </tr>
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
                      <p id="subtotal">
                        <span>SUBTOTAL:</span>
                        <span><?php echo number_format($factura->monto,2); ?> €</span>
                      </p>
                        
                      <?php /*
                      <?php if ($factura->porc_descuento > 0) { 
                        <p id="descuento">
                          <span>DTO. <?php echo number_format($factura->porc_descuento,2)  %:</span>
                          <span><?php echo number_format($factura->descuento,2) </span>
                        </p>
                        <p id="subtotal_descuento">
                          <span>SUBTOTAL:</span>
                          <span><?php echo number_format($factura->subtotal - $factura->descuento,2); </span>
                        </p>
                      <?php } 
                      <?php if ($discrimina_iva == 1 && $empresa->id != 86) { 
                        foreach($factura->ivas as $i) { 
                          <p id="iva">
                            <span>IVA <?php echo mostrar_iva($i->id_alicuota_iva); :</span>
                            <span><?php echo number_format($i->iva,2); </span>
                          </p>
                        <?php }
                      <?php } */ ?>
                      <p id="total">
                        <span>TOTAL:</span>
                        <span><?php echo number_format($factura->monto,2); ?> €</span>
                      </p>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <div class="tar">
            <button class="boton_imprimir" onclick="window.print()">IMPRIMIR</button>
          </div>
        </div>
      
    
      </div>
    </div>
  </div>
</body>
</html>