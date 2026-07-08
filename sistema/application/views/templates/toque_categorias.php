<script type="text/template" id="toque_categorias_panel_template">
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <h1 class="m-n font-thin h3"><i class="fa fa-cogs icono_principal"></i>Configuracion
      / <b><%= (ID_EMPRESA == 1319) ? "Especialidades" : "Categorias" %></b>
    </h1>
  </div>
  <div class="wrapper-md ng-scope">
    <div class="panel panel-default">
    
      <div class="panel-heading oh">
        <div class="row">
          <div class="col-md-6 col-lg-3 sm-m-b">
            <div class="search_container"></div>
          </div>
          <% if (control.check("toque_categorias") > 1) { %>
            <div class="col-md-6 col-lg-offset-3 col-lg-6 text-right">
              <a class="btn btn-info btn-addon" href="app/#toque_categoria"><i class="fa fa-plus"></i>&nbsp;&nbsp;Nuevo&nbsp;&nbsp;</a>
            </div>
          <% } %>
        </div>
      </div>
      <div class="panel-body">
        <div class="b-a table-responsive">
          <table id="toque_categorias_table" class="table table-striped sortable m-b-none default footable">
            <thead>
              <tr>
                <th style="width:20px;">
                  <label class="i-checks m-b-none">
                    <input class="esc sel_todos" type="checkbox"><i></i>
                  </label>
                </th>
                <th>ID</td>
                <th class="w50 tac hidden-xs"></th>
                <th class="sorting" data-sort-by="nombre">Nombre</th>
                <% if (permiso > 1) { %>
                  <th class="w100"></th>
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


<script type="text/template" id="toque_categorias_item">
  <td>
    <label class="i-checks m-b-none">
      <input class="esc check-row" value="<%= id %>" type="checkbox"><i></i>
    </label>
  </td>
  <td class="ver"><span><%= id %></span></td>
  <td class="ver hidden-xs">
    <% if (!isEmpty(path)) { %><img src="/sistema/<%= path %>" class="customcomplete-image"/><% } %>
  </td>
  <td class="ver"><span class='text-info'><%= nombre %></span></td>
  <% if (permiso > 1) { %>
    <td class="p5 td_acciones">
      <div class="btn-group dropdown ml10">
        <button class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <i class="fa fa-plus"></i>
        </button>    
        <ul class="dropdown-menu pull-right">
          <li><a href="javascript:void(0)" class="duplicar" data-id="<%= id %>">Duplicar</a></li>
          <li><a href="javascript:void(0)" class="delete" data-id="<%= id %>">Eliminar</a></li>
        </ul>
      </div>
    </td>
  <% } %>
</script>

<script type="text/template" id="toque_categorias_edit_panel_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <h1 class="m-n font-thin h3"><i class="fa fa-cogs icono_principal"></i>Configuracion
    / <%= (ID_EMPRESA == 1319) ? "Especialidades" : "Categorias" %>
    / <b><%= (id == undefined) ? 'Nueva' : nombre %></b>
  </h1>
</div>
<div class="wrapper-md ng-scope">
  <div class="centrado rform">
    <div class="row">
      <div class="col-md-10 col-md-offset-1">
        <div class="panel panel-default">
          <div class="panel-body">
          
            <div class="padder">

              <div class="row">
                <div class="col-md-10">
                  <div class="form-group">
                    <label class="control-label">Nombre</label>
                    <input type="text" <%= (!edicion)?"disabled":"" %> name="nombre" class="form-control" id="toque_categorias_nombre" value="<%= nombre %>"/>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group">
                    <label class="control-label">Orden</label>
                    <input <%= (!edicion)?"disabled":"" %> type="text" name="orden" class="form-control" id="toque_categorias_orden" value="<%= orden %>"/>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label class="control-label">Subtitulo</label>
                <input <%= (!edicion)?"disabled":"" %> type="text" name="subtitulo" class="form-control" id="toque_categorias_subtitulo" value="<%= subtitulo %>"/>
              </div>
              <div class="form-group">
                <label class="control-label">Link</label>
                <input <%= (!edicion)?"disabled":"" %> type="text" name="link" class="form-control" id="toque_categorias_link" value="<%= link %>"/>
              </div>

              <?php
              single_upload(array(
                "name"=>"path",
                "label"=>(($empresa->id == 1284) ? "Imagen 1" : "Logo"),
                "url"=>"/sistema/toque_categorias/function/save_image/",
                "url_file"=>"/sistema/toque_categorias/function/save_file/",
                "width"=>(isset($empresa->config["toque_categoria_image_width"]) ? $empresa->config["toque_categoria_image_width"] : 400),
                "height"=>(isset($empresa->config["toque_categoria_image_height"]) ? $empresa->config["toque_categoria_image_height"] : 400),
                "quality"=>(isset($empresa->config["toque_categoria_image_quality"]) ? $empresa->config["toque_categoria_image_quality"] : 0.92),
              )); ?>    

              <?php
              single_upload(array(
                "name"=>"path_2",
                "label"=>(($empresa->id == 1284) ? "Imagen 2" : "Imagen"),
                "url"=>"/sistema/toque_categorias/function/save_image/",
                "url_file"=>"/sistema/toque_categorias/function/save_file/",
                "width"=>(isset($empresa->config["toque_categoria_image_width"]) ? $empresa->config["toque_categoria_image_width"] : 400),
                "height"=>(isset($empresa->config["toque_categoria_image_height"]) ? $empresa->config["toque_categoria_image_height"] : 400),
                "quality"=>(isset($empresa->config["toque_categoria_image_quality"]) ? $empresa->config["toque_categoria_image_quality"] : 0.92),
              )); ?>  
              
            </div>
          </div>
        </div>
        <% if (edicion) { %>
          <button class="btn guardar btn-success">Guardar</button>
        <% } %>
      </div>
    </div>
  </div>
</div>

</script>