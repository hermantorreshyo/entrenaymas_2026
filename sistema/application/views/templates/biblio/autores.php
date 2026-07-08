<script type="text/template" id="autores_panel_template">
    
    <div class="bg-light lter b-b wrapper-md ng-scope">
      <h1 class="m-n font-thin h3">Listado de Autores</h1>
    </div>
    <div class="wrapper-md ng-scope">
        <div class="panel panel-default">
        
            <div class="panel-heading oh">
                <div class="search_container col-lg-4 col-md-6 col-sm-9 col-xs-12"></div>
                <a class="btn pull-right btn-success btn-addon" href="app/#autor"><i class="fa fa-plus"></i>Nuevo</a>
            </div>
            <div class="panel-body">
                <div class="b-a table-responsive">
                    <table id="autores_table" class="table table-striped sortable m-b-none default footable">
                        <thead>
                            <tr>
                                <th class="sorting" data-sort-by="nombre">Nombre</th>
                                <% if (permiso > 1) { %>
                                    <th class="w100"></th>
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


<script type="text/template" id="autores_item">
	<td><span class='ver'><%= nombre %></span></td>
	<% if (permiso > 1) { %>
		<td><button class="btn buscar_libros btn-default btn-sm">Ver libros</button></td>
		<td><i class="glyphicon glyphicon-remove delete text-danger" data-id="<%= id %>" /></td>
	<% } %>
</script>

<script type="text/template" id="autores_edit_panel_template">

<div class="bg-light lter b-b wrapper-md ng-scope">
  <h1 class="m-n font-thin h3">
    <% if (id == undefined) { %>
        Nuevo Autor
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
                            <input type="text" name="nombre" class="form-control" id="autores_nombre" value="<%= nombre %>"/>
                        <% } else { %>
                            <span><%= nombre %></span>
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



<script type="text/template" id="autores_edit_mini_panel_template">
  <div class="panel pb0 mb0">
	<div class="panel-body">
	  <div class="form-group">
		<input type="text" autocomplete="off" placeholder="Nombre" name="nombre" class="tab form-control" id="autores_mini_nombre"/>
	  </div>
	  <div class="form-group">
		<button class="btn guardar btn-success tab btn-block">Guardar</button>
	  </div>
	</div>
  </div>
</script>