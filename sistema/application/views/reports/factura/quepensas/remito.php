<?php
$codigo_comprobante = "00"; //str_pad($factura->id_tipo_comprobante,2,"0",STR_PAD_LEFT);
$comprobante = ("REMITO");
$letra = "R";
$discrimina_iva = 0;

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
        <?php foreach($facturas as $factura) { ?>
        <div class="a4">
            <div class="inner">
                <div class="row">
                    <div class="col-xs-12">
                        <h2 class="original">
                            ORIGINAL
                        </h2>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-5">
                        <img style="width: 100%" src="/sistema/application/views/reports/factura/quepensas/logo.png"/>
                    </div>
                    <div class="col-sm-1 pl0 pr0">
                        <div class="letra">
                            <h1><?php echo $letra; ?></h1>
                            <div class="codigo_comprobante">COD. <?php echo $codigo_comprobante; ?></div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <h1 class="orden_publicidad">Orden de Publicidad</h1>
                        <h2 class="tipo_comprobante"><?php echo $comprobante; ?></h2>
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
                    <div class="col-sm-12"><div class="linea"></div></div>
                    <div class="col-sm-6">
                        Fecha: <b><?php echo $factura->fecha; ?></b>
                    </div>
                    <div class="col-sm-6">
                        N&uacute;mero: <b><?php echo end(explode(" ",$factura->numero)) ?></b>
                    </div>

                    <div class="col-sm-12"><div class="linea"></div></div>
                    <div class="col-sm-6">
                        Cliente: <b><?php echo utf8_decode($factura->cliente->nombre); ?></b>
                    </div>
                    <div class="col-sm-6">
                        CUIT: <b><?php echo $factura->cliente->cuit; ?></b>
                    </div>
                    <div class="col-sm-12"><div class="linea"></div></div>
                    <div class="col-sm-6">
                        Domicilio: <b><?php echo utf8_decode($factura->cliente->direccion); ?></b>
                    </div>
                    <div class="col-sm-6">
                        Localidad: <b><?php echo utf8_decode($factura->cliente->localidad); ?></b>
                    </div>
                    <div class="col-sm-12"><div class="linea"></div></div>
                    <div class="col-sm-6">
                        Condici&oacute;n IVA: <b><?php echo $factura->cliente->tipo_iva; ?></b>
                    </div>
                    <div class="col-sm-6">
                        Condici&oacute;n de Venta: <b><?php echo ($factura->tipo_pago == "C") ? "Cuenta Corriente":"Efectivo"; ?></b>
                    </div>
                    <div class="col-sm-12"><div class="linea"></div></div>
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
                    <div class="col-sm-12"><div class="linea mb20 mt20"></div></div>
                    <div class="col-sm-12">
                        <div class="fr">
                            <b>Subtotal</b> <span class="dib w100 tar">$ <?php echo number_format($factura->subtotal,2); ?></span>
                        </div>
                    </div>
                    <?php if ($discrimina_iva == 1) { ?>
                        <div class="col-sm-12"><div class="linea mb20 mt20"></div></div>
                        <div class="col-sm-12">
                            <div class="fr">
                                <b>IVA 21%</b> <span class="dib w100 tar">$ <?php echo number_format($factura->iva,2); ?></span>
                            </div>
                        </div>
                    <?php } ?>
                    <div class="col-sm-12"><div class="linea mb20 mt20"></div></div>
                    <div class="col-sm-12">
                        El valor expresado es en pesos argentinos e incluye IVA, 21%
                        <div class="fr">
                            <b>TOTAL</b> <span class="dib w100 tar">$ <?php echo number_format($factura->total,2); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php } ?>
    </div>
</body>
</html>