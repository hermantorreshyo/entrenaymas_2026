<script type="text/template" id="valoracion_stock_resultados_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
    <h1 class="m-n font-thin h3"><i class="fa fa-tags icono_principal"></i>Productos / 
        <b>Valoraci&oacute;n de Stock</b>
    </h1>
</div>
<div class="wrapper-md">
    <div class="panel panel-default">
    
        <div class="panel-heading clearfix">

            <div class="pull-left form-inline">
                <div class="input-group">
                    <input type="text" id="valoracion_stock_fecha" class="w150 form-control no-model">
                    <span class="input-group-btn">
                        <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                        <button class="btn btn-default buscar"><i class="fa fa-search"></i></button>
                    </span>
                </div>
            </div>

            <div class="pull-left form-inline">
              <div class="input-group">
                <select id="valoracion_stock_sucursales" class="form-control w200">
                  <?php //TODO: Configurar si un usuario puede ver los valoracion_stock de los demas o no ?>
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
            </div>
            
            <button class="btn pull-right btn-sm btn-primary imprimir btn-addon" title="Imprimir">
				<i class="glyphicon glyphicon-print"></i><span class="hidden-xs">Imprimir</span>
			</button>

            <button class="btn pull-right btn-sm btn-info mr5 exportar btn-addon">
                <i class="fa fa-file-excel-o"></i><span class="hidden-xs">Exportar</span>
            </button>
        </div>
        <div class="panel-body">
            <div class="b-a table-responsive">
                <table id="valoracion_stock_tabla" class="table table-small sortable m-b-none default footable">
                    <thead>
                        <tr>
							<th>Mov.</th>
							<th>C&oacute;digo</th>
							<th>Producto</th>
							<th class="tar">Stock</th>
                            <th class="tar">Costo Neto</th>
							<th class="tar">Costo Final</th>
							<th class="tar">Precio Final</th>
                        </tr>
                    </thead>
                    <tbody class="tbody"></tbody>
					<tfoot class="tfoot">
						<tr>
    						<td></td>
    						<td></td>
    						<td></td>
    						<td></td>
    						<td id="costo_neto_total" class="bold fs16 tar"></td>
                            <td id="costo_final_total" class="bold fs16 tar"></td>
    						<td id="precio_total" class="bold fs16 tar"></td>
						</tr>
					</tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
</script>


<script type="text/template" id="valoracion_stock_item_resultados_template">
    <td class=""><%= fecha %></td>
    <td class=""><%= codigo %></td>
    <td class=""><%= nombre %></td>
    <td class="tar "><%= Number(cantidad).toFixed(2) %></td>
    <td class="tar ">$ <%= Number(costo_neto).toFixed(2) %></td>
    <td class="tar ">$ <%= Number(costo_final).toFixed(2) %></td>
    <td class="tar ">$ <%= Number(precio).toFixed(2) %></td>
</script>