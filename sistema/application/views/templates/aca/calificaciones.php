<script type="text/template" id="calificaciones_panel_template">
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <h1 class="m-n font-thin h3"><i class="fa fa-tags icono_principal"></i>Tipos de calificaciones</h1>
  </div>
  <div class="wrapper-md ng-scope">
    <div class="panel panel-default">
    
      <div class="panel-heading oh">
        <div class="row">
          <div class="col-md-6 col-lg-3 sm-m-b">
            <div class="search_container"></div>
          </div>
          <div class="col-md-6 col-lg-offset-3 col-lg-6 text-right">
            <a class="btn btn-info btn-addon" href="app/#calificacion"><i class="fa fa-plus"></i>&nbsp;&nbsp;Nuevo&nbsp;&nbsp;</a>
          </div>
        </div>
      </div>
      <div class="panel-body">
        <div class="b-a table-responsive">
          <table id="calificaciones_table" class="table table-striped sortable m-b-none default footable">
            <thead>
              <tr>
                <th class="sorting" data-sort-by="nombre">Nombre</th>
                <th class="sorting" data-sort-by="equivalente">Equivalente</th>
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
</script>


<script type="text/template" id="calificaciones_item">
  <td class="ver"><span class='text-info'><%= nombre %></span></td>
  <td class="ver"><%= equivalente %></td>
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

<script type="text/template" id="calificaciones_edit_panel_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <h1 class="m-n font-thin h3"><i class="fa fa-tags icono_principal"></i>Tipos de calificaciones
    / <b><%= (id == undefined) ? 'Nuevo' : nombre %></b>
  </h1>
</div>
<div class="wrapper-md ng-scope">
  <div class="centrado rform">
    <div class="row">
      <div class="col-md-4">
      </div>
      <div class="col-md-8">
        <div class="panel panel-default">
          <div class="panel-body">
            <div class="padder">
              <div class="form-group">
                <label class="control-label">Nombre</label>
                <input type="text" name="nombre" class="form-control" id="calificaciones_nombre" value="<%= nombre %>"/>
              </div>
              <div class="form-group">
                <label class="control-label">Equivalente num&eacute;rico</label>
                <input type="number" min="0" name="equivalente" class="form-control" id="calificaciones_equivalente" value="<%= equivalente %>"/>
              </div>
            </div>
          </div>
        </div>
        <% if (edicion) { %>
          <div class="tar">
            <button class="btn guardar btn-success">Guardar</button>
          </div>
        <% } %>
      </div>
    </div>
  </div>
</div>
</script>