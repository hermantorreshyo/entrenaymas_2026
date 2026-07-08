<script type="text/template" id="deuda_proveedores_resultados_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <h1 class="m-n font-thin h3"><i class="fa fa-user icono_principal"></i>Contactos / <b>Proveedores</b></h1>
</div>
<div class="wrapper-md ng-scope">
  <div class="panel panel-default">

    <?php $active = "deuda_proveedores"; include("cli/proveedores_menu.php"); ?>

    <div class="panel-heading clearfix">
      <div class="clearfix">
        <div style="display: inline-block">
          <div class="fl w150">
            <div class="input-group">
              <input type="text" class="form-control" id="deuda_proveedores_fecha_desde" placeholder="Desde">
              <span class="input-group-btn">
                <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
              </span>
            </div>
          </div>
          <?php /*
          <div class="fl w150">
            <div class="input-group">
              <input type="text" class="form-control" id="deuda_proveedores_fecha_hasta" placeholder="Hasta">
              <span class="input-group-btn">
                <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
              </span>  
            </div>
          </div>
          */ ?>
          <% if (control.check("almacenes")>0 || MEGASHOP == 1 || ID_EMPRESA == 224) { %>
            <div class="fl w150">
              <select class="form-control" id="deuda_proveedores_sucursales">
                <% if (ID_SUCURSAL != 0) { %>
                  <% for(var i=0;i< window.almacenes.length;i++) { %>
                    <% var o = almacenes[i]; %>
                    <% if (ID_SUCURSAL == o.id) { %>
                      <option selected value="<%= o.id %>"><%= o.nombre %></option>
                    <% } %>
                  <% } %>                    
                <% } else { %>
                  <option value="0">Sucursal</option>
                  <% for(var i=0;i< window.almacenes.length;i++) { %>
                    <% var o = almacenes[i]; %>
                    <option value="<%= o.id %>"><%= o.nombre %></option>
                  <% } %>
                <% } %>
              </select>
            </div>
          <% } %>
          <div class="fl">
            <button tabindex="-1" type="button" class="btn pull-left btn-default generar"><i class="fa fa-search"></i></button>
            <button tabindex="-1" class="btn btn-default pull-left advanced-search-btn"><i class="fa fa-angle-double-down"></i></button>
          </div>
        </div>
        <div class="pull-right">
          <div class="btn-group dropdown">
            <button class="btn btn-default dropdown-toggle btn-addon" data-toggle="dropdown">
              <i class="fa fa-cog"></i><span>Opciones</span>
              <span class="caret"></span>
            </button>
            <ul class="dropdown-menu pull-right">
              <li><a href="javascript:void" class="exportar">Exportar Excel</a></li>
            </ul>
          </div>
        </div>
      </div>
      <div class="advanced-search-div bg-light dk">
        <div class="wrapper oh">
          <h4 class="m-t-xs"><i class="fa fa-search"></i> B&uacute;squeda Avanzada:</h4>
          <div class="form-inline">
            <select style="display: inline-block;" class="form-control no-model" id="deuda_tipo_proveedor">
              <option value="0">Todos</option>
              <option <%= (ID_EMPRESA == 134)?"selected":"" %> value="1">Mercaderia</option>
              <option value="2">Alquiler</option>
              <option value="3">Profesional</option>
              <option value="4">Otros</option>
            </select>
            <div style="display: inline-block; margin-right: 15px; margin-left: 10px;">
              <label class="i-checks">
              <input type="checkbox" checked="checked" id="deuda_filtrar_en_cero">
              <i></i>Filtrar cuentas en cero
              </label>
            </div>
            <div class="form-group">
              <button class="generar btn btn-default"><i class="fa fa-search"></i> Buscar</button>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="panel-body">
      <div class="b-a table-responsive">
        <table class="table table-small table-striped sortable m-b-none default footable">
          <thead>
            <th class="sorting" data-sort-by="id">Cod.</th>
            <th class="sorting" data-sort-by="nombre">Proveedor</th>
            <th class="tar">+90</th>
            <th class="tar">90</th>
            <th class="tar">60</th>
            <th class="tar">30</th>
            <th class="tar">Saldo</th>
            <th class="tar">Ult.Compra</th>
            <th class="sorting" data-sort-by="ultima_compra">Fecha</th>
            <th class="tar">Ult.Pago</th>
            <th class="sorting" data-sort-by="ultimo_pago">Fecha</th>
          </thead>
          <tbody id="deuda_proveedores_tbody" class="tbody">
            <tr><td colspan="20">Seleccione una fecha y haga click en Buscar.</td></tr>
          </tbody>
          <tfoot>
            <tr>
              <td colspan="2" class="fs16 bold fila_alerta tar">Totales</td>
              <td id="deuda_proveedores_total_saldo_mas_90" class="fs16 bold tar fila_alerta"></td>
              <td id="deuda_proveedores_total_saldo_90" class="fs16 bold tar fila_alerta"></td>
              <td id="deuda_proveedores_total_saldo_60" class="fs16 bold tar fila_alerta"></td>
              <td id="deuda_proveedores_total_saldo_30" class="fs16 bold tar fila_alerta"></td>
              <td id="deuda_proveedores_total_saldo" class="fs16 bold tar fila_alerta"></td>
              <td id="deuda_proveedores_total_compras" class="fs16 bold tar fila_alerta"></td>
              <td class="fila_alerta"></td>
              <td id="deuda_proveedores_total_pagos" class="fs16 bold tar fila_alerta"></td>
              <td class="fila_alerta"></td>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </div>
</div>
</script>

<script type="text/template" id="deuda_proveedores_item_resultados_template">
  <td><%= codigo %></td>
  <td><span class="text-info"><%= nombre %></span></td>
  <td class="tar <%= (dias_pago <= 120)?"text-danger":"" %>"><%= (Number(saldo_mas_90) == 0) ? '' : Number(saldo_mas_90).format(2) %></td>
  <td class="tar <%= (dias_pago <= 90)?"text-danger":"" %>"><%= (Number(saldo_90) == 0) ? '' : Number(saldo_90).format(2) %></td>
  <td class="tar <%= (dias_pago <= 60)?"text-danger":"" %>"><%= (Number(saldo_60) == 0) ? '' : Number(saldo_60).format(2) %></td>
  <td class="tar <%= (dias_pago <= 30)?"text-danger":"" %>"><%= (Number(saldo_30) == 0) ? '' : Number(saldo_30).format(2) %></td>
  <td class="tar <%= (dias_pago <= 0)?"text-danger":"" %>"><%= (Number(saldo) == 0) ? '' : Number(saldo).format(2) %></td>
  <td class="tar"><%= (Number(monto_ultima_compra) == 0) ? '' : Number(monto_ultima_compra).format(2) %></td>
  <td><%= ultima_compra %></td>
  <td class="tar"><%= (Number(monto_ultimo_pago) == 0) ? '' : Number(monto_ultimo_pago).format(2) %></td>
  <td><%= ultimo_pago %></td>
</script>


<?php /*

BACKUP DE CUANDO TENIA LAS DOS FECHAS


<script type="text/template" id="deuda_proveedores_resultados_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <h1 class="m-n font-thin h3"><i class="fa fa-user icono_principal"></i>Contactos / <b>Proveedores</b></h1>
</div>
<div class="wrapper-md ng-scope">
  <div class="panel panel-default">

    <?php $active = "deuda_proveedores"; include("cli/proveedores_menu.php"); ?>

    <div class="panel-heading clearfix">
      <div class="clearfix">
        <div style="display: inline-block">
          <div class="fl w150">
            <div class="input-group">
              <input type="text" class="form-control" id="deuda_proveedores_fecha_desde" placeholder="Desde">
              <span class="input-group-btn">
                <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
              </span>
            </div>
          </div>
          <div class="fl w150">
            <div class="input-group">
              <input type="text" class="form-control" id="deuda_proveedores_fecha_hasta" placeholder="Hasta">
              <span class="input-group-btn">
                <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
              </span>  
            </div>
          </div>
          <% if (control.check("almacenes")>0 || MEGASHOP == 1 || ID_EMPRESA == 224) { %>
            <div class="fl w150">
              <select class="form-control" id="deuda_proveedores_sucursales">
                <% if (ID_SUCURSAL != 0) { %>
                  <% for(var i=0;i< window.almacenes.length;i++) { %>
                    <% var o = almacenes[i]; %>
                    <% if (ID_SUCURSAL == o.id) { %>
                      <option selected value="<%= o.id %>"><%= o.nombre %></option>
                    <% } %>
                  <% } %>                    
                <% } else { %>
                  <option value="0">Sucursal</option>
                  <% for(var i=0;i< window.almacenes.length;i++) { %>
                    <% var o = almacenes[i]; %>
                    <option value="<%= o.id %>"><%= o.nombre %></option>
                  <% } %>
                <% } %>
              </select>
            </div>
          <% } %>
          <div class="fl">
            <button tabindex="-1" type="button" class="btn pull-left btn-default generar"><i class="fa fa-search"></i></button>
            <button tabindex="-1" class="btn btn-default pull-left advanced-search-btn"><i class="fa fa-angle-double-down"></i></button>
          </div>
        </div>
        <div class="pull-right">
          <div class="btn-group dropdown">
            <button class="btn btn-default dropdown-toggle btn-addon" data-toggle="dropdown">
              <i class="fa fa-cog"></i><span>Opciones</span>
              <span class="caret"></span>
            </button>
            <ul class="dropdown-menu pull-right">
              <li><a href="javascript:void" class="exportar">Exportar Excel</a></li>
            </ul>
          </div>
        </div>
      </div>
      <div class="advanced-search-div bg-light dk">
        <div class="wrapper oh">
          <h4 class="m-t-xs"><i class="fa fa-search"></i> B&uacute;squeda Avanzada:</h4>
          <div class="form-inline">
            <select style="display: inline-block;" class="form-control no-model" id="deuda_tipo_proveedor">
              <option value="0">Todos</option>
              <option <%= (ID_EMPRESA == 134)?"selected":"" %> value="1">Mercaderia</option>
              <option value="2">Alquiler</option>
              <option value="3">Profesional</option>
              <option value="4">Otros</option>
            </select>
            <div style="display: inline-block; margin-right: 15px; margin-left: 10px;">
              <label class="i-checks">
              <input type="checkbox" checked="checked" id="deuda_filtrar_en_cero">
              <i></i>Filtrar cuentas en cero
              </label>
            </div>
            <div class="form-group">
              <button class="generar btn btn-default"><i class="fa fa-search"></i> Buscar</button>
            </div>
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
            <th class="tar">+90</th>
            <th class="tar">90</th>
            <th class="tar">60</th>
            <th class="tar">30</th>
            <th class="tar">Saldo</th>
            <th class="tar">Compra</th>
            <th class="sorting" data-sort-by="ultima_compra">Fecha Ult.</th>
            <th class="tar">Pago</th>
            <th class="sorting" data-sort-by="ultimo_pago">Fecha Ult.</th>
            <th class="tar">Saldo</th>
            <th class="th_acciones"></th>
          </thead>
          <tbody id="deuda_proveedores_tbody" class="tbody">
            <tr><td colspan="20">Seleccione una fecha y haga click en Buscar.</td></tr>
          </tbody>
          <tfoot>
            <tr>
              <td colspan="2" class="fs16 bold fila_alerta tar">Totales</td>
              <td id="deuda_proveedores_total_saldo_mas_90" class="fs16 bold tar fila_alerta"></td>
              <td id="deuda_proveedores_total_saldo_90" class="fs16 bold tar fila_alerta"></td>
              <td id="deuda_proveedores_total_saldo_60" class="fs16 bold tar fila_alerta"></td>
              <td id="deuda_proveedores_total_saldo_30" class="fs16 bold tar fila_alerta"></td>
              <td id="deuda_proveedores_total_saldo" class="fs16 bold tar fila_alerta"></td>
              <td id="deuda_proveedores_total_compras" class="fs16 bold tar fila_alerta"></td>
              <td class="fila_alerta"></td>
              <td id="deuda_proveedores_total_pagos" class="fs16 bold tar fila_alerta"></td>
              <td class="fila_alerta"></td>
              <td id="deuda_proveedores_total" class="fs16 bold tar fila_alerta"></td>
              <td class="fila_alerta"></td>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </div>
</div>
</script>

<script type="text/template" id="deuda_proveedores_item_resultados_template">
  <td><%= codigo %></td>
  <td><span class="text-info"><%= nombre %></span></td>
  <td class="tar"><%= Number(saldo_mas_90).toFixed(2) %></td>
  <td class="tar"><%= Number(saldo_90).toFixed(2) %></td>
  <td class="tar"><%= Number(saldo_60).toFixed(2) %></td>
  <td class="tar"><%= Number(saldo_30).toFixed(2) %></td>
  <td class="tar"><%= Number(saldo).toFixed(2) %></td>
  <td class="tar"><%= (Number(total_compras) == 0) ? '' : Number(total_compras).toFixed(2) %></td>
  <td><%= ultima_compra %></td>
  <td class="tar"><%= (Number(total_pagos) == 0) ? '' : Number(total_pagos).toFixed(2) %></td>
  <td><%= ultimo_pago %></td>
  <td class="tar"><%= Number(Number(saldo) + Number(total_compras) + Number(total_pagos)).toFixed(2) %></td>
  <td><i title="Ver cuenta corriente" class="fa fa-search edit text-dark" /></td>
</script>
*/ ?>