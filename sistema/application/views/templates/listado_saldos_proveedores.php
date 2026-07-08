<script type="text/template" id="listado_saldos_proveedores_resultados_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <div class="row clearfix padder">
    <h1 class="m-n font-thin h3 pull-left"><i class="fa fa-user icono_principal"></i>Proveedores</h1>
  </div>
</div>
<div class="wrapper-md ng-scope">
  <div class="panel panel-default">

    <?php $active = "listado_saldos_proveedores"; include("cli/proveedores_menu.php"); ?>

    <div class="panel-heading clearfix">
      <div class="row">
        <div class="col-md-9 sm-m-b">
          <div class="row pl10 pr10">
            <div class="col-sm-4 col-lg-2 col-xs-12 pr5 pl5">
              <div class="input-group">
                <input type="text" class="form-control" id="listado_saldos_proveedores_fecha" placeholder="Fecha">
                <span class="input-group-btn">
                  <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                </span>
              </div>
            </div>
            <div class="col-sm-4 col-xs-12 pr5 pl5" style="<%= (window.almacenes.length <= 1)?"display:none":"" %>">
              <select class="form-control w180" id="listado_saldos_proveedores_sucursales">
                <option value="0">Sucursal</option>
                <% for(var i=0;i< window.almacenes.length;i++) { %>
                  <% var o = almacenes[i]; %>
                  <option value="<%= o.id %>"><%= o.nombre %></option>
                <% } %>
              </select>
            </div>
            <div class="col-sm-4 col-xs-12 pr5 pl5">
              <button tabindex="-1" type="button" class="btn btn-default generar"><i class="fa fa-search"></i></button>
              <button tabindex="-1" class="btn btn-default advanced-search-btn btn-addon ml5"><i class="fa fa-filter"></i><span><?php echo lang(array("es"=>"Filtros","en"=>"Filters")); ?></span></button>
            </div>
          </div>
        </div>
        <div class="col-md-3 text-right">
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
              <input type="text" class="form-control" id="listado_saldos_proveedores_fecha_desde" placeholder="Desde">
              <span class="input-group-btn">
                <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
              </span>
            </div>
          </div>
          <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
            <select class="form-control no-model" id="listado_saldos_tipo_proveedor">
              <option value="0">Todos</option>
              <option <%= (ID_EMPRESA == 134)?"selected":"" %> value="1">Mercaderia</option>
              <option value="2">Alquiler</option>
              <option value="3">Profesional</option>
              <option value="4">Otros</option>
            </select>
          </div>
          <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
            <label class="i-checks mt5">
            <input type="checkbox" checked="checked" id="listado_saldos_filtrar_en_cero">
            <i></i>Filtrar cuentas en cero
            </label>
          </div>
          <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
            <button class="generar btn btn-block btn-dark"><i class="fa fa-search"></i> Buscar</button>
          </div>
        </div>
      </div>
    </div>
    <div class="panel-body">
      <div class="b-a table-responsive">
        <table class="table table-small table-striped sortable m-b-none default footable">
          <thead>
            <th class="sorting" data-sort-by="id">C&oacute;digo</th>
            <th class="sorting" data-sort-by="nombre">Proveedor</th>
            <th class="tar sorting" data-sort-by="saldo">Saldo&nbsp;&nbsp;&nbsp;</th>
            <th class="tar"></th>
            <th class="th_acciones"></th>
          </thead>
          <tbody id="listado_saldos_proveedores_tbody" class="tbody">
            <tr><td colspan="20">Seleccione una fecha y haga click en Buscar.</td></tr>
          </tbody>
          <tfoot>
            <tr>
              <td colspan="2" class="fs16 bold fila_alerta tar">Totales</td>
              <td id="listado_saldos_proveedores_total" class="fs16 bold tar fila_alerta"></td>
              <td class="fila_alerta"></td>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </div>
</div>
</script>

<script type="text/template" id="listado_saldos_proveedores_item_resultados_template">
  <td><%= codigo %></td>
  <td><%= nombre %></td>
  <td class="tar"><%= Number(saldo).toFixed(2) %></td>
  <td class="tar"><%= (total_pagos == 0) ? '' : Number(total_pagos).toFixed(2) %></td>
  <td><i title="Ver cuenta corriente" class="fa fa-search edit text-dark" /></td>
</script>