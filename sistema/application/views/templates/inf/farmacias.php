<script type="text/template" id="farmacias_panel_template">
  <div class="bg-light lter b-b wrapper-md ng-scope">
	<h1 class="m-n font-thin h3">Listado de Farmacias</h1>
  </div>
  <div class="wrapper-md ng-scope">
	<div class="panel panel-default">
	  <div class="panel-heading oh">
		<div class="search_container col-lg-3 col-md-4 col-sm-6"></div>
		<a class="btn pull-right btn-success btn-addon" href="app/#farmacia"><i class="fa fa-plus"></i>Nueva</a>
	  </div>
	  <div class="panel-body">
		<div class="b-a table-responsive">
		  <table id="farmacias_table" data-ordenable-table="farmacia" data-ordenable-where="" class="table table-striped ordenable m-b-none default footable">
			<thead>
			  <tr>
				<th>Nombre</th>
				<th>Direccion</th>
				<th>Telefono</th>
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


<script type="text/template" id="farmacias_item">
  <td class="edit"><%= nombre %></td>
  <td class="edit"><%= direccion %></td>
  <td class="edit"><%= telefono %></td>
  <% if (permiso > 1) { %>
	<td><i class="fa fa-file-text-o edit text-dark" data-id="<%= id %>" /></td>
	<td><i class="glyphicon glyphicon-remove delete text-danger" data-id="<%= id %>" /></td>
  <% } %>
</script>

<script type="text/template" id="farmacias_edit_panel_template">

<div class="bg-light lter b-b wrapper-md ng-scope">
  <h1 class="m-n font-thin h3">
    <% if (id == undefined) { %>
        Nueva Farmacia
    <% } else { %>
        <%= nombre %>
    <% } %>	      
  </h1>
</div>

<div class="wrapper-md">
    <div class="tab-container">
        <ul class="nav nav-tabs" role="tablist">
          <li class="active">
              <a href="#tab1" role="tab" data-toggle="tab"><i class="fa fa-info"></i>Datos</a>
          </li>
          <li>
            <a href="#tab2" id="link_tab2" role="tab" data-toggle="tab"><i class="fa fa-map-marker"></i>Ubicaci&oacute;n</a>
          </li>
        </ul>
        <div class="tab-content">
			<div id="tab1" class="tab-pane active panel-body">
				<div class="form-horizontal">    
	
					<div class="form-group">
						<label class="col-md-2 control-label tal">Nombre</label>
						<div class="col-md-10">
							<input type="text" name="nombre" class="form-control" id="farmacias_nombre" value="<%= nombre %>"/>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-2 control-label tal">Direccion</label>
						<div class="col-md-10">
							<input type="text" name="direccion" class="form-control" id="farmacias_direccion" value="<%= direccion %>"/>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-2 control-label tal">Telefono</label>
						<div class="col-md-10">
							<input type="text" name="telefono" class="form-control" id="farmacias_telefono" value="<%= telefono %>"/>
						</div>
					</div>
					
					<div class="line line-dashed b-b line-lg pull-in"></div>
					<% if (edicion) { %>
						<div class="form-group">
							<div class="col-xs-12">    
								<button class="btn btn-success guardar">Guardar</button>
							</div>
						</div>
					<% } %>
				</div>
			</div>
            <div id="tab2" class="tab-pane panel-body">
              <div class="form-horizontal">
                
                <div class="h4">Mapa</div>
                <div class="line b-b m-b"></div>
                <div style="height:400px;" id="mapa"></div>
                <div class="help-block">Puede arrastrar el marcador del mapa para ponerlo en la direccion exacta.</div>
                
                <div class="line line-dashed b-b line-lg pull-in"></div>
                <% if (edicion) { %>
                    <div class="form-group">
                        <div class="col-xs-12">    
                            <button class="btn btn-success guardar">Guardar</button>
                            <img src="/sistema/resources/images/ajax-loader.gif" class="img_loading"/>
                        </div>
                    </div>
                <% } %>
              </div>
            </div>
		</div>
    </div>
</div>

</script>