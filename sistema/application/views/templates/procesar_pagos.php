<script type="text/template" id="procesar_pagos_resultados_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <h1 class="m-n font-thin h3">Caja por Reparto</h1>
</div>
<div class="wrapper-md ng-scope pb0">
  <div class="panel panel-default">
    <div class="panel-body">
      <div class="form-inline">
        <label class="control-label fl mt7 mr15">Reparto</label>
        <div class="form-group fl w100">
          <input type="text" id="procesar_pagos_fecha" value="<?php echo date("d/m/Y"); ?>" class="form-control">
        </div>
        <div class="form-group fl w60">
          <input type="text" class="form-control" id="procesar_pagos_numero" value="1"/>      
        </div>
        <div class="form-group fl w120">
          <select class="form-control" id="procesar_pagos_puntos_venta">
            <option value="0">Punto de Venta</option>
            <% for(var i=0;i< puntos_venta.length;i++) { %>
              <% var pv = puntos_venta[i] %>
              <% if (ID_SUCURSAL == 0 || ID_SUCURSAL == pv.id_sucursal) { %>
                <option value="<%= pv.id %>"><%= pv.nombre %></option>
              <% } %>  
            <% } %>
          </select>
        </div>
        <button class="btn fl ml10 btn-success buscar btn-addon">
          <i class="fa fa-search"></i>
          <span class="hidden-xs">Buscar</span>
        </button>
      </div>
    </div>
  </div>
</div>  
  
<div class="wrapper-md pb0 pt0">
  <div class="tab-container">
    <ul class="nav nav-tabs" role="tablist">
      <li class="active">
        <a href="#tab1" role="tab" data-toggle="tab">Pagos</a>
      </li>
      <li>
        <a href="#tab2" role="tab" data-toggle="tab">Gastos</a>
      </li>
      <li>
        <a href="#tab4" role="tab" data-toggle="tab">Cobranzas</a>
      </li>
      <li>
        <a href="#tab3" role="tab" data-toggle="tab">Totales</a>
      </li>
    </ul>
    <div class="tab-content">
      <div id="tab1" class="tab-pane active panel-body">
        <div class="b-a table-responsive">
          <table id="procesar_pagos_tabla" class="table sortable m-b-none default">
            <thead class="thead">
              <th class="w25">Pago</th>
              <th class="col-xs-0">Empresa</th>
              <th class="col-xs-0">Fecha</th>
              <th class="col-xxs-0">Comprobante</th>
              <th class="col-xs-0 w100 tar">Neto</th>
              <th class="col-xs-0 w100 tar">IVA</th>
              <th class="w100 tar">Total</th>
              <th class="w150 tar">Efectivo</th>
            </thead>
            <tbody class="tbody"></tbody>
            <tfoot>
              <tr>
                <td></td>
                <td class="col-xs-0"></td>
                <td class="col-xs-0"></td>
                <td class="col-xxs-0"></td>
                <td class="col-xs-0"></td>
                <td class="col-xs-0"></td>
                <td id="reparto_total_facturacion" class="tar bold fs16">$ 0.00</td>
                <td id="reparto_total_efectivo" class="tar bold fs16">$ 0.00</td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
      <div id="tab2" class="tab-pane panel-body">
        <div class="row">
          <div class="form-group col-lg-3 col-sm-4 col-xs-6">
            <input type="hidden" id="repartos_concepto_id"/>
            <div class="input-group">
              <input type="text" class="form-control" placeholder="Concepto" id="repartos_concepto_codigo"/>
              <span class="input-group-btn">
              <button class="btn btn-info" id="repartos_buscar_conceptos" type="button">Buscar</button>
              </span>
            </div>
          </div>
          <div class="form-group col-lg-2 col-sm-3 col-xs-6">
            <input type="text" class="form-control" disabled id="repartos_concepto_nombre" />
          </div>
          <div class="form-group col-lg-2 col-sm-3 col-xs-6">
            <input type="text" value="0.00" class="form-control" id="repartos_gasto"/>
          </div>
          <div class="form-group col-lg-2 col-sm-2 col-xs-6">
            <button class="btn btn-success" id="repartos_agregar_gasto">Agregar</button>
          </div>
        </div>
        <div class="row">
          <div class="col-xs-12">
            <div class="b-a oh">
              <table id="repartos_tabla_gastos" class="table sortable m-b-none default footable">
                <thead class="bg-light">
                  <tr>
                    <th>Concepto</th>
                    <th>Costo</th>
                    <th class="w25"></th>
                    <th class="w25"></th>
                  </tr>
                </thead>
                <tbody></tbody>
                <tfoot>
                  <tr>
                    <td></td>
                    <td class="bold" id="repartos_tabla_total_gastos">0.00</td>
                    <td></td>
                    <td></td>
                  </tr>
                </tfoot>
              </table>
            </div>
          </div>
        </div>
      </div>
      
      <div id="tab4" class="tab-pane panel-body">
        <div class="row">
          <div class="form-group col-lg-2 col-md-3 col-sm-4 col-xs-6">
            <div class="input-group">
              <input type="text" class="form-control action" id="repartos_codigo_cliente" placeholder="Cliente"/>
              <span class="input-group-btn">
              <button title="Buscar" id="repartos_buscar_cliente" class="btn btn-default" type="button"><i class="fa fa-search"></i></button>
              </span>
            </div>
          </div>
          <div class="form-group col-lg-2 col-md-2 col-sm-3 col-xs-6">
            <input type="text" class="form-control" id="repartos_cliente_nombre" value="" disabled />
          </div>
          <div class="form-group col-lg-2 col-sm-3 col-xs-6">
            <input type="text" value="0.00" class="form-control" id="repartos_cliente_total"/>
          </div>
          <div class="form-group col-lg-2 col-sm-2 col-xs-6">
            <button class="btn btn-success" id="repartos_agregar_cobranza">Agregar</button>
          </div>
        </div>
        <div class="row">
          <div class="col-xs-12">
            <div class="b-a oh">
              <table id="repartos_tabla_cobranzas" class="table sortable m-b-none default footable">
                <thead class="bg-light">
                  <tr>
                    <th>Codigo</th>
                    <th>Cliente</th>
                    <th>Total</th>
                    <th class="w25"></th>
                    <th class="w25"></th>
                  </tr>
                </thead>
                <tbody></tbody>
                <tfoot>
                  <tr>
                    <td></td>
                    <td></td>
                    <td class="bold" id="repartos_tabla_total_cobranzas">0.00</td>
                    <td></td>
                    <td></td>
                  </tr>
                </tfoot>
              </table>
            </div>
          </div>
        </div>
      </div>
      
      
      <div id="tab3" class="tab-pane panel-body">
      
        <div class="form-horizontal">
          <div class="form-group">
            <label class="col-lg-2 col-md-3 col-sm-4 col-xs-12 control-label tal">Efectivo Inicial</label>
            <div class="col-lg-2 col-md-3 col-sm-4 col-xs-12">
              <input type="text" name="efectivo_inicial" id="repartos_total_efectivo_inicial" value="<%= Number(efectivo_inicial).toFixed(2) %>" class="form-control"/>
            </div>
          </div>
          <div class="form-group">
            <label class="col-lg-2 col-md-3 col-sm-4 col-xs-12 control-label tal">Pagos recibidos</label>
            <div class="col-lg-2 col-md-3 col-sm-4 col-xs-12">
              <input type="text" disabled name="total_pagos" id="repartos_total_pagos" class="form-control" value="<%= Number(total_pagos).toFixed(2) %>"/>
            </div>
          </div>
          <div class="form-group">
            <label class="col-lg-2 col-md-3 col-sm-4 col-xs-12 control-label tal">Cobranzas realizadas</label>
            <div class="col-lg-2 col-md-3 col-sm-4 col-xs-12">
              <input type="text" disabled name="total_cobranzas" id="repartos_total_cobranzas" class="form-control" value="<%= Number(total_cobranzas).toFixed(2) %>"/>
            </div>
          </div>
          <div class="form-group">
            <label class="col-lg-2 col-md-3 col-sm-4 col-xs-12 control-label tal">Gastos realizados</label>
            <div class="col-lg-2 col-md-3 col-sm-4 col-xs-12">
              <input type="text" disabled name="total_gastos" id="repartos_total_gastos" class="form-control" value="<%= Number(total_gastos).toFixed(2) %>"/>
            </div>
          </div>
          <div class="line line-dashed b-b line-lg pull-in"></div>
          <div class="form-group">
            <label class="col-lg-2 col-md-3 col-sm-4 col-xs-12 control-label tal bold">TOTAL GENERAL</label>
            <div class="col-lg-2 col-md-3 col-sm-4 col-xs-12">
              <input type="text" disabled name="total" id="repartos_total" class="form-control bold fs16" value="<%= Number(total).toFixed(2) %>"/>
            </div>
          </div>
          <div class="line line-dashed b-b line-lg pull-in"></div>
          <div class="form-group">
            <label class="col-lg-2 col-md-3 col-sm-4 col-xs-12 control-label tal">Efectivo 1</label>
            <div class="col-lg-2 col-md-3 col-sm-4 col-xs-12">
              <input type="text" name="efectivo_1" id="repartos_efectivo_1" class="form-control" value="<%= Number(efectivo_1).toFixed(2) %>"/>
            </div>
          </div>
          <div class="form-group">
            <label class="col-lg-2 col-md-3 col-sm-4 col-xs-12 control-label tal">Efectivo 2</label>
            <div class="col-lg-2 col-md-3 col-sm-4 col-xs-12">
              <input type="text" name="efectivo_2" id="repartos_efectivo_2" class="form-control" value="<%= Number(efectivo_2).toFixed(2) %>"/>
            </div>
          </div>
          <div class="line line-dashed b-b line-lg pull-in"></div>
          <div class="form-group">
            <label class="col-lg-2 col-md-3 col-sm-4 col-xs-12 control-label tal bold">DIFERENCIA</label>
            <div class="col-lg-2 col-md-3 col-sm-4 col-xs-12">
              <input type="text" disabled name="diferencia" id="repartos_diferencia" class="form-control bold fs16" value="<%= Number(diferencia).toFixed(2) %>"/>
            </div>
          </div>
          
        </div>
        <div class="panel-footer oh">
          <button class="btn btn-success fr confirmar">Confirmar</button>
        </div>      
      </div>
    </div>
  </div>
</div>
</script>


<script type="text/template" id="procesar_pagos_item_resultados_template">
  <td><label class="i-checks m-b-none"><input tabindex="-1" <%= (estado == "C")?"disabled":"" %> <%= (estado == "C" && pagada == "1") ? "checked" : ( (estado != "C") ? "checked" : "" ) %> type="checkbox"><i></i></label></td>
  <td class="col-xs-0"><%= cliente %></td>
  <td class="col-xs-0"><%= fecha %></td>
  <td class="col-xxs-0"><%= comprobante %></td>
  <td class="tar col-xs-0"><%= neto %></td>
  <td class="tar col-xs-0"><%= Number(iva).toFixed(2) %></td>
  <td class="tar"><%= total %></td>
  <td><input type="text" <%= (estado == "C")?"disabled":"" %> class="form-control number tar efectivo cliente_<%= id_cliente %>" data-id_empresa="<%= id_empresa %>" data-id_factura="<%= id %>" data-id_cliente="<%= id_cliente %>" value="<%= (estado=='C') ? pago : total %>"/></td>
</script>