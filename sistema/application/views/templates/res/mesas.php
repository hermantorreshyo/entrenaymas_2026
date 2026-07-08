<script type="text/template" id="salones_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <% if (edicion) { %>
    <h1 class="m-n font-thin h3"><i class="fa fa-cog icono_principal"></i>Configuraci&oacute;n 
    / <b>Salones y mesas</b>
    </h1>
  <% } else { %>
    <h1 class="m-n font-thin h3"><i class="fa fa-cutlery icono_principal"></i>Mesas</h1>
  <% } %>
</div>
<div class="salones_container ng-scope">
  <div class="panel panel-default">
    <div class="panel-body">
      <div class="tab-container">
        <ul class="nav nav-tabs" role="tablist">
          <% for(var i=0;i < salones.length;i++) { %>
            <% var salon = salones[i]; %>
            <li data-id="<%= salon.id %>" data-i="<%= i %>" class="tab_link <%= (i==0)?'active':'' %>">
              <a href="#tab<%=i%>" role="tab" data-toggle="tab">
                <%= salon.nombre %>
                <% if (edicion) { %>
                  <i data-id="<%= salon.id %>" class="editar_salon fa fa-pencil cp m-l-xs"></i>
                <% } %>
              </a>
            </li>
          <% } %>
          <% if (edicion) { %>
            <button class="btn pull-right btn-info nuevo btn-addon"><i class="fa fa-plus"></i>Nuevo</button>
          <% } %>
        </ul>
        <div class="tab-content">
          <% for(var i=0;i < salones.length;i++) { %>
            <div id="tab<%=i%>" class="tab-pane <%= (i==0)?'active':'' %>"></div>
          <% } %>
        </div>
      </div>
    </div>
  </div>
</div>
</script>

<script type="text/template" id="salon_template">
<div class="panel panel-default">
  <div class="panel-heading">
    <span class="font-bold"><%= (id == 0) ? "Nuevo Sal&oacute;n" : nombre %></span>
  </div>
  <div class="panel-body">
    <div class="row">
      <div class="col-md-12">
        <div class="form-group">
          <label class="control-label">Nombre</label>
          <input type="text" id="salon_nombre" class="form-control" value="<%= nombre %>" name="nombre"/>
        </div>
      </div>
    </div>
  </div>
  <div class="panel-footer oh">
    <button class="btn guardar btn-success">Guardar</button>
    <button class="btn eliminar fr btn-danger">Eliminar</button>
  </div>
</div>
</script>


<script type="text/template" id="mesas_template">
<div class="mesas_layer">
  <table class="mesas_tabla">
    <tbody class="tbody">
      <% for(var i=0;i < 20;i++) { %>
        <tr>
          <% for(var j=0;j < 20;j++) { %>
            <td class="casillero" data-id_salon="<%= id_salon %>" data-x="<%= j %>" data-y="<%= i %>" id="mesa_<%= id_salon %>_<%= j %>_<%= i %>">
              <% if (edicion) { %>
                <a class="agregar_mesa"><i class="fa fa-plus"></i></a>
              <% } %>
            </td>
          <% } %>
        </tr>
      <% } %>
    </tbody>
  </table>
</div>
</script>

<script type="text/template" id="mesa_view_template">
<div>
  <span class="numero"><%= nombre %></span>
</div>
</script>

<script type="text/template" id="mesa_template">
<div class="panel panel-default">
  <div class="panel-heading">
    <span class="font-bold"><%= (id == undefined) ? "Nueva mesa":"Editar mesa" %></span>
  </div>
  <div class="panel-body">
    <div class="form-group">
      <label class="control-label">N&uacute;mero de mesa:</label>
      <input type="text" id="mesa_nombre" class="form-control" value="<%= nombre %>" placeholder="Ej: 1" name="nombre"/>
    </div>
  </div>
  <div class="panel-footer oh">
    <button class="btn guardar btn-success">Guardar</button>
    <button class="btn eliminar fr btn-danger">Eliminar</button>
  </div>
</div>
</script>
