<script type="text/template" id="ventas_totales_template">

<div class="bg-light lter b-b wrapper-md ng-scope">
  <div class="row clearfix padder">
    <h1 class="m-n font-thin h3 pull-left">Totales de Ventas</h1>
    <div class="form-inline pull-right">
      <button onclick="workspace.cambiar_estado()" class="m-r btn <?php echo ($estado==0) ? "btn-default":"btn-danger" ?>">Supervisor</button>
    </div>
  </div>
</div>

<div class="wrapper-md ng-scope">
    <div class="panel panel-default">
      
        <div class="panel-heading clearfix">
          <div class="row">
            <div class="col-md-6 sm-m-b">
              <div style="width: 120px; display: inline-block">
                <input type="text" class="w100p form-control" id="ventas_totales_desde" placeholder="Desde" />  
              </div>
              <div style="width: 120px; display: inline-block">
                <input type="text" class="w100p form-control" id="ventas_totales_hasta" placeholder="Hasta" />  
              </div>
              <div style="width: 150px; display: inline-block">
                <select class="form-control" id="ventas_totales_agrupado_por">
                  <option value="">Agrupado por</option>
                  <option value="clientes">Clientes</option>
                  <option value="articulos">Articulos</option>
                  <option value="rubros">Categorias</option>
                  <option value="vendedores">Vendedores</option>
                </select>
              </div>
              <button class="btn btn-default buscar"><i class="fa fa-search"></i></button>
              <button class="btn btn-default advanced-search-btn"><i class="fa fa-angle-double-down"></i></button>
            </div>
            <div class="col-md-6 text-right">
              <div class="btn-group dropdown">
                <button class="btn btn-sm btn-default dropdown-toggle btn-addon" data-toggle="dropdown">
                  <i class="fa fa-database"></i><span class="hidden-xs">Datos</span>
                  <span class="caret"></span>
                </button>
                <ul class="dropdown-menu">
                  <li><a href="javascript:void" class="exportar">Exportar Excel</a></li>
                  <li><a href="javascript:void" class="exportar_csv">Exportar CSV</a></li>
                </ul>
              </div>
            </div>
          </div>
        </div>
        <div class="advanced-search-div bg-light dk" style="display:none">
          <div class="wrapper clearfix">
            <h4 class="m-t-xs"><i class="fa fa-search"></i> B&uacute;squeda Avanzada:</h4>
            <div class="form-inline">
              
              <!--
              <% if (control.check("vendedores")>0) { %>
                <select class="form-control" id="ventas_totales_vendedores">
                  <option value="0">Vendedor</option>
                  <% for(var i=0;i<vendedores.length;i++) { %>
                      <% var o = vendedores[i]; %>
                      <option value="<%= o.id %>"><%= o.nombre %></option>
                  <% } %>
                </select>
              <% } %>
              -->
              
              <div class="form-group">
                <button class="buscar btn btn-default"><i class="fa fa-search"></i> Buscar</button>
              </div>            
            </div>
          </div>
        </div>
        <div class="panel-body">
            <div class="b-a table-responsive">
                <table id="ventas_totales_tabla" class="table table-striped sortable m-b-none default footable">
                    <thead>
                        <tr>
                            <th>Concepto</th>
                            <th class="w100">Neto</th>
                            <th class="w100">IVA</th>
                            <th class="w100">Total</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                    <tfoot>
                      <tr>
                        <td></td>
                        <td id="ventas_totales_total_neto" class="bold"></td>
                        <td id="ventas_totales_total_iva" class="bold"></td>
                        <td id="ventas_totales_total_total" class="bold"></td>
                      </tr>
                    </tfoot>
                    <!--<div class="pagination_container hide-if-no-paging"></div>-->
                </table>              
            </div>
        </div>
    </div>
</div>
</script>

<script type="text/template" id="ventas_totales_item_resultados_template">
  <td class=""><%= concepto %></td>
  <td class=""><%= Number(neto).toFixed(2) %></td>
  <td class=""><%= Number(iva).toFixed(2) %></td>
  <td class=""><%= Number(total).toFixed(2) %></td>
</script>