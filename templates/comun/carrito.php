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
  "carrito_vacio"=>"Su carrito de compras est&aacute; vac&iacute;o.",
  "titulo_detalle_compra"=>"Detalle de su compra",
  "header_producto"=>"Nombre de Producto",
  "header_precio"=>"Precio",
  "header_cantidad"=>"Cantidad",
  "header_subtotal"=>"Subtotal",
  "totales"=>"Totales",
  "descuento"=>"Descuento",
  "iva"=>"IVA",
  "no_definido"=>"No Definido",
  "total_general"=>"Total general",
  "finalizar_compra"=>"Finalizar compra",
  "quitar"=>"Quitar",
);
if ($checkout_language == "en") {
  $ll["carrito_vacio"] = "Your cart is empty.";
  $ll["titulo_detalle_compra"] = "Purchase Detail";
  $ll["header_producto"] = "Product";
  $ll["header_precio"] = "Price";
  $ll["header_cantidad"] = "Quantity";
  $ll["header_subtotal"] = "Subtotal";
  $ll["totales"] = "Total";
  $ll["descuento"] = "Discount";
  $ll["iva"] = "Taxes";
  $ll["no_definido"] = "Undefined";
  $ll["total_general"] = "Total";
  $ll["finalizar_compra"] = "Checkout";
  $ll["quitar"] = "Delete";
}
?>
<div class="varcreative-checkout">
  <div class="varcreative-container">

    <?php if ( $carrito->cantidad == 0) { ?>

      <div class="varcreative-panel checkout_registro panel_activo">
        <div class="varcreative-panel-body">
          <?php echo $ll["carrito_vacio"]; ?>
        </div>
      </div>

    <?php } else { ?>

      <a href="javascript:void(0)" class="varcreative-panel-heading" style="margin-top: 40px"><?php echo $ll["titulo_detalle_compra"]?></a>

      <?php
      // 0 = ESTADO INICIAL
      // 1 = ESTADO PENDIENTE. El admin esta revisando el pedido para su autorizacion. El cliente NO puede pagar
      // 2 = ESTADO APROBADO. El cliente puede terminar el pago
      function render_cart($carrito) { 
        global $articulo_model, $web_model, $empresa, $conx, $ocultar_costo_envio, $ll, $boton_whatsapp; 
        $usar_precio_neto = $articulo_model->usar_precio_neto();
        ?>
        <div class="varcreative-panel checkout_registro varcreative-panel-full panel_activo">
          <div class="varcreative-panel-body varcreative-panel-carrito">
            <div class="information-blocks">
              <div class="table-responsive">
                <table class="cart-table">
                  <tbody>
                    <tr>
                      <th class="column-1 tal"><?php echo $ll["header_producto"]?></th>
                      <th class="column-3 tal"><?php echo $ll["header_cantidad"]?></th>
                      <th class="column-2 tac"><?php echo $ll["header_precio"]?> <?php echo ($empresa->id == 186)?"sin IVA":"" ?></th>
                      <th class="column-4 tac"><?php echo $ll["header_subtotal"]?></th>
                    </tr>
                    <?php 
                    $peso = 0;
                    $total = 0;
                    $items = array();
                    foreach($carrito->items as $item) { 

                      $producto = $articulo_model->get($item->id_articulo); 

                      // Calculamos el peso total del pedido
                      // Peso aforado
                      //$peso_aforado = $producto->ancho * $producto->alto * $producto->profundidad * 250;
                      //if ($peso_aforado > $producto->peso) $producto->peso = $peso_aforado;

                      $peso = $peso + ($producto->peso * $item->cantidad);
                      $total = $total + (($item->cantidad+0)*($item->precio+0));

                      ?>
                      <tr class="producto producto_<?php echo $producto->id ?>">

                        <?php // Datos utilizados por el carrito ?>
                        <input type="hidden" class="prod id" value="<?php echo $producto->id ?>" />
                        <input type="hidden" class="prod_<?php echo $producto->id ?> carrito" value="<?php echo $carrito->numero ?>" />
                        <input type="hidden" class="prod_<?php echo $producto->id ?> cantidad" value="<?php echo $item->cantidad?>" />
                        <input type="hidden" class="prod_<?php echo $producto->id ?> porc_iva" value="<?php echo $producto->porc_iva?>" />
                        <input type="hidden" class="prod_<?php echo $producto->id ?> peso" value="<?php echo $producto->peso ?>" />
                        <input type="hidden" class="prod_<?php echo $producto->id ?> ancho" value="<?php echo $producto->ancho ?>" />
                        <input type="hidden" class="prod_<?php echo $producto->id ?> alto" value="<?php echo $producto->alto ?>" />
                        <input type="hidden" class="prod_<?php echo $producto->id ?> profundidad" value="<?php echo $producto->profundidad ?>" />
                        <input type="hidden" class="prod_<?php echo $producto->id ?> precio" value="<?php echo ($producto->precio_final_dto)?>" />
                        <input type="hidden" class="prod_<?php echo $producto->id ?> nombre" value="<?php echo $producto->nombre ?>" />
                        <input type="hidden" class="prod_<?php echo $producto->id ?> categoria" value="<?php echo $producto->rubro ?>" />
                        <input type="hidden" class="prod_<?php echo $producto->id ?> descripcion" value="<?php echo $item->descripcion ?>" />
                        <input type="hidden" class="prod_<?php echo $producto->id ?> imagen" value="<?php echo ($producto->path)?>" />
                        <input type="hidden" class="prod_<?php echo $producto->id ?> fragil" value="<?php echo ($producto->fragil)?>" />
                        <input type="hidden" class="prod_<?php echo $producto->id ?> stock" value="<?php echo ($producto->stock)?>" />
                        <input type="hidden" class="prod_<?php echo $producto->id ?> id_usuario" value="<?php echo ($producto->id_usuario)?>" />
                        <input type="hidden" class="prod_<?php echo $producto->id ?> id_opcion_1" value="<?php echo $item->id_opcion_1 ?>" />
                        <input type="hidden" class="prod_<?php echo $producto->id ?> id_opcion_2" value="<?php echo $item->id_opcion_2 ?>" />
                        <input type="hidden" class="prod_<?php echo $producto->id ?> id_opcion_3" value="<?php echo $item->id_opcion_3 ?>" />
                        <input type="hidden" class="prod_<?php echo $producto->id ?> id_variante" value="<?php echo $item->id_variante ?>" />

                        <td>
                          <div class="traditional-cart-entry">
                            <div class="content">
                              <div class="cell-view" style="display: table; width: 100%">
                                <div style="display: table-cell; width: 70px">
                                  <a class="image" href="<?php echo mklink($producto->link) ?>">
                                    <?php if (!empty($producto->path)) { ?>
                                      <img src="<?php echo $producto->path ?>" alt="<?php echo ($producto->nombre);?>">
                                    <?php } else if (!empty($empresa->no_imagen)) { ?>
                                      <img src="/sistema/<?php echo $empresa->no_imagen ?>" alt="<?php echo ($producto->nombre);?>">
                                    <?php } else { ?>
                                      <img src="images/no-imagen.png" alt="<?php echo ($producto->nombre);?>">
                                    <?php } ?>
                                  </a>
                                </div>
                                <div style="display: table-cell; vertical-align: top">
                                  <div>
                                    <?php if (!empty($producto->rubro)) { ?>
                                      <a href="javascript:void(0)" class="tag"><?php echo $producto->rubro; ?></a>
                                    <?php } ?>
                                    <a class="traditional-cart-title" href="<?php echo mklink($producto->link) ?>"><?php echo $producto->nombre ?></a>
                                    <?php if (!empty($item->descripcion)) { ?>
                                      <a href="javascript:void(0)" class="tag"><?php echo $item->descripcion; ?></a>
                                    <?php } ?>
                                  </div>
                                  <a onclick="eliminar_item(this,<?php echo $item->id_articulo ?>)" class="remove-button"><?php echo $ll["quitar"]?> <i class="fa fa-times"></i></a>
                                </div>
                              </div>
                            </div>
                          </div>
                        </td>
                        <td class="quantity-col">
                          <select <?php echo($carrito->id_tipo_estado>1)?"disabled":"" ?> onchange="cambiar_cantidad(this)" class="select varcreative-input" data-id="<?php echo $producto->id ?>">
                            <?php $maximo = ($producto->maximo_disponible > 0 && $producto->stock > $producto->maximo_disponible) ? $producto->maximo_disponible : $producto->stock;
                            $maximo = ($maximo > 0) ? $maximo : 50; ?>
                            <?php for($i=1;$i<=$maximo;$i++) { ?>
                            <option <?php echo($item->cantidad==$i)?"selected":"" ?>><?php echo $i ?></option>
                            <?php } ?>
                          </select> 
                        </td>
                        <td class="tac punit"><?php echo format($producto->precio_final_dto,true,$producto->moneda) ?></td>
                        <td><div class="subtotal tac"><?php echo format($item->total,true,$producto->moneda)?></div></td>
                      </tr>
                    <?php } ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div id="calcular_costo_envio_carrito_container" class="varcreative-col5">
            <?php 
            $numero_carrito = $carrito->numero;
            //if (!isset($ocultar_costo_envio)) include_once("templates/comun/calcular_costo_envio.php"); ?>
          </div>
          <div class="varcreative-col2 varcreative-hidden-xs">&nbsp;</div>
          <div class="varcreative-col5">
            <div class="varcreative-panel varcreative-panel-full checkout_registro panel_activo">
              <a href="javascript:void(0)" class="varcreative-panel-heading"><?php echo $ll["totales"] ?></a>
              <div class="varcreative-panel-body">

                <div class="varcreative-table-row">
                  <span class="label_costo_envio varcreative-pull-left"><?php echo $ll["header_subtotal"] ?></span>
                  <span class="numero_costo_envio varcreative-pull-right"><?php echo format($carrito->subtotal) ?></span>
                </div>

                <?php if ($carrito->porc_descuento != 0) { ?>
                  <div class="varcreative-table-row">
                    <span class="label_costo_envio varcreative-pull-left"><?php echo $ll["descuento"] ?> <?php echo $carrito->porc_descuento ?>%</span>
                    <span class="numero_costo_envio varcreative-pull-right"><?php echo format($carrito->descuento) ?></span>
                  </div>
                <?php } ?>

                <?php 
                $metodo_envio = $web_model->get_metodo_envio(); /*
                if ($metodo_envio !== FALSE && !empty($metodo_envio->forma_envio) && !isset($ocultar_costo_envio)) { ?>
                  <div class="costo_envio varcreative-table-row">
                    <span class="label_costo_envio varcreative-pull-left">
                      Costo de env&iacute;o
                    </span>
                    <span class="numero_costo_envio varcreative-pull-right">
                      <?php echo ($carrito->costo_envio < 0) ? "No definido" : format($carrito->costo_envio)." <i style='color:#30a142' class='fa fa-check'></i>" ?>
                    </span>
                  </div>
                <?php }*/ ?>

                <?php if ($usar_precio_neto == 1) { ?>
                  <div class="varcreative-table-row">
                    <span class="varcreative-pull-left"><?php echo $ll["iva"] ?></span>
                    <span class="varcreative-pull-right"><?php echo ($carrito->iva < 0) ? $ll["no_definido"] : format($carrito->iva) ?></span>
                  </div>
                <?php } ?>

                <div class="total_general varcreative-table-row">
                  <span class="label_total_general"><?php echo $ll["total_general"] ?></span>
                  <span class="numero_total_general">
                    <?php echo format($carrito->total + $carrito->costo_envio) ?>
                  </span>
                </div>
              </div>
            </div>

            <div class="varcreative-panel-footer varcreative-finalizar-cont">
              <?php if (isset($boton_whatsapp) && $boton_whatsapp == 1) { ?>
                <a href="javascript:void(0)" onclick="abrir_modal_clienapp()" class="clienapp_boton">
                  <i class="fa fa-whatsapp" aria-hidden="true"></i> ENVIAR WHATSAPP
                </a>
              <?php } else { ?>
                <?php $boton_finalizar_compra = isset($empresa->config["boton_finalizar_compra"]) ? $empresa->config["boton_finalizar_compra"] : $ll["finalizar_compra"]; ?>
                <a href="<?php echo mklink("checkout/".$carrito->numero."/"); ?>" class="varcreative-btn"><?php echo $boton_finalizar_compra ?></a>
              <?php } ?>
            </div>
          </div>
        </div>
      <?php } // End of function 

      // Si los carritos tienen items, renderizamos
      if ($carrito->cantidad > 0) render_cart($carrito); ?>

    <?php } ?>
  </div>
</div>