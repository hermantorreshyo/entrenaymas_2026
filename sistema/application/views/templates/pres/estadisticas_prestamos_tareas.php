<script type="text/template" id="estadisticas_prestamos_tareas_resultados_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <h1 class="m-n font-thin h3">
    <i class="fa fa-bar-chart icono_principal"></i>Estad&iacute;sticas
    / <b>Tareas</b>
  </h1>
</div>  
<div class="wrapper-md">
  <div class="panel panel-default">
  
    <div class="panel-heading clearfix">

      <div class="pull-left form-inline">
        <div class="input-group">
          <input type="text" id="estadisticas_prestamos_tareas_desde" class="w120 form-control no-model">
          <span class="input-group-btn">
            <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
          </span>
        </div>
      </div>
      <div class="pull-left form-inline">
        <div class="input-group">
          <input type="text" id="estadisticas_prestamos_tareas_hasta" class="w120 form-control no-model">
          <span class="input-group-btn">
            <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
          </span>
        </div>
      </div>
      <button class="btn pull-left btn-default buscar"><i class="fa fa-search"></i></button>
      <div class="fr">
        <div class="btn-group dropdown">
          <button class="btn btn-default dropdown-toggle btn-addon" data-toggle="dropdown">
          <i class="fa fa-cog"></i><span>Opciones</span>
          <span class="caret"></span>
          </button>
          <ul class="dropdown-menu">
          <li><a href="javascript:void" class="exportar">Exportar Excel</a></li>
          </ul>
        </div>
      </div>
    </div>
    <div class="panel-body">
      <div class="b-a table-responsive">
        <table class="table table-small table-striped sortable m-b-none default footable">
          <thead>
            <tr>
              <th>Vendedor</th>
              <th>Sucursal</th>
              <th class="tar">Tareas</th>
              <th class="tar">Cumplidas</th>
              <th class="tar">%</th>
            </tr>
          </thead>
          <tbody id="estadisticas_prestamos_tareas_tbody" class="tbody"></tbody>
        </table>
      </div>
    </div>
  </div>
</div>
</script>


<script type="text/template" id="estadisticas_prestamos_tareas_item_resultados_template">
  <td class=""><span class="text-info"><%= usuario %></span></td>
  <td class=""><%= sucursal %></td>
  <td class="tar"><%= total_tareas %></td>
  <td class="tar"><%= total_tareas_cumplidas %></td>
  <td class="tar"><%= Number(porcentaje).toFixed(2) %>%</td>
</script>