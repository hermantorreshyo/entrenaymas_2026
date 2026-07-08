<?php 
include_once("carrito_css.php"); 

// Si estamos usando un template en algun lenguaje en particular
if (isset($default_language)) {
  $checkout_language = $default_language;
} else {
  // Corroboramos el lenguaje
  include_once("models/Language_Model.php");
  $lm = new Language_Model($empresa->id,$conx);
  $checkout_language = $lm->get_language();
}

$ll = array(
  "titulo"=>"Compra exitosa!",
  "titulo_detalle_compra"=>"Detalle de su compra",
  "descuento"=>"Descuento",
  "iva"=>"IVA",
  "total"=>"TOTAL",
  "costo_envio"=>"COSTO DE ENVIO",
  "titulo_datos_compra"=>"Datos de la Compra",
  "ir_al_inicio"=>"Ir al inicio",
  "estado"=>"Estado",
  "aprobado"=>"Aprobado",
  "medio_pago"=>"Medio de Pago",
  "tarjeta"=>"Tarjeta de cr&eacute;dito",
  "forma_pago"=>"Forma de Pago",
  "envio"=>"Env&iacute;o",
  "retiro_sucursal"=>"Retiro en sucursal",
);
if ($checkout_language == "en") {
  $ll["titulo"] = "Successful Purchase";
  $ll["titulo_detalle_compra"] = "Detail of your Purchase";
  $ll["descuento"] = "Discount";
  $ll["iva"] = "Taxes";
  $ll["total"] = "Total";
  $ll["costo_envio"] = "Shipping Cost";
  $ll["titulo_datos_compra"] = "Details";
  $ll["ir_al_inicio"] = "Go to home";
  $ll["estado"] = "Status";
  $ll["aprobado"] = "Approved";
  $ll["medio_pago"] = "Payment Method";
  $ll["tarjeta"] = "Credit Card";
  $ll["forma_pago"] = "Method of Payment";
  $ll["envio"] = "Shipping";
  $ll["retiro_sucursal"] = "Branch Withdrawal";
}
?>
<div class="varcreative-checkout">
  <div class="varcreative-container resultado-compra">
    <div class="varcreative-col4">
      <div style="padding: 60px 30px; text-align: center;">
        <div style="font-size: 32px; padding-top: 0px; margin-bottom: 30px;" class="varcreative-titulo-compra"><?php echo $ll["titulo"] ?></div>
        <div style="">
          <img style="width: 100%; max-width: 243px" src="/sistema/resources/images/compra_exitosa.png"/>
        </div>
      </div>
    </div>
    <div class="varcreative-col8">

      <?php if (isset($mensaje_compra_ok)) { ?>
        <div class="varcreative-panel">
          <div class="varcreative-panel-body">
            <div><?php echo $mensaje_compra_ok ?></div>
          </div>
        </div>
      <?php } ?>

      <div class="varcreative-col6">
        <div class="varcreative-panel checkout_registro panel_activo">
          <a href="javascript:void(0)" class="varcreative-panel-heading"><?php echo $ll["titulo_detalle_compra"] ?></a>
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
                  <td class="tar w100">
                    <?php echo $item->total_con_iva ?> €
                  </td>
                </tr>
              <?php } ?>
              <tfoot>
                <?php if ($factura->porc_descuento > 0) { ?>
                  <tr>
                    <td><?php echo $ll["descuento"] ?> <?php echo $factura->porc_descuento ?></td>
                    <td class="tar"><?php echo $factura->descuento ?> €</td>
                  </tr>
                <?php } ?>
                <?php if ($factura->iva > 0) { ?>
                  <tr>
                    <td><?php echo $ll["iva"] ?></td>
                    <td class="tar"><?php echo $factura->iva ?> €</td>
                  </tr>
                <?php } ?>
                <tr>
                  <td><b><?php echo $ll["total"] ?>:</b></td>
                  <td class="tar"><b class="green"><?php echo $factura->total ?> €</b></td>
                </tr>
                <?php if ($factura->costo_envio > 0) { ?>
                  <tr>
                    <td><?php echo $ll["costo_envio"] ?></td>
                    <td class="tar"><?php echo $factura->costo_envio ?> €</td>
                  </tr>
                <?php } ?>
              </tfoot>
            </table>
          </div>
        </div>
      </div>
      <div class="varcreative-col6">
        <div class="varcreative-panel checkout_registro panel_activo">
          <a href="javascript:void(0)" class="varcreative-panel-heading"><?php echo $ll["titulo_datos_compra"] ?></a>
          <div class="varcreative-panel-body">
            <p style="line-height: 36px;">
              <?php if (!empty($tipo_pago)) { ?>
                <p>
                  <b><?php echo $ll["medio_pago"] ?>:</b> <?php echo $tipo_pago; ?><br/>
                </p>
              <?php } ?>
              <?php if (!empty($forma_pago)) { 
                if ($forma_pago == "credit_card") $forma_pago = $ll["tarjeta"];?>
                <p>
                  <b><?php echo $ll["forma_pago"] ?>:</b> <?php echo $forma_pago; ?><br/>
                </p>
              <?php } ?>
              <?php if ($factura->retirar_envio == 1) { ?>
                <p>
                  <b><?php echo $ll["envio"] ?>:</b> <?php echo $ll["retiro_sucursal"] ?><br/>
                </p>
              <?php } ?>
              <?php if (!empty($estado_pago)) { 
                if ($estado_pago == "approved") $estado_pago = $ll["aprobado"]; ?>
                <p>
                  <b><?php echo $ll["estado"] ?>:</b> <?php echo $estado_pago ?><br/>
                </p>
              <?php } ?>
            </p>
          </div>
        </div>
        <div class="varcreative-panel-footer">
          <a href="<?php echo mklink("/"); ?>" class="varcreative-btn varcreative-pull-right"><?php echo $ll["ir_al_inicio"] ?></a>
        </div>
      </div>
    </div>
  </div>
</div>