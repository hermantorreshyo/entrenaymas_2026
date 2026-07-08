<script type="text/template" id="conferencistas_panel_template">
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <% var modulo = control.get("menu_not_eventos") %>
    <h1 class="m-n font-thin h3"><i class="<%= modulo.clase %> icono_principal"></i><%= modulo.title %>
      <% var modulo1 = control.get("conferencistas") %>
      / <b><%= modulo1.title %></b>
    </h1>
  </div>
  <div class="wrapper-md ng-scope">
    <div class="panel panel-default">
    
      <div class="panel-heading oh">
        <div class="row">
          <div class="col-md-6 col-lg-3 sm-m-b">
            <div class="search_container"></div>
          </div>
          <div class="col-md-6 col-lg-offset-3 col-lg-6 text-right">
            <a class="btn btn-info btn-addon" href="app/#conferencista"><i class="fa fa-plus"></i>&nbsp;&nbsp;<?php echo lang(array("es"=>"Nuevo","en"=>"New")); ?>&nbsp;&nbsp;</a>
          </div>
        </div>
      </div>
      <div class="panel-body">
        <div class="b-a table-responsive">
          <table id="conferencistas_table" class="table table-striped sortable m-b-none default footable">
            <thead>
              <tr>
                <th style="width:20px;">
                  <label class="i-checks m-b-none">
                    <input class="esc sel_todos" type="checkbox"><i></i>
                  </label>
                </th>
                <th class="w50 tac hidden-xs"></th>
                <th class="sorting" data-sort-by="nombre"><?php echo lang(array("es"=>"Nombre","en"=>"Name")); ?></th>
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


<script type="text/template" id="conferencistas_item">
  <td>
    <label class="i-checks m-b-none">
      <input class="esc check-row" value="<%= id %>" type="checkbox"><i></i>
    </label>
  </td>
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
          <li>
            <a href="javascript:void(0)" class="duplicar" data-id="<%= id %>">
              <?php echo lang(array("es"=>"Duplicar","en"=>"Duplicate")); ?>
            </a>
          </li>
          <li>
            <a href="javascript:void(0)" class="delete" data-id="<%= id %>">
              <?php echo lang(array("es"=>"Eliminar","en"=>"Delete")); ?>
            </a>
          </li>
        </ul>
      </div>
    </td>
  <% } %>
</script>

<script type="text/template" id="conferencistas_edit_panel_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <% var modulo = control.get("menu_not_eventos") %>
  <h1 class="m-n font-thin h3"><i class="<%= modulo.clase %> icono_principal"></i><%= modulo.title %>
    <% var modulo1 = control.get("conferencistas") %>
    / <%= modulo1.title %>
    / <b><%= (id == undefined) ? 'New' : nombre %></b>
  </h1>
</div>
<div class="wrapper-md ng-scope">
  <div class="centrado rform">
    <div class="row">
      <div class="col-md-4"></div>
      <div class="col-md-8">
        <div class="panel panel-default">
          <div class="panel-body">
          
            <div class="padder">

              <div class="form-group">
                <label class="control-label"><?php echo lang(array("es"=>"Nombre","en"=>"Name")); ?></label>
                <% if (edicion) { %>
                  <input type="text" name="nombre" class="form-control" id="conferencistas_nombre" value="<%= nombre %>"/>
                <% } else { %>
                  <span><%= nombre %></span>
                <% } %>
              </div>

              <div class="form-group">
                <label class="control-label"><?php echo lang(array("es"=>"Titulo de la conferencia","en"=>"Conference title")); ?></label>
                <% if (edicion) { %>
                  <input type="text" name="titulo" class="form-control" id="conferencistas_titulo" value="<%= titulo %>"/>
                <% } else { %>
                  <span><%= titulo %></span>
                <% } %>
              </div>        

              <div class="form-group">
                <label class="control-label"><?php echo lang(array("es"=>"Subtitulo de la conferencia","en"=>"Conference subtitle")); ?></label>
                <% if (edicion) { %>
                  <input type="text" name="subtitulo" class="form-control" id="conferencistas_subtitulo" value="<%= subtitulo %>"/>
                <% } else { %>
                  <span><%= subtitulo %></span>
                <% } %>
              </div>

              <div class="form-group">
                <label class="control-label"><?php echo lang(array("es"=>"Tematica","en"=>"Subject")); ?></label>
                <% if (edicion) { %>
                  <input type="text" name="tematica" class="form-control" id="conferencistas_tematica" value="<%= tematica %>"/>
                <% } else { %>
                  <span><%= tematica %></span>
                <% } %>
              </div>  

              <div class="form-group">
                <label class="control-label">
                  <?php echo lang(array(
                    "es"=>"Categor&iacute;a",
                    "en"=>"Category",
                  )); ?>
                </label>
                <select id="conferencista_categorias" class="form-control"></select>
              </div>

              <div class="form-group">
                <label class="control-label">
                  <?php echo lang(array(
                    "es"=>"Cliente",
                    "en"=>"Company",
                  )); ?>
                </label>
                <select id="conferencista_clientes" class="form-control no-model w100p"></select>
              </div>

              <div class="form-group">
                <label class="control-label">
                  <?php echo lang(array(
                    "es"=>"Evento",
                    "en"=>"Event",
                  )); ?>
                </label>
                <select id="conferencista_eventos" class="form-control"></select>
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">
                      <?php echo lang(array(
                        "es"=>"Fecha",
                        "en"=>"Date",
                      )); ?>
                    </label>
                    <div class="input-group">
                      <input type="text" name="fecha" id="conferencista_fecha" value="<%= fecha %>" class="form-control"/>
                      <span class="input-group-btn">
                        <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                      </span>              
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">
                      <?php echo lang(array(
                        "es"=>"Lugar",
                        "en"=>"Location",
                      )); ?>
                    </label>
                    <input type="text" name="lugar" id="conferencista_lugar" value="<%= lugar %>" class="form-control"/>
                  </div>
                </div>
              </div>

              <?php
              single_upload(array(
                "name"=>"path",
                "label"=>lang(array("es"=>"Imagen","en"=>"Image")),
                "url"=>"/sistema/conferencistas/function/save_image/",
                "width"=>(isset($empresa->config["conferencista_image_width"]) ? $empresa->config["conferencista_image_width"] : 400),
                "height"=>(isset($empresa->config["conferencista_image_height"]) ? $empresa->config["conferencista_image_height"] : 400),
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