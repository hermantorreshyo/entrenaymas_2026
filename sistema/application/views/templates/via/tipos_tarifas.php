<script type="text/template" id="tipos_tarifas_panel_template">
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <h1 class="m-n font-thin h3"><i class="fa fa-tags icono_principal"></i> Tipos de Tarifas</h1>
  </div>
  <div class="wrapper-md ng-scope">
    <div class="panel panel-default">

      <div class="panel-heading oh">
        <div class="row">
          <div class="col-md-6 col-lg-3 sm-m-b">
            <div class="search_container"></div>
          </div>
          <div class="col-md-6 col-lg-offset-3 col-lg-6 text-right">
            <a class="btn btn-info btn-addon" href="app/#tipo_tarifa"><i class="fa fa-plus"></i>Nuevo</a>
          </div>
        </div>
      </div>
      <div class="panel-body">
        <div class="b-a table-responsive">
          <table id="tipos_tarifas_table" class="table table-striped sortable m-b-none default footable">
            <thead>
              <tr>
                <th style="width:20px;">
                  <label class="i-checks m-b-none">
                    <input class="esc sel_todos" type="checkbox"><i></i>
                  </label>
                </th>
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


<script type="text/template" id="tipos_tarifas_item">
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

<script type="text/template" id="tipos_tarifas_edit_panel_template">
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <h1 class="m-n font-thin h3"><i class="fa fa-tags icono_principal"></i>Tipos de Tarifas /
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

                <div class="form-group lang-control">
                  <label class="control-label">Nombre</label>
                  <div class="input-group">
                    <input type="text" id="tipos_tarifas_nombre" class="form-control active" value="<%= nombre %>" name="nombre"/>
                    <input type="text" id="tipos_tarifas_nombre_en" name="nombre_en" class="form-control" id="tipos_tarifas_nombre_en" value="<%= nombre_en %>"/>
                    <input type="text" id="tipos_tarifas_nombre_pt" name="nombre_pt" class="form-control" id="tipos_tarifas_nombre_pt" value="<%= nombre_pt %>"/>
                    <div class="input-group-btn">
                      <label class="btn btn-default btn-lang active" data-id="tipos_tarifas_nombre" uncheckable=""><img title="Espa&ntilde;ol" src="resources/images/es.png"/></label>
                      <label class="btn btn-default btn-lang" data-id="tipos_tarifas_nombre_en" uncheckable=""><img title="Ingl&eacute;s" src="resources/images/en.png"/></label>
                      <label class="btn btn-default btn-lang" data-id="tipos_tarifas_nombre_pt" uncheckable=""><img title="Portugues" src="resources/images/pt.png"/></label>
                    </div>
                  </div>
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

<script type="text/template" id="tipos_tarifas_edit_mini_panel_template">
  <div class="panel pb0 mb0">
    <div class="panel-body">
      <div class="form-group">
        <input type="text" name="nombre" class="form-control tab" id="tipos_tarifas_mini_nombre" value="<%= nombre %>"/>
      </div>
      <div class="form-group">
        <button class="btn guardar tab btn-success btn-block">Guardar</button>
      </div>
    </div>
  </div>
</script>