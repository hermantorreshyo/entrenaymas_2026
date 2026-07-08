<script type="text/template" id="web_templates_panel_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <h1 class="m-n font-thin h3">Listado de Templates</h1>
</div>
<div class="wrapper-md ng-scope">
  <div class="panel panel-default">  
	<div class="panel-heading oh">
	  <div class="search_container col-lg-3 col-md-4 col-sm-6"></div>
	  <a class="btn pull-right btn-success btn-addon" href="app/#web_template"><i class="fa fa-plus"></i>Agregar</a>
	</div>
	<div class="panel-body">
	  <div class="b-a table-responsive">
		<table id="web_templates_table" data-ordenable-table="web_template" data-ordenable-where="" class="table table-striped ordenable m-b-none default footable">
		  <thead>
			<tr>
			  <th>Nombre</th>
			  <th class="w25">Publico</th>
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


<script type="text/template" id="web_templates_item">
	<td class="edit"><%= nombre %></td>
	<td class="edit"><%= (publico==1)?"Si":"No" %></td>
	<% if (permiso > 1) { %>
		<td><i class="fa fa-file-text-o edit text-dark" data-id="<%= id %>" /></td>
		<td><i class="glyphicon glyphicon-remove delete text-danger" data-id="<%= id %>" /></td>
	<% } %>
</script>

<script type="text/template" id="web_templates_edit_panel_template">

<div class="bg-light lter b-b wrapper-md ng-scope">
  <h1 class="m-n font-thin h3">
    <% if (id == undefined) { %>
        Nuevo Template
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
			<a href="#tab2" role="tab" data-toggle="tab"><i class="fa fa-cog"></i>Config.</a>
		</li>
	  </ul>
	  <div class="tab-content">
		<div id="tab1" class="tab-pane active panel-body">
		  <div class="form-horizontal">    
			<div class="form-group">
				<label class="col-lg-2 control-label tal">Nombre</label>
				<div class="col-lg-10">
					<input type="text" name="nombre" class="form-control" id="web_templates_nombre" value="<%= nombre %>"/>
				</div>
			</div>

      <div class="form-group">
        <label class="col-lg-2 control-label tal">Link Demo</label>
        <div class="col-lg-10">
          <input type="text" name="link_demo" class="form-control" id="web_templates_link_demo" value="<%= link_demo %>"/>
        </div>
      </div>

			<div class="form-group">
				<label class="col-lg-2 control-label tal">Path</label>
				<div class="col-lg-10">
					<input type="text" name="path" class="form-control" id="web_templates_path" value="<%= path %>"/>
				</div>
			</div>		

      <?php
      single_file_upload(array(
        "name"=>"thumbnail",
        "label"=>"Thumbnail",
        "url"=>"/sistema/web_templates/function/save_file/",
      )); ?>

      <?php
      single_file_upload(array(
        "name"=>"preview",
        "label"=>"Preview Grande",
        "url"=>"/sistema/web_templates/function/save_file/",
      )); ?>

			<div class="form-group cb">
				<label class="col-md-2 control-label">Publico </label>
				<div class="col-md-10">
					<% if (edicion) { %>
						<label class="i-switch i-switch-md bg-info m-t-xs m-r">
						  <input type="checkbox" id="web_templates_publico" name="publico" class="checkbox" value="1" <%= (publico == 1)?"checked":"" %> >
						  <i></i>
						</label>
					<% } else { %>
						<span><%= ((publico==0) ? "No" : "Si") %></span>
					<% } %>
				</div>
			</div>
			
			<div class="form-group">
				<label class="col-lg-2 control-label tal">Proyecto</label>
				<div class="col-lg-10">
					<select name="id_proyecto" class="form-control" id="web_templates_proyectos"></select>
				</div>
			</div>
			
			<div class="form-group">
				<label class="col-lg-2 control-label tal">Empresa Exclusiva</label>
				<div class="col-lg-10">
					<select name="id_empresa" class="w100p" id="web_templates_empresas"></select>
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
			
			<div class="col-xs-6 mb20">
				<textarea name="config" class="form-control" style="height: 320px"><%= config %></textarea>
			</div>
			
			<ul class="col-xs-6 text-muted fs14">
			  <li>logo_1_width | logo_1_height</li>
			  <li>slider_home_image_width | slider_home_image_height</li>
        <li>slider_2_home_image_width | slider_2_home_image_height</li>
			  <li>producto_image_width | producto_image_height </li>
			  <li>producto_galeria_image_width | producto_galeria_image_height </li>
			  <li>propiedad_image_width | propiedad_image_height </li>
        <li>entrada_image_width | entrada_image_height</li>
			  <li>entrada_image_height_categoria_{{ID_CATEGORIA}}</li>
        <li>entrada_galeria_image_width | entrada_galeria_image_height </li>
        <li>entrada_galeria_image_quality</li>
        <li>categoria_entrada_image_width | categoria_entrada_image_height</li>
			</ul>
			
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
	</div>
  </div>
</div>

</script>