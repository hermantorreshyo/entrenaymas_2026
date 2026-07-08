<script type="text/template" id="procesar_pedidos_resultados_template">
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <h1 class="m-n font-thin h3"><i class="fa fa-dollar icono_principal"></i>Pedidos</h1>
  </div>
  <div class="wrapper-md ng-scope">
    <div class="panel panel-default">

      <div class="panel-heading clearfix">
        <div class="row">
          <div class="col-xs-12 sm-m-b">
            <% if (control.check("proveedores")>0) { %>
            <input placeholder="Proveedor" style="float:left; width: 200px; display: inline-block" type="text" class="form-control" id="procesar_pedidos_proveedores"/>
            <% } %>
            <div class="form-group fl w180">
              <div class="input-group">
                <input type="text" title="Fecha" id="procesar_pedidos_fecha" class="form-control no-model">
                <span class="input-group-btn">
                  <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                </span>
              </div>
            </div>
            <button class="btn btn-default fl buscar"><i class="fa fa-search"></i></button>
          </div>
        </div>
      </div>

      <div class="panel-body">
        <div class="b-a table-responsive">
          <table id="procesar_pedidos_tabla" class="table table-small sortable m-b-none default">
            <thead class="thead">
              <th class="">C&oacute;d. Int.</th>
              <th class="">C&oacute;d. Prov.</th>
              <th class="">Producto</th>
              <th class="">Stock Ant.</th>
              <th class="">Stock Act.</th>
              <th class="tar th_acciones">Pedido</th>
            </thead>
            <tbody class="tbody"></tbody>
          </table>
        </div>
        <div class="clearfix">
          <button class="btn crear_pedido btn-success m-t guardar">Generar pedido</button>
        </div>
      </div>

    </div>
  </div>
</div>
</script>


<script type="text/template" id="procesar_pedidos_item_resultados_template">
  <td class=""><%= codigo %></td>
  <td class=""><%= codigo_prov %></td>
  <td class="nombre"><%= articulo %></td>
  <td class=""><%= stock_ant %></td>
  <td class=""><%= stock_act %></td>
  <td class="tar">
    <input type="text" data-costo="<%= costo_final %>" data-id="<%= id_articulo %>" class="form-control cantidad no-model w100p" value="<%= Number(stock_ant - stock_act).toFixed(2) %>" />
  </td>
</script>