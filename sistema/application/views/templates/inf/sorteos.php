<script type="text/template" id="sorteos_resultados_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <h1 class="m-n font-thin h3">Listado de Sorteos</h1>
</div>
<div class="wrapper-md ng-scope">
    <div class="panel panel-default">
        <div class="panel-heading clearfix">
          <div class="row">
            <div class="col-md-6 col-lg-3 sm-m-b">
              <div class="input-group">
                  <input type="text" id="sorteos_buscar" placeholder="Buscar..." autocomplete="off" class="form-control">
                  <span class="input-group-btn">
                    <button class="btn btn-default"><i class="fa fa-search"></i></button>
                  </span>
              </div>
            </div>
            <% if (!seleccionar) { %>
              <div class="col-md-6 col-lg-offset-3 col-lg-6 text-right">
                <a class="btn btn-success btn-addon ml5" href="app/#sorteo">
                  <i class="fa fa-plus"></i><span class="hidden-xs">Nuevo</span>
                </a>
              </div>
            <% } %>
          </div>
        </div>
        <div class="panel-body">
            <div class="b-a table-responsive">
            <table id="sorteos_tabla" class="table table-striped sortable m-b-none default footable">
                <thead>
                  <tr>
                    <% if (!seleccionar) { %>
                      <th style="width:20px;">
                          <label class="i-checks m-b-none">
                              <input class="esc sel_todos" type="checkbox"><i></i>
                          </label>
                      </th>
                    <% } else { %>
                      <th style="width:20px;"></th>
                    <% } %>
                    <th>Titulo</th>
                    <% if (!seleccionar) { %>
                      <th style="width: 120px;">Acciones</th>
                    <% } %>
                  </tr>
                </thead>
                <tbody class="tbody"></tbody>
                <tfoot class="pagination_container hide-if-no-paging"></tfoot>
              </table>
            </div>
        </div>          
    </div>
</div>
</script>

<script type="text/template" id="sorteos_item_resultados_template">
    <% var clase = (activo==1)?"":"text-muted"; %>
    <% if (seleccionar) { %>
      <td>
        <label class="i-checks m-b-none">
          <input class="radio esc" value="<%= codigo %>" name="radio" type="radio"><i></i>
        </label>
      </td>
    <% } else { %>
      <td>
        <label class="i-checks m-b-none">
          <input class="esc check-row" value="<%= id %>" type="checkbox"><i></i>
        </label>
      </td>    
    <% } %>
    <td class="<%= clase %> data"><%= titulo %></td>
    <% if (!seleccionar) { %>
      <td class="tar <%= clase %>">
        <i title="Activo" class="fa-check iconito fa activo <%= (activo == 1)?"active":"" %>"></i>
        <i title="Destacado" class="fa fa-star iconito destacado <%= (destacado == 1)?"active":"" %>"></i>
        <div class="btn-group dropdown">
          <i title="Opciones" class="iconito fa fa-caret-down dropdown-toggle" data-toggle="dropdown"></i>
          <ul class="dropdown-menu pull-right">
            <li><a href="javascript:void(0)" class="duplicar" data-id="<%= id %>">Duplicar</a></li>
            <li><a href="javascript:void(0)" class="eliminar" data-id="<%= id %>">Eliminar</a></li>
          </ul>
        </div>        
      </td>      
    <% } %>
</script>


<script type="text/template" id="sorteo_template">
    
<div class="bg-light lter b-b wrapper-md ng-scope">
  <h1 class="m-n font-thin h3">
	<% if (id == undefined) { %>
	    Nueva Sorteo
	<% } else { %>
	    <%= titulo %>
	<% } %>
  </h1>
</div>

<div class="wrapper-md pb0">
    <div class="tab-container">
        <ul class="nav nav-tabs" role="tablist">
          <li class="active">
            <a href="#tab1" role="tab" data-toggle="tab"><i class="fa fa-info"></i>Informaci&oacute;n</a>
          </li>
          <?php /*
		  <li>
			  <a href="#tab3" role="tab" data-toggle="tab"><i class="fa fa-comments"></i>Participantes</a>
		  </li>		  	
      */ ?>	  
        </ul>
        <div class="tab-content">
            <div id="tab1" class="tab-pane active panel-body">
                        
                <div class="form-horizontal">
                
                    <div class="form-group">
                        <label class="col-md-2 control-label">Titulo</label>
                        <div class="col-md-10">
                            <% if (edicion) { %>
                                <input type="text" required name="titulo" id="sorteo_titulo" value="<%= titulo %>" class="form-control"/>
                            <% } else { %>
                                <span><%= titulo %></span>
                            <% } %>
                        </div>
                    </div>
		    
                    <div class="form-group">
                        <label class="col-md-2 control-label">Texto</label>
                        <div class="col-md-10">
                            <% if (edicion) { %>
			      <textarea name="texto" class="form-control" id="sorteo_texto"><%= texto %></textarea>
                            <% } else { %>
                                <span><%= texto %></span>
                            <% } %>
                        </div>
                    </div>		    
					
                    <?php
                    /*
                    single_file_upload(array(
                      "name"=>"path",
                      "label"=>"Imagen",
                      "url"=>"sorteos/function/save_file/",
                    ));                    
                    */
                    ?>
                    <?php /*
                    <div class="form-group">
                        <label class="col-md-2 control-label">V&aacute;lida</label>
                        <div class="col-md-10">
                          <div class="form-inline">
                            <input type="text" name="valida_desde" id="sorteo_valida_desde" value="<%= valida_desde %>" class="form-control w150"/>
                            <input type="text" name="valida_hasta" id="sorteo_valida_hasta" value="<%= valida_hasta %>" class="form-control w150"/>
                          </div>
                        </div>
                    </div>
                    */ ?>
					
                    <div class="line line-dashed b-b line-lg pull-in"></div>
                    <% if (edicion) { %>
                        <div class="form-group">
                            <div class="col-xs-12">
                                <button class="btn guardar btn-success">Guardar</button>
                                <img src="/sistema/resources/images/ajax-loader.gif" class="img_loading"/>
                            </div>
                        </div>
                    <% } %>                    
                    
                </div>
            </div>
			
            <div id="tab3" class="tab-pane panel-body">
              <div class="form-horizontal">
                
                <div class="h4">Participantes</div>
                <div class="line b-b m-b"></div>
                
				<div class="b-a table-responsive">
					<table id="sorteos_tabla" class="table table-striped sortable m-b-none default footable">
						<thead>
							<tr>
								<th>Foto</th>
								<th>Nombre</th>
								<th>Fecha</th>
							</tr>
						</thead>
						<tbody class="tbody">
							<% if (usuarios.length == 0) { %>
								<tr><td colspan="5">El sorteo no tiene participantes.</td></tr>
							<% } else { %>
								<% for (var i=0;i<usuarios.length;i++) { %>
									<% var c = usuarios[i] %>
									<tr>
										<td>
										<% if (!isEmpty(c.path)) { %>
										  <img src="<%= show_path(c.path) %>" class="customcomplete-image"/>
										<% } %>
										</td>
										<td><a class="text-info" href="app/#web_user/<%= c.id_usuario %>"><%= c.nombre %></a></td>
										<td><%= c.fecha %> a las <%= c.hora %></td>
									</tr>
								<% } %>
							<% } %>
						</tbody>
					</table>
				</div>
					
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