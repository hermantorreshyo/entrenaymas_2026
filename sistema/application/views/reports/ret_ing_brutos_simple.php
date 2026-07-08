<!DOCTYPE>
<html>
<head>
<style type="text/css">
h1 {
    font-size: 14px;
}
body {
    margin: 0px;
    padding: 0px;
    font-family: Arial;
    font-size: 12px;
}
.fs12 { font-size: 12px !important; }
.fs14 { font-size: 14px !important; }
.a4 {
    width: 210mm;
    height: 291mm;
    overflow: hidden;
    /*border: solid 1px black;*/
}
.a4_2 {
    padding: 10px;
    width: 46%;
    overflow: hidden;
}
.bold {
    font-weight: bold;
}
.titulo {
    border: solid 1px black;
    padding: 3px;
    text-align: center;
    overflow: hidden;
}
.fl { float: left; }
.fr { float: right; }
.cb { clear: both; }
.oh { overflow: hidden; }
.mt5 { margin-top: 5px; }
.mt10 { margin-top: 10px; }
.mt15 { margin-top: 15px; }
.mt20 { margin-top: 20px; }
.ml5 { margin-left: 5px; }
.ml10 { margin-left: 10px; }
.ml20 { margin-left: 20px; }

.tabla {
    border-collapse: collapse;
}
.tabla tr td {
    border: solid 1px black;
    font-size: 11px;
    padding: 3px;
}
.tal { text-align: left; }
.tac { text-align: center; }
.tar { text-align: right; }

.b1b { border: solid 1px black; }
.bbs { border-bottom: solid 1px black; }

.p5 { padding: 5px; }
.p10 { padding: 10px; }
.p15 { padding: 15px; }
.p20 { padding: 20px; }
.p30 { padding: 30px; }
.p40 { padding: 40px; }

.numeral {
    border-right: solid 1px black;
    position: relative;
    font-size: 12px;
    padding: 1px 3px;
    float: left;
    font-weight: bold;
}
.lh {
    line-height: 20px !important;
}
</style>
</head>
<body>
<?php //print_r($orden_pago); ?>
<div class="a4">
<div class="p30" style="position: relative">
    
        <img src="/sistema/<?php echo $empresa->config["firma_path"] ?>" style="position: absolute; top: 350px; left: 250px; z-index:0"/>
    
    <div style="overflow:hidden">
        <h1 class="fl">
            IMPUESTO SOBRE LOS INGRESOS BRUTOS<br/>
            COMPROBANTE DE RETENCION R- 122 V2
        </h1>
        <div class="fr tar mt10">
            Certificado N&deg;: 0000 <?php echo str_pad($orden_pago->numero_certificado_ret_ib,8,'0',STR_PAD_LEFT); ?><br/>
            Fecha: <?php echo date("d/m/Y"); ?>
        </div>
    </div>
    
    <div class="cb lh">
        <b>A. Datos del Agente de Retenci&oacute;n</b><br/>
        <b>Apellido y Nombre o Denominaci&oacute;n: </b><?php echo $empresa->razon_social; ?> <br/>
        <b>C.U.I.T.: </b>N&deg; <?php echo $empresa->cuit; ?><br/>
        <b>Domicilio: </b> <?php echo $empresa->direccion; ?> - Localidad: <?php echo $empresa->localidad; ?>. C.P.: <?php echo $empresa->codigo_postal; ?>
    </div>
    
    <div class="cb lh mt10">
        <b>B. Datos del sujeto retenido</b><br/>
        <b>Raz&oacute;n Social: </b><?php echo $orden_pago->proveedor; ?> <br/>
        <b>Nro. de C.U.I.T.: </b>N&deg; <?php echo $orden_pago->cuit; ?><br/>
        <b>Nro. de Inscripcion Ingresos Brutos: </b>N&deg; <?php echo $orden_pago->convenio_multilateral; ?><br/>
        <b>Domicilio: </b> <?php echo $orden_pago->direccion; ?> - Localidad: <?php echo $orden_pago->localidad; ?>
    </div>
    
    <?php
    $total_neto = 0;
    foreach($orden_pago->comprobantes as $comp) {
        if ($comp->id_tipo_comprobante == 3)
            $total_neto = $total_neto - $comp->total_neto;
        else
            $total_neto = $total_neto + $comp->total_neto;
    }
    ?>
    <div class="cb lh mt10">
        <b>C. Datos de la Operacion</b><br/>
        <b>Fecha de Operaci&oacute;n: </b><?php echo $orden_pago->fecha; ?>
        <b class="ml20">C&oacute;digo: </b>6<br/>
        <b>Base Imponible: </b>$ <?php echo number_format($total_neto,2,',','.'); ?>
        <b class="ml20">Al&iacute;cuota: </b><?php echo number_format($orden_pago->porc_ret_ib,2,',','.'); ?>
        <b class="ml20">Importe Retenido: </b>$ <?php echo number_format(abs($orden_pago->ret_ing_brutos),2,',','.'); ?> <br/>
    </div>
    
    <div class="cb mt20 lh">
        <b>Firma del Agente de Retenci&oacute;n: </b> <br/>
    </div>
    
</div>
</div>
</body>
</html>