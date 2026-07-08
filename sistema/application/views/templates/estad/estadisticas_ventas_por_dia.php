<script type="text/template" id="estadisticas_ventas_por_dia_template">
  <div id="estadisticas_ventas_por_dia_container" class="col">
    <div class="bg-light titulo-pagina lter b-b wrapper-md">
      <div class="row">
        <div class="col-xs-12">
          <h1 class="m-n font-thin h3 text-black">
            <i class="fa fa-bar-chart icono_principal"></i>Estad&iacute;sticas
            / <b>Ventas por d&iacute;a</b>
          </h1>
        </div>
      </div>
    </div>

    <div class="wrapper-md">

      <div class="panel panel-default">
        <div class="panel-body">
          <div class="">
            <div class="input-group pull-left" style="width: 140px;">
              <input type="text" id="estadisticas_ventas_por_dia_fecha_desde" class="form-control" autocomplete="off">
              <span class="input-group-btn">
                <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
              </span>              
            </div>
            <div class="input-group pull-left" style="width: 140px;">
              <input type="text" id="estadisticas_ventas_por_dia_fecha_hasta" class="form-control" autocomplete="off">
              <span class="input-group-btn">
                <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
              </span>              
            </div>
            <% if (typeof almacenes != "undefined") { %>
              <select class="form-control pull-left m-l-xs" style="display: inline-block; width: 160px;" id="estadisticas_ventas_por_dia_sucursales">
                <% if (ID_SUCURSAL != 0) { %>
                  <% for(var i=0; i< almacenes.length; i++) { %>
                    <% var alm = almacenes[i] %>
                    <% if (ID_SUCURSAL == alm.id) { %>
                      <option value="<%= alm.id %>"><%= alm.nombre %></option>
                    <% } %>
                  <% } %>
                <% } else { %>
                  <option value="0">Sucursal</option>
                  <% for(var i=0; i< almacenes.length; i++) { %>
                    <% var alm = almacenes[i] %>
                    <option value="<%= alm.id %>"><%= alm.nombre %></option>
                  <% } %>
                <% } %>
              </select>
            <% } %>
            <button class="btn btn-default buscar pull-left m-l-xs"><i class="fa fa-search"></i></button>
            <button class="btn btn-default imprimir pull-left m-l-xs"><i class="fa fa-print"></i></button>
          </div>
        </div>
      </div>

      <div class="panel panel-default">
        <div class="panel-body">
          <div class="b-a table-responsive">
            <table id="estadisticas_ventas_por_dia_table" class="table table-small table-striped sortable m-b-none default footable">
              <thead>
                <tr>
                  <th>Fecha</th>
                  <th>Sucursal</th>
                  <th>Tickets</th>
                  <th>Efectivo</th>
                  <th>Tarjetas</th>
                  <th>Recargo</th>
                  <th>Venta Total</th>
                  <th>Costo Mercaderia</th>
                  <th>Ganancia Bruta</th>
                  <th>% Margen</th>
                </tr>
              </thead>
              <tbody></tbody>
              <tfoot class="bg-important">
                <tr>
                  <td></td>
                  <td></td>
                  <td id="estadisticas_ventas_por_dia_tickets" class="bold">0</td>
                  <td id="estadisticas_ventas_por_dia_efectivo" class="bold">$ 0.00</td>
                  <td id="estadisticas_ventas_por_dia_tarjetas" class="bold">$ 0.00</td>
                  <td id="estadisticas_ventas_por_dia_intereses" class="bold">$ 0.00</td>
                  <td id="estadisticas_ventas_por_dia_venta" class="bold">$ 0.00</td>
                  <td id="estadisticas_ventas_por_dia_cmv" class="bold">$ 0.00</td>
                  <td id="estadisticas_ventas_por_dia_ganancia" class="bold">$ 0.00</td>
                  <td id="estadisticas_ventas_por_dia_margen" class="bold">0.00 %</td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
      </div>

    </div>
  </div>
</script>

<script type="text/template" id="estadisticas_ventas_por_dia_item_template">
<td><%= fecha %></td>
<td><%= sucursal %></td>
<td><%= cantidad %></td>
<td>$ <%= Number(efectivo).toFixed(2) %></td>
<td>$ <%= Number(tarjetas).toFixed(2) %></td>
<td>$ <%= Number(intereses).toFixed(2) %></td>
<td class="negro">$ <%= Number(total).toFixed(2) %></td>
<td>$ <%= Number(costo).toFixed(2) %></td>
<td>$ <%= Number(total - costo).toFixed(2) %></td>
<% var margen = (costo > 0) ? (((total-costo) / costo) * 100) : 0 %>
<td><%= Number(margen).toFixed(2) %> %</td>
</script>