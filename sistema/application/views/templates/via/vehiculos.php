<script type="text/template" id="vehiculos_resultados_template">
<div class="seccion_vacia" style="display:none">
  <h1 class="h1">Todav&iacute;a no ten&eacute;s ningun veh&iacute;culo</h1>
  <h3 class="h3">Para a&ntilde;adir tu primer veh&iacute;culo, hace click en el siguiente bot&oacute;n</h3>
  <div class="list-icon">
  <a href="app/#vehiculo"><i class="icon-note"></i></a>
  </div>
  <div>
  <a class="btn btn-lg btn-info btn-addon" href="app/#vehiculo">
    <i class="fa fa-plus"></i><span>&nbsp;&nbsp;Nuevo&nbsp;&nbsp;</span>
  </a>
  </div>
  <p>
  Si necesitas ayuda o asesoramiento, no dudes en comunicarte, hace click <a class="text-info">aca</a>.
  </p>
</div>
<div class="seccion_llena" style="display:none">
  <% if (!seleccionar) { %>
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <h1 class="m-n font-thin h3"><i class="fa fa-truck icono_principal"></i>Veh&iacute;culos
  </div>
  <% } %>
  <div class="<%= (seleccionar)?'':'wrapper-md' %> ng-scope">
    <div class="panel panel-default">
      <div class="panel-heading clearfix">
      <div class="row">
        <div class="<% if (!seleccionar) { %>col-md-6 col-lg-3 <% } else { %> col-xs-12 <% } %> sm-m-b">
        <div class="input-group">
          <input type="text" id="vehiculos_buscar" placeholder="Buscar..." autocomplete="off" class="form-control">
          <span class="input-group-btn">
            <button class="btn buscar btn-default"><i class="fa fa-search"></i></button>
          </span>
        </div>
        </div>
        <% if (!seleccionar) { %>
        <div class="col-md-6 col-lg-offset-3 col-lg-6 text-right">
          <a class="btn btn-info btn-addon ml5" href="app/#vehiculo">
          <i class="fa fa-plus"></i><span>&nbsp;&nbsp;Nuevo&nbsp;&nbsp;</span>
          </a>
        </div>
        <% } %>
      </div>
      </div>
      <div class="panel-body">
        <div class="b-a table-responsive">
        <table id="vehiculos_tabla" class="table <%= (seleccionar)?'table-small':'' %> table-striped sortable m-b-none default footable">
          <thead>
          <tr>
            <% if (!seleccionar) { %>
            <th style="width:20px;">
              <label class="i-checks m-b-none">
                <input class="esc sel_todos" type="checkbox"><i></i>
              </label>
            </th>
            <% } else { %>
            <th style="width:20px;"></th>
            <% } %>
            <th>Nombre</th>
            <% if (!seleccionar) { %>
            <th class="th_acciones w100">Acciones</th>
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

<script type="text/template" id="vehiculos_item_resultados_template">
  <% var clase = "" %>
  <% if (seleccionar) { %>
    <td>
      <label class="i-checks m-b-none">
        <input class="radio esc" value="<%= id %>" name="radio" type="radio"><i></i>
      </label>
    </td>
  <% } else { %>
    <td>
      <label class="i-checks m-b-none">
        <input class="esc check-row" value="<%= id %>" type="checkbox"><i></i>
      </label>
    </td>
  <% } %>
  <td class="<%= clase %> data"><%= nombre %><br/></td>
  <% if (!seleccionar) { %>
    <td class="tar <%= clase %>">
    <div class="btn-group dropdown">
      <i title="Opciones" class="iconito fa fa-caret-down dropdown-toggle" data-toggle="dropdown"></i>
      <ul class="dropdown-menu pull-right">
      <li><a href="javascript:void(0)" class="duplicar" data-id="<%= id %>">Duplicar</a></li>
      <li><a href="javascript:void(0)" class="eliminar" data-id="<%= id %>">Eliminar</a></li>
      </ul>
    </div>
    </td>
  <% } %>
</script>


<script type="text/template" id="vehiculo_template">
<% if (edicion) { %>
  <div class="bg-light lter b-b wrapper-md ng-scope">
  <h1 class="m-n font-thin h3">
    <i class="fa fa-truck icono_principal"></i>Veh&iacute;culos /
    <b><%= (id == undefined) ? 'Nuevo' : nombre %></b>
  </h1>
  </div>
  <div class="wrapper-md ng-scope">
    <div class="centrado rform">
      <div class="row">
        <div class="col-md-10 col-md-offset-1">
          <div class="panel panel-default">
            <div class="panel-body">
              <div class="padder">

              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">Nombre</label>
                    <input type="text" id="vehiculo_nombre" class="form-control" value="<%= nombre %>" name="nombre"/>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">Patente</label>
                    <input type="text" id="vehiculo_patente" class="form-control" value="<%= patente %>" name="patente"/>
                  </div>
                </div>
                <% if (ID_EMPRESA == 501) { %>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="control-label">Capacidad</label>
                      <input type="text" id="vehiculo_cant_asientos_piso_1" class="form-control" value="<%= cant_asientos_piso_1 %>" name="cant_asientos_piso_1"/>
                    </div>
                  </div>
                <% } %>
              </div>

              <?php /*

              PONER EN VIAJES
              <div class="form-group">
                <label class="control-label">Precio base</label>
                <div class="form-inline">
                <select id="vehiculo_monedas" class="form-control" name="moneda">
                  <% for(var i=0; i < window.monedas.length; i++) { %>
                  <% var o = monedas[i]; %>
                  <option <%= (o.codigo == moneda)?"selected":"" %> value="<%= o.codigo %>"><%= o.codigo %></option>
                  <% } %>
                </select>
                <input id="vehiculo_precio" value="<%= precio %>" type="number" class="form-control number" name="precio"/>
                </div>
              </div>
              */ ?>

              </div>
            </div>
          </div>
        </div>
      </div>
      <% if (id != undefined && ID_EMPRESA != 501) { %>
      <div class="row">
        <div class="col-md-10 col-md-offset-1">
          <div class="panel panel-default">
            <div class="panel-body">
            <div class="tab-container">
              <ul class="nav nav-tabs" role="tablist">
              <li data-i="1" class="tab_link active">
                <a href="#tab1" role="tab" data-toggle="tab">Piso 1</a>
              </li>
              <li data-i="2" class="tab_link">
                <a href="#tab2" role="tab" data-toggle="tab">Piso 2</a>
              </li>
              </ul>
              <div class="tab-content">
              <div id="tab1" class="tab-pane active"></div>
              <div id="tab2" class="tab-pane"></div>
              </div>
            </div>
            </div>
          </div>
        </div>
      </div>
      <% } %>

      <div class="row">
      <div class="col-md-10 col-md-offset-1">
        <button class="btn guardar btn-success">Guardar</button>
      </div>
      </div>
    </div>
  </div>
<% } else { %>
<div class="tab-container">
  <ul class="nav nav-tabs" role="tablist">
  <li data-i="1" class="tab_link active">
    <a href="#tab1" role="tab" data-toggle="tab">Piso 1</a>
  </li>
  <li data-i="2" class="tab_link">
    <a href="#tab2" role="tab" data-toggle="tab">Piso 2</a>
  </li>
  </ul>
  <div class="tab-content">
  <div id="tab1" class="tab-pane active"></div>
  <div id="tab2" class="tab-pane"></div>
  </div>
</div>
<% } %>
</script>


<script type="text/template" id="asientos_template">
<div style="max-height: 350px; overflow: auto">
  <div class="mesas_layer" style="width: 100%">
  <table class="mesas_tabla">
    <tbody class="tbody">
    <% for(var i=0;i < 30;i++) { %>
      <tr>
      <% for(var j=0;j < 5;j++) { %>
        <td class="casillero" data-piso="<%= piso %>" data-x="<%= j %>" data-y="<%= i %>" id="asiento_<%= piso %>_<%= j %>_<%= i %>">
        <% if (edicion) { %>
          <a class="agregar_mesa"><i class="fa fa-plus"></i></a>
        <% } %>
        </td>
      <% } %>
      </tr>
    <% } %>
    </tbody>
  </table>
  </div>
</div>
</script>


<script type="text/template" id="asiento_view_template">
<div>
  <span class="numero"><%= numero_asiento %></span>
</div>
</script>

<script type="text/template" id="asiento_template">
<div class="panel panel-default">
  <div class="panel-heading">
  <span class="font-bold"><%= (id == undefined) ? "Nuevo asiento":"Editar asiento" %></span>
  </div>
  <div class="panel-body">
  <div class="form-group">
    <label class="control-label">N&uacute;mero</label>
    <input type="text" id="asiento_numero_asiento" class="form-control" value="<%= numero_asiento %>" placeholder="Ej: 1" name="numero_asiento"/>
  </div>
  <div class="form-group">
    <label class="control-label">Tarifa</label>
    <select class="form-control no-model" id="asiento_tarifas">
    <% for(var i=0;i< tipos_tarifas.length; i++) { %>
      <% var tt = tipos_tarifas[i] %>
      <option <%= (id_tipo_tarifa == tt.id)?"selected":"" %> value="<%= tt.id %>"><%= tt.nombre %></option>
    <% } %>
    </select>
  </div>
  </div>
  <div class="panel-footer oh">
  <button class="btn guardar btn-success">Guardar</button>
  <button class="btn eliminar fr btn-danger">Eliminar</button>
  </div>
</div>
</script>
