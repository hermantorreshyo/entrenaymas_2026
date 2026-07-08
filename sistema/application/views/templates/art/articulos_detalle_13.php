<div class="wrapper-md">
  <div class="centrado rform">
    <div class="row">
      <div class="col-md-4">
        <div class="detalle_texto">Informaci&oacute;n general</div>
      </div>
      <div class="col-md-8">
        <div class="panel panel-default">
          <div class="panel-body">
            <div class="padder">
              <div class="row">
                <div class="col-md-3">
                  <div class="form-group">
                    <label class="control-label">Codigo Interno</label>
                    <% if (edicion) { %>
                      <input type="text" required name="codigo" id="articulo_codigo" value="<%= codigo %>" class="form-control"/>
                    <% } else { %>
                      <span><%= codigo %></span>
                    <% } %>
                  </div>
                </div>
                <div class="col-md-9">
                  <div class="form-group">
                    <label class="control-label">Nombre</label>
                    <% if (edicion) { %>
                      <input type="text" required name="nombre" id="articulo_nombre" value="<%= nombre %>" class="form-control"/>
                    <% } else { %>
                      <span><%= nombre %></span>
                    <% } %>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label class="control-label">Descripci&oacute;n</label>
                <input type="text" name="descripcion" id="articulo_descripcion" value="<%= descripcion %>" class="form-control"/>
              </div>
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Categoria</label>
                    <div class="input-group">
                      <select id="articulo_rubros" class="w100p"></select>
                      <span class="input-group-btn">
                        <button tabindex="-1" class="btn btn-info agregar_rubro">+</button>  
                      </span>
                    </div>
                  </div>  
                </div>
                <% if (control.check("marcas") > 0) { %>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="control-label">Marca</label>
                      <div class="input-group">
                        <select id="articulo_marcas" class="w100p"></select>
                        <span class="input-group-btn">
                          <button tabindex="-1" class="btn btn-info agregar_marca">+</button>  
                        </span>
                      </div>
                    </div>
                  </div>
                <% } %>
              </div>
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Costo Final</label>
                    <div>
                      <div class="col-xs-5 p0">
                        <select id="articulo_monedas" class="form-control" name="moneda">
                          <% for(var i=0;i< window.monedas.length;i++) { %>
                            <% var o = monedas[i]; %>
                            <option <%= (o.id == moneda)?"selected":"" %> value="<%= o.id %>"><%= o.signo %> (<%= o.nombre %>)</option>
                          <% } %>
                        </select>
                      </div>
                      <div class="col-xs-7 p0">
                        <input id="articulo_precio_final_dto" value="<%= precio_final_dto %>" type="text" class="form-control number" name="precio_final_dto"/>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">
                      <?php echo lang(array(
                        "es"=>"Stock actual",
                        "en"=>"Stock",
                      )); ?>
                    </label>
                    <input type="text" name="stock" id="articulo_stock" value="<%= stock %>" class="form-control"/>
                    <input type="checkbox" id="articulo_usa_stock" checked="checked" value="1" style="display: none" />
                  </div>
                </div>
              </div>

              <?php
              single_upload(array(
                  "name"=>"path",
                  "label"=>"Imagen Principal",
                  "url"=>"/sistema/articulos/function/save_image/",
                  "width"=>(isset($empresa->config["producto_image_width"]) ? $empresa->config["producto_image_width"] : 256),
                  "height"=>(isset($empresa->config["producto_image_height"]) ? $empresa->config["producto_image_height"] : 256),
              )); ?>  

            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-4"></div>
      <div class="col-md-8">
        <button class="btn guardar btn-success">Guardar</button>
      </div>
    </div>
  </div>
</div>