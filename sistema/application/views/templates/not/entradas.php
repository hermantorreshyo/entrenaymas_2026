<script type="text/template" id="entradas_resultados_template">
<div class="seccion_vacia" style="display:none">
  <h1 class="h1">
    <?php echo lang(array("es"=>"Todav&iacute;a no ten&eacute;s ning&uacute;n entrada","en"=>"You haven't upload any post")); ?>
  </h1>
  <h3 class="h3">
    <?php echo lang(array("es"=>"Para crear tu primera entrada, hace click en el siguiente bot&oacute;n","en"=>'Click in "New Post" button to create the first')); ?>
  </h3>
  <div class="list-icon">
    <a href="app/#entrada"><i class="icon-note"></i></a>
  </div>
  <div>
    <% if (control.check("entradas")>2) { %>
      <a class="btn btn-lg btn-info btn-addon" href="<%= (window.entradas_id_categoria == 0) ? "app/#entrada" : "app/#nueva_entrada/"+window.entradas_id_categoria %>">
        <i class="fa fa-plus"></i><span>
        <?php echo lang(array("es"=>"  Nuevo  ","en"=>"New Post")); ?>
        </span>
      </a>
    <% } %>
  </div>
  <p>
    <?php echo lang(array("es"=>"Si necesitas ayuda o asesoramiento, no dudes en comunicarte, hace click ","en"=>"If you need some help, please communicate with us ")); ?>
    <a class="text-info">
      <?php echo lang(array("es"=>"acá!","en"=>"here!")); ?>
    </a>
  </p>
</div>
<div class="seccion_llena" style="display:none">
  <% if (!seleccionar) { %>
    <div class="bg-light lter b-b wrapper-md ng-scope">
      <h1 class="m-n font-thin h3"><i class="fa fa-file-text icono_principal"></i>
      <?php echo lang(array("es"=>"Entradas","en"=>"Posts")); ?>
      </h1>
    </div>
  <% } %>
  <div class="<% if (!seleccionar) { %>wrapper-md<% } %> ng-scope">
    <div class="panel panel-default">
        <div class="panel-heading clearfix">
          <div class="row">
            <div class="col-md-6 col-lg-3 sm-m-b">
              <div class="input-group">
                  <input type="text" id="entradas_buscar" placeholder="<?php echo lang(array("es"=>"Buscar","en"=>"Search")); ?>..." value="<%= window.entradas_filter %>" autocomplete="off" class="form-control">
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

                <?php /*
                <div class="btn-group dropdown ml5">
                  <button class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                    <span>Opciones</span>
                    <span class="caret"></span>
                  </button>
                  <ul class="dropdown-menu pull-right">
                    <li><a href="javascript:void" class="exportar_csv">Exportar</a></li>
                    <li><a href="javascript:void" class="importar_csv">Importar</a></li>
                  </ul>
                </div>
                */ ?>
                <% if (control.check("entradas")>2) { %>
                  <a class="btn btn-info btn-addon ml5" href="<%= (window.entradas_id_categoria == 0) ? "app/#entrada" : "app/#nueva_entrada/"+window.entradas_id_categoria %>">
                    <i class="fa fa-plus"></i>
                    <span class="hidden-xs">
                      <?php echo lang(array("es"=>"&nbsp;&nbsp;Nueva&nbsp;&nbsp;","en"=>"&nbsp;&nbsp;New&nbsp;&nbsp;")); ?>
                    </span>
                  </a>
                <% } %>

              </div>
            <% } %>
          </div>
        </div>
        <div class="advanced-search-div bg-light dk" style="<%= (window.entradas_id_categoria == 0) ? 'display:none' : 'display:block' %>">
          <div class="wrapper oh">
            <h4 class="m-t-xs"><i class="fa fa-search"></i> <?php echo lang(array("es"=>"B&uacute;squeda Avanzada:","en"=>"Advanced Search:")); ?></h4>
            <div class="form-inline">
              <div style="width: 250px; display: inline-block">
                <select id="entradas_buscar_categorias" class="w100p"></select>
              </div>
              <div class="form-group">
                <button id="entradas_buscar_avanzada_btn" class="btn btn-default"><i class="fa fa-search"></i> <?php echo lang(array("es"=>"Buscar","en"=>"Search")); ?></button>
              </div>
            </div>
          </div>
        </div>
        <% if (!seleccionar) { %>
          <div class="bulk_action wrapper pb0">
            <button class="btn btn-default eliminar_lote btn-addon"><?php echo lang(array("es"=>"Eliminar","en"=>"Delete")); ?></button>
          </div>
        <% } %>

        <div class="panel-body">
          <div class="b-a table-responsive">
            <table id="entradas_tabla" class="table table-striped sortable m-b-none default footable">
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
                    <th class="w50 tac"><?php echo lang(array("es"=>"Imagen","en"=>"Image")); ?></th>
                    <th class="sorting" data-sort-by="titulo"><?php echo lang(array("es"=>"T&iacute;tulo","en"=>"Title")); ?></th>
                    <th class="sorting" data-sort-by="categoria"><?php echo lang(array("es"=>"Categor&iacute;a","en"=>"Category")); ?></th>
                    <th class="sorting" data-sort-by="A.fecha"><?php echo lang(array("es"=>"Fecha","en"=>"Date")); ?></th>
                    <% if (!seleccionar) { %>
                      <th class="th_acciones w150"><?php echo lang(array("es"=>"Acciones","en"=>"Actions")); ?></th>
                    <% } %>
                  </tr>
                </thead>
                <tbody class="tbody"></tbody>
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
</div>
</script>

<script type="text/template" id="entradas_item_resultados_template">
    <% var clase = (activo==1)?"":"text-muted"; %>
    <% if (!seleccionar) { %>
      <td>
        <label class="i-checks m-b-none">
          <input class="esc check-row" value="<%= id %>" type="checkbox"><i></i>
        </label>
      </td>
    <% } %>
    <td class="<%= clase %> data">
      <% if (!isEmpty(path)) { %>
        <img src="<%= show_path(path) %>" class="customcomplete-image"/>
      <% } %>
    </td>
    <td class="<%= clase %> data">
      <span class="text-info"><%= titulo %></span>
      <% if (ID_EMPRESA == 225 || ID_EMPRESA == 105 || ID_EMPRESA == 1052) { %>
        <% if (nivel_importancia == 1) { %>
          <span class="label bg-success">Portada</span>
        <% } else if (nivel_importancia == 2) { %>
          <span class="label bg-success">Portada principal</span>
        <% } %>
      <% } %>
    </td>
    <td class="<%= clase %> data"><%= categoria %></td>
    <td class="<%= clase %> data"><%= fecha %></td>
    <% if (!seleccionar) { %>
      <td class="tar <%= clase %>">
        <a target="_blank" href="http://<%= String(DOMINIO+'/'+link+'?preview=1').replace('//','/') %>"><i title="Ir a pagina" class="fa-external-link iconito fa"></i></a>
        <i title="Activo" class="fa-check iconito fa activo <%= (activo == 1)?"active":"" %>"></i>
        <i title="Destacado" class="fa fa-star iconito destacado <%= (destacado == 1)?"active":"" %>"></i>
        <div class="btn-group dropdown">
          <i title="Opciones" class="iconito fa fa-caret-down dropdown-toggle" data-toggle="dropdown"></i>
          <ul class="dropdown-menu pull-right">
            <% if (ID_EMPRESA == 225 || ID_EMPRESA == 105 || ID_EMPRESA == 1052) { %>
              <li>
                <% if (nivel_importancia == 0) { %>
                  <a href="javascript:void(0)" class="nivel_importancia_1" data-id="<%= id %>">
                    Portada normal
                  </a>
                  <a href="javascript:void(0)" class="nivel_importancia_2" data-id="<%= id %>">
                    Portada principal
                  </a>
                <% } %>
                <% if (nivel_importancia == 1) { %>
                  <a href="javascript:void(0)" class="nivel_importancia_0" data-id="<%= id %>">
                    Sacar de portada
                  </a>
                  <a href="javascript:void(0)" class="nivel_importancia_2" data-id="<%= id %>">
                    Portada principal
                  </a>
                <% } %>
                <% if (nivel_importancia == 2) { %>
                  <a href="javascript:void(0)" class="nivel_importancia_0" data-id="<%= id %>">
                    Sacar de portada
                  </a>
                  <a href="javascript:void(0)" class="nivel_importancia_1" data-id="<%= id %>">
                    Portada normal
                  </a>
                <% } %>

              </li>
            <% } %>
            <% if ((MILLING == 1) && id_cliente != 0) { %>
              <% if (custom_1 == "1") { %>
                <li><a class="text-danger" href="javascript:void(0)"><?php echo lang(array("es"=>"Email enviado","en"=>"Email sent")); ?></a></li>
              <% } else { %>
                <li><a href="javascript:void(0)" class="notificar_email" data-id="<%= id %>"><?php echo lang(array("es"=>"Notificar","en"=>"Send Email")); ?></a></li>
              <% } %>
            <% } %>
            <% if (ID_EMPRESA == 70) { %>
              <li><a href="javascript:void(0)" class="notificar" data-id="<%= id %>"><?php echo lang(array("es"=>"Notificar","en"=>"Send Notification")); ?></a></li>
            <% } else if (ID_EMPRESA == 341) { %>
              <li><a href="javascript:void(0)" class="notificar_laboral_gym" data-id="<%= id %>"><?php echo lang(array("es"=>"Notificar","en"=>"Send Notification")); ?></a></li>
            <% } %>
            <li><a href="javascript:void(0)" class="duplicar" data-id="<%= id %>"><?php echo lang(array("es"=>"Duplicar","en"=>"Duplicate")); ?></a></li>
            <li><a href="javascript:void(0)" class="eliminar" data-id="<%= id %>"><?php echo lang(array("es"=>"Eliminar","en"=>"Delete")); ?></a></li>
          </ul>
        </div>
      </td>
    <% } %>
</script>


<script type="text/template" id="entrada_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <h1 class="m-n font-thin h3"><i class="fa fa-file-text icono_principal"></i><?php echo lang(array("es"=>"Entradas","en"=>"Posts")); ?>
    / <b>
    <?php echo lang(array("es"=>"<%= (id == undefined)?'Nueva':titulo %>","en"=>"<%= (id == undefined)?'New':titulo %>")); ?>
    </b>
  </h1>
</div>
<?php if ($empresa->id == 285) {
  include("entradas_detalle_285.php"); 
} else {
  include("entradas_detalle.php");
} ?>
</script>
