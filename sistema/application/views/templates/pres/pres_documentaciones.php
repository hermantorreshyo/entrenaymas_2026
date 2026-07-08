<script type="text/template" id="pres_documentaciones_panel_template">
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <h1 class="m-n font-thin h3"><i class="fa fa-cog icono_principal"></i>Configuraci&oacute;n
      / <b>Tipo de documentaci&oacute;n</b>
    </h1>
  </div>
  <div class="wrapper-md ng-scope">
    <div class="panel panel-default">
    
      <div class="panel-heading oh">
        <div class="row">
          <div class="col-md-6 col-lg-3 sm-m-b">
            <div class="search_container"></div>
          </div>
          <div class="col-md-6 col-lg-offset-3 col-lg-6 text-right">
            <a class="btn btn-info btn-addon" href="app/#pres_documentacion"><i class="fa fa-plus"></i>&nbsp;&nbsp;Nuevo&nbsp;&nbsp;</a>
          </div>
        </div>
      </div>
      <div class="panel-body">
        <div class="b-a table-responsive">
          <table id="pres_documentaciones_table" class="table table-striped sortable m-b-none default footable">
            <thead>
              <tr>
                <th style="width:20px;">
                  <label class="i-checks m-b-none">
                    <input class="esc sel_todos" type="checkbox"><i></i>
                  </label>
                </th>
                <th class="sorting" data-sort-by="nombre">Nombre</th>
                <th class="sorting" data-sort-by="nombre">D&iacute;as para pedir renovaci&oacute;n</th>
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


<script type="text/template" id="pres_documentaciones_item">
  <td>
    <label class="i-checks m-b-none">
      <input class="esc check-row" value="<%= id %>" type="checkbox"><i></i>
    </label>
  </td>
  <td class="ver"><span class='text-info'><%= nombre %></span> <%= (obligatoria==1)?"(Oblig.)":"" %></td>
  <td class="ver"><span class=''><%= dias_renovacion %></span></td>
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

<script type="text/template" id="pres_documentaciones_edit_panel_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <h1 class="m-n font-thin h3"><i class="fa fa-cog icono_principal"></i>Configuraci&oacute;n 
    / Tipo de documentaci&oacute;n
    / <b><%= (id == undefined) ? 'Nueva' : nombre %></b>
  </h1>
</div>
<div class="wrapper-md ng-scope">
  <div class="centrado rform">
    <div class="row">
      <div class="col-md-4">
        <div class="detalle_texto"></div>
      </div>
      <div class="col-md-8">
        <div class="panel panel-default">
          <div class="panel-body">
          
            <div class="padder">

              <div class="form-group">
                <label class="control-label">Nombre</label>
                <% if (edicion) { %>
                  <input type="text" name="nombre" class="form-control" id="pres_documentaciones_nombre" value="<%= nombre %>"/>
                <% } else { %>
                  <span><%= nombre %></span>
                <% } %>
              </div>

              <div class="form-group">
                <label class="control-label">D&iacute;as para pedir la renovacion de la documentaci&oacute;n</label>
                <% if (edicion) { %>
                  <input type="text" name="dias_renovacion" class="form-control" id="pres_documentaciones_dias_renovacion" value="<%= dias_renovacion %>"/>
                <% } else { %>
                  <span><%= dias_renovacion %></span>
                <% } %>
                <span class="text-muted fs14">(0 = No se pide renovaci&oacute;n)</span>
              </div>

              <div class="form-group">
                <div class="checkbox mt0 mb0">
                  <label class="i-checks">
                    <input type="checkbox" id="pres_documentaciones_obligatoria" class="checkbox no-model" value="1" <%= (obligatoria == 1)?"checked":"" %>>
                    <i></i>
                    Documentaci&oacute;n obligatoria
                  </label>
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