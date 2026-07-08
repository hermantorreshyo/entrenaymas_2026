<?php
// Esta funcion se utiliza en el detalle del producto, para habilitar el calculo de envio
function render_costo_envio_producto($config = array()) {
  global $empresa, $carrito_model;

  // Parametros
  $producto = isset($config["producto"]) ? $config["producto"] : FALSE;
  $codigo_postal = isset($config["codigo_postal"]) ? $config["codigo_postal"] : "";
  $precio = isset($config["precio"]) ? $config["precio"] : 0;
  $alto = isset($config["alto"]) ? $config["alto"] : 0;
  $ancho = isset($config["ancho"]) ? $config["ancho"] : 0;
  $profundidad = isset($config["profundidad"]) ? $config["profundidad"] : 0;
  $peso = isset($config["peso"]) ? $config["peso"] : 0;
  $cantidad = isset($config["cantidad"]) ? $config["cantidad"] : 1;
  $coordinar_envio = isset($config["coordinar_envio"]) ? $config["coordinar_envio"] : 0;

  // Si tenemos mas cantidad, modificamos algunos parametros
  if ($cantidad > 1) {
    $precio = $precio * $cantidad;
    $ancho = $ancho * $cantidad; // Duplicamos el ancho (como poner una caja al lado de la otra)
    $peso = $peso * $cantidad;
  }

  // Obtenemos el metodo de envio
  $metodo_envio = $carrito_model->get_metodo_envio();
  if ($metodo_envio === FALSE || empty($metodo_envio->forma_envio)) return; 

  $costo_envio = -1; // Envio No Calculado

  // DEBE COORDINARSE EL ENVIO
  if ($producto->coordinar_envio == 1 && $empresa->id != 410) { ?>
    <div class="item-ship mt10">
      <img src="/templates/comun/img/shipping-car.png" alt="Shipping"> <span>Este producto requiere coordinar su envio.</span>
    </div>

  <?php 
  // EL PRODUCTO ESTA MARCADO COMO "ENVIO GRATIS"
  } else if ($producto->no_totalizar_reparto == 1) { ?>
    <div class="item-ship mt10">
      <img width="160" src="/templates/comun/img/envio-gratis.png" alt="Envio Gratis">
    </div>

  <?php 
  // TODAVIA NO SE PASO EL CODIGO POSTAL, TENEMOS QUE PEDIRLO
  } else if (empty($codigo_postal)) { ?>

    <div class="item-ship mt10">
      <div class="costo_envio_calcular">
        <img src="/templates/comun/img/shipping-car.png" alt="shipping" /> 
        <span>CALCULAR</span> 
        <a href="javascript:void(0)" onclick="open_modal_calcular_costo_envio()">COSTO DE ENVIO</a>
      </div>
    </div>

    <?php // ESTE ES EL LIGHTBOX QUE APARECE PARA PEDIR EL COSTO DE ENVIO ?>
    <div id="modal_calcular_costo_envio" class="modal" role="dialog">
      <div class="modal-dialog">
        <div class="modal-content varcreative-checkout">
          <div class="varcreative-panel varcreative-panel-full checkout_registro panel_activo">
            <a href="javascript:void(0)" class="varcreative-panel-heading">Calcular costo de env&iacute;o</a>
            <?php 
            if ($metodo_envio->forma_envio == "MERCADOENVIOS") { ?>
              <form method="get" class="varcreative-panel-body">
                <div class="input-group">
                  <div class="varcreative-form-group">
                    <input name="vc_codigo_postal" type="text" required value="<?php echo isset($codigo_postal) ? $codigo_postal : "" ?>" id="costo_envio_codigo_postal" class="varcreative-input">
                    <span class="varcreative-label">C&oacute;digo postal de entrega</span>
                  </div>
                  <div class="input-group-btn">
                    <button class="varcreative-btn varcreative-btn-2 calcular_btn">Calcular</button>
                  </div>
                </div>
                <div class="oh">
                  <a class="varcreative-link" onclick="javascript:window.open('https://www.andreani.com/buscador-de-codigos-postales-resultado','_blank')">No conozco mi c&oacute;digo postal</a>
                </div>
              </form>
            <?php 
            } else if ($metodo_envio->forma_envio == "REPARTO" && sizeof($metodo_envio->valores_unicos)>0) { ?>
              <form method="get" class="varcreative-panel-body">
                <div class="input-group">
                  <div class="varcreative-form-group">
                    <select name="vc_codigo_postal" class="varcreative-input" required id="costo_envio_codigo_postal">
                      <option value="">Seleccione</option>
                      <?php foreach($metodo_envio->valores_unicos as $v) { ?>
                        <option <?php echo( (isset($codigo_postal) && $codigo_postal == $v) ?"selected":"") ?> value="<?php echo $v ?>"><?php echo $v ?></option>
                      <?php } ?>
                    </select>
                    <span class="varcreative-label varcreative-label-fixed">Zonas de Envío *</span>
                  </div>
                  <div class="input-group-btn">
                    <button class="varcreative-btn varcreative-btn-2 calcular_btn">Calcular</button>
                  </div>
                </div>
              </form>
            <?php } ?>
          </div>
        </div>
      </div>
    </div>
    <script type="text/javascript">
    function open_modal_calcular_costo_envio() { $("#modal_calcular_costo_envio").modal('show'); }
    </script>

  <?php 
  // CALCULAMOS EL COSTO DE ENVIO
  // ============================

  // SI ESTA DEFINIDO UN COSTO DE ENVIO, LLAMAMOS A LAS FUNCIONES DE CARRITO_MODEL
  } else if (!empty($codigo_postal)) {

    if ($metodo_envio->forma_envio == "REPARTO" && sizeof($metodo_envio->valores)>0) {

      // Calculamos el envio de acuerdo al REPARTO PROPIO
      $costo_envio = $carrito_model->do_calcular_reparto(array(
        "codigo_postal"=>$codigo_postal,
        "id_empresa"=>$empresa->id,
        "total"=>$precio,
      ));
    } else if ($metodo_envio->forma_envio == "MERCADOENVIOS") {

      // Calculamos el envio segun MERCADOENVIOS
      $costo_envio = $carrito_model->do_calcular_costo_envio_mercadoenvio(array(
        "codigo_postal"=>$codigo_postal,
        "id_empresa"=>$empresa->id,
        "peso"=>$peso,
        "alto"=>$alto,
        "ancho"=>$ancho,
        "profundidad"=>$profundidad,
        "precio"=>$precio,
        "coordinar_envio"=>$coordinar_envio,
      ));
    } ?>

    <?php 
    // Si la variable $costo_envio es cero, entonces es porque el costo de envio es GRATIS
    if ($costo_envio == 0) { ?>
      <div class="item-ship mt10">
        <img width="160" src="/templates/comun/img/envio-gratis.png" alt="Envio Gratis">
      </div>

    <?php 
    // Tiene un valor especifico, lo mostramos
    } else if ($costo_envio > 0) { ?>
      <div class="item-ship mt10">
        <div class="costo_envio_resultado">
          <img src="/templates/comun/img/shipping-car.png" alt="shipping" /> 
          <span>COSTO DE ENV&Iacute;O: <b class="costo_envio_res"></b> (para <b class="cantidad"><?php echo $cantidad ?></b> unid.)</span> 
        </div>
      </div>
    <?php } ?>

  <?php } ?>

<?php } ?>