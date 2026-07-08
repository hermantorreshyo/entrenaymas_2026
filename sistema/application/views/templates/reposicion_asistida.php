<script type="text/template" id="reposicion_asistida_edit_panel_template">
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <h1 class="m-n font-thin h3"><i class="fa fa-tags icono_principal"></i>Reposicion Asistida</h1>
  </div>
  <div class="wrapper-md pb0">
    <div class="centrado">
      <div class="panel panel-info pull-in">
        <div class="panel-heading font-bold">Listado de Productos</div>
        <div class="panel-body">
          <div class="b-a" style="overflow: auto; height: 250px; margin-top: 15px;">
            <table class="table table-small sortable m-b-none default footable">
              <thead class="bg-light">
                <tr>
                  <th>Codigo</th>
                  <th>Cod. Prov.</th>
                  <th>Articulo</th>
                  <th>Stock Actual</th>
                  <th>Stock Minimo</th>
                  <th>Venta</th>
                  <th>Sugerido</th>
                  <th>Pedido</th>
                </tr>
              </thead>
              <tbody>
                <% for(var i=0;i< results.length;i++) { %>
                  <% var r = results[i] %>
                  <tr>
                    <td><%= r.codigo_articulo %></td>
                    <td><%= r.codigo_prov %></td>
                    <td><span class="text-info"><%= r.articulo %></span></td>
                    <td><%= r.stock_actual %></td>
                    <td><%= r.stock_minimo %></td>
                    <td><%= r.venta %></td>
                    <td><%= r.sugerido %></td>
                    <td><input type="text" data-id="<%= r.id_articulo %>" class="form-control pedido_item no-model" value="<%= r.sugerido %>"/></td>
                  </tr>
                <% } %>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="oh m-t m-b tar pull-in">
        <button class="btn btn-success generar btn-addon"><i class="icon fa fa-plus"></i>Generar Pedido</button>
      </div>

    </div>
  </div>
</script>

<script type="text/template" id="reposicion_asistida_panel_template">
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <h1 class="m-n font-thin h3"><i class="fa fa-tags icono_principal"></i>Reposicion Asistida</h1>
  </div>
  <div class="wrapper-md ng-scope">
    <div class="panel panel-default">
    
      <div class="panel-heading oh">
        <div class="row">

          <div class="col-xs-12 sm-m-b">
            <div style="display: inline-block">
              <div class="input-group" style="width: 250px;">
                <input type="text" id="reposicion_asistida_buscar" value="<%= window.reposicion_asistida_filter %>" placeholder="<?php echo lang(array("es"=>"Buscar","en"=>"Search")); ?>..." autocomplete="off" class="form-control">
              </div>
            </div>
            <div style="display: inline-block">
              <div class="input-group" style="width: 150px;">
                <select class="form-control" id="reposicion_asistida_buscar_sucursales">
                  <% if (ID_SUCURSAL != 0) { %>
                    <% for(var i=0;i< window.almacenes.length;i++) { %>
                      <% var o = almacenes[i]; %>
                      <% if (ID_SUCURSAL == o.id) { %>
                        <option value="<%= o.id %>"><%= o.nombre %></option>
                      <% } %>  
                    <% } %>
                  <% } else { %>
                    <% for(var i=0;i< window.almacenes.length;i++) { %>
                      <% var o = almacenes[i]; %>
                      <% if (!(ID_EMPRESA == 868 && o.id == 531)) { %>
                        <option <%= (window.reposicion_asistida_id_sucursal == o.id)?"selected":"" %> value="<%= o.id %>"><%= o.nombre %></option>
                      <% } %>
                    <% } %>
                  <% } %>
                </select>                     
              </div>
            </div>
            <div style="display: inline-block">
              <div class="input-group">
                <button class="buscar btn btn-default"><i class="fa fa-search"></i> Buscar</button>
              </div>
            </div>

            <div class="fr">
              <a target="_blank" href="https://www.varcreative.com/sistema/reposicion_asistida/function/calcular/" class="btn btn-default">Recalcular</a>
            </div>              

          </div>

        </div>
      </div>
      <div class="panel-body">
        <div class="b-a table-responsive">
          <table id="reposicion_asistida_table" class="table table-small table-striped sortable m-b-none default footable">
            <thead>
              <tr>
                <th>Proveedor</th>
                <th>Clase</th>
                <th>Ult. Pedido</th>
                <th>Estado</th>
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


<script type="text/template" id="reposicion_asistida_item">
  <td class="ver"><span class='text-info'><%= nombre %></span></td>
  <td class="ver">
    <% if (tipo == 10) { %>
      <span class="label bg-success">Clase A</span>
    <% } else if (tipo == 5) { %>
      <span class="label bg-warning">Clase B</span>
    <% } else if (tipo == 3) { %>
      <span class="label bg-light dk">Clase C</span>
    <% } %>
  </td>
  <td class="ver"><%= (fecha_ultimo_pedido != '0000-00-00') ? fecha_ultimo_pedido : "" %></td>
  <td class="ver">
    <% if (pedir == 1) { %><span class="label bg-danger">Reponer</span><% } %>
  </td>
</script>