<script type="text/template" id="pilotos_panel_template">
	<h2 class="titulo">Listado de Pilotos</h2>	

	<div class="search_container" class="mt5 mb10"></div>

	<div class="tabla_container">
		<table id="pilotos_table">
			<thead>
				<tr>
					<th>Nombre</th>
					<% if (permiso > 1) { %>
						<th class="w25"></th>
						<th class="w25"></th>
					<% } %>
				</tr>
			</thead>
			<tbody>
                <tr><td colspan="5" style="text-align:center"><img src="/resources/images/loading.gif"/></td></tr>
            </tbody>
		</table>
		<div class="pagination_container"></div>
	</div>
</script>


<script type="text/template" id="pilotos_item">
	<td><span class='ver'><%= nombre %></span></td>
	<% if (permiso > 1) { %>
		<td><img src="resources/images/edit.png" data-id="<%= id %>" class="edit" alt="Edit"/></td>
		<td><img src="resources/images/delete.png" data-id="<%= id %>" class="delete" alt="Delete"/></td>
	<% } %>
</script>

<script type="text/template" id="pilotos_edit_panel_template">

	<div class="titulo">
		<% if (id == undefined) { %>
			Nuevo Piloto
		<% } else { %>
			<%= nombre %>
		<% } %>	
	</div>

	<div class="row">
		<label>Nombre: </label>
		<% if (edicion) { %>
			<input type="text" name="nombre" class="input" id="pilotos_nombre" value="<%= nombre %>"/>
            <span class="icono_requerido">•</span>
		<% } else { %>
			<span><%= nombre %></span>
		<% } %>
	</div>
    	
	<% if (edicion) { %>
	<div class="row">
		<button class="button verde guardar">Guardar</button>
		<button class="button verde nuevo">Limpiar</button>
	</div>
	<% } %>

</script>