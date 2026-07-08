<script type="text/template" id="localidades_panel_template">
    
    <div class="bg-light lter b-b wrapper-md ng-scope">
      <h1 class="m-n font-thin h3">Listado de Localidades</h1>
    </div>    
    
    <div class="wrapper-md ng-scope">
        <div class="panel panel-default">
        
            <div class="panel-heading oh">
                <div class="search_container col-lg-4 col-md-6 col-sm-9 col-xs-12"></div>
                <a class="btn pull-right btn-success btn-addon" href="app/#localidad"><i class="fa fa-plus"></i>Nuevo</a>
            </div>
            <div class="panel-body">
                <div class="b-a table-responsive">
                    <table id="localidades_table" class="m-b-none table table-striped sortable default footable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                								<th>Partido/Departamento</th>
                								<th>Provincia</th>
                								<th>Pais</th>
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


<script type="text/template" id="localidades_item">
  <td><span class='ver'><%= id %></span></td>
	<td><span class='ver'><%= nombre %></span></td>
	<td><span class='ver'><%= departamento %></span></td>
	<td><span class='ver'><%= provincia %></span></td>
	<td><span class='ver'><%= pais %></span></td>
	<% if (permiso > 1) { %>
		<td><i class="fa fa-file-text-o edit text-dark" data-id="<%= id %>" /></td>
		<td><i class="glyphicon glyphicon-remove delete text-danger" data-id="<%= id %>" /></td>
	<% } %>
</script>

<script type="text/template" id="localidades_edit_panel_template">
    
<div class="bg-light lter b-b wrapper-md ng-scope">
  <h1 class="m-n font-thin h3">
    <% if (id == undefined) { %>
        Nuevo Localidad
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
                    <label class="col-lg-2 control-label">Nombre: </label>
                    <div class="col-lg-10">
                        <% if (edicion) { %>
                            <input type="text" name="nombre" class="form-control" id="localidades_nombre" value="<%= nombre %>"/>
                        <% } else { %>
                            <span><%= nombre %></span>
                        <% } %>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="col-lg-2 control-label">Codigo Postal: </label>
                    <div class="col-lg-10">
                        <% if (edicion) { %>
                            <input type="text" name="codigo_postal" class="form-control" id="localidades_codigo_postal" value="<%= codigo_postal %>"/>
                        <% } else { %>
                            <span><%= codigo_postal %></span>
                        <% } %>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="col-lg-2 control-label">Link: </label>
                    <div class="col-lg-10">
                        <% if (edicion) { %>
                            <input type="text" name="link" class="form-control" id="localidades_link" value="<%= link %>"/>
                        <% } else { %>
                            <span><%= link %></span>
                        <% } %>
                    </div>
                </div>
                
                <div class="line line-dashed b-b line-lg pull-in"></div>
            
                <% if (edicion) { %>
                    <div class="form-group">
                        <div class="col-lg-offset-2 col-lg-10">    
                            <button class="btn btn-success guardar">Guardar</button>
                        </div>
                    </div>
                <% } %>
            </div>
        </div>
    </div>
</div>
</script>
