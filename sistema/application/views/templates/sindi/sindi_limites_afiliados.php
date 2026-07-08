<script type="text/template" id="sindi_limites_afiliados_panel_template">
	<div class="panel-body oh pb0">
		<div class="row">
			<div class="col-md-6 col-lg-3 sm-m-b">
				<div class="search_container"></div>
			</div>
		</div>
	</div>
	<div class="panel-body">
		<div class="b-a table-responsive">
			<table id="sindi_limites_afiliados_table" class="table table-small table-striped sortable m-b-none default footable">
				<thead>
					<tr>
						<th class="sorting" data-sort-by="id_afiliado">Afiliado</th>
						<% if (tipo == 5) { %>
							<th class="sorting" data-sort-by="id_tipo_practica">Tipo Practica</th>
						<% } %>
						<th class="tac w60">Cantidad</th>
						<% if (tipo == 5) { %>
							<th class="tac w60">Meses</th>
						<% } %>
						<th class="sorting tac w130" data-sort-by="vencimiento">Vencimiento</th>
						<th class="">Motivo</th>
					</tr>
				</thead>
				<tbody></tbody>
				<tfoot class="pagination_container hide-if-no-paging"></tfoot>
			</table>
		</div>
	</div>
</script>

<script type="text/template" id="sindi_limites_afiliados_item">
	<td class="ver"><span class='text-info'><%= nombreafiliado %></span></td>
	<% if (tipo == 5) { %>
		<td class="ver"><span class='text-info'><%= nombrepractica %></span></td>
	<% } %>
	<td class="ver tac"><span class='text-info'><%= cantidad %></span></td>
	<% if (tipo == 5) { %>
		<td class="ver tac"><span class='text-info'><%= meses %></span></td>
	<% } %>
	<td class="ver tac"><span class='text-info'><%= (vencimiento=="0000-00-00")?"Sin Vencimiento":vencimiento %></span></td>
	<td class="ver"><span class='text-info'><%= motivo %></span></td>
</script>

<script type="text/template" id="sindi_limites_afiliados_edit_panel_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
	<h1 class="m-n font-thin h3"><i class="fa fa-tags icono_principal"></i>

		/ <b><%= (id == undefined) ? 'Nueva' : cantidad %></b>
	</h1>
</div>
<form onsubmit="return false" class="wrapper-md ng-scope">
	<div class="centrado rform">
		<div class="row">
			<div class="col-md-10 col-md-offset-1">
				<div class="panel panel-default">
					<div class="panel-body">
						<div class="padder">
							<div class="row">
								<div class="col-md-3">
									<div class="form-group">
										<label class="control-label">id_afiliado</label>
										<input <%= (!edicion)?"disabled":"" %> type="text" name="id_afiliado" autocomplete="off" class="form-control" id="sindi_limites_afiliados_id_afiliado" value="<%= id_afiliado %>"/>
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label class="control-label">cantidad</label>
										<input <%= (!edicion)?"disabled":"" %> type="number" name="cantidad" autocomplete="off" class="form-control no-spinner" id="sindi_limites_afiliados_cantidad" value="<%= cantidad %>"/>
									</div>
								</div>
						</div>
						</div>
					</div>
				</div>
				<% if (edicion) { %>
					<button class="btn guardar btn-success">Guardar</button>
				<% } %>
			</div>
		</div>
	</div>
</form>

</script>