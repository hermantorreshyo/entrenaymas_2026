<script type="text/template" id="galerias_categorias_tree_panel_template">

    <div class="bg-light lter b-b wrapper-md ng-scope">
      <h1 class="m-n font-thin h3">Categorias</h1>
    </div>
    
    <div class="wrapper-md pb0">
        <div class="panel panel-default">
            <div class="panel-heading oh">
                <a class="btn btn-success btn-sm btn-addon nuevo" href="javascript:void(0)">
                    <i class="fa fa-plus"></i>
                    <span class="hidden-xs">Nuevo</span>
                </a>
            </div>
            <div class="panel-body oh">
			  <div id="galerias_categorias_nestable" ui-jq="nestable" class="dd"></div>
            </div>
        </div>
    </div>
	
</script>


<script type="text/template" id="galerias_categorias_panel_template">
    
    <div class="bg-light lter b-b wrapper-md ng-scope">
      <h1 class="m-n font-thin h3">Listado de Categorias</h1>
    </div>
    
    <div class="wrapper-md pb0">
        <div class="tab-container">
            <ul class="nav nav-tabs" role="tablist">
              <li class="active">
                  <a href="#tab1" role="tab" data-toggle="tab">Buscar</a>
              </li>
            </ul>
            <div class="tab-content">
                <div id="tab1" class="tab-pane active panel-body pt5 pb5">
                    <div class="form-horizontal">
                        <div class="form-group m-b-none">
                            <div class="search_container col-lg-4 col-md-6 col-sm-9 col-xs-12"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>    

    <div class="wrapper-md ng-scope pt0">
        <div class="panel panel-default">
        
            <div class="panel-heading oh">
                <span class="font-bold m-t-xs pull-left">Resultados de B&uacute;squeda</span>
                <a class="btn pull-right btn-success btn-addon nuevo" href="javascript:void(0)"><i class="fa fa-plus"></i>Nuevo</a>
            </div>
            <div class="panel-body">
                <div class="b-a table-responsive">
                    <table id="galerias_categorias_table" class="table sortable m-b-none default footable">
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


<script type="text/template" id="galerias_categorias_item">
	<td><span class='ver'><%= nombre %></span></td>
	<% if (permiso > 1) { %>
		<td><i class="fa fa-file-text-o edit text-dark" data-id="<%= id %>" /></td>
		<td><i class="glyphicon glyphicon-remove delete text-danger" data-id="<%= id %>" /></td>
	<% } %>
</script>

<script type="text/template" id="galerias_categorias_edit_panel_template">

<div class="bg-light lter b-b wrapper-md ng-scope">
  <h1 class="m-n font-thin h3">
    <% if (id == undefined) { %>
        Nueva Categoria
    <% } else { %>
        <%= nombre %> (ID:<%= id %>)
    <% } %>	      
  </h1>
</div>

<div class="wrapper-md ng-scope">
  <div class="tab-container">
	  <ul class="nav nav-tabs" role="tablist">
		<li class="active">
			<a href="#tab1" role="tab" data-toggle="tab"><i class="fa fa-info"></i>Informacion</a>
		</li>
	  </ul>
	  <div class="tab-content">
          <div id="tab1" class="tab-pane active panel-body">

            <div class="form-horizontal">

                <div class="form-group">
                    <label class="col-lg-2 control-label">Nombre</label>
                    <div class="col-lg-10">
                        <% if (edicion) { %>
                            <input type="text" name="nombre" class="form-control" id="galerias_categorias_nombre" value="<%= nombre %>"/>
                        <% } else { %>
                            <span><%= nombre %></span>
                        <% } %>
                    </div>
                </div>
				
                <div class="form-group">
                    <label class="col-lg-2 control-label">Pertenece a</label>
                    <div class="col-lg-10">
                        <select class="form-control" name="id_padre" id="galerias_categorias_padre"></select>
                    </div>
                </div>
				
				  <div class="form-group cb">
					  <label class="col-md-2 control-label">Activo </label>
					  <div class="col-md-10">
						  <% if (edicion) { %>
							  <label class="i-switch i-switch-md bg-info m-t-xs m-r">
								<input type="checkbox" name="activo" class="checkbox" value="1" <%= (activo == 1)?"checked":"" %> >
								<i></i>
							  </label>
						  <% } else { %>
							  <span><%= ((activo==0) ? "No" : "Si") %></span>
						  <% } %>
					  </div>
				  </div>
				  
                <div class="line line-dashed b-b line-lg pull-in"></div>
                <% if (edicion) { %>
                    <div class="form-group">
                        <div class="col-xs-12">
                            <button class="btn guardar btn-success">Guardar</button>
                            <% if (id != undefined) { %>
                                <button class="btn btn-danger eliminar fr">Eliminar</button>
                            <% } %>                            
                        </div>
                    </div>
                <% } %>
				  
			</div>
		  </div>
		  
	  </div>
  </div>
</div>
</script>


<script type="text/template" id="subgalerias_categorias_panel_template">
    
<div class="bg-light lter b-b wrapper-md ng-scope">
  <h1 class="m-n font-thin h3">Listado de Subgalerias_categorias</h1>
</div>

<div class="wrapper-md pb0">
	<div class="tab-container">
		<ul class="nav nav-tabs" role="tablist">
		  <li class="active">
			  <a href="#tab1" role="tab" data-toggle="tab">Buscar</a>
		  </li>
		</ul>
		<div class="tab-content">
			<div id="tab1" class="tab-pane active panel-body pt5 pb5">
				<div class="form-horizontal">
					<div class="form-group m-b-none">
						<div class="search_container col-lg-4 col-md-6 col-sm-9 col-xs-12"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>    

<div class="wrapper-md ng-scope pt0">
	<div class="panel panel-default">
	
		<div class="panel-heading oh">
			<span class="font-bold m-t-xs pull-left">Resultados de B&uacute;squeda</span>
			<a class="btn pull-right btn-success btn-addon" href="app/#subcategoria_entrada"><i class="fa fa-plus"></i>Nuevo</a>
		</div>
		<div class="panel-body">
			<div class="b-a table-responsive">
				<table id="subgalerias_categorias_table" class="table sortable m-b-none default footable">
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


<script type="text/template" id="subgalerias_categorias_item">
	<td>
		<span class='ver'><%= nombre %></span>
	</td>
	<% if (permiso > 1) { %>
        <td><i class="fa fa-file-text-o edit text-dark" data-id="<%= id_categoria_entrada %>" /></td>
        <td><i class="glyphicon glyphicon-remove delete text-danger" data-id="<%= id_categoria_entrada %>" /></td>
	<% } %>
</script>

<script type="text/template" id="subgalerias_categorias_edit_panel_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <h1 class="m-n font-thin h3">
    <% if (id == undefined) { %>
        Nuevo Subcategoria_entrada
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
                            <input type="text" name="nombre" class="form-control" id="subgalerias_categorias_nombre" value="<%= nombre %>"/>
                        <% } else { %>
                            <span><%= nombre %></span>
                        <% } %>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="col-lg-2 control-label">Rubro</label>
                    <div class="col-lg-10">
                        <select class="form-control" name="id_categoria_entrada" id="subgalerias_categorias_categoria_entrada"></select>
                    </div>
                </div>            
                <div class="line line-dashed b-b line-lg pull-in"></div>
                <% if (edicion) { %>
                    <div class="form-group">
                        <div class="col-lg-10">
                            <button class="btn guardar btn-success">Guardar</button>
                            <% if (id != undefined) { %>
                                <button class="btn btn-danger eliminar fr">Eliminar</button>
                            <% } %>                            
                        </div>
                    </div>
                <% } %>
            </div>
        </div>
    </div>
</div>

</script>