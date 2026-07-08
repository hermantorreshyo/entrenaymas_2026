<script type="text/template" id="pres_cajas_diarias_panel_template">
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <h1 class="m-n font-thin h3"><i class="fa fa-dollar icono_principal"></i> Cajas diarias</h1>
  </div>
  <div class="wrapper-md ng-scope">
    <div class="panel panel-default">

      <ul class="nav nav-tabs nav-tabs-2" role="tablist">
        <li class="active">
          <a href="#tab_caja_diaria_0" id="listado_caja" role="tab" data-toggle="tab"><i class="fa fa-list text-info"></i> Caja Diaria</a>
        </li>
        <li>
          <a href="#tab_caja_diaria_1" id="ver_resumen_caja" role="tab" data-toggle="tab"><i class="fa fa-bar-chart text-danger"></i> Resumen</a>
        </li>
      </ul>
      <div class="panel-heading oh">
        <div class="row">
          <div class="col-xs-12">
            <div class="form-inline">    
              <div class="input-group" style="width: 140px;">
                <input type="text" autocomplete="off" placeholder="Desde" id="pres_cajas_diarias_desde" class="form-control">
                <span class="input-group-btn">
                  <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                </span>              
              </div>
              <div class="input-group" style="width: 140px;">
                <input type="text" autocomplete="off" placeholder="Hasta" id="pres_cajas_diarias_hasta" class="form-control">
                <span class="input-group-btn">
                  <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                </span>              
              </div>
              <select class="form-control" style="display: inline-block; width: 200px;" id="pres_cajas_diarias_sucursales">
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

              <select class="form-control" style="display: inline-block; width: 200px;" id="pres_cajas_diarias_conceptos">
                <option value='0'>Concepto</option>
                <%= workspace.crear_select(tipos_gastos,"",0) %>
              </select>

              <div class="form-group">
                <button class="buscar btn btn-default"><i class="fa fa-search"></i></button>
              </div>
              <% if (control.check("pres_caja_diaria")>1) { %>
                <button class="nuevo_gasto fr btn btn-success">Agregar movimiento</button>
                <button class="exportar_excel fr m-r-xs btn btn-default">Exportar</button>
              <% } %>
            </div>
          </div>
        </div>
      </div>
      <div class="tab-content">
        <div id="tab_caja_diaria_0" class="tab-pane pr0 pl0 active">
          <div class="panel-body">
            <div class="b-a table-responsive">
              <table id="pres_cajas_diarias_table" class="table table-small table-striped sortable m-b-none default footable">
                <thead>
                  <tr>
                    <th style="width:20px;">
                      <label class="i-checks m-b-none">
                        <input class="esc sel_todos" type="checkbox"><i></i>
                      </label>
                    </th>
                    <th style="width:170px;" class="sorting" data-sort-by="fecha">Fecha</th>
                    <th style="width:140px;">Concepto</th>
                    <th>Observaciones</th>
                    <th>Usuario</th>
                    <th class="tar" style="width:180px;">Ingresos</th>
                    <th class="tar" style="width:180px;">Pagos</th>
                    <th class="tar" style="width:180px;">Dtos.</th>
                    <th class="tar" style="width:180px;">Otorgaciones</th>
                    <th class="tar" style="width:180px;">Retiros</th>
                    <th class="tar" style="width:180px;">Gastos</th>
                    <th class="tar" style="width:180px;">Otros</th>
                    <th class="tar" style="width:180px;">Saldo</th>
                  </tr>
                </thead>
                <tbody>
                </tbody>
                <tfoot class="pagination_container hide-if-no-paging"></tfoot>
              </table>
            </div>
          </div>
        </div>
        <div id="tab_caja_diaria_1" class="tab-pane pr0 pl0">
          <div class="panel-body">
            <div class="b-a table-responsive">
              <table id="pres_cajas_diarias_table_resumen" class="table table-small table-striped sortable m-b-none default footable">
                <thead>
                  <tr>
                    <th>Concepto</th>
                    <th class="tar" style="width:180px;">Monto</th>
                  </tr>
                </thead>
                <tbody></tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>    
</script>


<script type="text/template" id="pres_cajas_diarias_item">
  <td>
    <label class="i-checks m-b-none">
      <input class="esc check-row" type="checkbox"><i></i>
    </label>
  </td>
  <td><span class='ver'><%= fecha %></span></td>
  <td><span class='ver'><%= concepto %>
    <% if (estado_facturacion == 1) { %>
      <span class="label bg-danger">Para facturar</span>
    <% } else if (estado_facturacion == 2) { %>
      <span class="label bg-success">Facturado</span>
    <% } %>
  </span></td>
  <td><span class='ver'><%= observaciones %></span></td>
  <td><span class='ver'><%= usuario %></span></td>
  <td class="tar"><span class='ver text-success'><%= (id_concepto == 272) ? ("$ "+Number(monto-descuento).format()) : ""  %></span></td>
  <td class="tar"><span class='ver text-success'><%= (id_concepto == 241) ? ("$ "+Number(monto-descuento).format()) : ""  %></span></td>
  <td class="tar"><span class='ver text-info'><%= (id_concepto == 241) ? ("$ "+Number(descuento).format()) : ""  %></span></td>
  <td class="tar"><span class='ver text-danger'><%= (id_concepto == 271) ? ("$ "+Number(monto-descuento).format()) : ""  %></span></td>
  <td class="tar"><span class='ver text-danger'><%= (id_concepto == 242) ? ("$ "+Number(monto-descuento).format()) : ""  %></span></td>
  <td class="tar"><span class='ver text-danger'><%= (id_concepto == 373) ? ("$ "+Number(monto-descuento).format()) : ""  %></span></td>
  <td class="tar"><span class='ver text-danger'><%= (id_concepto != 272 && id_concepto != 241 && id_concepto != 271 && id_concepto != 242 && id_concepto != 373) ? ("$ "+Number(monto-descuento).format()) : ""  %></span></td>
  <td class="tar"><span class='ver'>$ <%= Number(saldo).format() %></span></td>
</script>

<script type="text/template" id="pres_gastos_edit_panel_template">
  <div class="panel panel-default">
    <div class="panel-heading">
      <%= (id == undefined)?"Cargar movimiento":"Editar movimiento" %>
    </div>
    <div class="panel-body">
      <div class="form-group">
        <label class="control-label">Concepto</label>
        <select class="form-control esc" name="id_concepto" id="pres_gastos_tipo">
          <option value='0'>Sin especificar</option>
          <%= workspace.crear_select(tipos_gastos,"",id_concepto) %>
        </select>
      </div>
      <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            <label class="control-label">Fecha</label>
            <div class="input-group">
              <input type="text" value="<%= fecha %>" name="fecha" class="form-control esc" id="pres_gastos_fecha"/>
              <span class="input-group-btn">
                <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
              </span>              
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label class="control-label">Efectivo</label>
            <input type="text" value="<%= monto %>" name="monto" class="form-control esc" id="pres_gastos_monto"/>
          </div>
        </div>
      </div>
      <div class="form-group">
        <label class="control-label">Observaciones</label>
        <textarea name="observaciones" class="h80 form-control"><%= observaciones %></textarea>
      </div>
    </div>
    <div class="panel-footer clearfix">
      <% if (id != undefined && control.check("pres_caja_diaria")>2) { %>
        <button class="btn btn-danger eliminar fl">Eliminar</button>
      <% } %>
      <button class="btn btn-success guardar fr">Guardar</button>
      <% if (id_prestamo != 0) { %>
        <button class="btn btn-info ver_prestamo fr m-r-xs">Ver Prestamo</button>
      <% } %>
    </div>
  </div>
</script>