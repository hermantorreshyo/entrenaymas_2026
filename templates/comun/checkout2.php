<?php
require '/home/ubuntu/data/vendor/autoload.php';
include_once("carrito_css.php");

// Metodo de envio configurado en el administrador
$metodo_envio = $carrito_model->get_metodo_envio();
$usar_precio_neto = $articulo_model->usar_precio_neto();
$moneda_mp = $carrito_model->get_moneda_mercadopago();

// Si tiene configuraciones por defecto
if (isset($empresa->config["carrito_id_provincia"])) {
  $carrito->id_provincia = $empresa->config["carrito_id_provincia"];
}
if (isset($empresa->config["carrito_id_localidad"])) {
  $carrito->id_localidad = $empresa->config["carrito_id_localidad"];
}
$boton_confirmar_compra = isset($empresa->config["boton_confirmar_compra"]) ? $empresa->config["boton_confirmar_compra"] : "Confirmar compra";

$vc_direccion = ( (isset($carrito->direccion) && !empty($carrito->direccion)) ? $carrito->direccion : ( (isset($_COOKIE["direccion"]) && !empty($_COOKIE["direccion"])) ? $_COOKIE["direccion"] : "" ) );

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
  "titulo_registro"=>"Registrate para comprar",
  "registro_nombre_label"=>"Nombre y Apellido",
  "registro_celular_label"=>"Celular",
  "registro_dni_label"=>"DNI/CUIT",
  "registro_direccion_facturacion_label"=>"Direcci&oacute;n para facturaci&oacute;n",
  "registro_email_label"=>"E-mail",
  "registro_continuar"=>"Registrarme y continuar",
  "titulo_recibir_producto"=>"¿Cómo querés recibir tu producto?",
  "opcion_a_coordinar"=>"Coordinar con el vendedor",
  "opcion_a_domicilio"=>"Enviar a mi domicilio",
  "envio_domicilio"=>"Envío a domicilio",
  "opcion_retiro_sucursal"=>"Retiro en sucursal del vendedor",
  "opcion_seleccione_sucursal"=>"SELECCIONE LA SUCURSAL POR LA CUAL DESEA PASAR A RETIRAR",
  "continuar"=>"Continuar",
  "titulo_donde_recibirlo"=>"¿Dónde querés recibirlo?",
  "direccion"=>"Dirección",
  "ciudad"=>"Ciudad",
  "codigo_postal"=>"Código Postal",
  "zonas_envio"=>"Zonas de Envío",
  "provincia"=>"Provincia",
  "modificar"=>"Modificar",
  "pago_sucursal"=>"Pago en Sucursal",
  "transferencia"=>"Transferencia o dep&oacute;sito bancario",
  "contrarrembolso"=>"Efectivo contrarrembolso",
  "pago_convenir"=>"Pago a convenir",
  "titulo_detalle_envio"=>"Detalle del envío",
  "convenir_envio"=>"Envio a Convenir",
  "titulo_detalle_pago"=>"Detalle del pago",
  "titulo_pagar"=>"¿Cómo querés pagar?",
  "cambiar_forma_envio"=>"Cambiar forma de envio",
  "costo_envio"=>"Costo de Envio",
  "error_compra_minima"=>"ATENCION: compra m&iacute;nima de $",
  "cantidad"=>"Cantidad",
  "subtotal"=>"Subtotal",
  "cupon_descuento"=>"Cup&oacute;n de descuento",
  "descuento"=>"Descuento",
  "iva"=>"IVA",
  "gratis"=>"Gratis",
  "total"=>"Total",
);
if ($checkout_language == "en") {
  $ll["carrito_vacio"] = "Your cart is empty.";
  $ll["titulo_registro"] = "Register";
  $ll["registro_nombre_label"] = "Full Name";
  $ll["registro_celular_label"] = "Phone";
  $ll["registro_dni_label"] = "Document";
  $ll["registro_direccion_facturacion_label"] = "Billing Address";
  $ll["registro_email_label"] = "E-mail";
  $ll["registro_continuar"] = "Register and continue";
  $ll["titulo_recibir_producto"] = "How do you want to receive your products?";
  $ll["opcion_a_coordinar"] = "Coordinate with seller";
  $ll["opcion_a_domicilio"] = "Send to my address";
  $ll["envio_domicilio"] = "Home delivery";
  $ll["opcion_retiro_sucursal"] = "Pick up";
  $ll["opcion_seleccione_sucursal"] = "SELECT THE BRANCH FOR WHICH YOU WISH TO PASS WITHDRAWAL";
  $ll["continuar"] = "Continue";
  $ll["titulo_donde_recibirlo"] = "Where do you want to receive it?";
  $ll["direccion"] = "Address";
  $ll["ciudad"] = "City";
  $ll["codigo_postal"] = "Postal Code";
  $ll["zonas_envio"] = "Shipping Zones";
  $ll["provincia"] = "State";
  $ll["modificar"] = "Modify";
  $ll["pago_sucursal"] = "Payment in commerce";
  $ll["transferencia"] = "Bank Transfer";
  $ll["contrarrembolso"] = "Cash on delivery";
  $ll["pago_convenir"] = "Payment to be agreed";
  $ll["titulo_detalle_envio"] = "Shipping Details";
  $ll["convenir_envio"] = "Shipping to be agreed";
  $ll["titulo_detalle_pago"] = "Payment Details";
  $ll["titulo_pagar"] = "How do you want to pay?";
  $ll["cambiar_forma_envio"] = "Change shipping method";
  $ll["costo_envio"] = "Shipping Cost";
  $ll["error_compra_minima"] = "ATTENTION: minimum purchase of $";
  $ll["cantidad"] = "Quantity";
  $ll["subtotal"] = "Subtotal";
  $ll["cupon_descuento"] = "Discount Coupon";
  $ll["descuento"] = "Discount";
  $ll["iva"] = "Taxes";
  $ll["gratis"] = "Free";
  $ll["total"] = "Total";
}

// EXCEPCIONES DE PALABRAS
if ($empresa->id == 1041) {
  $ll["titulo_donde_recibirlo"] = "¿Dónde quieres recibirlo?";
  $ll["titulo_recibir_producto"] = "¿Cómo quieres recibir tu producto?";
  $ll["titulo_pagar"] = "¿Cómo quieres pagar?";
}

?>
<div class="varcreative-checkout">
  <div class="varcreative-container">

    <?php if (sizeof($carrito->items)==0) { ?>
      <div class="varcreative-panel" style="margin: 150px auto">
        <div class="varcreative-panel-body">
          <?php echo $ll["carrito_vacio"] ?>
        </div>
      </div>        
    <?php } else { ?>

      <div class="varcreative-col8">

        <?php 
        // PASO 0: REGISTRO
        if ($carrito->numero_paso == 0) { ?>
          <div class="varcreative-panel checkout_registro panel_activo">
            <form onsubmit="return varcreative_enviar_registro()">
              <a href="javascript:void(0)" class="varcreative-panel-heading"><?php echo $ll["titulo_registro"] ?></a>
              <div class="varcreative-panel-body">

                <div class="row">
                  <div class="varcreative-col6">
                    <div class="varcreative-form-group">
                      <input type="text" class="varcreative-input" required id="registro_nombre" value="<?php echo(isset($carrito->cliente)?$carrito->cliente:"") ?>" name="nombre" />
                      <span class="varcreative-label"><?php echo $ll["registro_nombre_label"] ?></span>
                    </div>
                  </div>

                  <?php if (isset($empresa->tienda_registro_telefono) && $empresa->tienda_registro_telefono == 1) { ?>
                    <div class="varcreative-col6">
                      <div class="varcreative-form-group">
                        <input type="number" class="varcreative-input" onfocus="add_placeholder(this)" onblur="remove_placeholder(this)" data-placeholder="Sin 0 ni 15" required id="registro_telefono" value="<?php echo(isset($carrito->telefono)?$carrito->telefono:"") ?>" name="telefono" />
                        <span class="varcreative-label"><?php echo $ll["registro_celular_label"] ?></span>
                      </div>
                    </div>
                  <?php } ?>

                  <?php if (isset($empresa->tienda_registro_documento) && $empresa->tienda_registro_documento == 1) { ?>
                    <div class="varcreative-col6">
                      <div class="varcreative-form-group">
                        <input type="number" id="registro_cuit" required class="varcreative-input" name="cuit" />
                        <span class="varcreative-label"><?php echo $ll["registro_dni_label"] ?></span>
                      </div>
                    </div>
                  <?php } ?>

                  <?php if (isset($empresa->tienda_registro_direccion) && $empresa->tienda_registro_direccion == 1) { ?>
                    <div class="varcreative-col6">
                      <div class="varcreative-form-group">
                        <input type="text" class="varcreative-input" onfocus="add_placeholder(this)" onblur="remove_placeholder(this)" data-placeholder="<?php echo $ll["registro_direccion_facturacion_label"] ?>" required id="registro_direccion" value="<?php echo $vc_direccion ?>" name="direccion" />
                        <span class="varcreative-label"><?php echo $ll["registro_direccion_facturacion_label"] ?></span>
                      </div>
                    </div>
                  <?php } ?>

                </div>

                <div class="varcreative-form-group">
                  <input type="text" id="registro_email" required class="varcreative-input" value="<?php echo(isset($email)?$email:"") ?>" name="email" />
                  <span class="varcreative-label"><?php echo $ll["registro_email_label"] ?></span>
                </div>
                <?php /*
                <div class="varcreative-form-group">
                  <input type="password" id="registro_ps" required class="varcreative-input" />
                  <span class="varcreative-label">Clave</span>
                </div>
                */ ?>
              </div>
              <div class="varcreative-panel-footer">
                <input id="registro_submit" type="submit" class="varcreative-btn" value="<?php echo $ll["registro_continuar"] ?>" />
              </div>
            </form>
          </div>

        <?php 
        // PASO 1: COMO RECIBIR EL PRODUCTO
        } else if ($carrito->numero_paso == 1 && $metodo_envio !== FALSE) { ?>

          <?php 
          // SI ES CLASSVAR ESTE PASO LO SALTAMOS
          if ($empresa->id_proyecto == 19) {
            // Pasamos directamente al proximo paso
            $carrito->numero_paso = 4;
            $carrito_model->guardar($carrito);
            ?>
            <script type="text/javascript">
              window.location.reload();
            </script>

          <?php 
          // NO TIENE SETEADO EL METODO DE ENVIO, ENTONCES PASAMOS AL PROXIMO PASO
          } else if (
            empty($metodo_envio->forma_envio) // No esta definido un metodo de envio (MERCADOENVIOS O REPARTO)
            && $metodo_envio->convenir_envio == 0 // Ni tampoco se va a convenir
          ) { 
            // Pasamos directamente al proximo paso
            $carrito->numero_paso = 4;
            $carrito_model->guardar($carrito);
            ?>
            <script type="text/javascript">
              window.location.reload();
            </script>
          <?php } ?>

          <div class="varcreative-panel checkout_registro panel_activo">
            
            <a href="javascript:void(0)" class="varcreative-panel-heading"><?php echo $ll["titulo_recibir_producto"] ?></a>  

            <?php 
            // Algun producto esta marcado como coordinar envio, no se puede hacer otra cosa
            if (isset($carrito->coordinar_envio) && $carrito->coordinar_envio == 1) { ?>

              <div class="varcreative-option-btn varcreative-option-coordinar-vendedor" onclick="varcreative_forma_envio('a_convenir')">
                <?php echo $ll["opcion_a_coordinar"] ?>
              </div>
            
            <?php } else { ?>

              <?php 
              // ENVIO A DOMICILIO
              if (
                (!empty($metodo_envio->forma_envio) // Esta definida una forma de envio
                && $carrito->total >= $empresa->tienda_envio_desde) // El monto total supera al envio minimo
                || $empresa->id == 120 // VULCA
              ){ ?>
                <div class="varcreative-option-btn varcreative-option-envio-domicilio" onclick="varcreative_forma_envio('envio_domicilio')">
                  <?php echo $ll["opcion_a_domicilio"] ?>
                </div>
              <?php } ?>

              <?php 
              // RETIRO EN SUCURSAL
              if ($metodo_envio->retiro_sucursal == 1) { ?>
                <div class="varcreative-option-btn varcreative-option-retiro-sucursal" onclick="varcreative_forma_envio('retiro_sucursal')">
                  <?php echo $ll["opcion_retiro_sucursal"] ?>
                </div>
                <?php 
                // SI LA EMPRESA TIENE CONFIGURADAS SUCURSALES PARA EL RETIRO
                $sucursales = $web_model->get_sucursales_para_retiro();
                if (sizeof($sucursales)>0) { ?>
                  <div id="varcreative-option-retiro-sucursal-sucursales" style="display: none" class="varcreative-option-subpanel">
                    <div class="varcreative-option-subpanel-title">
                      <?php echo $ll["opcion_seleccione_sucursal"] ?>
                    </div>
                    <?php 
                    // ANACLETO
                    if ($empresa->id == 42) { ?>
                      <div class="row">
                        <?php foreach($sucursales as $suc) { ?>
                          <div class="varcreative-col6">
                            <div class="radio">
                              <label class="i-checks">
                                <input type="radio" <?php echo (sizeof($sucursales) == 1)?"checked":"" ?> id="varcreative-sucursal-<?php echo $suc->id ?>" value="<?php echo $suc->nombre ?>" name="varcreative-sucursal" />
                                <i></i>
                                <div class="varcreative-option-sucursal-texto">
                                  <b><?php echo $suc->nombre ?></b>
                                  <?php if (!empty($suc->direccion)) { ?>
                                    <br/><?php echo $suc->direccion ?>
                                  <?php } ?>
                                </div>
                              </label>
                            </div>                                
                          </div>
                        <?php } ?>
                      </div>
                      <div class="varcreative-advertencia">
                        Deber&aacute; esperar recibir <span class="naranja">UN SEGUNDO CORREO</span> donde se le informar&aacute;
                        <span class="naranja">CUANDO</span> podr&aacute; retirar su compra.
                      </div>
                    <?php 
                    // SI NO ES ANACLETO, MOSTRAMOS LAS SUCURSALES HABILITADAS PARA EL RETIRO:
                    } else { ?>
                      <?php foreach($sucursales as $suc) { ?>
                        <div class="ml20 mb20">
                          <div class="radio">
                            <label class="i-checks pl0">
                              <input type="radio" <?php echo (sizeof($sucursales) == 1)?"checked":"" ?> id="varcreative-sucursal-<?php echo $suc->id ?>" value="<?php echo $suc->nombre ?>" name="varcreative-sucursal" />
                              <i></i>
                              <div class="varcreative-option-sucursal-texto">
                                <b><?php echo $suc->nombre ?></b>
                                <?php if (!empty($suc->direccion)) { ?>
                                  <br/><?php echo $suc->direccion ?>
                                <?php } ?>
                              </div>
                            </label>
                          </div>                                
                        </div>
                      <?php } ?>                    
                    <?php } ?>
                    <div class="varcreative-sucursales-siguiente">
                      <a onclick="varcreative_forma_envio_siguiente('retiro_sucursal')" class="btn cp <?php echo ($empresa->id != 42)?"varcreative-btn":"" ?>"><?php echo $ll["continuar"] ?></a>
                    </div>
                  </div>
                <?php } ?>
              <?php } ?>

              <?php 
              // COORDINAR ENVIO CON EL VENDEDOR
              if ($metodo_envio->convenir_envio == 1) { ?>
                <div class="varcreative-option-btn varcreative-option-coordinar-vendedor" onclick="varcreative_forma_envio('a_convenir')">
                  <?php echo ($empresa->id == 133) ? "Envio a domicilio" : $ll["opcion_a_coordinar"] ?>
                </div>
              <?php } ?>

            <?php } ?>

          </div>

        <?php 
        // PASO 2: ELEGIR DOMICILIO DE ENTREGA
        } else if ($carrito->numero_paso == 2) { ?>

          <div class="varcreative-panel panel_activo">
            <a href="javascript:void(0)" class="varcreative-panel-heading"><?php echo $ll["titulo_donde_recibirlo"] ?></a>  
            <form id="forma_envio_form" onsubmit="return varcreative_enviar_direccion()">
              <div class="varcreative-panel-body">

                <?php if (($metodo_envio !== FALSE && (!empty($metodo_envio->forma_envio)) ) || $empresa->id == 120) { ?>

                  <input type="hidden" id="registro_empresa_envio" value="<?php echo $metodo_envio->forma_envio ?>" />

                  <div id="datos_direccion">
                    <?php 
                    // SI HAY QUE LLEVARLO DE ALGUNA FORMA
                    if ($metodo_envio->forma_envio == "MERCADOENVIOS") { ?>

                      <?php 
                      // SI EL CODIGO POSTAL FUE DEFINIDO DESDE EL EXTERIOR
                      if (isset($codigo_postal_zona_envio) && !empty($codigo_postal_zona_envio)) { ?>
                        <input type="hidden" id="registro_codigo_postal" value="<?php echo $codigo_postal_zona_envio ?>" name="codigo_postal" />

                        <div class="varcreative-form-group">
                          <input type="text" class="varcreative-input" required id="registro_direccion" value="<?php echo $vc_direccion ?>" name="direccion" />
                          <span class="varcreative-label"><?php echo $ll["direccion"] ?> *</span>
                        </div>

                      <?php 
                      // PEDIMOS EL CODIGO POSTAL AL USUARIO
                      } else { ?>

                        <div class="row">
                          <div class="varcreative-col6">
                            <div class="varcreative-form-group">
                              <input type="text" class="varcreative-input" required id="registro_codigo_postal" value="<?php echo(isset($carrito->codigo_postal)?$carrito->codigo_postal:"") ?>" name="codigo_postal" />
                              <span class="varcreative-label"><?php echo $ll["codigo_postal"] ?> *</span>
                            </div>
                          </div>
                          <?php if (isset($empresa->tienda_registro_ciudad) && $empresa->tienda_registro_ciudad == 1) { ?>
                            <div class="varcreative-col6">
                              <div class="varcreative-form-group">
                                <input type="text" class="varcreative-input" required id="registro_localidad_texto" value="<?php echo(isset($carrito->localidad)?$carrito->localidad:"") ?>" name="localidad" />
                                <span class="varcreative-label"><?php echo $ll["ciudad"] ?> *</span>
                              </div>
                            </div>
                          <?php } ?>
                        </div>
                        <div class="varcreative-form-group">
                          <input type="text" class="varcreative-input" required id="registro_direccion" value="<?php echo $vc_direccion ?>" name="direccion" />
                          <span class="varcreative-label"><?php echo $ll["direccion"] ?> *</span>
                        </div>

                      <?php } ?>

                    <?php } else if ($metodo_envio->forma_envio == "REPARTO") { ?>

                        <?php if (sizeof($metodo_envio->valores_unicos)>0) { ?>
                          <div class="varcreative-form-group">
                            <select name="codigo_postal" class="varcreative-input" required id="registro_codigo_postal">
                              <option value="">Seleccione</option>
                              <?php foreach($metodo_envio->valores_unicos as $v) { ?>
                                <option <?php echo( (isset($carrito->codigo_postal) && $carrito->codigo_postal == $v) ?"selected":"") ?> value="<?php echo $v ?>"><?php echo $v ?></option>
                              <?php } ?>
                            </select>
                            <span class="varcreative-label varcreative-label-fixed"><?php echo $ll["zonas_envio"] ?> *</span>
                          </div>
                        <?php } ?>

                        <?php if (isset($empresa->tienda_registro_ciudad) && $empresa->tienda_registro_ciudad == 1) { ?>
                          <div class="varcreative-form-group">
                            <input type="text" class="varcreative-input" required id="registro_localidad_texto" value="<?php echo(isset($carrito->localidad)?$carrito->localidad:"") ?>" name="localidad" />
                            <span class="varcreative-label"><?php echo $ll["ciudad"] ?> *</span>
                          </div>
                        <?php } ?>

                        <div class="varcreative-form-group">
                          <input type="text" class="varcreative-input" required id="registro_direccion" value="<?php echo $vc_direccion ?>" name="direccion" />
                          <span class="varcreative-label"><?php echo $ll["direccion"] ?> *</span>
                        </div>                      

                    <?php 
                    // SOLAMENTE SI ES VULCA: PEDIMOS LAS PROVINCIAS Y LAS CIUDADES
                    } else if ($empresa->id == 120) { ?>
                      <div class="row">
                        <div class="varcreative-col6">
                          <div class="varcreative-form-group">
                            <select <?php echo isset($empresa->config["carrito_id_provincia_disabled"]) ? "disabled":"" ?> name="id_provincia" class="varcreative-input provincia_select" id="registro_provincia" onchange="cambiar_provincia(this.value)">
                              <option value="0"><?php echo $ll["provincia"] ?></option>
                              <?php 
                              $sql_provincia = "SELECT * FROM com_provincias WHERE 1=1 ";
                              if (isset($empresa->config["carrito_id_pais"])) $sql_provincia.= "AND id_pais = '".$empresa->config["carrito_id_pais"]."' ";
                              $sql_provincia.= "ORDER BY nombre ASC ";
                              $q = mysqli_query($conx,$sql_provincia);
                              while(($r=mysqli_fetch_object($q))!==NULL) { ?>
                                <option <?php echo ($carrito->id_provincia == $r->id)?"selected":"" ?> value="<?php echo $r->id; ?>"><?php echo $r->nombre ?></option>
                              <?php } ?>
                            </select>
                          </div>
                        </div>
                        <div class="varcreative-col6">
                          <div class="varcreative-form-group">
                            <select <?php echo isset($empresa->config["carrito_id_localidad_disabled"]) ? "disabled":"" ?> data-selected="<?php echo $carrito->id_localidad ?>" class="varcreative-input localidad_select" name="id_localidad" id="registro_localidad">
                              <option value="0"><?php echo $ll["ciudad"] ?></option>
                            </select>
                          </div>
                        </div>
                      </div>
                      <div class="varcreative-form-group">
                        <input type="text" class="varcreative-input" required id="registro_direccion" value="<?php echo $vc_direccion ?>" name="direccion" />
                        <span class="varcreative-label"><?php echo $ll["direccion"] ?> *</span>
                      </div>

                    <?php } ?>

                  </div>
                <?php } ?>
              </div>
              <div class="varcreative-panel-footer">
                <a class="varcreative-option-edit" style="float: left; margin: 10px 0px;" href="javascript:void(0)" onclick="varcreative_volver_atras(1)"><?php echo $ll["cambiar_forma_envio"] ?></a>
                <input id="forma_envio_submit" type="submit" class="varcreative-btn" value="<?php echo $ll["continuar"] ?>" />
              </div>
            </form>
          </div>

        <?php 
        // PASO 4: FORMAS DE PAGO
        } else if ($carrito->numero_paso == 4) { ?>

          <div class="varcreative-panel panel_activo">
            
            <a href="javascript:void(0)" class="varcreative-panel-heading"><?php echo $ll["titulo_pagar"] ?></a>  

              <?php 
              // PAGO EN SUCURSAL
              if ($carrito->forma_envio == "retiro_sucursal" && $carrito_model->get_pago_sucursal()) { ?>
                <div class="varcreative-option-btn varcreative-option-pago-sucursal" onclick="varcreative_forma_pago('pago_sucursal')">
                  <?php echo $ll["pago_sucursal"] ?>
                </div>
              <?php } ?>

              <?php 
              // PAGO CON MERCADOPAGO
              if (isset($mp) && $mp !== FALSE) { ?>
                <div class="varcreative-option-btn varcreative-option-pago-mercadopago" onclick="varcreative_forma_pago('mercadopago')">
                  MercadoPago
                </div>
              <?php } ?>

              <?php 
              // PAGO CON TRANSFERENCIA BANCARIA
              if ($carrito_model->get_transferencia_bancaria()) { ?>
                <div class="varcreative-option-btn varcreative-option-pago-transferencia-bancaria" onclick="varcreative_forma_pago('transferencia')">
                  <?php echo $ll["transferencia"] ?>
                </div>
              <?php } ?>

              <?php 
              // PAGO CONTRARREMBOLSO
              if ($carrito_model->get_contrarrembolso($carrito) && $carrito->forma_envio == "envio_domicilio") { ?>
                <div class="varcreative-option-btn varcreative-option-pago-sucursal" onclick="varcreative_forma_pago('contrarrembolso')">
                  <?php echo $ll["contrarrembolso"] ?>
                </div>
              <?php } ?>

              <?php 
              // PAGO A CONVENIR
              if ($carrito_model->get_convenir_pago()) { ?>
                <div class="varcreative-option-btn varcreative-option-pago-convenir" onclick="varcreative_forma_pago('a_convenir')">
                  <?php echo $ll["pago_convenir"] ?>
                </div>
              <?php } ?>
            </div>

          <?php 
          // ULTIMO PASO: MOSTRAMOS EL RESUMEN DE TODO LO ELEGIDO
          } else if ($carrito->numero_paso == 99) {  ?>
            
            <?php if (!(empty($metodo_envio->forma_envio) && $metodo_envio->convenir_envio == 0) && $empresa->id_proyecto != 19) { ?>
              <div class="varcreative-panel panel_activo">
                <a href="javascript:void(0)" class="varcreative-panel-heading"><?php echo $ll["titulo_detalle_envio"] ?></a>  
                <div class="varcreative-option-btn">
                  <div>
                    <?php echo ($carrito->forma_envio == "envio_domicilio") ? $ll["envio_domicilio"] : ""; ?>
                    <?php echo ($carrito->forma_envio == "retiro_sucursal") ? (!empty($carrito->sucursal) ? "Retiro en ".$carrito->sucursal : $ll["opcion_retiro_sucursal"]) : ""; ?>
                    <?php echo ($carrito->forma_envio == "a_convenir") ? $ll["convenir_envio"] : ""; ?>
                    <a class="varcreative-option-edit" href="javascript:void(0)" onclick="varcreative_volver_atras(1)"><?php echo $ll["modificar"] ?></a>
                  </div>
                  <?php if ($carrito->forma_envio == "envio_domicilio") { ?>
                    <div style="font-weight: bold; margin-top: 5px;">
                      <?php echo $carrito->direccion ?>
                      <?php echo (!empty($carrito->codigo_postal)) ? " - $carrito->codigo_postal":"" ?>
                      <?php echo (!empty($carrito->localidad)) ? " - $carrito->localidad":"" ?>
                    </div>
                  <?php } ?>
                </div>
              </div>
            <?php } ?>

            <div class="varcreative-panel panel_activo">
              <a href="javascript:void(0)" class="varcreative-panel-heading"><?php echo $ll["titulo_detalle_pago"] ?></a>  
              <div class="varcreative-option-btn">
                <?php echo ($carrito->forma_pago == "mercadopago") ? "MercadoPago" : ""; ?>
                <?php echo ($carrito->forma_pago == "pago_sucursal") ? $ll["pago_sucursal"] : ""; ?>
                <?php echo ($carrito->forma_pago == "transferencia") ? $ll["transferencia"] : ""; ?>
                <?php echo ($carrito->forma_pago == "contrarrembolso") ? $ll["contrarrembolso"] : ""; ?>
                <?php echo ($carrito->forma_pago == "a_convenir") ? $ll["pago_convenir"] : ""; ?>
                <a class="varcreative-option-edit" href="javascript:void(0)" onclick="varcreative_volver_atras(4)"><?php echo $ll["modificar"] ?></a>
              </div>
            </div>


            <?php 
            // SI ESTAMOS EN EL ULTIMO PASO
            $items = array();
            if ($carrito->numero_paso == 99) { 
              $peso_total = 0;
              foreach($carrito->items as $item) { 
                $producto = $articulo_model->get($item->id_articulo); 
                $peso_total += (float) $producto->peso;

                if ($carrito->porc_descuento > 0) {
                  $precio_unitario = $item->precio * ((100-$carrito->porc_descuento)/100);
                } else {
                  $precio_unitario = $item->precio + 0;
                }

                $items[] = array(
                  "id"=>$item->id_articulo,
                  "title"=>$producto->nombre,
                  "currency_id"=>$moneda_mp,
                  "quantity"=>$item->cantidad + 0,
                  "unit_price"=>$precio_unitario,
                );
              }
              if ($carrito->costo_envio > 0 && (!empty($metodo_envio->forma_envio)) && ($metodo_envio->forma_envio != "MERCADOENVIOS" || (isset($carrito->excepcion_envio) && $carrito->excepcion_envio == 1)) ) {
                // Si tiene costo de envio, agregamos otro item
                $items[] = array(
                  "id"=>0,
                  "title"=>$ll["costo_envio"],
                  "currency_id"=>$moneda_mp,
                  "quantity"=>1,
                  "unit_price"=>$carrito->costo_envio + 0,
                );
              } ?>
              <div class="varcreative-panel panel_activo" style="margin-bottom: 40px; overflow: hidden;">
                <div id="finalizar_pago_container" class="tac mt10">

                  <?php if ($carrito->total > $empresa->tienda_compra_minima) { ?>
                  
                    <?php if ($carrito->forma_pago == "mercadopago") {                     

                      // Si es VULCATIRES
                      if ($empresa->id == 120 && isset($cuenta_vulcatires)) {
                        if ($cuenta_vulcatires == "SAN_MARINO") {
                          $notification_url = mklink("ipn-mp-vulca-1/");
                        } else if ($cuenta_vulcatires == "PATAGONIA") {
                          $notification_url = mklink("ipn-mp-vulca-2/");
                        } else if ($cuenta_vulcatires == "MAR_DEL_PLATA") {
                          $notification_url = mklink("ipn-mp-vulca-3/");
                        } else {
                          $notification_url = mklink("ipn-mp/");  
                        }
                        
                      } else {
                        $notification_url = mklink("ipn-mp/");
                      }
                      ?>
                      <div id="finalizar_mp">
                        <?php

                        // Agrega credenciales
                        MercadoPago\SDK::setAccessToken('APP_USR-2693978642246786-040214-0412d431b680c04887edfdf9f18b3334-200788880');

                        // Crea un objeto de preferencia
                        $preference = new MercadoPago\Preference();

                        // Crea un ítem en la preferencia
                        $items2 = array();
                        foreach($items as $it) {
                          $item = new MercadoPago\Item();
                          $item->title = $it["title"];
                          $item->quantity = $it["quantity"];
                          $item->unit_price = $it["unit_price"];
                          $items2[] = $item;
                        }
                        $preference->items = $items2;
                        $preference->back_urls = array(
                          "success" => mklink("compra-ok/"),
                          "failure" => mklink("compra-fail/"),
                          "pending" => mklink("compra-pending/"),
                        );
                        $payer = new MercadoPago\Payer();
                        $payer->name = $carrito->cliente;
                        $payer->email = $carrito->email;
                        if (!empty($carrito->telefono)) {
                          $payer->phone = array(
                            "area_code" => "",
                            "number" => $carrito->telefono,
                          );
                        }
                        $preference->notification_url = $notification_url;
                        $preference->auto_return = "all";
                        $preference->external_reference = $carrito->id;

                        if (!isset($carrito->excepcion_envio)) $carrito->excepcion_envio = 0;

                        // Si ademas le tenemos que incluir MERCADOENVIO
                        /*
                        if ($metodo_envio->forma_envio == "MERCADOENVIOS" 
                          && $carrito->forma_envio != "retiro_sucursal" 
                          && $carrito->forma_envio != "a_convenir" 
                          && $carrito->excepcion_envio == 0) {

                          $shipments = new MercadoPago\Shipments();
                          $shipments->mode = "me2";
                          if ($carrito->ancho_total > 0 && $carrito->alto_total > 0 && $carrito->profundidad_total > 0 && $peso_total > 0) {
                            $dimensiones = $carrito_model->calcular_dimensiones(array(
                              "ancho"=>round($carrito->ancho_total * 100,0),
                              "alto"=>round($carrito->alto_total * 100,0),
                              "profundidad"=>round($carrito->profundidad_total * 100,0),
                            ));
                            $shipments->dimensions = $dimensiones.",".round($peso_total * 1000,0);
                            if (!empty($carrito->codigo_postal)) {
                              $shipments->receiver_address=array(
                                "zip_code" => $carrito->codigo_postal,
                                "street_name" => $carrito->direccion,
                              );
                            }
                          }
                          $preference->shipments = $shipments;
                        }
                        */

                        try {  $preference->save(); ?>
                          <form action="<?php echo mklink("checkout/") ?>" method="POST">
                            <script
                             src="https://www.mercadopago.com.ar/integrations/v1/web-payment-checkout.js"
                             data-preference-id="<?php echo $preference->id; ?>">
                            </script>
                          </form>
                        <?php } catch(Exception $e) { 
                          var_dump($e);
                          file_put_contents("/home/ubuntu/data/log_preference_data.txt", $e->getMessage()."\n", FILE_APPEND);
                          if ($e->getMessage() == "collector doesn't have me2 active") {
                            // El vendedor no tiene MercadoEnvio activo echo ?>
                            <div class="varcreative-error-message">No se puede finalizar la compra ya que falta configurar MercadoEnvios en la cuenta del vendedor. <br/>Disculpe las molestias ocasionadas.</div><?php
                            send_error(array(
                              "message"=>"$empresa->nombre no tiene configurado MercadoEnvios.",
                            ));
                          } else if ($e->getMessage() == "Invalid dimension values.") { ?>
                            <div class="varcreative-error-message">El tamaño del paquete es inválido para MercadoEnvios.<br/>Disculpe las molestias ocasionadas.</div><?php
                            send_error(array(
                              "message"=>"$empresa->nombre invalid dimension values: $dimensiones",
                            ));
                          } else {
                            send_error(array(
                              "message"=>print_r($e,TRUE),
                            ));
                          }
                        }?>
                      </div>
                    <?php } else if ($carrito->forma_pago == "pago_sucursal") { ?>
                      <div id="finalizar_pago_sucursal">
                        <button onclick="finalizar_pago_sucursal()" class="varcreative-btn finalizar"><?php echo $boton_confirmar_compra ?></button>
                      </div>
                    <?php } else if ($carrito->forma_pago == "transferencia") { ?>
                      <div id="finalizar_pago_transferencia">
                        <button onclick="finalizar_pago_transferencia()" class="varcreative-btn finalizar"><?php echo $boton_confirmar_compra ?></button>
                      </div>
                    <?php } else if ($carrito->forma_pago == "contrarrembolso") { ?>
                      <div id="finalizar_pago_contrarrembolso">
                        <button onclick="finalizar_pago_contrarrembolso()" class="varcreative-btn finalizar"><?php echo $boton_confirmar_compra ?></button>
                      </div>
                    <?php } else if ($carrito->forma_pago == "a_convenir") { ?>
                      <div id="finalizar_pago_a_convenir">
                        <button onclick="finalizar_pago_a_convenir()" class="varcreative-btn finalizar"><?php echo $boton_confirmar_compra ?></button>
                      </div>
                    <?php } ?>
                  
                  <?php } else { 
                    // NO SE SUPERA EL LIMITE DE COMPRA MINIMA ?>
                    <div class="varcreative-warning">
                      <i class="fa fa-warning"></i> <?php echo $ll["error_compra_minima"]." ".$empresa->tienda_compra_minima ?>
                    </div>
                  <?php } ?>

                </div>
              </div>
            <?php } ?>

          <?php } ?>

          <?php 
          // USAMOS ESTO PARA MOSTRAR LOS ERRORES
          if (isset($carrito->mensaje_error) && !empty($carrito->mensaje_error)) { ?>
            <div class="varcreative-panel panel_activo">
              <div class="varcreative-warning">
                <?php echo $carrito->mensaje_error ?>
              </div>
            </div>
          <?php } ?>

      </div>
      <div class="varcreative-col4">
        <div class="varcreative-col-resumen <?php echo ($carrito->numero_paso == 99)?"varcreative-col-resumen-active":"" ?>">
          <div class="varcreative-col-padding">
            <div class="varcreative-tabla-resumen varcreative-table-row">
              <?php 
              $peso = 0; $total = 0; $items = array();
              foreach($carrito->items as $item) { 
                $producto = $articulo_model->get($item->id_articulo,array(
                  "id_variante"=>$item->id_variante, // En caso de que sea una variante de un producto, el stock puede ser distinto
                )); ?>
                <div class="producto producto_<?php echo $producto->id ?>">
                  <?php // Datos utilizados por el carrito ?>
                  <input type="hidden" class="prod id" value="<?php echo $producto->id ?>" />
                  <input type="hidden" class="prod_<?php echo $producto->id ?> carrito" value="<?php echo $carrito->numero ?>" />
                  <input type="hidden" class="prod_<?php echo $producto->id ?> cantidad" value="<?php echo $item->cantidad?>" />
                  <input type="hidden" class="prod_<?php echo $producto->id ?> peso" value="<?php echo $producto->peso?>" />
                  <input type="hidden" class="prod_<?php echo $producto->id ?> precio" value="<?php echo ($producto->precio_final_dto)?>" />
                  <input type="hidden" class="prod_<?php echo $producto->id ?> nombre" value="<?php echo $producto->nombre ?>" />
                  <input type="hidden" class="prod_<?php echo $producto->id ?> categoria" value="<?php echo $producto->rubro ?>" />
                  <input type="hidden" class="prod_<?php echo $producto->id ?> imagen" value="<?php echo ($producto->path)?>" />
                  <input type="hidden" class="prod_<?php echo $producto->id ?> fragil" value="<?php echo ($producto->fragil)?>" />
                  <input type="hidden" class="prod_<?php echo $producto->id ?> stock" value="<?php echo ($producto->stock)?>" />
                  <input type="hidden" class="prod_<?php echo $producto->id ?> id_opcion_1" value="<?php echo $item->id_opcion_1 ?>" />
                  <input type="hidden" class="prod_<?php echo $producto->id ?> id_opcion_2" value="<?php echo $item->id_opcion_2 ?>" />
                  <input type="hidden" class="prod_<?php echo $producto->id ?> id_opcion_3" value="<?php echo $item->id_opcion_3 ?>" />
                  <input type="hidden" class="prod_<?php echo $producto->id ?> id_variante" value="<?php echo $item->id_variante ?>" />
                  <input type="hidden" class="prod_<?php echo $producto->id ?> descripcion" value="<?php echo $item->descripcion ?>" />

                  <div class="varcreative-avatar">
                    <?php if (!empty($producto->path)) { ?>
                      <a href="<?php echo mklink ($producto->link) ?>"><img src="<?php echo $producto->path ?>" alt="<?php echo ($producto->nombre);?>"></a>
                    <?php } else if (!empty($empresa->no_imagen)) { ?>
                      <a href="<?php echo mklink ($producto->link) ?>"><img src="/sistema/<?php echo $empresa->no_imagen ?>" alt="<?php echo ($producto->nombre);?>"></a>
                    <?php } else { ?>
                      <a href="<?php echo mklink ($producto->link) ?>"><img src="images/no-imagen.png" alt="<?php echo ($producto->nombre);?>"></a>
                    <?php } ?>
                    <div class="varcreative-eliminar-item">
                      <img src="/templates/comun/img/delete.png" alt="Eliminar" onclick="varcreative_ir_modificar_carrito()"/>
                    </div>
                  </div>
                  <div class="varcreative-item-info">
                    <a class="varcreative-nombre-item" href="<?php echo mklink($producto->link) ?>">
                      <?php echo ($producto->nombre) ?>
                      <?php echo (!empty($item->descripcion) ? "<br/>".$item->descripcion : "") ?>
                    </a>
                    <div class="varcreative-item-controls">
                      <div class="item-unit">
                        <span><?php echo $ll["cantidad"] ?>: </span>
                        <select disabled class="varcreative-input varcreative-input-cantidad" <?php /*data-id="<?php echo $producto->id ?>" onchange="cambiar_cantidad(this)" */ ?>>
                          <?php $maximo = ($producto->maximo_disponible > 0 && $producto->stock > $producto->maximo_disponible) ? $producto->maximo_disponible : $producto->stock;
                          $maximo = ($maximo > 0) ? $maximo : 50; ?>
                          <?php for($i=1;$i<=$maximo;$i++) { ?>
                          <option <?php echo ($i==$item->cantidad)?"selected":"" ?> value="<?php echo $i ?>"><?php echo $i ?></option>
                          <?php } ?>
                        </select>
                      </div>
                      <div style="float: right;">
                        <?php echo format($item->precio,true,$producto->moneda)?>
                      </div>                              
                    </div>
                  </div>
                </div>
              <?php } ?>
            </div>

            <div class="varcreative-subtotal varcreative-table-row">
              <span class="subtotal varcreative-pull-left"><?php echo $ll["subtotal"] ?></span> 
              <span class="varcreative-pull-right"><?php echo format($carrito->subtotal); ?></span>
            </div>

            <div class="descuento varcreative-table-row">
              <span class="label_cupon_descuento varcreative-pull-left">
                <?php echo $ll["cupon_descuento"] ?>
              </span>
              <span class="varcreative-pull-right">
                <input value="<?php echo isset($carrito->codigo_promocional)?$carrito->codigo_promocional:"" ?>" type="text" onchange="varcreative_cupon_descuento(this)" id="varcreative-cupon-descuento" class="varcreative-input-2" />
              </span>
            </div>

            <?php if ($carrito->porc_descuento != 0) { ?>
              <div class="descuento varcreative-table-row">
                <span class="label_descuento varcreative-pull-left">
                  <?php echo $ll["descuento"]." ".$carrito->porc_descuento ?>%
                </span>
                <span class="numero_descuento varcreative-pull-right">
                  <?php echo format($carrito->descuento) ?>
                </span>
              </div>
            <?php } ?>

            <?php if ($usar_precio_neto == 1) { ?>
              <div class="iva varcreative-table-row">
                <span class="label_iva varcreative-pull-left"><?php echo $ll["iva"] ?></span>
                <span class="numero_iva varcreative-pull-right"><?php echo format($carrito->iva) ?></span>
              </div>
            <?php } ?>

            <?php if ($carrito->costo_envio > 0) { ?>
              <div class="costo_envio varcreative-table-row">
                <span class="label_costo_envio varcreative-pull-left"><?php echo $ll["costo_envio"] ?></span>
                <span class="numero_costo_envio varcreative-pull-right"><?php echo format($carrito->costo_envio) ?></span>
              </div>
            <?php } else if ($carrito->total >= $empresa->tienda_envio_desde && $carrito->costo_envio == 0 && $carrito->forma_envio != "retiro_sucursal" && $carrito->forma_envio != "a_convenir" && $carrito->numero_paso > 3) { ?>
              <div class="costo_envio varcreative-table-row">
                <span class="label_costo_envio varcreative-pull-left"><?php echo $ll["costo_envio"] ?></span>
                <span class="costo_envio_gratis varcreative-pull-right"><?php echo $ll["gratis"] ?></span>
              </div>
            <?php } ?>

            <div class="total_general varcreative-table-row">
              <span class="label_total_general"><?php echo $ll["total"] ?></span>
              <span class="numero_total_general"><?php echo format($carrito->total + (($carrito->costo_envio > 0) ? $carrito->costo_envio : 0)) ?></span>
            </div>

          </div>
        </div>
      </div>
    </div>
  <?php } ?>
</div>