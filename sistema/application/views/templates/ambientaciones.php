<script type="text/template" id="ambientaciones_resultados_template">
  <div class="seccion_llena">
    <% if (!seleccionar) { %>
      <div class="bg-light lter b-b wrapper-md ng-scope">
        <h1 class="m-n font-thin h3"><i class="fa fa-tags icono_principal"></i> Ambientaciones</h1>
      </div>
    <% } %>
    <div class="<%= (!seleccionar)?'wrapper-md':''%> ng-scope">
        <div class="panel panel-default">
            <div class="panel-heading clearfix">
              <div class="row">
                <div class="<% if (!seleccionar) { %>col-md-6 col-lg-3 <% } else { %> col-xs-12 <% } %> sm-m-b">
                  <div class="input-group">
                      <input type="text" id="ambientaciones_buscar" placeholder="Buscar..." autocomplete="off" class="form-control">
                      <span class="input-group-btn">
                        <button class="btn btn-default"><i class="fa fa-search"></i></button>
                      </span>
                  </div>
                </div>
                <% if (!seleccionar) { %>
                  <div class="col-md-6 col-lg-offset-3 col-lg-6 text-right">
                    <a class="btn btn-info btn-addon ml5" href="app/#ambientacion">
                      <i class="fa fa-plus"></i><span>&nbsp;&nbsp;Nuevo&nbsp;&nbsp;</span>
                    </a>
                  </div>
                <% } %>
              </div>
            </div>
            <div class="panel-body">
                <div class="b-a table-responsive">
                <table id="ambientaciones_tabla" class="table <%= (seleccionar)?'table-small':'' %> table-striped sortable m-b-none default footable">
                    <thead>
                      <tr>
                        <th style="width:20px;">
                          <% if (!seleccionar) { %>
                            <label class="i-checks m-b-none">
                              <input class="esc sel_todos" type="checkbox"><i></i>
                            </label>
                          <% } %>
                        </th>
                        <th class="w50 tac hidden-xs"></th>
                        <th data-sort-by="nombre">Nombre</th>
                        <th class="hidden-xs" data-sort-by="categoria">Categor&iacute;a</th>
                        <th class="w70 hidden-xs">Orden</th>
                        <% if (!seleccionar) { %>
                          <th class="th_acciones w120">Acciones</th>
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
  </div>
<?php /* <% } %> */ ?>
</script>

<script type="text/template" id="ambientaciones_item_resultados_template">
    <% var clase = (activo==1)?"":"text-muted"; %>
    <% if (seleccionar) { %>
      <td>
          <label class="i-checks m-b-none">
              <input class="radio esc" value="<%= id %>" name="radio" type="radio"><i></i>
          </label>
      </td>
    <% } else { %>
      <td>
          <label class="i-checks m-b-none">
              <input class="esc check-row" value="<%= id %>" type="checkbox"><i></i>
          </label>
      </td>
      <td class="<%= clase %> data hidden-xs">
        <img src="/sistema/<%= path %>" class="customcomplete-image"/>
      </td>
    <% } %>    
    <td class="<%= clase %> data">
      <span class="text-info nombre"><%= nombre %></span>
    </td>
    <td class="<%= clase %> data"><%= categoria %></td>
    <td class="<%= clase %>">
      <input type="numer" min="0" class="ordenador form-control no-model" value="<%= orden %>">
    </td>
    <% if (!seleccionar) { %>
      <td class="p5 <%= clase %> td_acciones">
        <i title="Activo" class="fa-check iconito fa activo <%= (activo == 1)?"active":"" %>"></i>
        <i title="Destacado" class="fa fa-star iconito destacado <%= (destacado == 1)?"active":"" %>"></i>
        <div class="btn-group dropdown ml10">
          <button class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fa fa-plus"></i>
          </button>        
          <ul class="dropdown-menu pull-right">
            <li><a href="javascript:void(0)" class="duplicar" data-id="<%= id %>">Duplicar</a></li>
            <li><a href="javascript:void(0)" class="eliminar" data-id="<%= id %>">Eliminar</a></li>
          </ul>
        </div>    
      </td>
    <% } %>
</script>


<script type="text/template" id="ambientacion_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <h1 class="m-n font-thin h3"><i class="fa fa-tags icono_principal"></i>Ambientaciones / 
    <b><%= (id == undefined)?"Nuevo":nombre %></b>
  </h1>
</div>
<div class="wrapper-md">
  <div class="centrado rform">
    <div class="row">
      <div class="col-md-4">
        <div class="detalle_texto">Informaci&oacute;n general</div>
      </div>
      <div class="col-md-8">
        <div class="panel panel-default">
          <div class="panel-body">
            <div class="padder">
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">T&iacute;tulo</label>
                    <input type="text" required name="nombre" id="ambientacion_nombre" value="<%= nombre %>" class="form-control"/>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Categoria</label>
                    <select id="ambientacion_categoria" class="form-control">
                      <option <%= (categoria=="Ba&ntilde;o")?"selected":"" %>>Ba&ntilde;o</option>
                      <option <%= (categoria=="Cocina")?"selected":"" %>>Cocina</option>
                    </select>
                  </div>
                </div>
              </div>
              <?php
              single_upload(array(
                  "name"=>"path",
                  "label"=>"Imagen Principal",
                  "url"=>"/sistema/ambientaciones/function/save_image/",
                  "width"=>(isset($empresa->config["ambientaciones_image_width"]) ? $empresa->config["ambientaciones_image_width"] : 256),
                  "height"=>(isset($empresa->config["ambientaciones_image_height"]) ? $empresa->config["ambientaciones_image_height"] : 256),
              )); ?>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="line b-b m-b-lg"></div>

    <div class="row">
      <div class="col-md-4">
        <div class="detalle_texto">Descripci&oacute;n</div>
      </div>
      <div class="col-md-8">
        <div class="panel panel-default">
          <div class="panel-body">
            <div class="padder">
              <div class="form-group">
                <textarea name="texto" id="ambientacion_texto"><%= texto %></textarea>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="line b-b m-b-lg"></div>

    <div class="row">
      <div class="col-md-4">
        <div class="detalle_texto">Multimedia</div>
      </div>
      <div class="col-md-8">
        <div class="panel panel-default">
          <div class="panel-body">
            <div class="padder">
              <?php
              multiple_upload(array(
                "name"=>"images",
                "label"=>"Galeria de fotos",
                "url"=>"ambientaciones/function/save_image/",
                "width"=>(isset($empresa->config["ambientaciones_galeria_image_width"]) ? $empresa->config["ambientaciones_galeria_image_width"] : 881),
                "height"=>(isset($empresa->config["ambientaciones_galeria_image_height"]) ? $empresa->config["ambientaciones_galeria_image_height"] : 462),
              )); ?>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="line b-b m-b-lg"></div>

    <div class="row">
      <div class="col-md-4">
        <div class="detalle_texto">Productos</div>
        <div class="text-muted">Art&iacute;culos que pertenecen a la ambientacion.</div>
      </div>
      <div class="col-md-8">
        <div class="panel panel-default">
          <div class="panel-body">
            <div class="padder">

              <div class="form-group">
                <label class="control-label">Productos para consultar</label>
                <select multiple id="ambientacion_caracteristicas" style="width: 100%">
                  <% if (!isEmpty(caracteristicas)) { %>
                    <% var carac = caracteristicas.split(";;;") %>
                    <% for (var i=0; i< carac.length; i++) { %>
                      <% var o = carac[i] %>
                      <option selected><%= o %></option>
                    <% } %>
                  <% } %>
                </select>
                <div class="text-muted fs14">Nota: Escribe un producto y presiona Enter para ingresarlo.</div>
              </div>

              <div class="form-group">
                <label class="control-label">Productos para comprar</label>
                <input class="form-control" type="text" id="ambientaciones_buscar_productos" placeholder="Busque los productos especificos con los que desea relacionar..." />
              </div>
              <div class="b-a table-responsive">
                <ul id="ambientaciones_tabla_articulos" style="height: 384px; overflow-y: auto;" class="list-group gutter list-group-lg list-group-sp">
                  <% for(var i=0;i< articulos.length;i++) { %>
                    <% var a = articulos[i]; %>
                    <li class='list-group-item'>
                      <span><i class='fa fa-sort text-muted fa m-r-sm'></i> </span>
                      <img style='margin-left: 10px; margin-right:10px; max-height:50px' src='/sistema/<%= a.path %>'/>
                      <span class='id dn'><%= a.id %></span>
                      <span class='nombre'><%= a.nombre %></span>
                      <span class='pull-right m-t eliminar_relacionado'><i class='fa fa-fw fa-times'></i> </span>
                    </li>
                  <% } %>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="line b-b m-b-lg"></div>

    <div class="row">
      <div class="col-md-4">
        <div class="detalle_texto">SEO</div>
      </div>
      <div class="col-md-8">
        <div class="panel panel-default">
          <div class="panel-body">
            <div class="padder">
              <div class="form-group">
                <label class="control-label">Titulo</label>
                <textarea placeholder="Titulo del navegador cuando se visualice la pagina" id="entrada_seo_title" class="form-control no-model"><%= seo_title %></textarea>
              </div>
              <div class="form-group">
                <label class="control-label">Descripcion</label>
                <textarea id="entrada_seo_description" placeholder="Escribe una breve descripcion de la ambientacion" class="form-control no-model"><%= seo_description %></textarea>
              </div>
              <div class="form-group">
                <label class="control-label">Palabras Clave</label>
                <textarea id="entrada_seo_keywords" placeholder="Escribe las palabras clave que describan la ambientacion" class="form-control no-model"><%= seo_keywords %></textarea>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="line b-b m-b-lg"></div>
        
    <div class="row">
      <div class="col-md-4"></div>
      <div class="col-md-8">
        <button class="btn guardar btn-success">Guardar</button>
      </div>
    </div>
  </div>
</div>
</script>