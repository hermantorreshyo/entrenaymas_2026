<script type="text/template" id="listado_saldos_clientes_resultados_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <div class="row clearfix padder">
  <h1 class="m-n font-thin h3 pull-left"><i class="fa fa-user icono_principal"></i>Clientes</h1>
  </div>
</div>
<div class="wrapper-md ng-scope">
  <div class="panel panel-default">

    <?php $active = "listado_saldos_clientes"; include("cli/clientes_menu.php"); ?>

    <div class="panel-heading clearfix">
      <div class="row">
        <div class="col-md-6 col-lg-3 sm-m-b">

          <div class="input-group">
            <input type="text" class="form-control" id="listado_saldos_clientes_fecha" placeholder="Fecha">
            <span class="input-group-btn">
              <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
            </span>        
            <span class="input-group-btn">
              <button tabindex="-1" type="button" class="btn btn-default generar"><i class="fa fa-search"></i></button>
            </span>        

            <span class="input-group-btn">
              <button tabindex="-1" class="btn btn-default advanced-search-btn btn-addon btn-addon-2 ml5"><i class="fa fa-filter"></i><span><?php echo lang(array("es"=>"Filtros","en"=>"Filters")); ?></span></button>
            </span>
          </div>

        </div>
        <div class="col-md-6 col-lg-offset-3 col-lg-6 text-right">
          <div class="btn-group dropdown">
            <button class="btn btn-default dropdown-toggle btn-addon" data-toggle="dropdown">
            <i class="fa fa-cog"></i><span>Opciones</span>
            <span class="caret"></span>
            </button>
            <ul class="dropdown-menu">
            <li><a href="javascript:void" class="exportar">Exportar Excel</a></li>
            <li class="divider"></li>
            <li><a onclick="workspace.cambiar_estado()" href="javascript:void(0)">Modo supervisor</a></li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <div class="advanced-search-div bg-light dk">
      <div class="wrapper oh">
        <h4 class="m-t-xs m-b"><i class="fa fa-filter"></i> <?php echo lang(array("es"=>"Filtros:","en"=>"Filters:")); ?></h4>
        <div class="row pl10 pr10">
          <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
            <div class="input-group">
              <input type="text" class="form-control" id="listado_saldos_clientes_fecha_desde" placeholder="Desde">
              <span class="input-group-btn">
                <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
              </span>
            </div>
          </div>          
          <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
            <select id="listado_saldos_clientes_agrupado_por" class="form-control">
              <option value="">Agrupado por</option>
              <option value="vendedor">Vendedor</option>
            </select>
          </div>
          <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
            <select id="listado_saldos_clientes_etiquetas" class="w200 form-control no-model"></select>
          </div>   
          <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">       
            <label class="i-checks mt5">
              <input type="checkbox" checked="checked" id="listado_saldos_filtrar_en_cero">
              <i></i>Filtrar cuentas en cero
            </label>
          </div>
          <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
            <button id="listado_saldos_clientes_buscar" class="btn generar btn-default btn-dark btn-block"><i class="fa fa-search m-r-xs"></i> <?php echo lang(array("es"=>"Buscar","en"=>"Search")); ?></button>
          </div>
        </div>
      </div>
    </div>
    <div class="panel-body">
      <div class="b-a table-responsive">
        <table class="table table-small table-striped sortable m-b-none default footable">
          <thead>
            <th class="sorting" data-sort-by="id">Codigo</th>
            <th class="sorting" data-sort-by="nombre">Cliente</th>
            <th>Observaciones</th>
            <th class="tar sorting" data-sort-by="saldo">Saldo&nbsp;&nbsp;&nbsp;</th>
            <th class="th_acciones"></th>
          </thead>
          <tbody id="listado_saldos_clientes_tbody" class="tbody">
            <tr><td colspan="20">Seleccione una fecha y haga click en Buscar.</td></tr>
          </tbody>
          <tfoot>
            <tr>
              <td class="fila_alerta fs16 bold tar" colspan="3">Totales</td>
              <td id="listado_saldos_clientes_total" class="fs14 bold tar fila_alerta"></td>
              <td class="fila_alerta"></td>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </div>
</div>
</script>

<script type="text/template" id="listado_saldos_clientes_item_resultados_template">
  <% if (codigo < 0) { %>
  <td colspan="5" class="bg-light lter bold"><%= nombre %></td>
  <% } else { %>
  <td><%= codigo %></td>
  <td><%= nombre %></td>
  <td><%= observaciones %></td>
  <td class="tar"><%= Number(saldo).toFixed(2) %></td>
  <td><i title="Ver cuenta corriente" class="fa fa-search edit text-dark" /></td>
  <% } %>
</script>