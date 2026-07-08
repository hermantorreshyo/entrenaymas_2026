<?php include_once("carrito_css.php"); ?>
<div class="varcreative-checkout">
  <div class="varcreative-container resultado-compra">
    <div class="varcreative-col4">
      <div style="padding: 60px 30px; text-align: center;">
        <div style="font-size: 32px; padding-top: 0px; margin-bottom: 30px;" class="varcreative-titulo-compra">Compra cancelada</div>
        <div style="">
          <img style="width: 100%; max-width: 243px" src="/sistema/resources/images/compra_rechazada.png"/>
        </div>
      </div>
    </div>
    <div class="varcreative-col8">
      <div class="varcreative-col6">
        <div class="varcreative-panel checkout_registro panel_activo">
          <a href="javascript:void(0)" class="varcreative-panel-heading">Detalle de su compra</a>
          <div class="varcreative-panel-body">
            <table class="compra_ok_table">
              <?php foreach($factura->items as $item) { ?>
                <tr>
                  <td>
                    <b><?php echo $item->nombre ?>
                    x <?php echo round($item->cantidad,0) ?></b>
                    <?php if (isset($item->descripcion) && !empty($item->descripcion)) { ?>
                      <br><span><?php echo $item->descripcion ?></span>
                    <?php } ?>
                  </td>
                  <td class="tar grey w100">
                    <?php echo $item->total_con_iva ?> €
                  </td>
                </tr>
              <?php } ?>
              <tfoot>
                <tr>
                  <td><b>TOTAL:</b></td>
                  <td class="tar"><b class="green"><?php echo $factura->total ?> €</b></td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
      </div>
      <div class="varcreative-col6">
        <div class="varcreative-panel checkout_registro panel_activo">
          <a href="javascript:void(0)" class="varcreative-panel-heading">Datos de compra</a>
          <div class="varcreative-panel-body">
            <p style="line-height: 26px;">
              <?php if (!empty($tipo_pago)) { ?>
                <p>
                  <b>Medio de Pago:</b> <?php echo $tipo_pago; ?><br/>
                </p>
              <?php } ?>
              <?php if (!empty($forma_pago)) { 
                if ($forma_pago == "credit_card") $forma_pago = "Tarjeta de cr&eacute;dito";?>
                <p>
                  <b>Forma de Pago:</b> <?php echo $forma_pago; ?><br/>
                </p>
              <?php } ?>
              <?php if (!empty($estado_pago)) { 
                if ($estado_pago == "approved") $estado_pago = "Aprobado";
                else if ($estado_pago == "pending") $estado_pago = "Pendiente"; ?>
                <p>
                  <b>Estado:</b> <?php echo $estado_pago ?><br/>
                </p>
              <?php } ?>
            </p>
          </div>
          <div class="varcreative-panel-footer">
            <a href="<?php echo mklink("/"); ?>" class="varcreative-btn varcreative-pull-right">Ir al inicio</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>