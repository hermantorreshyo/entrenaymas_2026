<script type="text/template" id="asistencias_panel_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <% var modulo = control.get("comisiones") %>
  <h1 class="m-n font-thin h3"><i class="<%= modulo.clase %> icono_principal"></i><%= modulo.title %>
    / <%= nombre %> / <b>Asistencias</b>
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
      <li class="active">
        <a href="javascript:void(0)" role="tab" data-toggle="tab">
          <i class="fa text-info fa-address-book m-r-xs"></i>
          Asistencias
        </a>
      </li>
      <li>
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
          <a class="btn btn-default m-r-xs" href="app/#asistencias/<%= id %>"><i class="fa fa-reply m-r-xs"></i>Volver</a>
          <% if (ASISTENCIA_ALUMNO_POR_MATERIA == 1) { %>
            <div class="form-group dib w200">
              <select id="asistencias_buscar_materias" class="w100p form-control">
                <option value="0">Seleccionar materia</option>
              </select>
            </div>
          <% } %>
          <div class="form-group dib w150">
            <div class="input-group">
              <input type="text" id="asistencias_buscar_fecha" class="form-control">
              <span class="input-group-btn">
                <button class="btn btn-cal btn-default"><i class="glyphicon glyphicon-calendar"></i></button>
              </span>
            </div>
          </div>

          <div class="fr">
            <button class="btn btn-default imprimir"><i class="fa fa-print m-r-xs"></i> Imprimir</button>
            <button class="btn btn-success guardar">&nbsp;&nbsp;&nbsp;Guardar&nbsp;&nbsp;&nbsp;</button>
          </div>

        </div>
        <div class="b-a table-responsive">
          <table id="asistencias_table" class="table table-striped sortable m-b-none default footable">
            <thead>
              <tr>
                <th class="hidden-xs" style="width: 20px;">#</th>
                <th class="hidden-xs" style="width: 20px;"></th>
                <th>Alumno</th>
                <th style="min-width: 400px; max-width: 400px; width: 400px">Asistencia</th>
                <th style="width: 300px;">Observaciones</th>
              </tr>
            </thead>
            <tbody class="tbody"></tbody>
          </table>
        </div>
        <div class="panel-footer clearfix tar">
          <div class="fl">
            <span>Asistencias:</span>
            <b id="asistencias_asistencia"></b>
            <span class="m-l">Inasistencias:</span>
            <b id="asistencias_inasistencia"></b>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>    
</script>

<script type="text/template" id="asistencias_item_template">
  <input type="hidden" class="id_clase" value="<%= id_clase %>">
  <input type="hidden" class="fecha" value="<%= fecha %>">
  <input type="hidden" class="id_alumno" value="<%= id_alumno %>">
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

<script type="text/template" id="asistencias_reporte_panel_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <% var modulo = control.get("comisiones") %>
  <h1 class="m-n font-thin h3"><i class="<%= modulo.clase %> icono_principal"></i><%= modulo.title %>
    / <%= nombre %> / <b>Asistencias</b>
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
      <li class="active">
        <a href="javascript:void(0)" class="buscar_todos" role="tab" data-toggle="tab">
          <i class="fa text-info fa-address-book m-r-xs"></i>
          Asistencias
        </a>
      </li>
      <li>
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
          <% if (ASISTENCIA_ALUMNO_POR_MATERIA == 1) { %>
            <div class="form-group dib w200">
              <select id="asistencias_reporte_materias" class="w100p form-control">
                <option value="0">Seleccionar materia</option>
              </select>
            </div>
          <% } %>
          <div class="form-group dib w180">
            <select class="trimestre_select form-control w100p no-model">
              <% for(var i=0;i< trimestres.length; i++) { %>
                <% var t = trimestres[i] %>
                <% var selected = moment().isBetween(moment(t.fecha_desde,"DD/MM/YYYY"), moment(t.fecha_hasta,"DD/MM/YYYY"));%>
                <option <%= (selected)?"selected":"" %> value="<%= t-id %>" data-desde="<%= t.fecha_desde %>" data-hasta="<%= t.fecha_hasta %>"><%= t.nombre %></option>
              <% } %>
              <option data-desde="<%= moment().subtract(3,'months').format('DD/MM/YYYY') %>" data-hasta="<%= moment().format('DD/MM/YYYY') %>" value="0">Rango de fechas</option>
            </select>
          </div>
          <div class="form-group dib w150">
            <div class="input-group">
              <input placeholder="Desde" value="<%= fecha_desde %>" type="text" id="asistencias_reporte_fecha_desde" class="form-control">
              <?php /*
              <span class="input-group-btn">
                <button class="btn btn-cal btn-default"><i class="glyphicon glyphicon-calendar"></i></button>
              </span>*/ ?>
            </div>
          </div>
          <div class="form-group dib w150">
            <div class="input-group">
              <input placeholder="Hasta" value="<%= fecha_hasta %>" type="text" id="asistencias_reporte_fecha_hasta" class="form-control">
              <?php /*
              <span class="input-group-btn">
                <button class="btn btn-cal btn-default"><i class="glyphicon glyphicon-calendar"></i></button>
              </span>*/?>
            </div>
          </div>

          <div class="fr">
            <button class="btn m-r-xs btn-default imprimir"><i class="fa fa-print m-r-xs"></i> Imprimir</button>
            <a class="btn btn-info btn-addon" href="app/#asistencia/<%= id %>">
              <i class="fa fa-plus"></i><span>&nbsp;&nbsp;Cargar Asistencia&nbsp;&nbsp;</span>
            </a>
          </div>
        </div>
        <div id="asistencias_reporte_tabla"></div>
        <?php /*
        <div class="panel-footer clearfix tar">
          <div class="fl">
            <span>Asistencias:</span>
            <b id="asistencias_asistencia"></b>
            <span class="m-l">Inasistencias:</span>
            <b id="asistencias_inasistencia"></b>
          </div>
        </div>
        */ ?>
      </div>
    </div>
  </div>
</div>    
</script>

<script type="text/template" id="asistencias_reporte_tabla_template">
<div class="">
  <div class="col-xs-4 col-sm-3 p0">
    <div class="b-a oh">
      <table id="reporte_asistencias_table_nombres" class="table table-striped sortable m-b-none default footable">
        <thead>
          <tr>
            <th class="hidden-xs" style="width: 20px;">#</th>
            <th class="hidden-xs" style="width: 20px;"></th>
            <th style="min-width: 180px;">Alumno</th>
          </tr>
        </thead>
        <tbody class="tbody"></tbody>
      </table>
    </div>
  </div>
  <div class="col-xs-8 col-sm-9 p0">
    <div class="b-a table-responsive">
      <table id="reporte_asistencias_table" class="table table-striped sortable m-b-none default footable">
        <thead>
          <tr>
            <% for(i=0;i< clases.length;i++) { %>
              <% var c = clases[i] %>
              <th style="min-width: 75px; padding-left: 0px; padding-right: 0px; text-align: center;">
                <input type="checkbox" id="check_<%= i %>" class="check_fecha m0" value="<%= moment(c.fecha).format('DD/MM') %>" /> 
                <label for="check_<%= i %>" class="fs14 bold mb0 cp"><%= moment(c.fecha).format("DD/MM") %></label>
              </th>
            <% } %>
          </tr>
        </thead>
        <tbody class="tbody"></tbody>
      </table>
    </div>
  </div>
</div>
</script>

<script type="text/template" id="asistencias_reporte_item_template">
  <% for(i=0;i< clases.length;i++) { %>
    <% var as = clases[i] %>
    <% var color = "" %>
    <% if (as.condicion=='P') { color = "bg-success"; } %>
    <% if (as.condicion=='T') { color = "bg-warning"; } %>
    <% if (as.condicion=='A') { color = "bg-danger"; } %>
    <% if (as.condicion=='J') { color = "bg-primary"; } %>
    <td class="<%= color %> tac" style="padding: 0px">
      <%= as.condicion %>
      <%= (isEmpty(as.observaciones)) ? "" : "<i data-toggle='tooltip' title='"+as.observaciones+"' class='fa blanco fa-commenting'></i>" %>
    </td>
  <% } %>
</script>
