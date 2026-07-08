<div class="bg-light lter b-b wrapper-md ng-scope clearfix">
  <h1 class="m-n font-thin h3 pull-left"><i class="fa fa-tags icono_principal"></i><?php echo lang(array("es"=>"Productos","en"=>"Products")); ?></h1>
  <div class="btn-group dropdown pull-right <%= (ID_EMPRESA == 1284 && PERFIL != 1399)?"dn":"" %>">
    <button class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown"><i class="fa fa-cog"></i></button>
    <ul class="dropdown-menu pull-right">
      <% var modulo = control.get("rubros") %>
      <% if (modulo.permiso > 0) { %>
        <li><a href="app/#rubros"><%= modulo.nombre_es %></a></li>
      <% } %>
      <% var modulo = control.get("marcas") %>
      <% if (modulo.permiso > 0) { %>
        <li><a href="app/#marcas"><%= modulo.nombre_es %></a></li>
      <% } %>
      <% var modulo = control.get("articulos") %>
      <% if (modulo.permiso > 2) { %>
        <li><a href="app/#articulos_etiquetas">Etiquetas</a></li>
      <% } %>
      <?php // TODO: Hacer esto configurable ?>
      <% if (ID_EMPRESA == 229 || ID_EMPRESA == 230 || ID_EMPRESA == 980 || ID_EMPRESA == 1355) { %>
        <li><a href="javascript:void(0)" onclick="workspace.actualizar_articulos_clientes()">Actualizar promedio de ventas</a></li>
      <% } %>
    </ul>
  </div>
</div>
