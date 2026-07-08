<script type="text/template" id="tarjetas_resultados_template">
<div class="seccion_llena">
  <% if (!seleccionar) { %>
    <div class="bg-light lter b-b wrapper-md ng-scope">
      <h1 class="m-n font-thin h3"><i class="fa fa-cog icono_principal"></i>Configuraci&oacute;n
      / <b>Tarjetas</b>
      </h1>
    </div>
  <% } %>
  <div class="<%= (seleccionar)?'':'wrapper-md' %> ng-scope">
    <div class="panel panel-default">
        <div class="panel-heading clearfix">
          <div class="row">
            <div class="<% if (!seleccionar) { %>col-md-6 col-lg-3 <% } else { %> col-xs-12 <% } %> sm-m-b">
              <div class="input-group">
                  <input type="text" id="tarjetas_buscar" placeholder="Buscar..." autocomplete="off" class="form-control">
                  <span class="input-group-btn">
                    <button class="btn buscar btn-default"><i class="fa fa-search"></i></button>
                  </span>
              </div>
            </div>
            <% if (!seleccionar) { %>
              <div class="col-md-6 col-lg-offset-3 col-lg-6 text-right">
                <a class="btn btn-info btn-addon ml5" href="app/#tarjeta">
                  <i class="fa fa-plus"></i><span>&nbsp;&nbsp;Nuevo&nbsp;&nbsp;</span>
                </a>
              </div>
            <% } %>
          </div>
        </div>
      <div class="panel-body">
        <div class="b-a table-responsive">
          <table id="tarjetas_tabla" class="table <%= (seleccionar)?'table-small':'' %> table-striped sortable m-b-none default footable">
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
                  <th class="th_acciones w150">Acciones</th>
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

<script type="text/template" id="tarjetas_item_resultados_template">
  <% var clase = "" %>
  <% if (seleccionar) { %>
    <td>
      <label class="i-checks m-b-none">
        <input class="radio esc" value="<%= codigo %>" name="radio" type="radio"><i></i>
      </label>
    </td>
  <% } else { %>
    <td>
      <label class="i-checks m-b-none">
        <input class="esc check-row" value="<%= id %>" type="checkbox"><i></i>
      </label>
    </td>
  <% } %>
  <td class="<%= clase %> data">
    <span class="text-info"><%= nombre.ucwords() %></span>
  </td>
  <% if (!seleccionar) { %>
    <td class="<%= clase %>">
      <div class="fr m-t-xs btn-group dropdown">
        <i title="Opciones" class="iconito fa fa-caret-down dropdown-toggle" data-toggle="dropdown"></i>
        <ul class="dropdown-menu pull-right">
          <li><a href="javascript:void(0)" class="duplicar" data-id="<%= id %>">Duplicar</a></li>
          <li><a href="javascript:void(0)" class="eliminar" data-id="<%= id %>">Eliminar</a></li>
        </ul>
      </div>
    </td>
  <% } %>
</script>


<script type="text/template" id="tarjeta_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <h1 class="m-n font-thin h3">
    <i class="fa fa-cog icono_principal"></i>Configuraci&oacute;n 
    / Tarjetas
    / <b><%= (id == undefined) ? 'Nuevo' : nombre %></b>
  </h1>
</div>
<div class="wrapper-md ng-scope">
  <div class="centrado rform">
    <div class="row">
      <div class="col-md-10 col-md-offset-1">
        <div class="panel panel-default">
          <div class="panel-body">
            <div class="padder">
              <div class="form-group">
                <label class="control-label">T&iacute;tulo</label>
                <input type="text" id="tarjeta_nombre" class="form-control" value="<%= nombre %>" name="nombre"/>
              </div>
              <div class="form-group">
                <label class="control-label">N&uacute;mero de comercio</label>
                <input type="text" id="tarjeta_numero_comercio" class="form-control" value="<%= numero_comercio %>" name="numero_comercio"/>
              </div>
            </div>
          </div>
        </div>
        <div class="panel panel-default">
          <div class="panel-body">
            <div class="padder">
              <div class="form-group mb0 clearfix">
                <label class="control-label">
                  Cuotas y coeficientes
                </label>
                <a class="expand-link fr">
                  <?php echo lang(array(
                    "es"=>"+ Ver opciones",
                    "en"=>"+ View options",
                  )); ?>
                </a>
                <div class="panel-description">
                  <?php echo lang(array(
                    "es"=>"Ingrese los coeficientes que se aplicaran a cada cuota.",
                  )); ?>                  
                </div>
              </div>
            </div>
          </div>
          <div class="panel-body expand" style="<%= (cuotas.length > 0)?'display:block':'' %>">

            <div class="padder">
              <div class="m-b row clearfix">
                <div class="form-group col-sm-6">
                  <label class="control-label">Cuota</label>
                  <select id="tarjeta_cuotas" class="form-control no-model">
                    <% for(var t=1; t <= 24; t++) { %>
                      <option value="<%= t %>"><%= t %></option>
                    <% } %>
                  </select>
                </div>
                <div class="form-group col-sm-6">
                  <label class="control-label">Coeficiente</label>
                  <div class="input-group">
                    <input id="tarjeta_cuota_interes" value="0" type="text" class="form-control"/>
                    <span class="input-group-btn">
                      <a id="cuota_agregar" class="btn btn-info"><i class="fa ico fa-plus"></i></a>
                    </span>
                  </div>
                </div>
                <div class="form-group col-sm-4">
                  <label class="control-label">Oferta desde</label>
                  <div class="input-group">
                    <input type="text" id="tarjeta_cuota_fecha_desde" class="form-control">
                    <span class="input-group-btn">
                        <button class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                    </span>
                  </div>
                </div>
                <div class="form-group col-sm-4">
                  <label class="control-label">Hasta</label>
                  <div class="input-group">
                    <input type="text" id="tarjeta_cuota_fecha_hasta" class="form-control w-md">
                    <span class="input-group-btn">
                        <button class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                    </span>
                  </div>
                </div>
                <div class="form-group col-sm-4">
                  <label class="control-label">Coef. de Oferta</label>
                  <input type="text" id="tarjeta_cuota_interes_especial" class="no-model form-control" />
                </div>
              </div>
              <div class="table-responsive">
                <table id="tarjeta_cuotas_tabla" class="table m-b-none default footable">
                  <thead>
                    <tr>
                      <th style="display: none"></th>
                      <th>Cuota</th>
                      <th>Coeficiente</th>
                      <th></th>
                    </tr>
                  </thead>
                  <tbody>
                    <% for(var i=0;i< cuotas.length;i++) { %>
                      <% var p = cuotas[i] %>
                      <tr>
                        <td class="cuota editar_cuota"><span class="text-info"><%= p.cuota_desde %></span></td>
                        <td class="fecha_desde dn editar_cuota"><%= p.fecha_desde %></td>
                        <td class="fecha_hasta dn editar_cuota"><%= p.fecha_hasta %></td>
                        <td class="interes_especial dn editar_cuota"><%= p.interes_especial %></td>
                        <td class="interes editar_cuota"><%= p.interes %></td>
                        <td class="tar">
                          <button class="btn btn-sm btn-white eliminar_cuota"><i class="fa fa-trash"></i></button>
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
    </div>
    <div class="line b-b m-b-lg"></div>
    <% if (edicion) { %>
      <div class="row">
        <div class="col-md-10 col-md-offset-1 tar">
          <button class="btn guardar btn-success">Guardar</button>
        </div>
      </div>
    <% } %>
  </div>
</div>

</script>