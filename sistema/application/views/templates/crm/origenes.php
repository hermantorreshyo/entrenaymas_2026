<script type="text/template" id="origenes_panel_template">
    <div class="bg-light lter b-b wrapper-md ng-scope">
      <h1 class="m-n font-thin h3">Origenes</h1>
    </div>
    
    <div class="wrapper-md ng-scope">
        <div class="panel panel-default">
        
            <div class="panel-heading oh">
			  <div class="search_container col-lg-4 col-md-6 col-sm-9 col-xs-12"></div>
			  <a class="btn pull-right btn-success btn-addon" href="app/#origen"><i class="fa fa-plus"></i>Nuevo</a>
            </div> 
            <div class="panel-body">
                <div class="b-a table-responsive">
                    <table id="origenes_table" data-ordenable-table="origenes" class="ordenable table table-striped sortable m-b-none default footable">
                        <thead>
                            <tr>
                                <th>Nombre</th>
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


<script type="text/template" id="origenes_item">
	<td><span class='ver'><%= nombre %></span></td>
	<% if (permiso > 1) { %>
		<td><i class="fa fa-file-text-o edit text-dark" data-id="<%= id %>" /></td>
		<td><i class="glyphicon glyphicon-remove delete text-danger" data-id="<%= id %>" /></td>
	<% } %>
</script>

<script type="text/template" id="origenes_edit_panel_template">

<div class="bg-light lter b-b wrapper-md ng-scope">
  <h1 class="m-n font-thin h3">
    <% if (id == undefined) { %>
        Nuevo Origen
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
                            <input type="text" name="nombre" class="form-control" id="origenes_nombre" value="<%= nombre %>"/>
                        <% } else { %>
                            <span><%= nombre %></span>
                        <% } %>
                    </div>
                </div>
								
                <div class="form-group">
                    <label class="col-lg-2 control-label">Color</label>
                    <div class="col-lg-10">
                        <% if (edicion) { %>
                            <input type="text" name="color" class="form-control" id="origenes_color" value="<%= color %>"/>
                        <% } else { %>
                            <span><%= color %></span>
                        <% } %>
                    </div>
                </div>
				
				<div class="form-group cb">
					<label class="col-md-2 control-label">Pymvar </label>
					<div class="col-md-10">
					  <label class="i-switch i-switch-md bg-info m-t-xs m-r">
						<input type="checkbox" name="proyecto_1" class="checkbox" value="1" <%= (proyecto_1 == 1)?"checked":"" %> >
						<i></i>
					  </label>
					</div>
				</div>
				<div class="form-group cb">
					<label class="col-md-2 control-label">Shopvar </label>
					<div class="col-md-10">
					  <label class="i-switch i-switch-md bg-info m-t-xs m-r">
						<input type="checkbox" name="proyecto_2" class="checkbox" value="1" <%= (proyecto_2 == 1)?"checked":"" %> >
						<i></i>
					  </label>
					</div>
				</div>
				<div class="form-group cb">
					<label class="col-md-2 control-label">Inmovar </label>
					<div class="col-md-10">
					  <label class="i-switch i-switch-md bg-info m-t-xs m-r">
						<input type="checkbox" name="proyecto_3" class="checkbox" value="1" <%= (proyecto_3 == 1)?"checked":"" %> >
						<i></i>
					  </label>
					</div>
				</div>
				<div class="form-group cb">
					<label class="col-md-2 control-label">Inforvar </label>
					<div class="col-md-10">
					  <label class="i-switch i-switch-md bg-info m-t-xs m-r">
						<input type="checkbox" name="proyecto_4" class="checkbox" value="1" <%= (proyecto_4 == 1)?"checked":"" %> >
						<i></i>
					  </label>
					</div>
				</div>
				<div class="form-group cb">
					<label class="col-md-2 control-label">Colvar </label>
					<div class="col-md-10">
					  <label class="i-switch i-switch-md bg-info m-t-xs m-r">
						<input type="checkbox" name="proyecto_5" class="checkbox" value="1" <%= (proyecto_5 == 1)?"checked":"" %> >
						<i></i>
					  </label>
					</div>
				</div>
				<div class="form-group cb">
					<label class="col-md-2 control-label">Resvar </label>
					<div class="col-md-10">
					  <label class="i-switch i-switch-md bg-info m-t-xs m-r">
						<input type="checkbox" name="proyecto_6" class="checkbox" value="1" <%= (proyecto_6 == 1)?"checked":"" %> >
						<i></i>
					  </label>
					</div>
				</div>
				<div class="form-group cb">
					<label class="col-md-2 control-label">Docvar </label>
					<div class="col-md-10">
					  <label class="i-switch i-switch-md bg-info m-t-xs m-r">
						<input type="checkbox" name="proyecto_7" class="checkbox" value="1" <%= (proyecto_7 == 1)?"checked":"" %> >
						<i></i>
					  </label>
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