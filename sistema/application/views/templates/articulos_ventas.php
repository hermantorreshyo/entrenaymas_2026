<script type="text/template" id="articulos_ventas_template">
<div class="col">
  <div class="bg-light lter b-b wrapper-md">
    <div class="row">
      <div class="col-lg-6 col-sm-4 col-xs-12">
        <h1 class="m-n font-thin h3 text-black">
          <i class="fa fa-bar-chart icono_principal"></i>Estad&iacute;sticas
          / <b>Articulos vendidos</b>
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
              <select id="articulos_ventas_agrupado_por" class="form-control">
                <option value="A">Articulos</option>
                <option value="D">Departamentos</option>
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
              <label class="control-label">Periodo</label>
              <div class="">
                <div class="col-xs-6 p0">
                  <div class="form-group">
                    <div class="input-group">
                      <input placeholder="Desde" type="text" id="articulos_ventas_desde" class="form-control no-model">
                      <span class="input-group-btn">
                        <button tabindex="-1" type="button" style="padding-left: 0px !important; padding-right: 0px !important" class="btn w30 tac btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                      </span>
                    </div>
                  </div>
                </div>
                <div class="col-xs-6 p0">
                  <div class="form-group">
                    <div class="input-group">
                      <input placeholder="Hasta" type="text" id="articulos_ventas_hasta" class="form-control no-model">
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
              <div id="articulos_ventas_ver_filtros_link" class="oh cp mb5">
                <label class="control-label">Filtros</label>
                <span class="link fr">Ver filtros</span>
              </div>

              <div id="articulos_ventas_ver_filtros" style="display:none">

                <% if (ID_SUCURSAL > 0) { %>
                  <div class="form-group <%= (almacenes.length <= 1)?"dn":"" %>">
                    <select id="articulos_ventas_sucursales" class="form-control no-model">
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
                    <select id="articulos_ventas_sucursales" class="form-control no-model">
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
                    <select class="form-control" id="articulos_ventas_puntos_venta">
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
                
                <% if (control.check("departamentos_comerciales")>0) { %>
                  <div class="form-group">
                    <select id="articulos_ventas_departamentos_comerciales" class="w100p no-model"></select>
                  </div>
                <% } %>

                <% if (control.check("repartos")>0) { %>
                  <div class="form-group">
                    <input type="number" placeholder="Reparto" id="articulos_ventas_repartos" class="form-control no-model"/>
                  </div>
                <% } %>

                <% if (control.check("rubros")>0) { %>
                  <div class="form-group">
                    <select id="articulos_ventas_rubros" class="w100p no-model"></select>
                  </div>
                <% } %>
                <% if (control.check("marcas")>0) { %>
                  <div class="form-group">
                    <select id="articulos_ventas_marcas" class="w100p no-model"></select>
                  </div>
                <% } %>
                <div class="form-group">
                  <input type="text" id="articulos_ventas_articulos" placeholder="Articulos..." class="form-control no-model">
                </div>
                <% if (control.check("vendedores")>0) { %>
                  <div class="form-group">
                    <select id="articulos_ventas_vendedores" class="w100p no-model"></select>
                  </div>
                <% } %>
                <% if (control.check("clientes")>0) { %>
                  <div class="form-group">
                    <input type="text" id="articulos_ventas_clientes" placeholder="Cliente..." class="form-control no-model">
                  </div>
                <% } %>
                <% if (control.check("proveedores")>0) { %>
                  <div class="form-group">
                    <div class="input-group">
                      <input type="text" class="dn" id="cargar_compras_id_proveedor" value=""/>
                      <input type="text" id="articulos_ventas_proveedores" placeholder="Proveedor..." class="form-control no-model">
                      <span class="input-group-btn">
                        <button id="articulos_ventas_buscar_proveedores" class="btn btn-default" type="button"><i class="fa fa-search"></i></button>
                      </span>
                    </div> 
                  </div>
                <% } %>
                <div class="form-group">
                  <div class="checkbox">
                    <label class="i-checks">
                      <input type="checkbox" name="en_oferta" value="1" id="articulos_ventas_en_oferta">
                      <i></i>En oferta
                    </label>
                  </div>
                  <div class="checkbox">
                    <label class="i-checks">
                      <input type="checkbox" name="incluir_stock" value="1" id="articulos_ventas_incluir_stock">
                      <i></i>Incluir stock actual
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

        <div class="row">
          <div class="col-md-3">
            <div class="panel padder-v item tac" style="height: 117px">
              <div id="articulos_ventas_cmv" class="font-thin fs26 m-t-sm">$ 0.00</div>
              <span class="text-muted text-md pt10 db">Costo mercaderia vendida</span>
            </div>
          </div>
          <div class="col-md-3">
            <div class="block panel padder-v item tac" style="height: 117px">
              <div id="articulos_ventas_ganancia" class="font-thin fs26 m-t-sm">$ 0.00</div>
              <span class="text-muted text-md pt10 db">Ganancia bruta</span>
            </div>
          </div>
          <div class="col-md-3">
            <div class="block panel padder-v item tac" style="height: 117px">
              <span id="articulos_ventas_marcacion" class="font-thin fs26 block m-t-sm">0.00</span>
              <span class="text-muted text-md pt10 db">% de marcaci&oacute;n promedio</span>
            </div>
          </div>
          <div class="col-md-3">
            <div class="panel padder-v item tac bg-success" style="height: 117px">
              <div id="articulos_ventas_total_vendido" class="fs26 m-t-sm">$ 0.00</div>
              <span class="text-muted text-md pt10 db">Total vendido</span>
            </div>
          </div>
        </div>

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
            <div class="b-a" style="overflow: auto; min-height: 330px; max-height: 330px;">
              <table id="articulos_ventas_tabla" class="table table-small footable sortable m-b-none default">
                <thead class="thead">
                  <tr>
                    <th>Codigo</th>
                    <th>EAN</th>
                    <th>Prov.</th>
                    <th class="sorting" data-sort-by="A.nombre">Nombre</th>
                    <th class="sorting" data-sort-by="cantidad">Cant.</th>
                    <th class="sorting" data-sort-by="devolucion">Dev.</th>
                    <th class="sorting" data-sort-by="bonificado">Bonif.</th>
                    <th class="sorting" data-sort-by="costo_final">CMV</th>
                    <th class="sorting" data-sort-by="total_final">Venta</th>
                    <th>Ganancia</th>
                    <th>Prov.</th>
                    <th style="display:none" class="mostrar_si_stock">Stock</th>
                    <th style="display:none" class="mostrar_si_stock">Dias</th>
                  </tr>
                </thead>
                <tbody class="tbody" style="min-height: 280px"></tbody>
                <tfoot class="pagination_container hide-if-no-paging"></tfoot>
              </table>
            </div>
          </div>
        </div>

        <div class="panel panel-default">
          <div class="panel-body">
            <span class="text-muted text-md">Total de unidades: </span>
            <span id="articulos_ventas_cantidad_total" class="text-md m-l font-bold">0.00</span>

            <span class="text-muted text-md m-l-lg">Total bonificaciones: </span>
            <span id="articulos_ventas_total_bonificado" class="text-md m-l font-bold">0.00</span>

            <span class="text-muted text-md m-l-lg">Cantidad de bonificaciones: </span>
            <span id="articulos_ventas_bonificado" class="text-md m-l font-bold">0.00</span>

          </div>
        </div>

      </div>
    </div>
  </div>
</div>
</script>

<script type="text/template" id="articulos_ventas_item_resultados_template">
  <% var decimal_stock = (MEGASHOP == 1)?0:2 %>
  <td><%= codigo %></td>
  <td><%= codigo_barra.replaceAll("###","<br/>") %></td>
  <td><%= codigo_prov %></td>
  <td><span class="<%= (!isEmpty(custom_2) ? "text-danger":((activo==1)?"text-info":"text-muted")) %>"><%= nombre %> <%= (!isEmpty(custom_2) ? "(*)":"") %></span></td>
  <td class="tar"><%= Number(cantidad).toFixed(decimal_stock) %></td>
  <td class="tar"><%= Number(devolucion).toFixed(decimal_stock) %></td>
  <td class="tar"><%= Number(bonificado).toFixed(decimal_stock) %></td>
  <td class="tar"><%= Number(costo_final).toFixed(2) %></td>
  <td class="tar"><%= Number(total_final).toFixed(2) %></td>
  <td class="tar"><%= Number(ganancia).toFixed(2) %></td>
  <td class=""><%= proveedor %></td>
  <td style="display:none" class="tar mostrar_si_stock"><%= Number(stock).toFixed(decimal_stock) %></td>
  <td style="display:none" class="tar mostrar_si_stock"><%= Number(dias_stock).toFixed(2) %></td>
</script>