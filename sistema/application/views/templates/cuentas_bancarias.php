<script type="text/template" id="cuentas_bancarias_panel_template">
<div class="bg-light lter b-b wrapper-md">
	<h1 class="m-n font-thin h3"><i class="fa fa-cog icono_principal"></i>Configuraci&oacute;n
		/ <b>Cuentas bancarias</b>
	</h1>
</div>
<div class="wrapper-md ng-scope">
	<div class="panel panel-default">
	
		<div class="panel-heading oh">
			<div class="row">
				<div class="col-md-6 col-lg-3 sm-m-b">
					<div class="search_container"></div>
				</div>
				<div class="col-md-6 col-lg-offset-3 col-lg-6 text-right">
					<a class="btn btn-info btn-addon" href="app/#cuenta_bancaria"><i class="fa fa-plus"></i>&nbsp;&nbsp;Nuevo&nbsp;&nbsp;</a>
				</div>
			</div>
		</div>
		<div class="panel-body">
			<div class="b-a table-responsive">
				<table id="cuentas_bancarias_table" class="table table-striped sortable m-b-none default footable">
					<thead>
						<tr>
							<th class="sorting" data-sort-by="nombre">Nombre / N&uacute;mero</th>
							<th class="sorting" data-sort-by="banco">Banco</th>
							<% if (permiso > 1) { %>
								<th class="w25"></th>
								<th class="w25"></th>
							<% } %>
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


<script type="text/template" id="cuentas_bancarias_item">
	<td><span class='ver'><%= nombre %></span></td>
	<td><span class='ver'><%= banco %></span></td>
	<% if (permiso > 1) { %>
		<td><i class="fa fa-file-text-o edit text-dark" data-id="<%= id %>" /></td>
		<td><i class="glyphicon glyphicon-remove delete text-danger" data-id="<%= id %>" /></td>
	<% } %>
</script>

<script type="text/template" id="cuentas_bancarias_edit_panel_template">
<div class="bg-light lter b-b wrapper-md">
	<h1 class="m-n font-thin h3"><i class="fa fa-cog icono_principal"></i>Configuraci&oacute;n
		/ Cuentas bancarias
		/ <b><%= (id == undefined) ? "Nueva" : nombre %></b>
	</h1>
</div>
<div class="wrapper-md ng-scope">
	<div class="panel panel-default">
	
		<div class="panel-heading">
			<span class="font-bold">Ingrese los datos</span>
		</div>
		<div class="panel-body">
		
			<div class="form-horizontal">

				<div class="form-group">
					<label class="col-lg-2 control-label">Nombre / N&uacute;mero</label>
					<div class="col-lg-10">
						<% if (edicion) { %>
							<input type="text" name="nombre" class="form-control" id="cuentas_bancarias_nombre" value="<%= nombre %>"/>
						<% } else { %>
							<span><%= nombre %></span>
						<% } %>
					</div>
				</div>
				
				<div class="form-group">
					<label class="col-lg-2 control-label">Banco</label>
					<div class="col-lg-10">
						<% if (edicion) { %>
							<select class="form-control" id="cuentas_bancarias_bancos">
							<option value="0">Seleccione</option>
							<% for(var i=0;i<bancos.length;i++) { %>
								<% var banco = bancos[i] %>
								<option <%= (banco.id == id_banco)?"selected":"" %> value="<%= banco.id %>"><%= banco.nombre %></option>
							<% } %>
							</select>
						<% } else { %>
							<span><%= banco %></span>
						<% } %>
					</div>
				</div>
				
				<div class="form-group">
					<label class="col-lg-2 control-label">Sucursal</label>
					<div class="col-lg-10">
						<% if (edicion) { %>
							<input type="text" name="sucursal" class="form-control" id="cuentas_bancarias_sucursal" value="<%= sucursal %>"/>
						<% } else { %>
							<span><%= sucursal %></span>
						<% } %>
					</div>
				</div>				
				
				<div class="form-group">
					<label class="col-lg-2 control-label">CBU</label>
					<div class="col-lg-10">
						<% if (edicion) { %>
							<input type="text" name="cbu" class="form-control" id="cuentas_bancarias_cbu" value="<%= cbu %>"/>
						<% } else { %>
							<span><%= cbu %></span>
						<% } %>
					</div>
				</div>				
				
				<div class="line line-dashed b-b line-lg pull-in"></div>
				<% if (edicion) { %>
					<div class="form-group">
						<div class="col-lg-offset-2 col-lg-10">
							<button class="btn guardar btn-success">Guardar</button>
						</div>
					</div>
				<% } %>
			</div>
		</div>
	</div>
</div>

</script>