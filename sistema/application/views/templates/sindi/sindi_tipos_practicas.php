<script type="text/template" id="sindi_tipos_practicas_panel_template">
<div class="panel panel-default">
	<div class="panel-heading oh">
		<b class="h4">Tipos de Prácticas</b>
	</div>
	<div class="panel-body">
		<div class="row form-group">
			<div class="col-md-6 sm-m-b">
				<div class="search_container"></div>
			</div>
      <% if (control.check("sindi_tipos_practicas") > 1) { %>
				<div class="col-md-6 text-right">
					<a class="btn btn-info btn-addon nuevo" href="javascript:void(0)"><i class="fa fa-plus"></i>&nbsp;&nbsp;Nuevo&nbsp;&nbsp;</a>
				</div>
      <% } %>
		</div>
		<div class="b-a table-responsive">
			<table id="sindi_tipos_practicas_table" class="table table-small table-striped sortable m-b-none default footable">
				<thead>
					<tr>
						<th class="sorting" data-sort-by="nombre">Nombre</th>
						<th class="sorting" data-sort-by="precio">Valor</th>
						<th class="w30"></th>
					</tr>
				</thead>
				<tbody></tbody>
				<tfoot class="pagination_container hide-if-no-paging"></tfoot>
			</table>
		</div>
	</div>
</div>
</script>


<script type="text/template" id="sindi_tipos_practicas_item">
	<td class="ver"><span class='text-info'><%= nombre %></span></td>
	<td class="ver"><%= precio %></td>
	<td class="p5 td_acciones">
		<div class="btn-group dropdown ml10">
			<button class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				<i class="fa fa-plus"></i>
			</button>		
			<ul class="dropdown-menu pull-right">
				<li><a href="javascript:void(0)" class="editar" data-id="<%= id %>">Editar</a></li>
				<li><a href="javascript:void(0)" class="delete" data-id="<%= id %>">Eliminar</a></li>
			</ul>
		</div>
	</td>
</script>

<script type="text/template" id="sindi_tipos_practicas_edit_panel_template">
<form onsubmit="return false" class='modal-content'>
  <div class='modal-header'>
		<b><%= (id == undefined) ? 'Nuevo Tipo de Practica' : nombre %></b>
	</div>
	<div class="modal-body">
		<div class="">

			<div class="form-group">
				<label class="control-label">Nombre</label>
				<input <%= (!edicion)?"disabled":"" %> type="text" required minlength="3" name="nombre" class="form-control" id="sindi_tipos_practicas_nombre" value="<%= nombre %>"/>
			</div>
			<div class="form-group">
				<label class="control-label">Precio</label>
				<input <%= (!edicion)?"disabled":"" %> type="number" required name="precio" class="form-control no-spinner" min="0" step="1" id="sindi_tipos_practicas_precio" value="<%= precio %>"/>
			</div>

		</div>

	</div>
	<div class="modal-footer oh">
    <a class="btn btn-default pull-left cerrar">Cerrar</a> 
		<button class="btn guardar btn-success float-right">Guardar</button>
	</div>
</form>
</script>