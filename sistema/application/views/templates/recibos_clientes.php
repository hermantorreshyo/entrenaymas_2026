<script type="text/template" id="recibo_clientes_template">
<div class="panel panel-default mb0">
  <div class="panel-heading font-bold">
  Recibo de Pago
  <i class="pull-right cerrar_lightbox glyphicon glyphicon-remove cp"></i>
  </div>
  <div class="panel-body pb0">

    <input id="recibo_clientes_punto_venta" type="hidden" value="<%= (typeof id_punto_venta_default != 'undefined' ? id_punto_venta_default : 0) %>" />

    <div class="form-inline m-b">
      <% if (mostrar_fecha == 1) { %>
      <label class="control-label">Fecha de Pago</label>
      <div class="ml5" style="width: 100px; display: inline-block">
        <input type="text" <%= (id!=undefined)?"disabled":""%> class="w100p form-control" id="recibo_clientes_fecha"/>
      </div>
    <% } %>
    <% if (mostrar_numero == 1) { %>

      <label class="control-label m-l">Punto Venta</label>
      <div class="ml5" style="width: 100px; display: inline-block">
        <input type="text" <%= (id!=undefined)?"disabled":""%> class="w100p form-control" id="recibo_clientes_punto_venta_numero" value="<%= punto_venta %>"/>
      </div>

      <label class="control-label m-l">Nro. de Recibo</label>
      <div class="ml5" style="width: 100px; display: inline-block">
        <input type="text" <%= (id!=undefined)?"disabled":""%> class="w100p form-control" id="recibo_clientes_numero" value="<%= numero %>"/>
      </div>
    <% } %>
    </div>
    
    <% if (mostrar_comprobantes == 1) { %>
      <h4>Comprobantes incluidos:</h4>
      <div class="b-a m-b">
        <table class="table table-small sortable m-b-none default footable" style="overflow:auto; height:100px;">
          <thead>
            <tr>
              <th>Fecha</th>
              <th>Comprobante</th>
              <th>Numero</th>
                <% if (id == undefined) { %>
                  <th class="tar">Debe</th>
                  <th class="tar">Haber</th>
                  <th class="tar">Saldo</th>
                <% } else { %>
                  <th class="tar">Total</th>
                  <th class="tar">Pago</th>
                <% } %>
            </tr>
          </thead>
          <tbody id="recibo_clientes_tabla_comprobantes" class="tbody"></tbody>
          <tfoot>
              <tr>
              <td colspan="3" class="bold fs14">TOTALES</td>
                <% if (id == undefined) { %>
                  <td class="tar bold fs14" id="recibo_clientes_total_debe"></td>
                  <td class="tar bold fs14" id="recibo_clientes_total_haber"></td>
                  <td class="tar bold fs14" id="recibo_clientes_total"></td>
              <% } else { %>
                  <td class="tar bold fs14" id="recibo_clientes_total_debe"></td>
                  <td class="tar bold fs14" id="recibo_clientes_total"></td>
              <% } %>
              </tr>
          </tfoot>
        </table>
      </div>
    <% } %>

    <h4>Formas de Pago:</h4>
    
    <div class="tab-container">
      <ul class="nav nav-tabs" role="tablist">
        <% if (mostrar_efectivo == 1) { %>
          <li class="active">
            <a href="#tab1" role="tab" data-toggle="tab"><i class="fa fa-dollar"></i>Efectivo</a>
          </li>          
        <% } %>
        <% if (mostrar_depositos == 1) { %>
          <li>
            <a href="#tab2" role="tab" data-toggle="tab"><i class="fa fa-exchange"></i>Depositos / Transf.</a>
          </li>
        <% } %>
        <% if (mostrar_cheques == 1) { %>
          <li>
            <a href="#tab5" role="tab" data-toggle="tab"><i class="fa fa-bank"></i>Cheques</a>
          </li>
        <% } %>
        <% if (mostrar_tarjetas == 1) { %>
          <li>
            <a href="#tab4" id="tab_tarjetas" role="tab" data-toggle="tab"><i class="fa fa-credit-card"></i>Tarjetas</a>
          </li>
        <% } %>
        <li>
          <a href="#tab10" id="tab_otros" role="tab" data-toggle="tab"><i class="fa fa-book"></i>Otros</a>
        </li>
        <li>
          <a href="#tab7" id="tab_observaciones" role="tab" data-toggle="tab"><i class="fa fa-comments"></i>Observaciones</a>
        </li>
      </ul>
      <div class="tab-content">
        
        <div id="tab7" class="tab-pane">
          <div class="form-group">
            <textarea class="form-control h80" <%= (id!=undefined)?"disabled":""%> placeholder="Escriba aqui algun comentario u observacion..." name="observaciones" id="recibo_observaciones"><%= observaciones %></textarea>
          </div>
        </div>

        <div id="tab1" class="tab-pane active">
          <% if (id == undefined) { %>
            <div class="clearfix m-b">
              <div class="col-md-3 col-sm-6 p0">
                <label class="text-muted">Caja</label>
                <select class="form-control" id="recibo_clientes_movimientos_efectivo_cajas">
                  <% for(var i=0;i< window.cajas.length;i++) { %>
                    <% var c = window.cajas[i] %>
                    <% if (c.tipo == 0) { %>
                      <option value="<%= c.id %>"><%= c.nombre %></option>
                    <% } %>
                  <% } %>
                </select>
              </div>
              <div class="col-md-3 col-sm-6 p0">
                <label class="text-muted">Importe</label>
                <div class="input-group">
                  <input type="text" class="w100p form-control" id="recibo_clientes_movimientos_efectivo_monto" value="0"/>
                  <span class="input-group-btn">
                    <button id="recibo_clientes_movimientos_efectivo_agregar" class="btn btn-info form-control"><i class="fa fa-plus"></i></button>
                  </span>
                </div>
              </div>
            </div>
          <% } %>
          <div class="b-a table-responsive">
            <table class="table table-small table-striped sortable m-b-none default footable">
              <thead>
                <tr>
                  <th>Caja</th>
                  <th class="tar">Monto</th>
                  <th class="w25"></th>
                </tr>
              </thead>
              <tbody class="tbody" id="recibo_movimientos_efectivo_table"></tbody>
              <tfoot>
                <tr>
                  <td class="bold">Total Efectivo</td>
                  <td class="tar bold" id="recibo_movimientos_efectivo_total">$ 0.00</td>
                  <td>&nbsp;</td>
                </tr>
              </tfoot>
            </table>            
          </div>
        </div>

        <div id="tab10" class="tab-pane">
          <div class="clearfix m-b">
            <?php /*
            <div class="col-md-3 p0">
              <label class="text-muted">Efectivo</label>
              <input type="text" <%= (id!=undefined)?"disabled":""%> class="tar input bold form-control" id="recibo_efectivo" value="<%= efectivo %>"/>
            </div>
            */ ?>
            <div class="col-md-2 p0">
              <label class="text-muted">Descuento</label>
              <input type="text" <%= (id!=undefined)?"disabled":""%> class="tar input bold form-control" id="recibo_descuento" value="<%= descuento %>"/>
            </div>
            <div class="col-md-2 p0">
              <label class="text-muted">Ret. IIBB</label>
              <input type="text" <%= (id!=undefined)?"disabled":""%> class="tar input bold form-control" id="recibo_retencion_iibb" value="<%= retencion_iibb %>"/>
            </div>
            <div class="col-md-2 p0">
              <label class="text-muted">Ret. Ganancias</label>
              <input type="text" <%= (id!=undefined)?"disabled":""%> class="tar input bold form-control" id="recibo_retencion_ganancias" value="<%= retencion_ganancias %>"/>
            </div>
            <div class="col-md-2 p0">
              <label class="text-muted">Ret. SUSS</label>
              <input type="text" <%= (id!=undefined)?"disabled":""%> class="tar input bold form-control" id="recibo_retencion_suss" value="<%= retencion_suss %>"/>
            </div>
            <div class="col-md-2 p0">
              <label class="text-muted">Ret. IVA</label>
              <input type="text" <%= (id!=undefined)?"disabled":""%> class="tar input bold form-control" id="recibo_retencion_iva" value="<%= retencion_iva %>"/>
            </div>
            <div class="col-md-2 p0">
              <label class="text-muted">Ret. Otras</label>
              <input type="text" <%= (id!=undefined)?"disabled":""%> class="tar input bold form-control" id="recibo_retencion_otras" value="<%= retencion_otras %>"/>
            </div>
          </div>
        </div>

        <div id="tab2" class="tab-pane">
          <% if (id == undefined) { %>
            <div class="clearfix m-b">
              <div class="col-md-3 col-sm-6 p0">
                <label class="text-muted">Cuenta</label>
                <select class="form-control" id="recibo_clientes_depositos_cajas">
                  <% for(var i=0;i< window.cajas.length;i++) { %>
                    <% var c = window.cajas[i] %>
                    <% if (c.tipo == 1) { %>
                      <option value="<%= c.id %>"><%= c.nombre %></option>
                    <% } %>
                  <% } %>
                </select>
              </div>
              <div class="col-md-3 col-sm-6 p0">
                <label class="text-muted">Importe</label>
                <div class="input-group">
                  <input type="text" class="w100p form-control" id="recibo_clientes_depositos_monto" value="0"/>
                  <span class="input-group-btn">
                    <button id="recibo_clientes_depositos_agregar" class="btn btn-info form-control"><i class="fa fa-plus"></i></button>
                  </span>
                </div>
              </div>
            </div>
          <% } %>
          <div class="b-a table-responsive">
            <table class="table table-small table-striped sortable m-b-none default footable">
              <thead>
                <tr>
                  <th>Caja</th>
                  <th class="tar">Monto</th>
                  <th class="w25"></th>
                </tr>
              </thead>
              <tbody class="tbody" id="recibo_depositos_table"></tbody>
              <tfoot>
                <tr>
                  <td class="bold">Total Depositos</td>
                  <td class="tar bold" id="recibo_depositos_total">$ 0.00</td>
                  <td>&nbsp;</td>
                </tr>
              </tfoot>
            </table>            
          </div>
        </div>

        <div id="tab5" class="tab-pane">
          <% if (id == undefined) { %>
            <div class="clearfix m-b">
              <button class="btn btn-sm btn-addon btn-default" id="recibo_cheques_terceros"><i class="fa fa-search"></i> Buscar cheque de tercero</button>
              <!--<button class="btn btn-sm btn-addon btn-default" id="recibo_cheques_terceros_nuevo"><i class="fa fa-plus"></i> Nuevo</button>-->
            </div>
            <div class="clearfix m-b">
              <div class="col-md-2 col-sm-6 p0">
                <label class="text-muted">F. Emision</label>
                <input type="text" class="form-control action no-model" id="recibo_cheques_fecha_emision"/>
              </div>          
              <div class="col-md-2 col-sm-6 p0">
                <label class="text-muted">F. Cobro</label>
                <input type="text" class="form-control action no-model" id="recibo_cheques_fecha_cobro"/>
              </div>
              <div class="col-md-2 col-sm-6 p0">
                <label class="text-muted">Banco</label>
                <select id="recibo_cheques_bancos" class="form-control no-model">
                  <option value="0">Banco</option>
                  <% for(var i=0;i<bancos.length;i++) { %>
                  <% var banco = bancos[i] %>
                  <option value="<%= banco.id %>"><%= banco.nombre %></option>
                  <% } %>
                </select>
              </div>
              <div class="col-md-2 col-sm-6 p0">
                <label class="text-muted">Numero</label>
                <input type="text" class="form-control action no-model" id="recibo_cheques_numero"/>
              </div>
              <div class="col-md-2 col-sm-6 p0">
                <label class="text-muted">Titular</label>
                <input type="text" class="form-control action no-model" id="recibo_cheques_titular"/>
              </div>
              <div class="col-md-2 col-sm-6 p0">
                <label class="text-muted">Importe</label>
                <div class="input-group">
                  <input type="text" class="form-control no-model" id="recibo_cheques_monto"/>
                  <span class="input-group-btn">
                    <button title="Ingresar linea" id="recibo_cheques_agregar_item" class="btn btn-info form-control"><i class="fa fa-plus"></i></button>
                  </span>
                </div>
              </div>
            </div>
          <% } %>
          <div class="b-a table-responsive">
            <table class="table table-small table-striped sortable m-b-none default footable">
              <thead>
                <tr>
                  <th>Banco</th>
                  <th>N&uacute;mero</th>
                  <th>Fecha Pago</th>
                  <th class="tar">Monto</th>
                  <th></th>
                </tr>
              </thead>
              <tbody class="tbody" id="recibo_cheques_table"></tbody>
              <tfoot>
                <tr>
                  <td colspan="3" class="bold">Total Cheques</td>
                  <td class="tar bold" id="recibo_cheque_total">$ 0.00</td>
                  <td>&nbsp;</td>
                </tr>
              </tfoot>
            </table>            
          </div>
        </div>

        <div id="tab4" class="tab-pane">
        <% if (id == undefined) { %>
          <div class="clearfix m-b">
                      <div class="col-md-2 col-sm-6 p0">
                          <label class="text-muted">Tarjeta</label>
              <select id="recibo_tarjetas" class="form-control"></select>
                      </div>          
                      <div class="col-md-2 col-sm-6 p0">
                          <label class="text-muted">Lote</label>
                          <input type="text" class="form-control action no-model" id="recibo_tarjeta_lote"/>
                      </div>
                      <div class="col-md-2 col-sm-6 p0">
                          <label class="text-muted">Cupon</label>
                          <input type="text" class="form-control action no-model" id="recibo_tarjeta_cupon"/>
                      </div>
                      <div class="col-md-2 col-sm-6 p0">
                          <label class="text-muted">Importe</label>
                          <input type="text" class="form-control action no-model" id="recibo_tarjeta_importe"/>
                      </div>
                      <div class="col-md-2 col-sm-6 p0">
                          <label class="text-muted">Cuotas</label>
              <select id="recibo_tarjeta_cuotas" class="form-control no-model">
                <option>1</option>
                <option>2</option>
                <option>3</option>
                <option>4</option>
                <option>5</option>
                <option>6</option>
                <option>7</option>
                <option>8</option>
                <option>9</option>
                <option>10</option>
                <option>11</option>
                <option>12</option>
                <option>13</option>
                <option>14</option>
                <option>15</option>
                <option>16</option>
                <option>17</option>
                <option>18</option>
                <option>19</option>
                <option>20</option>
                <option>21</option>
                <option>22</option>
                <option>23</option>
                <option>24</option>
              </select>
                      </div>
                      <div class="col-md-2 col-sm-6 p0">
                          <label class="text-muted">Interes</label>
                          <div class="input-group">
                              <input type="text" class="form-control no-model" disabled id="recibo_tarjeta_interes"/>
                              <span class="input-group-btn">
                                <button title="Ingresar linea" id="recibo_tarjeta_agregar_item" class="btn btn-info form-control"><i class="fa fa-plus"></i></button>
                              </span>
                          </div>
                      </div>
                  </div>
        <% } %>
        <div class="b-a table-responsive">
          <table class="table table-small table-striped sortable m-b-none default footable">
            <thead>
              <tr>
                <th>Tarjeta</th>
                <th>Lote</th>
                <th>Cup&oacute;n</th>
                <th>Cuotas</th>
                <th>Importe</th>
                <th></th>
              </tr>
            </thead>
            <tbody class="tbody" id="recibo_tarjetas_table"></tbody>
            <tfoot>
              <tr>
                <td colspan="4" class="bold">Total Tarjetas</td>
                <td class="tar bold" id="recibo_tarjetas_total">$ 0.00</td>
                <td>&nbsp;</td>
              </tr>
            </tfoot>
          </table>            
        </div>
        </div>

      </div>
    </div>
  </div>
  <div class="panel-footer clearfix">
    <div class="row">
    <div class="col-md-3 col-sm-6">
      <label class="control-label bold fs16">TOTAL RECIBIDO:</label>
      <input type="text" class="tar input bold fs16 form-control" disabled id="recibo_total_valores_entregados"/>
    </div>
    <div class="col-md-3 col-sm-6">
        <label class="control-label">Vuelto:</label>
      <input type="text" <%= (id!=undefined)?"disabled":""%> class="tar input w100p form-control" id="recibo_vuelto" value="<%= vuelto %>"/>
    </div>
    <div class="col-md-3 col-sm-6">
      <label class="control-label">Diferencia:</label>
      <input type="text" <%= (id!=undefined)?"disabled":""%> class="tar input form-control no-model" disabled id="recibo_total_diferencia"/>
    </div>
    <% if (id == undefined) { %>
      <div class="col-md-3 col-sm-6 tar">
        <label class="control-label">&nbsp;&nbsp;</label>
        <div>
          <button class="btn btn-success guardar">Guardar</button>
        </div>
      </div>
    <% } else { %>
      <div class="col-md-3 col-sm-6 tar">
        <label class="control-label">&nbsp;&nbsp;</label>
        <div>
          <button class="btn btn-primary imprimir_recibo">Imprimir</button>
        </div>
      </div>    
    <% } %>
  </div>
  </div>
</div>
</script>

<script type="text/template" id="cuentas_corrientes_clientes_item_recibo_template">
  <td><%= fecha %></td>
  <td><%= hora %></td>
  <% var s = numero.split("-"); %>
  <td class="tar"><%= ((s.length >= 1) ? s[1] : "") %></td>
  <td class="tar"><%= ((s.length >= 2) ? s[2] : "") %></td>
  <td class="tar"><%= ((s.length >= 0) ? s[0] : "") %></td>
  <td class="tar">$ <%= Number(neto).toFixed(2) %></td>
  <td class="tar">$ <%= Number(parseFloat(iva21) + parseFloat(iva105)).toFixed(2) %></td>
  <td class="tar">$ <%= Number(percepcion_ib).toFixed(2) %></td>
  <td class="tar">$ <%= Number(total).toFixed(2) %></td>
</script>

<script type="text/template" id="cuentas_corrientes_clientes_item_cheques_recibo_template">
  <td><%= banco %></td>
  <td><%= numero %></td>
  <td><%= fecha_cobro %></td>
  <td class="tar"><%= Number(monto).toFixed(2) %></td>
  <td>
    <% if (!solo_lectura) { %>
    <i class="glyphicon glyphicon-remove eliminar delete text-danger" data-id="<%= id %>" />
    <% } %>
  </td>
</script>