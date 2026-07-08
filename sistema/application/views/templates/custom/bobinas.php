<script type="text/template" id="bobinas_resultados_template">
  <div class="bg-light lter b-b wrapper-md">
    <h1 class="m-n font-thin h3">
      <i class="fa fa-database icono_principal mr10"></i>Bobinas
    </h1>
  </div>
  <div class="wrapper-md ng-scope">
    <div class="panel panel-default">
      <div class="panel-heading clearfix">
        <div class="row">
          <div class="col-md-6 col-lg-3 sm-m-b">
            <div class="input-group">
              <input type="text" id="autos_buscar" placeholder="Buscar..." autocomplete="off" class="form-control">
              <span class="input-group-btn">
                <button class="btn btn-default"><i class="fa fa-search"></i></button>
              </span>
            </div>
          </div>
          <div class="col-md-6 col-lg-offset-3 col-lg-6 text-right">
            <a class="btn btn-info btn-addon" href="app/#cargar_bobinas">
              <i class="fa fa-plus"></i><span>&nbsp;&nbsp;Cargar&nbsp;&nbsp;</span>
            </a>
          </div>
        </div>
      </div>
      <div class="panel-body">
        <div class="b-a table-responsive">
        <table id="bobinas_tabla" class="table table-striped sortable m-b-none default footable">
            <thead>
              <tr>
                <th style="width:20px;">
                  <label class="i-checks m-b-none">
                    <input class="esc sel_todos" type="checkbox"><i></i>
                  </label>
                </th>
                <th>Tipo</th>
                <th>Numero</th>
                <th>Ancho</th>
                <th>Gramaje</th>
                <th>Peso</th>
                <th class="sorting" data-sort-by="fecha_alta">Alta</th>
                <th class="sorting" data-sort-by="fecha_baja">Baja</th>
                <th style="width:100px;"></th>
              </tr>
            </thead>
            <tbody class="tbody"></tbody>
            <tfoot class="pagination_container hide-if-no-paging"></tfoot>
          </table>
        </div>
      </div>
    </div>
  </div>
</script>

<script type="text/template" id="bobinas_item_resultados_template">
  <% var clase = (fecha_baja=="")?"":"text-muted"; %>
  <td>
    <label class="i-checks m-b-none">
      <input class="esc check-row" value="<%= id %>" type="checkbox"><i></i>
    </label>
  </td>
  <td class="<%= clase %> data">
    <span class="text-info"><%= tipo %></span>
  </td>
  <td class="<%= clase %> data"><%= numero %></td>
  <td class="<%= clase %> data"><%= ancho %></td>
  <td class="<%= clase %> data"><%= gramaje %></td>
  <td class="<%= clase %> data"><%= peso %></td>
  <td class="<%= clase %> data"><%= fecha_alta %></td>
  <td class="<%= clase %> data"><%= fecha_baja %></td>
  <td class="p5 tar <%= clase %>">
    <div class="btn-group dropdown">
      <i title="Opciones" class="iconito fa fa-caret-down dropdown-toggle" data-toggle="dropdown"></i>
      <ul class="dropdown-menu pull-right">
        <li><a href="javascript:void(0)" class="duplicar" data-id="<%= id %>">Duplicar</a></li>
        <li><a href="javascript:void(0)" class="eliminar" data-id="<%= id %>">Eliminar</a></li>
      </ul>
    </div>
  </td>
</script>


<script type="text/template" id="bobina_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <h1 class="m-n font-thin h3">
    <i class="fa fa-database icono_principal mr10"></i>Bobinas
    / <b><%= (id == undefined) ? "Nuevo" : "Modificar" %></b>
  </h1>
</div>
<div class="wrapper-md">
  <div class="centrado rform">
    <div class="row">

      <div class="col-md-4">
        <div class="detalle_texto">
        </div>
      </div>

      <div class="col-md-8">
        <div class="panel panel-default">
          <div class="panel-body">
            <div class="padder">

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Tipo</label>
                    <select id="bobina_tipos" class="form-control"></select>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">N&uacute;mero</label>
                    <input type="text" id="bobina_numero" value="<%= numero %>" name="numero" class="form-control"/>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">Ancho</label>
                    <input type="text" id="bobina_ancho" value="<%= ancho %>" name="ancho" class="form-control"/>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">Gramaje</label>
                    <input type="text" id="bobina_gramaje" value="<%= gramaje %>" name="gramaje" class="form-control"/>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">Peso</label>
                    <input type="text" id="bobina_peso" value="<%= peso %>" name="peso" class="form-control"/>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Fecha de alta</label>
                    <div class="input-group">
                      <input type="text" class="form-control" id="bobina_fecha_alta" value="<%= fecha_alta %>" name="fecha_alta">
                      <span class="input-group-btn">
                        <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                      </span>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Fecha de baja</label>
                    <div class="input-group">
                      <input type="text" class="form-control" id="bobina_fecha_baja" value="<%= fecha_baja %>" name="fecha_baja">
                      <span class="input-group-btn">
                        <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                      </span>
                    </div>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Procedencia</label>
                    <input type="text" id="bobina_procedencia" value="<%= procedencia %>" name="procedencia" class="form-control"/>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">C&oacute;digo de proveedor</label>
                    <input type="text" id="bobina_codigo_proveedor" value="<%= codigo_proveedor %>" name="codigo_proveedor" class="form-control"/>
                  </div>
                </div>
              </div>

              <div class="form-group">
                <label class="control-label">Observaciones</label>
                <textarea id="bobina_observaciones" name="observaciones" class="form-control"><%= observaciones %></textarea>
              </div>

            </div>
          </div>
        </div>

      </div>
    </div>
    <div class="row">
      <div class="col-md-4"></div>
      <div class="col-md-8">
        <button class="btn guardar btn-success">Guardar</button>
      </div>
    </div>
  </div>
</div>
</script>

<script type="text/template" id="cargar_bobinas_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <h1 class="m-n font-thin h3">
    <i class="fa fa-database icono_principal mr10"></i>Bobinas
    / <b>Cargar</b>
  </h1>
</div>
<div class="wrapper-md">
  <div class="centrado rform">
    <div class="panel panel-default">
      <div class="panel-body">
        <div class="padder">

          <div class="col-md-2">
            <div class="form-group">
              <label class="control-label">Tipo</label>
              <select id="cargar_bobina_tipos" class="form-control no-model"></select>
            </div>
          </div>
          <div class="col-md-2">
            <div class="form-group">
              <label class="control-label">N&uacute;mero</label>
              <input type="text" id="cargar_bobina_numero" class="form-control no-model"/>
            </div>
          </div>

          <div class="col-md-2">
            <div class="form-group">
              <label class="control-label">Ancho</label>
              <input type="text" id="cargar_bobina_ancho" class="form-control no-model"/>
            </div>
          </div>
          <div class="col-md-2">
            <div class="form-group">
              <label class="control-label">Gramaje</label>
              <input type="text" id="cargar_bobina_gramaje" class="form-control no-model"/>
            </div>
          </div>
          <div class="col-md-2">
            <div class="form-group">
              <label class="control-label">Peso</label>
              <input type="text" id="cargar_bobina_peso" class="form-control no-model"/>
            </div>
          </div>

          <div class="col-md-2">
            <div class="form-group">
              <label class="control-label">Fecha de alta</label>
              <div class="input-group">
                <input type="text" class="form-control no-model" id="cargar_bobina_fecha_alta">
                <span class="input-group-btn">
                  <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                </span>
              </div>
            </div>
          </div>

          <div class="col-md-4">
            <div class="form-group">
              <label class="control-label">Procedencia</label>
              <input type="text" id="cargar_bobina_procedencia" class="form-control no-model"/>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label class="control-label">C&oacute;digo de proveedor</label>
              <input type="text" id="cargar_bobina_codigo_proveedor" class="form-control no-model"/>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label class="control-label">Observaciones</label>
              <div class="input-group">
                <input type="text" id="cargar_bobina_observaciones" class="form-control no-model"/>
                <span class="input-group-btn">
                  <button class="btn btn-info"><i class="fa fa-plus"></i></button>
                </span>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>
    <div class="panel panel-default">
      <div class="panel-body">
        <div class="b-a table-responsive">
          <table id="cargar_bobinas_tabla" class="table table-striped sortable m-b-none default footable">
            <thead>
              <tr>
                <th>Tipo</th>
                <th>Numero</th>
                <th>Ancho</th>
                <th>Gramaje</th>
                <th>Peso</th>
                <th>Cod. Prov.</th>
                <th style="width:20px;"></th>
              </tr>
            </thead>
            <tbody class="tbody"></tbody>
            <tfoot>
              <tr>
                <td colspan="4" class="tar bold">Peso total</td>
                <td id="cargar_bobinas_peso_total"></td>
                <td colspan="2"></td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>
    <div class="tar">
      <button class="btn guardar btn-success">Guardar</button>
    </div>
  </div>
</div>
</script>