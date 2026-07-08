<script type="text/template" id="encuestas_resultados_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <h1 class="m-n font-thin h3">Listado de Encuestas</h1>
</div>
<div class="wrapper-md ng-scope">
    <div class="panel panel-default">
        <div class="panel-heading clearfix">
          <div class="row">
            <div class="col-md-6 col-lg-3 sm-m-b">
              <div class="input-group">
                  <input type="text" id="encuestas_buscar" placeholder="Buscar..." autocomplete="off" class="form-control">
                  <span class="input-group-btn">
                    <button class="btn btn-default"><i class="fa fa-search"></i></button>
                  </span>
              </div>
            </div>
            <% if (!seleccionar) { %>
              <div class="col-md-6 col-lg-offset-3 col-lg-6 text-right">
                <a class="btn btn-success btn-addon ml5" href="app/#encuesta">
                  <i class="fa fa-plus"></i><span class="hidden-xs">Nueva</span>
                </a>
              </div>
            <% } %>
          </div>
        </div>
        <div class="panel-body">
            <div class="b-a table-responsive">
            <table id="encuestas_tabla" class="table table-striped sortable m-b-none default footable">
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
                      <th style="width: 70px;">Acciones</th>
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

<script type="text/template" id="encuestas_item_resultados_template">
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


<script type="text/template" id="encuesta_template">
    
<div class="bg-light lter b-b wrapper-md ng-scope">
  <h1 class="m-n font-thin h3">
	<% if (id == undefined) { %>
	    Nueva Encuesta
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
          <% if (id != undefined) { %>
            <li>
              <a href="#tab2" role="tab" data-toggle="tab"><i class="fa fa-info"></i>Votos</a>
            </li>
          <% } %>
        </ul>
        <div class="tab-content">
            <div id="tab1" class="tab-pane active panel-body">
                        
                <div class="form-horizontal">
                
                    <div class="form-group">
                        <label class="col-md-2 control-label">Titulo</label>
                        <div class="col-md-10">
                            <% if (edicion) { %>
                                <input type="text" required name="titulo" id="encuesta_titulo" value="<%= titulo %>" class="form-control"/>
                            <% } else { %>
                                <span><%= titulo %></span>
                            <% } %>
                        </div>
                    </div>					
                    
                    <div class="form-group">
                        <label class="col-md-2 control-label">V&aacute;lida</label>
                        <div class="col-md-10">
                          <div class="form-inline">
                            <input type="text" name="valida_desde" id="encuesta_valida_desde" value="<%= valida_desde %>" class="form-control w150"/>
                            <input type="text" name="valida_hasta" id="encuesta_valida_hasta" value="<%= valida_hasta %>" class="form-control w150"/>
                          </div>
                        </div>
                    </div>
					
                    <div class="form-group">
                        <label class="col-md-2 control-label">Forma participaci&oacute;n</label>
                        <div class="col-md-10">
						  <select class="form-control" id="encuesta_forma_participacion" name="forma_participacion">
							<option value="facebook" <%= (forma_participacion == "facebook")?"selected":"" %>>Login con facebook</option>
							<option value="libre" <%= (forma_participacion == "libre")?"selected":"" %>>Libre</option>
						  </select>
                        </div>
                    </div>					
					
                    <div class="form-group">
                        <label class="col-md-2 control-label">Opciones</label>
                        <div class="col-md-10">
                          <div>
							<div class="input-group">
								<input type="text" id="encuesta_opcion" placeholder="Escribe aqui una opcion para la encuesta..." autocomplete="off" class="form-control">
								<span class="input-group-btn">
								  <button class="btn btn-default" id="encuesta_opcion_mas"><i class="fa fa-plus"></i></button>
								</span>
							</div>							
						  </div>
						  <div class="b-a m-t">
							<table id="encuesta_opciones_tabla" class="table table-small m-b-none">
							  <thead>
								<tr>
								  <th>Titulo</th>
                  <th>Votos</th>
								  <th style="width:150px;" class="tar">Acciones</th>
								</tr>
							  </thead>
							  <tbody>
                <% var total_votos = 0 %>
                <% for(var i=0;i< opciones.length;i++) { %>
                  <% var o = opciones[i] %>
                  <% total_votos += parseInt(o.votos) %>
                <% } %>

								<% for(var i=0;i< opciones.length;i++) { %>
								  <% var o = opciones[i] %>
								  <tr id="<%= o.id %>">
                    <td class="nombre"><%= o.nombre %></td>
                    <td class="votos">
                      <b><%= o.votos %> votos</b>
                      (<%= (total_votos == 0) ? "0.00%" : Number(o.votos/total_votos*100).toFixed(2)+"%" %>)
                    </td>
  									<td class="tar">
  									  <i class="fa fa-edit iconito"></i>
  									  <i class="fa fa-remove iconito"></i>
  									</td>
								  </tr>
								<% } %>
							  </tbody>
							</table>
						  </div>
                        </div>
                    </div>
                    
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

            <% if (id != undefined) { %>
              <div id="tab2" class="tab-pane panel-body">
                <div class="form-horizontal">

                <% for(var i=0;i< opciones.length;i++) { %>
                  <% var o = opciones[i] %>
                  <div style="margin-bottom: 40px">
                    <h3><%= o.nombre %></h3>
                    <hr>
                    <table class="table table-xs small">
                      <% for(var j=0;j< o.usuarios.length;j++) { %>
                        <% var u = o.usuarios[j] %>
                        <tr>
                          <td><img src="<%= u.path %>" class="thumb thumb-xs" /></td>
                          <td><%= u.nombre %></td>
                          <td><%= u.fecha %></td>
                        </tr>
                      <% } %>
                    </table>
                  </div>
                <% } %>

                </div>
              </div>
            <% } %>


        </div>
    </div>
</div>
</script>