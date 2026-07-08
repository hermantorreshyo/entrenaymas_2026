<script type="text/template" id="rss_sources_panel_template">
    
    <div class="bg-light lter b-b wrapper-md ng-scope">
      <h1 class="m-n font-thin h3">RSS Feeds</h1>
    </div>
    <div class="wrapper-md ng-scope">
        <div class="panel panel-default">
        
            <div class="panel-heading oh">
                <div class="search_container col-lg-4 col-md-6 col-sm-9 col-xs-12"></div>
                <a class="btn pull-right btn-success btn-addon" href="app/#rss_source"><i class="fa fa-plus"></i>Nuevo</a>
            </div>
            <div class="panel-body">
                <div class="b-a table-responsive">
                    <table id="rss_sources_table" class="table table-striped sortable m-b-none default footable">
                        <thead>
                            <tr>
                                <th class="sorting" data-sort-by="nombre">Nombre</th>
								<th class="sorting" data-sort-by="url">URL</th>
                                <% if (permiso > 1) { %>
								  <th></th>
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


<script type="text/template" id="rss_sources_item">
	<td><span class='ver'><%= nombre %></span></td>
	<td><span class='ver'><%= url %></span></td>
	<% if (permiso > 1) { %>
	  <td><a href="/sistema/application/cronjobs/rss.php?id_empresa=<%= ID_EMPRESA %>&id_rss=<%= id %>" target="_blank" class="btn btn-default">Ejecutar</a></td>
	  <td><i class="fa fa-file-text-o edit text-dark" data-id="<%= id %>" /></td>
	  <td><i class="glyphicon glyphicon-remove delete text-danger" data-id="<%= id %>" /></td>
	<% } %>
</script>

<script type="text/template" id="rss_sources_edit_panel_template">

<div class="bg-light lter b-b wrapper-md ng-scope">
  <h1 class="m-n font-thin h3">
    <% if (id == undefined) { %>
        Nuevo RSS
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
					  <input type="text" name="nombre" class="form-control" id="rss_sources_nombre" value="<%= nombre %>"/>
                    </div>
                </div>				
				
                <div class="form-group">
                    <label class="col-lg-2 control-label">URL</label>
                    <div class="col-lg-10">
					  <input type="text" name="url" class="form-control" id="rss_sources_url" value="<%= url %>"/>
                    </div>
                </div>
				
				<div class="form-group cb">
					<label class="col-md-2 control-label">Feed Activo </label>
					<div class="col-md-10">
					  <label class="i-switch i-switch-md bg-info m-t-xs m-r">
						<input type="checkbox" id="rss_sources_activo" name="activo" class="checkbox" value="1" <%= (activo == 1)?"checked":"" %> >
						<i></i>
					  </label>
					</div>
				</div>
				
                <div class="form-group">
                    <label class="col-lg-2 control-label">Cantidad Feeds</label>
                    <div class="col-lg-10">
					  <input type="text" name="noticias_cantidad" class="form-control" id="rss_sources_noticias_cantidad" value="<%= noticias_cantidad %>"/>
                    </div>
                </div>
				
                <div class="form-group">
                    <label class="col-lg-2 control-label">Nombre de fuente</label>
                    <div class="col-lg-10">
					  <input type="text" name="fuente" class="form-control" id="rss_sources_noticias_fuente" value="<%= fuente %>"/>
                    </div>
                </div>				
				<!--
                <div class="form-group">
                    <label class="col-lg-2 control-label">Tiempo de actualizacion</label>
                    <div class="col-lg-10">
					  <select class="form-control" name="tiempo" id="rss_sources_tiempo">
						<option value="30" <%= (tiempo==30)?"selected":"" %>>30 minutos</option>
						<option value="60" <%= (tiempo==60)?"selected":"" %>>1 hora</option>
						<option value="120" <%= (tiempo==120)?"selected":"" %>>2 horas</option>
						<option value="360" <%= (tiempo==360)?"selected":"" %>>6 horas</option>
						<option value="720" <%= (tiempo==720)?"selected":"" %>>12 horas</option>
						<option value="1440" <%= (tiempo==1440)?"selected":"" %>>24 horas</option>
					  </select>
                    </div>
                </div>
				-->
				
				<div class="h4 m-t-lg">Valores por defecto de las noticias cargadas</div>
				<div class="line b-b m-b"></div>
				
				<div class="form-group cb">
					<label class="col-md-2 control-label">Activa </label>
					<div class="col-md-10">
					  <label class="i-switch i-switch-md bg-info m-t-xs m-r">
						<input type="checkbox" id="rss_sources_noticias_activo" name="noticias_activo" class="checkbox" value="1" <%= (noticias_activo == 1)?"checked":"" %> >
						<i></i>
					  </label>
					</div>
				</div>				
				
				<div class="form-group cb">
					<label class="col-md-2 control-label">Destacada </label>
					<div class="col-md-10">
					  <label class="i-switch i-switch-md bg-info m-t-xs m-r">
						<input type="checkbox" id="rss_sources_noticias_destacado" name="noticias_destacado" class="checkbox" value="1" <%= (noticias_destacado == 1)?"checked":"" %> >
						<i></i>
					  </label>
					</div>
				</div>
				
				<div class="form-group">
					<label class="col-md-2 control-label">Categoria por defecto</label>
					<div class="col-md-10">
						<select id="rss_sources_categorias" class="form-control"></select>
					</div>
				</div>				
				  <?php /*
                <div class="form-group">
                    <label class="col-lg-2 control-label">Etiquetas por defecto</label>
                    <div class="col-lg-10">
					  <input type="text" name="noticias_etiquetas" id="rss_sources_noticias_etiquetas" value="<%= noticias_etiquetas %>"/>
                    </div>
                </div>					
                */ ?>

				<div class="form-group cb">
					<label class="col-md-2 control-label">Obtener contenido </label>
					<div class="col-md-10">
					  <label class="i-switch i-switch-md bg-info m-t-xs m-r">
						<input type="checkbox" id="rss_sources_noticias_incluir_contenido" name="noticias_incluir_contenido" class="checkbox" value="1" <%= (noticias_incluir_contenido == 1)?"checked":"" %> >
						<i></i>
					  </label>
					</div>
				</div>
				
                <div class="form-group">
                    <label class="col-lg-2 control-label">XPath al contenido</label>
                    <div class="col-lg-10">
					  <input type="text" name="noticias_path_contenido" class="form-control" id="rss_sources_noticias_path_contenido" value="<%= noticias_path_contenido %>"/>
                    </div>
                </div>
				
                <div class="form-group">
                    <label class="col-lg-2 control-label">Reemplazos</label>
                    <div class="col-lg-10">
					  <textarea style="height: 280px;" class="form-control" name="reemplazos" id="rss_sources_reemplazos"><%= reemplazos %></textarea>
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