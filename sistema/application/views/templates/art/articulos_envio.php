<div class="panel panel-default <%= (ID_EMPRESA == 1284)?"dn":"" %>">
  <div class="panel-body">
    <div class="padder">
      <div class="form-group mb0 clearfix">
        <label class="control-label">
          <?php echo lang(array(
            "es"=>"Env&iacute;o",
            "en"=>"Shipping",
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
            "es"=>"Informaci&oacute;n relacionada al despacho y entrega del producto: dimensiones, peso, etc.",
            "en"=>"Agregar variantes a productos como talle, color, etc.",
          )); ?>                  
        </div>
      </div>
    </div>
  </div>
  <div class="panel-body expand">
    <div class="padder">
      <div class="row">
        <div class="col-md-3">
          <div class="form-group">
            <label class="control-label"><?php echo (isset($empresa->config["producto_ancho_label"]) ? $empresa->config["producto_ancho_label"] : "Ancho") ?></label>
            <?php if (isset($empresa->config["producto_ancho_label"])) { ?>
              <input type="text" name="ancho" id="articulo_ancho" value="<%= ancho %>" class="form-control"/>
            <?php } else { ?>
              <div class="input-group no-br">
                <input type="text" name="ancho" id="articulo_ancho" value="<%= ancho %>" class="form-control"/>
                <span class="input-group-addon">Mts</span>
              </div>
            <?php } ?>
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label class="control-label"><?php echo (isset($empresa->config["producto_alto_label"]) ? $empresa->config["producto_alto_label"] : "Alto") ?></label>
            <?php if (isset($empresa->config["producto_alto_label"])) { ?>
              <input type="text" name="alto" id="articulo_alto" value="<%= alto %>" class="form-control"/>
            <?php } else { ?>
              <div class="input-group no-br">
                <input type="text" name="alto" id="articulo_alto" value="<%= alto %>" class="form-control"/>
                <span class="input-group-addon">Mts</span>
              </div>
            <?php } ?>
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label class="control-label"><?php echo (isset($empresa->config["producto_profundidad_label"]) ? $empresa->config["producto_profundidad_label"] : "Profundidad") ?></label>
            <?php if (isset($empresa->config["producto_profundidad_label"])) { ?>
              <input type="text" name="profundidad" id="articulo_profundidad" value="<%= profundidad %>" class="form-control"/>
            <?php } else { ?>
              <div class="input-group no-br">
                <input type="text" name="profundidad" id="articulo_profundidad" value="<%= profundidad %>" class="form-control"/>
                <span class="input-group-addon">Mts</span>
              </div>
            <?php } ?>
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label class="control-label">Peso</label>
            <div class="input-group no-br">
              <input type="text" name="peso" id="articulo_peso" value="<%= peso %>" class="form-control"/>
              <span class="input-group-addon">Kgs</span>
            </div>
          </div>
        </div>
      </div>

      <div class="form-group">
        <div class="checkbox">
          <label class="i-checks">
            <input type="checkbox" id="articulo_no_totalizar_reparto" name="no_totalizar_reparto" value="1" <%= (no_totalizar_reparto == 1)?"checked":"" %> >
            <i></i>
            Envio gratis para este producto
          </label>
        </div>
      </div>

      <div class="form-group">
        <div class="checkbox">
          <label class="i-checks">
            <input type="checkbox" id="articulo_coordinar_envio" name="coordinar_envio" value="1" <%= (coordinar_envio == 1)?"checked":"" %> >
            <i></i>
            Coordinar envio de este producto con el cliente.
          </label>
        </div>
      </div>
      <div class="form-group">
        <div class="checkbox">
          <label class="i-checks">
            <input type="checkbox" id="articulo_fragil" name="fragil" value="1" <%= (fragil == 1)?"checked":"" %> >
            <i></i>
            <% if (ID_EMPRESA == 42) { %>
              Marcar el producto como 'fragil'.
            <% } else { %>
              Habilitar el envío solamente a las áreas excepcionales.
            <% } %>
          </label>
        </div>
      </div>

    </div>
  </div>
</div>