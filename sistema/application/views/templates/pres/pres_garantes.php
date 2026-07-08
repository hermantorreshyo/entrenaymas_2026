<script type="text/template" id="pres_garantes_panel_template">
<% if (seleccionar) { %>
  <div class="panel panel-default">
    <div class="panel-heading clearfix">
      <div class="row">
        <div class="col-xs-12 sm-m-b">
          <div class="input-group">
            <input type="text" id="pres_garantes_buscar" value="<%= window.pres_garantes_filter %>" placeholder="Buscar..." autocomplete="off" class="form-control">
            <span class="input-group-btn">
              <button class="btn btn-default buscar"><i class="fa fa-search"></i></button>
            </span>
          </div>
        </div>
      </div>
    </div>
    <div class="panel-body">
      <div class="b-a table-responsive">
        <table id="pres_garantes_table" class="table table-small table-striped sortable m-b-none default footable">
          <thead>
            <tr>
              <th style="width:20px;"></th>
              <th class="sorting" data-sort-by="nombre">Nombre</th>
            </tr>
          </thead>
          <tbody></tbody>
          <tfoot class="pagination_container hide-if-no-paging"></tfoot>
        </table>
      </div>
    </div>
  </div>
<% } else { %>
  <div class="seccion_llena">
    <div class="bg-light lter b-b wrapper-md ng-scope">
      <% var modulo = control.get("pres_garantes") %>
      <h1 class="m-n font-thin h3"><i class="<%= modulo.clase %> icono_principal"></i><%= modulo.title %></h1>
    </div>
    <div class="wrapper-md ng-scope">
      <div class="panel panel-default">
        <div class="panel-heading clearfix">
          <div class="row">
            <div class="col-md-6 col-lg-3 sm-m-b">
              <div class="input-group">
                <input type="text" id="pres_garantes_buscar" value="<%= window.pres_garantes_filter %>" placeholder="Buscar..." autocomplete="off" class="form-control">
                <span class="input-group-btn">
                <button class="btn btn-default buscar"><i class="fa fa-search"></i></button>
                </span>
              </div>
            </div>
            <div class="col-md-6 col-lg-offset-3 col-lg-6 text-right">
              <a class="btn btn-info btn-addon ml5" href="app/#pres_garante">
                <i class="fa fa-plus"></i><span>&nbsp;&nbsp;Nuevo&nbsp;&nbsp;</span>
              </a>
            </div>
          </div>
        </div>
        <div class="panel-body">
          <div class="b-a table-responsive">
            <table id="pres_garantes_table" class="table table-striped sortable m-b-none default footable">
              <thead>
                <tr>
                  <th style="width:20px;"></th>
                  <th class="w50 tac hidden-xs"></th>
                  <th class="sorting" data-sort-by="apellido">Nombre</th>
                  <th class="w20"></th>
                  <th class="col-xxs-0">Documento</th>
                  <th class="col-xxs-0 sorting" data-sort-by="telefono">Telefono</th>
                  <th class="col-xxs-0 sorting" data-sort-by="localidad">Localidad</th>
                  <% if (permiso > 1) { %>
                    <th class="th_acciones w120">Acciones</th>
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
  </div>
<% } %>
</script>

<script type="text/template" id="pres_garantes_item">
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
    <td class="<%= clase %> data hidden-xs">
    <% if (!isEmpty(path)) { %>
      <img src="/sistema/<%= path %>" class="customcomplete-image"/>
    <% } else { %>
      <span class="avatar xs avatar-texto <%= (activo==1)?'bg-info':'bg-light dker' %> pull-left">
        <%= isEmpty(nombre) ? email.substr(0,1).toUpperCase() : nombre.substr(0,1).toUpperCase() %>
      </span>
    <% } %>
    </td>
  <% } %> 
  <td class='data'><span class="capitalize <%= (activo==1)?'text-info':'text-muted' %>"><%= apellido.ucwords() %> <%= nombre.ucwords() %></span></td>
  <td>
    <% if (!isEmpty(nota)) { %>
      <i data-toggle="tooltip" title="<%= nota %>" class="fa fa-commenting text-warning"></i>
    <% } %>
  </td>
  <% if (!seleccionar) { %>
    <td class="data col-xxs-0 <%= clase %>">
      <span>
        <%= (id_tipo_documento == 96) ? "DNI":"" %>
        <%= (id_tipo_documento == 89) ? "LE":"" %>
        <%= (id_tipo_documento == 90) ? "LC":"" %>
        <%= (id_tipo_documento == 94) ? "Pas.":"" %>
        <%= documento %>
      </span>
    </td>
    <td class="data col-xxs-0 <%= clase %>"><span><%= (isEmpty(telefono))?"—":telefono %></span></td>
    <td class="data col-xxs-0 <%= clase %>"><span class="text-info"><%= (isEmpty(localidad))?"—":localidad.toLowerCase() %></span></td>
  <% } %> 
  <% if (permiso > 1) { %>
    <td class="p5 <%= clase %> td_acciones">
      <i title="Activo" class="fa-check iconito fa activo <%= (activo == 1)?"active":"" %>"></i>
      <div class="btn-group dropdown ml10">
        <button class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <i class="fa fa-plus"></i>
        </button>   
        <ul class="dropdown-menu pull-right">
          <li><a href="javascript:void(0)" class="delete" data-id="<%= id %>">Eliminar</a></li>
        </ul>
      </div>  
    </td>
  <% } %>
</script>

<script type="text/template" id="pres_garantes_edit_panel_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <% var modulo = control.get("pres_garantes") %>
  <h1 class="m-n font-thin h3"><i class="<%= modulo.clase %> icono_principal"></i><%= modulo.title %>
    / <b><%= (id == undefined)?"Nuevo":nombre.ucwords() %></b>
  </h1>
</div>
<div class="wrapper-md">
  <div class="centrado rform">
    <?php include("pres_garantes_detalle.php"); ?>
    <div class="line b-b m-b-lg"></div>
    <div class="row">
      <div class="col-md-4"></div>
      <div class="col-md-8 tar">
        <button class="btn guardar btn-success">Guardar</button>
      </div>
    </div>
  </div>
</div>
</script>