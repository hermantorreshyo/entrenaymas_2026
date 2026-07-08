<script type="text/template" id="cajas_diarias_panel_template">
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <h1 class="m-n font-thin h3"><i class="fa fa-inbox icono_principal"></i>Caja / <b>Arqueos de Cajas</b></h1>
  </div>
  <div class="wrapper-md ng-scope">
    <div class="panel panel-default">

      <?php $active = "cajas_diarias"; include("caja/caja_menu.php"); ?>

      <div class="panel-heading oh">
        <div class="row">
          <div class="col-xs-12">
            <div class="form-inline">    
              <div class="input-group" style="width: 140px;">
                <input type="text" placeholder="Desde" id="cajas_diarias_desde" autocomplete="off" class="form-control">
                <span class="input-group-btn">
                  <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                </span>              
              </div>
              <div class="input-group" style="width: 140px;">
                <input type="text" placeholder="Hasta" id="cajas_diarias_hasta" autocomplete="off" class="form-control">
                <span class="input-group-btn">
                  <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                </span>              
              </div>
              <select class="form-control" style="display: inline-block; width: 200px;" id="cajas_diarias_puntos_venta">
                <option value="0">Todas</option>
                <% for(var i=0;i< puntos_venta.length;i++) { %>
                  <% var pv = puntos_venta[i] %>
                  <% if (ID_SUCURSAL == 0 || ID_SUCURSAL == pv.id_sucursal) { %>
                    <option <%= (window.caja_diaria_punto_venta == pv.id) ? "selected":"" %> value="<%= pv.id %>"><%= pv.nombre %></option>
                  <% } %>
                <% } %>
              </select>
              <div class="form-group">
                <button class="buscar btn btn-default"><i class="fa fa-search"></i> Buscar</button>
              </div>

              <% if (control.check("caja_diaria")>1) { %>
                <div class="pull-right">
                  <a class="btn btn-info btn-addon" href="app/#caja_diaria"><i class="fa fa-plus"></i>&nbsp;&nbsp;Ver Arqueo de Caja&nbsp;&nbsp;</a>
                </div>              
              <% } %>

            </div>
          </div>
        </div>
      </div>
      <div class="bulk_action wrapper pb0">
        <button class="btn btn-default imprimir_agrupado btn-addon"><i class="icon fa fa-print"></i>Imprimir</button>
        <% if (VOLVER_SUPERADMIN == 1) { %>
          <button class="btn btn-default recalcular btn-addon"><i class="icon fa fa-calculator"></i>Recalcular</button>
        <% } %>
      </div>
      <div class="panel-body">
        <div class="b-a table-responsive">
          <table id="cajas_diarias_table" class="table table-small table-striped sortable m-b-none default footable">
            <thead>
              <tr>
                <th style="width:20px;">
                  <label class="i-checks m-b-none">
                    <input class="esc sel_todos" type="checkbox"><i></i>
                  </label>
                </th>
                <th style="width:20px;"></th>
                <th style="width:20px;">Caja</th>
                <th class="sorting" data-sort-by="fecha">Fecha</th>
                <th>Ef. Inicial</th>
                <th>Vta. Efectivo</th>
                <th>Gastos</th>
                <th>Ef. Final</th>
                <th>Ef. Real</th>
                <th>Diferencia</th>
                <th>Tarjetas</th>
                <th>Intereses</th>
                <th>Venta Total</th>
              </tr>
            </thead>
            <tbody></tbody>
            <!--
            <tfoot>
              <tr>
                <td colspan="10"></td>
                <td id="caja_diaria_total" class="bold">$ 0.00</td>
              </tr>
            </tfoot>
          -->
          <tfoot class="pagination_container hide-if-no-paging"></tfoot>
        </table>
      </div>
    </div>
  </div>
</div>    
</script>


<script type="text/template" id="cajas_diarias_item">
<td>
  <label class="i-checks m-b-none">
    <input class="esc check-row" value="<%= id %>" data-id_punto_venta="<%= id_punto_venta %>" type="checkbox"><i></i>
  </label>
</td>
<td style="width: 20px">
  <% if (confirmada == 1) { %>
    <div class="pr">
      <i title="Confirmada" class="fa fa-check text-info pa" style="left: 0px; top:-5px"></i>
      <i title="Confirmada" class="fa fa-check text-info pa" style="left: 8px; top:-5px"></i>
    </div>
  <% } else { %>
    <% if (uploaded == 1) { %>
      <i title="Subido correctamente" class="fa fa-check text-success"></i>
    <% } %>
  <% } %>
</td>
<td><span class='ver'><%= numero %></span></td>
<td><span class='ver'><%= fecha %> <%= hora %></span></td>
<td><span class='ver'>$ <%= efectivo_inicial %></span></td>
<td><span class='ver'>$ <%= efectivo %></span></td>
<td><span class='ver'>$ <%= salida_efectivo %></span></td>
<td><span class='ver'>$ <%= Number(Number(efectivo_inicial) + Number(efectivo) - Number(salida_efectivo)).toFixed(2) %></span></td>
<td><span class='ver'>$ <%= efectivo_real %></span></td>
<td><span class='ver'>$ <%= diferencia %></span></td>
<td><span class='ver'>$ <%= Number(tarjetas-intereses).toFixed(2) %></span></td>
<td><span class='ver'>($ <%= Number(intereses).toFixed(2) %>)</span></td>
<td><span class='ver'>$ <%= Number(Number(efectivo) + Number(tarjetas) - Number(intereses) + Number(cheques) - Number(salida_efectivo)).toFixed(2) %></span></td>
</script>

<script type="text/template" id="caja_diaria_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <h1 class="m-n font-thin h3"><i class="fa fa-inbox icono_principal"></i>Caja / <b>Arqueos de Cajas</b></h1>
</div>
<div class="wrapper-md">
  <div class="centrado rform">
    <div class="row">
      <div class="col-md-2">
        <div class="detalle_texto"></div>
      </div>
      <div class="col-md-8">
        <div class="panel panel-default">
          <div class="panel-body">
            <div class="padder">

              <div class="row">
                <div class="col-md-6">
                  <label class="control-label mt10"><%= punto_venta %></label>
                </div>
                <div class="col-md-6 tar">
                  <label class="control-label mt10"><%= fecha %></label>
                </div>
              </div>
              <div class="line b-b"></div>

              <div class="row">
                <div class="col-md-6 tar">
                  <label class="control-label mt10">Efectivo inicial</label>
                </div>
                <div class="col-md-6">
                  <input type="text" <%= (estado=="C" || (typeof NO_MODIFICAR_EFECTIVO_INICIAL != "undefined"))?"disabled":"" %> value="<%= efectivo_inicial %>" id="caja_diaria_efectivo_inicial" class="no-model calcular form-control"/>
                </div>
              </div>
              <div class="line b-b"></div>

              <div style="<%= ((MEGASHOP == 1 || ID_EMPRESA == 421) && (PERFIL == 319 || PERFIL == 503 || (typeof OCULTAR_DETALLE_CAJA_DIARIA != undefined)) && LOCAL == 1)?"display:none":"" %>">
                <div class="row">
                  <div class="col-md-6 tar"> 
                    <span class="bold">Ventas:</span>
                  </div>
                  <div class="col-md-6">
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6 tar"> 
                    <label class="control-label mt10">Efectivo</label>
                  </div>
                  <div class="col-md-6">
                    <input type="text" id="caja_diaria_efectivo" value="<%= Number(efectivo).toFixed(2) %>" disabled class="no-model form-control"/>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6 tar"> 
                    <label class="control-label mt10">Tarjetas</label>
                  </div>
                  <div class="col-md-6">
                    <div class="input-group">
                      <input type="text" id="caja_diaria_tarjetas" value="<%= Number(tarjetas - intereses).toFixed(2) %>" disabled class="no-model form-control"/>
                      <span class="input-group-btn">
                        <button id="caja_diaria_tarjetas_boton" class="btn btn-default" type="button">+</button>
                      </span>
                    </div>                         
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6 tar"> 
                    <label class="control-label mt10">Cheques</label>
                  </div>
                  <div class="col-md-6">
                    <input type="text" id="caja_diaria_cheques" value="<%= Number(cheques).toFixed(2) %>" disabled class="no-model form-control"/>
                  </div>
                </div>

                <div class="row mt15">
                  <div class="col-md-6 tar"> 
                    <span class="bold">Pagos de Cuenta Corriente:</span>
                  </div>
                  <div class="col-md-6">
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6 tar"> 
                    <label class="control-label mt10">Efectivo</label>
                  </div>
                  <div class="col-md-6">
                    <input type="text" id="caja_diaria_pago_efectivo" value="<%= Number(pago_efectivo).toFixed(2) %>" disabled class="no-model form-control"/>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6 tar"> 
                    <label class="control-label mt10">Cheques</label>
                  </div>
                  <div class="col-md-6">
                    <input type="text" id="caja_diaria_pago_cheques" value="<%= Number(pago_cheques).toFixed(2) %>" disabled class="no-model form-control"/>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6 tar"> 
                    <label class="control-label mt10">Tarjetas</label>
                  </div>
                  <div class="col-md-6">
                    <input type="text" id="caja_diaria_pago_tarjetas" value="<%= Number(pago_tarjetas).toFixed(2) %>" disabled class="no-model form-control"/>
                  </div>
                </div>

                <div class="row mt15">
                  <div class="col-md-6 tar"> 
                    <label class="control-label mt10 bold">Subtotal</label>
                  </div>
                  <div class="col-md-6">
                    <input type="text" disabled id="caja_diaria_subtotal_entradas" class="no-model form-control"/>
                  </div>
                </div>
                <div class="line b-b"></div>
                <div class="row">
                  <div class="col-md-6 tar"> 
                    <span class="bold">Salidas:</span>
                  </div>
                  <div class="col-md-6">
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6 tar"> 
                    <label class="control-label mt10">Gastos en efectivo</label>
                  </div>
                  <div class="col-md-6">
                    <div class="input-group">
                      <input type="text" id="caja_diaria_salida_efectivo" disabled value="<%= Number(salida_efectivo).toFixed(2) %>" class="no-model form-control"/>
                      <span class="input-group-btn">
                        <button id="caja_diaria_salida_efectivo_boton" class="btn btn-default" type="button">+</button>
                      </span>
                    </div>                         
                  </div>
                </div>
                <div class="line b-b"></div>
                <div class="row">
                  <div class="col-md-6 tar"> 
                    <label class="control-label mt10 fs18 bold">TOTAL:</label>
                  </div>
                  <div class="col-md-6">
                    <input type="text" id="caja_diaria_total" disabled class="no-model form-control"/>
                  </div>
                </div>
                <div class="line b-b"></div>
                <div class="row">
                  <div class="col-md-6 tar"> 
                    <label class="control-label mt10">Efectivo Total</label>
                  </div>
                  <div class="col-md-6">
                    <input type="text" disabled id="caja_diaria_efectivo_total" value="<%= Number(efectivo + efectivo_inicial - salida_efectivo).toFixed(2) %>" class="form-control no-model calcular"/>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6 tar"> 
                    <label class="control-label mt10">Efectivo Real</label>
                  </div>
                  <div class="col-md-6">
                    <input type="text" <%= (MEGASHOP == 0 && confirmada==1)?"disabled":"" %> id="caja_diaria_efectivo_real" value="<%= efectivo_real %>" name="efectivo_real" class="form-control no-model calcular"/>
                  </div>
                </div>              
                <div class="row">
                  <div class="col-md-6 tar"> 
                    <label class="control-label mt10">Diferencia</label>
                  </div>
                  <div class="col-md-6">
                    <input type="text" disabled id="caja_diaria_diferencia" value="<%= Number(Number(efectivo_real) - Number(efectivo) + Number(salida_efectivo) - Number(efectivo_inicial)).toFixed(2) %>" class="no-model form-control"/>
                  </div>
                </div>
                <div class="line b-b"></div>
                <div class="row">
                  <div class="col-md-6 tar"> 
                    <label class="control-label mt10">Retiro</label>
                  </div>
                  <div class="col-md-6">
                    <input type="text" <%= (confirmada==1)?"disabled":"" %> id="caja_diaria_retiro" value="<%= retiro %>" name="retiro" class="form-control no-model calcular"/>
                  </div>
                </div>
              </div>             

            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-2"></div>
      <div class="col-md-8 clearfix">
        <% if (estado=="A") { %>
          <button class="btn guardar fl btn-lg mr5 btn-success">Guardar</button>
          <% if (controlador_fiscal != "") { %>
            <button class="btn imprimir_x fl btn-lg btn-default mr5">Imprimir X</button>
            <button class="btn imprimir_z fl btn-lg btn-default mr5">Imprimir Z</button>
          <% } %>
          <button class="btn cerrar fr btn-lg btn-danger m-l">Cerrar caja</button>
        <% } else if (estado == "C") { %>
          <% if (Math.max(control.check("caja_diaria"),control.check("cajas_diarias")) >= 3) { %>
            <% if (LOCAL == 0 && (MEGASHOP == 1 || (MEGASHOP == 0 && confirmada == 0))) { %>
              <button class="btn confirmar fl btn-lg m-r btn-info">Confirmar</button>
              <button class="btn guardar fl btn-lg m-r btn-success">Guardar</button>
            <% } %>
          <% } %>
          <button class="btn imprimir fl btn-lg btn-default">Imprimir</button>
        <% } else if (estado=="X") { %>
          <button class="btn guardar fl btn-lg btn-success">Guardar</button>
          <% if (controlador_fiscal != "") { %>
            <button class="btn imprimir_x fl btn-lg btn-default mr5">Imprimir X</button>
            <button class="btn imprimir_z fl btn-lg btn-default mr5">Imprimir Z</button>
          <% } %>
        <% } %>
      </div>
    </div>

  </div>
</div>
</script>

<script type="text/template" id="caja_diaria_tarjetas_panel_template">
<div class="panel panel-default">
  <div class="panel-heading">
    <b>Detalle de tarjetas</b>
  </div>
  <div class="panel-body">
    <div class="b-a" style="height: 300px; overflow: auto;">
      <table id="cajas_diarias_table" class="table table-small table-striped sortable m-b-none default footable">
        <thead>
          <tr>
            <th style="width:20px;"></th>
            <th>Fecha</th>
            <th>Comprobante</th>
            <th>Tarjeta</th>
            <th>Lote</th>
            <th>Cup&oacute;n</th>
            <th>Cuotas</th>
            <th class="tar">Importe</th>
            <th class="tar">Inter&eacute;s</th>
            <th class="tar">Total</th>
          </tr>
        </thead>
        <tbody></tbody>
        <tfoot class="bg-important">
          <tr>
            <td id="caja_diaria_cantidad_cupones" colspan="7"></td>
            <td class="tar" id="caja_diaria_total_importe">$ 0.00</td>
            <td class="tar" id="caja_diaria_total_intereses">$ 0.00</td>
            <td class="tar" id="caja_diaria_total">$ 0.00</td>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>
</div>
</script>


<script type="text/template" id="caja_diaria_gastos_panel_template">
<div class="panel panel-default">
  <div class="panel-heading">
    <b>Detalle de gastos en efectivo</b>
  </div>
  <div class="panel-body">
    <div class="form-group">
      <button class="btn btn-info nuevo_gasto">Agregar gasto</button>
    </div>
    <div class="b-a" style="height: 300px; overflow: auto;">
      <table id="cajas_diarias_gastos_table" class="table table-striped sortable m-b-none default footable">
        <thead>
          <tr>
            <th>Fecha</th>
            <th>Concepto</th>
            <th>Proveedor</th>
            <th>Observacion</th>
            <th class="tar">Total</th>
            <th style="width:20px;"></th>
            <th style="width:20px;"></th>
          </tr>
        </thead>
        <tbody></tbody>
        <tfoot class="bg-important">
          <tr>
            <td colspan="4"></td>
            <td class="tar bold" id="caja_diaria_gastos_total">$ 0.00</td>
            <td></td>
            <td></td>
          </tr>
        </tfoot>
      </table>
    </div>
    <div class="oh tar mt20">
      <button class="btn btn-default cerrar">Aceptar</button>
    </div>
  </div>
</div>
</script>

<script type="text/template" id="gastos_edit_panel_template">
<div class="panel panel-default">
  <div class="panel-heading">
    <%= (id == undefined)?"Cargar gasto":"Editar gasto" %>
  </div>
  <div class="panel-body">
    <div class="form-group">
      <label class="control-label">Tipo de Gasto</label>
      <select class="form-control esc" name="id_tipo_gasto" id="gastos_tipo"></select>
    </div>
    <div class="form-group">
      <label class="control-label">Proveedor</label>
      <select class="form-control esc" name="id_proveedor" id="gastos_proveedor"></select>
    </div>
    <div class="form-group">
      <label class="control-label">Efectivo</label>
      <input type="text" value="<%= total %>" name="total" class="form-control esc" id="gastos_total"/>
    </div>
    <div class="form-group">
      <label class="control-label">Observaciones</label>
      <textarea name="observaciones" class="h80 form-control"><%= observaciones %></textarea>
    </div>
  </div>
  <div class="panel-footer tar">
    <button class="btn btn-success guardar">Guardar</button>
  </div>
</div>
</script>


<script type="text/template" id="gastos_item">
  <td class='ver'><%= fecha %></td>
  <td class='ver'><span class="text-info"><%= tipo_gasto %></span></td>
  <td class='ver'><%= proveedor %></td>
  <td class='ver'><%= observaciones %></td>
  <td class="ver tar">$ <%= Number(total).toFixed(2) %></td>
  <td><span class="btn btn-white edit cp" data-id='<%= id %>'><i class='fa fa-pencil'></i></td>
  <td><span class="btn btn-white delete cp" data-id='<%= id %>'><i class='fa fa-trash'></i></span></td>
</script>