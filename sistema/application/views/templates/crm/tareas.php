<script type="text/template" id="tareas_panel_template">
  <div class="hbox hbox-auto-xs hbox-auto-sm">
    <div class="col">
      <div class="bg-light lter b-b wrapper-md ng-scope">
        <h1 class="m-n font-thin h3"><i class="fa fa-calendar icono_principal"></i><b>Tareas</b>
        </h1>
      </div>
      <div class="wrapper-md ng-scope">
        <div class="panel panel-default">
          <div class="panel-heading clearfix">
            <div class="">
              <div class="sm-m-b">
                <div class="dib w200">
                  <input type="hidden" id="tareas_id_contacto" value="0">
                  <input type="text" placeholder="Buscar..." autocomplete="off" id="tareas_buscar" class="form-control fl no-model">
                </div>
                <% if (SOLO_USUARIO == 0) { %>
                  <div class="dib w170">
                    <select id="tareas_usuarios" class="form-control no-model fl">
                      <option value="0">Todos los usuarios</option>
                      <% for(var i=0;i< window.usuarios.models.length;i++) { %>
                        <% var o = window.usuarios.models[i]; %>
                        <option value="<%= o.id %>"><%= o.get("nombre") %></option>
                      <% } %>
                    </select>
                  </div>
                <% } %>
                <div class="dib w120">
                  <select id="tareas_estados" class="form-control no-model fl">
                    <option value="-1">Estado</option>
                    <option value="1">Realizadas</option>
                    <option value="0">No realizadas</option>
                  </select>
                </div>
                <div class="dib">
                  <button class="fl btn btn-default buscar"><i class="fa fa-search"></i></button>
                </div>
                <div class="pull-right">
                  <a class="btn btn-info btn-addon nuevo" href="javascript:void(0)"><i class="fa fa-plus"></i>&nbsp;&nbsp;Nueva Tarea&nbsp;&nbsp;</a>
                </div>
              </div>
            </div>
          </div>
          <div class="panel panel-default">
            <div class="panel-body">
              <div id="tareas_calendario"></div>
            </div>
          </div>
        </div>
      </div> 
    </div>
    <div class="col w-md bg-white-only b-l bg-auto no-border-xs fs14">
      <div class="padder-md">      
        <div class="m-b m-t text-md">Tareas Vencidas</div>
        <div class="streamline b-l m-b" id="tareas_vertical"></div>
      </div>      
    </div>
  </div>
</script>

<script type="text/template" id="tareas_item_vertical">
  <div class="m-l">
    <div class="text-muted"><%= (fecha != moment().format("DD/MM/YYYY")) ? fecha+". "+hora : hora %></div>
    <p><a href="javascript:void(0)" class="text-info"><%= nombre %></a> <%= asunto %></p>
  </div>
</script>

<script type="text/template" id="tareas_edit_panel_template">
<div class="panel panel-default">
  <div class="panel-heading oh">
    <span class="bold fl fs16 mt7"><%= (id == undefined) ? "Nueva tarea":"Editar tarea" %></span>
    <button class="fr btn btn-default cerrar"><i class="fa fa-times"></i></button>
  </div>
  <div class="panel-body">
    <input type="hidden" id="tarea_id_contacto" value="<%= id_contacto %>"/>
    <div class="form-group">
      <% if (ID_EMPRESA != 228) { %>
        <div class="input-group">
          <input type="text" class="form-control no-model" id="tarea_cliente" value="<%= nombre %>" placeholder="Buscar o agregar un nuevo contacto..." />
          <span class="input-group-btn">
            <button tabindex="-1" class="btn btn-info nuevo_cliente">+</button>  
          </span>
        </div>
      <% } else { %>
        <label class="control-label">Cliente</label>
        <input type="text" class="form-control no-model" id="tarea_cliente" value="<%= nombre %>" placeholder="Buscar un cliente..." />
      <% } %>
    </div>
    <div class="row">
      <div class="col-md-4 pr0">
        <div class="form-group">
          <label class="control-label">Asunto</label>
          <div class="input-group">
            <select id="tarea_asuntos" class="select form-control" name="id_asunto"></select>
            <span class="input-group-btn">
              <button tabindex="-1" class="btn btn-info agregar_asunto">+</button>  
            </span>
          </div>
        </div>
      </div>
      <div class="col-md-8">
        <div class="row">
          <div class="col-md-6 pr0">
            <div class="form-group">
              <label class="control-label">Fecha</label>
              <div class="input-group">
                <input placeholder="Fecha" type="text" class="form-control no-model" id="tarea_fecha"/>
                <span class="input-group-btn">
                  <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                </span>        
              </div>
            </div>
          </div>
          <div class="col-md-6 pl0">
            <div class="form-group">
              <label class="control-label">Promesa</label>
              <div class="input-group">
                <input placeholder="Fecha" type="text" class="form-control no-model" id="tarea_fecha_visto"/>
                <span class="input-group-btn">
                  <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                </span>        
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="form-group">
      <textarea id="tarea_texto" name="texto" placeholder="Escribe aqui la tarea para realizar..." class="form-control no-model h100"><%= texto %></textarea>
    </div>
  </div>
  <div class="panel-footer clearfix">
    <% if (id != undefined) { %>
      <button class="btn btn-danger fl eliminar"><i class="fa fa-trash"></i></button>
      <% if (estado == 0) { %>
        <button class="btn btn-success ml5 fr btn-addon realizada"><i class="fa fa-check"></i>Hecho!</button>
      <% } else { %>
        <button class="btn btn-warning ml5 fr btn-addon no_realizada"><i class="fa fa-ban"></i>Volver a pendiente</button>
      <% } %>
    <% } %>
    <button class="btn btn-default fr guardar">Guardar</button>
  </div>
</div>
</script>




<script type="text/template" id="tareas_listado_template">
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <h1 class="m-n font-thin h3"><i class="fa fa-calendar icono_principal"></i>Tareas
    </h1>
  </div>
  <div class="wrapper-md ng-scope">
    <div class="panel panel-default">    
      <div class="panel-heading clearfix">

        <div class="input-group fl w150">
          <select class="form-control action no-model" id="tareas_filtro_fecha">
            <option value="0">Fecha Tarea</option>
            <option value="1">Fecha Promesa</option>
          </select>
        </div>

        <div class="input-group fl w150">
          <div class="input-group">
            <input type="text" placeholder="Desde" id="tareas_desde" autocomplete="off" class="form-control">
            <span class="input-group-btn">
              <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
            </span>              
          </div>
        </div>
        <div class="input-group fl w150">
          <div class="input-group">
            <input type="text" placeholder="Hasta" id="tareas_hasta" autocomplete="off" class="form-control">
            <span class="input-group-btn">
              <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
            </span>              
          </div>
        </div>

        <div class="input-group fl w200">
          <input type="text" id="tareas_buscar" value="<%= window.tareas_filter %>" placeholder="Buscar..." autocomplete="off" class="form-control">
          <span class="input-group-btn">
            <button class="btn btn-default buscar"><i class="fa fa-search"></i></button>
          </span>
        </div>
        <div class="input-group fl w150">
          <select class="form-control action no-model" id="tareas_sucursales">
            <option value="0">Sucursal</option>
            <% for(var i=0;i< almacenes.length;i++) { %>
              <% var almacen = almacenes[i] %>
              <option value="<%= almacen.id %>" <%= (window.tareas_id_sucursal == almacen.id)?"selected":"" %>><%= almacen.nombre %></option>
            <% } %>
          </select>
        </div>
        <% if (PERFIL == 284) { %>
          <div class="input-group fl w150">
            <select id="tareas_usuarios" class="form-control no-model fl">
              <option value="0">Usuario</option>
              <% for(var i=0;i< window.usuarios.models.length;i++) { %>
                <% var o = window.usuarios.models[i]; %>
                <option <%= (window.tareas_id_usuario == o.id)?"selected":"" %> value="<%= o.id %>"><%= o.get("nombre") %></option>
              <% } %>
            </select>
          </div>
        <% } %>

        <?php /*
        <div class="fr">
          <div class="btn-group dropdown">
            <button class="btn btn-default dropdown-toggle btn-addon" data-toggle="dropdown">
              <i class="fa fa-cog"></i><span>Opciones</span>
              <span class="caret"></span>
            </button>
            <ul class="dropdown-menu pull-right">
              <li><a href="javascript:void" class="imprimir">Imprimir</a></li>
              <li><a href="javascript:void" class="exportar">Exportar Excel</a></li>
            </ul>
          </div>
        </div>
        */ ?>
      </div>
      <div class="panel-body">
        <div class="b-a table-responsive">
          <table id="tareas_table" class="table table-small table-striped sortable m-b-none default footable">
            <thead>
              <tr>
                <th style="width:20px;">
                  <label class="i-checks m-b-none">
                    <input class="esc sel_todos" type="checkbox"><i></i>
                  </label>
                </th>
                <th class="w150">Fecha</th>
                <th class="">Nombre</th>
                <th class="w150">Tipo</th>
                <th class="">Observacion</th>
                <th class="">Estado</th>
                <th class="w150">Promesa</th>
                <th class="th_acciones"></th>
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


<script type="text/template" id="tareas_item">
  <td>
    <label class="i-checks m-b-none">
      <input class="esc check-row" value="<%= id %>" type="checkbox"><i></i>
    </label>
  </td>
  <td><%= fecha %></td>
  <td><a href="/sistema/app/#pres_cliente_acciones/<%= id_contacto %>/seguimiento" target="_blank" class="text-info"><%= nombre %></a></td>
  <td><%= asunto %>
    <%= (ID_EMPRESA == 228 && custom_1 == '1') ? '<i class="fa fa-star text-warning"></i>' : '' %>
  </td>
  <td><%= texto %></td>
  <td>
    <% if (estado == 0 && moment(fecha,"DD/MM/YYYY HH:mm:ss").isBefore(moment()) ) { %>
      <span class="label bg-danger">Vencida</span>
    <% } else if (estado == 1) { %>
      <span class="label bg-success">Realizada</span>
    <% } else { %>
      <span class="label bg-warning inline">Pendiente</span>
    <% } %>
  </td>
  <td><%= fecha_visto %></td>
  <td><a href="javascript:void(0)" class="btn btn-white ver"><i class="fa fa-pencil"></i></a></td>
</script>