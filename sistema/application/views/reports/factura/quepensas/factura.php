<?php
$codigo_comprobante = str_pad($factura->id_tipo_comprobante,2,"0",STR_PAD_LEFT);

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
    case 998:
        $comprobante = ("PRESUPUESTO"); $letra = "X"; $discrimina_iva = 0; break;
    case 999:
        $comprobante = ("REMITO"); $letra = "X"; $discrimina_iva = 0; break;
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
<link href="https://fonts.googleapis.com/css?family=Raleway:200,400,600,900" rel="stylesheet">
<link href="/sistema/resources/css/common.css" rel="stylesheet"/>
<link href="/sistema/resources/css/bootstrap.css" rel="stylesheet"/>
<style type="text/css">
body { font-family: 'Raleway', sans-serif; font-size: 14px; background-color: #EEE; }
.a4 { width: 210mm; height: 291mm; overflow: hidden; margin: 0 auto; background-color: white; }
.inner { padding: 40px; position: relative; }
.orden_publicidad { padding: 0px; margin-top: 0px; margin-bottom: 3px; font-weight: 400; font-size: 22px; }
.tipo_comprobante { font-weight: 900; text-transform: uppercase; font-size: 24px; padding: 0px; margin-top: 0px; margin-bottom: 5px; }
.original { margin-top: 0px; margin-bottom: 15px; text-align: center; padding: 10px; font-size: 16px; background-color: #e0e0e0; font-weight: 400; text-transform: uppercase; }
p { font-size: 11px; }
b { font-weight: 600; }
.linea { float: left; border-top: solid 1px #878787; clear: both; width: 100%; margin-top: 5px; margin-bottom: 5px; }

.tabla { margin-top: 30px; margin-bottom: 30px; min-height: 400px; }
.tabla table { width: 100%; border-collapse: collapse; font-size: 13px; }
.tabla table thead th { font-size: 16px; font-weight: 600; background-color: transparent; padding: 8px; border-bottom: solid 1px #878787; }
.tabla table td { padding: 8px; vertical-align: top; }

.totales { margin-top: 15px; margin-bottom: 15px; }
.totales > p { margin-bottom: 15px; margin-top: 10px;}
.totales > p > span { font-weight: bold; display: inline-block; text-align: left; width: 48%; }
.totales > p > span:first-child { font-weight: normal; text-align: right;  }
#total { font-weight: bold; font-size: 16px; margin-top: 15px; border-top: solid 1px #878787; padding-top: 15px; }

.cae_container { margin-top: 20px; }
.cae_container > p > span { text-align: left; margin-right: 10px; }
.cae_container > p > span:first-child { font-weight: bold;  }

.letra { background-color: text-align: center; }
.letra h1 { font-size: 42px; margin: 0px; padding: 10px; text-align: center; border: solid 1px #878787; font-weight: 900; }
.letra .codigo_comprobante { text-align: center; font-weight: 600; font-size: 9px; margin-top: 3px; margin-bottom: 3px; }

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
                <div class="row">
                    <div class="col-xs-12">
                        <h2 class="original">
                          <?php if ($copia == 0) echo "ORIGINAL";
                          else if ($copia == 1) echo "DUPLICADO";
                          else if ($copia == 2) echo "TRIPLICADO";
                          else if ($copia == 3) echo "CUADRUPLICADO"; ?>
                        </h2>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-5">
                        <img style="width: 100%" src="/sistema/application/views/reports/factura/quepensas/logo.png"/>
                    </div>
                    <div class="col-xs-1 pl0 pr0">
                        <div class="letra">
                            <h1><?php echo $letra; ?></h1>
                            <div class="codigo_comprobante">COD. <?php echo $codigo_comprobante; ?></div>
                        </div>
                    </div>
                    <div class="col-xs-6">
                        <h2 class="tipo_comprobante"><?php echo $comprobante ?></h2>
                        <p>
                            Raz&oacute;n Social: <b><?php echo $empresa->razon_social?></b>
                            | Pueyrred&oacute;n 255. Chacabuco (6740)<br/>
                            <b>IVA RESPONSABLE INSCRIPTO</b>
                            | Inicio de actividades: 03/01/2017<br/>
                            CUIT: <b>20-30347032-9</b>
                        </p>
                    </div>
                </div>
                <div class="row fs16 mt30">
                    <div class="col-xs-12"><div class="linea"></div></div>
                    <div class="col-xs-6">
                        Fecha: <b><?php echo $factura->fecha; ?></b>
                    </div>
                    <div class="col-xs-6">
                        N&uacute;mero: <b><?php echo end(explode(" ",$factura->comprobante)) ?></b>
                    </div>
                    <div class="col-xs-12"><div class="linea"></div></div>
                    <div class="col-xs-6">
                        Cliente: <b><?php echo ($factura->cliente->nombre); ?></b>
                    </div>
                    <div class="col-xs-6">
                        CUIT: <b><?php echo $factura->cliente->cuit; ?></b>
                    </div>
                    <div class="col-xs-12"><div class="linea"></div></div>
                    <div class="col-xs-6">
                        Domicilio: <b><?php echo ($factura->cliente->direccion); ?></b>
                    </div>
                    <div class="col-xs-6">
                        Localidad: <b><?php echo ucfirst(strtolower($factura->cliente->localidad)); ?></b>
                    </div>
                    <div class="col-xs-12"><div class="linea"></div></div>
                    <div class="col-xs-6">
                        Condici&oacute;n IVA: <b><?php echo $factura->cliente->tipo_iva; ?></b>
                    </div>
                    <div class="col-xs-6">
                        Condici&oacute;n de Venta: <b><?php echo ($factura->tipo_pago == "C") ? "Cuenta Corriente":"Efectivo"; ?></b>
                    </div>
                    <div class="col-xs-12"><div class="linea"></div></div>
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
                
                <div class="row fs16 mt30">
                    <div class="col-xs-12"><div class="linea mb20 mt20"></div></div>

                    <?php if (!empty($factura->observaciones)) {
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
                        } ?>
                        <div class="col-xs-12">
                          <?php echo $obs ?>
                        </div>
                        <div class="col-xs-12"><div class="linea mb20 mt20"></div></div>
                    <?php } ?>

                    <div class="">
                      <div class="com-xs-6">

                        <div class="col-xs-12">
                          <?php if (!empty($factura->cae)) { ?>
                            <div class="fl">
                              <b>C.A.E.:</b> <span class="dib w100 tar"><?php echo $factura->cae ?></span>
                            </div>
                          <?php } ?>
                            <div class="fr">
                                <b>Subtotal</b> <span class="dib w100 tar">$ <?php echo number_format($factura->subtotal,2); ?></span>
                            </div>
                        </div>
                        <?php if ($discrimina_iva == 1) { ?>
                            <div class="col-xs-12"><div class="linea mb20 mt20"></div></div>
                            <div class="col-xs-12">
                              <?php if (!empty($factura->cae)) { ?>
                                <div class="fl">
                                  <b>Fecha Vto. de CAE:</b> <span class="dib w100 tar"><?php echo $factura->fecha_vto ?></span>
                                </div>
                              <?php } ?>
                                <div class="fr">
                                    <b>IVA 21%</b> <span class="dib w100 tar">$ <?php echo number_format($factura->iva,2); ?></span>
                                </div>
                            </div>
                        <?php } else if (!empty($factura->cae)) { ?>
                          <div class="col-xs-12"><div class="linea mb20 mt20"></div></div>
                          <div class="col-xs-12">
                            <div class="fl">
                              <b>Fecha Vto. de CAE:</b> <span class="dib w100 tar"><?php echo $factura->fecha_vto ?></span>
                            </div>
                          </div>
                        <?php } ?>
                        <div class="col-xs-12"><div class="linea mb20 mt20"></div></div>
                        <div class="col-xs-12">
                            <div class="fr fs18">
                                <b>TOTAL</b> <span class="dib w100 tar">$ <?php echo number_format($factura->total,2); ?></span>
                            </div>
                        </div>

                      </div>
                    </div>

                    <div class="oh" style="clear: both;">
                        <div class="barcode">
                            <div><img src="/sistema/application/helpers/barcode.php?text=<?php echo $barcode; ?>" /></div>
                            <div><?php echo $barcode ?></div>
                        </div>
                    </div>                    

                </div>
            </div>
        </div>
        <?php } ?>
    </div>
</body>
</html>