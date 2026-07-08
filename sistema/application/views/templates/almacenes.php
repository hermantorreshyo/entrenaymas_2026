<script type="text/template" id="almacenes_panel_template">
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <% var modulo = control.get("almacenes") %>
    <h1 class="m-n font-thin h3">
      <i class="fa fa-cog icono_principal"></i>Configuraci&oacute;n
      / <b><%= modulo.title %></b>
    </h1>
  </div>
  <div class="wrapper-md ng-scope">

    <div class="panel panel-default">
      <ul class="nav nav-tabs nav-tabs-2" role="tablist">
        <li class="active">
          <a href="javascript:void(0)"><i class="fa fa-database text-info"></i> Almacenes</a>
        </li>
        <li class="">
          <a href="app/#centros_costos"><i class="fa fa-home text-danger"></i> Centro de Costos</a>
        </li>
      </ul>
      <div class="panel-heading oh">
        <div class="row">
          <div class="col-md-6 col-lg-3 sm-m-b">
            <div class="search_container"></div>
          </div>
          <div class="col-md-6 col-lg-offset-3 col-lg-6 text-right">
            <a class="btn pull-right btn-info btn-addon" href="app/#almacen"><i class="fa fa-plus"></i>&nbsp;&nbsp;Nuevo&nbsp;&nbsp;</a>
          </div>
        </div>
      </div>
      <div class="panel-body">
        <div class="b-a table-responsive">
          <table id="almacenes_table" class="table table-striped sortable m-b-none default footable">
            <thead>
              <tr>
                <th class="sorting" data-sort-by="nombre">Nombre</th>
                <th class="sorting" data-sort-by="id_centro_costo">Centro de Costo</th>
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


<script type="text/template" id="almacenes_item">
	<td><span class='ver'><%= nombre %></span></td>
  <td><span class='ver'><%= centro_costo %></span></td>
	<% if (permiso > 1) { %>
		<td><i class="fa fa-file-text-o edit text-dark" data-id="<%= id %>" /></td>
		<td><i class="glyphicon glyphicon-remove delete text-danger" data-id="<%= id %>" /></td>
	<% } %>
</script>

<script type="text/template" id="almacenes_edit_panel_template">
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <% var modulo = control.get("almacenes") %>
    <h1 class="m-n font-thin h3">
      <i class="fa fa-cog icono_principal"></i>Configuraci&oacute;n
      / <%= modulo.title %>
      / <b><%= (id == undefined) ? "Nuevo" : nombre %></b>
    </h1>
  </div>
  <div class="wrapper-md">
    <div class="centrado rform">
      <div class="row">
        <div class="col-md-offset-1 col-md-10">
          <div class="panel panel-default">
            <div class="panel-body">
              <div class="padder">
                <div class="form-group">
                  <label class="control-label">Nombre</label>
                  <input type="text" name="nombre" class="form-control" id="almacenes_nombre" value="<%= nombre %>"/>
                </div>
                <div class="form-group">
                  <label class="control-label">Direccion</label>
                  <input type="text" name="direccion" class="form-control" id="almacenes_direccion" value="<%= direccion %>"/>
                </div> 
                <% if (control.check("puntos_venta")>0) { %>
                  <div class="form-group">
                    <label class="control-label">Puntos de venta</label>
                    <select id="almacen_puntos_venta" class="w100p"></select>
                  </div>
                <% } %>
                <% if (control.check("razones_sociales")>0) { %>
                  <div class="form-group">
                    <label class="control-label">Razon Social</label>
                    <select id="almacen_razones_sociales" class="form-control"></select>
                  </div>
                <% } %>
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="control-label">Orden</label>
                      <input type="text" id="almacen_orden" name="orden" value="<%= orden %>" class="form-control"/>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="control-label">Centro de costos</label>
                      <select id="almacen_centros_costos" name="id_centro_costo" class="form-control"></select>
                    </div>
                  </div>
                </div>

                <div class="form-group">
                  <div class="checkbox">
                    <label class="i-checks">
                      <input type="checkbox" <%= (!edicion)?"disabled":"" %> id="almacen_para_retiro" name="para_retiro" value="1" <%= (para_retiro == 1)?"checked":"" %> ><i></i>
                      Habilitar en la web el retiro a esta sucursal
                    </label>
                  </div>          
                </div>


              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-offset-1 col-md-10 tar">
          <button class="btn guardar btn-success">Guardar</button>
        </div>
      </div>
    </div>
  </div>
</script>



<script type="text/template" id="centros_costos_panel_template">
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <h1 class="m-n font-thin h3">
      <i class="fa fa-cog icono_principal"></i>Configuraci&oacute;n
      / <b>Centro de Costos</b>
    </h1>
  </div>
  <div class="wrapper-md ng-scope">

    <div class="panel panel-default">
      <ul class="nav nav-tabs nav-tabs-2" role="tablist">
        <li class="">
          <a href="app/#almacenes"><i class="fa fa-database text-info"></i> Almacenes</a>
        </li>
        <li class="active">
          <a href="javascript:void(0)"><i class="fa fa-home text-danger"></i> Centro de Costos</a>
        </li>
      </ul>
      <div class="panel-heading oh">
        <div class="row">
          <div class="col-md-6 col-lg-3 sm-m-b">
            <div class="search_container"></div>
          </div>
          <div class="col-md-6 col-lg-offset-3 col-lg-6 text-right">
            <a class="btn pull-right btn-info btn-addon" href="app/#centro_costo"><i class="fa fa-plus"></i>&nbsp;&nbsp;Nuevo&nbsp;&nbsp;</a>
          </div>
        </div>
      </div>
      <div class="panel-body">
        <div class="b-a table-responsive">
          <table id="centros_costos_table" class="table table-striped sortable m-b-none default footable">
            <thead>
              <tr>
                <th class="sorting" data-sort-by="nombre">Nombre</th>
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
<script type="text/template" id="centros_costos_item">
  <td><span class='ver'><%= nombre %></span></td>
  <% if (permiso > 1) { %>
    <td><i class="fa fa-file-text-o edit text-dark" data-id="<%= id %>" /></td>
    <td><i class="glyphicon glyphicon-remove delete text-danger" data-id="<%= id %>" /></td>
  <% } %>
</script>
<script type="text/template" id="centros_costos_edit_panel_template">
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <h1 class="m-n font-thin h3">
      <i class="fa fa-cog icono_principal"></i>Configuraci&oacute;n
      / Centro de Costos
      / <b><%= (id == undefined) ? "Nuevo" : nombre %></b>
    </h1>
  </div>
  <div class="wrapper-md">
    <div class="centrado rform">
      <div class="row">
        <div class="col-md-offset-1 col-md-10">
          <div class="panel panel-default">
            <div class="panel-body">
              <div class="padder">
                <div class="form-group">
                  <label class="control-label">Nombre</label>
                  <input type="text" name="nombre" class="form-control" id="centros_costos_nombre" value="<%= nombre %>"/>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-offset-1 col-md-10 tar">
          <button class="btn guardar btn-success">Guardar</button>
        </div>
      </div>
    </div>
  </div>
</script>