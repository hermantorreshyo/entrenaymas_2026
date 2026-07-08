<script type="text/template" id="sindi_practicas_panel_template">
<% if (small == 0) { %>
  <div class="panel-body clearfix">
    <div class="row">
      <div class="col-md-6 col-lg-3 sm-m-b">
        <div class="search_container">
        </div>
      </div>
        <div class="col-md-6 col-lg-offset-3 col-lg-6 text-right">
          <a class="btn btn-info btn-addon nuevo" href="javascript:void(0)"><i class="fa fa-plus"></i>&nbsp;&nbsp;Nuevo&nbsp;&nbsp;</a>
        </div>
    </div>
  </div>
<% } %>
<div class="<%= (small==0) ? "panel-body":"panel-footer" %>">
  <% if (small == 1) { %>
    <p class="tac">Ultimos Bonos de Practicas del Afiliado</p>
  <% } %>
  <div class="b-a table-responsive">
    <table id="sindi_practicas_table" class="table table-small table-striped sortable m-b-none default footable">
      <thead>
        <tr>
          <th class="tac" data-sort-by="numero">Práctica Nº</th>
          <th class="tac" data-sort-by="fecha">Fecha</th>
          <th colspan="2" class="tac" data-sort-by="id_paciente">Afiliado</th>
          <% if (small==0) { %>
            <th class="tac" data-sort-by="id_condicion_especial">Condición Especial</th>
            <th class="tac" data-sort-by="id_hospital">Hospital</th>
          <% } %>
          <th class="tac" data-sort-by="id_importe">Importe</th>
          <th class="tac">Estado</th>
        </tr>
      </thead>
      <tbody></tbody>
      <tfoot class="pagination_container hide-if-no-paging"></tfoot>
    </table>
  </div>
</div>
</script>


<script type="text/template" id="sindi_practicas_item">
	<td class="<%= (small==0)?"ver":"" %> tac"><span class='<%= (small==0)?"text-info":"text-default" %>'><%= numero %></span></td>
	<td class="<%= (small==0)?"ver":"" %> tac"><span class='<%= (small==0)?"text-info":"text-default" %>'><%= moment(fecha).format('DD/MM/YYYY') %></span></td>
  <td class="<%= (small==0)?"ver":"" %> tar"><span class='<%= (small==0)?"text-info":"text-default" %>'><%= codigoafiliado %>-<%= identificadorafiliado %></span></td>
  <td class="<%= (small==0)?"ver":"" %>"><span class='<%= (small==0)?"text-info":"text-default" %>'><%= nombreafiliado %></span></td>
  <% if (small==0) { %>
	  <td class="ver tac"><span class='text-info'><%= condicionespecial %></span></td>
	  <td class="ver tac"><span class='text-info'><%= (hospital == 1)?"Si":"No" %></span></td>
  <% } %>
	<td class="<%= (small==0)?"ver":"" %> tac"><span class='<%= (small==0)?"text-info":"text-default" %>'>$ <%= importe %></span></td>
	<td class="<%= (small==0)?"ver":"" %> tac"><span style="display: block" class="label label-<%= (anulada == 0)?'success">Entregada':'danger">Anulada' %></span></td>
</script>

<script type="text/template" id="sindi_practicas_edit_panel_template">
<form onsubmit="return false" class="modal-content">

  <div class='modal-header' style="height: 53px">
    <div class="row">
      <div class="col-md-3">
        <b><%= (id == undefined) ? 'Nueva Práctica' : 'Práctica Nº '+numero %><b>
      </div>
      <div class="col-md-3 tac">
        <%= (id == undefined) ? '' : fecha %>
      </div>
      <div class="col-md-6 tar">
        <% if (id == undefined) { %>
          (<%= (window.afiliado == null)?'':window.afiliado.codigo %>/<%= (window.afiliado == null)?'':window.afiliado.identificador %>)<b> <%= (window.afiliado == null)?'':window.afiliado.nombre %></b>
        <% } else { %>
          (<%= codigoafiliado %>/<%= identificadorafiliado %>)<b> <%= nombreafiliado %></b>
        <% } %>
      </div>
    </div>
  </div>
  <div class="modal-body">

    <div class="row">
      <div class="col-md-3" style="border-right: 1px solid lightgrey">
        <div class="row">
          <div class="col-md-12">
            <div class="form-group">
              <label class="control-label">Tipo de Practica <i id="limite_superado_alerta" style="display:none" class="fa fa-exclamation-triangle text-danger"></i></label>
              <select <%= (id!=undefined)?"disabled":"" %> name="id_tipo_practica" class="form-control" id="sindi_practicas_tipos"></select>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            <div class="form-group">
              <label class="control-label">Condición Especial <i id="condicion_especial_alerta" style="display:none" class="fa fa-exclamation-triangle text-warning"></i></label>
              <select <%= (id!=undefined)?"disabled":"" %> name="id_condicion_especial" class="form-control" id="sindi_practicas_id_condicion_especial">
              </select>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            <div class="form-group">
              <label class="control-label">Tipo de Bono</label>
              <select <%= (id!=undefined)?"disabled":"" %> name="id_tipo_bono" class="form-control" id="sindi_practicas_id_tipo_bono">
                <option <%= (id_tipo_bono=="1")?"selected":"" %> value="1">Ambulatorio</option>
                <option <%= (id_tipo_bono=="2")?"selected":"" %> value="2">Internación</option>
              </select>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            <div class="form-group">
              <label class="i-checks m-b-none">
                <input <%= (id!=undefined)?"disabled":"" %> type="checkbox" class="esc check-row" value="1" <%= (hospital==1)?"checked":"" %> name="hospital" id="sindi_practicas_hospital" ><i></i>
                Hospital
              </label>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-9">
        <div class="clearfix" style="<%= (id!=undefined)?"display:none":"" %>">
          <div class="col-sm-2 p0" style="width: 80px">
            <div class="input-group" style="width: 100%">
              <label class="control-label fs14">Codigo</label>
              <select id="sindi_practicas_item_codigo" class="form-control no-model"></select>
            </div>
          </div>
          <div class="col-sm-5 p0" style="width: 292px">
            <div class="input-group w100p">
              <label class="control-label fs14">Practica</label>
              <select id="sindi_practicas_item_nombre" class="form-control no-model"></select>
            </div>
          </div>
          <div class="col-sm-2 p0">
            <div class="input-group">
              <label class="control-label fs14">Precio Unit.</label>
              <input type="number" class="form-control no-model action tac no-spinner" min="0" step="0.01" id="sindi_practicas_item_precio"/>
            </div>
          </div>
          <div class="col-sm-2 p0">
            <div class="input-group">
              <label class="control-label fs14">Cantidad</label>
              <input type="number" class="form-control action no-model tac no-spinner" min="1" step="1" autocomplete="off" value="1" id="sindi_practicas_item_cantidad"/>
            </div>
          </div>
          <div class="col-sm-1 p0">
            <div class="input-group">
              <label class="control-label fs14">Añadir</label>
              <button title="Ingresar Práctica" class="clearfix agregar_item btn btn-info ml0" style="width: 100%"><i class="fa fa-plus"></i></button>
            </div>
          </div>
        </div>
        <div class="b-a" style="overflow: auto; margin-top: 15px;max-height:280px">
          <table id="sindi_practicas_items" class="table table-small sortable m-b-none default footable">
            <thead class="bg-light">
              <tr>
                <th class="w75">Codigo</th>
                <th>Practica</th>
                <th class="w75">Cantidad</th>
                <th class="w75">Precio</th>
                <th class="w75">Subtotal</th>
                <th class="w25"></th>
              </tr>
            </thead>
            <tbody>
              <% for(var i=0;i<items.length;i++) { %>
                <% var o = items[i] %>
                <tr>
                  <td><%= o.codigo %></td>
                  <td><%= o.nombre %></td>
                  <td><%= o.cantidad %></td>
                  <td><%= o.importe_unitario %></td>
                  <td><%= Number(o.cantidad * o.importe_unitario).toFixed(2) %></td>
                  <td></td>
                </tr>
              <% } %>
            </tbody>
          </table>
        </div>
      </div>
    </div>

  </div>
    <div id="tabla_footer_practicas"></div>
  <div class="modal-footer">
    <a class="btn btn-default pull-left cerrar">Cerrar</a>
    <button style="<%= (id!=undefined)?"display:none":"" %>" class="btn guardar btn-success">Guardar</button>
    <button style="<%= (id==undefined || anulada == 1)?"display:none":"" %>" class="btn anular btn-danger">Anular</button>
  </div>

</form>
</script>