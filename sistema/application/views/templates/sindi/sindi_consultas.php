<script type="text/template" id="sindi_consultas_panel_template">
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
    <p class="tac">Ultimos Bonos de Consultas del Afiliado</p>
  <% } %>
  <div class="b-a table-responsive">
    <table id="sindi_consultas_table" class="table table-small table-striped sortable m-b-none default footable">
      <thead>
        <tr>
          <th class="tac" data-sort-by="numero">Consulta Nº</th>
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


<script type="text/template" id="sindi_consultas_item">
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

<script type="text/template" id="sindi_consultas_edit_panel_template">
<form onsubmit="return false" class="modal-content">
  <div class='modal-header' style="height: 53px">
    <div class="row">
      <div class="col-md-3">
        <b><%= (id == undefined) ? 'Nueva Consulta' : 'Consulta Nº '+numero %><b>
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
  <div class="modal-body pb0 <%= (supero_limite==1)?"":"dn" %>">
    <div class="dt pl10">
      <div class="dtc vam">
        <span><i class="fa fa-exclamation-circle text-danger fa-2x" aria-hidden="true"></i></span>
      </div>
      <div class="dtc vam pl10">
        <span>ALCANZADO EL LIMITE MENSUAL ASIGNADO. NO PUEDE DAR BONOS DE CONSULTA A ESTE AFILIADO. </span>
      </div>
    </div>
  </div>
  <div class="modal-body pb0 <%= (supero_limite==1)?"dn":"" %>">
      <div class="row">
        <div class="col-md-3">
          <div class="form-group">
            <label class="control-label">Tipo de Bono</label>
            <select <%= (id!=undefined)?"disabled":"" %> name="id_tipo_bono" autocomplete="off" class="form-control" id="sindi_consultas_id_tipo_bono">
              <option value="1" selected>Ambulatorio</option>
              <option value="2">Internación</option>
            </select>
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label class="control-label">Condición Especial <i id="condicion_especial_alerta" style="display:none" class="fa fa-exclamation-triangle text-warning"></i></label>
            <select <%= (id!=undefined)?"disabled":"" %> name="id_condicion_especial" autocomplete="off" class="form-control" id="sindi_consultas_id_condicion_especial">
            </select>
          </div>
        </div>

        <div class="col-md-2">
          <div class="form-group">
            <label class="control-label">Importe</label>
            <input <%= (id!=undefined)?"disabled":"" %> type="number" min="0" step="0.01" name="importe" autocomplete="off" class="form-control tac no-spinner" id="sindi_consultas_importe" value="<%= importe %>"/>
          </div>
        </div>
        <div class="col-md-2">
          <div class="form-group text-center">
            <label class="control-label">Hospital</label><br>
            <label class="i-checks m-b-none">
              <input <%= (id!=undefined)?"disabled":"" %> type="checkbox" class="esc check-row" value="1" name="hospital" id="sindi_consultas_hospital" ><i></i>
            </label>
          </div>
        </div>
        <div class="col-md-2">
          <div class="form-group text-center">
            <label class="control-label">Recetarios</label><br>
            <label class="i-checks m-b-none">
              <input <%= (id!=undefined)?"disabled":"" %> type="checkbox" class="esc check-row no-model" value="1" id="sindi_consultas_recetarios" ><i></i>
            </label>
          </div>
        </div>
    <!--<div class="col-md-2">
          <div class="form-group">
            <label class="control-label">Concepto</label>
            <select <%= (id!=undefined)?"disabled":"" %> name="id_concepto" autocomplete="off" class="form-control" id="sindi_consultas_id_concepto">
              <option value="1" selected></option>
              <option value="2">Gastos</option>
              <option value="3">Honorarios</option>
            </select>
          </div>
        </div> -->
      </div>
    
  </div>
  <% var consreal = ((window.afiliado != undefined)?window.afiliado.limites[0].consultas_realizadas:"0")  %>
  <% var conscant = ((window.afiliado != undefined)?window.afiliado.limites[0].cantidad:"0") %>
  <div class="tac text-muted fs12"><span>(<%= consreal %> Consultas realizadas este mes de las <%= conscant %> que tiene asignadas como limite)</span></div>    
  <div id="tabla_footer"></div>
  <div class="modal-footer">
    <a class="btn btn-default pull-left cerrar">Cerrar</a>
    <% if (id==undefined) { %>
      <% if (supero_limite == 0) { %>
        <button class="btn guardar btn-success">Guardar</button>
      <% } %>
    <% } else { %>
      <% if (permiso > 2 && anulada == 0) { %>
        <button class="btn anular btn-danger">Anular</button>
      <% } %>
    <% } %>
  </div>

</form>

</script>