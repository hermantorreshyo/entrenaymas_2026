<script type="text/template" id="anunciantes_panel_template">

  <div class="bg-light lter b-b wrapper-md ng-scope">
    <h1 class="m-n font-thin h3">
      Anunciantes
    </h1>
  </div>
  <div class="wrapper-md">
    <div class="panel panel-default">
      <div class="panel-heading clearfix">
        <div class="row">
          <div class="search_container col-lg-3 col-md-6"></div>
          <div class="col-md-6 col-lg-offset-3 col-lg-6 text-right">
            <% if (control.check("anunciantes") >= 2) { %>
              <a class="btn pull-right btn-info btn-addon" href="app/#anunciante"><i class="fa fa-plus"></i>&nbsp;&nbsp;<?php echo lang(array("es"=>"Nuevo","en"=>"New")); ?>&nbsp;&nbsp;</a>
            <% } %>
          </div>
        </div>
      </div>
      <div class="panel-body">
        <div class="b-a table-responsive">
          <table id="anunciantes_table" data-ordenable-table="email_template" data-ordenable-where="" class="table table-striped ordenable m-b-none default footable">
            <thead>
              <tr>
                <th class="w120"></th>
                <th>Nombre</th>
                <th>Texto</th>
                <th>Redirección</th>
                <% if (PERFIL == 1355) { %>
                  <th>Donde mostrar</th>
                <% } %>
                <% if (control.check("anunciantes") >= 2) { %>
                  <th class="w25"></th>
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


<script type="text/template" id="anunciantes_item">

  <% var clase = (control.check("anunciantes") >= 2) ? 'edit' : '' %>

  <td class="<%= clase %> w120"><img class="propiedad-image" src="<%= path %>"></td>
  <td class="<%= clase %>"><%= nombre %></td>
  <td class="<%= clase %>"><%= texto %></td>
  <td class=""><a class="btn btn-info" target="_blank" href="<%= link %>">Ir al sitio web</a></td>
  <% if (PERFIL == 1355) { %>
    <td class="<%= clase %>"><%= mostrar %></td>
  <% } %>
  <% if (control.check("anunciantes") >= 3) { %>
    <td>
      <div class="btn-group dropdown">
        <i title="Opciones" class="iconito fa fa-caret-down dropdown-toggle" data-toggle="dropdown"></i>
        <ul class="dropdown-menu pull-right">
          <li><a href="javascript:void(0)" class="delete" data-id="<%= id %>"><?php echo lang(array("es"=>"Eliminar","en"=>"Delete")); ?></a></li>
        </ul>
      </div>
    </td>
  <% } %>
</script>

<script type="text/template" id="anunciantes_edit_panel_template">

  <div class="bg-light lter b-b wrapper-md ng-scope">
    <h1 class="m-n font-thin h3">
      Anunciantes / <b><%= (id == undefined) ? "<?php echo lang(array("es"=>"Nuevo","en"=>"New")); ?>":"<?php echo lang(array("es"=>"Editar","en"=>"Edit")); ?>" %></b>
    </h1>
  </div>
  <div class="wrapper-md">
    <div class="centrado rform">

      <div class="row">
        <div class="col-md-10 col-md-offset-1">
          <div class="panel panel-default">
            <div class="panel-body">
              <div class="padder">
                <div class="form-group">
                  <label class="control-label">Nombre</label>
                  <input type="text" name="nombre" class="form-control" id="anunciantes_nombre" value="<%= nombre %>"/>
                </div>
                <div class="form-group mt10">
                  <label class="control-label">Link de Redirección</label>
                  <input type="text" name="link" class="form-control" id="anunciantes_link" value="<%= link %>"/>
                </div>
                <div class="form-group mt10">
                  <label class="control-label">Texto</label>
                  <textarea class="form-control" rows="5" name="texto" id="anunciantes_texto"><%= texto %></textarea>
                </div>
                <?php
                  single_upload(array(
                    "name"=>"path",
                    "label"=>"Imagen Principal",
                    "url"=>"/sistema/anunciantes/function/save_image/",
                  )); 
                ?>

                <div class="form-group mt10">
                  <label class="control-label">Donde mostrar</label>
                  <select class="form-control" name="mostrar">
                    <option <%= (mostrar == "todos") ? 'selected' : '' %> value="todos">Todos</option>
                    <option <%= (mostrar == "home") ? 'selected' : '' %> value="home">Home</option>
                    <option <%= (mostrar == "backend") ? 'selected' : '' %> value="backend">Backend</option>
                  </select>
                </div>

              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-10 col-md-offset-1 tar">
          <button class="btn guardar btn-success"><?php echo lang(array("es"=>"Guardar","en"=>"Save")); ?></button>
        </div>
      </div>

    </div>
  </div>

</script>