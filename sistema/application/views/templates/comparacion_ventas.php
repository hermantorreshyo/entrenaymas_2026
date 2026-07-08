<script type="text/template" id="comparacion_ventas_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <h1 class="m-n font-thin h3">Comparacion de Ventas</h1>
</div>
<div class="wrapper-md pb0">

    <div class="panel panel-default">
        <div class="form-inline wrapper pt0">
            <div class="form-group m-r-md m-t-md">
                <label class="control-label">Periodo 1</label>
                <div class="input-group m-l-xs w-md">
                    <input type="text" class="form-control" id="comparacion_ventas_desde_1" />
                    <span class="input-group-btn">
                      <button disabled class="btn btn-default">-</button>
                    </span>
                    <input type="text" class="form-control" id="comparacion_ventas_hasta_1" />
                </div>
            </div>
            <div class="form-group m-r-md m-t-md">
                <label class="control-label">Periodo 2</label>
                <div class="input-group m-l-xs w-md">
                    <input type="text" class="form-control" id="comparacion_ventas_desde_2" />
                    <span class="input-group-btn">
                      <button disabled class="btn btn-default">-</button>
                    </span>
                    <input type="text" class="form-control" id="comparacion_ventas_hasta_2" />
                </div>
            </div>
            <div class="form-group m-r-md m-t-md">
                <select id="comparacion_ventas_parametro" class="form-control m-l-xs">
                    <option value="A">Articulos</option>
                    <option value="R">Rubros</option>
                    <option value="C">Clientes</option>
                </select>
            </div>
            <div class="form-group m-r-md m-t-md">
                <select id="comparacion_ventas_vendedor" class="form-control m-l-xs"></select>
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
                <table id="comparacion_ventas_tabla" class="table sortable m-b-none default">
                    <thead class="thead">
                        <tr>
                            <th class="sorting" data-sort-by="descripcion">Detalle</th>
                            <th class="sorting tar" data-sort-by="total_1">Ventas $</th>
                            <th class="sorting tar" data-sort-by="cantidad_1">Cant.</th>
                            <th class="sorting tar" data-sort-by="total_2">Ventas $</th>
                            <th class="sorting tar" data-sort-by="cantidad_2">Cant.</th>
                            <th class="sorting tar" data-sort-by="variacion">Var (%)</th>
                        </tr>
                    </thead>
                    <tbody class="tbody" style="min-height: 280px"></tbody>
                    <tfoot>
                        <tr>
                            <td></td>
                            <td class="tar bold" id="total_ventas_1"></td>
                            <td class="tar bold" id="total_cantidad_1"></td>
                            <td class="tar bold" id="total_ventas_2"></td>
                            <td class="tar bold" id="total_cantidad_2"></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>    
</script>


<script type="text/template" id="comparacion_ventas_item_resultados_template">
    <td><%= descripcion %></td>
    <td class="tar"><%= Number(total_1).toFixed(2) %></td>
    <td class="tar"><%= Number(cantidad_1).toFixed(2) %></td>
    <td class="tar"><%= Number(total_2).toFixed(2) %></td>
    <td class="tar"><%= Number(cantidad_2).toFixed(2) %></td>
    <td class="tar"><%= Number(variacion).toFixed(2) %></td>
</script>