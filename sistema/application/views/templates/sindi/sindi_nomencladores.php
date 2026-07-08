<script type="text/template" id="sindi_practicas_template">
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <h1 class="m-n font-thin h3"><i class="fa fa-user-md icono_principal"></i>Auditor / <b>Nomenclador</b>
    </h1>
  </div>
	<div class="wrapper-md ng-scope">
		<div class="">
			<div class="row">
				<div class="col-md-4">
					<div class="panel panel-default">
						<div class="panel-heading oh">
							<b class="h4">Consulta</b>
						</div>
						<div class="panel-body">
							<div class="input-group">
								<span class="input-group-addon">Valor Consulta</span>
					      <input type="number" min="0" step="1" name="importeconsulta" class="form-control no-spinner tac" id="sindi_nomencladores_importe_consulta"/>
					      <span class="input-group-btn">
                  <a style="width:112px"class="btn btn-info btn-addon guardarconsulta" href="javascript:void(0)"><i class="fa fa-plus"></i>&nbsp;&nbsp;Guardar&nbsp;&nbsp;</a>
                </span>
							</div>
						</div>
					</div>
					<div id="lista_tipos"></div>
				</div>

				<div class="col-md-8">
					<div id="lista_nomenclador"></div>
				</div>
			</div>

		</div>
	</div>
</script>

<script type="text/template" id="sindi_nomencladores_panel_template">
<div class="panel panel-default">
	<div class="panel-heading oh">
		<b class="h4">Prácticas</b>
	</div>
	<div class="panel-body">
		<div class="row form-group">
			<div class="col-md-6 sm-m-b">
				<div class="search_container"></div>
			</div>
      <% if (control.check("sindi_nomencladores") > 1) { %>
				<div class="col-md-6 text-right">
					<a class="btn btn-info btn-addon nuevo" href="javascript:void(0)"><i class="fa fa-plus"></i>&nbsp;&nbsp;Nuevo&nbsp;&nbsp;</a>
				</div>
      <% } %>
		</div>
		<div class="b-a table-responsive">
			<table id="sindi_nomencladores_table" class="table table-small table-striped sortable m-b-none default footable">
				<thead>
					<tr>
						<th style="width:20px;">
							<label class="i-checks m-b-none">
								<input class="esc sel_todos" type="checkbox"><i></i>
							</label>
						</th>
						<th class="sorting" data-sort-by="codigo">Codigo</th>
						<th class="sorting" data-sort-by="nombre">Nombre</th>
						<th class="sorting" data-sort-by="importe">Valor</th>
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


<script type="text/template" id="sindi_nomencladores_item">
	<td>
		<label class="i-checks m-b-none">
			<input class="esc check-row" value="<%= id %>" type="checkbox"><i></i>
		</label>
	</td>
	<td class="ver"><%= codigo %></td>
	<td class="ver"><span class='text-info'><%= nombre %></span></td>
	<td class="ver"><%= importe %></td>
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

<script type="text/template" id="sindi_nomencladores_edit_panel_template">
<form onsubmit="return false" class='modal-content'>
  <div class='modal-header'>
		<b><%= (id == undefined) ? 'Nuevo Nomenclador' : nombre %></b>
	</div>
	<div class="modal-body">
		<div class="">

			<div class="form-group">
				<label class="control-label">Codigo</label>
				<input <%= (!edicion)?"disabled":"" %> type="number" min="1" step="1" name="codigo" class="form-control no-spinner" id="sindi_nomencladores_codigo" value="<%= codigo %>"/>
			</div>

			<div class="form-group">
				<label class="control-label">Nombre</label>
				<input <%= (!edicion)?"disabled":"" %> type="text" required minlength="3" name="nombre" class="form-control" id="sindi_nomencladores_nombre" value="<%= nombre %>"/>
			</div>

			<div class="form-group">
				<label class="control-label">Valor</label>
				<input <%= (!edicion)?"disabled":"" %> type="number" min="0" step="1" name="importe" class="form-control no-spinner" id="sindi_nomencladores_importe" value="<%= importe %>"/>
			</div>

			<div class="form-group">
				<label class="control-label">Tipo</label>
				<select id="sindi_nomencladores_tipos" class="form-control" name="id_tipo_practica"></select>
			</div>

		</div>

	</div>
	<div class="modal-footer oh">
    <a class="btn btn-default pull-left cerrar">Cerrar</a>
		<button class="btn guardar btn-success float-right">Guardar</button>
	</div>
</form>
</script>