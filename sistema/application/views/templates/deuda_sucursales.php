<script type="text/template" id="deuda_sucursales_resultados_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <div class="row clearfix padder">
  <h1 class="m-n font-thin h3 pull-left"><i class="fa fa-users icono_principal"></i>Proveedores
    / <b>Deuda por sucursal</b>
  </h1>
  </div>
</div>
<div class="wrapper-md ng-scope">
  <div class="panel panel-default">
    <div class="panel-heading clearfix">
      <div class="clearfix">
        <div style="display: inline-block">
          <div class="dib w200">
            <input type="hidden" id="deuda_sucursales_id_proveedor"/>
            <input title="Ingrese el codigo de Proveedor o comience a escribir parte del nombre" type="text" class="form-control action no-model fl" id="deuda_sucursales_proveedor" placeholder="Nombre o codigo de proveedor" value=""/>
          </div>
          <div class="dib w150">
            <div class="input-group">
              <input type="text" class="form-control" id="deuda_sucursales_fecha_desde" placeholder="Desde">
              <span class="input-group-btn">
                <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
              </span>
            </div>
          </div>
          <div class="dib w150">
            <div class="input-group">
              <input type="text" class="form-control" id="deuda_sucursales_fecha_hasta" placeholder="Hasta">
              <span class="input-group-btn">
                <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
              </span>  
            </div>
          </div>
          <div class="dib">
            <button tabindex="-1" type="button" class="btn pull-left btn-default generar"><i class="fa fa-search"></i></button>
          </div>
        </div>
        <div class="pull-right">
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
    <div class="panel-body">
      <div class="b-a table-responsive">
        <table class="table table-small table-striped sortable m-b-none default footable">
          <thead>
            <th class="sorting" data-sort-by="nombre">Sucursal</th>
            <th class="tar">Saldo ant.</th>
            <th class="tar">Compra</th>
            <th class="sorting" data-sort-by="ultima_compra">Fecha Ult.</th>
            <th class="tar">Pago</th>
            <th class="sorting" data-sort-by="ultimo_pago">Fecha Ult.</th>
            <th class="tar">Saldo</th>
            <th class="th_acciones"></th>
          </thead>
          <tbody id="deuda_sucursales_tbody" class="tbody">
            <tr><td colspan="20">Seleccione una fecha y haga click en Buscar.</td></tr>
          </tbody>
          <tfoot>
            <tr>
              <td class="fs16 bold fila_alerta tar">Totales</td>
              <td id="deuda_sucursales_total_saldo" class="fs16 bold tar fila_alerta"></td>
              <td id="deuda_sucursales_total_compras" class="fs16 bold tar fila_alerta"></td>
              <td class="fila_alerta"></td>
              <td id="deuda_sucursales_total_pagos" class="fs16 bold tar fila_alerta"></td>
              <td class="fila_alerta"></td>
              <td id="deuda_sucursales_total" class="fs16 bold tar fila_alerta"></td>
              <td class="fila_alerta"></td>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </div>
</div>
</script>

<script type="text/template" id="deuda_sucursales_item_resultados_template">
  <td><span class="text-info"><%= nombre %></span></td>
  <td class="tar"><%= Number(saldo).toFixed(2) %></td>
  <td class="tar"><%= (Number(total_compras) == 0) ? '' : Number(total_compras).toFixed(2) %></td>
  <td><%= ultima_compra %></td>
  <td class="tar"><%= (Number(total_pagos) == 0) ? '' : Number(total_pagos).toFixed(2) %></td>
  <td><%= ultimo_pago %></td>
  <td class="tar"><%= Number(Number(saldo) + Number(total_compras) + Number(total_pagos)).toFixed(2) %></td>
  <td><i title="Ver cuenta corriente" class="fa fa-search edit text-dark" /></td>
</script>