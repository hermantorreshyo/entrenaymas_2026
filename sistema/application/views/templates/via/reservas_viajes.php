<script type="text/template" id="reservas_viajes_resultados_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <h1 class="m-n font-thin h3"><i class="fa fa-suitcase icono_principal"></i>Reservas
</div>
<div class="wrapper-md">
  <div class="panel panel-default">
    <div class="panel-heading clearfix">
      <div class="row">
        <div class="col-md-6 col-lg-3 sm-m-b">
          <div class="input-group">
            <input type="text" id="reservas_viajes_buscar" placeholder="Buscar..." autocomplete="off" class="form-control">
            <span class="input-group-btn">
              <button class="btn buscar btn-default"><i class="fa fa-search"></i></button>
            </span>
          </div>
        </div>
        <div class="col-md-6 col-lg-offset-3 col-lg-6 text-right">
          <a class="btn btn-info btn-addon ml5 nuevo" href="javascript:void(0)">
            <i class="fa fa-plus"></i><span>&nbsp;&nbsp;Nuevo&nbsp;&nbsp;</span>
          </a>
        </div>
      </div>
    </div>
    <div class="panel-body">
      <div class="b-a table-responsive">
        <table id="reservas_tabla" class="table table-striped sortable m-b-none default footable">
          <thead>
            <tr>
              <th style="width:20px;">
                <label class="i-checks m-b-none">
                  <input class="esc sel_todos" type="checkbox"><i></i>
                </label>
              </th>
              <th>Cliente</th>
              <th>Viaje</th>
              <th>Fecha</th>
              <th>Para</th>
              <th>Estado</th>
              <th class="tar" style="width: 140px">Total</th>
              <th class="tar" style="width: 140px">Pagado</th>
              <th class="th_acciones"></th>
            </tr>
          </thead>
          <tbody class="tbody"></tbody>
          <tfoot class="pagination_container hide-if-no-paging"></tfoot>
        </table>
      </div>
    </div>
  </div>
</div>
</script>

<script type="text/template" id="reservas_viajes_item_resultados_template">
  <% var clase = "" %>
  <td>
    <label class="i-checks m-b-none">
      <input class="esc check-row" value="<%= id %>" type="checkbox"><i></i>
    </label>
  </td>
  <td class="<%= clase %> data">
    <span class="text-info"><%= cliente.ucwords() %></span>
  </td>
  <td class="<%= clase %> data"><%= viaje.ucwords() %></td>
  <td class="<%= clase %> data"><%= fecha_realizacion %></td>
  <td class="<%= clase %> data"><%= fecha_reserva %></td>
  <td class="<%= clase %> data">
    <% if (id_tipo_estado == 6 || id_tipo_estado == 5 || id_tipo_estado == 4) { %>
      <span class="label bg-success"><%= estado %></span>
    <% } else if (id_tipo_estado == 7) { %>
      <span class="label bg-danger"><%= estado %></span>
    <% } else { %>
      <span class="label bg-light dk"><%= estado %></span>
    <% } %> 
  </td>
  <td class="<%= clase %> data tar">$ <%= total %></td>
  <td class="<%= clase %> data tar">$ <%= pagado %></td>
  <td class="<%= clase %>">
    <div class="fr m-t-xs btn-group dropdown">
      <i title="Opciones" class="iconito fa fa-caret-down dropdown-toggle" data-toggle="dropdown"></i>
      <ul class="dropdown-menu pull-right">
        <li><a href="javascript:void(0)" class="imprimir" data-id="<%= id %>">Imprimir</a></li>
        <li><a href="javascript:void(0)" class="eliminar" data-id="<%= id %>">Eliminar</a></li>
      </ul>
    </div>
  </td>
</script>
