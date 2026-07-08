<script type="text/template" id="clasificados_objetos_resultados_template">
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <h1 class="m-n font-thin h3">Listado de Clasificados</h1>
  </div>
  <div class="wrapper-md ng-scope">
      <div class="panel panel-default">
          <div class="panel-heading clearfix">
            <div class="row">
              <div class="col-md-6 col-lg-3 sm-m-b">
                <div class="input-group">
                    <input type="text" id="objetos_buscar" placeholder="Buscar..." autocomplete="off" class="form-control">
                    <span class="input-group-btn">
                      <button class="btn btn-default"><i class="fa fa-search"></i></button>
                    </span>
                </div>
              </div>
              <% if (!seleccionar) { %>
                <div class="col-md-6 col-lg-offset-3 col-lg-6 text-right">

                  <a class="btn btn-success btn-addon ml5" href="app/#clasificado_objeto">
                    <i class="fa fa-plus"></i><span class="hidden-xs">Nuevo</span>
                  </a>
                </div>
              <% } %>
            </div>
          </div>
          <div class="panel-body">
              <div class="b-a table-responsive">
              <table id="clasificados_objetos_tabla" class="table table-striped sortable m-b-none default footable">
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
                      <th>Nombre</th>
                      <th class="w150 sorting" data-sort-by="precio_final">Precio</th>
                      <th class="w150">Consultas</th>
                      <% if (!seleccionar) { %>
                        <th style="width:100px;"></th>
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

<script type="text/template" id="clasificados_objetos_item_resultados_template">
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
    <td class="<%= clase %> data"><%= nombre %></td>
    <td class="<%= clase %> data tar"><%= moneda %> <%= Number(precio_final).toFixed(2) %></td>
    <td class="<%= clase %> data">
      <span class="label bg-success"><%= cantidad_consultas %></span>
    </td>
    <% if (!seleccionar) { %>
      <td class="p5 tar <%= clase %>">
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


<script type="text/template" id="clasificado_objeto_template">

<div class="bg-light lter b-b wrapper-md ng-scope">
  <h1 class="m-n font-thin h3">
	<% if (id == undefined) { %>
	    Nuevo Clasificado
	<% } else { %>
	    Editar Clasificado
	<% } %>
  </h1>
</div>

<div class="wrapper-md pb0">
    <div class="panel panel-default">
      <div class="panel-body">

          <div class="form-horizontal">

              <div class="form-group">
                  <label class="col-md-2 control-label">Titulo</label>
                  <div class="col-md-10">
                    <input type="text" id="clasificado_objeto_nombre" value="<%= nombre %>" name="nombre" class="form-control"/>
                  </div>
              </div>
              <div class="form-group">
                  <label class="col-md-2 control-label">Precio Final</label>
                  <div class="col-md-10">
                    <div class="form-inline">
                      <select id="clasificado_objeto_monedas" class="form-control" name="moneda">
                        <% for(var i=0;i<window.monedas.length;i++) { %>
                          <% var o = monedas[i]; %>
                          <option <%= (o.signo == moneda)?"selected":"" %> value="<%= o.signo %>"><%= o.signo %></option>
                        <% } %>
                      </select>
                      <input id="clasificado_objeto_precio_final" value="<%= precio_final %>" type="number" class="form-control number" name="precio_final"/>
                    </div>
                  </div>
              </div>
            </div>

            <div class="line line-dashed b-b line-lg pull-in"></div>

            <div class="form-horizontal row">
              <div class="col-md-6">
                <div class="h4">Texto</div>
                <div class="line b-b m-b"></div>

                <div class="form-group">
                  <div class="col-xs-12">
                    <textarea name="texto" style="height:120px" class="form-control" placeholder="Descripcion del clasificado..." id="clasificado_objeto_texto"><%= texto %></textarea>
                  </div>
                </div>

              </div>
              <div class="col-md-6">
                <div class="h4">Fotos</div>
                <div class="line b-b m-b"></div>

                <?php
                multiple_upload(array(
                  "name"=>"images",
                  "label"=>"",
                  "url"=>"objetos/function/save_image/",
                  "width"=>(isset($empresa->config["propiedad_galeria_image_width"]) ? $empresa->config["propiedad_galeria_image_width"] : 800),
                  "height"=>(isset($empresa->config["propiedad_galeria_image_height"]) ? $empresa->config["propiedad_galeria_image_height"] : 600),
                  "quality"=>(isset($empresa->config["propiedad_galeria_image_quality"]) ? $empresa->config["propiedad_galeria_image_quality"] : 0),
                  "thumbnail_width"=>(isset($empresa->config["propiedad_galeria_thumbnail_width"]) ? $empresa->config["propiedad_galeria_thumbnail_width"] : 267),
                  "thumbnail_height"=>(isset($empresa->config["propiedad_galeria_thumbnail_height"]) ? $empresa->config["propiedad_galeria_thumbnail_height"] : 150),
                )); ?>

              </div>
            </div>

          <div class="line line-dashed b-b line-lg pull-in"></div>
      </div>

      <div class="line line-dashed b-b line-lg pull-in"></div>

      <div class="col-xs-12">
        <div class="h4">Vendedor</div>
        <div class="line b-b m-b"></div>
        <textarea name="texto_privado" style="height:120px" class="form-control" placeholder="Datos del vendedor..." id="clasificado_objeto_texto_privado"><%= texto_privado %></textarea>
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

</script>
