<script type="text/template" id="web_users_resultados_template">
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <h1 class="m-n font-thin h3">Listado de Usuarios</h1>
  </div>
  <div class="wrapper-md ng-scope">
      <div class="panel panel-default">
          <div class="panel-heading clearfix">
            <div class="row">
              <div class="col-md-6 col-lg-3 sm-m-b">
                <div class="input-group">
                    <input type="text" id="web_users_buscar" placeholder="Buscar..." value="<%= filter %>" autocomplete="off" class="form-control">
                    <span class="input-group-btn">
                      <button class="btn btn-default"><i class="fa fa-search"></i></button>
                    </span>
                </div>
              </div>
              <% if (!seleccionar) { %>
                <div class="col-md-6 col-lg-offset-3 col-lg-6 text-right">
                  
                  <a class="btn btn-success btn-addon ml5" href="app/#web_user">
                    <i class="fa fa-plus"></i><span class="hidden-xs">Nuevo</span>
                  </a>
                  
                  <div class="btn-group dropdown ml5">
                    <button class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                      <span>Acciones</span>
                      <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu pull-right">
                      <li><a href="javascript:void" class="eliminar_lote">Eliminar</a></li>
                    </ul>
                  </div>                  
                  
                </div>
              <% } %>
            </div>
          </div>
          <div class="panel-body">
              <div class="b-a table-responsive">
              <table id="web_users_tabla" class="table table-small table-striped sortable m-b-none default footable">
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
                      <th class="w50 tac">Foto</th>
                      <th class="sorting" data-sort-by="nombre">Nombre</th>
                      <th class="sorting" data-sort-by="email">Email</th>
                      <% if (!seleccionar) { %>
                        <th style="width:130px;text-align:right">Acciones</th>
                      <% } %>
                    </tr>
                  </thead>
                  <tbody class="tbody"><tbody>
                  <tfoot class="pagination_container hide-if-no-paging"></tfoot>
                </table>
              </div>
          </div>
          <!--
          <div class="panel-footer clearfix bg-light lter">
            <button class="btn btn-info enviar btn-addon pull-left"><i class="icon fa fa-send"></i>Enviar</button>
          </div>
          -->
      </div>
  </div>
</script>

<script type="text/template" id="web_users_item_resultados_template">
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
    <td class="<%= clase %> data tac">
      <% if (!isEmpty(path)) { %>
        <img src="<%= show_path(path) %>" class="customcomplete-image-xs"/>
      <% } %>
    </td>
    <td class="<%= clase %> data"><%= nombre %></td>
    <td class="<%= clase %> data"><%= email %></td>
    <% if (!seleccionar) { %>
      <td class="tar <%= clase %>">
        <i title="Activo" class="fa-check iconito fa activo <%= (activo == 1)?"active":"" %>"></i>
        <div class="btn-group dropdown">
          <i title="Opciones" class="iconito fa fa-caret-down dropdown-toggle" data-toggle="dropdown"></i>
          <ul class="dropdown-menu pull-right">
            <li><a href="javascript:void(0)" class="eliminar" data-id="<%= id %>">Eliminar</a></li>
          </ul>
        </div>        
      </td>
    <% } %>
</script>


<script type="text/template" id="web_user_template">
    
<div class="bg-light lter b-b wrapper-md ng-scope">
  <h1 class="m-n font-thin h3">
	<% if (id == undefined) { %>
	    Nuevo Usuario
	<% } else { %>
	    <%= nombre %>
	<% } %>
  </h1>
</div>

<div class="wrapper-md pb0">
    <div class="tab-container">
        <ul class="nav nav-tabs" role="tablist">
			<li class="active">
				<a href="#tab1" role="tab" data-toggle="tab"><i class="fa fa-info"></i>Informaci&oacute;n</a>
			</li>
			<li>
				<a href="#tab3" role="tab" data-toggle="tab"><i class="fa fa-comments"></i>Comentarios</a>
			</li>		  
        </ul>
        <div class="tab-content">
            <div id="tab1" class="tab-pane active panel-body">
                        
                <div class="form-horizontal">
                  
                    <div class="form-group">
                        <label class="col-md-2 control-label">Nombre</label>
                        <div class="col-md-10">
                            <% if (edicion) { %>
                                <input type="text" required name="nombre" id="web_user_nombre" value="<%= nombre %>" class="form-control"/>
                            <% } else { %>
                                <span><%= nombre %></span>
                            <% } %>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-md-2 control-label">Email</label>
                        <div class="col-md-10">
                            <% if (edicion) { %>
                                <input type="text" name="email" id="web_user_email" value="<%= email %>" class="form-control"/>
                            <% } else { %>
                                <span><%= email %></span>
                            <% } %>
                        </div>
                    </div>
                    
                    <?php
                    single_upload(array(
                        "name"=>"path",
                        "label"=>"Foto",
                        "url"=>"/sistema/web_users/function/save_image/",
                        "width"=>(isset($empresa->config["web_user_image_width"]) ? $empresa->config["web_user_image_width"] : 256),
                        "height"=>(isset($empresa->config["web_user_image_height"]) ? $empresa->config["web_user_image_height"] : 256),
                    )); ?>
                    
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
            
            <div id="tab3" class="tab-pane panel-body">
              <div class="form-horizontal">
                
                <div class="h4">Comentarios</div>
                <div class="line b-b m-b"></div>
                
				<div class="b-a table-responsive">
					<table id="web_users_tabla" class="table table-striped sortable m-b-none default footable">
						<thead>
							<tr>
								<th>Fecha</th>
								<th>Entrada</th>
								<th>Comentario</th>
								<th>Acciones</th>
							</tr>
						</thead>
						<tbody class="tbody">
							<% if (comentarios.length == 0) { %>
								<tr><td colspan="5">La nota no tiene comentarios.</td></tr>
							<% } else { %>
								<% for (var i=0;i<comentarios.length;i++) { %>
									<% var c = comentarios[i] %>
									<tr>
										<td><%= c.fecha %> a las <%= c.hora %></td>
										<td><a class="text-info" href="app/#entrada/<%= c.id_entrada %>"><%= c.entrada %></a></td>
										<td><%= c.texto %></td>
										<td>
											<i title="Activo" data-id="<%= c.id %>" title="Activo" class="fa-check fa activar_comentario iconito <%= (c.estado == 1)?"active":"" %>"></i>
											<i title="Eliminar" data-id="<%= c.id %>" title="Eliminar" class="fa-remove eliminar_comentario fa iconito"></i>
										</td>
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
