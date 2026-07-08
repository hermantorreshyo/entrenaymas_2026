<script type="text/template" id="medios_pago_configuracion_edit_panel_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <h1 class="m-n font-thin h3"><i class="fa fa-cog icono_principal"></i>Configuraci&oacute;n
  / <b>Medios de Pago</b>
  </h1>
</div>
<div class="wrapper-md">
  <div class="centrado rform">
    <div class="row">
      <div class="col-md-10 col-md-offset-1">
        
        <div class="panel panel-default <%= (IDIOMA == "en")?"dn":"" %>">
          <div class="panel-body">
            <div class="padder">
              <div class="form-group mb0 clearfix">
                <label class="control-label">
                  <?php echo lang(array(
                    "es"=>"MercadoPago",
                    "en"=>"MercadoPago",
                  )); ?>
                </label>
                <a class="expand-link fr">
                  <?php echo lang(array(
                    "es"=>"+ Ver opciones",
                    "en"=>"+ View options",
                  )); ?>
                </a>
                <div class="panel-description">
                  <?php echo lang(array(
                    "es"=>"Ingrese los datos de su cuenta de Mercadopago para enlazar su web y poder recibir los pagos efectuados por clientes.",
                  )); ?>                  
                </div>
              </div>
            </div>
          </div>
          <div class="panel-body expand" style="<%= (habilitar_mp==1)?"display:block":"" %>">
            <div class="padder">
              <div class="form-group">
                <div class="checkbox">
                  <label class="i-checks">
                    <input type="checkbox" name="habilitar_mp" class="checkbox" value="1" <%= (habilitar_mp == 1)?"checked":"" %> >
                    <i></i>
                    <?php echo lang(array(
                      "es"=>"Habilitar la opci&oacute;n de pagar con MercadoPago.",
                    )); ?>
                  </label>
                </div>
              </div>
              <div class="form-group">
                <label class="control-label tal">Client ID</label>
                <input type="text" name="mp_client_id" class="form-control" value="<%= mp_client_id %>"/>
              </div>
              <div class="form-group">
                <label class="control-label tal">Client Secret</label>
                <input type="text" name="mp_client_secret" class="form-control" value="<%= mp_client_secret %>"/>
              </div>
              <div class="form-group">
                <a class="btn btn-default" href="https://www.mercadopago.com/mla/account/credentials?type=basic" target="_blank">Obtener credenciales</a>
              </div>
              <div class="form-group">
                <label class="control-label">Moneda</label>
                <select id="medios_pago_configuracion_monedas" class="form-control" name="mp_moneda">
                  <% for(var i=0;i< window.monedas.length;i++) { %>
                    <% var o = monedas[i]; %>
                    <option <%= (o.codigo == mp_moneda)?"selected":"" %> value="<%= o.codigo %>"><%= o.signo %> (<%= o.nombre %>)</option>
                  <% } %>
                </select>
              </div>
            </div>
          </div>
        </div>
        <% if (ID_EMPRESA != 263) { %>
          <div class="panel panel-default">
            <div class="panel-body">
              <div class="padder">
                <div class="form-group mb0 clearfix">
                  <label class="control-label">
                    <?php echo lang(array(
                      "es"=>"Paypal",
                      "en"=>"Paypal",
                    )); ?>
                  </label>
                  <a class="expand-link fr">
                    <?php echo lang(array(
                      "es"=>"+ Ver opciones",
                      "en"=>"+ View options",
                    )); ?>
                  </a>
                  <div class="panel-description">
                    <?php echo lang(array(
                      "es"=>"Reciba pagos internacionales a trav&eacute;s de Paypal.",
                      "en"=>"Receive payments with PayPal."
                    )); ?>                  
                  </div>
                </div>
              </div>
            </div>
            <div class="panel-body expand" style="<%= (habilitar_paypal==1)?"display:block":"" %>">
              <div class="padder">
                <div class="form-group">
                  <div class="checkbox">
                    <label class="i-checks">
                      <input type="checkbox" name="habilitar_paypal" class="checkbox" value="1" <%= (habilitar_paypal == 1)?"checked":"" %> >
                      <i></i>
                      <?php echo lang(array(
                        "es"=>"Habilitar la opci&oacute;n de pagar con Paypal.",
                        "en"=>"Enable the PayPal integration.",
                      )); ?>
                    </label>
                  </div>
                </div>
                <div class="form-group">
                  <label class="control-label tal">
                    <?php echo lang(array(
                      "es"=>"Email utilizado en cuenta de Paypal.",
                      "en"=>"PayPal Email.",
                    )); ?>
                  </label>
                  <input type="text" name="paypal_email" class="form-control" value="<%= paypal_email %>"/>
                </div>
              </div>
            </div>
          </div>

          <div class="panel panel-default">
            <div class="panel-body">
              <div class="padder">
                <div class="form-group mb0 clearfix">
                  <label class="control-label">Stripe</label>
                  <a class="expand-link fr">
                    <?php echo lang(array(
                      "es"=>"+ Ver opciones",
                      "en"=>"+ View options",
                    )); ?>
                  </a>
                  <div class="panel-description">
                    <?php echo lang(array(
                      "es"=>"Reciba pagos internacionales a trav&eacute;s de Stripe.",
                      "en"=>"Receive payments with Stripe."
                    )); ?>                  
                  </div>
                </div>
              </div>
            </div>
            <div class="panel-body expand" style="<%= (habilitar_stripe==1)?"display:block":"" %>">
              <div class="padder">
                <div class="form-group">
                  <div class="checkbox">
                    <label class="i-checks">
                      <input type="checkbox" name="habilitar_stripe" class="checkbox" value="1" <%= (habilitar_stripe == 1)?"checked":"" %> >
                      <i></i>
                      <?php echo lang(array(
                        "es"=>"Habilitar la opci&oacute;n de pagar con Stripe.",
                        "en"=>"Enable Stripe integration.",
                      )); ?>
                    </label>
                  </div>
                </div>
                <div class="form-group">
                  <label class="control-label tal">Public Key</label>
                  <input type="text" name="stripe_public" class="form-control" value="<%= stripe_public %>"/>
                </div>
                <div class="form-group">
                  <label class="control-label tal">Secret Key</label>
                  <input type="text" name="stripe_secret" class="form-control" value="<%= stripe_secret %>"/>
                </div>
              </div>
            </div>
          </div>

          <div class="panel panel-default">
            <div class="panel-body">
              <div class="padder">
                <div class="form-group mb0 clearfix">
                  <label class="control-label">
                    <?php echo lang(array(
                      "es"=>"Transferencia bancaria",
                      "en"=>"Bank Transfer",
                    )); ?>
                  </label>
                  <a class="expand-link fr">
                    <?php echo lang(array(
                      "es"=>"+ Ver opciones",
                      "en"=>"+ View options",
                    )); ?>
                  </a>
                  <div class="panel-description">
                    <?php echo lang(array(
                      "es"=>"Permita recibir pagos a trav&eacute;s de transferencia bancaria o dep&oacute;sito.",
                      "en"=>"Enable payment method with bank transfer.",
                    )); ?>
                  </div>
                </div>
              </div>
            </div>
            <div class="panel-body expand" style="<%= (habilitar_banco==1)?"display:block":"" %>">
              <div class="padder">
                <div class="form-group">
                  <div class="checkbox">
                    <label class="i-checks">
                      <input type="checkbox" name="habilitar_banco" class="checkbox" value="1" <%= (habilitar_banco == 1)?"checked":"" %> >
                      <i></i>
                      <?php echo lang(array(
                        "es"=>"Habilitar la opci&oacute;n de pagar con Transferencia Bancaria.",
                        "en"=>"Enable Bank Transfer method.",
                      )); ?>
                    </label>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="panel panel-default">
            <div class="panel-body">
              <div class="padder">
                <div class="form-group mb0 clearfix">
                  <label class="control-label">
                    <?php echo lang(array(
                      "es"=>"Pago contrarrembolso",
                      "en"=>"Payment in cash",
                    )); ?>
                  </label>
                  <a class="expand-link fr">
                    <?php echo lang(array(
                      "es"=>"+ Ver opciones",
                      "en"=>"+ View options",
                    )); ?>
                  </a>
                  <div class="panel-description">
                    <?php echo lang(array(
                      "es"=>"Permita recibir pagos en efectivo cuando se entrega la mercaderia.",
                      "en"=>"Allow payments in cash when the users receive the products.",
                    )); ?>
                  </div>
                </div>
              </div>
            </div>
            <div class="panel-body expand" style="<%= (habilitar_contrarrembolso>=1)?"display:block":"" %>">
              <div class="padder">
                <div class="form-group">
                  <select class="form-control" name="habilitar_contrarrembolso">
                    <option value="0" <%= (habilitar_contrarrembolso == 0) ? "selected":"" %>><?php echo lang(array("es"=>"Deshabilitar pago contrarrembolso.")); ?></option>
                    <option value="1" <%= (habilitar_contrarrembolso == 1) ? "selected":"" %>><?php echo lang(array("es"=>"Habilitar pago contrarrembolso en efectivo, para cualquier forma de envio.")); ?></option>
                    <option value="2" <%= (habilitar_contrarrembolso == 2) ? "selected":"" %>><?php echo lang(array("es"=>"Habilitar pago contrarrembolso en efectivo, unicamente para las zonas con excepciones de envio.")); ?></option>
                  </select>
                </div>
              </div>
            </div>
          </div>
          <div class="panel panel-default">
            <div class="panel-body">
              <div class="padder">
                <div class="form-group mb0 clearfix">
                  <label class="control-label">
                    <?php echo lang(array(
                      "es"=>"Pago a convenir",
                      "en"=>"Agree payments later",
                    )); ?>
                  </label>
                  <a class="expand-link fr">
                    <?php echo lang(array(
                      "es"=>"+ Ver opciones",
                      "en"=>"+ View options",
                    )); ?>
                  </a>
                  <div class="panel-description">
                    <?php echo lang(array(
                      "es"=>"La forma de pago se acordar&aacute; luego de haber realizado la compra.",
                      "en"=>"Agree with the customer after the purchase has been made."
                    )); ?>
                  </div>
                </div>
              </div>
            </div>
            <div class="panel-body expand" style="<%= (habilitar_a_convenir==1)?"display:block":"" %>">
              <div class="padder">
                <div class="form-group">
                  <div class="checkbox">
                    <label class="i-checks">
                      <input type="checkbox" name="habilitar_a_convenir" class="checkbox" value="1" <%= (habilitar_a_convenir == 1)?"checked":"" %> >
                      <i></i>
                      <?php echo lang(array(
                        "es"=>"Habilitar la opci&oacute;n de pago a convenir.",
                        "en"=>"Enable payments later."
                      )); ?>
                    </label>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="panel panel-default">
            <div class="panel-body">
              <div class="padder">
                <div class="form-group mb0 clearfix">
                  <label class="control-label">
                    <?php echo lang(array(
                      "es"=>"Pago en sucursal",
                      "en"=>"Payment in Store",
                    )); ?>
                  </label>
                  <a class="expand-link fr">
                    <?php echo lang(array(
                      "es"=>"+ Ver opciones",
                      "en"=>"+ View options",
                    )); ?>
                  </a>
                  <div class="panel-description">
                    <?php echo lang(array(
                      "es"=>"Permitir que el cliente pague cuando retire el producto en la sucursal.",
                      "en"=>"Allow the payments in store.",
                    )); ?>
                  </div>
                </div>
              </div>
            </div>
            <div class="panel-body expand" style="<%= (habilitar_pago_sucursal==1)?"display:block":"" %>">
              <div class="padder">
                <div class="form-group">
                  <div class="checkbox">
                    <label class="i-checks">
                      <input type="checkbox" name="habilitar_pago_sucursal" class="checkbox" value="1" <%= (habilitar_pago_sucursal == 1)?"checked":"" %> >
                      <i></i>
                      <?php echo lang(array(
                        "es"=>"Habilitar la opci&oacute;n de pago en sucursal.",
                        "en"=>"Enable payments in store."
                      )); ?>
                    </label>
                  </div>
                </div>
              </div>
            </div>
          </div>

        <% } %>
        <% if (ID_EMPRESA == 114) { %>
          <div class="panel panel-default">
            <div class="panel-body">
              <div class="padder">
                <div class="form-group mb0 clearfix">
                  <label class="control-label">
                    <?php echo lang(array(
                      "es"=>"SMS Masivos",
                      "en"=>"SMS Masivos",
                    )); ?>
                  </label>
                  <a class="expand-link fr">
                    <?php echo lang(array(
                      "es"=>"+ Ver opciones",
                      "en"=>"+ View options",
                    )); ?>
                  </a>
                  <div class="panel-description">
                    <?php echo lang(array(
                      "es"=>"Integraci&oacute;n con plataforma de env&iacute;o de SMS.",
                    )); ?>                  
                  </div>
                </div>
              </div>
            </div>
            <div class="panel-body expand">
              <div class="padder">
                <div class="form-group">
                  <label class="control-label tal">Usuario</label>
                  <input type="text" name="sms_gateway_user" class="form-control" value="<%= sms_gateway_user %>"/>
                </div>
                <div class="form-group">
                  <label class="control-label tal">Contrase&ntilde;a</label>
                  <input type="text" name="sms_gateway_password" class="form-control" value="<%= sms_gateway_password %>"/>
                </div>
              </div>
            </div>
          </div>
        <% } %>
      </div>
      <div class="line b-b m-b-lg"></div>
    </div>
    <div class="row">
      <div class="col-md-10 col-md-offset-1 tar">
        <button class="btn guardar btn-success">Guardar</button>
      </div>
    </div>
  </div>
</div>
</script>