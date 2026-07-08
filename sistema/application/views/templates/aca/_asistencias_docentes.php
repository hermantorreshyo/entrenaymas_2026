<script type="text/template" id="asistencias_docentes_panel_template">
<div class="bg-light lter b-b wrapper-md">
  <% var modulo = control.get("asistencias_docentes") %>
  <h1 class="m-n font-thin h3"><i class="<%= modulo.clase %> icono_principal"></i><%= modulo.title %></h1>
</div>
<div class="wrapper-md">
  <div class="centrado rform">
    <div class="panel panel-default">
      <div class="panel-heading clearfix">
        <div class="form-inline">
          <% if (ASISTENCIA_DOCENTE_POR_MATERIA == 1) { %>
            <div class="form-group dib w200">
              <select id="asistencias_docentes_buscar_comisiones" class="w100p"></select>
            </div>
            <div class="form-group dib w200">
              <select id="asistencias_docentes_buscar_materias" class="w100p form-control">
                <option value="0">Seleccionar materia</option>
              </select>
            </div>
          <% } %>
          <div class="form-group dib w150">
            <div class="input-group">
              <input type="text" id="asistencias_docentes_buscar_fecha" class="form-control">
              <span class="input-group-btn">
                <button class="btn btn-cal btn-default"><i class="glyphicon glyphicon-calendar"></i></button>
              </span>
            </div>
          </div>
          <button class="btn btn-default buscar">Ver Planilla</button>
        </div>
      </div>
      <div class="panel-body">
        <div class="b-a table-responsive">
          <table id="asistencias_docentes_table" class="table table-striped sortable m-b-none default footable">
            <thead>
              <tr>
                <th class="hidden-xs" style="width: 20px;">#</th>
                <th class="hidden-xs" style="width: 20px;"></th>
                <th>Docente</th>
                <th style="min-width: 400px; max-width: 400px; width: 400px">Asistencia</th>
                <th style="width: 300px;">Observaciones</th>
              </tr>
            </thead>
            <tbody class="tbody"></tbody>
          </table>
        </div>
      </div>
      <div class="panel-footer clearfix tar">
        <div class="fl">
          <span>Asistencias:</span>
          <b id="asistencias_docentes_asistencia"></b>
          <span class="m-l">Inasistencias:</span>
          <b id="asistencias_docentes_inasistencia"></b>
        </div>
        <button class="btn btn-default imprimir">Imprimir Planilla</button>
        <button class="btn btn-success guardar">Guardar</button>
      </div>
    </div>
  </div>
</div>    
</script>

<script type="text/template" id="asistencias_docentes_item_template">
  <input type="hidden" class="id_clase" value="<%= id_clase %>">
  <input type="hidden" class="fecha" value="<%= fecha %>">
  <input type="hidden" class="id_docente" value="<%= id_docente %>">
  <td class="ver hidden-xs"><%= numero %></td>
  <td class="ver hidden-xs">
    <% if (!isEmpty(path)) { %>
      <img src="/sistema/<%= path %>" class="customcomplete-image"/>
    <% } else { %>
      <span class="avatar xs avatar-texto bg-info %> pull-left">
        <%= isEmpty(nombre) ? "" : nombre.substr(0,1) %>
      </span>
    <% } %>
  </td>
  <td class="ver"><span class='text-info'><%= nombre %></span></td>
  <td>
    <div class="btn-group">
      <label data-valor="P" class="btn <%= (condicion=='P')?'active btn-success':'btn-default' %> condicion">Presente</label>
      <label data-valor="T" class="btn <%= (condicion=='T')?'active btn-warning':'btn-default' %> condicion">Tarde</label>
      <label data-valor="A" class="btn <%= (condicion=='A')?'active btn-danger':'btn-default' %> condicion">Ausente</label>
      <label data-valor="J" class="btn <%= (condicion=='J')?'active btn-primary':'btn-default' %> condicion">Aus. c/ justificacion</label>
    </div>
  </td>
  <td>
    <input type="text" value="<%= observaciones %>" class="form-control observaciones no-model">
  </td>
</script>
