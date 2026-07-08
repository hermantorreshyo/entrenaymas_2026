<?php
$codigo_comprobante = "00"; //str_pad($compra->id_tipo_comprobante,2,"0",STR_PAD_LEFT);

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
<title><?php echo $compra->numero_1."-".$compra->numero_2 ?></title>
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
  <?php switch ($compra->id_tipo_comprobante) {
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
  $discrimina_iva = 0;

  // Dependiendo la cantidad de productos que tiene la factura
  $piezas_netos = array_chunk($compra->netos, 27);

  $nro_pieza = 1;
  foreach($piezas_netos as $netos) { ?>
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
                      <p><b><?php echo $proveedor->nombre ?></b></p>
                      <?php if (!empty($proveedor->direccion)) { ?>
                        <p><?php echo $proveedor->direccion ?> - <?php echo $proveedor->localidad ?></p>
                      <?php } ?>
                    </div>
                  </div>
                </td>
                <td>
                  <div class="p10" style="margin-bottom: 0px; padding-bottom: 0px; text-align: right">
                    <p><b>Numero: </b>
                      <?php echo $compra->numero_1."-".$compra->numero_2; ?>
                    </p>
                    <p><b>Fecha: <?php echo $compra->fecha; ?></b></p>
                  </div>                
                </td>
              </tr>
            </table>
            <div style="margin-left: 4px;">
              <p>
                <b>Cliente: </b><span><?php echo $empresa->razon_social?></span>
              </p>
              <p>
                <?php if (!empty($empresa->direccion)) { ?>
                  <p><?php echo $empresa->direccion ?> - <?php echo $empresa->localidad ?></p>
                <?php } ?>
              </p>
            </div>          
          </div>
          <div class="tabla">
            <table>
              <thead>
                <tr>
                  <th style="width: 50%;">Descripcion</th>
                  <th style="width: 15%;">Subtotal</th>
                </tr>
              </thead>
              <tbody>
                <?php 
                foreach($netos as $i) { ?>
                  <tr>
                    <td><?php echo $i->nombre_concepto; ?></td>
                    <td>$ <?php echo number_format($i->neto_dto,2); ?></td>
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
                      <p id="total">
                        <span>TOTAL:</span>
                        <span>$ <?php echo number_format($compra->total_general,2); ?></span>
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