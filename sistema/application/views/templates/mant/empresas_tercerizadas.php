<script type="text/template" id="empresas_tercerizadas_panel_template">
  <div class="bg-light lter b-b wrapper-md ng-scope">
  <% var modulo = control.get("empresas_tercerizadas") %>
  <h1 class="m-n font-thin h3"><i class="<%= modulo.clase %> icono_principal"></i> Configuraci&oacute;n / <b><%= modulo.title %></b></h1>
  </div>
  <div class="wrapper-md ng-scope">
    <div class="panel panel-default">
      <div class="panel-heading oh">
        <div class="row">
          <div class="col-md-6 col-lg-3 sm-m-b">
            <div class="search_container"></div>
          </div>
          <div class="col-md-6 col-lg-offset-3 col-lg-6 text-right">
            <a class="btn btn-info btn-addon" href="app/#empresa_tercerizada"><i class="fa fa-plus"></i>Nuevo</a>
          </div>
        </div>
      </div>
      <div class="panel-body">
        <div class="b-a table-responsive">
          <table id="empresas_tercerizadas_table" class="table table-striped sortable m-b-none default footable">
            <thead>
              <tr>
                <th style="width:20px;">
                  <label class="i-checks m-b-none">
                    <input class="esc sel_todos" type="checkbox"><i></i>
                  </label>
                </th>
                <th>Nombre</th>
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


<script type="text/template" id="empresas_tercerizadas_item">
  <td>
    <label class="i-checks m-b-none">
      <input class="esc check-row" value="<%= id %>" type="checkbox"><i></i>
    </label>
  </td>
  <td><span class='ver text-info'><%= nombre %></span></td>
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

<script type="text/template" id="empresas_tercerizadas_edit_panel_template">
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <% var modulo = control.get("empresas_tercerizadas") %>
    <h1 class="m-n font-thin h3"><i class="<%= modulo.clase %> icono_principal"></i> Configuraci&oacute;n / <%= modulo.title %> / <b><%= (id == undefined) ? 'Nuevo' : 'Editar' %></b></h1>
  </div>
  <div class="wrapper-md ng-scope">
    <div class="centrado rform">
      <div class="row">
        <div class="col-md-4">
          <div class="detalle_texto">Informaci&oacute;n</div>
        </div>
        <div class="col-md-8">
          <div class="panel panel-default">
            <div class="panel-body">
              <div class="padder">
                <div class="form-group">
                  <label class="control-label">Nombre</label>
                  <input type="text" name="nombre" class="form-control" id="empresas_tercerizadas_nombre" value="<%= nombre %>"/>
                </div>
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

<script type="text/template" id="empresas_tercerizadas_edit_mini_panel_template">
<div class="panel pb0 mb0">
  <div class="panel-body">
    <div class="oh m-b">
      <h4 class="h4 pull-left">Nueva empresa</h4>
      <i class="pull-right fa fa-times text-muted cp cerrar"></i>
    </div>
    <div class="form-group">
      <input placeholder="Nombre" type="text" name="nombre" class="form-control tab" id="empresas_tercerizadas_mini_nombre" value="<%= nombre %>"/>
    </div>
    <div class="form-group tar mb0">
      <button class="btn guardar tab btn-success">Guardar</button>
    </div>
  </div>
</div>
</script>