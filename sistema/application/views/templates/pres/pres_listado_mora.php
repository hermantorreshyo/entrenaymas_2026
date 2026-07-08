<script type="text/template" id="pres_listado_mora_panel_template">
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <h1 class="m-n font-thin h3"><i class="glyphicon glyphicon-stats icono_principal"></i>Estad&iacute;sticas
      / <b>Listado de Mora</b>
    </h1>
  </div>
  <div class="wrapper-md ng-scope">
    <div class="panel panel-default">    
      <div class="panel-heading clearfix">
        <div class="input-group fl w200">
          <input type="text" id="pres_listado_mora_buscar" value="<%= window.pres_listado_mora_filter %>" placeholder="Buscar..." autocomplete="off" class="form-control">
          <span class="input-group-btn">
            <button class="btn btn-default buscar"><i class="fa fa-search"></i></button>
          </span>
        </div>
        <div class="input-group fl w150">
          <select class="form-control action no-model" id="pres_listado_mora_sucursales">
            <option value="0">Sucursal</option>
            <% for(var i=0;i< almacenes.length;i++) { %>
              <% var almacen = almacenes[i] %>
              <option value="<%= almacen.id %>" <%= (window.pres_listado_mora_id_sucursal == almacen.id)?"selected":"" %>><%= almacen.nombre %></option>
            <% } %>
          </select>
        </div>
        <div class="input-group fl w250">
          <select class="form-control action no-model" id="pres_listado_mora_planes"></select>
        </div>
        <div class="fr">
          <div class="btn-group dropdown">
            <button class="btn btn-default dropdown-toggle btn-addon" data-toggle="dropdown">
              <i class="fa fa-cog"></i><span>Opciones</span>
              <span class="caret"></span>
            </button>
            <ul class="dropdown-menu pull-right">
              <li><a href="javascript:void" class="imprimir">Imprimir</a></li>
              <li><a href="javascript:void" class="exportar">Exportar Excel</a></li>
            </ul>
          </div>
        </div>
      </div>
      <div class="panel-body">
        <div class="b-a table-responsive">
          <table id="pres_listado_mora_table" class="table table-striped table-small sortable m-b-none default footable">
            <thead>
              <tr>
                <th style="width:20px;">
                  <label class="i-checks m-b-none">
                    <input class="esc sel_todos" type="checkbox"><i></i>
                  </label>
                </th>
                <th class="w50 tac hidden-xs"></th>
                <th class="">Nombre</th>
                <th class="col-xxs-0">Localidad</th>
                <th class="col-xxs-0">Telefono</th>
                <th class="col-xxs-0">Plan</th>
                <th class="col-xxs-0">#</th>
                <th class="col-xxs-0">Valor</th>
                <th class="">Cuotas</th>
                <th class="">Ult. Pago</th>
                <th class="sorting" data-sort-by="dias_mora">Mora</th>
                <th class="">Deuda</th>
                <% if (permiso > 1) { %>
                  <th class="w100"></th>
                <% } %>
              </tr>
            </thead>
            <tbody></tbody>
            <tfoot class="pagination_container hide-if-no-paging"></tfoot>
          </table>
        </div>
      </div>
    </div>
  </div>  
</script>


<script type="text/template" id="pres_listado_mora_item">
  <td>
    <label class="i-checks m-b-none">
      <input class="esc check-row" value="<%= id %>" type="checkbox"><i></i>
    </label>
  </td>
  <td class="data hidden-xs">
    <% if (!isEmpty(path)) { %>
      <img src="/sistema/<%= path %>" class="customcomplete-image"/>
    <% } else { %>
      <span class="avatar xs avatar-texto bg-info pull-left">
        <%= nombre.substr(0,1).toUpperCase() %>
      </span>
    <% } %>
  </td>
  <td class="ver"><a target="_blank" href="app/#pres_cliente_acciones/<%= id %>" class='text-info'><%= apellido %> <%= nombre %></a></td>
  <td class="ver col-xxs-0"><span><%= localidad %></span></td>
  <td class="ver col-xxs-0"><span><%= telefono %></span></td>
  <td class="ver col-xxs-0"><span><%= plan %></span></td>
  <td class="ver col-xxs-0"><span><%= numero %></span></td>
  <td class="ver col-xxs-0"><span><%= valor_cuota %></span></td>
  <td class="ver"><span><%= cantidad_cuotas_pagas %>/<%= cantidad_cuotas %></span></td>
  <td class="ver"><span><%= fecha_ultimo_pago %> ($ <%= Number(ultimo_pago).toFixed(2) %>)</span></td>
  <td class="ver w80"><span><%= dias_mora %></span></td>
  <td class="ver"><span><%= deuda_vencida %></span></td>
  <% if (permiso > 1) { %>
    <td class="p5 td_acciones">
      <div class="btn-group dropdown ml10">
        <button class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <i class="fa fa-plus"></i>
        </button>    
        <ul class="dropdown-menu pull-right">
        </ul>
      </div>
    </td>
  <% } %>
</script>