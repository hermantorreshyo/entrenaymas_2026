<script type="text/template" id="sindi_reintegros_panel_template">
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
    <p class="tac">Ultimos Reintegros del Afiliado</p>
  <% } %>
  <div class="b-a table-responsive">
    <table id="sindi_reintegros_table" class="table table-small table-striped sortable m-b-none default footable">
      <thead>
        <tr>
          <th class="tac" data-sort-by="numero">Reintegro Nº</th>
          <th class="tac" data-sort-by="fecha">Fecha</th>
          <th colspan="2" class="tac" data-sort-by="id_afiliado">Afiliado</th>
          <% if (small==0) { %>
            <th class="tac" data-sort-by="fecha_documento">Fecha Doc.</th>
            <th class="tac" data-sort-by="importe_documento">Importe Doc.</th>
          <% } %>
          <th class="tac" data-sort-by="importe_reintegro">Reintegro</th>
          <th class="tac">Estado</th>
        </tr>
      </thead>
      <tbody></tbody>
      <tfoot class="pagination_container hide-if-no-paging"></tfoot>
    </table>
  </div>
</div>
</script>


<script type="text/template" id="sindi_reintegros_item">
	<td class="<%= (small==0)?"ver":"" %> tac"><span class='<%= (small==0)?"text-info":"text-default" %>'><%= numero %></span></td>
	<td class="<%= (small==0)?"ver":"" %> tac"><span class='<%= (small==0)?"text-info":"text-default" %>'><%= moment(fecha).format('DD/MM/YYYY') %></span></td>
  <td class="<%= (small==0)?"ver":"" %> tar"><span class='<%= (small==0)?"text-info":"text-default" %>'><%= codigoafiliado %>-<%= identificadorafiliado %></span></td>
	<td class="<%= (small==0)?"ver":"" %>"><span class='<%= (small==0)?"text-info":"text-default" %>'><%= nombreafiliado %></span></td>
  <% if (small==0) { %>
  	<td class="ver tac"><span class='text-info'><%= moment(fecha_documento).format('DD/MM/YYYY') %></span></td>
  	<td class="ver tac"><span class='text-info'>$ <%= importe_documento %></span></td>
  <% } %>
	<td class="<%= (small==0)?"ver":"" %> tac"><span class='<%= (small==0)?"text-info":"text-default" %>'>$ <%= importe_reintegro %></span></td>
	<td class="<%= (small==0)?"ver":"" %> tac tac"><span style="display: block" class="label label-<%= (anulada == 0)?'success">Entregado':'danger">Anulada' %></span></td>
</script>

<script type="text/template" id="sindi_reintegros_edit_panel_template">
<form onsubmit="return false" class="modal-content">
  <div class='modal-header' style="height: 53px">
    <div class="row">
      <div class="col-md-3">
        <b><%= (id == undefined) ? 'Nuevo Reintegro' : 'Reintegro Nº '+numero %><b>
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
        <div class="col-md-6">
          <div class="form-group">
            <label class="control-label">Tipo de Reintegro</label>
            <select <%= (id!=undefined)?"disabled":"" %> name="id_tipo_reintegro" class="form-control" id="sindi_reintegros_id_tipo_reintegro">
            </select>
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label class="control-label">O.S. / Sindicato</label>
            <select <%= (id!=undefined)?"disabled":"" %> name="id_os_sindi" class="form-control" id="sindi_reintegros_id_os_sindi">
              <option <%= (id_os_sindi==1)?"selected":"" %> value="1" selected>Sindicato</option>
              <option <%= (id_os_sindi==2)?"selected":"" %> value="2">Obra Social</option>
            </select>
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label class="control-label">Delegación</label>
            <select <%= (id!=undefined)?"disabled":"" %> name="id_delegacion" class="form-control" id="sindi_reintegros_id_delegacion">
              <option <%= (id_delegacion==1)?"selected":"" %> value="1" selected>Central</option>
              <option <%= (id_delegacion==2)?"selected":"" %> value="2">Chacabuco</option>
              <option <%= (id_delegacion==3)?"selected":"" %> value="3">Rojas</option>
              <option <%= (id_delegacion==4)?"selected":"" %> value="4">Carmen de Areco</option>
              <option <%= (id_delegacion==5)?"selected":"" %> value="5">San Antonio de Areco</option>
              <option <%= (id_delegacion==6)?"selected":"" %> value="6">San Andres de Giles</option>
              <option <%= (id_delegacion==7)?"selected":"" %> value="7">Mercedes</option>
            </select>
          </div>
        </div>
      </div>
      <hr class="style4 mt5 mb5">
      <div class="row">
        <div class="col-md-3">
          <div class="form-group">
            <label class="control-label">Factura</label>
            <input <%= (id!=undefined)?"disabled":"" %> type="text" name="factura" autocomplete="off" class="form-control tac no-spinner" id="sindi_reintegros_factura" value="<%= factura %>">
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label class="control-label">Recibo</label>
            <input <%= (id!=undefined)?"disabled":"" %> type="text" name="recibo" autocomplete="off" class="form-control tac no-spinner" id="sindi_reintegros_recibo" value="<%= recibo %>">
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label class="control-label">Fecha Documento</label>
            <input required <%= (id!=undefined)?"disabled":"" %> type="date" name="fecha_documento" value="<%= fecha_documento %>" class="form-control tac" id="sindi_reintegros_fecha_documento">
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label class="control-label">Importe Documento</label>
            <input <%= (id!=undefined)?"disabled":"" %> type="number" min="0" step="0.01" name="importe_documento" autocomplete="off" class="form-control tac no-spinner" id="sindi_reintegros_importe_documento" value="<%= importe_documento %>"/>
          </div>
        </div>
      </div>
      <hr class="style4 mt5 mb5">
      <div class="row">
        <div class="col-md-9">
          <div class="form-group">
            <label class="control-label">Detalle</label>
            <input <%= (id!=undefined)?"disabled":"" %> type="text" name="detalle" autocomplete="off" class="form-control" id="sindi_reintegros_detalle" value="<%= detalle %>" placeholder="Detalle . . ."/>
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label class="control-label">Importe Reintegro</label>
            <input <%= (id!=undefined)?"disabled":"" %> type="number" min="0" step="0.01" name="importe_reintegro" autocomplete="off" class="form-control tac no-spinner" id="sindi_reintegros_importe_reintegro" value="<%= importe_reintegro %>"/>
          </div>
        </div>
      </div>

  </div>
  <div id="tabla_footer_reintegros"></div>
  <div class="modal-footer">
    <a class="btn btn-default pull-left cerrar">Cerrar</a>
    <% if (id == undefined) { %>
      <button class="btn guardar btn-success">Guardar</button>
    <% } else { %>
      <% if (permiso > 2 && anulada == 0) { %>
        <button class="btn anular btn-danger">Anular</button>
      <% } %>
    <% } %>
  </div>

</form>

</script>