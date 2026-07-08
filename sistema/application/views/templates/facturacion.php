<script type="text/template" id="facturacion_edit_panel_template">
  <?php
   if (file_exists(APPPATH."views/templates/fact/facturacion_edit_".$empresa->id.".php")) { ?>
    <?php include(APPPATH."views/templates/fact/facturacion_edit_".$empresa->id.".php"); ?>
  <?php } else { ?>
    <% if (FACTURACION_TIPO == "pv") { %>
      <?php include("fact/facturacion_edit_pv.php"); ?>
    <% } else { %>
      <?php include("fact/facturacion_edit_comprobante.php"); ?>
    <% } %>
  <?php } ?>
</script>

<script type="text/template" id="factura_item_template">
  <% var clase = (FACTURACION_MODIFICAR_ITEM == 1 && FACTURACION_TIPO == "pv") ? "editar" : "" %>
  <td class="<%= clase %>"><%= Number(cantidad).toFixed(FACTURACION_CANTIDAD_DECIMALES) %></td>
  <td class="<%= clase %>">
    <%= (anulado==1 && control.check("estadisticas_ventas")>=3)?"(ANULADO) <br/>":"" %><%= nombre %>
    <%= (typeof variante != undefined && !isEmpty(variante)) ? "<br/><span class='text-muted'>"+variante+"</span>" : "" %>
    <% if (custom_3 == 1) { %>
      <span class="label bg-warning m-l reservado">Reservado</span>
    <% } %>
    <% if (FACTURACION_MODIFICAR_ITEM == 1 && FACTURACION_TIPO == "pv" && tipo_cantidad == "X" && id_factura != 0) { %><span class="label bg-danger m-l">Revisar</span><% } %>
    <% if (!isEmpty(descripcion)) { %><br/><span class="text-muted"><%= descripcion %></span><% } %>
    <% if (FACTURACION_TIPO == "pv") { %>
      <% if (custom_1 != "") { %><span class="text-muted fs12 db"><%= custom_1 %></span><% } %>
    <% } %>
  </td>
  <% if (FACTURACION_TIPO == "pv") { %>
    <td class="<%= clase %>"><%= Number(precio).toFixed(FACTURACION_CANTIDAD_DECIMALES) %></td>
  <% } else { %>
    <td class="<%= clase %>"><%= Number(((discrimina_iva) ? neto : precio)).toFixed(FACTURACION_CANTIDAD_DECIMALES) %></td>
  <% } %>
  <% if (FACTURACION_TIPO != "pv") { %>
    <td><%= Number(bonificacion).toFixed(FACTURACION_CANTIDAD_DECIMALES) %>%</td>
  <% } %>
  <% if (FACTURACION_TIPO == "pv") { %>
    <td class="<%= clase %>"><%= Number(total_con_iva).toFixed(FACTURACION_CANTIDAD_DECIMALES) %></td>
  <% } else { %>
    <td class="<%= clase %>"><%= Number(((discrimina_iva) ? total_sin_iva : total_con_iva)).toFixed(FACTURACION_CANTIDAD_DECIMALES) %></td>
  <% } %>
  <% if (edicion) { %>
    <% if (FACTURACION_TIPO != "pv") { %>
      <% if (FACTURACION_MODIFICAR_ITEM == 1) { %>
        <% if ((typeof FACTURACION_PERMITIR_EDICION_FACTURA != "undefined") || (TIPO_EMPRESA != undefined && TIPO_EMPRESA == 3) || (id_articulo != 0 && id_factura == 0) || id_articulo == 0 || id_factura == 0) { %>
          <td class="w25 p5"><i title="Editar" class="fa fa-file-text-o editar text-dark" /></td>
        <% } %>
      <% } %>
      <td class="w25 p5">
        <% if ((typeof FACTURACION_PERMITIR_EDICION_FACTURA != "undefined") || (TIPO_EMPRESA != undefined && TIPO_EMPRESA == 3) || (id_articulo != 0 && id_factura == 0) || id_articulo == 0 || id_factura == 0) { %>
          <i title="Eliminar" class="glyphicon glyphicon-remove do_eliminar text-danger" />
        <% } %>
      </td>
    <% } else { %>    
      <td class="w25 p5">
        <% if (id_factura == 0) { %>
          <% if (controlador_fiscal != "") { %>
            <label class="i-checks m-b-none">
              <input class="radio check-row esc eliminar no-model" name="radio" type="radio"><i></i>
            </label>
          <% } else { %>
            <i title="Eliminar" class="glyphicon glyphicon-remove do_eliminar text-danger" />
          <% } %>
        <% } %>
      </td>
    <% } %>
  <% } %>
</script>


<script type="text/template" id="metodo_pago_panel_template">
  <div class="panel panel-default oh">
    <div class="panel-heading">Metodos de Pago</div>
    <div class="panel-body">
      <div class="form-horizontal">

        <?php // El subtotal solo aparece cuando hay un recargo por interes, ya que sino es siempre igual al TOTAL ?>
        <div id="metodo_pago_subtotal_container" class="form-group">
          <label class="control-label col-md-4">SUBTOTAL: </label>
          <div class="col-md-6">
            <input type="text" value="<%= Number(Number(subtotal) + Number(percepcion_ib)).toFixed(2) %>" disabled class="fs16 form-control bold tar" id="metodo_pago_subtotal"/>
          </div>
        </div>

        <% if (ID_EMPRESA == 135) { %>
          <div class="form-group">
            <div class="col-md-4">
              <label class="i-checks">
                <input type="checkbox" id="metodo_pago_impuesto_pais_check" class="checkbox no-model" value="1">
                <i></i> IMP. PAIS:
              </label>
            </div>
            <div class="col-md-6">
              <input type="text" value="0.00" disabled class="fs16 form-control bold tar no-model" id="metodo_pago_impuesto_pais"/>
            </div>
          </div>
        <% } %>

        <% if (base_percep_viajes > 0) { %>
          <div class="form-group">
            <label class="control-label col-md-4">PERCEP. 5% VIAJE EXT.: </label>
            <div class="col-md-6">
              <input type="text" value="0.00" disabled class="fs16 form-control bold tar no-model" id="metodo_pago_percep_viajes"/>
            </div>
          </div>
        <% } %>

        <?php // Este contenedor aparece cuando hay algun recargo en la tarjeta ?>
        <div id="metodo_pago_recargo_tarjetas_container" class="form-group">
          <label class="control-label col-md-4">RECARGO: </label>
          <div class="col-md-6">
            <input type="text" value="0.00" disabled class="fs16 form-control bold tar no-model" id="metodo_pago_interes"/>
          </div>
        </div>

        <div class="form-group">
          <label class="control-label col-md-4">TOTAL: </label>
          <div class="col-md-6">
            <input type="text" value="<%= Number(total).toFixed(2) %>" disabled class="fs16 form-control bold tar" id="metodo_pago_total"/>
          </div>
        </div>

        <div class="line line-dashed b-b line-lg"></div>

        <div class="form-group">
          <label class="control-label col-md-4">EFECTIVO: </label>
          <div class="col-md-6">
            <input type="text" class="form-control bold tar fs16 number esc keyp no-model" id="metodo_pago_efectivo"/>
          </div>
        </div>
        <% if (id_cliente != 0 && FACTURACION_OCULTAR_CUENTA_CORRIENTE == 1) { %>
          <div class="form-group">
            <label class="control-label col-md-4">CUENTA CTE.: </label>
            <div class="col-md-6">
              <input type="text" value="0.00" class="form-control bold tar keyp fs16 number" id="metodo_pago_cta_cte"/>
            </div>
          </div>
        <% } %>
        <div class="form-group">
          <label class="control-label col-md-4">TARJETAS: </label>
          <div class="col-md-6">
            <input type="text" value="<%= Number(tarjetas_0).toFixed(2) %>" disabled class="form-control bold tar fs16" id="metodo_pago_tarjetas_0"/>
          </div>
          <div class="col-md-2 pl0">
            <button id="metodo_pago_tarjetas_boton_0" class="btn btn-default keyp btn-block"><i class="fa fa-search"></i></button>
          </div>
        </div>
        <div class="form-group">
          <label class="control-label col-md-4"></label>
          <div class="col-md-6">
            <input type="text" value="<%= Number(tarjetas_1).toFixed(2) %>" disabled class="form-control bold tar fs16" id="metodo_pago_tarjetas_1"/>
          </div>
          <div class="col-md-2 pl0">
            <button id="metodo_pago_tarjetas_boton_1" class="btn btn-default keyp btn-block"><i class="fa fa-search"></i></button>
          </div>
        </div>
        <% if (id_cliente != 0) { %>
          <div class="form-group" style="<%= (MEGASHOP == 1 || ID_EMPRESA == 421)?'display:none':'' %>">
            <label class="control-label col-md-4">CHEQUES: </label>
            <div class="col-md-6">
              <input type="text" value="0.00" disabled class="form-control bold tar fs16" id="metodo_pago_cheques"/>
            </div>
            <div class="col-md-2 pl0">
              <button id="metodo_pago_cheques_boton" class="btn btn-default keyp btn-block"><i class="fa fa-search"></i></button>
            </div>
          </div>
        <% } %>
        <% if (id_cliente != 0 && FACTURACION_USA_CREDITOS_PERSONALES == 1) { %>
          <div class="form-group">
            <label class="control-label col-md-4">CREDITO: </label>
            <div class="col-md-6">
              <input type="text" value="0.00" disabled class="form-control bold tar keyp fs16 number" id="metodo_pago_creditos_personales"/>
            </div>
            <div class="col-md-2 pl0">
              <button id="metodo_pago_creditos_personales_boton" class="btn btn-default keyp btn-block"><i class="fa fa-search"></i></button>
            </div>
          </div>
        <% } %>
        <div class="line line-dashed b-b line-lg"></div>
        <div class="form-group">
          <label class="control-label col-md-4">SU VUELTO: </label>
          <div class="col-md-6">
            <input type="text" disabled style="font-size: 26px; height: 50px" class="form-control bold tar" id="metodo_pago_vuelto"/>
          </div>
        </div>

      </div>
    </div>
    <div class="panel-footer clearfix">
      <button id="metodo_pago_aceptar" class="btn btn-success guardar keyp fs16 bold fr">ACEPTAR</button>
    </div>
  </div>
</script>



<script type="text/template" id="metodo_pago_tarjeta_panel_template">
  <div class="panel panel-default oh">
    <div class="panel-heading">Tarjetas</div>
    <div class="panel-body">
      <div class="form-horizontal">
        <div class="form-group">
          <label class="col-md-4 control-label">Tarjeta: </label>
          <div class="col-md-8">
            <select class="form-control esc" id="metodo_pago_tarjeta_select" name="id_tarjeta"></select>
          </div>
        </div>
        <div class="form-group">
          <label class="col-md-4 control-label">Nro. Cuotas: </label>
          <div class="col-md-8">
            <select id="metodo_pago_tarjeta_cuotas" class="form-control esc" name="cuotas">
              <option <%= (cuotas == 1) ? "selected":"" %> value="1">1</option>
              <option <%= (cuotas == 2) ? "selected":"" %> value="2">2</option>
              <option <%= (cuotas == 3) ? "selected":"" %> value="3">3</option>
              <option <%= (cuotas == 4) ? "selected":"" %> value="4">4</option>
              <option <%= (cuotas == 5) ? "selected":"" %> value="5">5</option>
              <option <%= (cuotas == 6) ? "selected":"" %> value="6">6</option>
              <option <%= (cuotas == 7) ? "selected":"" %> value="7">7</option>
              <option <%= (cuotas == 8) ? "selected":"" %> value="8">8</option>
              <option <%= (cuotas == 9) ? "selected":"" %> value="9">9</option>
              <option <%= (cuotas == 10) ? "selected":"" %> value="10">10</option>
              <option <%= (cuotas == 11) ? "selected":"" %> value="11">11</option>
              <option <%= (cuotas == 12) ? "selected":"" %> value="12">12</option>
              <option <%= (cuotas == 13) ? "selected":"" %> value="13">13</option>
              <option <%= (cuotas == 14) ? "selected":"" %> value="14">14</option>
              <option <%= (cuotas == 15) ? "selected":"" %> value="15">15</option>
              <option <%= (cuotas == 16) ? "selected":"" %> value="16">16</option>
              <option <%= (cuotas == 17) ? "selected":"" %> value="17">17</option>
              <option <%= (cuotas == 18) ? "selected":"" %> value="18">18</option>
              <option <%= (cuotas == 19) ? "selected":"" %> value="19">19</option>
              <option <%= (cuotas == 20) ? "selected":"" %> value="20">20</option>
              <option <%= (cuotas == 21) ? "selected":"" %> value="21">21</option>
              <option <%= (cuotas == 22) ? "selected":"" %> value="22">22</option>
              <option <%= (cuotas == 23) ? "selected":"" %> value="23">23</option>
              <option <%= (cuotas == 24) ? "selected":"" %> value="24">24</option>
            </select>
          </div>
        </div>
        <div class="form-group">
          <label class="col-md-4 control-label">Cupon: </label>
          <div class="col-md-8">
            <input type="text" class="form-control esc" id="metodo_pago_tarjeta_cupon" value="<%= cupon %>" name="cupon"/>
          </div>
        </div>
        <div class="form-group">
          <label class="col-md-4 control-label">Lote: </label>
          <div class="col-md-8">
            <input type="text" class="form-control esc" id="metodo_pago_tarjeta_lote" value="<%= lote %>" name="lote"/>
          </div>
        </div>
        <div class="form-group">
          <label class="col-md-4 control-label">Importe: </label>
          <div class="col-md-8">
            <input type="text" class="form-control number esc" id="metodo_pago_tarjeta_importe" value="<%= importe %>" name="importe"/>
          </div>
        </div>
        <div class="form-group">
          <label class="col-md-4 control-label">Intereses: </label>
          <div class="col-md-8">
            <input type="text" class="form-control number esc" id="metodo_pago_tarjeta_interes" value="<%= interes %>" name="interes"/>
          </div>
        </div>
        <div class="form-group">
          <label class="col-md-4 control-label">Total: </label>
          <div class="col-md-8">
            <input type="text" class="form-control number esc" id="metodo_pago_tarjeta_total" value="<%= total %>" name="total"/>
          </div>
        </div>
      </div>
    </div>
    <div class="panel-footer clearfix">
      <button id="metodo_pago_tarjeta_eliminar" class="btn btn-danger eliminar fl">Eliminar</button>
      <button id="metodo_pago_tarjeta_aceptar" class="btn btn-success guardar fr">Aceptar</button>
    </div>
  </div>
</script>

<script type="text/template" id="metodo_pago_lista_cheques_panel_template">
  <div class="panel panel-default oh">
    <div class="panel-heading">Cheques</div>
    <div class="panel-body">
      <div class="tar oh">
        <button class="btn btn-info agregar_cheque fr">+ Agregar</button>  
      </div>
      <div class="b-a" style="overflow: auto; margin-top: 15px; height: 320px">
        <table id="tabla_items" class="table table-small sortable m-b-none default footable">
          <thead>
            <tr>
              <th>Banco</th>
              <th>Numero</th>
              <th>Emision</th>
              <th>Cobro</th>
              <th>Importe</th>
              <th></th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <% for(var i=0;i < cheques.length; i++) { %>
              <% var cheque = cheques[i] %>
              <tr>
                <td><%= cheque.banco %></td>
                <td><%= cheque.numero %></td>
                <td><%= cheque.fecha_emision %></td>
                <td><%= cheque.fecha_cobro %></td>
                <td><%= Number(cheque.importe).toFixed(2) %></td>
                <td><i data-pos="<%= i %>" class="fa fa-pencil editar_cheque"></i></td>
                <td><i data-pos="<%= i %>" class="fa fa-times text-danger eliminar_cheque"></i></td>
              </tr>
            <% } %>
          </tbody>
        </table>
      </div>
    </div>
    <div class="panel-footer clearfix">
      <button id="metodo_pago_cheque_aceptar" class="btn btn-success aceptar_cheques fr">Aceptar</button>
    </div>
  </div>
</script>

<script type="text/template" id="metodo_pago_cheque_panel_template">
  <div class="panel panel-default oh">
    <div class="panel-heading">Cheques</div>
    <div class="panel-body">
      <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            <label class="control-label">Banco</label>
            <select class="form-control esc" id="metodo_pago_banco_select" name="id_banco">
              <% for(var i=0;i< bancos.length; i++) { %>
                <% var banc = bancos[i] %>
                <option value="<%= banc.id %>"><%= banc.nombre %></option>
              <% } %>
            </select>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label class="control-label">Numero</label>
            <input type="text" class="form-control esc" id="metodo_pago_cheque_numero" value="<%= numero %>" name="numero"/>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            <label class="control-label">F. Emision</label>
            <div class="input-group">
              <input type="text" class="form-control esc" id="metodo_pago_cheque_fecha_emision" value="<%= fecha_emision %>" name="fecha_emision"/>
              <span class="input-group-btn">
                <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
              </span>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label class="control-label">F. Cobro</label>
            <div class="input-group">
              <input type="text" class="form-control esc" id="metodo_pago_cheque_fecha_cobro" value="<%= fecha_cobro %>" name="fecha_cobro"/>
              <span class="input-group-btn">
                <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
              </span>        
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            <label class="control-label">Titular</label>
            <input type="text" class="form-control esc" id="metodo_pago_cheque_cliente" value="<%= cliente %>" name="cliente"/>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label class="control-label">Importe</label>
            <input type="text" class="form-control esc number" id="metodo_pago_cheque_importe" value="<%= importe %>" name="importe"/>
          </div>
        </div>
      </div>
    </div>
    <div class="panel-footer clearfix">
      <button id="metodo_pago_cheque_aceptar" class="btn btn-success guardar fr">Aceptar</button>
    </div>
  </div>
</script>

<script type="text/template" id="metodo_pago_credito_personal_panel_template">
  <div class="panel panel-default oh">
    <div class="panel-heading">Cr&eacute;dito Personal</div>
    <div class="panel-body">
      <div class="form-horizontal">
        <div class="form-group dn">
          <label class="col-md-5 control-label">Tope cr&eacute;dito: </label>
          <div class="col-md-7">
            <input type="text" disabled class="form-control esc" value="<%= tope_credito %>" id="metodo_pago_credito_personal_tope_credito"/>
          </div>
        </div>
        <div class="form-group">
          <label class="col-md-5 control-label">Importe: </label>
          <div class="col-md-7">
            <input type="text" class="form-control number esc" id="metodo_pago_credito_personal_importe" value="<%= importe %>" name="importe"/>
          </div>
        </div>
        <div class="form-group">
          <label class="col-md-5 control-label">Nro. Cuotas: </label>
          <div class="col-md-7">
            <select id="metodo_pago_credito_personal_cantidad_cuotas" class="form-control esc" name="cantidad_cuotas">
              <% for(var i=2;i<=maximo_cuotas;i++) { %>
                <option <%= (cantidad_cuotas == i) ? "selected":"" %> value="<%= i %>"><%= i %></option>
              <% } %>
            </select>
          </div>
        </div>
        <div class="form-group">
          <label class="col-md-5 control-label">Valor de la cuota: </label>
          <div class="col-md-7">
            <input type="text" id="metodo_pago_credito_personal_valor_cuota" class="form-control number esc" <%= valor_cuota %> disabled id="metodo_pago_credito_personal_cuota"/>
          </div>
        </div>
        <div class="form-group">
          <label class="col-md-5 control-label">Pr&oacute;xima cuota: </label>
          <div class="col-md-7">
            <input type="text" id="metodo_pago_credito_personal_primera_cuota" value="<%= proxima_cuota %>" class="form-control no-model" disabled/>
          </div>
        </div>
      </div>
    </div>
    <div class="panel-footer clearfix">
      <button id="metodo_pago_credito_personal_eliminar" class="btn btn-danger eliminar fl">Eliminar</button>
      <button id="metodo_pago_credito_personal_aceptar" class="btn btn-success guardar fr">Aceptar</button>
    </div>
  </div>
</script>