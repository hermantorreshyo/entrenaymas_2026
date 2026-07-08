<script type="text/template" id="proyectos_panel_template">
	
	<div class="bg-light lter b-b wrapper-md ng-scope">
		<h1 class="m-n font-thin h3">Listado de Proyectos</h1>
	</div>
	
	<div class="wrapper-md ng-scope">
		<div class="panel panel-default">
		
			<div class="panel-heading oh">
				<div class="search_container col-lg-4 col-md-6 col-sm-9 col-xs-12"></div>
				<a class="btn pull-right btn-success btn-addon" href="app/#proyecto"><i class="fa fa-plus"></i>Nuevo</a>
			</div>
			<div class="panel-body">
				<div class="b-a table-responsive">
					<table id="proyectos_table" class="table table-striped sortable m-b-none default footable">
						<thead>
							<tr>
								<th>Nombre</th>
								<% if (permiso > 1) { %>
									<th class="w25"></th>
									<th class="w25"></th>
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


<script type="text/template" id="proyectos_item">
	<td><span class='ver'><%= nombre %></span></td>
	<% if (permiso > 1) { %>
		<td><i class="fa fa-file-text-o edit text-dark" data-id="<%= id %>" /></td>
		<td><i class="glyphicon glyphicon-remove delete text-danger" data-id="<%= id %>" /></td>
	<% } %>
</script>

<script type="text/template" id="proyectos_edit_panel_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <h1 class="m-n font-thin h3">
    <i class="fa fa-cogs icono_principal"></i>Proyectos /
    <b><%= (id == undefined) ? 'Nuevo' : nombre %></b>
  </h1>
</div>
<div class="wrapper-md ng-scope">
  <div class="centrado rform">

    <div class="panel panel-default">
      <div class="panel-body">
        <div class="padder">
  				<div class="form-group">
  					<label class="control-label">Nombre</label>
  					<input type="text" name="nombre" class="form-control" id="proyectos_nombre" value="<%= nombre %>"/>
  				</div>
        </div>
      </div>
    </div>

    <div class="panel panel-default">
      <div class="panel-body">
        <div class="padder">
          <div class="m-b row clearfix">
            <div class="form-group col-sm-4">
              <label class="control-label">Etiqueta</label>
              <input type="text" id="proyecto_modulo_nombre_es" class="form-control">
            </div>
            <div class="form-group col-sm-4">
              <label class="control-label">Modulo</label>
              <select id="proyecto_modulo_modulos" style="width: 100%" class="no-model">
                <option value="0">Ninguno</option>
                <% for(var t=0; t < window.modulos.length; t++) { %>
                  <% var o = window.modulos[t]; %>
                  <option value="<%= o.id %>"><%= o.nombre %></option>
                <% } %>
              </select>
            </div>
            <div class="form-group col-sm-4">
              <label class="control-label">Estado</label>
              <select id="proyecto_modulo_estado" class="form-control">
                <option value="1">Habilitado</option>
                <option value="2">Por defecto</option>
              </select>
            </div>
            <div class="form-group col-sm-2">
              <label class="control-label">Orden</label>
              <input type="text" id="proyecto_modulo_orden_1" class="form-control">
            </div>
            <div class="form-group col-sm-2">
              <label class="control-label">&nbsp;</label>
              <input type="text" id="proyecto_modulo_orden_2" class="form-control">
            </div>
            <div class="form-group col-sm-4">
              <label class="control-label">Clase</label>
              <div class="input-group">
                <input type="text" id="proyecto_modulo_clase" class="form-control">
                <span class="input-group-btn">
                  <a id="modulo_agregar" class="btn btn-info"><i class="fa fa-plus"></i></a>
                </span>
              </div>
            </div>
          </div>
          <div class="b-a" style="overflow: auto; max-height: 400px">
            <div class="table-responsive">
              <table id="proyecto_modulos_tabla" class="table m-b-none default footable">
                <thead>
                  <tr>
                    <th style="display: none"></th>
                    <th colspan="2">Orden</th>
                    <th style="width: 20px"></th>
                    <th>Nombre</th>
                    <th>ID</th>
                    <th>Estado</th>
                    <th style="width: 20px"></th>
                  </tr>
                </thead>
                <tbody>
                  <% for(var i=0;i< modulos.length;i++) { %>
                    <% var p = modulos[i] %>
                    <tr>
                      <td class="id_modulo dn"><%= p.id_modulo %></td>
                      <td class="estado dn"><%= p.estado %></td>
                      <td class="clase dn"><%= p.clase %></td>
                      <td class="orden_1 editar_modulo"><%= p.orden_1 %></td>
                      <td class="orden_2 editar_modulo"><%= p.orden_2 %></td>
                      <td class='editar_modulo'><i class='<%= p.clase %>'></i></td>
                      <td class="editar_modulo">
                        <span class="text-info editar_modulo">
                          <%= ((p.orden_2 != 0 && p.orden_1 != 0)?"<span class='dib w30'>-></span>":"") %>
                          <span class="nombre_es"><%= p.nombre_es %></span>
                        </span>
                      </td>
                      <td class="editar_modulo"><%= p.modulo %></td>
                      <td class="editar_modulo">
                        <%= (p.estado == 1)?"Habilitado":"" %>
                        <%= (p.estado == 2)?"Por defecto":"" %>
                      </td>
                      <td class="tar">
                        <button class="btn btn-sm btn-white eliminar_modulo"><i class="fa fa-trash"></i></button>
                      </td>
                    </tr>
                  <% } %>
                </tbody>
              </table>
            </div>
          </div>

        </div>
      </div>
    </div>

    <div class="line b-b m-b-lg"></div>
    <button class="btn guardar btn-success">Guardar</button>

  </div>

</div>
</script>