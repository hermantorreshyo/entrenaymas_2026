<script type="text/template" id="estadisticas_prestamos_activos_panel_template">
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <h1 class="m-n font-thin h3 text-black">
      <i class="fa fa-bar-chart icono_principal"></i>Estad&iacute;sticas
      / <b>Préstamos</b>
    </h1>
  </div>
  <div class="wrapper-md ng-scope">
    <div class="panel panel-default">    
      <div class="panel-heading clearfix">
        <div class="input-group fl w200">
          <input type="text" id="estadisticas_prestamos_activos_buscar" value="<%= window.estadisticas_prestamos_activos_filter %>" placeholder="Buscar..." autocomplete="off" class="form-control">
          <span class="input-group-btn">
            <button class="btn btn-default buscar"><i class="fa fa-search"></i></button>
          </span>
        </div>
        <div class="input-group fl w150">
          <select class="form-control action no-model" id="estadisticas_prestamos_activos_sucursales">
            <option value="0">Sucursal</option>
            <% for(var i=0;i< almacenes.length;i++) { %>
              <% var almacen = almacenes[i] %>
              <option value="<%= almacen.id %>" <%= (window.estadisticas_prestamos_activos_id_sucursal == almacen.id)?"selected":"" %>><%= almacen.nombre %></option>
            <% } %>
          </select>
        </div>
        <div class="fr">
          <div class="btn-group dropdown">
            <button class="btn btn-default dropdown-toggle btn-addon" data-toggle="dropdown">
              <i class="fa fa-cog"></i><span>Opciones</span>
              <span class="caret"></span>
            </button>
            <ul class="dropdown-menu pull-right">
              <?php /*<li><a href="javascript:void" class="imprimir">Imprimir</a></li>*/ ?>
              <li><a href="javascript:void" class="exportar">Exportar Excel</a></li>
            </ul>
          </div>
        </div>
      </div>
      <div class="panel-body">
        <div class="b-a table-responsive">
          <table id="estadisticas_prestamos_activos_table" class="table table-striped sortable m-b-none default footable">
            <thead>
              <tr>
                <th rowspan="2">Plan</th>
                <th rowspan="2" style="border-right: solid 1px #dddddd">Activos</th>
                <th colspan="6" style="border-bottom: solid 1px #dddddd" class="text-center">Mora</th>
                <th rowspan="2" style="border-left: solid 1px #dddddd">Cuota mas elegida</th>
                <th rowspan="2">Cant. Veces</th>
              </tr>
              <tr>
                <th class="">< 30</th>
                <th class="">30-60</th>
                <th class="">60-90</th>
                <th class="">> 90</th>
                <th class="">Total</th>
                <th class="">% Mora</th>
              </tr>
            </thead>
            <tbody></tbody>
            <tfoot class="bg-important"></tfoot>
          </table>
        </div>
      </div>
    </div>
  </div>  
</script>


<script type="text/template" id="estadisticas_prestamos_activos_item">
  <td><a href="app/#pres_clientes/<%= id_plan %>" target="_blank" class='text-info'><%= plan %></a></td>
  <td><span><%= cantidad %></span></td>
  <td><span><%= cantidad_mora_30 %></span></td>
  <td><span><%= cantidad_mora_60 %></span></td>
  <td><span><%= cantidad_mora_90 %></span></td>
  <td><span><%= cantidad_mora_mas_90 %></span></td>
  <td><span><%= cantidad_mora %></span></td>
  <td><span><%= (cantidad > 0) ? Number(cantidad_mora / cantidad * 100).toFixed(2) : Number(0).toFixed(2) %> %</span></td>
  <td><span><%= cuota_mas_elegida %></span></td>
  <td><span><%= veces_mas_elegida %></span></td>
</script>