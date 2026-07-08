<script type="text/template" id="chequeras_panel_template">
    <h2 class="titulo">Chequeras</h2>
    
    <div class="mb10">
	<div class="row oh mb10">
	    <span class="fl fwn">Filtro por banco:&nbsp;&nbsp;&nbsp;</span>
	    <div class="select_container" id="chequeras_table_bancos_select_container"></div>
	    <button class="button azul fs12 fl ml10 buscar">Buscar</button>
	</div>
    </div>

    <div class="tabla_container">
	<table id="chequeras_table">
	    <thead>
		<tr>
		    <th>Banco</th>
		    <th>Serie</th>
		    <% if (permiso > 1) { %>
			<th class="col_chica"></th>
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


<script type="text/template" id="chequeras_item">
    <td><span class='ver'><%= banco %></span></td>
    <td><span class='ver'><%= numero %></span></td>
    <% if (permiso > 1) { %>
	<td><img src="resources/images/edit.png" class="edit" alt="Edit"/></td>
    <% } %>
</script>

<script type="text/template" id="chequeras_edit_panel_template">

    <div class="titulo">
	<% if (numero == "") { %>
		Nueva Chequera
	<% } else { %>
	    Modificar Chequera
	<% } %>	
    </div>
    
    <div class="row">
	<span class="fwn w48">Empresa: </span>
	<select class="input w150" id="chequeras_empresas_select" name="id_empresa">
	    <option value="1">Ernesto Reynaldo</option>
	    <option value="2">Ana Gracia Reynaldo</option>
	</select>
    </div>

    <div class="row">
	<span class="fwn w48">Serie: </span>
	<% if (edicion) { %>
	    <input type="text" name="numero" class="input w100" id="chequeras_numero" value="<%= numero %>"/>
	    <span class="icono_requerido">•</span>
	<% } else { %>
	    <span><%= numero %></span>
	<% } %>
    </div>
    
    <div class="row oh">
	<span class="fwn fl w42">Banco: </span>
	<div class="select_container fl ml10" id="chequeras_banco_select_container"></div>
    </div>
    
    <div class="row">
	<span class="fwn mr10" style="width:100px">Numero de Inicio: </span>
	<% if (edicion) { %>
	    <input type="text" name="numero_desde" class="input w100" id="chequeras_numero_desde" value="<%= numero_desde %>"/>
	    <span class="icono_requerido">•</span>
	<% } else { %>
	    <span><%= numero_desde %></span>
	<% } %>
    </div>
    
    <div class="row">
	<span class="fwn mr10" style="width:100px">Numero de Fin: </span>
	<% if (edicion) { %>
	    <input type="text" name="numero_hasta" class="input w100" id="chequeras_numero_hasta" value="<%= numero_hasta %>"/>
	    <span class="icono_requerido">•</span>
	<% } else { %>
	    <span><%= numero_hasta %></span>
	<% } %>
    </div>
    
    <% if (edicion) { %>
	<div class="row">
	    <button class="button verde guardar">Guardar</button>
	    <button class="button verde limpiar">Limpiar</button>
	</div>
    <% } %>

</script>