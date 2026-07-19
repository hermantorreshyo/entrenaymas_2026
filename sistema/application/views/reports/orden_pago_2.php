<html>
<head>
<style type="text/css">
body {
  margin: 0px;
  padding: 0px;
  font-family: Arial;
  font-size: 12px;
}
.fs14 { font-size: 14px; }
.a4 {
  width: 210mm;
  height: 291mm;
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
.fs18 { font-size: 18px; }
.fs16 { font-size: 16px; }
.fs14 { font-size: 14px; }
.fl { float: left; }
.fr { float: right; }
.cb { clear: both; }
.oh { overflow: hidden; }
.ma { margin: 0 auto; }
.mt5 { margin-top: 5px; }
.mt10 { margin-top: 10px; }
.mt20 { margin-top: 20px; }
.a4 .ma { width: 90%; margin: 0 auto; }

.tabla {
  width: 100%;
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

.bbs {
  border-bottom: solid 1px black;
}

.interdeposito {
  font-size: 15px;
  clear: both;
  margin-top: 20px;
  border: dashed 1px black;
  padding: 10px;
  line-height: 20px;
}
@media print {
  .a4 {page-break-after: always;}
}
</style>
</head>
<body>
<div class="a4">
  <div class="ma">
    
    <div class="titulo">ORDEN DE PAGO</div>

    <div class="mt10 oh bold fs16">
      <?php echo strtoupper($empresa->razon_social); ?>
    </div>
    <div class="mt10 oh">
      <?php echo $empresa->direccion; ?> -
      CUIT: <?php echo $empresa->cuit; ?> 
    </div>
    
    <div class="mt10 oh">
      Nro. Orden de Pago: <b><?php echo $orden_pago->numero_2; ?></b>
    </div>
    
    <div class="mt10 oh">
      <div class="fr">
        <div>cuit: <?php echo $orden_pago->cuit; ?></div>
        <?php if (!empty($orden_pago->convenio_multilateral)) { ?>
          <div class="mt5">IB: <?php echo $orden_pago->convenio_multilateral; ?></div>
        <?php } ?>
      </div>

      <div class="fl">
      <div class="bold"><?php echo $orden_pago->proveedor; ?></div>
      <div class="mt5"><?php echo $orden_pago->direccion; ?></div>
      <div class="mt5"><?php echo $orden_pago->localidad; ?></div>
      </div>
    </div>
    
    <div class="mt10 oh">
      Fecha de Pago: <?php echo $orden_pago->fecha; ?>
    </div>
    
    <table class="tabla mt10" cellpadding=0 cellspacing=0>
      <tr>
        <td class="tac">Fecha</td>
        <td class="tac">N&deg; Comp.</td>
        <td class="tac">Neto</td>
        <td class="tac">IVA</td>
        <td class="tac">Total</td>
      </tr>
      <?php
      $total_neto = 0;
      $total_iva = 0;
      $total_gral = 0;
      ?>
      <?php foreach($orden_pago->comprobantes as $comprobante) { ?>
        <?php
        if ($comprobante->id_tipo_comprobante == 3) {
          $total_neto = $total_neto - $comprobante->total_neto;
          $total_iva = $total_iva - $comprobante->total_iva;
          $total_gral = $total_gral - $comprobante->pago;          
        } else {
          $total_neto = $total_neto + $comprobante->total_neto;
          $total_iva = $total_iva + $comprobante->total_iva;
          $total_gral = $total_gral + $comprobante->monto;
        }
        ?>
        <tr>
          <td><?php echo $comprobante->fecha; ?></td>
          <td class="tar"><?php echo $comprobante->numero; ?></td>
          <?php if ($comprobante->id_tipo_comprobante == 3) { ?>
            <td class="tar"><?php echo "-".number_format($comprobante->total_neto,2,',','.'); ?></td>
            <td class="tar"><?php echo "-".number_format($comprobante->total_iva,2,',','.'); ?></td>
            <td class="tar"><?php echo "-".number_format($comprobante->pago,2,',','.'); ?></td>
          <?php } else { ?>
            <td class="tar"><?php echo number_format($comprobante->total_neto,2,',','.'); ?></td>
            <td class="tar"><?php echo number_format($comprobante->total_iva,2,',','.'); ?></td>
            <td class="tar"><?php echo number_format($comprobante->monto,2,',','.'); ?></td>
          <?php } ?>
        </tr>
      <?php } ?>
      <tr>
        <td></td>
        <td></td>
        <td class="tar bold"><?php echo number_format($total_neto,2,',','.'); ?></td>
        <td class="tar bold"><?php echo number_format($total_iva,2,',','.'); ?></td>
        <td class="tar bold"><?php echo number_format($total_gral,2,',','.'); ?></td>
      </tr>
    </table>    
    
    <div style="margin-left: 32px; margin-top: 15px; overflow: hidden">
      <div class="bbs oh bold">
        Valores entregados:
      </div>
      <?php if ($orden_pago->ret_ing_brutos != 0) { ?>
        <div class="bbs oh mt5">
          Retenci&oacute;n de Ingresos Brutos<br/>
          <div>
            Al&iacute;cuota: 
            <div class="fr fs14 cb"><?php echo number_format($orden_pago->porc_ret_ib,2,',','.'); ?>%</div>
          </div>
          <div>
            Monto Retenido:
            <div class="fr fs14 cb">$ <?php echo number_format(abs($orden_pago->ret_ing_brutos),2,',','.'); ?></div>
          </div>
        </div>
      <?php } ?>
      <?php if ($orden_pago->ret_ganancias != 0) { ?>
        <div class="bbs oh mt5">
          Ret. Ganancias:
          <div class="fr fs14">$ <?php echo number_format(abs($orden_pago->ret_ganancias),2,',','.'); ?></div>
        </div>
      <?php } ?>
      <?php $total_cheques = 0; ?>
      <?php foreach($orden_pago->cheques as $cheque) { ?>
        <div class="bbs oh mt5">
          Cheque <?php echo mb_convert_encoding($cheque->banco, 'ISO-8859-1', 'UTF-8'); ?> N&deg; <?php echo $cheque->numero; ?> <?php echo $cheque->fecha_cobro; ?>:
          <div class="fr fs14 bold">$ <?php echo number_format(abs($cheque->monto),2,',','.'); ?></div>
          <?php $total_cheques = $total_cheques + $cheque->monto; ?>
        </div>
      <?php } ?>
      <div class="bbs oh mt5">
        Efectivo        
        <div class="fr fs14">$ <?php echo number_format($orden_pago->efectivo,2,',','.'); ?></div>
      </div>
      <?php if ($orden_pago->total_depositos > 0) { ?>
        <div class="bbs oh mt5">
          Dep&oacute;sito/Transferencia        
          <div class="fr fs14">$ <?php echo number_format($orden_pago->total_depositos,2,',','.'); ?></div>
        </div>
      <?php } ?>
      <?php if ($orden_pago->descuento != 0) { ?>
        <div class="bbs oh mt5">
          Descuento:
          <div class="fr fs14">$ <?php echo number_format($orden_pago->descuento,2,',','.'); ?></div>
        </div>
      <?php } ?>
      <?php if ($orden_pago->rotura != 0) { ?>
        <div class="bbs oh mt5">
          Rotura / Devoluci&oacute;n:
          <div class="fr fs14">$ <?php echo number_format($orden_pago->rotura,2,',','.'); ?></div>
        </div>
      <?php } ?>
      
      
      <?php if (!empty($orden_pago->observaciones)) { ?>
        <div class="bbs oh bold mt20">
          Observaciones:
        </div>
        <div class="oh mt5 mb15">
          <?php echo mb_convert_encoding(nl2br($orden_pago->observaciones), 'ISO-8859-1', 'UTF-8'); ?>
        </div>
      <?php } ?>
      
      
      <div class="bbs oh" style="width: 230px; margin-top: 20px; float: right">
        Total Pagado:
        <?php
        $total = 0;
        $total+= abs($orden_pago->ret_ing_brutos);
        $total+= abs($orden_pago->ret_ganancias);
        $total+= $total_cheques;
        $total+= $orden_pago->efectivo;
        $total+= $orden_pago->total_depositos;
        $total+= $orden_pago->descuento;
        $total+= $orden_pago->rotura;
        ?>
        <div class="fr fs14 bold">$ <?php echo number_format($total,2,',','.'); ?></div>
      </div>      
    </div>

    <?php if ($total > $total_gral) { ?>
      <div class="bbs oh" style="width: 230px; margin-bottom: 20px; float: right">
        <div class="bbs oh mt5">
          Saldo a favor:
          <div class="fr fs14">$ <?php echo number_format($total - $total_gral,2,',','.'); ?></div>
        </div>
      </div>      
    <?php } ?>

    
    <!--<div class="cb titulo mt20">para La Uni&oacute;n</div>-->
    
    <div class="oh" style="margin-top: 40px">
      Recib&iacute; de <?php echo $empresa->razon_social; ?> la cantidad de pesos
      <?php
      $V=new EnLetras(); 
      echo strtoupper($V->ValorEnLetras($total,""));
      ?>
      en concepto de pago de los comprobantes arriba detallados.
    </div>
    
    <div style="overflow: hidden">
      <div class="mt10 fl" style="width: 130px">
        <div style="border-top: dashed 1px black; margin-top: 35px;"></div>
        <div style="text-align: center; font-size: 12px; margin-top: 5px;">Fecha de Pago</div>
      </div>
      <div class="mt10 fr" style="width: 150px">
        <div style="border-top: dashed 1px black; margin-top: 35px;"></div>
        <div style="text-align: center; font-size: 12px; margin-top: 5px;">Firma</div>
        <div style="border-top: dashed 1px black; margin-top: 35px"></div>
        <div style="text-align: center; font-size: 12px; margin-top: 5px;">Aclaracion</div>
      </div>
    </div>
    
    <?php if ($orden_pago->forma_pago) { ?>
    
      <div class="interdeposito">
        <?php if (!empty($orden_pago->banco)) { ?>
          <b><?php echo $orden_pago->banco; ?></b><br/>
        <?php } ?>
        <b><?php echo $orden_pago->proveedor; ?></b><br/>
        <?php if (!empty($orden_pago->cuenta_bancaria)) { ?>
          Cuenta Nro: <?php echo $orden_pago->cuenta_bancaria; ?><br/>
        <?php } ?>
        <?php if (!empty($orden_pago->cbu)) { ?>
          CBU: <?php echo $orden_pago->cbu; ?><br/>
        <?php } ?>
        Monto: <b>$ <?php echo $orden_pago->efectivo; ?></b><br/>
        Depositante: <?php echo $empresa->razon_social; ?><br/>
        CUIT: <?php echo $empresa->cuit; ?><br/>
        Fecha: <?php echo $orden_pago->fecha; ?>
      </div>
    
    <?php } ?>
    
  </div>
</div>
<div class="a4">
  <div class="ma">
    <div class="titulo">ORDEN DE PAGO</div>

    <div class="mt10 oh bold fs16">
      <?php echo strtoupper($empresa->razon_social); ?>
    </div>
    <div class="mt10 oh">
      <?php echo $empresa->direccion; ?> -
      CUIT: <?php echo $empresa->cuit; ?> 
    </div>
    
    <div class="mt10 oh">
      Nro. Orden de Pago: <b><?php echo $orden_pago->numero_2; ?></b>
    </div>    
    
    <div class="mt10 oh">
      <div class="fr">
        <div>cuit: <?php echo $orden_pago->cuit; ?></div>
        <?php if (!empty($orden_pago->convenio_multilateral)) { ?>
          <div class="mt5">IB: <?php echo $orden_pago->convenio_multilateral; ?></div>
        <?php } ?>
      </div>

      <div class="fl">
      <div class="bold"><?php echo $orden_pago->proveedor; ?></div>
      <div class="mt5"><?php echo $orden_pago->direccion; ?></div>
      <div class="mt5"><?php echo $orden_pago->localidad; ?></div>
      </div>
    </div>
    
    <div class="mt10 oh">
      Fecha de Pago: <?php echo $orden_pago->fecha; ?>
    </div>
    
    <table class="tabla mt10" cellpadding=0 cellspacing=0>
      <tr>
        <td class="tac">Fecha</td>
        <td class="tac">N&deg; Factura</td>
        <td class="tac">Neto</td>
        <td class="tac">IVA</td>
        <td class="tac">Total</td>
      </tr>
      <?php
      $total_neto = 0;
      $total_iva = 0;
      $total_gral = 0;
      ?>
      <?php foreach($orden_pago->comprobantes as $comprobante) { ?>
        <?php
        if ($comprobante->id_tipo_comprobante == 3) {
          $total_neto = $total_neto - $comprobante->total_neto;
          $total_iva = $total_iva - $comprobante->total_iva;
          $total_gral = $total_gral - $comprobante->pago;          
        } else {
          $total_neto = $total_neto + $comprobante->total_neto;
          $total_iva = $total_iva + $comprobante->total_iva;
          $total_gral = $total_gral + $comprobante->monto;
        }
        ?>
        <tr>
          <td><?php echo $comprobante->fecha; ?></td>
          <td class="tar"><?php echo $comprobante->numero; ?></td>
          <?php if ($comprobante->id_tipo_comprobante == 3) { ?>
            <td class="tar"><?php echo "-".number_format($comprobante->total_neto,2,',','.'); ?></td>
            <td class="tar"><?php echo "-".number_format($comprobante->total_iva,2,',','.'); ?></td>
            <td class="tar"><?php echo "-".number_format($comprobante->pago,2,',','.'); ?></td>
          <?php } else { ?>
            <td class="tar"><?php echo number_format($comprobante->total_neto,2,',','.'); ?></td>
            <td class="tar"><?php echo number_format($comprobante->total_iva,2,',','.'); ?></td>
            <td class="tar"><?php echo number_format($comprobante->monto,2,',','.'); ?></td>
          <?php } ?>
        </tr>
      <?php } ?>
      <tr>
        <td></td>
        <td></td>
        <td class="tar bold"><?php echo number_format($total_neto,2,',','.'); ?></td>
        <td class="tar bold"><?php echo number_format($total_iva,2,',','.'); ?></td>
        <td class="tar bold"><?php echo number_format($total_gral,2,',','.'); ?></td>
      </tr>
    </table>
    
    <div style="width: 330px; margin-left: 32px; margin-top: 15px; overflow: hidden">
      <div class="bbs oh bold">
        Valores entregados:
      </div>
      <?php if ($orden_pago->ret_ing_brutos != 0) { ?>
        <div class="bbs oh mt5">
          Retenci&oacute;n de Ingresos Brutos<br/>
          <div>
            Al&iacute;cuota: 
            <div class="fr fs14 cb"><?php echo number_format($orden_pago->porc_ret_ib,2,',','.'); ?>%</div>
          </div>
          <div>
            Monto Retenido:
            <div class="fr fs14 cb">$ <?php echo number_format(abs($orden_pago->ret_ing_brutos),2,',','.'); ?></div>
          </div>
        </div>
      <?php } ?>
      <?php if ($orden_pago->ret_ganancias != 0) { ?>
        <div class="bbs oh mt5">
          Ret. Ganancias:
          <div class="fr fs14">$ <?php echo number_format(abs($orden_pago->ret_ganancias),2,',','.'); ?></div>
        </div>
      <?php } ?>
      <?php $total_cheques = 0; ?>
      <?php foreach($orden_pago->cheques as $cheque) { ?>
        <div class="bbs oh mt5">
          Cheque <?php echo mb_convert_encoding($cheque->banco, 'ISO-8859-1', 'UTF-8'); ?> N&deg; <?php echo $cheque->numero; ?> <?php echo $cheque->fecha_cobro; ?>:
          <div class="fr fs14 bold">$ <?php echo number_format(abs($cheque->monto),2,',','.'); ?></div>
          <?php $total_cheques = $total_cheques + $cheque->monto; ?>
        </div>
      <?php } ?>
      <div class="bbs oh mt5">
        Efectivo
        <div class="fr fs14">$ <?php echo number_format($orden_pago->efectivo,2,',','.'); ?></div>
      </div>
      <?php if ($orden_pago->total_depositos > 0) { ?>
        <div class="bbs oh mt5">
          Dep&oacute;sito/Transferencia        
          <div class="fr fs14">$ <?php echo number_format($orden_pago->total_depositos,2,',','.'); ?></div>
        </div>
      <?php } ?>
      <?php if ($orden_pago->descuento != 0) { ?>
        <div class="bbs oh mt5">
          Descuento:
          <div class="fr fs14">$ <?php echo number_format($orden_pago->descuento,2,',','.'); ?></div>
        </div>
      <?php } ?>
      <?php if ($orden_pago->rotura != 0) { ?>
        <div class="bbs oh mt5">
          Rotura / Devoluci&oacute;n:
          <div class="fr fs14">$ <?php echo number_format($orden_pago->rotura,2,',','.'); ?></div>
        </div>
      <?php } ?>

      
      <?php if (!empty($orden_pago->observaciones)) { ?>
        <div class="bbs oh bold mt20">
          Observaciones:
        </div>
        <div class="oh mt5 mb15">
          <?php echo mb_convert_encoding(nl2br($orden_pago->observaciones), 'ISO-8859-1', 'UTF-8'); ?>
        </div>
      <?php } ?>
      
      
      <div class="bbs oh" style="width: 230px; margin-top: 20px; float: right">
        Total Pagado:
        <?php
        $total = 0;
        $total+= abs($orden_pago->ret_ing_brutos);
        $total+= abs($orden_pago->ret_ganancias);
        $total+= $total_cheques;
        $total+= $orden_pago->efectivo;
        $total+= $orden_pago->total_depositos;
        $total+= $orden_pago->descuento;
        $total+= $orden_pago->rotura;
        ?>
        <div class="fr fs14 bold">$ <?php echo number_format($total,2,',','.'); ?></div>
      </div>
    </div>
    
    <div class="cb titulo mt20">para el proveedor</div>
    
  </div>
</div>
</body>
</html>