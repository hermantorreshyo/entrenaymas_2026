<div class="row">
  <div class="col-md-10 col-md-offset-1">
    <div class="panel panel-default">
      <div class="panel-body">
        <div class="padder">

          <div class="form-group">
            <label class="control-label">Nombre</label>
            <input <%= (!edicion)?"disabled":"" %> type="text" required name="nombre" id="clientes_nombre" value="<%= nombre %>" class="form-control"/>
          </div>
          <div class="form-group">
            <label class="control-label">Email </label>
            <% if (edicion) { %>
              <input type="text" name="email" class="form-control" id="clientes_email" value="<%= email %>"/>
            <% } else { %>
              <span><%= email %></span>
            <% } %>
          </div>

          <div class="form-group mb0 tar dn">
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

          <?php
          single_upload(array(
            "name"=>"path",
            "label"=>"Imagen principal",
            "url"=>"/sistema/clientes/function/save_image/",
            "width"=>(isset($empresa->config["cliente_image_width"]) ? $empresa->config["cliente_image_width"] : 256),
            "height"=>(isset($empresa->config["cliente_image_height"]) ? $empresa->config["cliente_image_height"] : 256),
            "quality"=>(isset($empresa->config["cliente_image_quality"]) ? $empresa->config["cliente_image_quality"] : 0.92),
            "thumbnail_width"=>(isset($empresa->config["cliente_thumbnail_width"]) ? $empresa->config["cliente_thumbnail_width"] : 0),
            "thumbnail_height"=>(isset($empresa->config["cliente_thumbnail_height"]) ? $empresa->config["cliente_thumbnail_height"] : 0),
          )); ?>

          <div class="form-group">
            <label class="control-label">Etiquetas</label>
            <select multiple id="cliente_etiquetas" style="width: 100%">
              <% for (var i=0; i< etiquetas.length; i++) { %>
                <% var o = etiquetas[i] %>
                <option selected><%= o %></option>
              <% } %>
            </select>
          </div>

          <div class="form-group">
            <label class="control-label">Observaciones </label>
            <% if (edicion) { %>
              <textarea placeholder="Escriba aqui otros datos de contacto o notas de su cliente..." style="height:100px" class="form-control" name="observaciones" id="cliente_observaciones"><%= observaciones %></textarea>
            <% } else { %>
              <span><%= observaciones %></span>
            <% } %>
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

          <div class="row">
            <div class="col-md-3 dn">
              <div class="form-group">
                <label class="control-label">C&oacute;digo interno</label>
                <% if (edicion) { %>
                  <input type="text" name="codigo" id="clientes_codigo" value="<%= codigo %>" class="form-control"/>
                <% } else { %>
                  <span><%= codigo %></span>
                <% } %>
              </div>  
            </div>
            <div class="col-md-4">
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
            <div class="col-md-4">
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
            <div class="col-md-4">
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

        </div>
      </div>
    </div>

    <div class="panel panel-default">
      <div class="panel-body">
        <div class="padder">
          <div class="form-group mb0 clearfix">
            <label class="control-label">
              <?php echo lang(array(
                "es"=>"Informaci&oacute;n de contacto",
                "en"=>"Contact information",
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
                "es"=>"Tel&eacute;fonos, direcciones, y dem&aacute;s datos para contactarte con tu cliente.",
                "en"=>"Tel&eacute;fonos, direcciones, y dem&aacute;s datos para contactarte con tu cliente.",
              )); ?>                  
            </div>
          </div>
        </div>
      </div>
      <div class="panel-body expand">
        <div class="padder">

          <div class="row dn">
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
              <input type="text" name="direccion" class="form-control" id="clientes_direccion" value="<%= direccion %>"/>
            <% } else { %>
              <span><%= direccion %></span>
            <% } %>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label class="control-label">Piso</label>
                <input <%= (!edicion)?"disabled":"" %> type="text" name="custom_3" class="form-control" id="clientes_custom_3" value="<%= custom_3 %>"/>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="control-label">Depto.</label>
                <input <%= (!edicion)?"disabled":"" %> type="text" name="custom_4" class="form-control" id="clientes_custom_4" value="<%= custom_4 %>"/>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label class="control-label">Tel&eacute;fono </label>
                <div class="oh">
                  <% if (edicion) { %>
                    <input type="text" name="telefono" class="form-control dib w100" id="clientes_telefono" value="<%= telefono %>"/>
                    <input type="text" name="celular" class="form-control dib w200" id="clientes_celular" value="<%= celular %>"/>
                  <% } else { %>
                    <span><%= telefono %><%= celular %></span>
                  <% } %>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label class="control-label">Latitud</label>
                <input type="text" name="latitud" value="<%= latitud %>" id="clientes_latitud" class="form-control"/>
              </div>  
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label class="control-label">Longitud</label>
                <input type="text" name="longitud" value="<%= longitud %>" id="clientes_longitud" class="form-control"/>
              </div>  
            </div>
          </div>

        </div>
      </div>
    </div>

    <div class="panel panel-default dn">
      <div class="panel-body">
        <div class="padder">
          <div class="form-group mb0 clearfix">
            <label class="control-label">
              <?php echo lang(array(
                "es"=>"Datos comerciales",
                "en"=>"Datos comerciales",
              )); ?>
            </label>
            <a class="expand-link fr">
              <?php echo lang(array(
                "es"=>"+ M&aacute;s opciones",
                "en"=>"+ More options",
              )); ?>
            </a>
            <div class="panel-description">
              <?php echo lang(array(
                "es"=>"Cuenta corriente, descuentos, listas de precios, etc.",
                "en"=>"Cuenta corriente, descuentos, listas de precios, etc.",
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
                <label class="control-label">Utilizar lista de precios </label>
                <% if (edicion) { %>
                  <select class="form-control" id="clientes_lista">
                    <option <%= (lista == 0) ? "selected":"" %> value="0">Lista 1</option>
                    <option <%= (lista == 1) ? "selected":"" %> value="1">Lista 2</option>
                    <option <%= (lista == 2) ? "selected":"" %> value="2">Lista 3</option>
                  </select>    
                <% } else { %>
                  <span>Lista <%= (lista+1) %></span>
                <% } %>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="control-label">Aplicar Descuento</label>
                <% if (edicion) { %>
                  <div class="input-group">
                    <input type="text" name="descuento" class="form-control" id="clientes_descuento" value="<%= descuento %>"/>
                    <span class="input-group-addon">%</span>
                  </div>
                <% } else { %>
                  <span><%= descuento %></span>
                <% } %>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label class="control-label">Forma de Pago </label>
                <% if (edicion) { %>
                  <select class="form-control" id="clientes_forma_pago">
                    <option <%= (forma_pago == "C") ? "selected":"" %> value="C">Cuenta Corriente</option>
                    <option <%= (forma_pago == "E") ? "selected":"" %> value="E">Efectivo</option>
                  </select>    
                <% } else { %>
                  <span>
                    <%= (forma_pago == "C") ? "Cuenta Corriente" : "" %>
                    <%= (forma_pago == "E") ? "Efectivo" : "" %>
                  </span>
                <% } %>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="control-label">Fecha Alta</label>
                <div class="input-group">
                  <input type="text" class="form-control" id="clientes_fecha_inicial" name="fecha_inicial" value="<%= fecha_inicial %>"/>
                  <span class="input-group-btn">
                    <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                  </span>        
                </div>
              </div>
            </div>
          </div>

          <% if (control.check("vendedores") > 0) { %>
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
          <% } %>

          <% if (control.check("planes") > 0) { %>
            <div class="form-group">
              <label class="control-label">Plan </label>
              <% if (edicion) { %>
                <select class="form-control" id="clientes_planes">
                <option value="0">-</option>
                <% for(var i=0;i < planes.length;i++) { %>
                  <% var o = planes[i]; %>
                  <option value="<%= o.id %>" <%= (o.id==id_plan)?"selected":"" %>><%= o.nombre %></option>
                <% } %>                   
                </select>
              <% } %>
            </div>
          <% } %>   

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label class="control-label">Saldo Inicial</label>
                <% if (edicion) { %>
                  <div class="input-group">
                    <input type="text" name="saldo_inicial" class="form-control" id="clientes_saldo_inicial" value="<%= saldo_inicial %>"/>
                    <span class="input-group-addon">$</span>
                  </div>
                <% } else { %>
                  <span><%= saldo_inicial %></span>
                <% } %>
              </div>
            </div>
            <% if (ESTADO == 1) { %>
              <div class="col-md-6">
                <div class="form-group">
                  <label class="control-label">Saldo Inicial B</label>
                  <% if (edicion) { %>
                    <div class="input-group">
                      <input type="text" name="saldo_inicial_2" class="form-control" id="clientes_saldo_inicial_2" value="<%= saldo_inicial_2 %>"/>
                      <span class="input-group-addon">$</span>
                    </div>
                  <% } else { %>
                    <span><%= saldo_inicial_2 %></span>
                  <% } %>
                </div>
              </div>
            <% } %>
          </div>
        
          <div class="form-group">
            <div class="form-inline">
              <% if (edicion) { %>
                <div class="checkbox" style="margin-left:6px">
                  <label class="i-checks">
                    <input type="checkbox" name="percibe_ib" class="checkbox" value="1" <%= (percibe_ib == 1)?"checked":"" %>><i></i>
                    Percibe ingresos brutos?
                  </label>
                </div>
              <% } else { %>
                <span><%= ((percibe_ib==0) ? "No percibe ingresos brutos" : "Percibe ingresos brutos") %></span>
              <% } %>

              <% if (edicion) { %>
                <div class="input-group w-sm m-l">
                  <input type="text" name="percepcion_ib" class="form-control" id="clientes_percepcion_ib" value="<%= percepcion_ib %>"/>
                  <span class="input-group-addon">%</span>
                </div>
              <% } else { %>
                <span><%= percepcion_ib %></span>
              <% } %>
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
                "es"=>"Contrase&ntilde;a",
                "en"=>"Password",
              )); ?>
            </label>
            <a class="expand-link fr">
              <?php echo lang(array(
                "es"=>"Cambiar contrase&ntilde;a",
                "en"=>"Change password",
              )); ?>
            </a>
            <div class="panel-description">
              <?php echo lang(array(
                "es"=>"Clave utilizada para ingresar al sistema.",
                "en"=>"Agregar variantes a productos como talle, color, etc.",
              )); ?>                  
            </div>
          </div>
        </div>
      </div>
      <div class="panel-body expand">
        <div class="padder">
          <div class="form-group">
            <label class="control-label">Contrase&ntilde;a</label>
            <input type="password" class="form-control" id="clientes_password" placeholder="Escriba aqui para cambiar la contrase&ntilde;a"/>
          </div>
          <div class="form-group">
            <label class="control-label">Repetir contrase&ntilde;a</label>
            <input type="password" class="form-control" id="clientes_password_2" placeholder="Escriba nuevamente la contrase&ntilde;a anterior"/>
          </div>
         </div>
      </div>
    </div>

  </div>
</div>