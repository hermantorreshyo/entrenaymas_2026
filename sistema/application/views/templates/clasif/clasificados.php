<script type="text/template" id="clasificados_resultados_template">
<% if (!seleccionar && clasificados.size() == 0 && filter == "") { %>
  <div class="seccion_vacia">
    <h1 class="h1">Todav&iacute;a no ten&eacute;s ning&uacute;n clasificado</h1>
    <h3 class="h3">Para crear tu primer clasificado, hace click en el siguiente bot&oacute;n</h3>
    <div class="list-icon">
      <a href="app/#clasificado"><i class="icon-note"></i></a>
    </div>
    <div>
      <a class="btn btn-lg btn-info btn-addon" href="app/#clasificado">
        <i class="fa fa-plus"></i><span>&nbsp;&nbsp;Nuevo&nbsp;&nbsp;</span>
      </a>
    </div>
    <p>
      Si necesitas ayuda o asesoramiento, no dudes en comunicarte, hace click <a class="text-info">aca</a>.
    </p>
  </div>
<% } else { %>
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <h1 class="m-n font-thin h3">Listado de Clasificados</h1>
  </div>
  <div class="wrapper-md ng-scope">
      <div class="panel panel-default">
          <div class="panel-heading clearfix">
            <div class="row">
              <div class="col-md-6 col-lg-3 sm-m-b">
                <div class="input-group">
                    <input type="text" id="clasificados_buscar" placeholder="Buscar..." autocomplete="off" value="<%= filter %>" class="form-control">
                    <span class="input-group-btn">
                      <button class="btn btn-default"><i class="fa fa-search"></i></button>
                    </span>
                    <span class="input-group-btn">
                      <button class="btn btn-default advanced-search-btn"><i class="fa fa-angle-double-down"></i></button>
                    </span>
                </div>
              </div>
              <% if (!seleccionar) { %>
                <div class="col-md-6 col-lg-offset-3 col-lg-6 text-right">
                  
                  <a class="btn btn-sm btn-success btn-addon mr5" href="app/#clasificado">
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
          <div class="advanced-search-div bg-light dk" style="display:<%= (id_categoria != 0) ? "block":"none" %>">
            <div class="wrapper oh">
              <h4 class="m-t-xs"><i class="fa fa-search"></i> B&uacute;squeda Avanzada:</h4>
              <div class="form-inline">
                <div style="width: 250px; display: inline-block">
                    <select id="clasificados_buscar_categorias" class="w100p"></select>
                </div>
                <div class="form-group">
                  <button id="clasificados_buscar_avanzada_btn" class="btn btn-default"><i class="fa fa-search"></i> Buscar</button>
                </div>
              </div>
            </div>
          </div>
        
          <div class="panel-body">
              <div class="b-a table-responsive">
              <table id="clasificados_tabla" class="table table-small table-striped sortable m-b-none default footable">
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
                      <th class="w50 tac">Imagen</th>
                      <th class="sorting" data-sort-by="titulo">Titulo</th>
                      <th class="sorting" data-sort-by="categoria">Categoria</th>
                      <% if (!seleccionar) { %>
                        <th>Acciones</th>
                      <% } %>
                    </tr>
                  </thead>
                  <tbody class="tbody">
                  <tbody>
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
<% } %>
</script>

<script type="text/template" id="clasificados_item_resultados_template">
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
    <td class="<%= clase %> data">
      <% if (!isEmpty(path)) { %>
        <img src="/sistema/<%= path %>" class="customcomplete-image"/>
      <% } %>
    </td>
    <td class="<%= clase %> data"><%= titulo %></td>
    <td class="<%= clase %> data"><%= categoria %></td>
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


<script type="text/template" id="clasificado_template">
    
<div class="bg-light lter b-b wrapper-md ng-scope">
  <h1 class="m-n font-thin h3">
	<% if (id == undefined) { %>
	    Nuevo Clasificado
	<% } else { %>
	    <%= titulo %>
	<% } %>
  </h1>
</div>

<div class="wrapper-md pb0">
    <div class="tab-container">
        <ul class="nav nav-tabs" role="tablist">
          <li class="active">
              <a href="#tab1" role="tab" data-toggle="tab"><i class="fa fa-info"></i>Informacion</a>
          </li>
          <!--
          <li>
              <a href="#tab4" role="tab" data-toggle="tab"><i class="fa fa-align-justify"></i>Texto Publico</a>
          </li>
          <li>
              <a href="#tab5" role="tab" data-toggle="tab"><i class="fa fa-align-justify"></i>Texto Privado</a>
          </li>
          -->
          <li>
            <a href="#tab2" id="link_tab2" role="tab" data-toggle="tab"><i class="fa fa-map-marker"></i>Ubicaci&oacute;n</a>
          </li>
          <!--
          <li>
              <a href="#tab6" role="tab" data-toggle="tab"><i class="fa fa-picture-o"></i>Imagenes</a>
          </li>
          <li>
              <a href="#tab11" role="tab" data-toggle="tab"><i class="fa fa-globe"></i>SEO</a>
          </li>
          -->
        </ul>
        <div class="tab-content">
            <div id="tab1" class="tab-pane active panel-body">
                        
                <div class="form-horizontal">
                  
                    <div class="form-group">
                        <label class="col-md-2 control-label">Categoria</label>
                        <div class="col-md-10">
                            <select id="clasificado_categorias" class="w100p"></select>
                        </div>
                    </div>
                  
                                    
                    <div class="form-group">
                        <label class="col-md-2 control-label">Nombre</label>
                        <div class="col-md-10">
                            <% if (edicion) { %>
                                <input type="text" required name="titulo" id="clasificado_titulo" value="<%= titulo %>" class="form-control"/>
                            <% } else { %>
                                <span><%= titulo %></span>
                            <% } %>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-md-2 control-label">Direccion</label>
                        <div class="col-md-10">
                            <% if (edicion) { %>
                                <input type="text" name="direccion" id="clasificado_direccion" value="<%= direccion %>" class="form-control"/>
                            <% } else { %>
                                <span><%= direccion %></span>
                            <% } %>
                        </div>
                    </div>
					
                    <div class="form-group">
                        <label class="col-md-2 control-label">Telefono</label>
                        <div class="col-md-10">
                            <% if (edicion) { %>
                                <input type="text" name="telefono" id="clasificado_telefono" value="<%= telefono %>" class="form-control"/>
                            <% } else { %>
                                <span><%= telefono %></span>
                            <% } %>
                        </div>
                    </div>
					
                    <div class="form-group">
                        <label class="col-md-2 control-label">Email</label>
                        <div class="col-md-10">
                            <% if (edicion) { %>
                                <input type="text" name="email" id="clasificado_email" value="<%= email %>" class="form-control"/>
                            <% } else { %>
                                <span><%= email %></span>
                            <% } %>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-md-2 control-label">Facebook</label>
                        <div class="col-md-10">
                            <% if (edicion) { %>
                                <input type="text" name="facebook" id="clasificado_facebook" value="<%= facebook %>" class="form-control"/>
                            <% } else { %>
                                <span><%= facebook %></span>
                            <% } %>
                        </div>
                    </div>                    
                    
                    <!--
                    <div class="form-group">
                        <label class="col-md-2 control-label">Precio</label>
                        <div class="col-md-10">
                            <% if (edicion) { %>
                                <input type="text" name="precio" id="clasificado_precio" value="<%= precio %>" class="form-control"/>
                            <% } else { %>
                                <span><%= precio %></span>
                            <% } %>
                        </div>
                    </div>
                    -->
                                        
                    <div class="form-group">
                        <label class="col-md-2 control-label">Descripcion Breve</label>
                        <div class="col-md-10">
                            <% if (edicion) { %>
                                <textarea id="clasificado_descripcion" placeholder="Aparece en el listado de clasificados" class="form-control" name="descripcion"><%= descripcion %></textarea>
                            <% } else { %>
                                <span><%= descripcion %></span>
                            <% } %>
                        </div>
                    </div>
                    
                    <?php /*
                    <div class="form-group">
                        <label class="col-md-2 control-label">Fecha Publicacion</label>
                        <div class="col-md-10">
                            <% if (edicion) { %>
                                <input type="text" name="fecha" id="clasificado_fecha" value="<%= fecha %>" class="form-control"/>
                            <% } else { %>
                                <span><%= fecha %></span>
                            <% } %>
                        </div>
                    </div>
                    */ ?>
                    
                    <!--
                    <div class="form-group">
                        <label class="col-md-2 control-label">Valido</label>
                        <div class="col-md-10">
                          <div class="form-inline">
                            <input type="text" name="activo_desde" id="clasificado_activo_desde" value="<%= activo_desde %>" class="form-control w150"/>
                            <input type="text" name="activo_hasta" id="clasificado_activo_hasta" value="<%= activo_hasta %>" class="form-control w150"/>
                          </div>
                        </div>
                    </div>
                    -->
                    
                    <?php /*
                    <div class="form-group cb">
                        <label class="col-md-2 control-label">Nuevo </label>
                        <div class="col-md-10">
                            <% if (edicion) { %>
                                <label class="i-switch i-switch-md bg-info m-t-xs m-r">
                                  <input type="checkbox" id="clasificado_nuevo" name="nuevo" class="checkbox" value="1" <%= (nuevo == 1)?"checked":"" %> >
                                  <i></i>
                                </label>
                            <% } else { %>
                                <span><%= ((nuevo==0) ? "No" : "Si") %></span>
                            <% } %>
                        </div>
                    </div>
                    */?>
                    
                    <div class="form-group">
                      <label class="col-md-2 control-label">Publicidad</label>
                      <div class="col-md-10">
                        <select class="w100p" id="clasificado_publicidades"></select>
                      </div>
                    </div>
                    
                    
                    <?php
                    single_upload(array(
                        "name"=>"path",
                        "label"=>"Imagen Principal",
                        "url"=>"/sistema/clasificados/function/save_image/",
                        "width"=>(isset($empresa->config["clasificado_image_width"]) ? $empresa->config["clasificado_image_width"] : 256),
                        "height"=>(isset($empresa->config["clasificado_image_height"]) ? $empresa->config["clasificado_image_height"] : 256),
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
            
            <div id="tab2" class="tab-pane panel-body">
              <div class="form-horizontal">
                
                <div class="col-xs-12">
                  <div class="h4">Mapa</div>
                  <div class="line b-b m-b"></div>
                  <div style="height:400px;" id="mapa"></div>
                  <div class="help-block">Puede arrastrar el marcador del mapa para ponerlo en la direccion exacta.</div>
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
            
            <div id="tab4" class="tab-pane panel-body">
              <div class="form-horizontal">
                <div class="form-group">
                  <div class="col-xs-12">
                    <textarea name="texto" id="clasificado_texto"><%= texto %></textarea>
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
            
            <div id="tab5" class="tab-pane panel-body">
              <div class="form-horizontal">
                <div class="form-group">
                  <div class="col-xs-12">
                    <textarea name="texto" id="clasificado_texto_privado"><%= texto_privado %></textarea>
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
            
            <div id="tab6" class="tab-pane panel-body">
                <div class="form-horizontal">
                  
                    <?php
                    multiple_upload(array(
                      "name"=>"images",
                      "label"=>"Listado de Fotos",
                      "url"=>"clasificados/function/save_image/",
                      "width"=>(isset($empresa->config["clasificado_galeria_image_width"]) ? $empresa->config["clasificado_galeria_image_width"] : 800),
                      "height"=>(isset($empresa->config["clasificado_galeria_image_height"]) ? $empresa->config["clasificado_galeria_image_height"] : 600),
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
            
            <div id="tab11" class="tab-pane panel-body">
				<div class="form-horizontal">
					<div class="form-group">
					  <label class="col-lg-1 control-label">Titulo</label>
					  <div class="col-lg-11">
						<textarea name="seo_title" class="form-control"><%= seo_title %></textarea>
						<span class="help-block m-b-none">Titulo del navegador cuando se visualice la pagina.</span>
					  </div>
					</div>
					<div class="line line-dashed b-b line-lg pull-in"></div>
					<div class="form-group">
					  <label class="col-lg-1 control-label">Descripcion</label>
					  <div class="col-lg-11">
						<textarea name="seo_description" class="form-control"><%= seo_description %></textarea>
						<span class="help-block m-b-none">Escribe una breve descripcion del producto.</span>
					  </div>
					</div>
					<div class="line line-dashed b-b line-lg pull-in"></div>
					<div class="form-group">
					  <label class="col-lg-1 control-label">Palabras Clave</label>
					  <div class="col-lg-11">
						<textarea name="seo_keywords" class="form-control"><%= seo_keywords %></textarea>
						<span class="help-block m-b-none">Escribe las palabras clave que describan el clasificado.</span>
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
