<script type="text/template" id="entradas_etiquetas_panel_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
	<h1 class="m-n font-thin h3"><i class="fa fa-file-text icono_principal"></i>Entradas
		/ <b>Etiquetas</b>
	</h1>
</div>
<div class="wrapper-md ng-scope">
	<div class="panel panel-default">
	
		<div class="panel-heading clearfix">
			<div class="row">
				<div class="search_container col-md-6 col-lg-3 sm-m-b"></div>
        <div class="col-md-6 col-lg-9">
				  <a class="btn pull-right btn-info btn-addon" href="app/#entrada_etiqueta"><i class="fa fa-plus"></i>&nbsp;&nbsp;Nueva&nbsp;&nbsp;</a>
        </div>
			</div>
		</div>
		<div class="panel-body">
			<div class="b-a table-responsive">
				<table id="entradas_etiquetas_table" class="table table-striped sortable m-b-none default footable">
					<thead>
						<tr>
							<th class="sorting" data-sort-by="nombre">Nombre</th>
							<% if (permiso > 1) { %>
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


<script type="text/template" id="entradas_etiquetas_item">
	<td><span class='ver text-info'><%= nombre %></span></td>
	<td>
		<% if (permiso > 1) { %>
		<div class="btn-group dropdown">
			<i title="Opciones" class="iconito fa fa-caret-down dropdown-toggle" data-toggle="dropdown"></i>
			<ul class="dropdown-menu pull-right">
				<li><a href="javascript:void(0)" class="delete" data-id="<%= id %>">Eliminar</a></li>
			</ul>
		</div>
		<% } %>
	</td>
</script>

<script type="text/template" id="entradas_etiquetas_edit_panel_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
	<h1 class="m-n font-thin h3"><i class="fa fa-file-text icono_principal"></i>Entradas
		/ Etiquetas
		/ <b><%= (id == undefined) ? "Nueva" : nombre	%></b>
	</h1>
</div>
<div class="wrapper-md">
	<div class="centrado rform">
		<div class="row">

			<div class="col-md-4">
				<div class="detalle_texto">
					Ingrese los datos
				</div>
			</div>
			<div class="col-md-8">
				<div class="panel panel-default">
					<div class="panel-body">
						<div class="padder">
							<div class="form-group">
								<label class="control-label">Nombre</label>
								<% if (edicion) { %>
									<input type="text" name="nombre" class="form-control" id="entradas_etiquetas_nombre" value="<%= nombre %>"/>
								<% } else { %>
									<span><%= nombre %></span>
								<% } %>
							</div>		
						</div>
					</div>
				</div>
			</div>
		</div>

		<% if (edicion) { %>
			<div class="line b-b m-b-lg"></div>
			<div class="row">
				<div class="col-md-4"></div>
				<div class="col-md-8 tar">
					<button class="btn guardar btn-success">Guardar</button>
				</div>
			</div>
		<% } %>
	</div>
</div>
</script>