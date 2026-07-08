<script type="text/template" id="estadisticas_sucursales_template">
  <div id="estadisticas_sucursales_container" class="col">
    <div class="bg-light titulo-pagina lter b-b wrapper-md">
      <div class="row">
        <div class="col-xs-12">
          <h1 class="m-n font-thin h3 text-black">
            <i class="fa fa-bar-chart icono_principal"></i>Estad&iacute;sticas
            / <b>Resumen de Sucursales</b>
          </h1>
        </div>
      </div>
    </div>

    <div class="wrapper-md">   

      <div class="panel panel-default">
        <div class="panel-body">
          <div class="">
            <div class="input-group pull-left" style="width: 140px;">
              <input type="text" id="estadisticas_sucursales_fecha_desde" class="form-control" autocomplete="off">
              <span class="input-group-btn">
                <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
              </span>              
            </div>
            <div class="input-group pull-left" style="width: 140px;">
              <input type="text" id="estadisticas_sucursales_fecha_hasta" class="form-control" autocomplete="off">
              <span class="input-group-btn">
                <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
              </span>              
            </div>
            <% if (typeof almacenes != "undefined") { %>
              <select class="form-control pull-left m-l-xs" style="display: inline-block; width: 160px;" id="estadisticas_sucursales_sucursales">
                <% if (ID_SUCURSAL != 0) { %>
                  <% for(var i=0; i< almacenes.length; i++) { %>
                    <% var alm = almacenes[i] %>
                    <% if (ID_SUCURSAL == alm.id) { %>
                      <option value="<%= alm.id %>"><%= alm.nombre %></option>
                    <% } %>
                  <% } %>
                <% } else { %>
                  <option value="0">Sucursal</option>
                  <% for(var i=0; i< almacenes.length; i++) { %>
                    <% var alm = almacenes[i] %>
                    <option value="<%= alm.id %>"><%= alm.nombre %></option>
                  <% } %>
                <% } %>
              </select>
            <% } %>
            <button class="btn btn-default buscar pull-left m-l-xs"><i class="fa fa-search"></i></button>
            <button class="btn btn-default imprimir pull-left m-l-xs">Imprimir</button>
          </div>
        </div>
      </div>

      <div class="tab-container">
        <ul class="nav nav-tabs nav-tabs-2" role="tablist">
          <li class="active">
            <a href="#tab_estad_suc_1" role="tab" data-toggle="tab"><i class="fa fa-shopping-cart text-success"></i> Ventas</a>
          </li>
          <li>
            <a href="#tab_estad_suc_2" role="tab" data-toggle="tab"><i class="fa fa-tags text-info"></i> Mercaderia</a>
          </li>
          <li>
            <a href="#tab_estad_suc_3" role="tab" data-toggle="tab"><i class="fa fa-dollar text-danger"></i> Gastos</a>
          </li>
          <li>
            <a href="#tab_estad_suc_8" role="tab" data-toggle="tab"><i class="fa fa-dollar text-success"></i> Otros Ingresos</a>
          </li>
          <li>
            <a href="#tab_estad_suc_6" role="tab" data-toggle="tab"><i class="fa fa-list text-warning"></i> Pagos</a>
          </li>          
          <li>
            <a href="#tab_estad_suc_4" role="tab" data-toggle="tab"><i class="fa fa-list-alt text-primary"></i> Deuda</a>
          </li>
          <% if (ID_EMPRESA == 249) { %>
            <li>
              <a href="#tab_estad_suc_5" role="tab" data-toggle="tab"><i class="fa fa-dollar text-success"></i> Saldos</a>
            </li>
            <% if (PERFIL == 302) { %>
              <li>
                <a href="#tab_estad_suc_final" role="tab" data-toggle="tab"><i class="fa fa-dollar text-primary"></i> Control Efectivo</a>
              </li>
            <% } %>
          <% } %>
        </ul>
        <div class="tab-content">
          <div id="tab_estad_suc_1" class="tab-pane active">

            <div class="resumen pb0 mb20">
              <div class="row pl10 pr10">
                <div class="col-sm-3 col-lg-2 pr5 pl5 mb10">
                  <div class="block tac panel padder-v b-a item bg-default mb0" style="height: 80px">
                    <span id="estadisticas_sucursales_ventas_efectivo" class="font-thin h3 block">0</span>
                    <span class="text-muted text-md pt5 db">Efectivo <%= (MEGASHOP == 1)?"Sistema":"" %> <span id="estadisticas_sucursales_ventas_efectivo_porc"></span></span>
                  </div>
                </div>

                <% if (MEGASHOP == 1) { %>
                  <div class="col-sm-3 col-lg-2 pr5 pl5 mb10">
                    <div class="block tac panel padder-v b-a item bg-default mb0" style="height: 80px">
                      <span id="estadisticas_sucursales_ventas_efectivo_caja" class="font-thin h3 block">0</span>
                      <span class="text-muted text-md pt5 db">Efectivo Caja</span>
                    </div>
                  </div>
                  <div class="col-sm-3 col-lg-2 pr5 pl5 mb10">
                    <div class="block tac panel padder-v b-a item bg-default mb0" style="height: 80px">
                      <span id="estadisticas_sucursales_ventas_efectivo_diferencia" class="font-thin h3 block">0</span>
                      <span class="text-muted text-md pt5 db">Diferencia Caja</span>
                    </div>
                  </div>
                <% } %>

                <div class="col-sm-3 col-lg-2 pr5 pl5 mb10">
                  <div class="block tac panel padder-v b-a item bg-default mb0" style="height: 80px">
                    <span id="estadisticas_sucursales_ventas_tarjetas" class="font-thin h3 block">0</span>
                    <span class="text-muted text-md pt5 db">Tarjetas <span id="estadisticas_sucursales_ventas_tarjetas_porc"></span></span>
                  </div>
                </div>
                <div class="col-sm-3 col-lg-2 pr5 pl5 mb10">
                  <div class="block tac panel padder-v b-a item bg-default mb0" style="height: 80px">
                    <span id="estadisticas_sucursales_ventas_cmv" class="font-thin h3 block">0</span>
                    <span class="text-muted text-md pt5 db">Costo Mercaderia</span>
                  </div>
                </div>
                <div class="col-sm-3 col-lg-2 pr5 pl5 mb10">
                  <div class="block tac panel padder-v item b-success bg-success mb0" style="height: 80px">
                    <div id="estadisticas_sucursales_ventas_total" class="h3 font-thin text-white block">0</div>
                    <span class="text-muted text-md pt5 db">Venta Total</span>
                  </div>
                </div>
                <div class="col-sm-3 col-lg-2 pr5 pl5 mb10">
                  <div class="block tac panel padder-v b-a item bg-default mb0" style="height: 80px">
                    <span id="estadisticas_sucursales_ventas_anterior" class="font-thin h3 block">0</span>
                    <span class="text-muted text-md pt5 db">Año Ant. <span id="estadisticas_sucursales_ventas_anterior_variacion"></span></span>
                  </div>
                </div>                
                <div class="col-sm-3 col-lg-2 pr5 pl5 mb10">
                  <div class="block tac panel padder-v b-a item bg-default mb0" style="height: 80px">
                    <span id="estadisticas_sucursales_ventas_ticket_promedio" class="font-thin h3 block">0</span>
                    <span class="text-muted text-md pt5 db">Ticket Promedio</span>
                  </div>
                </div>                
              </div>
            </div>            

            <div class="b-a table-responsive">
              <table id="estadisticas_sucursales_ventas_table" class="table table-small table-striped sortable m-b-none default footable">
                <thead>
                  <tr>
                    <th>Fecha</th>
                    <th>Ops.</th>
                    <th>Efectivo</th>
                    <th>Tarjetas</th>
                    <th>Recargo</th>
                    <th>Venta Total</th>
                    <th>Costo Mercaderia</th>
                    <th>Ganancia Bruta</th>
                    <th>% Margen</th>
                  </tr>
                </thead>
                <tbody></tbody>
                <tfoot class="bg-important">
                  <tr>
                    <td></td>
                    <td id="estadisticas_sucursales_tickets" class="bold">0</td>
                    <td id="estadisticas_sucursales_efectivo" class="bold">$ 0.00</td>
                    <td id="estadisticas_sucursales_tarjetas" class="bold">$ 0.00</td>
                    <td id="estadisticas_sucursales_intereses" class="bold">$ 0.00</td>
                    <td id="estadisticas_sucursales_venta" class="bold">$ 0.00</td>
                    <td id="estadisticas_sucursales_cmv" class="bold">$ 0.00</td>
                    <td id="estadisticas_sucursales_ganancia" class="bold">$ 0.00</td>
                    <td id="estadisticas_sucursales_margen" class="bold">0.00 %</td>
                  </tr>
                </tfoot>
              </table>
            </div>            
          </div>

          <div id="tab_estad_suc_2" class="tab-pane">
            
            <div class="resumen pb0 mb20">
              <div class="row pl10 pr10">
                <div class="col-sm-3 col-lg-2 pr5 pl5">
                  <div class="block tac panel padder-v b-a item bg-default mb0" style="height: 80px">
                    <span id="estadisticas_sucursales_stock_inicial" class="font-thin h3 block">0</span>
                    <span class="text-muted text-md pt5 db">Stock Inicial</span>
                  </div>
                </div>
                <div class="col-sm-3 col-lg-2 pr5 pl5">
                  <div class="block tac panel padder-v b-a item bg-default mb0" style="height: 80px">
                    <span id="estadisticas_sucursales_stock_final" class="font-thin h3 block">0</span>
                    <span class="text-muted text-md pt5 db">Stock Final <span id="estadisticas_sucursales_stock_final_variacion"></span></span>
                  </div>
                </div>
                <div class="col-sm-3 col-lg-2 pr5 pl5">
                  <div class="block tac panel padder-v b-a item bg-default mb0" style="height: 80px">
                    <span id="estadisticas_sucursales_ingresos" class="font-thin h3 block">0</span>
                    <span class="text-muted text-md pt5 db">Ingresos</span>
                  </div>
                </div>
              </div>
            </div>  

            <div class="b-a table-responsive">
              <table id="estadisticas_sucursales_ingresos_table" class="table table-small table-striped sortable m-b-none default footable">
                <thead>
                  <tr>
                    <th>Fecha</th>
                    <th>Numero</th>
                    <th>Proveedor</th>
                    <th>Total</th>
                    <th>Observaciones</th>
                  </tr>
                </thead>
                <tbody></tbody> 
              </table>
            </div>            

          </div>

          <div id="tab_estad_suc_3" class="tab-pane">

          </div>

          <div id="tab_estad_suc_8" class="tab-pane">

          </div>

          <div id="tab_estad_suc_4" class="tab-pane">

          </div>

          <div id="tab_estad_suc_6" class="tab-pane">
            <?php // PAGOS A PROVEEDORES ?>
          </div>

          <div id="tab_estad_suc_final" class="tab-pane">
            <div class="resumen pb0 mb20">
              <div class="row pl10 pr10">
                <div class="col-md-3 pr5 pl5 mb10">
                  <div class="block tac panel padder-v b-a item bg-default mb0" style="height: 80px">
                    <span id="estadisticas_sucursales_cuenta_efectivo_inicial" class="font-thin h3 block">0</span>
                    <span class="text-muted text-md pt5 db">Inicial</span>
                  </div>
                </div>
                <div class="col-md-3 pr5 pl5 mb10">
                  <div class="block tac panel padder-v b-a item bg-default mb10" style="height: 80px">
                    <span id="estadisticas_sucursales_cuenta_venta_efectivo" class="font-thin h3 block">0</span>
                    <span class="text-success text-md pt5 db">+ Venta</span>
                  </div>
                  <div class="block tac panel padder-v b-a item bg-default mb0" style="height: 80px">
                    <span id="estadisticas_sucursales_cuenta_otros_ingresos" class="font-thin h3 block">0</span>
                    <span class="text-success text-md pt5 db">+ Otros Ingresos</span>
                  </div>
                </div>
                <div class="col-md-3 pr5 pl5 mb10">
                  <div class="block tac panel padder-v b-a item bg-default mb10" style="height: 80px">
                    <span id="estadisticas_sucursales_cuenta_pago_prov" class="font-thin h3 block">0</span>
                    <span class="text-danger text-md pt5 db">- Pago a Prov.</span>
                  </div>
                  <div class="block tac panel padder-v b-a item bg-default mb10" style="height: 80px">
                    <span id="estadisticas_sucursales_cuenta_gastos" class="font-thin h3 block">0</span>
                    <span class="text-danger text-md pt5 db">- Gastos</span>
                  </div>
                  <div class="block tac panel padder-v b-a item bg-default mb10" style="height: 80px">
                    <span id="estadisticas_sucursales_cuenta_retiros" class="font-thin h3 block">0</span>
                    <span class="text-danger text-md pt5 db">- Retiro Socios</span>
                  </div>
                  <div class="block tac panel padder-v b-a item bg-default mb10" style="height: 80px">
                    <span id="estadisticas_sucursales_cuenta_carca_sociales_b" class="font-thin h3 block">0</span>
                    <span class="text-danger text-md pt5 db">- Cargas Sociales B</span>
                  </div>
                </div>                  
                <div class="col-md-3 pr5 pl5 mb10">
                  <div class="block tac panel padder-v b-a item bg-default mb10" style="height: 80px">
                    <span id="estadisticas_sucursales_cuenta_final" class="font-thin h3 block">0</span>
                    <span class="text-muted text-md pt5 db">Final Esperado</span>
                  </div>
                  <div class="block tac panel padder-v b-a item bg-default mb10" style="height: 80px">
                    <span id="estadisticas_sucursales_cuenta_final_declarado" class="font-thin h3 block">0</span>
                    <span class="text-muted text-md pt5 db">Final Declarado</span>
                  </div>
                  <div class="block tac panel padder-v b-a item bg-default mb10" style="height: 80px">
                    <span id="estadisticas_sucursales_cuenta_final_diferencia" class="font-thin h3 block">0</span>
                    <span class="text-muted text-md pt5 db">Diferencia</span>
                  </div>
                </div>                
              </div>
            </div>
          </div>

          <div id="tab_estad_suc_5" class="tab-pane">

            <div class="resumen pb0 mb20">
              <div class="row pl10 pr10">
                <div class="col-sm-3 col-lg-2 pr5 pl5">
                  <label class="control-label bold">Socio 1</label>
                  <div class="block tac panel padder-v b-a item bg-default mb0" style="height: 80px">
                    <span id="estadisticas_sucursales_socio_1_efectivo" class="font-thin h3 block">0</span>
                    <span class="text-muted text-md pt5 db">Efectivo</span>
                  </div>
                </div>                
                <div class="col-sm-3 col-lg-2 pr5 pl5">
                  <label class="control-label bold">&nbsp;</label>
                  <div class="block tac panel padder-v b-a item bg-default mb0" style="height: 80px">
                    <span id="estadisticas_sucursales_socio_1_banco" class="font-thin h3 block">0</span>
                    <span class="text-muted text-md pt5 db">Tarjeta</span>
                  </div>
                </div>                

                <div class="col-sm-3 col-lg-2 pr5 pl5">
                  <label class="control-label bold">Socio 2</label>
                  <div class="block tac panel padder-v b-a item bg-default mb0" style="height: 80px">
                    <span id="estadisticas_sucursales_socio_2_efectivo" class="font-thin h3 block">0</span>
                    <span class="text-muted text-md pt5 db">Efectivo</span>
                  </div>
                </div>                
                <div class="col-sm-3 col-lg-2 pr5 pl5">
                  <label class="control-label bold">&nbsp;</label>
                  <div class="block tac panel padder-v b-a item bg-default mb0" style="height: 80px">
                    <span id="estadisticas_sucursales_socio_2_banco" class="font-thin h3 block">0</span>
                    <span class="text-muted text-md pt5 db">Tarjeta</span>
                  </div>
                </div>                

                <div class="col-sm-3 col-lg-2 pr5 pl5">
                  <label class="control-label bold">Socio 3</label>
                  <div class="block tac panel padder-v b-a item bg-default mb0" style="height: 80px">
                    <span id="estadisticas_sucursales_socio_3_efectivo" class="font-thin h3 block">0</span>
                    <span class="text-muted text-md pt5 db">Efectivo</span>
                  </div>
                </div>                
                <div class="col-sm-3 col-lg-2 pr5 pl5">
                  <label class="control-label bold">&nbsp;</label>
                  <div class="block tac panel padder-v b-a item bg-default mb0" style="height: 80px">
                    <span id="estadisticas_sucursales_socio_3_banco" class="font-thin h3 block">0</span>
                    <span class="text-muted text-md pt5 db">Tarjeta</span>
                  </div>
                </div>

              </div>
            </div>  

            <div class="row pl10 pr10">
              <?php /*
              <div class="col-sm-3 col-lg-2 pr5 pl5">
                <div class="form-group">
                  <label class="control-label">Efectivo Inicial</label>
                  <input type="text" class="form-control" />
                </div>
              </div>
              <div class="col-sm-3 col-lg-2 pr5 pl5">
                <div class="form-group">
                  <label class="control-label">Efectivo Final</label>
                  <input type="text" class="form-control" />
                </div>
              </div>
              */ ?>
              <div class="col-sm-3 col-lg-2 pr5 pl5">
                <div class="form-group">
                  <label class="control-label">Banco Inicial</label>
                  <input id="estadisticas_sucursales_banco_inicial" disabled type="text" class="form-control" />
                </div>
              </div>
              <div class="col-sm-3 col-lg-2 pr5 pl5">
                <div class="form-group">
                  <label class="control-label">Banco Final</label>
                  <input id="estadisticas_sucursales_banco_final" type="text" class="form-control" />
                </div>
              </div>
              <div class="col-sm-3 col-lg-2 pr5 pl5">
                <div class="form-group">
                  <label class="control-label">Efectivo Inicial Real</label>
                  <input id="estadisticas_sucursales_efectivo_inicial" disabled type="text" class="form-control" />
                </div>
                <?php /*
                <div class="form-group">
                  <label class="control-label">Efectivo Inicial Sistema</label>
                  <input id="estadisticas_sucursales_efectivo_inicial_sistema" disabled type="text" class="form-control" />
                </div>
                */ ?>
              </div>
              <div class="col-sm-3 col-lg-2 pr5 pl5">
                <div class="form-group">
                  <label class="control-label">Efectivo Final Real</label>
                  <input id="estadisticas_sucursales_efectivo_final" type="text" class="form-control" />
                </div>
                <?php /*
                <div class="form-group">
                  <label class="control-label">Efectivo Final Sistema</label>
                  <input id="estadisticas_sucursales_efectivo_final_sistema" disabled type="text" class="form-control" />
                </div>
                */ ?>
              </div>
              <?php /*
              <% if (ID_EMPRESA == 249) { %>
                <div class="col-sm-3 col-lg-2 pr5 pl5">
                  <div class="form-group">
                    <label class="control-label">Cargas Sociales B</label>
                    <input id="estadisticas_sucursales_cargas_sociales_b" type="text" class="form-control" />
                  </div>
                </div>
              <% } %>
              */ ?>

              <% if (ID_EMPRESA == 249) { %>
                <div class="col-sm-3 col-lg-2 pr5 pl5">
                  <div class="form-group">
                    <label class="control-label">Cargas Sociales B</label>
                    <input disabled id="estadisticas_sucursales_ahorro_cargas_sociales" type="text" class="form-control" />
                  </div>
                </div>
              <% } %>

              <div class="col-sm-3 col-lg-2 pr5 pl5">
                <div class="form-group">
                  <label class="control-label">&nbsp;</label>
                  <button class="btn btn-block guardar_saldos btn-success">Guardar</button>
                </div>                
              </div>
            </div>

          </div>

        </div>
      </div>         

    </div>
  </div>
</script>

<script type="text/template" id="estadisticas_sucursales_ingresos_tabla_template">
  <div class="resumen pb0 mb20">
    <div class="row pl10 pr10">
      <div class="col-sm-3 col-lg-2 pr5 pl5">
        <div class="block tac panel padder-v b-a item bg-default mb0" style="height: 80px">
          <span id="estadisticas_sucursales_ingresos_efectivo" class="font-thin h3 block">$ <%= Number(total_efectivo).format() %></span>
          <span class="text-muted text-md pt5 db">Efectivo <span>(<%= Number((total_ingresos > 0) ? (total_efectivo / total_ingresos * 100) : 0).format() %>%)</span></span>
        </div>
      </div>
      <div class="col-sm-3 col-lg-2 pr5 pl5">
        <div class="block tac panel padder-v b-a item bg-default mb0" style="height: 80px">
          <span id="estadisticas_sucursales_ingresos_banco" class="font-thin h3 block">$ <%= Number(total_banco).format() %></span>
          <span class="text-muted text-md pt5 db">Banco <span>(<%= Number((total_ingresos > 0) ? (total_banco / total_ingresos * 100) : 0).format() %>%)</span></span>
        </div>
      </div>
      <div class="col-sm-3 col-lg-2 pr5 pl5">
        <div class="block tac panel padder-v b-success item bg-success mb0" style="height: 80px">
          <span id="estadisticas_sucursales_ingresos_total" class="font-thin text-white h3 block">$ <%= Number(total_ingresos).format() %></span>
          <span class="text-muted text-md pt5 db">Total Ingresos <span>(<%= Number((total_ventas > 0) ? (total_ingresos / total_ventas * 100) : 0).format() %>%)</span></span>
        </div>
      </div>
    </div>
  </div>
  <div class="b-a table-responsive">
    <table id="estadisticas_ingresos_ingresos_table" class="table table-small table-striped sortable m-b-none default footable">
      <thead>
        <tr>
          <th>Concepto</th>
          <% for (var i = 0; i< cajas_gastos.length; i++) { %>
            <% var o = cajas_gastos[i] %>
            <th><%= o.nombre %></th>
          <% } %>
          <th>%</th>
        </tr>
      </thead>
      <tbody>
        <% for (var i = 0; i< ingresos.length; i++) { %>
          <% var o = ingresos[i] %>
          <tr>
            <% var total_ingreso_por_concepto = 0 %>
            <td><span class="text-info"><%= o.nombre %></span></td>
            <% for (var j = 0; j< cajas_gastos.length; j++) { %>
              <% var caja_ingreso = cajas_gastos[j] %>
              <% for (var k = 0; k < o.cajas.length; k++) { %>
                <% var cc = o.cajas[k] %>
                <% if (caja_ingreso.id == cc.id) { %>
                  <td>$ <%= Number(cc.total).format() %></td>
                  <% total_ingreso_por_concepto += Number(cc.total) %>
                <% } %>
              <% } %>
            <% } %>
            <td><%= Number((total_ventas > 0) ? (total_ingreso_por_concepto / total_ventas * 100) : 0).format() %>%</td>
          </tr>
        <% } %>
      </tbody> 
    </table>
  </div>
</script>

<script type="text/template" id="estadisticas_sucursales_ventas_item_template">
<td><%= fecha %></td>
<td><%= cantidad %></td>
<td>$ <%= Number(efectivo).format() %></td>
<td>$ <%= Number(tarjetas).format() %></td>
<td>$ <%= Number(intereses).format() %></td>
<td class="negro">$ <%= Number(total).format() %></td>
<td>$ <%= Number(costo).format() %></td>
<td>$ <%= Number(total - costo).format() %></td>
<% var margen = (costo > 0) ? (((total-costo) / costo) * 100) : 0 %>
<td><%= Number(margen).format() %> %</td>
</script>

<script type="text/template" id="estadisticas_sucursales_ingresos_item_template">
  <td><%= fecha %></td>
  <td><%= numero_remito %></td>
  <td><span class="text-info"><%= proveedor %></span></td>
  <td>$ <%= Number(total).format() %></td>
  <td><%= observaciones %></td>
</script>

<script type="text/template" id="estadisticas_sucursales_gastos_tabla_template">
  <div class="resumen pb0 mb20">
    <div class="row pl10 pr10">
      <div class="col-sm-3 col-lg-2 pr5 pl5">
        <div class="block tac panel padder-v b-a item bg-default mb0" style="height: 80px">
          <span id="estadisticas_sucursales_gastos_efectivo" class="font-thin h3 block">$ <%= Number(total_efectivo).format() %></span>
          <span class="text-muted text-md pt5 db">Efectivo <span>(<%= Number((total_gastos > 0) ? (total_efectivo / total_gastos * 100) : 0).format() %>%)</span></span>
        </div>
      </div>
      <div class="col-sm-3 col-lg-2 pr5 pl5">
        <div class="block tac panel padder-v b-a item bg-default mb0" style="height: 80px">
          <span id="estadisticas_sucursales_gastos_banco" class="font-thin h3 block">$ <%= Number(total_banco).format() %></span>
          <span class="text-muted text-md pt5 db">Banco <span>(<%= Number((total_gastos > 0) ? (total_banco / total_gastos * 100) : 0).format() %>%)</span></span>
        </div>
      </div>
      <div class="col-sm-3 col-lg-2 pr5 pl5">
        <div class="block tac panel padder-v b-danger item bg-danger mb0" style="height: 80px">
          <span id="estadisticas_sucursales_gastos_total" class="font-thin text-white h3 block">$ <%= Number(total_gastos).format() %></span>
          <span class="text-muted text-md pt5 db">Total Gastos <span>(<%= Number((total_ventas > 0) ? (total_gastos / total_ventas * 100) : 0).format() %>%)</span></span>
        </div>
      </div>
    </div>
  </div>
  <div class="b-a table-responsive">
    <table id="estadisticas_gastos_ingresos_table" class="table table-small table-striped sortable m-b-none default footable">
      <thead>
        <tr>
          <th>Concepto</th>
          <% for (var i = 0; i< cajas_gastos.length; i++) { %>
            <% var o = cajas_gastos[i] %>
            <th><%= o.nombre %></th>
          <% } %>
          <th>%</th>
        </tr>
      </thead>
      <tbody>
        <% for (var i = 0; i< gastos.length; i++) { %>
          <% var o = gastos[i] %>
          <tr>
            <% var total_gasto_por_concepto = 0 %>
            <td><span class="text-info"><%= o.nombre %></span></td>
            <% for (var j = 0; j< cajas_gastos.length; j++) { %>
              <% var caja_gasto = cajas_gastos[j] %>
              <% for (var k = 0; k < o.cajas.length; k++) { %>
                <% var cc = o.cajas[k] %>
                <% if (caja_gasto.id == cc.id) { %>
                  <td>$ <%= Number(cc.total).format() %></td>
                  <% total_gasto_por_concepto += Number(cc.total) %>
                <% } %>
              <% } %>
            <% } %>
            <td><%= Number((total_ventas > 0) ? (total_gasto_por_concepto / total_ventas * 100) : 0).format() %>%</td>
          </tr>
        <% } %>
      </tbody> 
    </table>
  </div>
</script>

<script type="text/template" id="estadisticas_sucursales_pagos_tabla_template">
  <div class="resumen pb0 mb20">
    <div class="row pl10 pr10">
      <div class="col-sm-3 col-lg-2 pr5 pl5">
        <div class="block tac panel padder-v b-a item bg-default mb0" style="height: 80px">
          <span id="estadisticas_sucursales_total_pagos_efectivo" class="font-thin h3 block">$ <%= Number(total_pago_efectivo).format() %></span>
          <span class="text-muted text-md pt5 db">Efectivo</span>
        </div>
      </div>                
      <div class="col-sm-3 col-lg-2 pr5 pl5">
        <div class="block tac panel padder-v b-a item bg-default mb0" style="height: 80px">
          <span id="estadisticas_sucursales_total_pagos_cheques" class="font-thin h3 block">$ <%= Number(total_pago_cheques).format() %></span>
          <span class="text-muted text-md pt5 db">Cheques Cubiertos</span>
        </div>
      </div>                
      <div class="col-sm-3 col-lg-2 pr5 pl5">
        <div class="block tac panel padder-v b-a item bg-default mb0" style="height: 80px">
          <span id="estadisticas_sucursales_total_pagos_transferencias" class="font-thin h3 block">$ <%= Number(total_pago_transferencias).format() %></span>
          <span class="text-muted text-md pt5 db">Transferencias</span>
        </div>
      </div>                
      <div class="col-sm-3 col-lg-2 pr5 pl5">
        <div class="block tac panel padder-v b-a item bg-warning mb0" style="height: 80px">
          <span id="estadisticas_sucursales_total_pagos" class="font-thin h3 block">$ <%= Number(total_pagos).format() %></span>
          <span class="text-muted text-md pt5 db">Total Pagos</span>
        </div>
      </div>
    </div>
  </div>  
  <div class="b-a">
    <div style="min-height: 350px; max-height: 350px; overflow: auto;">
      <table class="estadisticas_pagos_table table table-small table-striped m-b-none">
        <thead>
          <tr>
            <th>Proveedor</th>
            <th class="tar w150">Efectivo</th>
            <?php //<th class="tar w149">Cheques Emitidos</th> ?>
            <th class="tar w150">Transf.</th>
            <th class="tar w150">Total</th>
            <th class="w20 th_acciones"></th>
          </tr>
        </thead>
        <tbody>
          <% var total_ordenes_pago = 0 %>
          <% var total_ef = 0 %>
          <?php /*<% var total_ch = 0 %>*/ ?>
          <% var total_tf = 0 %>
          <% for(var i=0;i< ordenes_pago.length;i++) { %>
            <% var o = ordenes_pago[i]; %>
            <tr>
              <td><a href="app/#cuentas_corrientes_proveedores/<%= o.id_proveedor %>" target="_blank" class="text-info"><%= o.nombre %></a>
                <% if (!isEmpty(o.observaciones)) { %>
                  <i data-toggle="tooltip" title="<%= o.observaciones %>" class="fa fa-commenting text-warning"></i>
                <% } %>                
              </td>
              <td class="tar"><%= Number(o.efectivo).toFixed(2) %></td>
              <?php // <td class="tar"><%= Number(o.total_cheques).toFixed(2) %></td>?>
              <td class="tar"><%= Number(o.total_depositos).toFixed(2) %></td>
              <td class="tar"><%= Number(o.total_general).toFixed(2) %></td>
              <td class="p0 tac"><a href="app/#cuentas_corrientes_proveedores/<%= o.id_proveedor %>" target="_blank"><i class="fa fa-search"></i></a></td>
              <% total_ef += parseFloat(o.efectivo) %>
              <?php /*<% total_ch += parseFloat(o.total_cheques) %>*/ ?>
              <% total_tf += parseFloat(o.total_depositos) %>
              <% total_ordenes_pago += parseFloat(o.total_general) %>
            </tr>
          <% } %>
        </tbody>
        <?php /*
        <tfoot class="bg-important">
          <tr>
            <td colspan="2" class="bold tar"></td>
            <td class="bold tar"><%= Number(total_ef).toFixed(2) %></td>
            <td class="bold tar"><%= Number(total_ch).toFixed(2) %></td>
            <td class="bold tar"><%= Number(total_tf).toFixed(2) %></td>
            <td class="bold tar"><%= Number(total_ordenes_pago).toFixed(2) %></td>
          </tr>
        </tfoot>
        */ ?>
      </table>
    </div>
  </div>
</script>

<script type="text/template" id="estadisticas_sucursales_deuda_tabla_template">
  <div class="resumen pb0 mb20">
    <div class="row pl10 pr10">
      <div class="col-sm-3 col-lg-2 pr5 pl5">
        <div class="block tac panel padder-v b-a item bg-default mb0" style="height: 80px">
          <span id="estadisticas_sucursales_deuda_cheques" class="font-thin h3 block">0</span>
          <span class="text-muted text-md pt5 db">Cheques <span id="estadisticas_sucursales_deuda_cheques_porc"></span></span>
        </div>
      </div>                
      <div class="col-sm-3 col-lg-2 pr5 pl5">
        <div class="block tac panel padder-v b-a item bg-default mb0" style="height: 80px">
          <span id="estadisticas_sucursales_deuda_proveedores" class="font-thin h3 block">0</span>
          <span class="text-muted text-md pt5 db">Efectivo <span id="estadisticas_sucursales_deuda_proveedores_porc"></span></span>
        </div>
      </div>                
      <div class="col-sm-3 col-lg-2 pr5 pl5">
        <div class="block tac panel padder-v b-a item bg-default mb0" style="height: 80px">
          <span id="estadisticas_sucursales_deuda_proveedores_adelantos" class="font-thin h3 block">0</span>
          <span class="text-muted text-md pt5 db">Adelantos</span>
        </div>
      </div>                
      <div class="col-sm-3 col-lg-2 pr5 pl5">
        <div class="block tac panel padder-v b-a item bg-primary mb0" style="height: 80px">
          <span id="estadisticas_sucursales_total_deuda" class="font-thin text-white h3 block"></span>
          <span class="text-muted text-md pt5 db">Deuda Total</span>
        </div>
      </div>
      <div class="col-sm-3 col-lg-2 pr5 pl5">
        <div class="block tac panel padder-v b-a item bg-default mb0" style="height: 80px">
          <span id="estadisticas_sucursales_deuda_proveedores_deuda_vencida" class="font-thin h3 block">0</span>
          <span class="text-muted text-md pt5 db">Vencida</span>
        </div>
      </div>                
    </div>
  </div> 

  <% if (deuda_cheques.length > 0) { %>
    <div class="resumen pb0 mb20">
      <h3 class="h4 mb10 bold">Deuda en cheques:</h3>
      <div class="row pl10 pr10">
        <% for(var ii=0;ii< deuda_cheques.length; ii++) { %>
          <% var mes = deuda_cheques[ii] %>        
          <div class="col-sm-3 col-lg-2 pr5 pl5 mb5">
            <div class="block tac panel padder-v b-a item bg-default mb0">
              <span class="text-muted text-md pt5 db">$ <%= Number(mes.total).format() %></span>
              <span class="text-muted text-md pt5 db"><%= mes.mes %></span>
            </div>
          </div>
        <% } %>
      </div>
    </div>
  <% } %>

  <div id="estadisticas_sucursales_deuda_table">
    <h3 class="h4 mb10 bold">Deuda de Proveedores:</h3>
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
            <td id="estadisticas_sucursales_deuda_proveedores_total_saldo_mas_90" class="fs16 bold tar fila_alerta"></td>
            <td id="estadisticas_sucursales_deuda_proveedores_total_saldo_90" class="fs16 bold tar fila_alerta"></td>
            <td id="estadisticas_sucursales_deuda_proveedores_total_saldo_60" class="fs16 bold tar fila_alerta"></td>
            <td id="estadisticas_sucursales_deuda_proveedores_total_saldo_30" class="fs16 bold tar fila_alerta"></td>
            <td id="estadisticas_sucursales_deuda_proveedores_total_saldo" class="fs16 bold tar fila_alerta"></td>
            <td id="estadisticas_sucursales_deuda_proveedores_total_compras" class="fs16 bold tar fila_alerta"></td>
            <td class="fila_alerta"></td>
            <td id="estadisticas_sucursales_deuda_proveedores_total_pagos" class="fs16 bold tar fila_alerta"></td>
            <td class="fila_alerta"></td>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>
</script>