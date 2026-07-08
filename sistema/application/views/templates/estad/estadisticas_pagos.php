<script type="text/template" id="estadisticas_pagos_template">
  <div id="estadisticas_pagos_container" class="col">
    <div class="bg-light titulo-pagina lter b-b wrapper-md">
      <div class="row">
        <div class="col-lg-6 col-sm-4 col-xs-12">
          <h1 class="m-n font-thin h3 text-black">
            <i class="fa fa-bar-chart icono_principal"></i>Estad&iacute;sticas
            / <b>Pagos</b>
          </h1>
        </div>
        <div class="col-lg-6 col-sm-8 col-xs-12">
          <div class="pull-right">
            <div class="w150 pull-left">
              <div class="input-group">
                <input type="text" id="estadisticas_pagos_fecha_desde" value="<%= fecha_desde %>" class="form-control">
                <span class="input-group-btn">
                  <button class="btn btn-cal btn-default"><i class="glyphicon glyphicon-calendar"></i></button>
                </span>
              </div>
            </div>
            <div class="w150 pull-left">
              <div class="input-group">
                <input type="text" id="estadisticas_pagos_fecha_hasta" value="<%= fecha_hasta %>" class="form-control">
                <span class="input-group-btn">
                  <button class="btn btn-cal btn-default"><i class="glyphicon glyphicon-calendar"></i></button>
                </span>
              </div>
            </div>
            <div class="w150 pull-left">
              <select class="form-control" id="estadisticas_pagos_sucursales">
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
            <button class="btn btn-default buscar pull-left m-l-xs"><i class="fa fa-search"></i> Buscar</button>
            <button class="btn btn-default imprimir pull-left m-l-xs"><i class="fa fa-print"></i></button>
          </div>
        </div>
      </div>
    </div>

    <div class="wrapper-md">
      
      <?php /*
      <div class="row pagina">
        <div class="col-md-6">
          <div class="row row-sm text-center">
            <div class="col-xs-6">
              <div class="panel padder-v item bg-info" style="height: 140px">
                <div class="h2 font-thin m-t-md"><%= Number(total_pagos).toFixed(2) %></div>
                <span class="text-muted text-md pt10 db">Total de pagos</span>
              </div>
            </div>
            <div class="col-xs-6">
              <div class="block panel padder-v item bg-success" style="height: 140px">
                <div class="h2 font-thin text-white m-t-md"><%= cantidad_operaciones %></div>
                <span class="text-muted text-md pt10 db">Cantidad de operaciones</span>
              </div>
            </div>
            <div class="col-xs-6">
              <div class="block panel padder-v item" style="height: 140px">
                <span class="font-thin h2 block m-t-md"><%= Number(cheques_emitidos).toFixed(2) %></span>
                <span class="text-muted text-md pt10 db">Cheques emitidos</span>
              </div>
            </div>
            <div class="col-xs-6">
              <div class="panel padder-v item" style="height: 140px">
                <div class="font-thin h2 m-t-md"><%= Number(total_efectivo).toFixed(2) %></div>
                <span class="text-muted text-md pt10 db">Total de efectivo</span>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="panel panel-default" style="min-height:300px">
            <div class="panel-heading font-bold">Formas de pago</div>
            <div class="panel-body" style="padding-top: 0px">
              <div class="row">
                <div class="col-md-6">
                  <div class="panel-footer">
                    <span class="label bg-success m-r-xs">1</span>
                    <small>Efectivo</small>
                    <small class="pull-right"><%= Number(total_efectivo).toFixed(2) %></small>
                  </div>
                  <div class="panel-footer">
                    <span class="label bg-info m-r-xs">2</span>
                    <small>Cheques propios</small>
                    <small class="pull-right"><%= Number(cheques_emitidos).toFixed(2) %></small>
                  </div>
                  <div class="panel-footer">
                    <span class="label bg-warning m-r-xs">3</span>
                    <small>Cheques de terceros</small>
                    <small class="pull-right"><%= Number(cheques_terceros).toFixed(2) %></small>
                  </div>
                  <div class="panel-footer">
                    <span class="label bg-danger m-r-xs">4</span>
                    <small>Dep&oacute;sitos / Transf.</small>
                    <small class="pull-right"><%= Number(transferencias).toFixed(2) %></small>
                  </div>
                </div>
                <div class="col-md-6">
                  <div id="dispositivos_bar" style="height: 200px"></div>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>
      */ ?>

      <div class="panel panel-default">
        <ul class="nav nav-tabs nav-tabs-2" role="tablist">
          <li class="active">
            <a id="tab1_link" href="#tab1" role="tab" data-toggle="tab">
              <i class="glyphicon glyphicon-shopping-cart text-warning m-r-xs"></i>
              &Oacute;rdenes de pago
            </a>
          </li>
          <li>
            <a id="tab2_link" href="#tab2" role="tab" data-toggle="tab">
              <i class="fa text-info fa-money m-r-xs"></i>
              Cheques por cancelar
            </a>
          </li>
        </ul>
        <div class="tab-content">
          <div id="tab1" class="tab-pane panel-body active">      
            <div class="">
              <div class="tar m-b-xs m-t-xs">
                <button class="btn btn-sm btn-default exportar_pagos">Exportar Excel</button>
              </div>
              <div class="pagina panel panel-default">
                <div style="min-height: 350px; max-height: 350px; overflow: auto;">
                  <table class="estadisticas_pagos_table table table-small table-striped m-b-none">
                    <thead>
                      <tr>
                        <th>Proveedor</th>
                        <th>Sucursal</th>
                        <th>Fecha</th>
                        <th class="tar w150">Efectivo</th>
                        <th class="tar w149">Cheques</th>
                        <th class="tar w150">Total</th>
                        <th class="w20 th_acciones"></th>
                      </tr>
                    </thead>
                    <tbody>
                      <% var total_ordenes_pago = 0 %>
                      <% var total_ef = 0 %>
                      <% var total_ch = 0 %>
                      <% for(var i=0;i< ordenes_pago.length;i++) { %>
                      <% var o = ordenes_pago[i]; %>
                      <tr>
                        <td><a href="app/#cuentas_corrientes_proveedores/<%= o.id_proveedor %>" target="_blank" class="text-info"><%= o.nombre %></a></td>
                        <td><%= o.sucursal %></td>
                        <td><%= o.fecha %></td>
                        <td class="tar"><%= Number(o.efectivo).toFixed(2) %></td>
                        <td class="tar"><%= Number(o.total_cheques).toFixed(2) %></td>
                        <td class="tar"><%= Number(o.total_general).toFixed(2) %></td>
                        <td class="p0 tac"><a href="app/#cuentas_corrientes_proveedores/<%= o.id_proveedor %>" target="_blank"><i class="fa fa-search"></i></a></td>
                        <% total_ef += parseFloat(o.efectivo) %>
                        <% total_ch += parseFloat(o.total_cheques) %>
                        <% total_ordenes_pago += parseFloat(o.total_general) %>
                      </tr>
                      <% } %>
                    </tbody>
                    <tfoot>
                      <tr>
                        <td colspan="<%= (control.check('almacenes')>0) ? 3 : 2 %>" class="bold tar"></td>
                        <td class="bold tar"><%= Number(total_ef).toFixed(2) %></td>
                        <td class="bold tar"><%= Number(total_ch).toFixed(2) %></td>
                        <td class="bold tar"><%= Number(total_ordenes_pago).toFixed(2) %></td>
                      </tr>
                    </tfoot>
                  </table>
                </div>
              </div>
            </div>
          </div>
          <div id="tab2" class="tab-pane panel-body">
            <div class="">
              <div class="tar m-b-xs m-t-xs">
                <button class="btn btn-sm btn-default exportar_cheques">Exportar Excel</button>
              </div>
              <div class="pagina panel panel-default">
                <div style="min-height: 350px; max-height: 350px; overflow: auto;">
                  <table class="estadisticas_pagos_cheques_por_cobrar table-small table table-striped m-b-none">
                    <thead>
                      <tr>
                        <th>Proveedor</th>
                        <th>Sucursal</th>
                        <th>Banco</th>
                        <th>Numero</th>
                        <th>Fecha cobro</th>
                        <th class="tar w150">Monto</th>
                        <th class="w20 th_acciones"></th>
                      </tr>
                    </thead>
                    <tbody>
                      <% var total_cheques_por_cobrar = 0 %>
                      <% for(var i=0;i< cheques_por_cobrar.length;i++) { %>
                        <% var o = cheques_por_cobrar[i]; %>
                        <tr>
                          <td><span class="text-info"><%= o.proveedor %></span></td>
                          <td><%= o.sucursal %></td>
                          <td><%= o.banco %></td>
                          <td><%= o.numero %></td>
                          <td><%= o.fecha_cobro %></td>
                          <td class="tar"><%= o.monto %></td>
                          <% total_cheques_por_cobrar += parseFloat(o.monto) %>
                          <td data-id="<%= o.id %>" class="p0 tac ver_cheque"><i class="fa fa-search"></i></td>
                        </tr>
                      <% } %>
                    </tbody>
                    <tfoot>
                      <tr>
                        <td colspan="<%= (control.check('almacenes')>0) ? 5 : 4 %>"></td>
                        <td class="bold tar"><%= Number(total_cheques_por_cobrar).toFixed(2) %></td>
                      </tr>
                    </tfoot>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</script>

              <?php /*
              <div class="col-xs-12 col-md-6">
                <div class="panel panel-default">
                  <div class="panel-heading font-bold">Comprobantes pagados en efectivo</div>
                  <div style="min-height: 350px; max-height: 350px; overflow: auto;">
                    <table class="estadisticas_pagos_table table-small table table-striped m-b-none">
                      <tbody>
                        <% var total_comp_efectivo = 0 %>
                        <% for(var i=0;i< comprobantes_efectivo.length;i++) { %>
                        <% var o = comprobantes_efectivo[i]; %>
                        <tr>
                          <td><a href="app/#compras/<%= o.id %>" target="_blank" class="text-info"><%= o.proveedor %></a></td>
                          <td><%= o.fecha %></td>
                          <td class="tar">$<%= o.total_general %></td>
                          <% total_comp_efectivo += parseFloat(o.total_general) %>
                        </tr>
                        <% } %>
                      </tbody>
                    </table>
                  </div>
                  <div class="panel-footer">
                    <div class="bold tar"><%= Number(total_comp_efectivo).toFixed(2) %></div>
                  </div>
                </div>
              </div>
              */ ?>
