<script type="text/template" id="estadisticas_articulos_sucursales_template">
  <div id="estadisticas_articulos_sucursales_container" class="col">
    <div class="bg-light titulo-pagina lter b-b wrapper-md">
      <div class="row">
        <div class="col-xs-12">
          <h1 class="m-n font-thin h3 text-black">
            <i class="fa fa-bar-chart icono_principal"></i>Estad&iacute;sticas
            / <b>Comparacion de articulos entre sucursales</b>
          </h1>
        </div>
      </div>
    </div>

    <div class="wrapper-md">

      <div class="panel panel-default">
        <div class="panel-body">
          <div class="">
            <div class="input-group pull-left" style="width: 140px;">
              <input type="text" id="estadisticas_articulos_sucursales_fecha_desde" class="form-control">
              <span class="input-group-btn">
                <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
              </span>              
            </div>
            <div class="input-group pull-left" style="width: 140px;">
              <input type="text" id="estadisticas_articulos_sucursales_fecha_hasta" class="form-control">
              <span class="input-group-btn">
                <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
              </span>              
            </div>

            <div class="pull-left" style="width: 140px;">
              <input type="text" id="estadisticas_articulos_sucursales_cantidad" placeholder="Cantidad" class="form-control">
            </div>

            <button class="btn btn-default buscar pull-left m-l-xs"><i class="fa fa-search"></i></button>

            <div class="btn-group dropdown pull-right">
              <button class="btn btn-default dropdown-toggle btn-addon" data-toggle="dropdown">
                <i class="fa fa-cog"></i><span><?php echo lang(array("es"=>"Opciones","en"=>"Options")); ?></span>
                <span class="caret"></span>
              </button>
              <ul class="dropdown-menu">
                <li><a href="javascript:void(0)" class="exportar">Exportar Excel</a></li>
              </ul>
            </div>
          </div>
        </div>
      </div>

      <div class="panel panel-default">
        <div class="panel-body">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <select class="form-control no-model" id="estadisticas_articulos_sucursales_1">
                  <% for(var i=0; i< almacenes.length; i++) { %>
                    <% var alm = almacenes[i] %>
                    <option value="<%= alm.id %>"><%= alm.nombre %></option>
                  <% } %>
                </select>
              </div>              
              <div class="b-a table-responsive">
                <table id="estadisticas_articulos_sucursales_table_1" class="table table-small table-striped sortable m-b-none default footable">
                  <thead>
                    <tr>
                      <th>Articulo</th>
                      <th class="tar">Cant.</th>
                      <th class="tar">Total</th>
                      <th class="tar">Ganancia</th>
                    </tr>
                  </thead>
                  <tbody></tbody>
                </table>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <select class="form-control no-model" id="estadisticas_articulos_sucursales_2">
                  <% for(var i=0; i< almacenes.length; i++) { %>
                    <% var alm = almacenes[i] %>
                    <option value="<%= alm.id %>"><%= alm.nombre %></option>
                  <% } %>
                </select>
              </div>              
              <div class="b-a table-responsive">
                <table id="estadisticas_articulos_sucursales_table_2" class="table table-small table-striped sortable m-b-none default footable">
                  <thead>
                    <tr>
                      <th>Articulo</th>
                      <th class="tar">Cant.</th>
                      <th class="tar">Total</th>
                      <th class="tar">Ganancia</th>
                    </tr>
                  </thead>
                  <tbody></tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</script>

<script type="text/template" id="estadisticas_articulos_sucursales_item_template">
  <td><span class="text-info"><%= nombre %></span></td>
  <td class="tar"><%= Number(cantidad).toFixed(2) %></td>
  <td class="tar"><%= Number(total).toFixed(2) %></td>
  <td class="tar"><%= Number(ganancia).toFixed(2) %></td>
</script>