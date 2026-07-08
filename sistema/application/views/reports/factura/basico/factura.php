<?php
$codigo_comprobante = str_pad($factura->id_tipo_comprobante,2,"0",STR_PAD_LEFT);

$letra = "R";
$discrimina_iva = 1;
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
  case 51:
    $comprobante = ("FACTURA"); $letra = "M"; $discrimina_iva = 0; break;
  case 52:
    $comprobante = ("NOTA DE D&Eacute;BITO"); $letra = "M"; $discrimina_iva = 0; break;
  case 53:
    $comprobante = ("NOTA DE CR&Eacute;DITO"); $letra = "M"; $discrimina_iva = 0; break;
  case 201:
    $comprobante = ("FACTURA MiPyME"); $letra = "A"; $discrimina_iva = 1; break;
  case 202:
    $comprobante = ("NOTA DE D&Eacute;BITO"); $letra = "A"; $discrimina_iva = 1; break;
  case 203:
    $comprobante = ("NOTA DE CR&Eacute;DITO"); $letra = "A"; $discrimina_iva = 1; break;
  case 206:
    $comprobante = ("FACTURA MiPyME"); $letra = "B"; $discrimina_iva = 0; break;
  case 207:
    $comprobante = ("NOTA DE D&Eacute;BITO"); $letra = "B"; $discrimina_iva = 0; break;
  case 208:
    $comprobante = ("NOTA DE CR&Eacute;DITO"); $letra = "B"; $discrimina_iva = 0; break;
  case 998:
    $comprobante = ("PRESUPUESTO"); $letra = "R"; $discrimina_iva = 0; break;
  case 999:
    $comprobante = (($factura->id_empresa == 1350) ? "INVOICE" : "REMITO"); $letra = (($factura->id_empresa == 1350) ? "I" : "R"); $discrimina_iva = 0; break;
}

// Si el cliente es de IMPORT SHOW, y tiene la etiqueta SINDICATO, que aparezca eso
if ($factura->id_empresa == 356 && isset($factura->cliente->etiquetas) && isset($factura->cliente->etiquetas[0]) && strtolower($factura->cliente->etiquetas[0]) == "sindicato" ) {
  $comprobante = "SINDICATO"; $letra = "X";
}

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
<title><?php echo $comprobante." ".$factura->comprobante ?></title>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />

<style type="text/css">
#barra {}
<?php $cborde = "#a1a1a1"; ?>
.a4 {
  width: 210mm;
  height: 280mm;
  overflow: hidden;
  margin: 0 auto;
  background-color: white;
}
@media only screen { .a4 { width: 100%; max-width: 960px; } }
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
.totales > p > span { font-weight: bold; display: inline-block; text-align: left; width: 160px; margin-right: 15px; }
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
$piezas_items = (sizeof($factura->items) > 0) ? array_chunk($factura->items, 27) : array($factura->items);
?>

<body>
  <?php echo $header; ?>
  <!-- FACTURA/BASICO/FACTURA.PHP -->
  <div id="printable">
    <?php
    $copias = $empresa->config["facturacion_cantidad_copias"];
    for($copia=0;$copia < $copias; $copia++) { 

      $nro_pieza = 1;
      foreach($piezas_items as $items) { ?>
        <div class="a4">
          <div class="inner">
            <div class="borde" style="margin-bottom: -1px;">
              <h2 style="text-align: center; padding: 0px; margin: 5px; font-size: 16px;">
                <?php if ($copia == 0) echo "ORIGINAL";
                else if ($copia == 1) echo "DUPLICADO";
                else if ($copia == 2) echo "TRIPLICADO";
                else if ($copia == 3) echo "CUADRUPLICADO"; ?>

                <?php if (sizeof($piezas_items)>1) { ?>
                  <span style="float: right; margin-right: 10px; margin-top: 3px; font-size: 12px; font-weight: normal">
                    Hoja <?php echo $nro_pieza ?> de <?php echo sizeof($piezas_items) ?>
                  </span>
                <?php } ?>

              </h2>
            </div>
            <div class="borde">
              <?php if ($empresa->id == 571) { ?>
                <p class="p20">
                  <b>Numero Pedido: </b><?php 
                  $exp = explode(" ",$factura->comprobante);
                  echo end($exp); ?><br/>
                  <b>Fecha: <?php echo $factura->fecha; ?></b>
                </p>
              <?php } else if(!empty($empresa->logo) && $empresa->id != 249 && $empresa->id != 228) { ?>
                <div class="fl w40p p20">
                  <div style="margin-bottom: 15px; margin-right: 20px; ">
                    <img style="width:95%" src="/sistema/<?php echo $empresa->logo ?>"/>
                  </div>
                </div>
                <div class="fl w50p" style="border-left: solid 1px <?php echo $cborde; ?>; margin-bottom: 0px; padding-bottom: 0px; font-size: 12px;">
                  <div class="letra" style="top: -1px; left: -35px;">
                    <h1><?php echo $letra; ?></h1>
                    <div class="codigo_comprobante">COD. <?php echo $codigo_comprobante; ?></div>
                  </div>
                  <div style="padding-left: 70px; float: none;">
                    <h2 style="margin-top: 10px; padding-top: 0px;"><?php echo $comprobante; ?></h2>
                    <p>
                      <b>Nro: </b><?php 
                      $exp = explode(" ",$factura->comprobante);
                      echo end($exp); ?>
                      <b>Fecha: <?php echo $factura->fecha; ?></b>
                    </p>
                    <p><b>Raz&oacute;n Social: </b><?php echo $empresa->razon_social?></p>
                    <?php if ($empresa->id == 228 && isset($sucursal) && !empty($sucursal) && !empty($sucursal->direccion)) { ?>
                      <p><b>Domicilio: </b><?php echo $sucursal->direccion."<br/>".$sucursal->nombre ?></p>
                    <?php } else { ?>
                      <?php if (!empty($empresa->direccion)) { ?>
                        <p><b>Domicilio: </b><?php echo $empresa->direccion ?></p>
                      <?php } ?>
                    <?php } ?>
                    <?php if (!empty($empresa->config["numero_ib"])) { ?>
                      <p><b>Ingresos Brutos: </b><?php echo $empresa->config["numero_ib"]; ?></p>
                    <?php } ?>
                    <?php
                    $fecha_inicio = fecha_es($empresa->config["fecha_inicio"]);
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
                  </div>
                </div>
              <?php } else { ?>
                <div class="fl w40p p20">
                  <p><b>Raz&oacute;n Social: </b><?php echo $empresa->razon_social?></p>

                  <?php if ($empresa->id == 228 && isset($sucursal) && !empty($sucursal) && !empty($sucursal->direccion)) { ?>
                    <p><b>Domicilio: </b><?php echo $sucursal->direccion."<br/>".$sucursal->nombre ?></p>
                  <?php } else { ?>
                    <?php if (!empty($empresa->direccion)) { ?>
                      <p><b>Domicilio: </b><?php echo $empresa->direccion ?></p>
                    <?php } ?>
                  <?php } ?>
                  <?php if (!empty($empresa->telefono)) { ?>
                    <p><b>Tel&eacute;fono: </b><?php echo $empresa->telefono ?></p>
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
                    <h2 style="margin-top: 0px; padding-top: 0px;"><?php echo $comprobante; ?></h2>
                    <p><b>Numero: </b>
                      <?php 
                      $exp = explode(" ",$factura->comprobante);
                      echo end($exp); ?>
                    </p>
                    <p><b>Fecha de Emision: <?php echo $factura->fecha; ?></b></p>
                  </div>
                </div>
              <?php } ?>
            </div>
            <div class="borde" style="padding: 15px 20px; margin-top: 15px;">
              <?php if ($factura->id_empresa == 228) { ?>
                <p>
                  <b>Cliente: </b><span>CONSUMIDOR FINAL</span>
                  <?php /*if(!empty($factura->cliente->cuit)) { ?>
                    <b class="ml30">CUIT: </b><span><?php echo $factura->cliente->cuit; ?></span>
                  <?php } */?>
                </p>
                <p>
                  <b>Condicion IVA: </b> <span>CONSUMIDOR FINAL</span>
                  <b class="ml30">Condicion de Venta: </b>
                  <span>
                    <?php echo ($factura->tipo_pago == "C") ? "Cuenta Corriente":""; ?>
                    <?php echo ($factura->tipo_pago == "E") ? "Efectivo":""; ?>
                    <?php echo ($factura->tipo_pago == "T") ? "Tarjeta":""; ?>
                    <?php echo ($factura->tipo_pago == "B") ? "Banco":""; ?>
                    <?php echo ($factura->tipo_pago == "H") ? "Cheque":""; ?>
                    <?php echo ($factura->tipo_pago == "O") ? "Otro":""; ?>
                  </span>
                </p>
              <?php } else { ?>
                <p>
                  <b>Cliente: </b><span><?php echo ($factura->cliente->nombre); ?></span>
                  <?php if(!empty($factura->cliente->cuit)) { ?>
                    <b class="ml30">CUIT: </b><span><?php echo $factura->cliente->cuit; ?></span>
                  <?php } ?>
                  <?php if(isset($factura->cliente->codigo) && !empty($factura->cliente->codigo)) { ?>
                    <span class="ml30">Codigo: <?php echo $factura->cliente->codigo; ?></span>
                  <?php } ?>
                </p>
                <?php if (!empty($factura->cliente->direccion)) { ?>
                  <p>
                    <b>Domicilio: </b> <span><?php echo ($factura->cliente->direccion); ?>
                    <?php if (!empty($factura->cliente->localidad)) { ?>
                      - <?php echo ($factura->cliente->localidad); ?>
                    <?php } ?>
                    </span>
                  </p>
                <?php } ?>
                <p>
                  <b>Condicion IVA: </b> <span><?php echo $factura->cliente->tipo_iva; ?></span>
                  <b class="ml30">Condicion de Venta: </b>
                  <span>
                    <?php echo ($factura->tipo_pago == "C") ? "Cuenta Corriente":""; ?>
                    <?php echo ($factura->tipo_pago == "E") ? "Efectivo":""; ?>
                    <?php echo ($factura->tipo_pago == "T") ? "Tarjeta":""; ?>
                    <?php echo ($factura->tipo_pago == "B") ? "Banco":""; ?>
                    <?php echo ($factura->tipo_pago == "H") ? "Cheque":""; ?>
                    <?php echo ($factura->tipo_pago == "O") ? "Otro":""; ?>
                  </span>
                  <?php if (!empty($factura->numero_remito)) { ?>
                    <b class="ml30">Remito Nro: </b>
                    <span><?php echo $factura->numero_remito ?></span>
                  <?php } ?>            
                </p>
              <?php } ?>
            </div>
            <div class="tabla" style="<?php echo ($factura->id_empresa == 1046)?"background-image:url(https://www.varcreative.com/sistema/uploads/1046/weg.jpg);background-position: center center; background-repeat: no-repeat":""; ?>">
              <table>
                <thead>
                  <tr>
                    <th>Cantidad</th>
                    <th>Cod.</th>
                    <th>Descripcion</th>
                    <th style="width: 70px; text-align: right;">Unitario</th>
                    <?php if ($discrimina_iva==1) { ?>
                      <th style="width: 25px">IVA</th>
                    <?php } ?>
                    <th style="width: 25px">Dto.</th>
                    <th style="width: 70px; text-align: right;">Subtotal</th>
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
                        <td class="tar"><?php echo number_format((($discrimina_iva==1)?$i->neto:$i->precio),2); ?> &euro;</td>
                        <?php if ($discrimina_iva==1) { ?>
                          <td><?php echo $i->porc_iva ?>%</td>
                        <?php } ?>
                        <td class="tar"><?php echo ($i->bonificacion > 0) ? ($i->bonificacion."%") : "" ?></td>
                        <td class="tar"><?php echo number_format((($discrimina_iva==1)?$i->total_sin_iva:$i->total_con_iva),2); ?> &euro;</td>
                      </tr>
                      <?php $total_neto += $i->total_sin_iva; ?>
                      <?php $total_con_iva += $i->total_con_iva; ?>
                    <?php } ?>
                  <?php } ?>
                </tbody>
              </table>
            </div>

            <?php if ($nro_pieza == sizeof($piezas_items)) { ?>
              <div class="borde">
                <div class="cae_container">
                  <?php if (!empty($factura->cae)) { ?>
                    <p>
                      <span>C.A.E.:</span>
                      <span><?php echo $factura->cae ?></span>
                    </p>
                    <p>
                      <span>Fecha Vto. de CAE:</span>
                      <span><?php echo ($factura->fecha_vto); ?></span>
                    </p>
                  <?php } ?>
                  <?php if ($factura->efectivo > 0) { ?>
                    <?php if ($factura->tipo_pago == "O") { ?>
                      <p><span>Forma Pago: Otro</span> <span><?php echo number_format($factura->efectivo - $factura->vuelto,2) ?></span></p>
                    <?php } else { ?>
                      <p><span>Efectivo:</span> <span><?php echo number_format($factura->efectivo - $factura->vuelto,2) ?></span></p>
                    <?php } ?>
                  <?php } ?>
                  <?php if ($factura->tarjeta > 0) { ?>
                    <?php if ($factura->tipo_pago == "B") { ?>
                      <p><span>Banco:</span> <span><?php echo number_format($factura->tarjeta,2) ?></span></p>
                    <?php } else { ?>
                      <p><span>Tarjeta:</span> <span><?php echo number_format($factura->tarjeta,2) ?></span></p>
                    <?php } ?>
                  <?php } ?>
                  <?php if ($factura->cta_cte > 0) { ?>
                    <p><span>Cta. Cte.:</span> <span><?php echo number_format($factura->cta_cte,2) ?></span></p>
                  <?php } ?>
                  <?php if ($factura->cheque > 0) { ?>
                    <p><span>Cheque:</span> <span><?php echo number_format($factura->cheque,2) ?></span></p>
                  <?php } ?>

                  <?php if (!empty($factura->observaciones)) {
                    // Analizamos las observaciones
                    $obs = (nl2br($factura->observaciones)); ?>
                    <p><?php echo $obs ?></p>
                  <?php } ?>

                </div>
                <div class="totales">
                  <p id="subtotal">
                    <span>SUBTOTAL:</span>
                    <span><?php echo number_format($total_con_iva,2); ?> &euro;</span>
                  </p>
                  <?php if ($factura->descuento > 0) { ?>
                    <p id="descuento">
                      <span>DESCUENTO POR TOPE MÁXIMO:</span>
                      <span><?php echo number_format($factura->descuento,2) ?> &euro;</span>
                    </p>
                  <?php } ?>
                  <?php if ($discrimina_iva == 1) { ?>
                    <?php foreach($factura->ivas as $i) { ?>
                      <p id="iva">
                        <span>IVA <?php echo mostrar_iva($i->id_alicuota_iva); ?>:</span>
                        <span><?php echo number_format($i->iva,2); ?> &euro;</span>
                      </p>
                    <?php } ?>
                  <?php } ?>
                  <?php if ($factura->interes > 0) { ?>
                    <p id="descuento">
                      <span>RECARGO TARJETA:</span>
                      <span><?php echo number_format($factura->interes,2) ?> &euro;</span>
                    </p>
                  <?php } ?>
                  <p id="total">
                    <span>TOTAL:</span>
                    <span><?php echo number_format($factura->total,2); ?> &euro;</span>
                  </p>
                </div>
              </div>
              <div class="oh">
                <?php if (isset($preference) && $preference !== FALSE) { ?>
                  <a style="background-color: #009ee3;
                    float: left;
    border: none;
    text-decoration: none;
    color: white;
    margin: 10px 0px;
    padding: 8px 20px;
    font-size: 16px;
    line-height: 34px;
    border-radius: 4px;
    text-shadow: 1px 1px 1px #969696;
    cursor: pointer;"
                    target="_blank"
                    href="<?php echo $preference["response"]["init_point"]; ?>" 
                    mp-mode="modal" 
                    name="MP-Checkout" 
                    class="pago-mercadopago">Pagar con MercadoPago</a>
                <?php } ?>
                <div class="barcode fr">
                  <div><img src="/sistema/application/helpers/barcode.php?text=<?php echo $barcode; ?>" /></div>
                  <div><?php echo $barcode ?></div>
                </div>
              </div>
            <?php } ?>

          </div>
        </div>
      <?php $nro_pieza++; } ?>
    <?php } ?>
  </div>
</body>
</html>