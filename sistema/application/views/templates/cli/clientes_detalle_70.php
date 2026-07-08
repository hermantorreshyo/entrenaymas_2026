<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-body">
        <div class="padder">
          <div class="form-group">
            <div class="row">
              <div class="col-md-8">
                <label class="control-label">Nombre / Raz&oacute;n Social</label>
                <% if (edicion) { %>
                  <input type="text" required name="nombre" id="clientes_nombre" value="<%= nombre %>" class="form-control"/>
                <% } else { %>
                  <span><%= nombre %></span>
                <% } %>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label class="control-label">Vendedor </label>
                  <% if (edicion) { %>
                    <select class="form-control" id="clientes_vendedores">
                    <option value="0">-</option>
                    <% for(var i=0;i < vendedores.length;i++) { %>
                      <% var o = vendedores[i]; %>
                      <option value="<%= o.id %>" <%= (o.id==id_vendedor)?"selected":"" %>><%= o.nombre %></option>
                    <% } %>                   
                    </select>
                  <% } %>
                </div>
              </div>
            </div>
          </div>
          <div class="form-group mb0 tar">
            <a class="expand-link" id="expand_principal">
              <?php echo lang(array(
                "es"=>"+ M&aacute;s opciones",
                "en"=>"+ More options",
              )); ?>
            </a>
          </div>
        </div>
      </div>
      <div class="panel-body expand">
        <div class="padder">

          <div class="row">
            <div class="col-md-3">
              <div class="form-group">
                <label class="control-label">C&oacute;digo interno</label>
                <% if (edicion) { %>
                  <input type="text" name="codigo" id="clientes_codigo" value="<%= codigo %>" class="form-control"/>
                <% } else { %>
                  <span><%= codigo %></span>
                <% } %>
              </div>  
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label class="control-label">Tipo de IVA </label>
                <% if (edicion) { %>
                  <select class="form-control" id="clientes_tipo_iva">
                    <option <%= (id_tipo_iva == 4) ? "selected":"" %> value="4">Consumidor Final</option>
                    <option <%= (id_tipo_iva == 2) ? "selected":"" %> value="2">Monotributo</option>
                    <option <%= (id_tipo_iva == 1) ? "selected":"" %> value="1">Responsable Inscripto</option>
                    <option <%= (id_tipo_iva == 3) ? "selected":"" %> value="3">Exento</option>
                  </select>    
                <% } else { %>
                  <span>
                    <%= (id_tipo_iva == 4) ? "Consumidor Final":"" %>
                    <%= (id_tipo_iva == 2) ? "Monotributo":"" %>
                    <%= (id_tipo_iva == 1) ? "Responsable Inscripto":"" %>
                    <%= (id_tipo_iva == 3) ? "Exento":"" %>
                  </span>
                <% } %>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label class="control-label">Tipo de Documento</label>
                  <select <%= (edicion)?"":"disabled" %> class="form-control" id="clientes_tipo_documento">
                    <option <%= (id_tipo_documento == 96) ? "selected":"" %> value="96">DNI</option>
                    <option <%= (id_tipo_documento == 80) ? "selected":"" %> value="80">CUIT</option>
                    <option <%= (id_tipo_documento == 86) ? "selected":"" %> value="86">CUIL</option>
                    <option <%= (id_tipo_documento == 89) ? "selected":"" %> value="89">Libreta Enrolamiento</option>
                    <option <%= (id_tipo_documento == 90) ? "selected":"" %> value="90">Libreta Civica</option>
                    <option <%= (id_tipo_documento == 94) ? "selected":"" %> value="94">Pasaporte</option>
                    <option <%= (id_tipo_documento == 99) ? "selected":"" %> value="99">Sin identificacion</option>
                  </select>    
              </div>  
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label class="control-label">Numero Doc/CUIT </label>
                <% if (edicion) { %>
                  <input type="text" name="cuit" class="form-control" id="clientes_cuit" value="<%= cuit %>"/>
                <% } else { %>
                  <span><%= cuit %></span>
                <% } %>
              </div>
            </div>
          </div>

          <div class="form-group">
            <% if (edicion) { %>
              <div class="checkbox">
                <label class="i-checks">
                  <input type="checkbox" name="activo" class="checkbox" value="1" <%= (activo == 1)?"checked":"" %> ><i></i>
                  El cliente est&aacute; activo.
                </label>
              </div>
            <% } else { %>
              <span><%= ((activo==0) ? "Cliente inactivo" : "Cliente activo") %></span>
            <% } %>
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
                "es"=>"Guia comercial / profesional",
              )); ?>
            </label>
            <a id="expand_mapa" class="expand-link fr">
              <?php echo lang(array(
                "es"=>"+ Ver opciones",
                "en"=>"+ View options",
              )); ?>
            </a>
            <div class="panel-description">
              <?php echo lang(array(
                "es"=>"Datos para completar la guia.",
              )); ?>                  
            </div>
          </div>
        </div>
      </div>
      <div class="panel-body expand">
        <div class="padder">

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label class="control-label">Guia comercial</label>
                <select class="form-control" id="cliente_custom_1" name="custom_1" <%= (edicion ? "":"disabled") %>>
                  <option value="0" <%= (custom_1 == "0")?"selected":"" %>>No posee</option>
                  <option value="1" <%= (custom_1 == "1")?"selected":"" %>>Tiene guia comercial</option>
                  <option value="2" <%= (custom_1 == "2")?"selected":"" %>>Tiene guia comercial bonificada</option>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="control-label">Valor Guia</label>
                <input type="text" class="form-control" id="cliente_custom_4" name="custom_4" value="<%= custom_4 %>">
              </div>
            </div>
          </div>

          <div class="form-group">
            <label class="control-label">Tipo</label>
            <% if (edicion) { %>
              <select class="form-control" id="cliente_custom_3" name="custom_3">
                <option value="Comercio" <%= (custom_3 == "Comercio")?"selected":"" %>>Comercio</option>
                <option value="Profesional" <%= (custom_3 == "Profesional")?"selected":"" %>>Profesional</option>
                <option value="Servicio" <%= (custom_3 == "Servicio")?"selected":"" %>>Servicio</option>
              </select>
            <% } else { %>
              <span><%= custom_3 %></span>
            <% } %>
          </div>

          <div class="form-group">
            <label class="control-label">Informaci&oacute;n</label>
            <% if (edicion) { %>
              <textarea style="height: 150px;" class="form-control" name="observaciones" id="cliente_observaciones"><%= observaciones %></textarea>
            <% } else { %>
              <span><%= observaciones %></span>
            <% } %>
          </div>

          <div class="form-group">
            <label class="control-label">Etiquetas</label>
            <select multiple id="cliente_etiquetas" style="width: 100%">
              <% for (var i=0; i< etiquetas.length; i++) { %>
                <% var o = etiquetas[i] %>
                <option selected><%= o %></option>
              <% } %>
            </select>
          </div>

          <?php
          single_upload(array(
            "name"=>"path",
            "label"=>"Logo",
            "url"=>"/sistema/clientes/function/save_image/",
            "width"=>(isset($empresa->config["cliente_image_width"]) ? $empresa->config["cliente_image_width"] : 256),
            "height"=>(isset($empresa->config["cliente_image_height"]) ? $empresa->config["cliente_image_height"] : 256),
            "quality"=>(isset($empresa->config["cliente_image_quality"]) ? $empresa->config["cliente_image_quality"] : 0.92),
            "thumbnail_width"=>(isset($empresa->config["cliente_thumbnail_width"]) ? $empresa->config["cliente_thumbnail_width"] : 0),
            "thumbnail_height"=>(isset($empresa->config["cliente_thumbnail_height"]) ? $empresa->config["cliente_thumbnail_height"] : 0),
          )); ?>

          <?php
          single_upload(array(
            "name"=>"path_2",
            "label"=>"Imagen principal",
            "url"=>"/sistema/clientes/function/save_image/",
            "width"=>(isset($empresa->config["cliente_image_2_width"]) ? $empresa->config["cliente_image_2_width"] : 256),
            "height"=>(isset($empresa->config["cliente_image_2_height"]) ? $empresa->config["cliente_image_2_height"] : 256),
            "quality"=>(isset($empresa->config["cliente_image_2_quality"]) ? $empresa->config["cliente_image_2_quality"] : 0.92),
          )); ?>

          <div class="row">
            <div class="col-md-8">
              <div class="form-group">
                <label class="control-label">Localidad</label>
                <input type="text" value="<%= localidad %>" id="clientes_localidad" placeholder="Escriba una ciudad y seleccionela de la lista" class="form-control"/>
              </div>  
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label class="control-label">C&oacute;digo Postal</label>
                <input type="text" name="codigo_postal" value="<%= codigo_postal %>" id="clientes_codigo_postal" class="form-control"/>
              </div>  
            </div>
          </div>

          <div class="form-group">
            <label class="control-label">Direccion </label>
            <% if (edicion) { %>
              <div class="input-group">
                <input type="text" name="direccion" value="<%= direccion %>" id="clientes_direccion" class="form-control"/>
                <div class="input-group-btn">
                  <button id="cargar_mapa" class="btn btn-default">Actualizar Mapa</button>
                </div>
              </div>
            <% } else { %>
              <span><%= direccion %></span>
            <% } %>
          </div>

          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label class="control-label">Tel&eacute;fono </label>
                <% if (edicion) { %>
                  <input type="text" name="telefono" class="form-control" id="clientes_telefono" value="<%= telefono %>"/>
                <% } else { %>
                  <span><%= telefono %></span>
                <% } %>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label class="control-label">Celular </label>
                <% if (edicion) { %>
                  <input type="text" name="celular" class="form-control" id="clientes_celular" value="<%= celular %>"/>
                <% } else { %>
                  <span><%= celular %></span>
                <% } %>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label class="control-label">FAX </label>
                <% if (edicion) { %>
                  <input type="text" name="fax" class="form-control" id="clientes_fax" value="<%= fax %>"/>
                <% } else { %>
                  <span><%= fax %></span>
                <% } %>
              </div>    
            </div>
          </div>

          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label class="control-label">Email </label>
                <% if (edicion) { %>
                  <input type="text" name="email" class="form-control" id="clientes_email" value="<%= email %>"/>
                <% } else { %>
                  <span><%= email %></span>
                <% } %>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label class="control-label">Facebook </label>
                <% if (edicion) { %>
                  <input type="text" name="facebook" class="form-control" id="clientes_facebook" value="<%= facebook %>"/>
                <% } else { %>
                  <span><%= facebook %></span>
                <% } %>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label class="control-label">Twitter </label>
                <% if (edicion) { %>
                  <input type="text" name="twitter" class="form-control" id="clientes_twitter" value="<%= twitter %>"/>
                <% } else { %>
                  <span><%= twitter %></span>
                <% } %>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label class="control-label">Instagram </label>
                <% if (edicion) { %>
                  <input type="text" name="instagram" class="form-control" id="clientes_instagram" value="<%= instagram %>"/>
                <% } else { %>
                  <span><%= instagram %></span>
                <% } %>
              </div>    
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label class="control-label">Linkedin </label>
                <% if (edicion) { %>
                  <input type="text" name="linkedin" class="form-control" id="clientes_linkedin" value="<%= linkedin %>"/>
                <% } else { %>
                  <span><%= linkedin %></span>
                <% } %>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label class="control-label">Youtube </label>
                <% if (edicion) { %>
                  <input type="text" name="youtube" class="form-control" id="clientes_youtube" value="<%= youtube %>"/>
                <% } else { %>
                  <span><%= youtube %></span>
                <% } %>
              </div>
            </div>
          </div>

          <div class="form-group">
            <label class="control-label">Horario</label>
            <textarea class="form-control" name="horario" id="cliente_horario"><%= horario %></textarea>
          </div>  

          <div style="height:400px;" id="mapa"></div>
          <div class="help-block"><button class="btn btn-default add_marker m-r">Agregar Marcador</button>Doble click al marcador para eliminarlo. </div>

        </div>
      </div>
    </div>

  </div>
</div>