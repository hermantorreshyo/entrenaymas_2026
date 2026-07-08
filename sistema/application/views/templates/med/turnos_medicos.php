<script type="text/template" id="turnos_medicos_template">
<div class="hbox hbox-auto-xs hbox-auto-sm">
  <div class="col">
    <div class="bg-light lter b-b wrapper-md">
      <% var modulo = control.get("turnos_medicos") %>
      <h1 class="m-n font-thin h3"><i class="<%= modulo.clase %> icono_principal"></i><%= modulo.title %></h1>
    </div>
    <div class="wrapper-md pb0">
      <div class="panel panel-default">
        <div class="panel-heading clearfix">
          <div class="row">
            <div class="col-md-6 col-lg-4 sm-m-b">
              <div class="input-group">
                <select class="w100p no-model" id="turnos_profesionales">
                  <% for(var i=0;i< profesionales.length; i++) { %>
                    <% var p = profesionales[i] %>
                    <option data-hora_desde="<%= (isEmpty(p.hora_desde) ? '' : p.hora_desde) %>" data-hora_hasta="<%= (isEmpty(p.hora_hasta) ? '' : p.hora_hasta) %>" data-duracion_turno="<%= (isEmpty(p.duracion_turno) ? 15 : p.duracion_turno) %>" value="<%= p.id %>"><%= p.apellido %> <%= p.nombre %></option>
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
                <label id="turnos_profesionales_marcar_turnos" class="btn active btn-default condicion">Turnos</label>
                <label id="turnos_profesionales_marcar_no_disponible" class="btn btn-default condicion">Horario no disponible</label>
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
  <?php /*
  <div class="col w-md bg-white-only b-l bg-auto no-border-xs fs14">
    <div class="wrapper-md ng-scope">
      <div class="m-b-sm text-md">Pr&oacute;ximos turnos</div>
      <ul class="list-group list-group-sm list-group-sp list-group-alt auto m-t">
        <li class="list-group-item">
          <span class="text-muted">Transfer to Jacob at 3:00 pm</span>
          <span class="block text-md text-info">B 15,000.00</span>
        </li>
        <li class="list-group-item">
          <span class="text-muted">Got from Mike at 1:00 pm</span>
          <span class="block text-md text-primary">B 23,000.00</span>
        </li>
        <li class="list-group-item">
          <span class="text-muted">Sponsored ORG at 9:00 am</span>
          <span class="block text-md text-warning">B 3,000.00</span>
        </li>
        <li class="list-group-item">
          <span class="text-muted">Send to Jacob at 8:00 am</span>
          <span class="block text-md">B 11,000.00</span>
        </li>
      </ul>
    </div>
  </div>
  */ ?>
</div>
</script>

<script type="text/template" id="turno_medico_edit_panel_template">
<div class="panel panel-default">
  <div class="panel-heading oh">
    <span class="bold fl mt5">Turno para <%= profesional %></span>
    <button class="fr btn btn-default cerrar"><i class="fa fa-times"></i></button>
  </div>
  <div class="panel-body oh">
    <div class="">
      <div class="form-group">
        <label class="control-label">Paciente</label>
        <input type="hidden" value="<%= id_paciente %>" id="turno_medico_id_paciente">
        <input type="text" placeholder="Escriba el nombre y seleccione de la lista..." value="<%= paciente %>" id="turno_medico_pacientes" class="form-control no-model">
      </div>
      <div class="row">
        <div class="col-md-4">
          <div class="form-group">
            <label class="control-label">Fecha</label>
            <div class="input-group">
              <input type="text" name="fecha" class="form-control" id="turno_medico_fecha" value="<%= fecha %>"/>
              <span class="input-group-btn">
                <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
              </span>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-group">
            <label class="control-label">Hora</label>
            <input type="text" name="hora" class="form-control" id="turno_medico_hora" value="<%= hora %>"/>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-group">
            <label class="control-label">&nbsp;</label>
            <div class="checkbox mt5">
              <label class="i-checks">
                <input type="checkbox" id="turno_medico_sin_horario" name="sin_horario" class="checkbox" value="1" <%= (sin_horario == 1)?"checked":"" %> >
                <i></i>
                Turno sin horario
              </label>
            </div>
          </div>
        </div>
      </div>
      <div class="form-group">
        <label class="control-label">Observaciones</label>
        <textarea placeholder="Escriba aqui alguna nota o recordatorio..." id="turno_medico_observaciones" class="form-control h60 no-model"><%= observaciones %></textarea>
      </div>
      <?php /*
      <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            <label class="control-label">Duracion</label>
            <input type="number" min="0" value="<%= duracion_cantidad %>" class="form-control no-model" id="turno_medico_duracion_cantidad"/>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label class="control-label">&nbsp;</label>
            <select id="turno_medico_duracion_tipo" class="form-control no-model">
              <option <%= (duracion_tipo=="M")?"selected":"" %> value="M">Minutos</option>
              <option <%= (duracion_tipo=="H")?"selected":"" %> value="H">Horas</option>
            </select>
          </div>
        </div>
      </div>  
      <% if (id == undefined) { %>
        <div class="form-group">
            <label class="control-label">Repetir</label>
            <div class="col-md-9">
              <div class="row">
                <div class="col-xs-6">
                  <select class="form-control no-model" id="turno_medico_repeticion">
                    <option value="0">Clase &uacute;nica</option>
                    <option value="1">Cada semana</option>
                    <option value="2">Cada 2 semanas</option>
                    <option value="3">Cada 3 semanas</option>
                    <option value="4">Cada 1 mes</option>
                  </select>
                </div>
                <div class="col-xs-6">
                  <input type="text" class="form-control no-model" placeholder="Hasta" id="turno_medico_fecha_hasta"/>
                </div>
              </div>
            </div>
        </div>    
      <% } %>
      */ ?>
    </div>
  </div>
  <div class="panel-footer clearfix">
    <% if (id != undefined) { %><button class="btn btn-danger eliminar fl">Eliminar</button><% } %>
    <button class="btn guardar btn-success fr">Guardar</button>
  </div>
</div>
</script>