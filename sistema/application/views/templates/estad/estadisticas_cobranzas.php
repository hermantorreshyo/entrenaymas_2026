<script type="text/template" id="estadisticas_cobranzas_template">
  <div id="estadisticas_cobranzas_container" class="col">
    <div class="bg-light titulo-pagina lter b-b wrapper-md">
      <div class="row">
        <div class="col-lg-6 col-sm-4 col-xs-12">
          <h1 class="m-n font-thin h3 text-black">
            <i class="fa fa-bar-chart icono_principal"></i>Estad&iacute;sticas
            / <b>Cobranzas</b>
          </h1>
        </div>
        <div class="col-lg-6 col-sm-8 col-xs-12">
          <div class="pull-right">
            <div class="w150 pull-left">
              <div class="input-group">
                <input type="text" id="estadisticas_cobranzas_fecha_desde" value="<%= fecha_desde %>" class="form-control">
                <span class="input-group-btn">
                  <button class="btn btn-cal btn-default"><i class="glyphicon glyphicon-calendar"></i></button>
                </span>
              </div>
            </div>
            <div class="w150 pull-left">
              <div class="input-group">
                <input type="text" id="estadisticas_cobranzas_fecha_hasta" value="<%= fecha_hasta %>" class="form-control">
                <span class="input-group-btn">
                  <button class="btn btn-cal btn-default"><i class="glyphicon glyphicon-calendar"></i></button>
                </span>
              </div>
            </div>
            <?php /*
            <% if (control.check("almacenes")>0) { %>
              <div class="w150 pull-left">
                <select class="form-control" id="estadisticas_cobranzas_sucursales">
                  <% if (ID_SUCURSAL != 0) { %>
                    <% for(var i=0;i< window.almacenes.length;i++) { %>
                      <% var o = almacenes[i]; %>
                      <% if (ID_SUCURSAL == o.id) { %>
                        <option selected value="<%= o.id %>"><%= o.nombre %></option>
                      <% } %>
                    <% } %>                    
                  <% } else { %>
                    <option <%= (id_sucursal == 0)?'selected':'' %> value="0">Todas</option>
                    <% for(var i=0;i< window.almacenes.length;i++) { %>
                      <% var o = almacenes[i]; %>
                      <option <%= (id_sucursal == o.id)?'selected':'' %> value="<%= o.id %>"><%= o.nombre %></option>
                    <% } %>
                  <% } %>
                </select>
              </div>
            <% } %>
            */ ?>
            <button class="btn btn-default buscar pull-left m-l-xs"><i class="fa fa-search"></i> Buscar</button>
            <button class="btn btn-default imprimir pull-left m-l-xs"><i class="fa fa-print"></i></button>
          </div>
        </div>
      </div>
    </div>

    <div class="wrapper-md">

      <div class="panel panel-default">
        <div class="panel-heading font-bold">
          <span>Resultados</span>
          <div class="btn-group dropdown pull-right">
            <button class="btn btn-sm btn-default dropdown-toggle btn-addon" data-toggle="dropdown">
              <i class="fa fa-cog"></i><span>Opciones</span>
              <span class="caret"></span>
            </button>
            <ul class="dropdown-menu">
              <li><a href="javascript:void" class="exportar">Exportar Excel</a></li>
            </ul>
          </div>
        </div>
        <div class="panel-body">
          <div class="b-a">
            <div style="min-height: 500px; max-height: 500px; overflow: auto;">
              <table id="estadisticas_cobranzas_table" class="table table-small table-striped m-b-none">
                <thead>
                  <tr>
                    <th>Fecha</th>
                    <th>Cliente</th>
                    <th class="tar w150">Efectivo</th>
                    <th class="tar w149">Cheques</th>
                    <th class="tar w149">Depositos</th>
                    <th class="tar w149">Tarjetas</th>
                    <th class="tar w150">Total</th>
                    <th class="w20 th_acciones"></th>
                  </tr>
                </thead>
                <tbody>
                  <% var total_pagos = 0 %>
                  <% var total_ef = 0 %>
                  <% var total_ch = 0 %>
                  <% var total_dep = 0 %>
                  <% var total_tar = 0 %>
                  <% for(var i=0;i< pagos.length;i++) { %>
                  <% var o = pagos[i]; %>
                  <tr>
                    <td><%= o.fecha %></td>
                    <td><a href="app/#cuentas_corrientes_clientes/<%= o.id_cliente %>" target="_blank" class="text-info"><%= o.nombre %></a></td>
                    <td class="tar">$ <%= Number(o.efectivo).toFixed(2) %></td>
                    <td class="tar">$ <%= Number(o.total_cheques).toFixed(2) %></td>
                    <td class="tar">$ <%= Number(o.total_depositos).toFixed(2) %></td>
                    <td class="tar">$ <%= Number(o.total_tarjetas).toFixed(2) %></td>
                    <% total_general = Number(parseFloat(o.efectivo) + parseFloat(o.total_cheques) + parseFloat(o.total_depositos) + parseFloat(o.total_tarjetas)).toFixed(2) %>
                    <td class="tar">$ <%= total_general %></td>
                    <td class="p0 tac"><a href="app/#cuentas_corrientes_clientes/<%= o.id_cliente %>" target="_blank"><i class="fa fa-search"></i></a></td>
                    <% total_ef += parseFloat(o.efectivo) %>
                    <% total_ch += parseFloat(o.total_cheques) %>
                    <% total_dep += parseFloat(o.total_depositos) %>
                    <% total_tar += parseFloat(o.total_tarjetas) %>
                    <% total_pagos += parseFloat(total_general) %>
                  </tr>
                  <% } %>
                </tbody>
                <tfoot>
                  <tr>
                    <td colspan="2" class="bold tar"></td>
                    <td class="bold tar">$ <%= Number(total_ef).toFixed(2) %></td>
                    <td class="bold tar">$ <%= Number(total_ch).toFixed(2) %></td>
                    <td class="bold tar">$ <%= Number(total_dep).toFixed(2) %></td>
                    <td class="bold tar">$ <%= Number(total_tar).toFixed(2) %></td>
                    <td class="bold tar">$ <%= Number(total_pagos).toFixed(2) %></td>
                  </tr>
                </tfoot>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</script>