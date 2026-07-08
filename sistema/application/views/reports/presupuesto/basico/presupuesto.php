<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
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
<title><?php echo $comprobante." ".$presupuesto->comprobante ?></title>
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
.inner { padding: 40px; }
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
th { text-align: left; }

.tabla { margin-top: 15px; margin-bottom: 15px; min-height: 400px; border: solid 1px <?php echo $cborde; ?> }
.tabla table { width: 100%; border-collapse: collapse; font-size: 13px; }
.tabla table thead th { background-color: #e1e1e1; padding: 8px; }
.tabla table td { padding: 4px 8px; vertical-align: top; }

.totales { margin-right: 40px; float: right; margin-top: 15px; margin-bottom: 15px; }
.totales > p > span { font-weight: bold; display: inline-block; text-align: left; width: 100px; margin-right: 15px; }
.totales > p > span:first-child { font-weight: normal; text-align: right;  }
#total { font-weight: bold; font-size: 14px; margin-top: 15px; border-top: solid 1px <?php echo $cborde; ?>; padding-top: 15px; }

.cae_container { float: left; margin-top: 20px; margin-left: 20px; }
.cae_container > p > span { text-align: left; margin-right: 10px; }
.cae_container > p > span:first-child { font-weight: bold;  }

.letra { position: relative; top: -21px; left: -56px; background-color: white; float: left; text-align: center; border: solid 1px <?php echo $cborde; ?>; }
.letra h1 { font-size: 42px; margin: 0px; padding: 10px 18px; border-bottom: solid 1px <?php echo $cborde; ?>; }
.letra .codigo_comprobante { font-size: 9px; margin-top: 3px; margin-bottom: 3px; }

.barcode { margin-top: 20px; font-size: 8px; text-align: center; }
.barcode > div { margin-bottom: 3px; }

@media print {
  body {-webkit-print-color-adjust: exact; }
  .inner { padding: 0px; }
  .a4 { page-break-after: always; }
  .a4:last-child { page-break-after: avoid; }
}
</style>
</head>

<?php
// Dependiendo la cantidad de productos que tiene la factura
$piezas_items = array_chunk($presupuesto->items, 20);
?>

<body>
  <?php echo $header; ?>
  <!-- PRESUPUESTO/BASICO/PRESUPUESTO.PHP -->
  <div id="printable">
    <?php $nro_pieza = 1;
    foreach($piezas_items as $items) { ?>
      <div class="a4">
        <div class="inner">
          <div class="borde" style="margin-bottom: -1px;">
            <h2 style="text-align: center; padding: 0px; margin: 5px; font-size: 16px;">
              PRESUPUESTO
              <?php if (sizeof($piezas_items)>1) { ?>
                <span style="float: right; margin-right: 10px; margin-top: 3px; font-size: 12px; font-weight: normal">
                  Hoja <?php echo $nro_pieza ?> de <?php echo sizeof($piezas_items) ?>
                </span>
              <?php } ?>
            </h2>
          </div>
          <div class="borde">
            <?php if(!empty($empresa->logo)) { ?>
              <div class="fl w40p p20">
                <div style="margin-bottom: 15px; margin-right: 20px; text-align: center;">
                  <img style="max-width:95%; max-height: 80px" src="/sistema/<?php echo $empresa->logo ?>"/>
                </div>
              </div>
              <div class="fl w50p" style="border-left: solid 1px <?php echo $cborde; ?>; margin-bottom: 0px; padding-bottom: 0px; font-size: 12px;">
                <div class="letra" style="top: -1px; left: -35px;">
                  <h1><?php echo $letra; ?></h1>
                  <div class="codigo_comprobante">COD. <?php echo $codigo_comprobante; ?></div>
                </div>
                <div style="padding-left: 70px; float: none; padding-top: 15px;">
                  <p><b>N&uacute;mero:</b> <?php echo $presupuesto->numero; ?></p>
                  <p><b>Raz&oacute;n Social: </b><?php echo $empresa->razon_social?></p>
                  <?php if (!empty($empresa->direccion)) { ?>
                    <p><b>Domicilio: </b><?php echo $empresa->direccion ?></p>
                  <?php } ?>
                  <?php if (!empty($empresa->config["numero_ib"])) { ?>
                    <p><b>Ingresos Brutos: </b><?php echo $empresa->config["numero_ib"]; ?></p>
                  <?php } ?>
                  <?php
                  $fecha_inicio = (isset($empresa->config["fecha_inicio"])) ? fecha_es($empresa->config["fecha_inicio"]) : "0000-00-00";
                  if ($fecha_inicio != "0000-00-00" && $fecha_inicio != "00/00/0000") { ?>
                    <p><b>Inicio de Actividades: </b> <?php echo $fecha_inicio ?></p>
                  <?php } ?>
                  <p>
                    <b>
                    <?php
                    switch($empresa->id_tipo_contribuyente) {
                      case 1: echo "IVA RESPONSABLE INSCRIPTO"; break;
                      case 2: echo "MONOTRIBUTO"; break;
                      case 3: echo "IVA EXENTO"; break;
                    }
                    ?>
                    </b>
                  </p>
                  <p><b>CUIT:</b> <?php echo $empresa->cuit; ?></p>
                  <p><b>Generado el: <?php echo $presupuesto->fecha; ?></b></p>
                  <?php if ($empresa->id != 224 && $empresa->id != 1325) { ?>
                    <p><b>V&aacute;lido hasta: <?php echo $presupuesto->fecha_hasta; ?></b></p>
                  <?php } ?>
                </div>
              </div>
            <?php } else { ?>
              <div class="fl w40p p20" style="padding-right: 50px;">
                <p><b>N&uacute;mero:</b> <?php echo $presupuesto->numero; ?></p>
                <p><b>Raz&oacute;n Social: </b><?php echo $empresa->razon_social?></p>
                <?php if (!empty($empresa->direccion)) { ?>
                  <p><b>Domicilio: </b><?php echo $empresa->direccion ?></p>
                <?php } ?>
                <p><b>CUIT: </b><?php echo $empresa->cuit; ?></p>
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
              <div class="fl w40p p20" style="border-left: solid 1px <?php echo $cborde; ?>; margin-bottom: 0px; padding-bottom: 0px">
                <div class="letra">
                  <h1><?php echo $letra; ?></h1>
                  <div class="codigo_comprobante">COD. <?php echo $codigo_comprobante; ?></div>
                </div>
                <div style="padding-left: 70px; float: none;">
                  <p><b>Generado el: <?php echo $presupuesto->fecha; ?></b></p>
                  <?php if ($empresa->id != 224 && $empresa->id != 1325) { ?>
                    <p><b>V&aacute;lido hasta: <?php echo $presupuesto->fecha_hasta; ?></b></p>
                  <?php } ?>
                </div>
              </div>
            <?php } ?>
          </div>
          <div class="borde" style="padding: 15px 20px; margin-top: 15px;">
            <p>
              <b>Cliente: </b><span><?php echo utf8_decode($presupuesto->cliente->nombre); ?></span>
              <?php if(!empty($presupuesto->cliente->cuit)) { ?>
                <b class="ml30">CUIT: </b><span><?php echo $presupuesto->cliente->cuit; ?></span>
              <?php } ?>
            </p>
            <?php if (!empty($presupuesto->direccion)) { ?>
              <p>
                <b>Domicilio: </b> <span><?php echo utf8_decode($presupuesto->direccion); ?>
                <?php if (!empty($presupuesto->localidad)) { ?>
                  - <?php echo utf8_decode($presupuesto->localidad); ?>
                <?php } ?>
                </span>
              </p>
            <?php } ?>
            <p>
              <b>Condicion IVA: </b> <span><?php echo $presupuesto->cliente->tipo_iva; ?></span>
              <?php if (!empty($presupuesto->numero_remito)) { ?>
                <b class="ml30">Remito Nro: </b>
                <span><?php echo $presupuesto->numero_remito ?></span>
              <?php } ?>            
            </p>
          </div>
          <div class="tabla">
            <table>
              <thead>
                <tr>
                  <th>Cantidad</th>
                  <th>Cod.</th>
                  <th>Descripcion</th>
                  <th>Unitario</th>
                  <th>Bonif.</th>
                  <th>Subtotal</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach($items as $i) { ?>
                  <tr>
                    <td><?php echo number_format($i->cantidad,2); ?></td>
                    <td><?php 
                    if ($presupuesto->id_empresa == 356) {
                      // IMPORT SHOW SIEMPRE MUESTRA LOS CODIGOS INTERNOS
                      echo $i->codigo;
                    } else {
                      if (!empty($i->custom_1)) {
                        echo $i->custom_1;
                      } else if (isset($i->codigo_barra) && !empty($i->codigo_barra)) {
                        $codigos = explode("###", $i->codigo_barra);
                        if ($presupuesto->id_empresa != 249) {
                          echo $codigos[0];
                        }
                      } else if (isset($i->codigo)) {
                        echo $i->codigo;
                      }
                    }
                    ?></td>
                    <td>
                      <?php echo $i->nombre; ?>
                      <?php if (!empty($i->descripcion)) { ?>
                        <br/><span><?php echo $i->descripcion; ?></span>
                      <?php } ?>
                      <?php if ($presupuesto->id_empresa == 249) { ?>
                        <br/><span style="font-size: 11px; color: #222; line-height: 14px;">
                          <?php 
                          $codigos = explode("###", $i->codigo_barra);
                          foreach($codigos as $cod) {
                            echo ($cod != $i->codigo) ? $cod.". " : "";
                          } ?>
                        </span>
                      <?php } ?>
                    </td>
                    <td><?php echo $moneda." ".(($i->porc_iva > 0) ? (number_format($i->neto,2)." (".$i->porc_iva.")") : number_format($i->precio,2)); ?></td>
                    <td><?php echo ($i->bonificacion != 0) ? str_replace(".00", "", $i->bonificacion)."%" : "" ?></td>
                    <td><?php echo $moneda." ".number_format($i->total,2); ?></td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
          <?php if (!empty($presupuesto->observaciones)) { ?>
            <div class="borde" style="padding: 15px 20px; margin-bottom: 15px;">
              <?php echo $presupuesto->observaciones ?>
            </div>
          <?php } ?>

          <?php if ($nro_pieza == sizeof($piezas_items)) { ?>
            <div class="borde">
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

                <?php if ($presupuesto->porc_iva > 0) { ?>
                  <p id="iva">
                    <span>IVA <?php echo number_format($presupuesto->porc_iva,2) ?> %:</span>
                    <span><?php echo $moneda." ".number_format($presupuesto->iva,2) ?></span>
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
            </div>
          <?php } ?>

        </div>
      </div>
    <?php $nro_pieza++; } ?>
  </div>
</body>
</html>