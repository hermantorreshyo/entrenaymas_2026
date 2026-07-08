<script type="text/template" id="web_paginas_panel_template">
  <div class="bg-light lter b-b wrapper-md ng-scope">
	<h1 class="m-n font-thin h3">P&aacute;ginas de Informaci&oacute;n</h1>
  </div>
  <% if (paginas.size() == 0) { %>
	<div class="seccion_vacia">
	  <h1 class="h1">Todav&iacute;a no ten&eacute;s p&aacute;ginas</h1>
	  <h3 class="h3">Para crear tu primera p&aacute;gina, hace click en el siguiente bot&oacute;n</h3>
	  <div class="list-icon">
		<a href="app/#web_pagina"><i class="icon-note"></i></a>
	  </div>
	  <div>
		<a class="btn btn-lg btn-primary btn-addon" href="app/#web_pagina">
		  <i class="fa fa-plus"></i><span>Nueva P&aacute;gina</span>
		</a>
	  </div>
	  <p>
		Las p&aacute;ginas brindan informaci&oacute;n al usuario sobre tu empresa o negocio.<br/>
		Por ej: Quienes Somos, Sobre Nosotros, Acerca de Mi, etc.<br/><br/>
		Si necesitas ayuda o asesoramiento, no dudes en <a class="text-info">comunicarte con nosotros</a>.
	  </p>
	</div>
  <% } else { %>	
    <div class="wrapper-md ng-scope">
        <div class="panel panel-default">
        
            <div class="panel-heading oh">
				<div class="search_container col-lg-3 col-md-4 col-sm-6"></div>
				<a class="btn pull-right btn-success btn-addon" href="app/#web_pagina"><i class="fa fa-plus"></i>Agregar</a>
            </div>
            <div class="panel-body">
                <div class="b-a table-responsive">
                    <table id="web_paginas_table" data-ordenable-table="web_pagina" data-ordenable-where="" class="table table-striped ordenable m-b-none default footable">
                        <thead>
                            <tr>
                                <th>Titulo</th>
								<th>Categoria</th>
                                <% if (permiso > 1) { %>
                                    <th class="w25"></th>
                                    <th class="w25"></th>
                                <% } %>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot class="web_pagination_container hide-if-no-paging"></tfoot>
                    </table>
                </div>
            </div>
        </div>
	</div>
  <% } %>
</script>


<script type="text/template" id="web_paginas_item">
	<td><%= titulo_es %></td>
	<td><%= categoria %></td>
	<% if (permiso > 1) { %>
		<td><i class="fa fa-file-text-o edit text-dark" data-id="<%= id %>" /></td>
		<td><i class="glyphicon glyphicon-remove delete text-danger" data-id="<%= id %>" /></td>
	<% } %>
</script>

<script type="text/template" id="web_paginas_edit_panel_template">

<div class="bg-light lter b-b wrapper-md ng-scope">
  <h1 class="m-n font-thin h3">
    <% if (id == undefined) { %>
        Nueva P&aacute;gina
    <% } else { %>
        <%= titulo_es %>
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
              <a href="#tab3" role="tab" data-toggle="tab"><i class="fa fa-globe"></i>SEO</a>
          </li>
        </ul>
        <div class="tab-content">
            <div id="tab1" class="tab-pane active panel-body">
				<div class="form-horizontal">    
	
					<div class="form-group">
						<label class="col-lg-1 col-md-3 col-sm-4 control-label tal">Titulo</label>
						<div class="col-lg-11 col-md-9 col-sm-8">
							<input type="text" name="titulo_es" class="form-control" id="web_paginas_titulo_es" value="<%= titulo_es %>"/>
						</div>
					</div>

					<div class="form-group">
						<label class="col-lg-1 col-md-3 col-sm-4 control-label tal">Categoria</label>
						<div class="col-lg-11 col-md-9 col-sm-8">
							<select class="form-control" id="web_paginas_categorias" name="id_categoria_web_pagina"></select>
						</div>
					</div>

					<?php
					single_upload(array(
					  "name"=>"path_2",
					  "label"=>"Imagen Listado",
					  "url"=>"/sistema/web_paginas/function/save_image/",
					  "width"=>(isset($empresa->config["pagina_2_image_width"]) ? $empresa->config["pagina_2_image_width"] : 400),
					  "height"=>(isset($empresa->config["pagina_2_image_height"]) ? $empresa->config["pagina_2_image_height"] : 400),
					)); ?>
					
					<div class="form-group">
						<label class="col-lg-1 col-md-3 col-sm-4 control-label tal">Texto Listado</label>
						<div class="col-lg-11 col-md-9 col-sm-8">
							<input type="text" name="breve_es" class="form-control" id="web_paginas_breve_es" value="<%= breve_es %>"/>
						</div>
					</div>
					
					
					<?php
					single_upload(array(
					  "name"=>"path",
					  "label"=>"Imagen Principal",
					  "url"=>"/sistema/web_paginas/function/save_image/",
					  "width"=>(isset($empresa->config["pagina_image_width"]) ? $empresa->config["pagina_image_width"] : 400),
					  "height"=>(isset($empresa->config["pagina_image_height"]) ? $empresa->config["pagina_image_height"] : 400),
					)); ?>
					
                    <div class="form-group cb">
						<label class="col-lg-1 col-md-3 col-sm-4 control-label tal">Activo</label>
						<div class="col-lg-11 col-md-9 col-sm-8">
                            <% if (edicion) { %>
                                <label class="i-switch i-switch-md bg-info m-t-xs m-r">
                                  <input type="checkbox" id="web_paginas_activo" name="activo" class="checkbox" value="1" <%= (activo == 1)?"checked":"" %> >
                                  <i></i>
                                </label>
                            <% } else { %>
                                <span><%= ((activo==0) ? "No" : "Si") %></span>
                            <% } %>
                        </div>
                    </div>					
					
					<div class="form-group">
					  <div class="col-xs-12">
						<textarea name="texto_es" id="web_paginas_texto"><%= texto_es %></textarea>
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

            <div id="tab3" class="tab-pane panel-body">
				<div class="form-horizontal">
					<div class="form-group">
					  <label class="col-lg-1 control-label">Titulo</label>
					  <div class="col-lg-11">
						<textarea name="seo_title" class="form-control"><%= seo_title %></textarea>
						<span class="help-block m-b-none">Escribe el titulo del navegador que se utilizara en la web_pagina.</span>
					  </div>
					</div>
					<div class="line line-dashed b-b line-lg pull-in"></div>
					<div class="form-group">
					  <label class="col-lg-1 control-label">Descripcion</label>
					  <div class="col-lg-11">
						<textarea name="seo_description" class="form-control"><%= seo_description %></textarea>
						<span class="help-block m-b-none">Escribe una breve descripcion de la web_pagina.</span>
					  </div>
					</div>
					<div class="line line-dashed b-b line-lg pull-in"></div>
					<div class="form-group">
					  <label class="col-lg-1 control-label">Palabras Clave</label>
					  <div class="col-lg-11">
						<textarea name="seo_keywords" class="form-control"><%= seo_keywords %></textarea>
						<span class="help-block m-b-none">Escribe las palabras clave que describan tu proyecto.</span>
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
		</div>
		
		
    </div>
</div>

</script>