<script type="text/template" id="sindi_condiciones_especiales_panel_template">
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <h1 class="m-n font-thin h3"><i class="fa fa-user-md icono_principal"></i>Auditor / <b>Condiciones Especiales</b>
    </h1>
  </div>
	<div class="wrapper-md ng-scope">
		<div class="panel panel-default">

			<div class="panel-heading oh">
				<div class="row">
					<div class="col-md-6 col-lg-3 sm-m-b">
						<div class="search_container"></div>
					</div>
          <% if (control.check("sindi_condiciones_especiales") > 1) { %>
  					<div class="col-md-6 col-lg-offset-3 col-lg-6 text-right">
  						<a class="btn btn-info btn-addon" href="app/#sindi_condicion_especial"><i class="fa fa-plus"></i>&nbsp;&nbsp;Nuevo&nbsp;&nbsp;</a>
  					</div>
          <% } %>
				</div>
			</div>
			<div class="panel-body">
				<div class="b-a table-responsive">
					<table id="sindi_condiciones_especiales_table" class="table table-striped sortable m-b-none default footable">
						<thead>
							<tr>
								<th class="sorting" data-sort-by="nombre">Nombre</th>
								<th>Vencimiento</th>
							</tr>
						</thead>
						<tbody></tbody>
						<tfoot class="pagination_container hide-if-no-paging"></tfoot>
					</table>
				</div>
			</div>
		</div>
	</div>
</script>

<script type="text/template" id="sindi_condiciones_especiales_item">
	<td class="ver"><span class='text-info'><%= nombre %></span></td>
	<td class="ver"><span class='text-info'><%= vencimiento %></span></td>
</script>

<script type="text/template" id="sindi_condiciones_especiales_edit_panel_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
	<h1 class="m-n font-thin h3"><i class="fa fa-tags icono_principal"></i>Condición Especial

		/ <b><%= (id == undefined) ? 'Nueva' : nombre %></b>
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
								<div class="col-md-9">
									<div class="form-group">
										<label class="control-label">Nombre</label>
										<input <%= (!edicion)?"disabled":"" %> type="text" name="nombre" autocomplete="off" class="form-control" id="sindi_condiciones_especiales_nombre" value="<%= nombre %>"/>
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<label class="control-label">Vencimiento (Meses)</label>
										<input <%= (!edicion)?"disabled":"" %> type="number" name="vencimiento" autocomplete="off" class="form-control tac no-spinner" id="sindi_condiciones_especiales_vencimiento" value="<%= vencimiento %>"/>
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