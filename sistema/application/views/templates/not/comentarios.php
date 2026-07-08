<script type="text/template" id="comentarios_resultados_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <h1 class="m-n font-thin h3"><i class="fa fa-file-text icono_principal"></i>Entradas
	/ <b>Comentarios</b>
  </h1>
</div>
<div class="wrapper-md ng-scope">
	<div class="panel panel-default">
		<div class="panel-heading clearfix">
		  <div class="row">
			<div class="col-md-6 col-lg-3 sm-m-b">
			  <div class="input-group">
				  <input type="text" id="comentarios_buscar" placeholder="Buscar..." value="<%= filter %>" autocomplete="off" class="form-control">
				  <span class="input-group-btn">
					<button class="btn btn-default"><i class="fa fa-search"></i></button>
				  </span>
				  <span class="input-group-btn">
					<button class="btn btn-default advanced-search-btn"><i class="fa fa-angle-double-down"></i></button>
				  </span>
			  </div>
			</div>
			<% if (!seleccionar) { %>
			  <div class="col-md-6 col-lg-offset-3 col-lg-6 text-right">
				<div class="btn-group dropdown ml5">
				  <button class="btn btn-default dropdown-toggle" data-toggle="dropdown">
					<span>Acciones</span>
					<span class="caret"></span>
				  </button>
				  <ul class="dropdown-menu pull-right">
					<li><a href="javascript:void" class="eliminar_lote">Eliminar</a></li>
				  </ul>
				</div>				  
			  </div>
			<% } %>
		  </div>
		</div>
		<div class="advanced-search-div bg-light dk" style="display:none">
		  <div class="wrapper oh">
			<h4 class="m-t-xs"><i class="fa fa-search"></i> B&uacute;squeda Avanzada:</h4>
			<div class="form-inline">
			  <div style="width: 250px; display: inline-block">
				  <select id="comentarios_buscar_usuarios" class="w100p"></select>
			  </div>
			  <div class="form-group">
				<button id="comentarios_buscar_avanzada_btn" class="btn btn-default"><i class="fa fa-search"></i> Buscar</button>
			  </div>
			</div>
		  </div>
		</div>
	  
		<div class="panel-body">
			<div class="b-a table-responsive">
			<table id="comentarios_tabla" class="table table-small table-striped sortable m-b-none default footable">
				<thead>
				  <tr>
					<% if (!seleccionar) { %>
					  <th style="width:20px;">
						  <label class="i-checks m-b-none">
							  <input class="esc sel_todos" type="checkbox"><i></i>
						  </label>
					  </th>					  
					<% } else { %>
					  <th style="width:20px;"></th>
					<% } %>
					<th class="w50 tac">Imagen</th>
					<th class="sorting" data-sort-by="usuario">Usuario</th>
					<th>Entrada</th>
					<th class="sorting" data-sort-by="A.fecha">Fecha</th>
					<th>Comentario</th>
					<% if (!seleccionar) { %>
					  <th style="width:150px;text-align:right">Acciones</th>
					<% } %>
				  </tr>
				</thead>
				<tbody class="tbody"><tbody>
				<tfoot class="pagination_container hide-if-no-paging"></tfoot>
			  </table>
			</div>
		</div>
	</div>
</div>
</script>

<script type="text/template" id="comentarios_item_resultados_template">
	<% var clase = (estado==1)?"":"text-muted"; %>
	<% if (seleccionar) { %>
	  <td>
		  <label class="i-checks m-b-none">
			  <input class="radio esc" value="<%= codigo %>" name="radio" type="radio"><i></i>
		  </label>
	  </td>
	<% } else { %>
	  <td>
		  <label class="i-checks m-b-none">
			  <input class="esc check-row" value="<%= id %>" type="checkbox"><i></i>
		  </label>
	  </td>	
	<% } %>
	<td class="<%= clase %> data">
	  <% if (!isEmpty(path)) { %>
		<a href=""><img src="<%= show_path(path) %>" class="customcomplete-sm customcomplete-image-xs"/></a>
	  <% } %>
	</td>
	<td class="<%= clase %> data"><a class="<%= (estado==1)?"text-info":clase %> link" href="app/#web_user/<%= id_usuario %>"><%= usuario %></a>
		<% if (!isEmpty(email)) { %><br/><%= email %><% } %>
	</td>
	<td class="<%= clase %> data"><a class="<%= (estado==1)?"text-info":clase %> link" href="app/#entrada/<%= id_entrada %>"><%= entrada %></a></td>
	<td class="<%= clase %> data"><%= fecha %></td>
	<td class="<%= clase %> data"><%= (texto.length > 20) ? texto.substr(0,20)+"..." : texto %></td>
	<% if (!seleccionar) { %>
	  <td class="tar <%= clase %>">
		<i title="Activo" class="fa-check iconito fa activo <%= (estado == 1)?"active":"" %>"></i>
		<i title="Destacado" class="fa fa-star iconito destacado <%= (destacado == 1)?"active":"" %>"></i>
		<div class="btn-group dropdown">
		  <i title="Opciones" class="iconito fa fa-caret-down dropdown-toggle" data-toggle="dropdown"></i>
		  <ul class="dropdown-menu pull-right">
			<li><a href="javascript:void(0)" class="eliminar" data-id="<%= id %>">Eliminar</a></li>
		  </ul>
		</div>
	  </td>
	<% } %>
</script>


<script type="text/template" id="comentario_template">
<div class="panel panel-default mb0">
  <div class="panel-heading font-bold">
	Editar Comentario
	<i class="pull-right cerrar_lightbox glyphicon glyphicon-remove cp"></i>
  </div>
  <div class="panel-body">
	<div class="form-horizontal">
	  <div class="form-group">
		<div class="col-xs-12">
		  <textarea class="form-control h120" id="comentario_texto" name="texto"><%= texto %></textarea>
		</div>
	  </div>
	</div>
  </div>
  <div class="panel-footer clearfix">
	<button class="cerrar_lightbox btn btn-default">Cerrar</button>
	<button class="btn guardar pull-right btn-success">
	  Guardar
	</button>
  </div>
</div>
</script>
