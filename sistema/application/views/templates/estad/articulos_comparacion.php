<script type="text/template" id="articulos_comparacion_template">
<div class="col">
  <div class="bg-light lter b-b wrapper-md">
    <div class="row">
      <div class="col-lg-6 col-sm-4 col-xs-12">
        <h1 class="m-n font-thin h3 text-black">
          <i class="fa fa-bar-chart icono_principal"></i>Estad&iacute;sticas
          / <b>Comparaci&oacute;n de Ventas</b>
        </h1>
      </div>
    </div>
  </div>
  <div class="wrapper-md">
    <div class="row rform">
      <div class="col-sm-6 col-md-3">

        <div class="panel panel-default mb10">
          <div class="panel-body pb0">
            <div class="form-group">
              <label class="control-label">Mostrar</label>
              <select id="articulos_comparacion_agrupado_por" class="form-control">
                <option value="A">Articulos</option>
                <option value="R">Rubros</option>
                <option value="C">Clientes</option>
                <option value="V">Vendedores</option>
              </select>
            </div>
          </div>
        </div>

        <div class="panel panel-default mb10">
          <div class="panel-body pb0">
            <div class="form-group">
              <label class="control-label">Periodos</label>
              <div class="">
                <div class="col-xs-6 p0">
                  <div class="form-group">
                    <div class="input-group">
                      <input placeholder="Desde" type="text" id="articulos_comparacion_desde" class="form-control no-model">
                      <span class="input-group-btn">
                        <button tabindex="-1" type="button" style="padding-left: 0px !important; padding-right: 0px !important" class="btn w30 tac btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                      </span>
                    </div>
                  </div>
                </div>
                <div class="col-xs-6 p0">
                  <div class="form-group">
                    <div class="input-group">
                      <input placeholder="Hasta" type="text" id="articulos_comparacion_hasta" class="form-control no-model">
                      <span class="input-group-btn">
                        <button tabindex="-1" type="button" style="padding-left: 0px !important; padding-right: 0px !important" class="btn w30 tac btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                      </span>
                    </div>
                  </div>
                </div>

                <div class="col-xs-6 p0">
                  <div class="form-group">
                    <div class="input-group">
                      <input placeholder="Desde" type="text" id="articulos_comparacion_desde_2" class="form-control no-model">
                      <span class="input-group-btn">
                        <button tabindex="-1" type="button" style="padding-left: 0px !important; padding-right: 0px !important" class="btn w30 tac btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                      </span>
                    </div>
                  </div>
                </div>
                <div class="col-xs-6 p0">
                  <div class="form-group">
                    <div class="input-group">
                      <input placeholder="Hasta" type="text" id="articulos_comparacion_hasta_2" class="form-control no-model">
                      <span class="input-group-btn">
                        <button tabindex="-1" type="button" style="padding-left: 0px !important; padding-right: 0px !important" class="btn w30 tac btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                      </span>
                    </div>
                  </div>
                </div>

              </div>              
            </div>
          </div>
        </div>        

        <div class="panel panel-default mb10">
          <div class="panel-body pb0">
            <div class="">
              <div id="articulos_comparacion_ver_filtros_link" class="oh cp mb5">
                <label class="control-label">Filtros</label>
                <span class="link fr">Ver filtros</span>
              </div>

              <div id="articulos_comparacion_ver_filtros" style="display:none">

                <% if (ID_SUCURSAL > 0) { %>
                  <div class="form-group <%= (almacenes.length <= 1)?"dn":"" %>">
                    <select id="articulos_comparacion_sucursales" class="form-control no-model">
                      <% for(var i=0; i< almacenes.length; i++) { %>
                        <% var alm = almacenes[i] %>
                        <% if (alm.id == ID_SUCURSAL) { %>
                          <option value="<%= alm.id %>"><%= alm.nombre %></option>
                        <% } %>
                      <% } %>
                    </select>
                  </div>
                <% } else { %>
                  <div class="form-group <%= (almacenes.length <= 1 || ID_EMPRESA == 229)?"dn":"" %>">
                    <select id="articulos_comparacion_sucursales" class="form-control no-model">
                      <option value="0">Sucursal</option>
                      <% for(var i=0; i< almacenes.length; i++) { %>
                        <% var alm = almacenes[i] %>
                        <option value="<%= alm.id %>"><%= alm.nombre %></option>
                      <% } %>
                    </select>
                  </div>
                <% } %>

                <% if (control.check("puntos_venta")>0 || MEGASHOP == 1 || ID_EMPRESA == 421) { %>
                  <div class="form-group">
                    <select class="form-control" id="articulos_comparacion_puntos_venta">
                      <option value="0">Punto de Venta</option>
                      <% for(var i=0;i< puntos_venta.length;i++) { %>
                        <% var pv = puntos_venta[i] %>
                        <% if (ID_SUCURSAL == 0 || ID_SUCURSAL == pv.id_sucursal) { %>
                          <option value="<%= pv.id %>"><%= pv.nombre %></option>
                        <% } %>  
                      <% } %>
                    </select>
                  </div>
                <% } %>

                <% if (control.check("rubros")>0) { %>
                  <div class="form-group">
                    <select id="articulos_comparacion_rubros" class="w100p no-model"></select>
                  </div>
                <% } %>
                <% if (control.check("marcas")>0) { %>
                  <div class="form-group">
                    <select id="articulos_comparacion_marcas" class="w100p no-model"></select>
                  </div>
                <% } %>
                <div class="form-group">
                  <input type="text" id="articulos_comparacion_articulos" placeholder="Articulos..." class="form-control no-model">
                </div>
                <% if (control.check("vendedores")>0) { %>
                  <div class="form-group">
                    <select id="articulos_comparacion_vendedores" class="w100p no-model"></select>
                  </div>
                <% } %>
                <% if (control.check("clientes")>0) { %>
                  <div class="form-group">
                    <input type="text" id="articulos_comparacion_clientes" placeholder="Cliente..." class="form-control no-model">
                  </div>
                <% } %>
                <% if (control.check("proveedores")>0) { %>
                  <div class="form-group">
                    <div class="input-group">
                      <input type="text" class="dn" id="cargar_compras_id_proveedor" value=""/>
                      <input type="text" id="articulos_comparacion_proveedores" placeholder="Proveedor..." class="form-control no-model">
                      <span class="input-group-btn">
                        <button id="articulos_comparacion_buscar_proveedores" class="btn btn-default" type="button"><i class="fa fa-search"></i></button>
                      </span>
                    </div> 
                  </div>
                <% } %>
                <div class="form-group">
                  <div class="checkbox">
                    <label class="i-checks">
                      <input type="checkbox" name="filtrar_en_cero" value="1" checked id="articulos_comparacion_filtrar_en_cero">
                      <i></i>Filtrar resultados en cero.
                    </label>
                  </div>
                </div>
              </div>

            </div>
          </div>
        </div>

        <div class="form-group">
          <button class="buscar btn btn-info btn-block">Consultar</button>
        </div>

      </div>
      <div class="col-sm-6 col-md-9">

        <div class="panel panel-default">
          <div class="panel-heading font-bold">
            <span>Resultados</span>
            <div class="btn-group dropdown pull-right">
              <button class="btn btn-sm btn-default dropdown-toggle btn-addon" data-toggle="dropdown">
                <i class="fa fa-cog"></i><span>Opciones</span>
                <span class="caret"></span>
              </button>
              <ul class="dropdown-menu">
                <li><a href="javascript:void" class="exportar">Exportar Excel</a></li>
              </ul>
            </div>
          </div>
          <div class="panel-body">
            <div class="b-a" style="overflow: auto; min-height: 480px; max-height: 330px;">
              <table id="articulos_comparacion_tabla" class="table table-small footable sortable m-b-none default">
                <thead class="thead">
                  <tr>
                    <th colspan="2"></th>
                    <th class="tac th_acciones" colspan="5">Periodo 1</th>
                    <th class="tac" colspan="7">Periodo 2</th>
                  </tr>
                  <tr>
                    <th class="titulo-tabla">Codigo</th>
                    <th class="titulo-tabla">Nombre</th>
                    <th class="titulo-tabla th_acciones" class="" data-sort-by="cantidad">Cant.</th>
                    <th class="titulo-tabla th_acciones" class="" data-sort-by="devolucion">Dev.</th>
                    <th class="titulo-tabla th_acciones" class="" data-sort-by="bonificado">Bonif.</th>
                    <th class="titulo-tabla th_acciones" class="" data-sort-by="costo_final">CMV</th>
                    <th class="titulo-tabla th_acciones" class="" data-sort-by="total_final">Venta</th>
                    <th class="titulo-tabla " data-sort-by="cantidad">Cant.</th>
                    <th class="titulo-tabla w100">%</th>
                    <th class="titulo-tabla " data-sort-by="devolucion">Dev.</th>
                    <th class="titulo-tabla " data-sort-by="bonificado">Bonif.</th>
                    <th class="titulo-tabla " data-sort-by="costo_final">CMV</th>
                    <th class="titulo-tabla " data-sort-by="total_final">Venta</th>
                    <th class="titulo-tabla w100">%</th>
                  </tr>
                </thead>
                <tbody class="tbody" style="min-height: 280px"></tbody>
                <tfoot class="bg-important">
                  <tr>
                    <td></td>
                    <td class="bold">TOTALES</td>
                    <td style="border-left: solid 1px #dee5e7" id="articulos_comparacion_cantidad_1"></td>
                    <td id="articulos_comparacion_devolucion_1"></td>
                    <td id="articulos_comparacion_bonificado_1"></td>
                    <td id="articulos_comparacion_costo_final_1"></td>
                    <td class="bold" id="articulos_comparacion_total_final_1"></td>
                    <td style="border-left: solid 1px #dee5e7" id="articulos_comparacion_cantidad_2"></td>
                    <td class="tar" id="articulos_comparacion_variacion_cantidad_2"></td>
                    <td id="articulos_comparacion_devolucion_2"></td>
                    <td id="articulos_comparacion_bonificado_2"></td>
                    <td id="articulos_comparacion_costo_final_2"></td>
                    <td class="bold" id="articulos_comparacion_total_final_2"></td>
                    <td class="tar" id="articulos_comparacion_variacion_total_final_2"></td>
                  </tr>
                </tfoot>
              </table>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>
</script>

<script type="text/template" id="articulos_comparacion_item_resultados_template">
  <td><%= codigo %></td>
  <td><span class="text-info"><%= nombre_item %></span></td>
  <td style="border-left: solid 1px #dee5e7" class="tar"><%= Number(cantidad_1).toFixed(2) %></td>
  <td class="tar"><%= Number(devolucion_1).toFixed(2) %></td>
  <td class="tar"><%= Number(bonificado_1).toFixed(2) %></td>
  <td class="tar"><%= Number(costo_final_1).toFixed(2) %></td>
  <td class="tar"><%= Number(total_final_1).toFixed(2) %></td>
  <td style="border-left: solid 1px #dee5e7" class="tar"><%= Number(cantidad_2).toFixed(2) %></td>
  <td class="tar"><span class="<%= (variacion_cantidad_2 > 0)?'text-success':(variacion_cantidad_2 < 0 ? 'text-danger' : '') %>"><%= Number(variacion_cantidad_2).toFixed(2) %>%</span></td>
  <td class="tar"><%= Number(devolucion_2).toFixed(2) %></td>
  <td class="tar"><%= Number(bonificado_2).toFixed(2) %></td>
  <td class="tar"><%= Number(costo_final_2).toFixed(2) %></td>
  <td class="tar"><%= Number(total_final_2).toFixed(2) %></td>
  <td class="tar"><span class="<%= (variacion_total_final_2 > 0)?'text-success':(variacion_total_final_2 < 0 ? 'text-danger' : '') %>"><%= Number(variacion_total_final_2).toFixed(2) %>%</span></td>
</script>