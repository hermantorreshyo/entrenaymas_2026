<script type="text/template" id="peliculas_resultados_template">
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <h1 class="m-n font-thin h3">Peliculas</h1>
  </div>
  <div class="wrapper-md ng-scope">
      <div class="panel panel-default">
          <div class="panel-heading clearfix">
            <div class="row">
              <div class="col-md-6 col-lg-3 sm-m-b">
                <div class="input-group">
                    <input type="text" id="peliculas_buscar" placeholder="Buscar..." autocomplete="off" class="form-control">
                    <span class="input-group-btn">
                      <button class="btn btn-default"><i class="fa fa-search"></i></button>
                    </span>
                </div>
              </div>
              <% if (!seleccionar) { %>
                <div class="col-md-6 col-lg-offset-3 col-lg-6 text-right">
                  <a class="btn btn-success btn-addon ml5" href="app/#pelicula">
                    <i class="fa fa-plus"></i><span class="hidden-xs">Nueva</span>
                  </a>
                </div>
              <% } %>
            </div>
          </div>        
          <div class="panel-body">
              <div class="b-a table-responsive">
              <table id="peliculas_tabla" class="table table-striped sortable m-b-none default footable">
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

<script type="text/template" id="peliculas_item_resultados_template">
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
    <td class="<%= clase %>"><a class="text-info" href="http://<%= DOMINIO+link %>" target="_blank"><%= "http://"+DOMINIO+link %></a></td>
    <% if (!seleccionar) { %>
      <td class="w25 p5"><i title="Editar" class="fa fa-file-text-o edit data text-dark" data-id="<%= id %>" /></td>
      <td class="w25 p5"><i title="Duplicar" class="fa fa-copy duplicar text-dark" data-id="<%= id %>" /></td>
      <td class="w25 p5"><i title="Eliminar" class="glyphicon glyphicon-remove eliminar text-danger" data-id="<%= id %>" /></td>
    <% } %>
</script>


<script type="text/template" id="pelicula_template">
    
<div class="bg-light lter b-b wrapper-md ng-scope">
  <div class="clearfix">
    <div class="pull-left">
      <h1 class="m-n font-thin h3">
      <% if (id == undefined) { %>
          Nueva Pelicula
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
        </ul>
        <div class="tab-content">
            <div id="tab1" class="tab-pane active panel-body">
                        
                <div class="form-horizontal">
                
                    <div class="form-group">
                        <label class="col-md-2 control-label">Titulo</label>
                        <div class="col-md-10">
                            <% if (edicion) { %>
                                <input type="text" name="nombre" id="pelicula_nombre" value="<%= nombre %>" class="form-control"/>
                            <% } else { %>
                                <span><%= nombre %></span>
                            <% } %>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-2 control-label">Lugar</label>
                        <div class="col-md-10">
                            <% if (edicion) { %>
                                <input type="text" name="lugar" id="pelicula_lugar" value="<%= lugar %>" class="form-control"/>
                            <% } else { %>
                                <span><%= lugar %></span>
                            <% } %>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-2 control-label">Fecha evento</label>
                        <div class="col-md-10">
                            <% if (edicion) { %>
                                <input type="text" name="fecha_evento" id="pelicula_fecha_evento" value="<%= fecha_evento %>" class="form-control"/>
                            <% } else { %>
                                <span><%= fecha_evento %></span>
                            <% } %>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-2 control-label">Genero</label>
                        <div class="col-md-10">
                            <% if (edicion) { %>
                              <select class="form-control" id="pelicula_genero">
                                <option value="Accion" <%= (genero=="Accion")?"selected":"" %>>Accion</option>
                                <option value="Amor" <%= (genero=="Amor")?"selected":"" %>>Amor</option>
                                <option value="Aventura" <%= (genero=="Aventura")?"selected":"" %>>Aventura</option>
                                <option value="Ciencia Ficcion" <%= (genero=="Ciencia Ficcion")?"selected":"" %>>Ciencia Ficcion</option>
                                <option value="Comedia" <%= (genero=="Comedia")?"selected":"" %>>Comedia</option>
                                <option value="Drama" <%= (genero=="Drama")?"selected":"" %>>Drama</option>
                                <option value="Fantasia" <%= (genero=="Fantasia")?"selected":"" %>>Fantasia</option>
                                <option value="Infantil" <%= (genero=="Infantil")?"selected":"" %>>Infantil</option>
                                <option value="Policial" <%= (genero=="Policial")?"selected":"" %>>Policial</option>
                                <option value="Porno" <%= (genero=="Porno")?"selected":"" %>>Porno</option>
                                <option value="Suspenso" <%= (genero=="Suspenso")?"selected":"" %>>Suspenso</option>
                                <option value="Terror" <%= (genero=="Terror")?"selected":"" %>>Terror</option>
                                <option value="Thriller" <%= (genero=="Thriller")?"selected":"" %>>Thriller</option>
                              </select>
                            <% } else { %>
                                <span><%= genero %></span>
                            <% } %>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-md-2 control-label">Edad</label>
                        <div class="col-md-10">
                            <% if (edicion) { %>
                              <select class="form-control" id="pelicula_edad">
                                <option value="ATP" <%= (edad=="ATP")?"selected":"" %>>ATP</option>
                                <option value="+13" <%= (edad=="+13")?"selected":"" %>>+13</option>
                                <option value="+16" <%= (edad=="+16")?"selected":"" %>>+16</option>
                                <option value="+18" <%= (edad=="+18")?"selected":"" %>>+18</option>
                              </select>
                            <% } else { %>
                                <span><%= edad %></span>
                            <% } %>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-md-2 control-label">Valido</label>
                        <div class="col-md-10">
                          <div class="form-inline">
                            <input type="text" name="valido_desde" id="pelicula_valido_desde" value="<%= valido_desde %>" class="form-control w150"/>
                            <input type="text" name="valido_hasta" id="pelicula_valido_hasta" value="<%= valido_hasta %>" class="form-control w150"/>
                          </div>
                        </div>
                    </div>                    
                    
                    <div class="form-group">
                        <label class="col-md-2 control-label">Descripcion Breve</label>
                        <div class="col-md-10">
                            <% if (edicion) { %>
                                <textarea class="form-control h80" id="pelicula_descripcion" name="descripcion"><%= descripcion %></textarea>
                            <% } else { %>
                                <span><%= descripcion %></span>
                            <% } %>
                        </div>
                    </div>                    
                    
                    <?php
                    single_upload(array(
                        "name"=>"path",
                        "label"=>"Imagen",
                        "url"=>"/sistema/peliculas/function/save_image/",
                        "width"=>(isset($empresa->config["pelicula_image_width"]) ? $empresa->config["pelicula_image_width"] : 256),
                        "height"=>(isset($empresa->config["pelicula_image_height"]) ? $empresa->config["pelicula_image_height"] : 256),
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
            
            <div id="tab4" class="tab-pane panel-body">
              <div class="form-horizontal">
                <div class="form-group">
                  <div class="col-xs-12">
                    <textarea name="texto" id="pelicula_texto"><%= texto %></textarea>
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