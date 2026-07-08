<script type="text/template" id="categorias_entrena_panel_template">
<% if (seleccion) { %>
  <div class="panel-body">
    <div class="form-group">
      <div class="search_container"></div>
    </div>
    <div class="b-a table-responsive" style="overflow: auto; max-height: 400px">
      <table id="categorias_entrena_table" data-ordenable-table="email_template" data-ordenable-where="" class="table table-striped ordenable m-b-none default footable">
        <thead>
          <tr>
            <th><?php echo lang(array("es"=>"Seleccione una opcion","en"=>"Select a template")); ?></th>
          </tr>
        </thead>
        <tbody></tbody>
        <tfoot class="pagination_container hide-if-no-paging"></tfoot>
      </table>
    </div>
  </div>
<% } else { %>
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <h1 class="m-n font-thin h3"><i class="fa fa-cog icono_principal"></i><?php echo lang(array("es"=>"Configuraci&oacute;n","en"=>"Configuration")); ?>
      / <b><?php echo lang(array("es"=>"Categorias","en"=>"Email Templates")); ?></b>
    </h1>
  </div>
  <div class="wrapper-md">
    <div class="panel panel-default">
      <div class="panel-heading clearfix">
        <div class="row">
          <div class="search_container col-lg-3 col-md-6"></div>
          <div class="col-md-6 col-lg-offset-3 col-lg-6 text-right">
            <a class="btn pull-right btn-info btn-addon" href="app/#categoria_entrena"><i class="fa fa-plus"></i>&nbsp;&nbsp;<?php echo lang(array("es"=>"Nuevo","en"=>"New")); ?>&nbsp;&nbsp;</a>
          </div>
        </div>
      </div>
      <div class="panel-body">
        <div class="b-a table-responsive">
          <table id="categorias_entrena_table" data-ordenable-table="email_template" data-ordenable-where="" class="table table-striped ordenable m-b-none default footable">
            <thead>
              <tr>
                <th><?php echo lang(array("es"=>"Asunto","en"=>"Subject")); ?></th>
                <th class="w25"></th>
              </tr>
            </thead>
            <tbody></tbody>
            <tfoot class="pagination_container hide-if-no-paging"></tfoot>
          </table>
        </div>
      </div>
    </div>
  </div>
<% } %>
</script>


<script type="text/template" id="categorias_entrena_item">
  <td class="edit"><%= nombre %></td>
  <td>
    <% if (!seleccion) { %>
      <div class="btn-group dropdown">
        <i title="Opciones" class="iconito fa fa-caret-down dropdown-toggle" data-toggle="dropdown"></i>
        <ul class="dropdown-menu pull-right">
          <% if (MILLING == 1) { %>
            <li><a href="javascript:void(0)" class="notificar" data-id="<%= id %>"><?php echo lang(array("es"=>"Notificar","en"=>"Send Notification")); ?></a></li>
          <% } %>
          <li><a href="javascript:void(0)" class="delete" data-id="<%= id %>"><?php echo lang(array("es"=>"Eliminar","en"=>"Delete")); ?></a></li>
        </ul>
      </div>
    <% } %>
  </td>
</script>

<script type="text/template" id="categorias_entrena_edit_panel_template">
<% if (!lightbox) { %>
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <h1 class="m-n font-thin h3"><i class="fa fa-cog icono_principal"></i><?php echo lang(array("es"=>"Configuraci&oacute;n","en"=>"Configuration")); ?>
      / <?php echo lang(array("es"=>"Categorias","en"=>"Email Templates")); ?>
      / <b><%= (id == undefined) ? "<?php echo lang(array("es"=>"Nuevo","en"=>"New")); ?>":"<?php echo lang(array("es"=>"Editar","en"=>"Edit")); ?>" %></b>
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
                  <label class="control-label"><?php echo lang(array("es"=>"Nombre","en"=>"Subject")); ?></label>
                  <input type="text" name="nombre" class="form-control" id="categorias_entrena_nombre" value="<%= nombre %>"/>
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
  
  <% } else { %>
    <div class="panel panel-default">
      <div class="panel-heading bold"><%= nombre %></div>
      <div class="panel-body">
        <textarea name="texto" id="categorias_entrena_texto"><%= texto %></textarea>
      </div>
      <div class="panel-footer">
        <button class="btn btn-success guardar"><?php echo lang(array("es"=>"Guardar","en"=>"Save")); ?></button>
      </div>
    </div>
  <% } %>
</script>