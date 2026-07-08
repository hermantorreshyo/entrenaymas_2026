<script type="text/template" id="categorias_entradas_tree_panel_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
	<h1 class="m-n font-thin h3"><i class="fa fa-file-text icono_principal"></i><?php echo lang(array("es"=>"Entradas","en"=>"Posts")); ?>
		/ <b>
			<?php echo lang(array("es"=>"  Categorías  ","en"=>"  Categories  ")); ?>
		</b>
	</h1>
</div>
<div class="wrapper-md pb0">
	<div class="centrado">
		<div class="panel panel-default">
      <% if (control.check("categorias_entradas")>1) { %>
  			<div class="panel-heading oh">
  				<a class="btn btn-info btn-addon nuevo" href="javascript:void(0)">
  					<i class="fa fa-plus"></i>
  					<span class="hidden-xs">
  						<?php echo lang(array("es"=>"  Nuevo  ","en"=>"  Add category  ")); ?>
  					</span>
  				</a>
  			</div>
      <% } %>
			<div class="panel-body oh">
				<div ui-jq="nestable" class="dd">
				<%= workspace.crear_nestable(categorias_noticias) %>
				</div>
			</div>
		</div>
	</div>
</div>
</script>

<script type="text/template" id="categorias_entradas_panel_template">
	<div class="bg-light lter b-b wrapper-md ng-scope">
		<h1 class="m-n font-thin h3">
			<?php echo lang(array("es"=>"Listado de Categorias","en"=>"Category list")); ?>
		</h1>
	</div>
	
	<div class="wrapper-md pb0">
		<div class="tab-container">
			<ul class="nav nav-tabs" role="tablist">
				<li class="active">
					<a href="#tab1" role="tab" data-toggle="tab">
						<?php echo lang(array("es"=>"Buscar","en"=>"Search")); ?>
					</a>
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
					<table id="categorias_entradas_table" class="table sortable m-b-none default footable">
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


<script type="text/template" id="categorias_entradas_item">
	<td><span class='ver'><%= nombre %></span></td>
	<% if (permiso > 1) { %>
		<td><i class="fa fa-file-text-o edit text-dark" data-id="<%= id %>" /></td>
		<td><i class="glyphicon glyphicon-remove delete text-danger" data-id="<%= id %>" /></td>
	<% } %>
</script>

<script type="text/template" id="categorias_entradas_edit_panel_template">
<div class="panel rform panel-default">
	<div class="panel-heading">
		<b><%= (id == undefined) ? "<?php echo lang(array("es"=>"Nueva","en"=>"New Category")); ?>" : nombre+" (ID:"+id+")" %></b>
	</div>
	<div class="panel-body">
    <div class="form-group lang-control">
      <label class="control-label">
        <?php echo lang(array(
          "es"=>"Nombre",
          "en"=>"Name",
        )); ?>
      </label>
      <div class="input-group">
        <input type="text" id="categorias_entradas_nombre" class="form-control active" value="<%= nombre %>" name="nombre"/>
        <input type="text" id="categorias_entradas_nombre_en" name="nombre_en" class="form-control" id="categorias_entradas_nombre_en" value="<%= nombre_en %>"/>
        <input type="text" id="categorias_entradas_nombre_pt" name="nombre_pt" class="form-control" id="categorias_entradas_nombre_pt" value="<%= nombre_pt %>"/>
        <div class="input-group-btn">
          <label class="btn btn-default btn-lang active" data-id="categorias_entradas_nombre" uncheckable=""><img title="Espa&ntilde;ol" src="resources/images/es.png"/></label>
          <label class="btn btn-default btn-lang" data-id="categorias_entradas_nombre_en" uncheckable=""><img title="Ingl&eacute;s" src="resources/images/en.png"/></label>
          <label class="btn btn-default btn-lang" data-id="categorias_entradas_nombre_pt" uncheckable=""><img title="Portugues" src="resources/images/pt.png"/></label>
        </div>
      </div>
    </div>

		<div class="form-group">
			<label class="control-label"><?php echo lang(array("es"=>"Pertenece a","en"=>"Parent")); ?></label>
			<select class="form-control" name="id_padre" id="categorias_entradas_padre"></select>
		</div>

		<div class="form-group cb mb0">
      <a class="expand-link fr">
        <?php echo lang(array(
          "es"=>"+ M&aacute;s opciones",
          "en"=>"+ More options",
        )); ?>
      </a>
     </div>

	</div>
	<div class="panel-body expand">
		<div class="row">
			<div class="col-md-8">
				<?php
				single_upload(array(
					"name"=>"path",
					"label"=>lang(array("es"=>"Imagen","en"=>"Image")),
					"url"=>"/sistema/entradas/function/save_image/",
          "url_file"=>"/sistema/entradas/function/save_file/",
					"width"=>(isset($empresa->config["categoria_entrada_image_width"]) ? $empresa->config["categoria_entrada_image_width"] : 256),
					"height"=>(isset($empresa->config["categoria_entrada_image_height"]) ? $empresa->config["categoria_entrada_image_height"] : 256),
					"quality"=>(isset($empresa->config["categoria_entrada_image_quality"]) ? $empresa->config["categoria_entrada_image_quality"] : 0),
					"thumbnail_width"=>(isset($empresa->config["categoria_entrada_thumbnail_width"]) ? $empresa->config["categoria_entrada_thumbnail_width"] : 0),
					"thumbnail_height"=>(isset($empresa->config["categoria_entrada_thumbnail_height"]) ? $empresa->config["categoria_entrada_thumbnail_height"] : 0),
				)); ?>
			</div>
			<div class="col-md-4">
				<div class="form-group cb">
					<label class="control-label tal">Color</label>
					<div class="input-group color colorpicker-component">
						<input type="text" class="form-control" value="<%= color %>" />
						<span class="input-group-addon"><i></i></span>
					</div>
				</div>
			</div>
		</div>

    <div class="form-group">
      <label class="control-label">
        <?php echo lang(array(
          "es"=>"Descripcion",
          "en"=>"Description",
        )); ?>
      </label>
      <textarea name="texto" class="form-control" id="categoria_entrada_texto"><%= texto %></textarea>
    </div>

		<div class="form-group">
      <label class="control-label">
        <?php echo lang(array(
          "es"=>"Categorias relacionadas",
          "en"=>"Related categories",
        )); ?>
      </label>
			<div id="categorias_entradas_tree" style="overflow: auto;"></div>
		</div>

    <div class="form-group">
      <label class="control-label"><?php echo lang(array("es"=>"Link Externo","en"=>"External Link")); ?></label>
      <input type="text" class="form-control" name="external_link" id="categoria_entrada_external_link" value="<%= external_link %>" />
    </div>

		<% if (edicion) { %>
			<div class="form-group cb">
				<div class="row">
					<div class="col-md-6">
						<label class="i-checks">
							<input type="checkbox" name="activo" class="checkbox" value="1" <%= (activo == 1)?"checked":"" %> >
							<i></i> <?php echo lang(array("es"=>"La categor&iacute;a esta activa.","en"=>"The category is active.")); ?>
						</label>
					</div>
          <div class="col-md-6">
            <label class="i-checks">
              <input type="checkbox" name="mostrar_home" class="checkbox" value="1" <%= (mostrar_home == 1)?"checked":"" %> >
              <i></i> <?php echo lang(array("es"=>"Mostrar en el Inicio.","en"=>"Show in home.")); ?>
            </label>
          </div>
					<?php if (isset($volver_superadmin) && $volver_superadmin == 1) { ?>
						<div class="col-md-6">
							<label class="i-checks">
								<input type="checkbox" name="fija" class="checkbox" value="1" <%= (fija == 1)?"checked":"" %> >
								<i></i> Fijar la categoria en el menu lateral.
							</label>
						</div>
					<?php } ?>
				</div>
			</div>
		<% } %>
		
	</div>
	<div class="panel-footer clearfix tar">
		<button class="btn guardar btn-success"><?php echo lang(array("es"=>"Guardar","en"=>"Save")); ?></button>
		<% if (id != undefined && fija == 0) { %>
			<button class="btn btn-danger eliminar fl"><?php echo lang(array("es"=>"Eliminar","en"=>"Delete")); ?></button>
		<% } %>
	</div>
</div>
</script>

<script type="text/template" id="categorias_entradas_edit_mini_panel_template">
<div class="panel pb0 mb0">
	<div class="panel-body">
		<div class="form-group">
			<input type="text" placeholder="<?php echo lang(array("es"=>"Nombre","en"=>"Name")); ?>" name="nombre" class="form-control tab" id="categorias_entradas_mini_nombre" value="<%= nombre %>"/>
		</div>
		<div class="form-group">
			<select class="form-control tab" name="id_padre" id="categorias_entradas_mini_padre"></select>
		</div>
		<div class="row">
			<div class="col-md-6">
				<button class="btn cerrar tab btn-default btn-block"><?php echo lang(array("es"=>"Cerrar","en"=>"Close")); ?></button>
			</div>
			<div class="col-md-6">
				<button class="btn guardar tab btn-success btn-block"><?php echo lang(array("es"=>"Guardar","en"=>"Save")); ?></button>
			</div>			
		</div>
	</div>
</div>
</script>