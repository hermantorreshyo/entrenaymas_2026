<script type="text/template" id="estadisticas_tarjetas_template">
  <div id="estadisticas_tarjetas_container" class="col">

    <?php include("print_header.php"); ?>

    <div class="bg-light titulo-pagina lter b-b wrapper-md no-print">
      <div class="row">
        <div class="col-xs-12">
          <h1 class="m-n font-thin h3 text-black">
            <i class="fa fa-bar-chart icono_principal"></i>Estad&iacute;sticas
            / <b>Tarjetas</b>
          </h1>
        </div>
      </div>
    </div>

    <div class="wrapper-md">

      <div class="panel panel-default">
        <div class="panel-body no-print">
          <div class="">
            <div class="input-group pull-left" style="width: 140px;">
              <input type="text" id="estadisticas_tarjetas_fecha_desde" value="<%= fecha_desde %>" class="form-control">
              <span class="input-group-btn">
                <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
              </span>              
            </div>
            <div class="input-group pull-left" style="width: 140px;">
              <input type="text" id="estadisticas_tarjetas_fecha_hasta" value="<%= fecha_hasta %>" class="form-control">
              <span class="input-group-btn">
                <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
              </span>              
            </div>

            <% if (ID_SUCURSAL == 0 && typeof almacenes != "undefined") { %>
              <select class="form-control pull-left m-l-xs <%= (almacenes.length <= 1)?"dn":"" %>" style="display: inline-block; width: 160px;" id="estadisticas_tarjetas_sucursales">
                <option <%= (id_sucursal == 0)?"selected":"" %> value="0">Sucursal</option>
                <% for(var i=0; i< almacenes.length; i++) { %>
                  <% var alm = almacenes[i] %>
                  <option <%= (id_sucursal == alm.id)?"selected":"" %> value="<%= alm.id %>"><%= alm.nombre %></option>
                <% } %>
              </select>
            <% } %>

            <% if (control.check("puntos_venta")>0 || MEGASHOP == 1 || ID_EMPRESA == 421) { %>
              <select class="form-control pull-left m-l-xs" style="display: inline-block; width: 160px;" id="estadisticas_tarjetas_puntos_venta">
                <option <%= (id_punto_venta==-1)?"selected":"" %> value="-1">Punto de Venta</option>
                <% for(var i=0;i< puntos_venta.length;i++) { %>
                  <% var pv = puntos_venta[i] %>
                  <% if (ID_SUCURSAL == 0 || ID_SUCURSAL == pv.id_sucursal) { %>
                    <option <%= (id_punto_venta==pv.id)?"selected":"" %> value="<%= pv.id %>"><%= pv.nombre %></option>
                  <% } %>  
                <% } %>
              </select>
            <% } %>

            <button class="btn btn-default buscar pull-left m-l-xs"><i class="fa fa-search"></i> Buscar</button>
            <button class="btn btn-default imprimir pull-left m-l-xs"><i class="fa fa-print"></i></button>
          </div>
        </div>
      </div>
      
      <div class="row pagina">
        <div class="col-md-7">
          <div class="row row-sm text-center">
            <div class="col-xs-4">
              <div class="panel padder-v item bg-info" style="height: 140px">
                <div class="font-thin h2 text-white m-t-md">$ <%= Number(total_sin_interes).format(2) %></div>
                <span class="text-muted text-md pt10 db">Total s/interes</span>
              </div>
            </div>
            <div class="col-xs-4">
              <div class="block panel padder-v item bg-success" style="height: 140px">
                <div class="h2 font-thin text-white m-t-md"><%= Number(total_con_interes).format(2) %></div>
                <span class="text-muted text-md pt10 db">Total c/interes</span>
              </div>
            </div>
            <div class="col-xs-4">
              <div class="panel padder-v item" style="height: 140px">
                <div class="h2 font-thin m-t-md"><%= Number(porcentaje_venta_tarjetas).format(2) %>%</div>
                <span class="text-muted text-md pt10 db">% Ventas Tarjetas / Total</span>
              </div>
            </div>            
            <div class="col-xs-4">
              <div class="panel padder-v item" style="height: 140px">
                <div class="font-thin h2 m-t-md">$ <%= Number(interes).format(2) %></div>
                <span class="text-muted text-md pt10 db">Interes</span>
              </div>
            </div>
            <div class="col-xs-4">
              <div class="block panel padder-v item" style="height: 140px">
                <span class="font-thin h2 block m-t-md"><%= Number(cantidad_operaciones).format(0) %></span>
                <span class="text-muted text-md pt10 db">Cantidad de cupones</span>
              </div>
            </div>
            <div class="col-xs-4">
              <div class="block panel padder-v item" style="height: 140px">
                <span class="font-thin h2 block m-t-md"><%= Number(tarjeta_promedio).format(0) %></span>
                <span class="text-muted text-md pt10 db">Tarjeta Promedio</span>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-5 page-break">
          <div class="panel wrapper">
            <h4 class="font-thin m-t-none m-b text-muted">Distribución</h4>
            <div id="estadisticas_tarjetas_graficos" style="height: 235px;"></div>
          </div>
        </div>

      </div>
    
      <div class="pagina row">
        <div class="col-xs-12 col-md-6">
          <div class="panel panel-default" style="min-height:395px">
            <div class="panel-heading font-bold">Operaciones por tarjeta</div>
            <table class="estadisticas_tarjetas_table table-small table table-striped m-b-none">
              <thead>
                <tr>
                  <th>Tarjeta</th>
                  <th>Fecha</th>
                  <th>Lote</th>
                  <th>Cupon</th>
                  <th>Cuotas</th>
                  <th class="tar">s/Interes</th>
                  <th class="tar">Interes</th>
                  <th class="tar">Total</th>
                </tr>
              </thead>
              <tbody>
                <% for(var i=0;i< listado.length;i++) { %>
                  <% var o = listado[i]; %>
                  <tr>
                    <td><%= o.tarjeta %></td>
                    <td><%= o.fecha %></td>
                    <td><%= o.lote %></td>
                    <td><%= o.cupon %></td>
                    <td><%= o.cuotas %></td>
                    <td class="tar"><%= Number(o.importe).format() %></td>
                    <td class="tar"><%= Number(o.interes).format() %></td>
                    <td class="tar"><%= Number(o.total).format() %></td>
                  </tr>
                <% } %>
              </tbody>
            </table>
          </div>
        </div>
      </div>

    </div>
  </div>
</script>