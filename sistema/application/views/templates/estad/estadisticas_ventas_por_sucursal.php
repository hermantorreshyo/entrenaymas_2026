<script type="text/template" id="estadisticas_ventas_por_sucursal_template">
  <div id="estadisticas_ventas_por_sucursal_container" class="col">
    <div class="bg-light titulo-pagina lter b-b wrapper-md">
      <div class="row">
        <div class="col-xs-12">
          <h1 class="m-n font-thin h3 text-black">
            <i class="fa fa-bar-chart icono_principal"></i>Estad&iacute;sticas
            / <b>Ventas por Sucursal</b>
          </h1>
        </div>
      </div>
    </div>

    <div class="wrapper-md">

      <div class="panel panel-default">
        <div class="panel-body">
          <div class="">
            <div class="input-group pull-left" style="width: 140px;">
              <input type="text" id="estadisticas_ventas_por_sucursal_fecha_desde" class="form-control">
              <span class="input-group-btn">
                <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
              </span>              
            </div>
            <div class="input-group pull-left" style="width: 140px;">
              <input type="text" id="estadisticas_ventas_por_sucursal_fecha_hasta" class="form-control">
              <span class="input-group-btn">
                <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
              </span>              
            </div>
            <button class="btn btn-default buscar pull-left m-l-xs"><i class="fa fa-search"></i></button>

            <div class="btn-group dropdown pull-right">
              <button class="btn btn-default dropdown-toggle btn-addon" data-toggle="dropdown">
                <i class="fa fa-cog"></i><span><?php echo lang(array("es"=>"Opciones","en"=>"Options")); ?></span>
                <span class="caret"></span>
              </button>
              <ul class="dropdown-menu">
                <li><a href="javascript:void(0)" class="imprimir">Imprimir</a></li>
                <li><a href="javascript:void(0)" class="exportar">Exportar Excel</a></li>
              </ul>
            </div>
          </div>
        </div>
      </div>

      <div class="panel panel-default">
        <div class="panel-body">
          <div class="b-a table-responsive">
            <table id="estadisticas_ventas_por_sucursal_table" class="table table-small table-striped sortable m-b-none default footable">
              <thead>
                <tr>
                  <th>Sucursal</th>
                  <th class="tar">Venta</th>
                  <th class="tar w120">Var.</th>
                  <th class="tar">%</th>
                  <th class="tar">Tickets</th>
                  <th class="tar">Tkt. Prom.</th>
                  <th class="tar">CMV</th>
                  <th class="tar">Ganancia</th>
                  <th class="tar">% Marc.</th>
                  <th class="tar">Oferta</th>
                  <th class="tar">Bonificacion</th>
                </tr>
              </thead>
              <tbody></tbody>
              <tfoot class="bg-important">
                <tr>
                  <td class="tar"></td>
                  <td class="tar" id="estadisticas_ventas_por_sucursal_total" class="bold">$ 0.00</td>
                  <td class="tar"></td>
                  <td class="tar"></td>
                  <td class="tar" id="estadisticas_ventas_por_sucursal_cantidad" class="bold">0</td>
                  <td class="tar"></td>
                  <td class="tar" id="estadisticas_ventas_por_sucursal_costo" class="bold">$ 0.00</td>
                  <td class="tar" id="estadisticas_ventas_por_sucursal_ganancia" class="bold">$ 0.00</td>
                  <td class="tar"></td>
                  <td class="tar" id="estadisticas_ventas_por_sucursal_oferta" class="bold">$ 0.00</td>
                  <td class="tar" id="estadisticas_ventas_por_sucursal_descuento" class="bold">$ 0.00</td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
      </div>

    </div>
  </div>
</script>

<script type="text/template" id="estadisticas_ventas_por_sucursal_item_template">
  <td><span class="text-info"><%= sucursal %></span></td>
  <td class="tar"><%= Number(total).toFixed(2) %></td>
  <td class="tar"><%= (variacion_venta > 0)?'<i class="fa fa-arrow-up text-success"></i>':(variacion_venta < 0 ? '<i class="fa fa-arrow-down text-danger"></i>' : '') %> <span data-toggle="tooltip" title="Año anterior: $ <%= venta_pasada %>"><%= Number(variacion_venta).toFixed(2) %>%</span></td>
  <td class="tar"><%= Number(porcentaje).toFixed(3) %>%</td>
  <td class="tar"><%= Number(cantidad).toFixed(2) %></td>
  <td class="tar"><%= Number(ticket_promedio).toFixed(2) %></td>
  <td class="tar"><%= Number(costo).toFixed(2) %></td>
  <td class="tar"><%= Number(ganancia).toFixed(2) %></td>
  <td class="tar"><%= Number(marcacion).toFixed(3) %> %</td>
  <td class="tar"><%= Number(oferta).toFixed(2) %></td>
  <td class="tar"><%= Number(descuento).toFixed(2) %></td>
</script>