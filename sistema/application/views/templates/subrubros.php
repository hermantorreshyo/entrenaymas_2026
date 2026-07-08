<script type="text/template" id="subrubros_panel_template">
    
<div class="bg-light lter b-b wrapper-md ng-scope">
  <h1 class="m-n font-thin h3">Listado de Subrubros</h1>
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
			<a class="btn pull-right btn-success btn-addon" href="app/#subrubro"><i class="fa fa-plus"></i>Nuevo</a>
		</div>
		<div class="panel-body">
			<div class="b-a table-responsive">
				<table id="subrubros_table" class="table sortable m-b-none default footable">
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


<script type="text/template" id="subrubros_item">
	<td>
		<span class='ver'><%= nombre %></span>
	</td>
	<% if (permiso > 1) { %>
        <td><i class="fa fa-file-text-o edit text-dark" data-id="<%= id_rubro %>" /></td>
        <td><i class="glyphicon glyphicon-remove delete text-danger" data-id="<%= id_rubro %>" /></td>
	<% } %>
</script>

<script type="text/template" id="subrubros_edit_panel_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <h1 class="m-n font-thin h3">
    <% if (id == undefined) { %>
        Nuevo Subrubro
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
                            <input type="text" name="nombre" class="form-control" id="subrubros_nombre" value="<%= nombre %>"/>
                        <% } else { %>
                            <span><%= nombre %></span>
                        <% } %>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="col-lg-2 control-label">Rubro</label>
                    <div class="col-lg-10">
                        <select class="form-control" name="id_rubro" id="subrubros_rubro"></select>
                    </div>
                </div>            
                <div class="line line-dashed b-b line-lg pull-in"></div>
                <% if (edicion) { %>
                    <div class="form-group">
                        <div class="col-lg-offset-2 col-lg-10">
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