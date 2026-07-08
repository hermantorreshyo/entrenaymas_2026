<script type="text/template" id="estadisticas_articulos_web_template">
  <div id="estadisticas_articulos_web_container" class="col">
    <div class="bg-light titulo-pagina lter b-b wrapper-md">
      <div class="row">
        <div class="col-xs-12">
          <h1 class="m-n font-thin h3 text-black">
            <i class="fa fa-bar-chart icono_principal"></i>Estad&iacute;sticas
            / <b>Visitas de Articulos</b>
          </h1>
        </div>
      </div>
    </div>

    <div class="wrapper-md">

      <div class="panel panel-default">
        <div class="panel-body">
          <div class="">
            <div class="input-group pull-left" style="width: 140px;">
              <input type="text" id="estadisticas_articulos_web_fecha_desde" class="form-control">
              <span class="input-group-btn">
                <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
              </span>              
            </div>
            <div class="input-group pull-left" style="width: 140px;">
              <input type="text" id="estadisticas_articulos_web_fecha_hasta" class="form-control">
              <span class="input-group-btn">
                <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
              </span>              
            </div>
            <button class="btn btn-default buscar pull-left m-l-xs"><i class="fa fa-search"></i></button>
            <button class="btn btn-default imprimir pull-left m-l-xs"><i class="fa fa-print"></i></button>
          </div>
        </div>
      </div>

      <div class="panel panel-default">
        <div class="panel-body">
          <div class="b-a table-responsive">
            <table id="estadisticas_articulos_web_table" class="table table-small table-striped sortable m-b-none default footable">
              <thead>
                <tr>
                  <th class=""></th>
                  <th>Nombre</th>
                  <th>Visitas Web</th>
                  <th>Consultas</th>
                  <th>Ventas</th>
                </tr>
              </thead>
              <tbody></tbody>
              <tfoot class="bg-important">
                <tr>
                  <td></td>
                  <td></td>
                  <td id="estadisticas_articulos_web_visitas" class="bold">0</td>
                  <td id="estadisticas_articulos_web_ventas" class="bold">0</td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
      </div>

    </div>
  </div>
</script>

<script type="text/template" id="estadisticas_articulos_web_item_template">
  <td class="data hidden-xs">
    <% if (!isEmpty(path)) { %>
      <img src="<%= path %>?t=<%= Math.ceil(Math.random()*10000) %>" class="customcomplete-image"/>
    <% } %>
  </td>
  <td><span class="text-info"><%= nombre %></span></td>
  <td><%= Number(visitas).toFixed(0) %></td>
  <td><%= Number(consultas).toFixed(0) %></td>
  <td><%= Number(venta).toFixed(0) %></td>
</script>