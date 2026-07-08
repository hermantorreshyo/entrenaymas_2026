<script type="text/template" id="toque_pedidos_repartidores_resultados_template">
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <h1 class="m-n font-thin h3"><i class="fa fa-shopping-cart icono_principal"></i>Pedidos</h1>
  </div>
  <div class="wrapper-md ng-scope">
    <div class="panel panel-default">

      <div class="panel-heading clearfix">
        <div class="row">
          <div class="col-md-10 sm-m-b">
            <div class="fl">
              <div class="input-group w200">
                <select id="toque_pedidos_repartidores_quincena" class="form-control">
                  <option value="hoy">Hoy</option>
                  <option value="ayer">Ayer</option>
                  <option value="quincena_actual">Quincena Actual</option>
                  <option value="quincena_anterior">Quincena Anterior</option>
                </select>
              </div>
            </div>
          </div> 
        </div>
      </div>

      <div class="panel-body resumen pb0">
        <div class="row">
          <div class="col-md-3">
            <div class="block tac panel padder-v item bg-success mb0" style="height: 80px">
              <div id="toque_pedidos_repartidores_resumen_total" class="h3 font-thin text-white block">0</div>
              <span class="text-muted text-md pt5 db">Total al 90%</span>
            </div>
          </div>
          <?php /*
          <div class="col-md-3">
            <div class="block tac panel padder-v item mb0" style="height: 80px">
              <div id="toque_pedidos_repartidores_resumen_efectivo" class="h3 font-thin block">0</div>
              <span class="text-muted text-md pt5 db">Efectivo</span>
            </div>
          </div>
          <div class="col-md-3">
            <div class="block tac panel padder-v item mb0" style="height: 80px">
              <div id="toque_pedidos_repartidores_resumen_costo_envio" class="h3 font-thin block">0</div>
              <span class="text-muted text-md pt5 db">Costo Envio</span>
            </div>
          </div>*/ ?>
          <div class="col-md-3">
            <div class="block tac panel padder-v item bg-info mb0" style="height: 80px">
              <span id="toque_pedidos_repartidores_resumen_cantidad" class="font-thin h3 block">0</span>
              <span class="text-muted text-md pt5 db">Cantidad</span>
            </div>
          </div>
        </div>
      </div>

      <div class="panel-body">
        <div class="b-a table-responsive">
          <table id="toque_pedidos_repartidores_tabla" class="table table-small table-striped sortable m-b-none default footable">
            <thead>
              <tr>
                <th>Fecha</th>
                <th>Comprobante</th>
                <th>Comercio</th>
                <th>Pago</th>
                <th>Total</th>
                <th>Costo Envio</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>              
        </div>
      </div>
    </div>
  </div>
</script>

<script type="text/template" id="toque_pedidos_repartidores_item_template">
  <td><%= fecha %></td>
  <td><%= comprobante %></td>
  <td><%= usuario %></td>
  <td>
    <% if (tarjeta > 0) { %>
      <i data-toggle="tooltip" title="MercadoPago: <%= custom_10 %>" class="fa fa-credit-card m-l-xs text-warning cp"></i>
    <% } %>
    <% if (efectivo > 0) { %>
      <i data-toggle="tooltip" title="Paga con $<%= efectivo %>" class="fa fa-money m-l-xs text-success"></i>
    <% } %>
    <% if (cheque > 0) { %>
      <i data-toggle="tooltip" title="Pago con cheque" class="fa fa-list-alt m-l-xs text-danger"></i>
    <% } %>
    <% if (cta_cte > 0) { %>
      <i data-toggle="tooltip" title="Billetera Toque: $<%= cta_cte %>" class="fa fa-table m-l-xs text-info"></i>
    <% } %>
    <% if (tarjeta == 0 && efectivo == 0 && cheque == 0 && cta_cte == 0) { %>
      <i data-toggle="tooltip" title="Pago Efectivo" class="fa fa-money m-l-xs text-success"></i>
    <% } %>
  </td>
  <td><%= Number(total).toFixed(2) %></td>
  <td><%= Number(costo_envio).toFixed(2) %></td>
</script>