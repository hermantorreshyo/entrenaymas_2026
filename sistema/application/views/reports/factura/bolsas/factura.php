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
    $comprobante = ("REMITO"); $letra = "R"; $discrimina_iva = 0; break;
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
@media only screen { .a4 { width: 100%; } }
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
.w55p { width: 55%; }
.w40p { width: 40%; }
.w30p { width: 30%; }
.w100p { width: 100%; }
.oh { overflow: hidden; }
.bold { font-weight: bold; }
.p20 { padding: 20px; }
.ml30 { margin-left: 30px; }
th { text-align: left; }

.tabla { margin-top: 15px; margin-bottom: 15px; min-height: 400px; border: solid 1px <?php echo $cborde; ?> }
.tabla table { width: 100%; border-collapse: collapse; font-size: 13px; }
.tabla table thead th { background-color: #e1e1e1; padding: 8px; }
.tabla table td { padding: 8px; vertical-align: top; }

.totales { margin-top: 15px; margin-bottom: 15px; }
.totales > p { margin-bottom: 15px; margin-top: 10px;}
.totales > p > span { font-weight: bold; display: inline-block; text-align: left; width: 48%; }
.totales > p > span:first-child { font-weight: normal; text-align: right;  }
#total { font-weight: bold; font-size: 16px; margin-top: 15px; border-top: solid 1px <?php echo $cborde; ?>; padding-top: 15px; }

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
    .inner { padding: 0px; }
    .a4 { page-break-after: always; }
    .a4:last-child { page-break-after: avoid; }
}
</style>
</head>
<body>
    <?php echo $header; ?>
    <div id="printable">
    <?php
    $copias = $empresa->config["facturacion_cantidad_copias"];
    for($copia=0;$copia < $copias; $copia++) { ?>
        <div class="a4">
            <div class="inner">
                <div class="borde" style="margin-bottom: -1px;">
                    <h2 style="text-align: center; padding: 0px; margin: 5px; font-size: 16px;">
                        <?php if ($copia == 0) echo "ORIGINAL";
                        else if ($copia == 1) echo "DUPLICADO";
                        else if ($copia == 2) echo "TRIPLICADO";
                        else if ($copia == 3) echo "CUADRUPLICADO"; ?>
                    </h2>
                </div>
                <div class="borde">
                    <div class="fl p20" style="width: 53%; border-right: solid 1px <?php echo $cborde; ?>;">
                        <?php if(!empty($empresa->path)) { ?>
                            <div style="margin-bottom: 30px; margin-right: 30px; float: left ">
                                <img style="width:90px" src="/sistema/<?php echo $empresa->path ?>"/>
                            </div>
                        <?php } ?>
                        <div class="fl">
                            <p><!--<b>Raz&oacute;n Social: </b>--><b><?php echo $empresa->razon_social?></b></p>
                            <?php if (!empty($empresa->direccion)) { ?>
                                <p><!--<b>Domicilio: </b>--><?php echo $empresa->direccion ?></p>
                                <p>Chacabuco (B) - CP: 6740</p>
                            <?php } ?>
                            <?php
                            $fecha_inicio = $empresa->config["fecha_inicio"];
                            if ($fecha_inicio != "0000-00-00" && $fecha_inicio != "00/00/0000") { ?>
                                <p><b>Inicio de Actividades: </b> <?php echo fecha_es($fecha_inicio) ?></p>
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
                            <p><?php echo ($empresa->id == 994) ? "lloretmrycia@outlook.com" : "bolsasdelsalado@hotmail.com" ?></p>
                        </div>
                    </div>
                    <div class="fl p20" style="width: 35%; margin-bottom: 0px; padding-bottom: 0px">
                        <div class="letra">
                            <h1><?php echo $letra; ?></h1>
                            <div class="codigo_comprobante">COD. <?php echo $codigo_comprobante; ?></div>
                        </div>
                        <div style="float: left;">
                            <h2 style="margin-top: 0px; padding-top: 0px;"><?php echo $comprobante; ?></h2>
                            <p><b>Numero: </b>
                              <?php $exp = explode(" ",$factura->comprobante);
                              echo end($exp); ?>
                            </p>
                            <p><b>Fecha: <?php echo $factura->fecha; ?></b></p>
                            <p><b>CUIT: </b><?php echo $empresa->cuit; ?></p>
                            <p><b>Ingresos Brutos: </b>Exento</p>                            
                        </div>
                    </div>
                </div>
                <div class="borde" style="padding: 15px 20px; margin-top: 15px;">
                    <p>
                        <b>Cliente: </b><span><?php echo utf8_decode($factura->cliente->nombre); ?></span>
                        <?php if(!empty($factura->cliente->cuit)) { ?>
                            <b class="ml30">CUIT: </b><span><?php echo $factura->cliente->cuit; ?></span>
                        <?php } ?>
                    </p>
                    <?php if (!empty($factura->cliente->direccion)) { ?>
                        <p>
                            <b>Domicilio: </b> <span><?php echo utf8_decode($factura->cliente->direccion); ?>
                            <?php if (!empty($factura->cliente->localidad)) { ?>
                                - <?php echo utf8_decode($factura->cliente->localidad); ?>
                            <?php } ?>
                            </span>
                        </p>
                    <?php } ?>
                    <p>
                        <b>Condicion IVA: </b> <span><?php echo $factura->cliente->tipo_iva; ?></span>
                        <b class="ml30">Condicion de Venta: </b>
                        <span><?php echo ($factura->tipo_pago == "C") ? "Cuenta Corriente":"Efectivo"; ?></span>
                        <?php if (!empty($factura->numero_remito)) { ?>
                            <b class="ml30">Remito Nro: </b>
                            <span><?php echo $factura->numero_remito ?></span>
                        <?php } ?>
                    </p>
                </div>
                <div class="tabla">
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 10%;">Cantidad</th>
                                <th style="width: 60%;">Descripcion</th>
                                <th style="width: 15%;">Unitario</th>
                                <th style="width: 15%;">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($factura->items as $i) { ?>
                                <tr>
                                    <td><?php echo number_format($i->cantidad,2); ?></td>
                                    <td>
                                        <?php echo $i->nombre; ?>
                                        <?php if (!empty($i->descripcion)) { ?>
                                        <br/><span><?php echo $i->descripcion; ?></span>
                                        <?php } ?>
                                    </td>
                                    <td>$ <?php echo number_format((($discrimina_iva==1)?$i->neto:$i->precio),2); ?></td>
                                    <td>$ <?php echo number_format((($discrimina_iva==1)?$i->total_sin_iva:$i->total_con_iva),2); ?></td>
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
                                    <?php if (!empty($factura->observaciones)) {
                                        // Analizamos las observaciones
                                        $obs = "";
                                        if ($factura->cotizacion_dolar > 0) {
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
                                        }
                                        ?>
                                        <div style="padding: 15px 20px; margin-bottom: 15px;">
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
                                    <?php } ?>
                                </td>
                                <td style="padding: 0px; vertical-align: bottom; border-left: solid 1px <?php echo $cborde; ?>; width: 30%">
                                    <div class="totales">
                                        <p id="subtotal">
                                            <span>SUBTOTAL:</span>
                                            <span>$ <?php echo number_format($factura->subtotal,2); ?></span>
                                        </p>
                                        <?php if ($factura->porc_descuento > 0) { ?>
                                            <p id="descuento">
                                                <span>DTO. <?php echo number_format($factura->porc_descuento,2) ?> %:</span>
                                                <span>$ <?php echo number_format($factura->descuento,2) ?></span>
                                            </p>
                                            <p id="subtotal_descuento">
                                                <span>SUBTOTAL:</span>
                                                <span>$ <?php echo number_format($factura->subtotal - $factura->descuento,2); ?></span>
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
                                        <?php if ($factura->percepcion_ib > 0) { ?>
                                            <p id="descuento">
                                                <span>PERCEP. IIBB. <?php echo number_format($factura->porc_ib,2) ?> %:</span>
                                                <span>$ <?php echo number_format($factura->percepcion_ib,2) ?></span>
                                            </p>
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
                <div class="oh" style="clear: both;">
                    <div class="barcode">
                        <div><img src="/sistema/application/helpers/barcode.php?text=<?php echo $barcode; ?>" /></div>
                        <div><?php echo $barcode ?></div>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
    </div>
</body>
</html>