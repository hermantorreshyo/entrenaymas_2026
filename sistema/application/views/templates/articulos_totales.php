<script type="text/template" id="articulos_totales_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <h1 class="m-n font-thin h3">Totales por Articulo</h1>
</div>
<div class="wrapper-md pb0">

    <div class="panel panel-default">
        <div class="form-inline wrapper pt0">
            <div class="form-group m-r-md m-t-md">
                <input placeholder="Codigo Articulo" type="text" class="form-control w150 m-l-xs" id="articulos_totales_codigo"/>
            </div>
            <div class="form-group m-r-md m-t-md">
                <input type="text" class="form-control m-l-xs w200" id="articulos_totales_descripcion" disabled/>
            </div>
            <div class="form-group m-r-md m-t-md">
                <div class="input-group m-l-xs w-md">
                    <input type="text" class="form-control" id="articulos_totales_desde" />
                    <span class="input-group-btn">
                      <button disabled class="btn btn-default">-</button>
                    </span>
                    <input type="text" class="form-control" id="articulos_totales_hasta" />
                </div>
            </div>
            <div class="form-group m-r-md m-t-md">
                <label class="control-label">Vendedor</label>
                <select id="articulos_totales_vendedor" class="form-control m-l-xs"></select>
            </div>                
            <div class="form-group m-r-md m-t-md">
                <button class="btn btn-success buscar"><i class="fa fa-search"></i></button>
            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading oh">
            <span class="bold m-t-xs pull-left">Resultados de B&uacute;squeda</span>
            <button class="btn pull-right btn-sm btn-info exportar btn-addon mr5">
                <i class="fa fa-file-excel-o"></i><span class="hidden-xs">Exportar</span>
            </button>
        </div>        
        <div class="panel-body">
            <div class="b-a" style="overflow: auto; height: 365px">
                <table id="articulos_totales_tabla" class="table sortable m-b-none default">
                    <thead class="thead">
                        <tr>
                            <th class="sorting" data-sort-by="codigo">Codigo</th>
                            <th class="sorting" data-sort-by="descripcion">Descripcion</th>
                            <th class="sorting" data-sort-by="cantidad_ventas">Facturas</th>
                            <th class="sorting" data-sort-by="cantidad">Vendido</th>
                            <th class="sorting" data-sort-by="recambio">Recambio</th>
                            <th>%</th>
                            <th class="sorting" data-sort-by="bonificado">Bonif</th>
                            <th class="sorting" data-sort-by="total">Total</th>
                        </tr>
                    </thead>
                    <tbody class="tbody" style="min-height: 280px"></tbody>
                </table>
            </div>
            <div class="mt15">
                <div class="form-inline">
                    <span class="bold">Cant. Vendida: </span>
                    <input type="text" id="articulos_totales_total_cantidad" value="0.00" disabled class="form-control bold fs14 w100 tar"/>
                    <span class="bold ml15">Importe Vendido: </span>
                    <input type="text" id="articulos_totales_total_precio" value="0.00" disabled class="form-control bold fs14 w100 tar"/>
                </div>
            </div>            
        </div>
    </div>
</div>    
</script>


<script type="text/template" id="articulos_totales_item_resultados_template">
    <td><%= codigo %></td>
    <td><%= descripcion %></td>
    <td class="tar"><%= Number(cantidad_ventas).toFixed(2) %></td>
    <td class="tar"><%= Number(cantidad).toFixed(2) %></td>
    <td class="tar"><%= Number(recambio).toFixed(2) %></td>
    <td class="tar">
      <% if (cantidad == 0) { %>
        <%= Number(0).toFixed(2) %>
      <% } else { %>
        <%= Number(recambio / cantidad * 100).toFixed(2) %>
      <% } %>
    </td>
    <td class="tar"><%= Number(bonificado).toFixed(2) %></td>
    <td class="tar"><%= Number(total).toFixed(2) %></td>
</script>