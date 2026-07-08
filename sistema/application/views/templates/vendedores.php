<script type="text/template" id="vendedores_panel_template">
<div class="bg-light lter b-b wrapper-md">
  <% if (ID_EMPRESA == 1095) { %>
    <h1 class="m-n font-thin h3"><i class="fa fa-users icono_principal mr10"></i>Pacientes</h1>
  <% } else { %>
  	<h1 class="m-n font-thin h3"><i class="fa fa-file-text icono_principal mr10"></i>Ventas
  		/	<b>Vendedores</b>
  	</h1>
  <% } %>
</div>
<div class="wrapper-md">
  <div class="panel panel-default">  
	<div class="panel-heading clearfix">
		<div class="row">
			<div class="col-md-6 col-lg-3 sm-m-b">
				<div class="search_container"></div>
			</div>
			<div class="col-md-6 col-lg-offset-3 col-lg-6 text-right">
				<a class="btn pull-right btn-info btn-addon" href="app/#vendedor"><i class="fa fa-plus"></i>&nbsp;&nbsp;Nuevo&nbsp;&nbsp;</a>
			</div>
		</div>	  
	</div>
	<div class="panel-body">
	  <div class="b-a table-responsive">
		<table id="vendedores_table" class="table table-striped m-b-none sortable default footable">
			<thead>
				<tr>
          <th class="sorting" data-sort-by="nombre">Nombre</th>
          <% if (control.check("seguimiento_vendedores")>0) { %><th></th><% } %>
          <% if (ID_EMPRESA == 1095) { %>
            <th class="sorting" data-sort-by="codigo">Documento</th>
            <th class="sorting" data-sort-by="direccion">Direccion</th>
            <th class="sorting" data-sort-by="telefono">Telefono</th>
            <th class="sorting" data-sort-by="color">Fecha Inicio</th>
          <% } else { %>
  					<th class="col-xxs-0">Email</th>
  					<th class="col-xxs-0">Tel&eacute;fono</th>
            <th class="col-xxs-0">Lista</th>
            <% if (permiso > 1) { %>
              <th class="col-xxs-0">Comisi&oacute;n</th>
            <% } %>
          <% } %>
					<% if (permiso > 1) { %>
						<th class="w100 th_acciones">Acciones</th>
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

<script type="text/template" id="vendedores_item">
	<td class='ver'><span class="text-info"><%= nombre %></span></td>
  <% if (control.check("seguimiento_vendedores")>0) { %>
    <td class="p5"><a class="btn btn-success btn-xs" href="app/#seguimiento_vendedores/<%= id %>">Ver Seguimiento</a></td>
  <% } %>
  <% if (ID_EMPRESA == 1095) { %>
    <td class="ver col-xxs-0"><%= codigo %></td>
    <td class="ver col-xxs-0"><%= direccion %></td>
    <td class="ver col-xxs-0"><%= telefono %></td>
    <td class="ver col-xxs-0"><%= color %></td>
  <% } else { %>
  	<td class="ver col-xxs-0"><%= email %></td>
  	<td class="ver col-xxs-0"><%= telefono %></td>
    <td class="ver col-xxs-0">
      <%= (lista_defecto == 0)?"No definida":"" %>
      <%= (lista_defecto == 1)?LISTA_1_NOMBRE:"" %>
      <%= (lista_defecto == 2)?LISTA_2_NOMBRE:"" %>
      <%= (lista_defecto == 3)?LISTA_3_NOMBRE:"" %>
      <%= (lista_defecto == 4)?LISTA_4_NOMBRE:"" %>
      <%= (lista_defecto == 5)?LISTA_5_NOMBRE:"" %>
      <%= (lista_defecto == 6)?LISTA_6_NOMBRE:"" %>
    </td>
    <% if (permiso > 1) { %>
      <td class="ver col-xxs-0"><%= comision %> %</td>
    <% } %>
  <% } %>
	<% if (permiso > 1) { %>
	  <td class="tar">
		<div class="btn-group dropdown">
		  <i title="Opciones" class="iconito fa fa-caret-down dropdown-toggle" data-toggle="dropdown"></i>
		  <ul class="dropdown-menu pull-right">
			<li><a href="javascript:void(0)" class="delete" data-id="<%= id %>">Eliminar</a></li>
		  </ul>
		</div>
	  </td>
	<% } %>
</script>

<script type="text/template" id="vendedores_edit_panel_template">
<div class="bg-light lter b-b wrapper-md">
  <% if (ID_EMPRESA == 1095) { %>
    <h1 class="m-n font-thin h3"><i class="fa fa-users icono_principal mr10"></i>Pacientes</h1>
  <% } else { %>  
  	<h1 class="m-n font-thin h3"><i class="fa fa-file-text icono_principal mr10"></i>Ventas
  		/	Vendedores
  		/ <b><%= (id == undefined)?"Nuevo":nombre %></b>
  	</h1>
  <% } %>
</div>
<div class="wrapper-md">
	<div class="centrado rform">
		<div class="row">
			<div class="col-md-10 col-md-offset-1">
				<div class="panel panel-default">
					<div class="panel-body">
						<div class="padder">
              <% if (ID_EMPRESA == 1095) { %>
                <div class="row">
                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="control-label">Nombre</label>
                      <input type="text" name="nombre" class="form-control" id="vendedores_nombre" value="<%= nombre %>"/>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="control-label">Direccion</label>
                      <input type="text" name="direccion" class="form-control" id="vendedores_direccion" value="<%= direccion %>"/>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="control-label">Telefono</label>
                      <input type="text" name="telefono" class="form-control" id="vendedores_telefono" value="<%= telefono %>"/>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="control-label">Fecha de Inicio</label>
                      <input type="text" name="color" class="form-control" id="vendedores_color" value="<%= color %>"/>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="control-label">Documento</label>
                      <input type="text" name="codigo" class="form-control" id="vendedores_codigo" value="<%= codigo %>"/>
                    </div>
                  </div>
                </div>
              <% } else { %>
  							<div class="row">
  								<div class="col-md-4">
  									<div class="form-group">
  										<label class="control-label">Nombre </label>
  										<input type="text" name="nombre" class="form-control" id="vendedores_nombre" value="<%= nombre %>"/>
  									</div>
  								</div>
  								<div class="col-md-4">
  									<div class="form-group">
  										<label class="control-label">Email </label>
  										<input <%= (edicion)?"":"disabled" %> type="text" name="email" class="form-control" id="vendedores_email" value="<%= email %>"/>
  									</div>
  								</div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="control-label">Contrase&ntilde;a </label>
                      <input <%= (edicion)?"":"disabled" %> type="text" name="password" class="form-control" id="vendedores_password" value="<%= password %>"/>
                    </div>
                  </div>
  							</div>
  							<div class="row">
  								<div class="col-md-4">
  									<div class="form-group">
  										<label class="control-label">Telefono </label>
  										<input <%= (edicion)?"":"disabled" %> type="text" name="telefono" class="form-control" id="vendedores_telefono" value="<%= telefono %>"/>
  									</div>
  								</div>
  								<div class="col-md-4">
  									<div class="form-group">
  										<label class="control-label">Direccion </label>
  										<input <%= (edicion)?"":"disabled" %> type="text" name="direccion" class="form-control" id="vendedores_direccion" value="<%= direccion %>"/>
  									</div>
  								</div>
  								<div class="col-md-4">
  									<div class="form-group">
  										<label class="control-label">Limite Descuento (%)</label>
  										<input <%= (edicion)?"":"disabled" %> type="text" name="limite_descuento" class="form-control" id="vendedores_limite_descuento" value="<%= limite_descuento %>"/>
  									</div>
  								</div>
  							</div>
  							<div class="row">
  								<div class="col-md-4">
  									<div class="form-group">
  										<label class="control-label">% Comision sobre ventas</label>
  										<input <%= (edicion)?"":"disabled" %> type="text" name="comision" class="form-control number" id="vendedores_comision" value="<%= comision %>"/>
  									</div>
  								</div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="control-label">Codigo</label>
                      <input <%= (edicion)?"":"disabled" %> type="text" name="codigo" class="form-control number" id="vendedores_codigo" value="<%= codigo %>"/>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="control-label">Color</label>
                      <div class="input-group color colorpicker-component">
                        <input <%= (edicion)?"":"disabled" %> type="text" class="form-control" value="<%= color %>" />
                        <span class="input-group-addon"><i></i></span>
                      </div>
                    </div>
                  </div>
  							</div>
                <div class="row">
                  <div class="col-md-3">
                    <div class="form-group">
                      <label class="control-label">Lista de Precios</label>
                      <select <%= (edicion)?"":"disabled" %> id="vendedores_listas" class="w100p form-control no-model">
                        <option <%= (lista_defecto == 0)?"selected":"" %> value="0">No definida</option>
                        <option <%= (lista_defecto == 1)?"selected":"" %> value="1"><%= LISTA_1_NOMBRE %></option>
                        <option <%= (lista_defecto == 2)?"selected":"" %> value="2"><%= LISTA_2_NOMBRE %></option>
                        <option <%= (lista_defecto == 3)?"selected":"" %> value="3"><%= LISTA_3_NOMBRE %></option>
                        <option <%= (lista_defecto == 4)?"selected":"" %> value="4"><%= LISTA_4_NOMBRE %></option>
                        <option <%= (lista_defecto == 5)?"selected":"" %> value="5"><%= LISTA_5_NOMBRE %></option>
                        <option <%= (lista_defecto == 6)?"selected":"" %> value="6"><%= LISTA_6_NOMBRE %></option>
                      </select>
                    </div>
                  </div>
    		          <% if (control.check("almacenes")>0) { %>
                    <div class="col-md-3">
      	              <div class="form-group">
                        <label class="control-label">Sucursal</label>
      	                <select <%= (edicion)?"":"disabled" %> id="vendedores_sucursales" class="w100p form-control no-model">
      	                  <option value="0">Sucursal</option>
      	                  <% for(var i=0;i< window.almacenes.length;i++) { %>
      	                    <% var alm = window.almacenes[i] %>
      	                    <option <%= (id_sucursal == alm.id ? "selected":"") %> value="<%= alm.id %>"><%= alm.nombre %></option>
      	                  <% } %>
      	                </select>
      	              </div>
                    </div>
                    <div class="col-md-3">
                      <div class="form-group">
                        <label class="control-label">Punto de Venta</label>
                        <select class="form-control" id="vendedores_puntos_venta">
                          <option value="0">-</option>
                          <% for(var i=0;i< puntos_venta.length;i++) { %>
                            <% var pv = puntos_venta[i] %>
                            <option <%= (id_punto_venta == pv.id)?"selected":"" %> value="<%= pv.id %>"><%= pv.nombre %></option>
                          <% } %>
                        </select>
                      </div>
                    </div>
    		          <% } %>
                  <div class="col-md-3">
                    <div class="form-group">
                      <label class="control-label">Perfil App</label>
                      <select class="form-control" id="vendedores_perfil_app">
                        <option <%= (perfil_app == 0)?"selected":"" %> value="0">Preventa</option>
                        <option <%= (perfil_app == 1)?"selected":"" %> value="1">Repartidor</option>
                        <option <%= (perfil_app == 2)?"selected":"" %> value="2">Ambos</option>
                      </select>
                    </div>
                  </div>
                </div>
						  </div>
            <% } %>

					</div>
				</div>
			</div>
		</div>
		<div class="line b-b m-b-lg"></div>
		<div class="row">
			<div class="col-md-10 col-md-offset-1 tar">
				<button class="btn guardar btn-success">Guardar</button>
			</div>
		</div>
	</div>
</div>

</script>

<script type="text/template" id="vendedores_seguimiento_template">
<div class="bg-light lter b-b wrapper-md">
  <h1 class="m-n font-thin h3"><i class="fa fa-map-marker icono_principal mr10"></i>Seguimiento</h1>
</div>
<div class="panel panel-default">  
  <div class="panel-heading clearfix">
    <div class="row pl10 pr10">
      <div class="col-lg-2 col-xs-6 h50 pr5 pl5">
        <div class="form-group">
          <label class="control-label"><%= (ID_EMPRESA == 1095)?"Paciente":"Vendedor" %></label>
          <select class="form-control" id="vendedores_seguimiento_vendedores">
            <option value="0">-</option>
            <% for(var i=0;i < vendedores.length;i++) { %>
              <% var o = vendedores[i]; %>
              <option <%= (id_vendedor == o.id)?"selected":"" %> value="<%= o.id %>"><%= o.nombre %></option>
            <% } %>
          </select>
        </div>
      </div>
      <div class="col-lg-2 col-xs-6 h50 pr5 pl5">
        <div class="form-group">
          <label class="control-label">Vista</label>
          <select class="form-control" id="vendedores_seguimiento_vista">
            <option value="2">Ultima Posicion</option>
            <option value="1">Recorrido Completo</option>
          </select>
        </div>
      </div>
      <div class="col-lg-2 col-xs-6 h50 pr5 pl5">
        <div class="form-group">
          <label class="control-label">Desde</label>
          <div class="input-group">
            <input autocomplete="off" type="text" placeholder="Desde" id="vendedores_seguimiento_fecha_desde" class="form-control">
            <span class="input-group-btn">
              <input autocomplete="off" type="text" placeholder="Hora" id="vendedores_seguimiento_hora_desde" value="00:00" class="form-control w70 no-model">
            </span>
            <span class="input-group-btn">
              <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
            </span>
          </div>
        </div>
      </div>
      <div class="col-lg-2 col-xs-6 h50 pr5 pl5">
        <div class="form-group">
          <label class="control-label">Hasta</label>
          <div class="input-group">
            <input autocomplete="off" type="text" placeholder="Hasta" id="vendedores_seguimiento_fecha_hasta" class="form-control">
            <span class="input-group-btn">
              <input autocomplete="off" type="text" placeholder="Hora" id="vendedores_seguimiento_hora_hasta" value="23:59" class="form-control w70 no-model">
            </span>
            <span class="input-group-btn">
              <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
            </span>
          </div>
        </div>
      </div>
      <div class="col-lg-2 col-xs-6 h50 pr5 pl5">
        <div class="form-group">
          <label class="control-label">&nbsp;</label>
          <button class="buscar btn btn-primary btn-block">Buscar</button>
        </div>
      </div>
    </div>
  </div>
  <div class="panel-body">
    <div style="height:500px" id="vendedores_seguimiento_mapa"></div>
  </div>
</div>
</script>