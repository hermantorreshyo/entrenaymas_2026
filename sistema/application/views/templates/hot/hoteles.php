<script type="text/template" id="hoteles_panel_template">
  <div class="bg-light lter b-b wrapper-md ng-scope">
  <h1 class="m-n font-thin h3"><i class="fa fa-tags icono_principal"></i> Hoteles</h1>
  </div>
  <div class="wrapper-md ng-scope">
    <div class="panel panel-default">
      <div class="panel-heading oh">
        <div class="row">
          <div class="col-md-6 col-lg-3 sm-m-b">
            <div class="search_container"></div>
          </div>
          <div class="col-md-6 col-lg-offset-3 col-lg-6 text-right">
            <a class="btn btn-info btn-addon" href="app/#hotel"><i class="fa fa-plus"></i>Nuevo</a>
          </div>
        </div>
      </div>
      <div class="panel-body">
        <div class="b-a table-responsive">
          <table id="hoteles_table" class="table table-striped sortable m-b-none default footable">
            <thead>
              <tr>
                <th style="width:20px;">
                  <label class="i-checks m-b-none">
                    <input class="esc sel_todos" type="checkbox"><i></i>
                  </label>
                </th>
                <th class="sorting" data-sort-by="nombre">Nombre</th>
                <th class="sorting" data-sort-by="direccion">Direccion</th>
                <th class="sorting" data-sort-by="telefono">Telefono</th>
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


<script type="text/template" id="hoteles_item">
  <td>
    <label class="i-checks m-b-none">
      <input class="esc check-row" value="<%= id %>" type="checkbox"><i></i>
    </label>
  </td>
  <td><span class='ver text-info'><%= nombre %></span></td>
  <td><span class='ver text-info'><%= direccion %></span></td>
  <td><span class='ver text-info'><%= telefono %></span></td>
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

<script type="text/template" id="hoteles_edit_panel_template">
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <h1 class="m-n font-thin h3"><i class="fa fa-tags icono_principal"></i>Hoteles /
      <b><%= (id == undefined) ? 'Nuevo' : nombre %></b>
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
                  <label class="control-label">Nombre</label>
                  <input type="text" name="nombre" class="form-control" id="hoteles_nombre" value="<%= nombre %>"/>
                </div>
                <div class="form-group">
                  <label class="control-label">Direcci&oacute;n</label>
                  <input type="text" name="direccion" class="form-control" id="hoteles_direccion" value="<%= direccion %>"/>
                </div>
                <div class="form-group">
                  <label class="control-label">Tel&eacute;fono</label>
                  <input type="text" name="telefono" class="form-control" id="hoteles_telefono" value="<%= telefono %>"/>
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

<script type="text/template" id="hoteles_edit_mini_panel_template">
  <div class="panel pb0 mb0">
    <div class="panel-body">
      <div class="form-group">
        <input type="text" name="nombre" class="form-control tab" id="hoteles_mini_nombre" value="<%= nombre %>"/>
      </div>
      <div class="form-group">
        <input type="text" name="direccion" class="form-control tab" id="hoteles_mini_direccion" value="<%= direccion %>"/>
      </div>
      <div class="form-group">
        <input type="text" name="telefono" class="form-control tab" id="hoteles_mini_telefono" value="<%= telefono %>"/>
      </div>
      <div class="form-group">
        <button class="btn guardar tab btn-success btn-block">Guardar</button>
      </div>
    </div>
  </div>
</script>