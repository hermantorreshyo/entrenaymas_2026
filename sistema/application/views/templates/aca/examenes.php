<script type="text/template" id="examenes_reporte_panel_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <% var modulo = control.get("comisiones") %>
  <h1 class="m-n font-thin h3"><i class="<%= modulo.clase %> icono_principal"></i><%= modulo.title %>
    / <%= nombre %> / <b>Ex&aacute;menes</b>
  </h1>
</div>
<div class="wrapper-md">
  <div class="panel panel-default">
    <ul class="nav nav-tabs nav-tabs-2" role="tablist">
      <li>
        <a href="app/#comision_calendario/<%= id %>">
          <i class="fa text-warning fa-calendar m-r-xs"></i>
          Cronograma
        </a>
      </li>
      <li>
        <a href="app/#asistencias/<%= id %>">
          <i class="fa text-info fa-address-book m-r-xs"></i>
          Asistencias
        </a>
      </li>
      <li class="active">
        <a href="app/#examenes/<%= id %>">
          <i class="fa text-success fa-file-text m-r-xs"></i>
          Exámenes
        </a>
      </li>
      <li>
        <a href="app/#comisiones">
          <i class="fa text-danger fa-share m-r-xs"></i>
          Volver a comisiones
        </a>
      </li>
    </ul>
    <div class="tab-content">
      <div id="tab1" class="tab-pane panel-body active">
        <div class="form-inline m-b">
          <div class="form-group dib w200">
            <select id="examenes_reporte_materias" class="w100p form-control">
              <option value="0">Seleccionar materia</option>
            </select>
          </div>
          <div class="form-group dib w180">
            <select class="trimestre_select form-control w100p no-model">
              <% for(var i=0;i< trimestres.length; i++) { %>
                <% var t = trimestres[i] %>
                <option value="<%= t-id %>" data-desde="<%= t.fecha_desde %>" data-hasta="<%= t.fecha_hasta %>"><%= t.nombre %></option>
              <% } %>
              <option data-desde="<%= moment().subtract(3,'months').format('DD/MM/YYYY') %>" data-hasta="<%= moment().format('DD/MM/YYYY') %>" value="0">Rango de fechas</option>
            </select>
          </div>
          <div class="form-group dib w150">
            <div class="input-group">
              <input placeholder="Desde" value="<%= fecha_desde %>" type="text" id="examenes_reporte_fecha_desde" class="form-control">
              <?php /*
              <span class="input-group-btn">
                <button class="btn btn-cal btn-default"><i class="glyphicon glyphicon-calendar"></i></button>
              </span>*/ ?>
            </div>
          </div>
          <div class="form-group dib w150">
            <div class="input-group">
              <input placeholder="Hasta" value="<%= fecha_hasta %>" type="text" id="examenes_reporte_fecha_hasta" class="form-control">
              <?php /*
              <span class="input-group-btn">
                <button class="btn btn-cal btn-default"><i class="glyphicon glyphicon-calendar"></i></button>
              </span>*/ ?>
            </div>
          </div>
          <div class="fr">
            <button class="btn btn-default m-r-xs imprimir"><i class="fa fa-print m-r-xs"></i> Imprimir</button>
            <button class="btn btn-info nuevo btn-addon"><i class="fa fa-plus"></i> Nuevo Examen</button>
          </div>
        </div>
        <div id="reporte_examenes_table"></div>
      </div>
      <div class="panel-footer clearfix tar">
        <?php /*
        <div class="fl">
          <span>Asistencias:</span>
          <b id="examenes_asistencia"></b>
          <span class="m-l">Inexamenes:</span>
          <b id="examenes_inasistencia"></b>
        </div>
        */ ?>
      </div>
    </div>
  </div>
</div>    
</script>

<script type="text/template" id="reporte_examenes_table_template">
  <table class="table table-striped sortable m-b-none default footable">
    <thead>
      <tr>
        <th class="hidden-xs" style="width: 20px;">#</th>
        <th class="hidden-xs" style="width: 20px;"></th>
        <th style="min-width: 180px;">Alumno</th>
        <% for(i=0;i< examenes.length;i++) { %>
          <% var c = examenes[i] %>
          <% c.nombre = c.nombre.ucwords() %>
          <th title="<%= c.nombre %>">
            <%= (c.nombre.length > 20) ? c.nombre.substr(0,20)+"..." : c.nombre %>
            <span class="text-muted fwn m-l-sm"><%= c.fecha.substr(0,5) %></span>
          </th>
        <% } %>
        <th class="th_acciones w140">Promedio</th>
      </tr>
    </thead>
    <tbody class="tbody"></tbody>
  </table>
</script>

<script type="text/template" id="examenes_reporte_item_template">
  <td class="ver hidden-xs"><%= numero %></td>
  <td class="ver hidden-xs">
    <% if (!isEmpty(path)) { %>
      <img src="/sistema/<%= path %>" class="customcomplete-image"/>
    <% } else { %>
      <span class="avatar xs avatar-texto bg-info %> pull-left">
        <%= isEmpty(nombre) ? "" : nombre.toUpperCase().substr(0,1) %>
      </span>
    <% } %>
  </td>
  <td class="ver"><span class='text-info'><%= nombre.ucwords() %></span></td>
  <% var promedio = 0 %>
  <% for(i=0;i< examenes.length;i++) { %>
    <% var as = examenes[i] %>
    <td>
      <input type="text" class="form-control no-model form-white" disabled value="<%= as.valor %>">
      <% if (!isEmpty(as.observaciones)) { %>
        <i data-toggle="tooltip" title="<%= as.observaciones %>" class="fa fa-commenting text-success m-l-xs"></i>
      <% } %>
      <a href="app/#examen/<%= as.id_examen %>" class="link m-l-xs">Editar</a>
    </td>
    <% promedio += (as.valor != "-" && as.valor != "") ? parseFloat(as.valor) : 0 %>
  <% } %>
  <td>
    <% promedio = (examenes.length > 0) ? Number(promedio / examenes.length).toFixed(2) : "" %>
    <input type="text" class="form-control no-model form-white" disabled value="<%= promedio %>">
  </td>
</script>

<script type="text/template" id="reporte_materias_table_template">
  <table class="table table-striped sortable m-b-none default footable">
    <thead>
      <tr>
        <th class="hidden-xs" style="width: 20px;">#</th>
        <th class="hidden-xs" style="width: 20px;"></th>
        <th style="min-width: 180px;">Alumno</th>
        <% for(i=0;i< materias.length;i++) { %>
          <% var c = materias[i] %>
          <% c.nombre = c.nombre.ucwords() %>
          <th style="min-width: 130px" title="<%= c.nombre %>">
            <%= (c.nombre.length > 25) ? c.nombre.substr(0,25)+"..." : c.nombre %>
          </th>
        <% } %>
        <th class="th_acciones w140">Promedio</th>
      </tr>
    </thead>
    <tbody class="tbody"></tbody>
  </table>
</script>

<script type="text/template" id="reporte_materias_item_template">
  <td class="ver hidden-xs"><%= numero %></td>
  <td class="ver hidden-xs">
    <% if (!isEmpty(path)) { %>
      <img src="/sistema/<%= path %>" class="customcomplete-image"/>
    <% } else { %>
      <span class="avatar xs avatar-texto bg-info %> pull-left">
        <%= isEmpty(nombre) ? "" : nombre.toUpperCase().substr(0,1) %>
      </span>
    <% } %>
  </td>
  <td class="ver"><span class='text-info'><%= nombre.ucwords() %></span></td>
  <% var promedio = 0 %>
  <% for(i=0;i< notas.length;i++) { %>
    <% var as = notas[i] %>
    <td>
      <input type="text" class="form-control no-model form-white" disabled value="<%= as.promedio %>">
      <a href="javascript:void(0)" class="link m-l-xs cp ver_examen" data-id_materia="<%= as.id_materia %>">Ver</a>
    </td>
    <% promedio += (as.promedio != "-" && as.promedio != "") ? parseFloat(as.promedio) : 0 %>
  <% } %>
  <td>
    <% promedio = (notas.length > 0) ? Number(promedio / notas.length).toFixed(2) : "" %>
    <input type="text" class="form-control no-model form-white" disabled value="<%= promedio %>">
  </td>
</script>

<script type="text/template" id="examen_panel_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <% var modulo = control.get("comisiones") %>
  <h1 class="m-n font-thin h3"><i class="<%= modulo.clase %> icono_principal"></i><%= modulo.title %>
    / <%= window.comision.get("nombre") %> / Ex&aacute;menes / <b><%= (id == undefined || id == 0) ? "Nuevo" : nombre %></b>
  </h1>
</div>
<div class="wrapper-md">
  <div class="centrado rform">
    <div class="row">
      <div class="col-md-4"></div>
      <div class="col-md-8">
        <div class="panel panel-default">
          <div class="panel-body">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label class="control-label">Nombre</label>
                  <input type="text" name="nombre" class="form-control" id="examen_nombre" value="<%= nombre %>"/>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label class="control-label">Fecha</label>
                  <div class="input-group">
                    <input type="text" name="fecha" class="form-control" id="examen_fecha" value="<%= fecha %>"/>
                    <span class="input-group-btn">
                      <button class="btn btn-cal btn-default"><i class="glyphicon glyphicon-calendar"></i></button>
                    </span>
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label class="control-label">Tipo de calificacion</label>
                  <select id="examen_comisiones" class="form-control">
                    <option <%= (numerico==1)?"selected":"" %> value="1">Numerica</option>
                  </select>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label class="control-label">Aprueba con</label>
                  <input type="text" value="<%= aprueba_con %>" name="aprueba_con" id="examen_aprueba_con" class="form-control">
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="panel panel-default">
          <div class="panel-body">
            <div class="b-a table-responsive">
              <table id="examenes_table" class="table table-striped sortable m-b-none default footable">
                <thead>
                  <tr>
                    <th class="hidden-xs" style="width: 20px;">#</th>
                    <th class="hidden-xs" style="width: 20px;"></th>
                    <th>Alumno</th>
                    <th>Nota</th>
                    <th style="width: 220px;">Observaciones</th>
                  </tr>
                </thead>
                <tbody class="tbody"></tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-4"></div>
      <div class="col-md-8 tar">
        <?php /*
        <div class="panel-footer clearfix tar">
          <div class="fl">
            <span>Aprobados:</span>
            <b id="examenes_aprobados"></b>
            <span class="m-l">Desaprobados:</span>
            <b id="examenes_desaprobados"></b>
          </div>
        </div>
        */ ?>
        <% if (id != undefined && id != 0) { %>
          <button class="btn btn-default imprimir m-r-xs"><i class="fa fa-print m-r-xs"></i> Imprimir</button>
        <% } %>
        <button class="btn btn-success guardar">Guardar</button>
      </div>
    </div>
  </div>
</div>    
</script>

<script type="text/template" id="examen_item_template">
  <input type="hidden" class="id_examen" value="<%= id_examen %>">
  <input type="hidden" class="id_alumno" value="<%= id_alumno %>">
  <td class="ver hidden-xs"><%= numero %></td>
  <td class="ver hidden-xs">
    <% if (!isEmpty(path)) { %>
      <img src="/sistema/<%= path %>" class="customcomplete-image"/>
    <% } else { %>
      <span class="avatar xs avatar-texto bg-info %> pull-left">
        <%= isEmpty(nombre) ? "" : nombre.substr(0,1).toUpperCase() %>
      </span>
    <% } %>
  </td>
  <td class="ver"><span class='text-info'><%= nombre.ucwords() %></span></td>
  <td>
    <input type="text" value="<%= valor %>" class="form-control form-white valor no-model">
  </td>
  <td>
    <input type="text" value="<%= observaciones %>" class="form-control observaciones no-model">
  </td>
</script>