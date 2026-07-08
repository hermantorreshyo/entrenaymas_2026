<script type="text/template" id="publicidades_categorias_panel_template">
  <div class="bg-light lter b-b wrapper-md ng-scope">
	<h1 class="m-n font-thin h3">Categorias de Publicidades</h1>
  </div>
  <div class="wrapper-md ng-scope">
	<div class="panel panel-default">
	  
	  <div class="panel-heading oh">
		  <div class="search_container col-lg-4 col-md-6 col-sm-9 col-xs-12"></div>
		  <a class="btn pull-right btn-success btn-addon" href="app/#publicidad_categoria"><i class="fa fa-plus"></i>Nuevo</a>
	  </div>
	  <div class="panel-body">
		  <div class="b-a table-responsive">
			  <table id="publicidades_categorias_table" class="table table-striped sortable m-b-none default footable">
				  <thead>
					  <tr>
						  <th class="sorting" data-sort-by="nombre">Nombre</th>
						  <th class="sorting" data-sort-by="precio">Precio</th>
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


<script type="text/template" id="publicidades_categorias_item">
	<td><span class='ver'><%= nombre %></span></td>
	<td><span class='ver'><%= precio %></span></td>
	<% if (permiso > 1) { %>
		<td><i class="fa fa-file-text-o edit text-dark" data-id="<%= id %>" /></td>
		<td><i class="glyphicon glyphicon-remove delete text-danger" data-id="<%= id %>" /></td>
	<% } %>
</script>

<script type="text/template" id="publicidades_categorias_edit_panel_template">

<div class="bg-light lter b-b wrapper-md ng-scope">
  <h1 class="m-n font-thin h3">
    <% if (id == undefined) { %>
        Nueva Categoria de Publicidad
    <% } else { %>
        <%= nombre %>
    <% } %>	      
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
                    <label class="col-lg-2 control-label">Nombre</label>
                    <div class="col-lg-10">
                        <% if (edicion) { %>
                            <input type="text" name="nombre" class="form-control" id="publicidades_categorias_nombre" value="<%= nombre %>"/>
                        <% } else { %>
                            <span><%= nombre %></span>
                        <% } %>
                    </div>
                </div>
				
				<div class="form-group">
					<label class="col-md-2 control-label">Tipo</label>
					<div class="col-md-10">
						<select id="publicidades_categorias_tipos" name="id_tipo" class="form-control">
						  <% for(var i=0;i<window.tipos_publicidades.length;i++) { %>
							<% var o = tipos_publicidades[i]; %>
							<option value="<%= o.id %>" <%= (o.id == id_tipo)?"selected":"" %>><%= o.nombre %></option>
						  <% } %>
						</select>
					</div>
				</div>				
				
                <div class="form-group">
                    <label class="col-lg-2 control-label">Precio</label>
                    <div class="col-lg-10">
                        <% if (edicion) { %>
                            <input type="text" name="precio" class="form-control" id="publicidades_categorias_precio" value="<%= precio %>"/>
                        <% } else { %>
                            <span><%= precio %></span>
                        <% } %>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-2 control-label">Ancho</label>
                    <div class="col-lg-10">
                        <% if (edicion) { %>
                            <input type="text" name="ancho" class="form-control" id="publicidades_categorias_ancho" value="<%= ancho %>"/>
                        <% } else { %>
                            <span><%= ancho %></span>
                        <% } %>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-2 control-label">Alto</label>
                    <div class="col-lg-10">
                        <% if (edicion) { %>
                            <input type="text" name="alto" class="form-control" id="publicidades_categorias_alto" value="<%= alto %>"/>
                        <% } else { %>
                            <span><%= alto %></span>
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