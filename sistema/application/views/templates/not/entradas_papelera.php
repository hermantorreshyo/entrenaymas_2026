<script type="text/template" id="papelera_reciclaje_entradas_template">
<div class="seccion_llena">
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <h1 class="m-n font-thin h3"><i class="fa fa-file-text icono_principal"></i>Papelera de Reciclaje</h1>
  </div>
  <div class="wrapper-md ng-scope">
    <div class="panel panel-default">
      <div class="panel-heading clearfix">
        <div class="row">
          <div class="col-md-6 col-lg-3 sm-m-b">
            <div class="input-group">
              <input type="text" id="entradas_buscar" placeholder="Buscar..." value="<%= window.entradas_filter %>" autocomplete="off" class="form-control">
              <span class="input-group-btn">
                <button class="btn btn-default"><i class="fa fa-search"></i></button>
              </span>
              <span class="input-group-btn">
                <button class="btn btn-default advanced-search-btn"><i class="fa fa-angle-double-down"></i></button>
              </span>
            </div>
          </div>
        </div>
      </div>
      <div class="advanced-search-div bg-light dk" style="<%= (window.entradas_id_categoria == 0) ? 'display:none' : 'display:block' %>">
        <div class="wrapper oh">
          <h4 class="m-t-xs"><i class="fa fa-search"></i> B&uacute;squeda Avanzada:</h4>
          <div class="form-inline">
            <div style="width: 250px; display: inline-block">
              <select id="entradas_buscar_categorias" class="w100p"></select>
            </div>
            <div class="form-group">
              <button id="entradas_buscar_avanzada_btn" class="btn btn-default"><i class="fa fa-search"></i> Buscar</button>
            </div>
          </div>
        </div>
      </div>
      <% if (!seleccionar) { %>
        <div class="bulk_action wrapper pb0">
          <button class="btn btn-default eliminar_lote btn-addon">Eliminar</button>
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
                <th class="w50 tac">Imagen</th>
                <th class="sorting" data-sort-by="titulo">Titulo</th>
                <th class="sorting" data-sort-by="categoria">Categoria</th>
                <th class="sorting" data-sort-by="A.fecha">Fecha</th>
                <% if (!seleccionar) { %>
                  <th class="th_acciones w150">Acciones</th>
                <% } %>
              </tr>
            </thead>
            <tbody class="tbody"></tbody>
            <tfoot class="pagination_container hide-if-no-paging"></tfoot>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
</script>

<script type="text/template" id="papelera_reciclaje_entrada_item_template">
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
  <% } %>
  <td class="<%= clase %> data">
    <% if (!isEmpty(path)) { %>
      <img src="<%= show_path(path) %>" class="customcomplete-image"/>
    <% } %>
  </td>
  <td class="<%= clase %> data">
    <span class="text-info"><%= titulo %></span>
    <% if (ID_EMPRESA == 225 && nivel_importancia == 1) { %>
      <span class="label bg-success">Portada</span>
    <% } %>
  </td>
  <td class="<%= clase %> data"><%= categoria %></td>
  <td class="<%= clase %> data"><%= fecha %></td>
  <% if (!seleccionar) { %>
    <td class="tar <%= clase %>">
      <a target="_blank" href="http://<%= String(DOMINIO+'/'+link+'?preview=1').replace('//','/') %>"><i title="Ir a pagina" class="fa-external-link iconito fa"></i></a>
      <div class="btn-group dropdown">
        <i title="Opciones" class="iconito fa fa-caret-down dropdown-toggle" data-toggle="dropdown"></i>
        <ul class="dropdown-menu pull-right">
          <li><a href="javascript:void(0)" class="restaurar" data-id="<%= id %>">Restaurar</a></li>
          <li><a href="javascript:void(0)" class="eliminar" data-id="<%= id %>">Eliminar definitivamente</a></li>
        </ul>
      </div>
    </td>
  <% } %>
</script>
