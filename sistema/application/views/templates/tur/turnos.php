<script type="text/template" id="turnos_template">
<div class="hbox hbox-auto-xs hbox-auto-sm">
  <div class="col">
    <div class="bg-light lter b-b wrapper-md">
      <% var modulo = control.get("turnos") %>
      <h1 class="m-n font-thin h3"><i class="<%= modulo.clase %> icono_principal"></i><%= modulo.title %></h1>
    </div>
    <div class="wrapper-md pb0">
      <div class="panel panel-default">
        <ul class="nav nav-tabs nav-tabs-2" role="tablist">
          <li>
            <a href="app/#turnos"><i class="fa fa-clock-o text-info"></i> Listado de Turnos</a>
          </li>
          <li class="active">
            <a href="app/#turnos_calendario"><i class="glyphicon glyphicon-calendar text-warning"></i> Calendario</a>
          </li>
        </ul>
        <div class="panel-heading clearfix">
          <div class="row">
            <div class="col-md-6 col-lg-4 sm-m-b">
              <div class="input-group">
                <select class="w100p no-model" id="turnos_servicios">
                  <?php /*<option data-hora_desde="08:00:00" data-hora_hasta="17:00:00" data-duracion_turno="0" value="0">Todos</option>*/ ?>
                  <% for(var i=0;i< turnos_servicios.length; i++) { %>
                    <% var p = turnos_servicios[i] %>
                    <% if (SOLO_USUARIO == 0 || (SOLO_USUARIO == 1 && p.id_usuario == ID_USUARIO)) { %>
                      <option data-hora_desde="<%= (isEmpty(p.hora_desde) ? '' : p.hora_desde) %>" data-hora_hasta="<%= (isEmpty(p.hora_hasta) ? '' : p.hora_hasta) %>" data-duracion_turno="<%= (isEmpty(p.duracion_turno) ? 15 : p.duracion_turno) %>" value="<%= p.id %>"><%= p.nombre %></option>
                    <% } %>
                  <% } %>
                </select>
                <span class="input-group-btn">
                  <button class="btn btn-default"><i class="fa fa-search"></i></button>
                </span>
              </div>
            </div>
            <div class="col-md-6 col-lg-8 sm-m-b tar">
              <span class="mr5">Marcar</span>
              <div class="btn-group">
                <label id="turnos_servicios_marcar_turnos" class="btn active btn-default condicion">Turnos</label>
                <label id="turnos_servicios_marcar_no_disponible" class="btn btn-default condicion">Horario no disponible</label>
              </div>
            </div>
          </div>
        </div>
        <div class="panel-body">
          <div id="calendar"></div>
        </div>
      </div>
    </div>
  </div>
</div>
</script>

<script type="text/template" id="turno_edit_panel_template">
<div class="panel panel-default">
  <div class="panel-heading oh">
    <span class="bold fl mt5">Turno para <%= servicio %></span>
    <button class="fr btn btn-default cerrar"><i class="fa fa-times"></i></button>
  </div>
  <div class="panel-body">
    <div class="">
      <div class="form-group">
        <label class="control-label"><%= (ID_PROYECTO == 7)?"Paciente":"Cliente" %></label>
        <input type="hidden" value="<%= id_cliente %>" id="turno_id_cliente">
        <input type="text" placeholder="Escriba el nombre y seleccione de la lista..." value="<%= cliente %>" id="turno_clientes" class="form-control no-model">
      </div>
      <div class="row">
        <div class="col-md-4">
          <div class="form-group">
            <label class="control-label">Fecha</label>
            <div class="input-group">
              <input type="text" name="fecha" class="form-control" id="turno_fecha" value="<%= fecha %>"/>
              <span class="input-group-btn">
                <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
              </span>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-group">
            <label class="control-label">Hora</label>
            <div class="input-group">
              <input type="text" class="form-control" value="<%= hora %>" id="turno_hora" name="hora"/>
              <span class="input-group-btn">
                <div class="btn-group dropdown pull-right ml0">
                  <button class="btn btn-default dropdown-toggle btn-addon" data-toggle="dropdown">
                    <span class="caret"></span>
                  </button>
                  <ul id="turno_horarios" class="dropdown-menu">
                  </ul>
                </div>
              </span>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-group">
            <label class="control-label">&nbsp;</label>
            <div class="checkbox mt5">
              <label class="i-checks">
                <input type="checkbox" id="turno_sin_horario" name="sin_horario" class="checkbox" value="1" <%= (sin_horario == 1)?"checked":"" %> >
                <i></i>
                Turno sin horario
              </label>
            </div>
          </div>
        </div>
      </div>
      <div class="form-group">
        <label class="control-label">Observaciones</label>
        <textarea placeholder="Escriba aqui alguna nota o recordatorio..." id="turno_observaciones" class="form-control h60 no-model"><%= observaciones %></textarea>
      </div>
      <?php /*
      <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            <label class="control-label">Duracion</label>
            <input type="number" min="0" value="<%= duracion_cantidad %>" class="form-control no-model" id="turno_duracion_cantidad"/>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label class="control-label">&nbsp;</label>
            <select id="turno_duracion_tipo" class="form-control no-model">
              <option <%= (duracion_tipo=="M")?"selected":"" %> value="M">Minutos</option>
              <option <%= (duracion_tipo=="H")?"selected":"" %> value="H">Horas</option>
            </select>
          </div>
        </div>
      </div>  
      <% if (id == undefined) { %>
        <div class="form-group">
            <label class="control-label">Periodicidad</label>
            <div class="col-md-9">
              <div class="row">
                <div class="col-xs-6">
                  <select class="form-control no-model" id="turno_repeticion">
                    <option value="0">No repetir</option>
                    <option value="1">Cada una semana</option>
                    <option value="2">Cada 2 semanas</option>
                    <option value="3">Cada 3 semanas</option>
                    <option value="4">Cada 1 mes</option>
                  </select>
                </div>
                <div class="col-xs-6">
                  <input type="text" class="form-control no-model" placeholder="Hasta" id="turno_fecha_hasta"/>
                </div>
              </div>
            </div>
        </div>    
      <% } %>
      */ ?>
    </div>
  </div>
  <div class="panel-footer clearfix">
    <button class="btn guardar btn-success fr">Guardar</button>
    <% if (id != undefined) { %>
      <button class="btn btn-danger eliminar fl">Eliminar</button>
      <button class="btn mr5 imprimir btn-default fr">Imprimir</button>
    <% } %>
  </div>
</div>
</script>


<script type="text/template" id="turnos_panel_template">
  <div class="bg-light lter b-b wrapper-md">
    <% var modulo = control.get("turnos") %>
    <h1 class="m-n font-thin h3"><i class="<%= modulo.clase %> icono_principal"></i><%= modulo.title %></h1>
  </div>
  <div class="wrapper-md ng-scope">
    <div class="panel panel-default">
    
      <ul class="nav nav-tabs nav-tabs-2" role="tablist">
        <li class="active">
          <a href="app/#turnos"><i class="fa fa-clock-o text-info"></i> Listado de Turnos</a>
        </li>
        <li>
          <a href="app/#turnos_calendario"><i class="glyphicon glyphicon-calendar text-warning"></i> Calendario</a>
        </li>
      </ul>

      <div class="panel-heading clearfix">
        <div class="row">
          <div class="col-md-4 col-lg-3 sm-m-b">
            <select class="w100p no-model" id="turnos_servicios">
              <?php /*<option data-hora_desde="08:00:00" data-hora_hasta="17:00:00" data-duracion_turno="0" value="0">Todos</option>*/ ?>
              <% for(var i=0;i< turnos_servicios.length; i++) { %>
                <% var p = turnos_servicios[i] %>
                <% if (SOLO_USUARIO == 0 || (SOLO_USUARIO == 1 && p.id_usuario == ID_USUARIO)) { %>
                  <option data-hora_desde="<%= (isEmpty(p.hora_desde) ? '' : p.hora_desde) %>" data-hora_hasta="<%= (isEmpty(p.hora_hasta) ? '' : p.hora_hasta) %>" data-duracion_turno="<%= (isEmpty(p.duracion_turno) ? 15 : p.duracion_turno) %>" value="<%= p.id %>"><%= p.nombre %></option>
                <% } %>
              <% } %>
            </select>
          </div>
          <div class="col-md-4 col-lg-3 sm-m-b">
            <div class="search_container"></div>
          </div>
          <% if (control.check("turnos") > 1) { %>
            <div class="col-md-4 col-lg-offset-3 col-lg-3 text-right">
              <a class="btn btn-info btn-addon nuevo" href="javascript:void(0)"><i class="fa fa-plus"></i>&nbsp;&nbsp;Nuevo&nbsp;&nbsp;</a>
            </div>
          <% } %>
        </div>
      </div>

      <div class="panel-body">
        <div class="b-a table-responsive">
          <table id="turnos_table" class="table table-striped sortable m-b-none default footable">
            <thead>
              <tr>
                <th style="width:20px;">
                  <label class="i-checks m-b-none">
                    <input class="esc sel_todos" type="checkbox"><i></i>
                  </label>
                </th>
                <th class="sorting" data-sort-by="nombre">Nombre</th>
                <th class="sorting" data-sort-by="servicio">Servicio</th>
                <th class="sorting" data-sort-by="nombre">Fecha</th>
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


<script type="text/template" id="turnos_item">
  <td>
    <label class="i-checks m-b-none">
      <input class="esc check-row" value="<%= id %>" type="checkbox"><i></i>
    </label>
  </td>
  <td class="ver"><span class='text-info'><%= nombre %></span></td>
  <td class="ver"><%= servicio %></td>
  <td class="ver"><%= fecha %></td>
  <% if (permiso > 1) { %>
    <td class="p5 td_acciones">
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