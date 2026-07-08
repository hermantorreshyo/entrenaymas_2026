<script type="text/template" id="landing_pages_resultados_template">
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <h1 class="m-n font-thin h3">Landing Pages</h1>
  </div>
  <div class="wrapper-md ng-scope">
      <div class="panel panel-default">
          <div class="panel-heading clearfix">
            <div class="row">
              <div class="col-md-6 col-lg-3 sm-m-b">
                <div class="input-group">
                    <input type="text" id="landing_pages_buscar" placeholder="Buscar..." autocomplete="off" class="form-control">
                    <span class="input-group-btn">
                      <button class="btn btn-default"><i class="fa fa-search"></i></button>
                    </span>
                </div>
              </div>
              <% if (!seleccionar) { %>
                <div class="col-md-6 col-lg-offset-3 col-lg-6 text-right">
                  <a class="btn btn-success btn-addon ml5" href="app/#landing_page">
                    <i class="fa fa-plus"></i><span class="hidden-xs">Nueva</span>
                  </a>
                </div>
              <% } %>
            </div>
          </div>        
          <div class="panel-body">
              <div class="b-a table-responsive">
              <table id="landing_pages_tabla" class="table table-striped sortable m-b-none default footable">
                  <thead>
                    <tr>
                      <% if (!seleccionar) { %>
                        <th style="width:20px;">
                            <label class="i-checks m-b-none">
                                <input class="esc sel_todos" type="checkbox"><i></i>
                            </label>
                        </th>
                        <th style="width: 10px"></th>
                      <% } else { %>
                        <th style="width:20px;"></th>
                      <% } %>
                      <th>Nombre</th>
                      <th>Link</th>
                      <% if (!seleccionar) { %>
                        <th style="width:10px;"></th>
                        <th style="width:10px;"></th>
                        <th style="width:10px;"></th>
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

<script type="text/template" id="landing_pages_item_resultados_template">
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
      <td class="p5 <%= clase %> data tac"><i title="Activo" class="glyphicon glyphicon-ok activo <%= (activo == 1)?"text-success":"text-muted" %>"></i></td>
    <% } %>
    <td class="<%= clase %> data"><%= nombre %></td>
    <td class="<%= clase %>"><a class="text-info" href="http://<%= DOMINIO+"/"+link_landing %>" target="_blank"><%= "http://"+DOMINIO+"/"+link_landing %></a></td>
    <% if (!seleccionar) { %>
      <td class="w25 p5"><i title="Editar" class="fa fa-file-text-o edit data text-dark" data-id="<%= id %>" /></td>
      <td class="w25 p5"><i title="Duplicar" class="fa fa-copy duplicar text-dark" data-id="<%= id %>" /></td>
      <td class="w25 p5"><i title="Eliminar" class="glyphicon glyphicon-remove eliminar text-danger" data-id="<%= id %>" /></td>
    <% } %>
</script>


<script type="text/template" id="landing_page_template">
    
<div class="bg-light lter b-b wrapper-md ng-scope">
  <div class="clearfix">
    <div class="pull-left">
      <h1 class="m-n font-thin h3">
      <% if (id == undefined) { %>
          Nueva Landing Page
      <% } else { %>
          <%= nombre %>
      <% } %>
      </h1>      
    </div>
    <div class="pull-right">
      <div class="btn-group dropdown">
        <button class="btn btn-default dropdown-toggle btn-addon" data-toggle="dropdown">
          <i class="glyphicon glyphicon-import"></i><span class="hidden-xs">Importar</span>
          <span class="caret"></span>
        </button>
        <ul class="dropdown-menu">
          <% if (control.check("articulos")>0) { %>
            <li><a href="javascript:void(0);" class="importar_articulos">Articulos</a></li>
          <% } %>
          <% if (control.check("propiedades")>0) { %>
            <li><a href="javascript:void(0);" class="importar_propiedades">Propiedades</a></li>
          <% } %>
        </ul>
      </div>
    </div>
  </div>
</div>

<div class="wrapper-md pb0">
    <div class="tab-container">
        <ul class="nav nav-tabs" role="tablist">
          <li class="active">
            <a href="#tab1" role="tab" data-toggle="tab"><i class="fa fa-info"></i>Informaci&oacute;n</a>
          </li>
          <li>
            <a href="#tab4" role="tab" data-toggle="tab"><i class="fa fa-align-justify"></i>Texto</a>
          </li>
          <% if (control.check("articulos")>0) { %>
            <li>
              <a href="#tab2" role="tab" data-toggle="tab"><i class="fa fa-cubes"></i>Art&iacute;culo</a>
            </li>
          <% } %>
          <li>
            <a href="#tab6" role="tab" data-toggle="tab"><i class="fa fa-picture-o"></i>Im&aacute;genes</a>
          </li>
          <li>
            <a href="#tab7" role="tab" data-toggle="tab"><i class="fa fa-cog"></i>Configuraci&oacute;n</a>
          </li>
        </ul>
        <div class="tab-content">
            <div id="tab1" class="tab-pane active panel-body">
                        
                <div class="form-horizontal">
                
                    <div class="form-group">
                        <label class="col-md-2 control-label">Titulo</label>
                        <div class="col-md-10">
                            <% if (edicion) { %>
                                <input type="text" name="nombre" id="landing_page_nombre" value="<%= nombre %>" class="form-control"/>
                            <% } else { %>
                                <span><%= nombre %></span>
                            <% } %>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-2 control-label">Subtitulo</label>
                        <div class="col-md-10">
                            <% if (edicion) { %>
                                <input type="text" name="subtitulo" id="landing_page_subtitulo" value="<%= subtitulo %>" class="form-control"/>
                            <% } else { %>
                                <span><%= subtitulo %></span>
                            <% } %>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-md-2 control-label">Descripcion Breve</label>
                        <div class="col-md-10">
                            <% if (edicion) { %>
                                <textarea class="form-control h80" id="landing_page_breve" name="breve"><%= breve %></textarea>
                            <% } else { %>
                                <span><%= breve %></span>
                            <% } %>
                        </div>
                    </div>                    
                    
                    <?php
                    single_upload(array(
                        "name"=>"path",
                        "label"=>"Imagen",
                        "url"=>"/sistema/landing_pages/function/save_image/",
                        "width"=>(isset($empresa->config["landing_page_image_width"]) ? $empresa->config["landing_page_image_width"] : 256),
                        "height"=>(isset($empresa->config["landing_page_image_height"]) ? $empresa->config["landing_page_image_height"] : 256),
                    )); ?>
                    
                    <div class="form-group">
                        <label class="col-md-2 control-label">Link</label>
                        <div class="col-md-10">
                            <% if (edicion) { %>
                                <input type="text" name="link" id="landing_page_link" value="<%= link %>" class="form-control"/>
                            <% } else { %>
                                <span><%= link %></span>
                            <% } %>
                        </div>
                    </div>                     
                    
                    <div class="form-group cb">
                        <label class="col-md-2 control-label">Activa </label>
                        <div class="col-md-10">
                            <% if (edicion) { %>
                                <label class="i-switch i-switch-md bg-info m-t-xs m-r">
                                  <input type="checkbox" id="landing_page_activo" name="activo" class="checkbox" value="1" <%= (activo == 1)?"checked":"" %> >
                                  <i></i>
                                </label>
                            <% } else { %>
                                <span><%= ((activo==0) ? "No" : "Si") %></span>
                            <% } %>
                        </div>
                    </div>

                    <div class="form-group cb">
                        <label class="col-md-2 control-label">Nuevo </label>
                        <div class="col-md-10">
                            <% if (edicion) { %>
                                <label class="i-switch i-switch-md bg-info m-t-xs m-r">
                                  <input type="checkbox" id="landing_page_nuevo" name="nuevo" class="checkbox" value="1" <%= (nuevo == 1)?"checked":"" %> >
                                  <i></i>
                                </label>
                            <% } else { %>
                                <span><%= ((nuevo==0) ? "No" : "Si") %></span>
                            <% } %>
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
            
            <div id="tab4" class="tab-pane panel-body">
              <div class="form-horizontal">
                <div class="form-group">
                  <div class="col-xs-12">
                    <textarea name="texto" id="landing_page_texto"><%= texto %></textarea>
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
                  
                    <div class="form-group">
                        <label class="col-md-2 control-label">Precio Final</label>
                        <div class="col-md-10">
                            <% if (edicion) { %>
                                <input type="text" name="precio_final" id="landing_page_precio_final" value="<%= precio_final %>" class="form-control"/>
                            <% } else { %>
                                <span><%= precio_final %></span>
                            <% } %>
                        </div>
                    </div>                  
                    <div class="form-group">
                        <label class="col-md-2 control-label">Porc. Descuento</label>
                        <div class="col-md-10">
                            <% if (edicion) { %>
                                <input type="text" name="porc_dto" id="landing_page_porc_dto" value="<%= porc_dto %>" class="form-control"/>
                            <% } else { %>
                                <span><%= porc_dto %></span>
                            <% } %>
                        </div>
                    </div>                  
                    <div class="form-group">
                        <label class="col-md-2 control-label">Precio Final c/dto</label>
                        <div class="col-md-10">
                            <% if (edicion) { %>
                                <input type="text" name="precio_final_dto" id="landing_page_precio_final_dto" value="<%= precio_final_dto %>" class="form-control"/>
                            <% } else { %>
                                <span><%= precio_final_dto %></span>
                            <% } %>
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
            
            
            <div id="tab6" class="tab-pane panel-body">
                <div class="form-horizontal">
                  
                    <?php
                    multiple_upload(array(
                      "name"=>"images",
                      "label"=>"Listado de Fotos",
                      "url"=>"landing_pages/function/save_image/",
                      "width"=>(isset($empresa->config["landing_page_galeria_image_width"]) ? $empresa->config["landing_page_galeria_image_width"] : 800),
                      "height"=>(isset($empresa->config["landing_page_galeria_image_height"]) ? $empresa->config["landing_page_galeria_image_height"] : 600),
                    )); ?>
                    
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
            
            <div id="tab7" class="tab-pane panel-body">
                <div class="form-horizontal">
                  
                    <div class="form-group cb">
                        <label class="col-md-2 control-label">Mostrar Form. Contacto </label>
                        <div class="col-md-10">
                            <% if (edicion) { %>
                                <label class="i-switch i-switch-md bg-info m-t-xs m-r">
                                  <input type="checkbox" id="landing_page_mostrar_form_contacto" name="mostrar_form_contacto" class="checkbox" value="1" <%= (mostrar_form_contacto == 1)?"checked":"" %> >
                                  <i></i>
                                </label>
                            <% } else { %>
                                <span><%= ((mostrar_form_contacto==0) ? "No" : "Si") %></span>
                            <% } %>
                        </div>
                    </div>                  
                  
                    <div class="form-group">
                        <label class="col-md-2 control-label">Email contacto</label>
                        <div class="col-md-10">
                            <% if (edicion) { %>
                                <input type="text" name="email_form_contacto" id="landing_page_email_form_contacto" value="<%= email_form_contacto %>" class="form-control"/>
                            <% } else { %>
                                <span><%= email_form_contacto %></span>
                            <% } %>
                        </div>
                    </div>                     
                    
                    <div class="line line-dashed b-b line-lg pull-in"></div>
                  
                    <div class="form-group">
                      <label class="col-md-2 control-label">C&oacute;digos de Seguimiento</label>
                      <div class="col-md-10">
                      <textarea name="codigos" class="form-control"><%= codigos %></textarea>
                      <span class="help-block m-b-none">Pegue aqui los codigos de seguimiento de Analytics, Facebook, etc.</span>
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
            
            </div>
        
             
        

        </div>
    </div>
</div>
     
</script>





<script type="text/template" id="landing_pages_impresiones_resultados_template">
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <h1 class="m-n font-thin h3">Estadisticas de Landing Pages</h1>
  </div>
  <div class="wrapper-md ng-scope">
      <div class="panel panel-default">
          <div class="panel-heading clearfix">
            <div class="row">
              <div class="col-md-6 col-lg-3 sm-m-b">
                <div class="input-group">
                  <span class="input-group-btn">
                    <button class="btn btn-default"><i class="fa fa-search"></i></button>
                  </span>
                </div>
              </div>
            </div>
          </div>
          <div class="panel-body">
              <div class="b-a table-responsive">
              <table id="landing_pages_impresiones_tabla" class="table table-striped sortable m-b-none default footable">
                  <thead>
                    <tr>
                      <th class="sorting">Nombre</th>
                      <th class="sorting">Categoria</th>
                      <th class="sorting">Impresa</th>
                      <th class="sorting">Prom./dia</th>
                      <th class="sorting">Clicks</th>
                      <th class="sorting">Contactos</th>
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

<script type="text/template" id="landing_pages_impresiones_item_resultados_template">
    <% var clase = (activo==1)?"":"text-muted"; %>
    <td class="<%= clase %> data"><%= nombre %></td>
    <td class="<%= clase %> data"><%= impresiones %></td>
    <td class="<%= clase %> data"><%= promedio_impresiones_dia %></td>
    <td class="<%= clase %> data"><%= clicks %></td>
    <td class="<%= clase %> data"><%= contactos %></td>
</script>