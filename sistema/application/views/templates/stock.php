<script type="text/template" id="stocks_resultados_template">
<?php include("art/articulos_header.php") ?>
<div class="wrapper-md ng-scope">
  <div class="panel panel-default">
    <?php $active = "stock"; include("art/articulos_menu.php"); ?>
    <div class="panel-heading clearfix">
      <div class="row">
        <div class="col-md-6 col-lg-4 sm-m-b">
          <div class="input-group">
            <input type="text" id="stocks_texto" placeholder="Buscar..." autocomplete="off" class="form-control">
            <span class="input-group-btn">
              <button class="btn btn-default buscar"><i class="fa fa-search"></i></button>
            </span>
            <span class="input-group-btn">
              <button class="btn btn-default advanced-search-btn btn-addon btn-addon-2 ml5"><i class="fa fa-filter"></i><span><?php echo lang(array("es"=>"Filtros","en"=>"Filters")); ?></span></button>
            </span>
          </div>
        </div>
        <div class="col-xs-12 col-lg-8 col-md-6 sm-m-b">
          <div class="form-inline">
            <div class="input-group">
              <select id="stocks_almacenes" class="form-control w200">
                <?php //TODO: Configurar si un usuario puede ver los stocks de los demas o no ?>
                <% if (MEGASHOP == 1 || ID_EMPRESA == 224 || ID_EMPRESA == 421) { %>
                  <% if (ID_SUCURSAL != 0) { %>
                    <% for(var i=0;i< window.almacenes.length;i++) { %>
                      <% var o = almacenes[i]; %>
                      <% if (ID_SUCURSAL == o.id) { %>
                        <option selected value="<%= o.id %>"><%= o.nombre %></option>
                      <% } %>
                    <% } %>                    
                  <% } else { %>
                    <% for(var i=0;i< window.almacenes.length;i++) { %>
                      <% var o = almacenes[i]; %>
                      <option value="<%= o.id %>"><%= o.nombre %></option>
                    <% } %>
                  <% } %>
                <% } else { %>
                  <% for(var i=0;i< window.almacenes.length;i++) { %>
                    <% var o = almacenes[i]; %>
                    <option value="<%= o.id %>"><%= o.nombre %></option>
                  <% } %>
                <% } %>
              </select>          
            </div>
            <button class="btn btn-default ml5 buscar btn-addon">
              <i class="fa fa-search"></i><span class="hidden-xs">Buscar</span>
            </button>
            
            <% if (control.check("stock")>1) { %>
              <button class="btn pull-right ml5 btn-success btn-addon nuevo">
                <i class="fa fa-plus"></i><span class="hidden-xs">Movimientos</span>
              </button>
            <% } %>
            
            <div class="btn-group pull-right dropdown">
              <button class="btn btn-default dropdown-toggle btn-addon" data-toggle="dropdown">
                <i class="fa fa-cog"></i><span>Operaciones</span>
                <span class="caret"></span>
              </button>
              <ul class="dropdown-menu">
                <li><a href="javascript:void(0)" class="exportar">Exportar Excel</a></li>
                <li><a href="javascript:void(0)" class="imprimir" data-tipo="4">Imprimir Stock</a></li>
                <li><a href="javascript:void(0)" class="imprimir_mov" data-tipo="1">Imprimir Ingresos</a></li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="advanced-search-div bg-light dk" style="<%= articulos.hasAdvancedFilters() ? "display:block" : "display:none" %>">
      <div class="wrapper oh">
        <h4 class="m-t-xs m-b"><i class="fa fa-filter"></i> <?php echo lang(array("es"=>"Filtros:","en"=>"Filters:")); ?></h4>
        <div class="row pl10 pr10">
          <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
            <div class="form-group">
              <select id="stocks_rubros" class="w100p"></select>
            </div>
          </div>
          <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
            <div class="form-group">
              <select id="stocks_marcas" class="w100p"></select>
            </div>
          </div>
          <% if (control.check("proveedores")>0) { %>
            <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
              <div class="form-group">
                <select id="stocks_proveedores" class="w100p"></select>
              </div>
            </div>
            <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
              <div class="form-group">
                <input type="text" placeholder="Codigo Prov." class="input form-control no-model" id="stocks_buscar_codigo_prov" />
              </div>
            </div>
          <% } %>
          <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
            <div class="form-group">
              <input type="text" id="stocks_desde" placeholder="Fecha alta" class="form-control no-model">
            </div>
          </div>
          <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
            <div class="form-group">
              <select id="stocks_filtro_cantidades" class="form-control">
                <option value="0">Filtro por stock</option>
                <option value="1">Stock sobre el minimo</option>
                <option value="2">Stock debajo del minimo</option>
                <option value="6">Con Stock</option>
                <option value="3">Sin Stock</option>
                <option value="5">Negativo</option>
                <option value="4">Con reservas</option>
              </select>
            </div>
          </div>
          <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
            <div class="form-group">
              <button id="articulos_buscar_avanzada_btn" class="btn btn-default buscar btn-dark btn-block"><i class="fa fa-search m-r-xs"></i> <?php echo lang(array("es"=>"Buscar","en"=>"Search")); ?></button>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="panel-body">
      <div class="b-a table-responsive">
        <table id="stocks_tabla" class="table table-small sortable m-b-none default footable">
          <thead>
            <tr>
              <th class="w150">Sucursal</th>
              <th class="w120 sorting" data-sort-by="A.codigo">Codigo</th>
              <% if (MEGASHOP == 1 || ID_EMPRESA == 421) { %>
                <th class="w120">Prov.</th>
              <% } %>
              <th class="sorting" data-sort-by="A.nombre">Descripcion</th>
              <th class="sorting tar w120" data-sort-by="stock_actual">Unidades</th>
              <th class="tar w120">Stk. Minimo</th>
              <% if (control.check("stock")>2) { %>
                <th class="tar w120">Costo Unit.</th>
                <th class="tar w120">Valoracion</th>
                <th class="w120">Ult. Alta</th>
                <th class="w120">Ult. Baja</th>
              <% } %>
              <th class="w20 th_acciones"></th>
            </tr>
          </thead>
          <tbody class="tbody"></tbody>
          <tfoot class="pagination_container hide-if-no-paging"></tfoot>
        </table>
      </div>
    </div>
  </div>
  <% if (control.check("stock")>2 || MEGASHOP == 1 || ID_EMPRESA == 421) { %>

    <div class="clearfix m-b <%= (ID_PROYECTO == 2)?"dn":"" %>">
      <span class="text-md m-t-xs m-r fl">Valorizar stock en: </span>
      <select id="stocks_moneda" class="form-control no-model db w80 fl">
        <option>$</option>
        <option>USD</option>
      </select>
      <span class="text-md m-t-xs m-r fl ml20">Cotizacion Dolar: </span>
      <span class="text-md m-t-xs m-r bold fl">$ <%= Number(COTIZACION_DOLAR).toFixed(2) %></span>
    </div>

    <div class="row">
      <div class="col-md-4">
        <div class="block panel padder-v item tac" style="height: 140px">
          <div id="stocks_cantidad_total" class="h2 font-thin m-t-md">0.00</div>
          <span class="text-muted text-md pt10 db">Total de unidades</span>
        </div>
      </div>
      <div class="col-md-4">
        <div class="block panel padder-v item tac" style="height: 140px">
          <div id="stocks_costo_total" class="h2 font-thin m-t-md">$ 0.00</div>
          <div id="stocks_costo_total_dolares" class="h2 font-thin m-t-md dn">$ 0.00</div>
          <span class="text-muted text-md pt10 db">Costo Final Total</span>
        </div>
      </div>
      <div class="col-md-4">
        <div class="block panel padder-v item tac" style="height: 140px">
          <div id="stocks_venta_total" class="h2 font-thin m-t-md">$ 0.00</div>
          <div id="stocks_venta_total_dolares" class="h2 font-thin m-t-md dn">$ 0.00</div>
          <span class="text-muted text-md pt10 db">Valoración Venta Total</span>
        </div>
      </div>
    </div>

  <% } %>
</div>
</script>


<script type="text/template" id="stocks_item_resultados_template">
  <% var clase = "" %>
  <% if (stock_actual < stock_minimo) { var clase = "text-warning"; } %>
  <% if (stock_actual <= 0) { var clase = "text-danger"; } %>
  <td class="<%= clase %>"><%= almacen %></td>
  <td class="<%= clase %>"><%= codigo %></td>
  <% if (MEGASHOP == 1 || ID_EMPRESA == 421) { %>
    <td class="<%= clase %>"><%= custom_10 %></td>
  <% } %>
  <td>
    <span class="<%= (isEmpty(clase) ? 'text-info' : clase) %>"><%= nombre %></span>
    <% if(variantes.length>0) { %>
      <i class="fa ver_variantes fa-plus-circle pull-right mt3"></i>
    <% } %>
  </td>
  <?php /*
  <td class="<%= clase %> tar">
    <% if (uxb != 0) { %>
      <%= Number(uxb).format() %>
    <% } %>
  </td>
  <td class="<%= clase %> tar">
    <% if (uxb != 0) { %>
      <%= Number(stock_actual / uxb).format() %>
    <% } %>
  </td>
  */ ?>
  <td class="<%= clase %> tar bold">
    <%= Number(stock_actual).format() %>
    <% if (reservado != 0) { %>
      (<%= Number(reservado).format() %>)
    <% } %>
  </td>
  <td class="<%= clase %> tar">
    <%= Number(stock_minimo).format() %>
    <% if (control.check("stock")>2) { %>
      <i data-toggle="tooltip" class="fa fa-pencil editar_stock_minimo text-dark ml5" title="Modificar stock minimo" />
    <% } %>
  </td>
  <% if (control.check("stock")>2) { %>
    <td class="<%= clase %> tar"><%= (moneda == "U$S") ? "U$S"+Number(costo_final).format() : Number(costo_final).format() %></td>
    <td class="<%= clase %> tar"><%= (moneda == "U$S" && typeof COTIZACION_DOLAR != "undefined") ? Number(costo_final * stock_actual * COTIZACION_DOLAR).format() : Number(costo_final * stock_actual).format() %></td>
    <td class="<%= clase %>"><%= fecha_ult_compra %></td>
    <td class="<%= clase %>"><%= fecha_ult_venta %></td>
  <% } %>
  <td><i class="fa fa-search view text-dark"/></td>
</script>

<script type="text/template" id="stocks_item_variante_resultados_template">
  <% var clase = (Number(stock) <= 0)?"text-danger":"" %>
  <td></td>
  <td></td>
  <% if (MEGASHOP == 1 || ID_EMPRESA == 421) { %>
    <td></td>
  <% } %>
  <td><span class="<%= clase %>"><%= nombre %></span></td>
  <td class="tar <%= clase %>">
    <%= Number(stock).format() %>
    <% if (reservado != 0) { %>
      (<%= Number(reservado).format() %>)
    <% } %>
  </td>
  <td></td>
  <% if (control.check("stock")>2) { %>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  <% } %>
  <td></td>
</script>

<script type="text/template" id="stock_template">
<div class="panel panel-default">
  <div class="panel-heading clearfix">
    <span class="font-bold fl m-t-sm">Movimiento de Stock<%= (!isEmpty(titulo) ? ": "+titulo : "") %></span>
    <button class="fr cp cerrar btn btn-default">
      <i class="fa fa-times text-muted"></i>
    </button>
  </div>
  <div class="panel-body">
    <div class="clearfix m-b">
      <div class="col-md-3 col-xs-6 p0">
        <label class="text-muted">Fecha</label>
        <div class="input-group">
          <input type="text" class="form-control" id="stock_fecha"/>
          <span class="input-group-btn">
            <button class="btn btn-cal btn-default"><i class="glyphicon glyphicon-calendar"></i></button>
          </span>
        </div>
      </div>
      <div class="col-md-3 col-xs-6 p0">
        <label class="text-muted">Movimiento</label>
        <select class="form-control" id="stock_movimiento">
          <option value="A">Alta</option>
          <option value="B">Baja</option>
          <option value="M">Ajuste</option>
          <option value="R">Rotura</option>
        </select>      
      </div>
      <div class="col-md-6 col-xs-6 p0">
        <label class="text-muted">Proveedor</label>
        <select class="form-control no-model" style="width: 100%" id="stock_proveedores"></select>
      </div>
    </div>
    <div class="clearfix m-b">
      <div class="col-md-3 col-sm-4 col-xs-6 p0">
        <label class="text-muted">Producto</label>
        <div class="input-group">
          <input type="hidden" id="stock_id_articulo" value="0"/>
          <input type="text" class="form-control action" id="stock_codigo_articulo" placeholder="Codigo"/>
          <span class="input-group-btn">
            <button id="stock_buscar" class="btn btn-default"><i class="fa fa-search"></i></button>
          </span>
        </div>
      </div>
      <div class="col-md-3 col-sm-6 col-xs-6 p0">
        <label class="text-muted">&nbsp</label>
        <input type="text" class="form-control" disabled id="stock_nombre_articulo"/>
      </div>
      <div class="col-md-2 col-sm-6 col-xs-4 p0 <%= (ID_PROYECTO == 2)?"":"dn" %>">
        <label class="text-muted">Variantes</label>
        <select class="form-control no-model" id="stock_variantes" disabled></select>
      </div>
      <div class="col-md-2 col-sm-6 col-xs-4 p0 <%= (ID_PROYECTO != 2)?"":"dn" %>">
        <label class="text-muted">Medida</label>
        <select class="form-control no-model" id="stock_medida">
        <option value="U">Unidades</option>
        <option value="B">Bultos</option>
        </select>
      </div>
      <div id="stock_bultos_cont" style="display: none;" class="col-md-2 col-sm-6 col-xs-6 p0">
        <label class="text-muted">Cantidad</label>
        <input type="text" class="form-control" placeholder="Bultos" id="stock_bultos" value=""/>
      </div>
      <div style="display: none;" class="col-md-2 col-xs-4 col-sm-6 p0">
        <label class="text-muted">UxB</label>
        <input type="text" class="form-control" placeholder="UxB" id="stock_uxb" value=""/>
      </div>
      <div id="stock_unidades_cont" class="col-md-2 col-xs-4 col-sm-6 p0">
        <label class="text-muted">Cantidad</label>
        <input type="text" class="form-control" placeholder="Unidades" id="stock_cantidad" value=""/>
      </div>
      
      <div class="col-md-2 col-sm-6 col-xs-4 p0">
        <label class="text-muted">Actual</label>
        <div class="input-group">
          <input type="text" class="form-control" placeholder="Actual" id="stock_stock" disabled/>
          <span class="input-group-btn">
            <button class="btn btn-info" id="stock_agregar"><i class="fa fa-plus"></i></button>
          </span>
        </div>
      </div>
    </div>
    <div class="b-a table-responsive" style="min-height: 180px; overflow: auto;">
      <table id="stocks_stock_tabla" class="table table-small table-striped sortable m-b-none default footable">
        <thead>
          <tr>
          <th style="width:50px">Codigo</th>
          <th>Articulo</th>
          <th class="<%= (ID_PROYECTO==2)?"":"dn" %>">Variante</th>
          <th style="width:40px">Mov.</th>
          <th style="width:40px">Actual</th>
          <th style="width:40px">Cantidad</th>
          <th style="width:40px">Stock</th>
          <th class="w25"></th>
          <th class="w25"></th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
  </div>
  <div class="panel-footer clearfix tar">
    <button class="btn btn-success fr guardar">Guardar</button>
  </div>
</div>
</script>

<script type="text/template" id="stock_modificar_template">
<div class="panel panel-default">
  <div class="panel-heading">
    <b><%= nombre %></b>
  </div>
  <div class="panel-body">  
    <div class="form-group">
      <label class="control-label">Stock m&iacute;nimo</label>
      <input type="text" class="form-control" name="stock_minimo" value="<%= Number(stock_minimo).toFixed(2) %>" id="stock_modificar_minimo"/>
    </div>
  </div>
  <div class="panel-footer clearfix tar">
  <button class="btn btn-success guardar">Guardar</button>
</div>
</script>


<script type="text/template" id="stock_detalle_template">
<div class="panel panel-default">
  <div class="panel-heading clearfix">
    <b class="pull-left mt5"><%= nombre %> <%= (!isEmpty(codigo)) ? "("+codigo+")" : "" %></b>
    <button class="pull-right btn btn-default btn-small cerrar"><i class="fa fa-times"></i></button>
  </div>
  <div class="panel-body">  
    <ul class="nav nav-tabs nav-tabs-2" role="tablist">
      <li class="render_tabla <%= (tab_default == "tabla")?"active":"" %>">
        <a href="#tab_detalle_stock1" role="tab" data-toggle="tab"><i class="fa fa-list-ul text-info mr5"></i> Lista</a>
      </li>
      <li class="render_grafico <%= (tab_default == "grafico")?"active":"" %>">
        <a href="#tab_detalle_stock2" role="tab" data-toggle="tab"><i class="fa fa-signal text-warning mr5"></i> Grafico</a>
      </li>
      <div class="pull-right mr5">
        <div class="input-group pull-left" style="width: 140px;">
          <input type="text" id="evolucion_stock_fecha_desde" class="form-control">
          <span class="input-group-btn">
            <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
          </span>              
        </div>
        <div class="input-group pull-left" style="width: 140px;">
          <input type="text" id="evolucion_stock_fecha_hasta" class="form-control">
          <span class="input-group-btn">
            <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
          </span>
        </div>
        <button class="btn buscar btn-default pull-left"><i class="fa fa-search"></i></button>
      </div>
    </ul>
    <div class="tab-content">
      <div id="tab_detalle_stock1" class="tab-pane pr0 pl0 panel-body <%= (tab_default == "tabla")?"active":"" %>">
        <div class="b-a" style="height:250px; overflow: auto;">
          <table id="stock_detalle_tabla" class="table table-small table-striped sortable m-b-none default footable">
            <thead>
              <tr>
              <th style="width:40px">Fecha</th>
              <th style="width:40px">Mov.</th>
              <th style="width:40px">Unid.</th>
              <th style="width:50px">Stock</th>
              <th>Obs.</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
      <div id="tab_detalle_stock2" class="tab-pane pr0 pl0 panel-body <%= (tab_default == "grafico")?"active":"" %>">
        <div id="evolucion_stock_grafico" style="height:250px;"></div>
      </div>
    </div>
  </div>
</div>
</script>