<script type="text/template" id="milling_halloffames_panel_template">
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <h1 class="m-n font-thin h3"><i class="fa fa-star icono_principal"></i>Milling Hall of Fame</h1>
  </div>
  <div class="wrapper-md ng-scope">
    <div class="panel panel-default">
    
      <div class="panel-heading oh">
        <div class="row">
          <div class="col-md-6 col-lg-3 sm-m-b">
            <div class="search_container"></div>
          </div>
          <% if (control.check("milling_halloffames") > 1) { %>
            <div class="col-md-6 col-lg-offset-3 col-lg-6 text-right">
              <a class="btn btn-info btn-addon" href="app/#milling_halloffame"><i class="fa fa-plus"></i>&nbsp;&nbsp;Nuevo&nbsp;&nbsp;</a>
            </div>
          <% } %>
        </div>
      </div>
      <div class="panel-body">
        <div class="b-a table-responsive">
          <table id="milling_halloffames_table" class="table table-striped sortable m-b-none default footable">
            <thead>
              <tr>
                <th style="width:20px;">
                  <label class="i-checks m-b-none">
                    <input class="esc sel_todos" type="checkbox"><i></i>
                  </label>
                </th>
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


<script type="text/template" id="milling_halloffames_item">
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
      <i title="Active" class="fa-check iconito fa activo <%= (activo == 1)?"active":"" %>"></i>
      <div class="btn-group dropdown ml10">
        <button class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <i class="fa fa-plus"></i>
        </button>    
        <ul class="dropdown-menu pull-right">
          <li><a href="javascript:void(0)" class="duplicar" data-id="<%= id %>">Duplicate</a></li>
          <li><a href="javascript:void(0)" class="delete" data-id="<%= id %>">Delete</a></li>
        </ul>
      </div>
    </td>
  <% } %>
</script>

<script type="text/template" id="milling_halloffames_edit_panel_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <h1 class="m-n font-thin h3"><i class="fa fa-star icono_principal"></i>Milling Hall of Fame 
    / <b><%= (id == undefined) ? 'New' : nombre %></b>
  </h1>
</div>
<div class="wrapper-md ng-scope">
  <div class="centrado rform">
    <div class="row">
      <div class="col-md-10 col-md-offset-1">
        <div class="panel panel-default">
          <div class="panel-body">
          
            <div class="padder">

              <div class="form-group">
                <label class="control-label">Name</label>
                <input type="text" <%= (!edicion)?"disabled":"" %> name="nombre" class="form-control" id="milling_halloffames_nombre" value="<%= nombre %>"/>
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Subtitle</label>
                    <input type="text" <%= (!edicion)?"disabled":"" %> name="subtitulo" class="form-control" id="milling_halloffames_subtitulo" value="<%= subtitulo %>"/>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Tipo</label>
                    <select name="tipo" class="form-control" id="milling_halloffames_tipo">
                      <option <%= (tipo=="0")?"selected":"" %> value="0">-</option>
                      <option <%= (tipo=="1")?"selected":"" %> value="1">Historic</option>
                      <option <%= (tipo=="2")?"selected":"" %> value="2">Modern</option>
                    </select>
                  </div>
                </div>
              </div>

              <div class="form-group">
                <label class="control-label">
                  <?php echo lang(array(
                    "es"=>"Descripci&oacute;n",
                    "en"=>"Description",
                  )); ?>
                </label>
                <textarea name="texto" id="milling_halloffames_texto"><%= texto %></textarea>
              </div>

              <div class="form-group">
                <label class="control-label">
                  Statement from the commitee
                </label>
                <textarea name="comite" id="milling_halloffames_comite"><%= comite %></textarea>
              </div>

              <?php
              single_upload(array(
                "name"=>"path",
                "label"=>"Image",
                "url"=>"/sistema/milling_halloffames/function/save_image/",
                "url_file"=>"/sistema/milling_halloffames/function/save_file/",
                "width"=>400,
                "height"=>400,
              )); ?>                     
              
            </div>
          </div>
        </div>

        <div class="panel panel-default">
          <div class="panel-body">
            <div class="padder">
              <?php
              $label = lang(array(
                "es"=>"Im&aacute;genes",
                "en"=>"Photos",
              )); ?>
              <?php 
              multiple_upload(array(
                "name"=>"images",
                "label"=>$label,
                "url"=>"milling_halloffames/function/save_image/",
                "width"=>800,
                "height"=>600,
                "upload_multiple"=>true,
              )); ?>
            </div>
          </div>
        </div>

        <% if (edicion) { %>
          <button class="btn guardar btn-success">Save</button>
        <% } %>
      </div>
    </div>
  </div>
</div>

</script>