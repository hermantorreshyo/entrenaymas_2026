<script type="text/template" id="reglas_ofertas_panel_template">
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <h1 class="m-n font-thin h3"><i class="fa fa-tags icono_principal"></i>Productos
      / <b>Ofertas</b>
    </h1>
  </div>
  <div class="wrapper-md ng-scope">
    <div class="panel panel-default">
    
      <div class="panel-heading oh">
        <div class="row">
          <div class="col-md-6 col-lg-3 sm-m-b">
            <div class="search_container"></div>
          </div>
          <% if (control.check("reglas_ofertas") > 1) { %>
            <div class="col-md-6 col-lg-offset-3 col-lg-6 text-right">
              <% if (MEGASHOP == 1) { %>
                <a class="btn btn-info btn-addon" href="app/#regla_oferta"><i class="fa fa-plus"></i>&nbsp;&nbsp;Nuevo&nbsp;&nbsp;</a>
              <% } else { %>
                <a class="btn btn-info btn-addon" href="app/#regla_oferta_2"><i class="fa fa-plus"></i>&nbsp;&nbsp;Nuevo&nbsp;&nbsp;</a>
              <% } %>
            </div>
          <% } %>
        </div>
      </div>
      <div class="panel-body">
        <div class="b-a table-responsive">
          <table id="reglas_ofertas_table" class="table table-striped sortable m-b-none default footable">
            <thead>
              <tr>
                <th style="width:20px;">
                  <label class="i-checks m-b-none">
                    <input class="esc sel_todos" type="checkbox"><i></i>
                  </label>
                </th>
                <th class="sorting" data-sort-by="nombre">Nombre</th>
                <th class="sorting" data-sort-by="desde">Desde</th>
                <th class="sorting" data-sort-by="hasta">Hasta</th>
                <% if (permiso > 1) { %>
                  <th class="w100"></th>
                <% } %>
              </tr>
            </thead>
            <tbody></tbody>
            <tfoot class="pagination_container hide-if-no-paging"></tfoot>
          </table>
        </div>
      </div>
    </div>
  </div>  
</script>


<script type="text/template" id="reglas_ofertas_item">
  <td>
    <label class="i-checks m-b-none">
      <input class="esc check-row" value="<%= id %>" type="checkbox"><i></i>
    </label>
  </td>
  <td class="ver"><span class='text-info'><%= nombre %></span></td>
  <td class="ver"><%= desde %></td>
  <td class="ver"><%= hasta %></td>
  <% if (permiso > 1) { %>
    <td class="p5 td_acciones">
      <i title="Activo" class="fa-check iconito fa activo <%= (activo == 1)?"active":"" %>"></i>
      <div class="btn-group dropdown ml10">
        <button class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <i class="fa fa-plus"></i>
        </button>    
        <ul class="dropdown-menu pull-right">
          <li><a href="javascript:void(0)" class="delete" data-id="<%= id %>">Eliminar</a></li>
        </ul>
      </div>
    </td>
  <% } %>
</script>

<script type="text/template" id="reglas_ofertas_edit_panel_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <h1 class="m-n font-thin h3"><i class="fa fa-tags icono_principal"></i>Productos 
    / Ofertas
    / <b><%= (id == undefined) ? 'Nueva' : nombre %></b>
  </h1>
</div>
<div class="wrapper-md ng-scope">
  <div class="centrado rform">
    <div class="row">
      <div class="col-md-10 col-md-offset-1">
        <div class="panel panel-default">
          <div class="panel-body">
            <div class="padder">
              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">Nombre</label>
                    <input type="text" name="nombre" class="form-control" id="reglas_ofertas_nombre" value="<%= nombre %>" <%= (!edicion)?"disabled":"" %>/>
                  </div>
                </div>
                <div class="col-md-8">
                  <div class="row">
                    <div class="col-md-4">
                      <div class="form-group">
                        <label class="control-label">Desde</label>
                        <div class="input-group">
                          <input type="text" class="form-control" id="reglas_ofertas_desde" value="<%= desde %>" name="desde">
                          <span class="input-group-btn">
                            <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                          </span>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="form-group">
                        <label class="control-label">Hasta</label>
                        <div class="input-group">
                          <input type="text" class="form-control" id="reglas_ofertas_hasta" value="<%= hasta %>" name="hasta">
                          <span class="input-group-btn">
                            <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                          </span>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="form-group">
                        <label class="control-label">Cant. Minima Arts.</label>
                        <input type="text" class="form-control" name="cantidad_minima" value="<%= cantidad_minima %>">
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="panel panel-default">
          <div class="panel-body">
            <div class="padder">
              <div class="clearfix">
                <div class="col-md-7 col-xs-12 p0">
                  <div class="col-sm-6 p0">
                    <label class="text-muted">C&oacute;digo</label>
                    <div class="input-group">
                      <input type="text" class="form-control action no-model" id="reglas_ofertas_codigo_articulo"/>
                      <span class="input-group-btn">
                        <button tabindex="-1" id="reglas_ofertas_buscar_articulo" class="btn btn-default" type="button"><i class="fa fa-search"></i></button>
                      </span>
                    </div>
                  </div>
                  <div class="col-sm-6 p0">
                    <label class="text-muted">Descripci&oacute;n</label>
                    <input disabled type="text" class="form-control action no-model" id="reglas_ofertas_item_nombre"/>
                  </div>
                </div>
                <div class="col-md-2 col-xs-12 p0">
                  <label class="text-muted">Grupo</label>
                  <input type="text" class="form-control action no-model" id="reglas_ofertas_item_orden"/>
                </div>
                <div class="col-md-3 col-xs-12 p0">
                  <label class="text-muted">Cant. Minima</label>
                  <div class="input-group">
                    <input type="text" class="form-control no-model" id="reglas_ofertas_cantidad_minima"/>
                    <span class="input-group-btn">
                      <button title="Ingresar linea" id="reglas_ofertas_agregar_item" class="btn btn-info form-control"><i class="fa fa-plus"></i></button>
                    </span>
                  </div>
                </div>
              </div>
              <div class="b-a" style="overflow: auto; margin-top: 15px;">
                <table id="reglas_ofertas_tabla_items" class="table table-small sortable m-b-none default footable">
                  <thead class="bg-light">
                    <tr>
                      <th>C&oacute;digo</th>
                      <th>Descripci&oacute;n</th>
                      <th class="w75">Grupo</th>
                      <th class="w75">Cant.</th>
                      <th class="w100"></th>
                    </tr>
                  </thead>
                  <tbody></tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        <% if (control.check("almacenes")>0 && window.almacenes.length > 0) { %>
          <div class="panel panel-default">
            <div class="panel-body">
              <div class="padder">
                <div class="form-group mb0 clearfix">
                  <label class="control-label">Sucursales</label>
                  <a class="expand-link fr">
                    <?php echo lang(array(
                      "es"=>"+ Ver opciones",
                      "en"=>"+ View options",
                    )); ?>
                  </a>
                  <div class="panel-description">Seleccione las sucursales que ser&aacute; v&aacute;lida la oferta.</div>
                </div>
              </div>
            </div>
            <div class="panel-body expand">
              <div class="padder">
                <div id="reglas_ofertas_sucursales" class="row">
                  <div class="col-xs-12">
                    <div class="row">
                      <div class="col-xs-6 col-md-4">
                        <label>Sucursal</label>
                      </div>
                      <div class="col-xs-6 col-md-2">
                        <label>Valor total</label>
                      </div>
                      <div class="col-xs-6 col-md-2">
                        <label>Descuento $</label>
                      </div>
                      <div class="col-xs-6 col-md-2">
                        <label>Oferta</label>
                      </div>
                    </div>
                  </div>
                  <% for(var i=0;i< window.almacenes.length; i++) { %>
                    <% var alm = window.almacenes[i] %>
                    <div class="col-xs-12">
                      <div class="row row_sucursal">
                        <div class="col-xs-6 col-md-4">
                          <div class="checkbox">
                            <label class="i-checks">
                              <% var encontro = false %>
                              <% var desc = 0 %>
                              <% var precio_total = 0 %>
                              <% for(var j=0;j< sucursales.length; j++) { %>
                                <% var suc = sucursales[j] %>
                                <% if (suc.id_sucursal == alm.id) { %>
                                  <% desc = suc.descuento_fijo %>
                                  <% precio_total = suc.precio_total %>
                                  <% encontro = true %>
                                <% } %>
                              <% } %>
                              <input class="check_sucursal" value="<%= alm.id %>" type="checkbox" <%= (encontro || (typeof id == "undefined")) ? "checked" : "" %>><i></i> 
                              <%= alm.nombre %>
                            </label>
                          </div>
                        </div>
                        <div class="col-xs-6 col-md-2">
                          <input type="text" class="form-control no-model precio_total" disabled value="<%= Number(precio_total).toFixed(2) %>" />
                        </div>
                        <div class="col-xs-6 col-md-2">
                          <input type="text" class="form-control no-model descuento_sucursal" value="<%= Number(desc).toFixed(2) %>" />
                        </div>
                        <div class="col-xs-6 col-md-2">
                          <input type="text" class="form-control no-model oferta_total" value="<%= Number(precio_total - desc).toFixed(2) %>" />
                        </div>
                      </div>
                    </div>
                  <% } %>
                </div>
              </div>
            </div>
          </div>
        <% } %>


        <% if (edicion) { %>
          <div class="tar">
            <button class="btn guardar btn-success">Guardar</button>
          </div>
        <% } %>
      </div>
    </div>
  </div>
</div>
</script>

<script type="text/template" id="reglas_ofertas_sin_articulos_edit_panel_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <h1 class="m-n font-thin h3"><i class="fa fa-tags icono_principal"></i>Productos 
    / Ofertas
    / <b><%= (id == undefined) ? 'Nueva' : nombre %></b>
  </h1>
</div>
<div class="wrapper-md ng-scope">
  <div class="centrado rform">
    <div class="row">
      <div class="col-md-10 col-md-offset-1">
        <div class="panel panel-default">
          <div class="panel-body">
            <div class="padder">
              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">Nombre</label>
                    <input type="text" name="nombre" class="form-control" id="reglas_ofertas_sin_articulos_nombre" value="<%= nombre %>" <%= (!edicion)?"disabled":"" %>/>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label"><%= (ID_EMPRESA == 571)?"Comercios":"Usuarios" %></label>
                    <select class="form-control" name="id_usuario" id="reglas_ofertas_sin_articulos_usuarios">
                      <option <%= (id_usuario == 0)?"selected":"" %> value="0">Todos</option>
                      <% for(var i=0;i< window.usuarios.models.length;i++) { %>
                        <% var o = window.usuarios.models[i]; %>
                        <option <%= (id_usuario == o.id)?"selected":"" %> value="<%= o.id %>"><%= o.get("nombre") %></option>
                      <% } %>
                    </select>
                  </div>
                </div>    
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">Etiquetas</label>
                    <select class="form-control" name="id_etiqueta" id="reglas_ofertas_sin_articulos_etiquetas">
                      <option <%= (id_etiqueta == 0)?"selected":"" %> value="0">-</option>
                      <% for(var i=0;i< window.articulos_etiquetas.length;i++) { %>
                        <% var o = window.articulos_etiquetas[i]; %>
                        <option <%= (id_etiqueta == o.id)?"selected":"" %> value="<%= o.id %>"><%= o.nombre %></option>
                      <% } %>
                    </select>
                  </div>
                </div>  
              </div>
              <div class="row">
                <div class="col-md-3">
                  <div class="form-group">
                    <label class="control-label">Valido Desde</label>
                    <div class="input-group">
                      <input type="text" class="form-control" id="reglas_ofertas_sin_articulos_desde" value="<%= desde %>" name="desde">
                      <span class="input-group-btn">
                        <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                      </span>
                    </div>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label class="control-label">Hasta</label>
                    <div class="input-group">
                      <input type="text" class="form-control" id="reglas_ofertas_sin_articulos_hasta" value="<%= hasta %>" name="hasta">
                      <span class="input-group-btn">
                        <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                      </span>
                    </div>
                  </div>
                </div>

                <div class="col-md-3">
                  <div class="form-group">
                    <label class="control-label">Cant. Minima Arts.</label>
                    <input type="text" class="form-control" name="cantidad_minima" value="<%= cantidad_minima %>">
                  </div>
                </div>

                <div class="col-md-3">
                  <div class="form-group">
                    <label class="control-label">Total Minimo ($)</label>
                    <input type="text" class="form-control" name="cantidad_minima_pesos" value="<%= cantidad_minima_pesos %>">
                  </div>
                </div>

              </div>

              <div class="form-group">
                <label class="i-checks m-r">
                  <input type="checkbox" id="reglas_ofertas_sin_articulos_lunes" value="L"><i></i>
                  Lunes
                </label>
                <label class="i-checks m-r">
                  <input type="checkbox" id="reglas_ofertas_sin_articulos_martes" value="M"><i></i>
                  Martes
                </label>
                <label class="i-checks m-r">
                  <input type="checkbox" id="reglas_ofertas_sin_articulos_miercoles" value="X"><i></i>
                  Miercoles
                </label>
                <label class="i-checks m-r">
                  <input type="checkbox" id="reglas_ofertas_sin_articulos_jueves" value="J"><i></i>
                  Jueves
                </label>
                <label class="i-checks m-r">
                  <input type="checkbox" id="reglas_ofertas_sin_articulos_viernes" value="V"><i></i>
                  Viernes
                </label>
                <label class="i-checks m-r">
                  <input type="checkbox" id="reglas_ofertas_sin_articulos_sabado" value="S"><i></i>
                  Sabado
                </label>
                <label class="i-checks m-r">
                  <input type="checkbox" id="reglas_ofertas_sin_articulos_domingo" value="D"><i></i>
                  Domingo
                </label>
              </div>

              <div class="row">
                <div class="col-md-3">
                  <div class="form-group">
                    <label class="control-label">Hora Inicio 1</label>
                    <input type="text" id="reglas_ofertas_sin_articulos_hora_desde_1" name="hora_desde_1" class="form-control" value="<%= hora_desde_1 %>"/>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label class="control-label">Hora Fin 1</label>
                    <input type="text" id="reglas_ofertas_sin_articulos_hora_hasta_1" name="hora_hasta_1" class="form-control" value="<%= hora_hasta_1 %>"/>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label class="control-label">Hora Inicio 2</label>
                    <input type="text" id="reglas_ofertas_sin_articulos_hora_desde_2" name="hora_desde_2" class="form-control" value="<%= hora_desde_2 %>"/>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label class="control-label">Hora Fin 2</label>
                    <input type="text" id="reglas_ofertas_sin_articulos_hora_hasta_2" name="hora_hasta_2" class="form-control" value="<%= hora_hasta_2 %>"/>
                  </div>
                </div>
              </div>       

              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">Codigo Promocional</label>
                    <input type="text" id="reglas_ofertas_sin_articulos_codigo_especial" name="codigo_especial" class="form-control" value="<%= codigo_especial %>"/>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">Maximo (0 = Sin Limite)</label>
                    <input type="text" id="reglas_ofertas_sin_articulos_codigo_limite_maximo" name="codigo_limite_maximo" class="form-control" value="<%= codigo_limite_maximo %>"/>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">Veces utilizado</label>
                    <input type="text" disabled id="reglas_ofertas_sin_articulos_codigo_cantidad_veces" name="codigo_cantidad_veces" class="form-control" value="<%= codigo_cantidad_veces %>"/>
                  </div>
                </div>
              </div>                     

              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">Accion</label>
                    <select class="form-control" name="accion" id="reglas_ofertas_sin_articulos_accion">
                      <option <%= (accion=="total")?"selected":"" %> value="total">Descuento en Total</option>
                      <option <%= (accion=="costo_envio")?"selected":"" %> value="costo_envio">Descuento en Costo de Envio</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">Descuento (%)</label>
                    <input type="text" class="form-control" name="descuento_porcentaje" value="<%= descuento_porcentaje %>">
                  </div>
                </div>                
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">Descuento ($)</label>
                    <input type="text" class="form-control" name="descuento_fijo" value="<%= descuento_fijo %>">
                  </div>
                </div>                
              </div>


            </div>
          </div>
        </div>


        <div class="panel panel-default">
          <div class="panel-body">
            <div class="padder">
              <div class="clearfix">
                <div class="col-md-5 col-xs-12 p0">
                  <div class="col-sm-6 p0">
                    <label class="text-muted">C&oacute;digo</label>
                    <div class="input-group">
                      <input type="text" class="form-control action no-model" id="reglas_ofertas_sin_articulos_item_codigo"/>
                      <span class="input-group-btn">
                        <button tabindex="-1" id="reglas_ofertas_sin_articulo_buscar_articulo" class="btn btn-default" type="button"><i class="fa fa-search"></i></button>
                      </span>
                    </div>
                  </div>
                  <div class="col-sm-6 p0">
                    <label class="text-muted">Descripci&oacute;n</label>
                    <input disabled type="text" class="form-control action no-model" id="reglas_ofertas_sin_articulos_item_nombre"/>
                  </div>
                </div>
                <div class="col-md-2 col-xs-12 p0">
                  <label class="text-muted">Grupo</label>
                  <input type="text" class="form-control action no-model" id="reglas_ofertas_sin_articulos_item_orden"/>
                </div>
                <div class="col-md-2 col-xs-12 p0">
                  <label class="text-muted">Cant. Minima</label>
                  <input type="text" class="form-control action no-model" id="reglas_ofertas_sin_articulos_item_cantidad_minima"/>
                </div>                
                <div class="col-md-2 col-xs-12 p0">
                  <label class="text-muted">Descuento</label>
                  <div class="input-group">
                    <input type="text" class="form-control no-model" id="reglas_ofertas_sin_articulos_item_descuento"/>
                    <span class="input-group-btn">
                      <button title="Ingresar linea" id="reglas_ofertas_sin_articulo_agregar_item" class="btn btn-info form-control"><i class="fa fa-plus"></i></button>
                    </span>
                  </div>
                </div>
              </div>
              <div class="b-a" style="overflow: auto; margin-top: 15px;">
                <table id="reglas_ofertas_sin_articulos_tabla_items" class="table table-small sortable m-b-none default footable">
                  <thead class="bg-light">
                    <tr>
                      <th>C&oacute;digo</th>
                      <th>Descripci&oacute;n</th>
                      <th class="w75">Grupo</th>
                      <th class="w75">Cant.</th>
                      <th class="w75">Desc.</th>
                      <th class="w100"></th>
                    </tr>
                  </thead>
                  <tbody></tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        <% if (edicion) { %>
          <div class="tar">
            <button class="btn guardar btn-success">Guardar</button>
          </div>
        <% } %>
      </div>
    </div>
  </div>
</div>
</script>