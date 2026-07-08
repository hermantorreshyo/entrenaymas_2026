<script type="text/template" id="habitaciones_panel_template">
<div class="seccion_vacia" style="display:none">
  <h1 class="h1">Todav&iacute;a no ten&eacute;s ninguna habitaci&oacute;n</h1>
  <h3 class="h3">Para a&ntilde;adir tu primera habitaci&oacute;n, hace click en el siguiente bot&oacute;n</h3>
  <div class="list-icon">
    <a href="app/#tipo_habitacion"><i class="icon-note"></i></a>
  </div>
  <div>
    <a class="btn btn-lg btn-info btn-addon" href="app/#habitacion">
      <i class="fa fa-plus"></i><span>&nbsp;&nbsp;Nuevo&nbsp;&nbsp;</span>
    </a>
  </div>
  <p>
    Si necesitas ayuda o asesoramiento, no dudes en comunicarte, hace click <a class="text-info">aca</a>.
  </p>
</div>
<div class="seccion_llena" style="display:none">
    <div class="bg-light lter b-b wrapper-md ng-scope">
        <h1 class="m-n font-thin h3"><i class="fa fa-tags icono_principal"></i>Habitaciones</h1>
    </div>
    <div class="wrapper-md ng-scope">
        <div class="panel panel-default">
        
            <div class="panel-heading oh">
                <div class="row">
                    <div class="col-md-6 col-lg-3 sm-m-b">
                        <div class="search_container"></div>
                    </div>
                    <div class="col-md-6 col-lg-offset-3 col-lg-6 text-right">
                        <a class="btn btn-info btn-addon" href="app/#habitacion"><i class="fa fa-plus"></i>&nbsp;&nbsp;Nuevo&nbsp;&nbsp;</a>
                    </div>
                </div>
            </div>
            <div class="panel-body">
                <div class="b-a table-responsive">
                    <table id="habitaciones_table" class="table table-striped sortable m-b-none default footable">
                        <thead>
                            <tr>
                                <th style="width:20px;">
                                    <label class="i-checks m-b-none">
                                        <input class="esc sel_todos" type="checkbox"><i></i>
                                    </label>
                                </th>
                                <th class="sorting" data-sort-by="nombre">Nombre</th>
                                <th class="sorting" data-sort-by="tipo">Tipo</th>
                                <th class="sorting" data-sort-by="capacidad">Capacidad</th>
                                <% if (permiso > 1) { %>
                                    <th class="w100"></th>
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


<script type="text/template" id="habitaciones_item">
    <td>
        <label class="i-checks m-b-none">
            <input class="esc check-row" value="<%= id %>" type="checkbox"><i></i>
        </label>
    </td>
	<td class="ver"><span class='<%= (activo==1)?"text-info":"" %>'><%= nombre %></span></td>
    <td class="ver"><%= tipo %></td>
    <td class="ver"><%= capacidad %></td>
	<% if (permiso > 1) { %>
        <td class="p5 td_acciones">
            <i title="Activo" class="fa-check iconito fa activo <%= (activo == 1)?"active":"" %>"></i>
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

<script type="text/template" id="habitaciones_edit_panel_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
    <h1 class="m-n font-thin h3"><i class="fa fa-tags icono_principal"></i>Habitaciones /
        <b><%= (id == undefined) ? 'Nueva' : nombre %></b>
    </h1>
</div>
<div class="wrapper-md ng-scope">
    <div class="centrado rform">
        <div class="row">
            <div class="col-md-4">
                <div class="detalle_texto">Datos de la habitaci&oacute;n</div>
            </div>
            <div class="col-md-8">
                <div class="panel panel-default">
                    <div class="panel-body">
                    
                        <div class="padder">
                            <div class="form-group">
                                <label class="control-label">Nombre</label>
                                <% if (edicion) { %>
                                    <input type="text" name="nombre" class="form-control" id="habitaciones_nombre" value="<%= nombre %>"/>
                                <% } else { %>
                                    <span><%= nombre %></span>
                                <% } %>
                            </div>
                            <div class="form-group">
                                <label class="control-label">Tipo</label>
                                <% if (edicion) { %>
                                    <select class="w100p" id="habitacion_tipos"></select>
                                <% } else { %>
                                    <span><%= tipo %></span>
                                <% } %>
                            </div>		
                            <div class="form-group">
                                <label class="control-label">Capacidad</label>
                                <% if (edicion) { %>
                                    <input type="text" name="capacidad" class="form-control" id="habitaciones_capacidad" value="<%= capacidad %>"/>
                                <% } else { %>
                                    <span><%= capacidad %></span>
                                <% } %>
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