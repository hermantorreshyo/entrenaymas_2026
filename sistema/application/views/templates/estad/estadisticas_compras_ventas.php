<script type="text/template" id="estadisticas_compras_ventas_template">
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <h1 class="m-n font-thin h3"><i class="glyphicon glyphicon-stats icon icono_principal"></i>Estadisticas</h1>
  </div>
  <div class="wrapper-md ng-scope">
    <div class="panel panel-default">

      <ul class="nav nav-tabs nav-tabs-2" role="tablist">
        <li class="active">
          <a href="javascript:void(0)"><i class="fa fa-list text-info"></i> Por Sucursal</a>
        </li>
        <li>
          <a href="app/#estadisticas_compras_ventas_por_articulos"><i class="fa fa-list text-info"></i> Por Articulos</a>
        </li>
      </ul>

      <div class="panel-heading clearfix">

        <div class="input-group pull-left" style="width: 200px;">
          <select id="estadisticas_compras_ventas_proveedores"></select>
        </div>

        <div class="input-group pull-left" style="width: 150px;">
          <select class="form-control" id="estadisticas_compras_ventas_sucursales">
            <% if (ID_SUCURSAL != 0) { %>
              <% for(var i=0;i< window.almacenes.length;i++) { %>
                <% var o = almacenes[i]; %>
                <% if (ID_SUCURSAL == o.id) { %>
                  <option value="<%= o.id %>"><%= o.nombre %></option>
                <% } %>  
              <% } %>
            <% } else { %>
              <option value="0">Sucursal</option>
              <% for(var i=0;i< window.almacenes.length;i++) { %>
                <% var o = almacenes[i]; %>
                <option value="<%= o.id %>"><%= o.nombre %></option>
              <% } %>
            <% } %>
          </select>   
        </div>

        <div class="input-group pull-left" style="width: 140px;">
          <input type="text" id="estadisticas_compras_ventas_fecha_desde" class="form-control">
          <span class="input-group-btn">
            <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
          </span>              
        </div>

        <div class="input-group pull-left" style="width: 140px;">
          <input type="text" id="estadisticas_compras_ventas_fecha_hasta" class="form-control">
          <span class="input-group-btn">
            <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
          </span>
        </div>

        <button class="btn btn-default buscar pull-left m-l-xs"><i class="fa fa-search"></i></button>

        <button class="btn btn-default advanced-search-btn ml5 pull-left"><i class="fa fa-filter"></i></button>

        <div class="btn-group dropdown pull-right">
          <button class="btn btn-default dropdown-toggle btn-addon" data-toggle="dropdown">
            <i class="fa fa-cog"></i><span><?php echo lang(array("es"=>"Opciones","en"=>"Options")); ?></span>
            <span class="caret"></span>
          </button>
          <ul class="dropdown-menu">
            <li><a href="javascript:void(0)" class="exportar">Exportar Excel</a></li>
            <li><a href="javascript:void(0)" class="exportar_solo_ventas">Exp. solo con Movimiento</a></li>
          </ul>
        </div>
      </div>
      <div class="advanced-search-div bg-light dk">
        <div class="wrapper clearfix">
          <h4 class="m-t-xs m-b"><i class="fa fa-filter"></i> <?php echo lang(array("es"=>"Filtros:","en"=>"Filters:")); ?></h4>
          <div class="row pl10 pr10">
            <div class="col-md-3 col-sm-3 col-xs-12 pr5 pl5">
              <div class="form-group">
                <input type="text" placeholder="Buscar..." id="estadisticas_compras_ventas_fecha_filter" class="form-control">
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="panel-body">
        <div class="b-a table-responsive" style="height: 400px; overflow: auto">
          <table id="estadisticas_compras_ventas_table" class="table table-small table-striped sortable m-b-none default footable">
            <thead>
              <tr>
                <th>Codigo</th>
                <th>EAN</th>
                <th>Prov.</th>
                <th class="sorting" style="min-width:150px" data-sort-by="nombre">Producto</th>
                <th>Costo</th>
                <th>Precio</th>
                <th>Margen</th>
                <th class="sorting" style="min-width:100px" data-sort-by="cantidad_compra">Compra</th>
                <th>Ult. Compra</th>
                <th class="sorting" style="min-width:100px" data-sort-by="cantidad_venta">Venta</th>
                <th>Ult. Venta</th>
                <th>Dif.</th>
                <th>%</th>
                <th>Stock</th>
                <th class="w25"></th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</script>

<script type="text/template" id="estadisticas_compras_ventas_por_articulos_template">
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <h1 class="m-n font-thin h3"><i class="glyphicon glyphicon-stats icon icono_principal"></i>Estadisticas</h1>
  </div>
  <div class="wrapper-md ng-scope">
    <div class="panel panel-default">

      <ul class="nav nav-tabs nav-tabs-2" role="tablist">
        <li>
          <a href="app/#estadisticas_compras_ventas"><i class="fa fa-list text-info"></i> Por Sucursal</a>
        </li>
        <li class="active">
          <a href="javascript:void(0)"><i class="fa fa-list text-info"></i> Por Articulos</a>
        </li>
      </ul>

      <div class="panel-heading clearfix">

        <div class="input-group pull-left" style="width: 200px;">
          <input type="text" placeholder="Codigos de articulos..." id="estadisticas_compras_ventas_por_articulos_filter" class="form-control">
        </div>

        <div class="input-group pull-left" style="width: 140px;">
          <input type="text" id="estadisticas_compras_ventas_por_articulos_fecha_desde" class="form-control">
          <span class="input-group-btn">
            <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
          </span>              
        </div>

        <div class="input-group pull-left" style="width: 140px;">
          <input type="text" id="estadisticas_compras_ventas_por_articulos_fecha_hasta" class="form-control">
          <span class="input-group-btn">
            <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
          </span>
        </div>

        <button class="btn btn-default buscar pull-left m-l-xs"><i class="fa fa-search"></i></button>

        <div class="btn-group dropdown pull-right">
          <button class="btn btn-default dropdown-toggle btn-addon" data-toggle="dropdown">
            <i class="fa fa-cog"></i><span><?php echo lang(array("es"=>"Opciones","en"=>"Options")); ?></span>
            <span class="caret"></span>
          </button>
          <ul class="dropdown-menu">
            <li><a href="javascript:void(0)" class="exportar">Exportar Excel</a></li>
          </ul>
        </div>
      </div>
      <div class="panel-body">
        <div class="b-a table-responsive" style="height: 400px; overflow: auto">
          <table id="estadisticas_compras_ventas_por_articulos_table" class="table table-small table-striped sortable m-b-none default footable">
            <thead>
              <tr>
                <th>Codigo</th>
                <th>EAN</th>
                <th>Prov.</th>
                <th class="sorting" style="min-width:150px" data-sort-by="nombre">Producto</th>
                <th>Costo</th>
                <th>Precio</th>
                <th>Margen</th>
                <th class="sorting" style="min-width:100px" data-sort-by="cantidad_compra">Compra</th>
                <th>Ult. Compra</th>
                <th class="sorting" style="min-width:100px" data-sort-by="cantidad_venta">Venta</th>
                <th>Ult. Venta</th>
                <th>Dif.</th>
                <th>%</th>
                <th>Stock</th>
                <th class="w25"></th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</script>

<script type="text/template" id="estadisticas_compras_ventas_item_template">
  <% if (id_articulo == -1) { %>
    <td colspan="15"><%= codigo %></td>
  <% } else { %>
    <% var decimal = (MEGASHOP == 1 || ID_EMPRESA == 421)?0:2 %>
    <td><%= codigo %></td>
    <td><%= codigo_barra.replaceAll("###","<br/>") %></td>
    <td><%= codigo_prov %></td>
    <td><span class="text-info"><%= nombre %></span></td>
    <td><%= Number(costo_final).toFixed(2) %></td>
    <td><%= Number(precio_final_dto).toFixed(2) %></td>
    <td><%= (costo_final > 0) ? Number(((precio_final_dto - costo_final)/costo_final)*100).toFixed(2) : Number(0).toFixed(2) %></td>
    <td><%= Number(cantidad_compra).toFixed(decimal) %></td>
    <td><%= fecha_compra %></td>
    <td><%= Number(cantidad_venta).toFixed(decimal) %></td>
    <td><%= fecha_venta %></td>
    <td><%= Number(cantidad_compra - cantidad_venta).toFixed(decimal) %></td>
    <td><%= Number(porcentaje).toFixed(2) %>%</td>
    <td><%= Number(stock).toFixed(decimal) %></td>
    <td><i class="fa fa-search cp ver_detalle"></i></td>
  <% } %>
</script>