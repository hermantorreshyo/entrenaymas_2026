<script type="text/template" id="stock_por_sucursal_resultados_template">
  <div>
    <?php include("art/articulos_header.php") ?>
    <div class="wrapper-md ng-scope">
      <div class="panel panel-default">
        <?php $active = "stock_por_sucursal"; include("art/articulos_menu.php"); ?>
        <div class="panel-heading clearfix">
          <div class="row">
            <div class="col-md-8 sm-m-b">
              <div class="input-group">
                <input type="text" id="stock_por_sucursal_buscar" value="<%= window.stock_por_sucursal_filter %>" placeholder="<?php echo lang(array("es"=>"Buscar","en"=>"Search")); ?>..." autocomplete="off" class="form-control">
                <span class="input-group-btn">
                  <button class="btn btn-default"><i class="fa fa-search"></i></button>
                </span>
                <span class="input-group-btn">
                  <button class="btn btn-default advanced-search-btn btn-addon btn-addon-2 ml5"><i class="fa fa-filter"></i><span><?php echo lang(array("es"=>"Filtros","en"=>"Filters")); ?></span></button>
                </span>
                <span class="input-group-btn">
                  <div class="btn-group dropdown ml5">
                    <button class="btn btn-default btn-addon btn-addon-2 dropdown-toggle" data-toggle="dropdown">
                      <i class="fa fa-cog"></i><span>Operaciones</span>
                    </button>
                    <ul class="dropdown-menu pull-right">
                      <li><a href="javascript:void" class="exportar">Exportar Excel</a></li>
                    </ul>
                  </div>
                </span>
              </div>
            </div>
          </div>
        </div>
        <div class="advanced-search-div bg-light dk" style="<%= (window.stock_por_sucursal_id_marca != 0 || window.stock_por_sucursal_id_rubro != 0 || window.stock_por_sucursal_fecha != '' || window.stock_por_sucursal_id_departamento != 0 || window.stock_por_sucursal_id_proveedor != 0 || window.stock_por_sucursal_activo != -1 || window.stock_por_sucursal_imagen != -1 || window.stock_por_sucursal_destacado != -1 || window.stock_por_sucursal_con_descuento != -1) ? "display:block" : "display:none" %>">
          <div class="wrapper oh">
            <h4 class="m-t-xs m-b"><i class="fa fa-filter"></i> <?php echo lang(array("es"=>"Filtros:","en"=>"Filters:")); ?></h4>
            <div class="row pl10 pr10">
              <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
                <div class="form-group">
                  <select id="stock_por_sucursal_buscar_categorias" class="w100p form-control no-model">
                    <option selected value="0"><?php echo lang(array("es"=>"Rubro","en"=>"Category")); ?></option>
                    <%= workspace.crear_select(rubros,"",window.stock_por_sucursal_id_rubro) %>
                  </select>
                </div>
              </div>
              <% if (control.check("marcas")>0) { %>
                <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
                  <div class="form-group">
                    <select id="stock_por_sucursal_buscar_marcas" class="w100p no-model">
                      <option selected value="0"></option>
                    </select>
                  </div>
                </div>
              <% } %>
              <% if (control.check("stock")>0) { %>
                <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
                  <div class="form-group">
                    <select id="stock_por_sucursal_filtro_stock" class="form-control no-model">
                      <option selected value="">Stock</option>
                      <option value="sin_stock">Productos sin stock</option>
                      <option value="con_stock">Productos con stock</option>
                    </select>
                  </div>
                </div>
              <% } %>
              <% if (control.check("proveedores")>0) { %>
                <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
                  <div class="form-group">
                    <select id="stock_por_sucursal_buscar_proveedores"></select>
                  </div>
                </div>
              <% } %>
              <% if (control.check("departamentos_comerciales")>0) { %>
                <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
                  <div class="form-group">
                    <select id="stock_por_sucursal_buscar_departamentos_comerciales" class="w100p no-model">
                      <option selected value="0"></option>
                    </select>
                  </div>
                </div>
              <% } %>
              <% if (ID_PROYECTO == 1) { %>
                <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
                  <div class="form-group">
                    <div class="input-group">
                      <input type="text" placeholder="Fecha Modif." class="input form-control no-model" id="stock_por_sucursal_fecha"/>
                      <span class="input-group-btn">
                        <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                      </span>
                    </div>
                  </div>
                </div>
              <% } %>
              <% if (MILLING == 0) { %>
                <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
                  <div class="form-group">
                    <select id="stock_por_sucursal_buscar_activo" class="w100p form-control no-model">
                      <option <%= (window.stock_por_sucursal_activo == -1)?"selected":"" %> value="-1">Activo/Inactivo</option>
                      <option <%= (window.stock_por_sucursal_activo == 1)?"selected":"" %> value="1">S&oacute;lo activos</option>
                      <option <%= (window.stock_por_sucursal_activo == 0)?"selected":"" %> value="0">S&oacute;lo inactivos</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
                  <div class="form-group">
                    <select id="stock_por_sucursal_buscar_destacado" class="w100p form-control no-model">
                      <option <%= (window.stock_por_sucursal_destacado == -1)?"selected":"" %> value="-1">Destacado</option>
                      <option <%= (window.stock_por_sucursal_destacado == 1)?"selected":"" %> value="1">S&oacute;lo destacados</option>
                      <option <%= (window.stock_por_sucursal_destacado == 0)?"selected":"" %> value="0">S&oacute;lo no destacados</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
                  <div class="form-group">
                    <select id="stock_por_sucursal_con_descuento" class="w100p form-control no-model">
                      <option <%= (window.stock_por_sucursal_con_descuento == -1)?"selected":"" %> value="-1">Descuento</option>
                      <option <%= (window.stock_por_sucursal_con_descuento == 1)?"selected":"" %> value="1">S&oacute;lo con descuento</option>
                      <option <%= (window.stock_por_sucursal_con_descuento == 0)?"selected":"" %> value="0">Sin descuento</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
                  <div class="form-group">
                    <select id="stock_por_sucursal_buscar_imagen" class="w100p form-control no-model">
                      <option <%= (window.stock_por_sucursal_imagen == -1)?"selected":"" %> value="-1">Imagen</option>
                      <option <%= (window.stock_por_sucursal_imagen == 1)?"selected":"" %> value="1">S&oacute;lo con imagen</option>
                      <option <%= (window.stock_por_sucursal_imagen == 0)?"selected":"" %> value="0">Sin imagen</option>
                    </select>
                  </div>
                </div>
              <% } %>
              <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
                <div class="form-group">
                  <button id="stock_por_sucursal_buscar_avanzada_btn" class="btn btn-default btn-dark btn-block"><i class="fa fa-search m-r-xs"></i> <?php echo lang(array("es"=>"Buscar","en"=>"Search")); ?></button>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <div class="panel-body">
          <div class="b-a table-responsive">
            <table id="stock_por_sucursal_tabla" class="table table-small table-striped sortable m-b-none default footable">
              <thead>
                <tr>
                  <th class="w120 sorting" data-sort-by="codigo">Codigo</th>
                  <th class="w120">EAN</th>
                  <th class="w120">Prov.</th>
                  <th class="sorting" data-sort-by="nombre">Descripcion</th>
                  <% for(var k=0;k< window.almacenes.length;k++) { %>
                    <% var alm = window.almacenes[k] %>
                    <% var mostrar = false %>
                    <% if (sucursales_usuario.length > 0) { %>
                      <% for(var kk=0;kk< sucursales_usuario.length; kk++) { %>
                        <% var skk = sucursales_usuario[kk] %>
                        <% if (skk.id_sucursal == alm.id) { %>
                          <% mostrar = true %>
                        <% } %>  
                      <% } %>
                    <% } else { %>
                      <% mostrar = true %>
                    <% } %>
                    <% if (mostrar) { %>
                      <th class="w80"><%= alm.nombre %></th>
                    <% } %>
                  <% } %>
                </tr>
              </thead>
              <tbody class="tbody"></tbody>
              <tfoot class="pagination_container hide-if-no-paging"></tfoot>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
<?php /* <% } %> */ ?>
</script>

<script type="text/template" id="stock_por_sucursal_item_resultados_template">
  <% var clase = (lista_precios==0)?"text-muted":"text-info"; %>
  <% var cant_dec = (ID_EMPRESA == 342) ? 0 : 2 %>
  <td><%= codigo %></td>
  <td><%= codigo_barra.replace(/\#\#\#/g,"<br/>") %></td>
  <td>
    <% for(var cp = 0;cp < proveedores.length;cp++) { %>
      <% var codigo_prov = proveedores[cp] %>
      <%= codigo_prov.codigo %><br/>
    <% } %>
  </td>
  <td><span class="<%= clase %>"><%= nombre %></span></td>
  <% for(var k=0;k< stock_almacenes.length; k++) { %>
    <% var salm = stock_almacenes[k] %>
    <% var mostrar = false %>
    <% if (sucursales_usuario.length > 0) { %>
      <% for(var kk=0;kk< sucursales_usuario.length; kk++) { %>
        <% var skk = sucursales_usuario[kk] %>
        <% if (skk.id_sucursal == salm.id_sucursal) { %>
          <% mostrar = true %>
        <% } %>  
      <% } %>
    <% } else { %>
      <% mostrar = true %>
    <% } %>
    <% if (mostrar) { %>
      <td>
        <span class="tag_precio w90 tac dib <%= (salm.stock_actual <= 0)?"bg-danger":"" %>">
          <% if (salm.stock_actual == 0) { %>
            Sin Stock
          <% } else { %>
            <%= Number(salm.stock_actual).format(cant_dec) %>
          <% } %>
          <% if (salm.reservado > 0) { %>
            (<%= Number(salm.reservado).format(cant_dec) %>)
          <% } %>
        </span>
      </td>
    <% } %>
  <% } %>
</script>