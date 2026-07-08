<script type="text/template" id="versiones_db_panel_template">
    
    <div class="bg-light lter b-b wrapper-md ng-scope">
      <h1 class="m-n font-thin h3">Actualizaciones de Base de Datos</h1>
    </div>
    
    <div class="wrapper-md ng-scope">
        <div class="panel panel-default">
        
            <div class="panel-heading oh">
			  <div class="search_container col-lg-4 col-md-6 col-sm-9 col-xs-12"></div>
			  <button class="btn pull-right btn-sm btn-default m-l exportar_sql">Exportar SQL</button>
              <a class="btn pull-right btn-success btn-addon" href="app/#version_db"><i class="fa fa-plus"></i>Nuevo</a>
            </div>
            <div class="panel-body">
                <div class="b-a table-responsive">
                    <table id="versiones_db_table" class="table table-striped sortable m-b-none default footable">
                        <thead>
                            <tr>
								<th class="w25">Subido</th>
                                <th>Numero</th>
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


<script type="text/template" id="versiones_db_item">
	<td class="p5 tac"><i title="Subido" class="glyphicon glyphicon-ok subido <%= (subido == 1)?"text-success":"text-muted" %>"></i></td>
	<td><span class='ver'><%= id %></span></td>
	<% if (permiso > 1) { %>
		<td><i class="fa fa-file-text-o edit text-dark" data-id="<%= id %>" /></td>
	<% } %>
</script>

<script type="text/template" id="versiones_db_edit_panel_template">

<div class="bg-light lter b-b wrapper-md ng-scope">
  <h1 class="m-n font-thin h3">
    <% if (id == undefined) { %>
        Nueva Actualizacion de Base de datos
    <% } else { %>
        Version: <%= id %>
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
				  <div class="col-xs-12">
					<textarea placeholder="Pegue aqui el SQL" style="height: 400px" class="form-control" name="texto"><%= texto %></textarea>
				  </div>
                </div>
                
                <div class="line line-dashed b-b line-lg pull-in"></div>
                <% if (edicion) { %>
                    <div class="form-group">
                        <div class="col-xs-12">
                            <button class="btn guardar btn-success">Guardar</button>
                        </div>
                    </div>
                <% } %>
            </div>
        </div>
    </div>
</div>

</script>