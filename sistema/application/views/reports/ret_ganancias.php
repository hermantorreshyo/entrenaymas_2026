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
    
    <?php if (isset($empresa->config["firma_path"]) && !empty($empresa->config["firma_path"])) { ?>
        <img src="/sistema/<?php echo $empresa->config["firma_path"] ?>" style="position: absolute; top: 390px; left: 250px; z-index:0"/>
    <?php } ?>

    <div style="overflow:hidden">
        <h1 class="fl">
            ANEXO IV RESOLUCION GENERAL Nº 738 <br/>
            CERTIFICADO DE RETENCION DE IMPUESTOS A LAS GANANCIAS (RG. 830)
        </h1>
        <div class="fr tar mt10">
            Certificado Nº: 0000 <?php echo date("Y");?> <?php echo str_pad($orden_pago->numero_certificado_ret_ganancias,8,'0',STR_PAD_LEFT); ?><br/>
            Fecha: <?php echo date("d/m/Y"); ?>
        </div>
    </div>
    
    <div class="cb lh">
        <b>A. Datos del Agente de Retenci&oacute;n</b><br/>
        <b>Apellido y Nombre o Denominaci&oacute;n: </b>
        <?php echo $empresa->razon_social ?>
        <br/>
        <b>C.U.I.T.: </b>
        <?php echo $empresa->cuit ?>
        <br/>
        <b>Domicilio: </b>
        <?php echo $empresa->direccion ?>
    </div>
    
    <div class="cb lh mt10">
        <b>B. Datos del sujeto retenido</b><br/>
        <b>Apellido y Nombre o Denominaci&oacute;n: </b><?php echo $orden_pago->proveedor; ?> <br/>
        <b>C.U.I.T./C.U.I.L./C.D.I.: </b>Nº <?php echo $orden_pago->cuit; ?><br/>
        <b>Domicilio: </b> <?php echo $orden_pago->direccion; ?> - Localidad: <?php echo $orden_pago->localidad; ?>
    </div>
    
    <div class="cb lh mt10">
        <b>C. Datos de Retenci&oacute;n Practicada</b><br/>
        <b>Impuesto: </b>Impuesto a las Ganancias
        <b class="ml20">R&eacute;gimen: </b>
            <?php if ($orden_pago->tipo_proveedor == 1) { ?>
                Enajenaci&oacute;n de bienes muebles y bienes de cambio
            <?php } else if ($orden_pago->tipo_proveedor == 2) { ?>
                Alquileres o arrendamientos de bienes muebles.
            <?php } else if ($orden_pago->tipo_proveedor == 3) { ?>
                Profesionales liberales, oficios, albacea, mandatorio.
            <?php } ?>
        <br/>
        <b>Comprobante que origina la Retenci&oacute;n: </b>Recibo/Orden de Pago Nº: <?php echo $orden_pago->numero_2; ?><br/>
        <b>Importe de comprobantes: </b>$ <?php echo number_format($total_neto,2,',','.'); ?>
        <b class="ml20">Base del c&aacute;lculo: </b>$
            <?php if ($orden_pago->tipo_proveedor == 1) { ?>
                <?php echo number_format($total_neto-12000,2,',','.'); ?>
            <?php } else if ($orden_pago->tipo_proveedor == 3) { ?>
                <?php echo number_format($total_neto-1200,2,',','.'); ?>
            <?php } ?>
        <br/>
        <b>Importe retenido en el mes: </b>$ <?php echo number_format($importe_retenido - $orden_pago->ret_ganancias,2,',','.'); ?> <br/>
        <b>Monto de la Retenci&oacute;n: </b>$ <?php echo number_format($orden_pago->ret_ganancias,2,',','.'); ?> <br/>
    </div>
    
    <div class="cb mt20 lh">
        <b>Firma del Agente de Retenci&oacute;n: </b> <br/>
        <b>Aclaraci&oacute;n: </b>
        <?php echo $empresa->razon_social ?>
        <br/>
        <b>Cargo: </b> Titular
    </div>
    
    <?php if ($simple == 0) { ?>

      <?php if (isset($empresa->firma) && !empty($empresa->firma)) { ?>
        <img src="<?php echo $empresa->firma ?>" style="position: absolute; top: 890px; left: 250px; z-index:0"/>
      <?php } ?>
    
      <div style="overflow:hidden; margin-top: 70px">
        <h1 class="fl">
            ANEXO IV RESOLUCION GENERAL Nº 738 <br/>
            CERTIFICADO DE RETENCION DE IMPUESTOS A LAS GANANCIAS (RG. 830)
        </h1>
        <div class="fr tar mt10">
            Certificado Nº: 0000 <?php echo date("Y");?> <?php echo str_pad($orden_pago->numero_certificado_ret_ganancias,8,'0',STR_PAD_LEFT); ?><br/>
            Fecha: <?php echo date("d/m/Y"); ?>
        </div>
      </div>
    
      <div class="cb lh">
        <b>A. Datos del Agente de Retenci&oacute;n</b><br/>
        <b>Apellido y Nombre o Denominaci&oacute;n: </b>
          <?php echo $empresa->razon_social ?>
        <br/>
        <b>C.U.I.T.: </b>
        <?php echo $empresa->cuit ?>
        <br/>
        <b>Domicilio: </b>
        <?php echo $empresa->direccion ?>
      </div>
      <div class="cb lh mt10">
        <b>B. Datos del sujeto retenido</b><br/>
        <b>Apellido y Nombre o Denominaci&oacute;n: </b><?php echo $orden_pago->proveedor; ?> <br/>
        <b>C.U.I.T./C.U.I.L./C.D.I.: </b>Nº <?php echo $orden_pago->cuit; ?><br/>
        <b>Domicilio: </b> <?php echo $orden_pago->direccion; ?> - Localidad: <?php echo $orden_pago->localidad; ?>
      </div>
      <div class="cb lh mt10">
        <b>C. Datos de Retenci&oacute;n Practicada</b><br/>
        <b>Impuesto: </b>Impuesto a las Ganancias
        <b class="ml20">R&eacute;gimen: </b>Enajenaci&oacute;n de bienes muebles y bienes de cambio<br/>
        <b>Comprobante que origina la Retenci&oacute;n: </b>Recibo/Orden de Pago Nº: <?php echo $orden_pago->numero_2; ?><br/>
        <b>Importe de comprobantes: </b>$ <?php echo number_format($total_neto,2,',','.'); ?>
        <b class="ml20">Base del c&aacute;lculo: </b>$ <?php echo number_format($total_neto-12000,2,',','.'); ?> <br/>
        <b>Importe retenido en el mes: </b>$ <?php echo number_format($importe_retenido - $orden_pago->ret_ganancias,2,',','.'); ?> <br/>
        <b>Monto de la Retenci&oacute;n: </b>$ <?php echo number_format($orden_pago->ret_ganancias,2,',','.'); ?> <br/>
      </div>
      <div class="cb mt20 lh">
        <b>Firma del Agente de Retenci&oacute;n: </b> <br/>
        <b>Aclaraci&oacute;n: </b> 
        <?php echo $empresa->razon_social ?>
        <br/>
        <b>Cargo: </b> Titular
      </div>

    <?php if (isset($empresa->config["firma_path"]) && !empty($empresa->config["firma_path"])) { ?>
        <img src="/sistema/<?php echo $empresa->config["firma_path"] ?>" style="position: absolute; top: 890px; left: 250px; z-index:0"/>
    <?php } ?>

    <?php } ?>

</div>
</div>
</body>
</html>