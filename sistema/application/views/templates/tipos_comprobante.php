<script type="text/template" id="tipos_comprobante_panel_template">
    
    <div class="bg-light lter b-b wrapper-md ng-scope">
      <h1 class="m-n font-thin h3">Listado de Tipos de Comprobantes</h1>
    </div>

	<div class="search_container" class="mt5 mb10"></div>

	<div class="tabla_container">
		<table id="tipos_comprobante_table">
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
                <tr>
                    <td colspan="5" style="text-align:center"><img src="/resources/images/loading.gif"/></td>
                </tr>            
            </tbody>
		</table>
		<div class="pagination_container"></div>
	</div>
</script>


<script type="text/template" id="tipos_comprobante_item">
	<td><span class='ver'><%= nombre %></span></td>
	<% if (permiso > 1) { %>
	<td>
		<img src="resources/images/edit.png" class="edit" alt="Edit"/>
	</td>
	<td>
		<img src="resources/images/delete.png" class="delete" alt="Edit"/>
	</td>
	<% } %>
</script>

<script type="text/template" id="tipos_comprobante_edit_panel_template">

	<div class="titulo">
		<% if (nombre == "") { %>
			Nuevo Tipo de Comprobante
		<% } else { %>
			<%= nombre %>
		<% } %>	
	</div>

	<div class="row">
		<label>Nombre: </label>
		<% if (edicion) { %>
			<input type="text" name="nombre" class="input" id="tipos_comprobante_nombre" value="<%= nombre %>"/>
		        <span class="icono_requerido">•</span>
		<% } else { %>
			<span><%= nombre %></span>
		<% } %>
	</div>

	<div class="row">
		<label>Operación: </label>
		<% if (edicion) { %>
			<div class="select_container select100" id="tipos_comprobante_operacion">
				<select class="select" name="operacion">
					<option <%= (operacion == "") ? "selected" : "" %> value="">Ninguna</option>
					<option <%= (operacion == "+") ? "selected" : "" %> value="+">Suma al IVA</option>
					<option <%= (operacion == "-") ? "selected" : "" %> value="-">Resta al IVA</option>
				</select>
			</div>
		        <span class="icono_requerido">•</span>
		<% } else { %>
			<span><%= operacion %></span>
		<% } %>
	</div>

	<% if (edicion) { %>
	<div class="row">
		<button class="button verde guardar">Guardar</button>
		<button class="button verde limpiar">Limpiar</button>
	</div>
	<% } %>

</script>