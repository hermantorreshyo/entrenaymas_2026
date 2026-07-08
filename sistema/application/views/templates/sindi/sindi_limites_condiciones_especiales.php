<script type="text/template" id="sindi_limites_condiciones_especiales_panel_template">
  <div class="panel-body oh pb0">
    <div class="row">
      <div class="col-md-6 col-lg-3 sm-m-b">
        <div class="search_container"></div>
      </div>
      <% if (control.check("sindi_limites_condiciones_especiales") > 1) { %>
        <div class="col-md-6 col-lg-offset-3 col-lg-6 text-right">
          <a class="btn btn-info btn-addon nuevo"><i class="fa fa-plus"></i>&nbsp;&nbsp;Nuevo&nbsp;&nbsp;</a>
        </div>
      <% } %>
    </div>
  </div>
  <div class="panel-body">
    <div class="b-a table-responsive">
      <table id="sindi_limites_condiciones_especiales_table" class="table table-small table-striped sortable m-b-none default footable">
        <thead>
          <tr>
            <th>Condicion Especial</th>
            <th>Tipo de Practica</th>
            <th class="tac w160">Cantidad</th>
            <th class="tac w160">Meses</th>
            <% if (permiso > 1) { %>
              <th class="w20"></th>
            <% } %>
          </tr>
        </thead>
        <tbody></tbody>
        <tfoot class="pagination_container hide-if-no-paging"></tfoot>
      </table>
    </div>
  </div>
</script>


<script type="text/template" id="sindi_limites_condiciones_especiales_item">
  <td class="ver"><span class='text-info'><%= condicionespecial %></span></td>
  <td class="ver"><span class='text-info'><%= tipodepractica %></span></td>
  <td class="ver tac"><span class='text-info'><%= cantidad %></span></td>
  <td class="ver tac"><span class='text-info'><%= meses %></span></td>
  <% if (permiso > 1) { %>
    <td class="p5 td_acciones">
      <a href="javascript:void(0)" class="delete tac" data-id="<%= id %>"><i class="fa fa-trash text-danger" aria-hidden="true"></i></a>
    </td>
  <% } %>
</script>

<script type="text/template" id="sindi_limites_condiciones_especiales_edit_panel_template">
<form onsubmit="return false" class="modal-content">
	<div class="modal-header">
		<b>Limite de Condicion Especial</b>
	</div>
  <div class="modal-body">
    <div class="row">
      <div class="padder">
      	<div class="form-group">
          <label class="control-label">Condicion Especial</label>
          <select class="form-control" name="id_condicion_especial" id="sindi_limite_condiciones_especiales"></select>
        </div>
        <div class="form-group">
          <label class="control-label">Tipo de Practica</label>
          <select class="form-control" name="id_tipo_practica" id="sindi_limite_condiciones_tipos_practicas"></select>
        </div>
        <div class="row">
        	<div class="col-md-6">
            <div class="form-group">
              <label class="control-label">Cantidad</label>
              <input <%= (!edicion)?"disabled":"" %> type="text" name="cantidad" autocomplete="off" class="form-control" id="sindi_limites_condiciones_especiales_cantidad" value="<%= cantidad %>"/>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label">Meses</label>
              <input <%= (!edicion)?"disabled":"" %> type="text" name="meses" autocomplete="off" class="form-control" id="sindi_limites_condiciones_especiales_meses" value="<%= meses %>"/>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="modal-footer">
    <a class="btn cerrar btn-default fl">Cerrar</a>
    <% if (edicion) { %>
      <button class="btn guardar btn-success fr">Guardar</button>
    <% } %>
  </div>
</form>
</script>