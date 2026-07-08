<script type="text/template" id="comisiones_vendedores_resultados_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
    <h1 class="m-n font-thin h3"><i class="fa fa-file-text icono_principal"></i>Ventas / 
        <b>Comisiones de vendedores</b>
    </h1>
</div>    
<div class="wrapper-md">
    <div class="panel panel-default">
    
        <div class="panel-heading clearfix">

            <div class="pull-left form-inline">
                <div class="input-group">
                    <input type="text" id="comisiones_vendedores_desde" class="w120 form-control no-model">
                    <span class="input-group-btn">
                        <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                    </span>
                </div>
            </div>
            <div class="pull-left form-inline">
                <div class="input-group">
                    <input type="text" id="comisiones_vendedores_hasta" class="w120 form-control no-model">
                    <span class="input-group-btn">
                        <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                    </span>
                </div>
            </div>
            <button class="btn pull-left btn-default buscar"><i class="fa fa-search"></i></button>

            <div class="fr">
                <div class="btn-group dropdown">
                  <button class="btn btn-default dropdown-toggle btn-addon" data-toggle="dropdown">
                    <i class="fa fa-cog"></i><span>Opciones</span>
                    <span class="caret"></span>
                  </button>
                  <ul class="dropdown-menu">
                    <li><a href="javascript:void" class="exportar">Exportar Excel</a></li>
                  </ul>
                </div>
            </div>
        </div>
        <div class="panel-body">
            <div class="b-a table-responsive">
                <table class="table table-small table-striped sortable m-b-none default footable">
                    <thead>
                        <tr>
                            <th>Vendedor</th>
                            <th>Comprobantes</th>
                            <th class="tar">Venta</th>
                            <th class="tar">% / Venta</th>  
                            <th class="tar">Promedio</th>
                            <th class="tar th_acciones">% Comision</th>
                            <th class="tar th_acciones">Comision</th>
                        </tr>
                    </thead>
                    <tbody id="comisiones_vendedores_tbody" class="tbody"></tbody>
                    <tfoot class="bg-important">
                        <tr>
                            <td></td>
                            <td class="bold" id="comisiones_vendedores_comprobantes"></td>
                            <td class="tar bold" id="comisiones_vendedores_total"></td>
                            <td></td>
                            <td class="tar bold" id="comisiones_vendedores_promedio"></td>
                            <td></td>
                            <td class="tar bold" id="comisiones_vendedores_comision"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
</script>


<script type="text/template" id="comisiones_vendedores_item_resultados_template">
    <td class=""><span class="text-info"><%= vendedor %></span></td>
    <td class=""><%= total_facturas %></td>
    <td class="tar negro">$ <%= Number(total).toFixed(2) %></td>
    <td class="tar"><%= Number(porc_venta).toFixed(2) %> %</td>
    <td class="tar">$ <%= (total_facturas != 0) ? Number(total / total_facturas).toFixed(2) : 0 %></td>
    <td class="tar"><%= Number(comision).toFixed(2) %> %</td>
    <td class="tar negro">$ <%= Number(total * (comision / 100)).toFixed(2) %></td>
</script>